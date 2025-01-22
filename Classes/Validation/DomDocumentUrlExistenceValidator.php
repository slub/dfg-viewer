<?php

declare(strict_types=1);

/**
 * Copyright notice
 *
 * (c) Saxon State and University Library Dresden <typo3@slub-dresden.de>
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 */

namespace Slub\Dfgviewer\Validation;

use DOMDocument;
use DOMXPath;
use Kitodo\Dlf\Validation\AbstractDlfValidator;
use Slub\Dfgviewer\Common\ValidationHelper;

/**
 * The validator checks the document URLs for their existence.
 *
 * @package TYPO3
 * @subpackage dlf
 *
 * @access public
 */
class DomDocumentUrlExistenceValidator extends AbstractDlfValidator
{
    private array $excludeHosts;

    public function __construct(array $configuration = [])
    {
        parent::__construct(DOMDocument::class);
        $this->excludeHosts = isset($configuration["excludeHosts"]) ? explode(",", $configuration["excludeHosts"]) : [];
    }



    protected function isValid($value): void
    {
        foreach ($this->getDocumentUrls($value) as $url) {
            if (!$this->isExcluded($url) && !$this->urlExists($url)) {
                $this->addError('URL "' . $url . '" could not be found.', 1737384167);
            }
        }
    }

    /**
     * Retrieve all URLs of the document, except for the URLs of the file groups, where only one URL is returned per file group and host.
     *
     * @param DOMDocument $document The document
     * @return array The array of URLs
     */
    protected function getDocumentUrls(DOMDocument $document): array
    {
        $tempDocument = clone $document; // do not modify original document
        $urls = $this->getFileUrlAndRemoveFileGroups($tempDocument);

        // get the urls of document without file group nodes
        preg_match_all('/' . ValidationHelper::URL_REGEX . '/i', $tempDocument->saveXML(), $matches);
        if (is_array($matches) && count($matches) > 0) {
            $urls += $matches[0];
        }
        return array_unique($urls);
    }

    /**
     * Get the representative file urls and remove file groups to prevent rechecking.
     *
     * To minimize the load of the exist check as much as possible, one URL is determined per file group and host.
     *
     * @param DOMDocument $document The document to validate
     * @return array The file urls to check
     */
    protected function getFileUrlAndRemoveFileGroups(DOMDocument $document): array
    {
        $urls = [];
        $hosts = [];
        $xpath = new DOMXpath($document);
        $fileGroups = $xpath->query(ValidationHelper::XPATH_FILE_SECTION_GROUPS);
        foreach ($fileGroups as $fileGroup) {
            $fLocats = $xpath->query('mets:file/mets:FLocat', $fileGroup);
            foreach ($fLocats as $fLocat) {
                // @phpstan-ignore-next-line
                $url = $fLocat->getAttribute("xlink:href");
                $host = parse_url($url, PHP_URL_HOST);
                if (!in_array($host, $hosts)) {
                    $hosts[] = $host;
                    $urls[] = $url;
                }
            }
            $hosts = []; // reset to check for every file group
            $fileGroup->parentNode->removeChild($fileGroup);
        }
        return $urls;
    }

    private function urlExists($url): bool
    {
        $headers = @get_headers($url);
        if ($headers === false || !is_array($headers) || count($headers) == 0) {
            return false;
        }

        preg_match('/HTTP\/\d\.\d\s+(\d+)/', $headers[0], $matches);
        $statusCode = (int)$matches[1];
        return $statusCode >= 200 && $statusCode < 400;
    }

    private function isExcluded($url): bool
    {
        foreach ($this->excludeHosts as $excludeHost) {
            if (str_starts_with($url, 'http://' . $excludeHost) || str_starts_with($url, 'https://' . $excludeHost)) {
                return true;
            }
        }
        return false;
    }
}
