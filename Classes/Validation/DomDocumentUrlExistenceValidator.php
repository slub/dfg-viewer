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
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Kitodo\Dlf\Validation\AbstractDlfValidator;
use Psr\Log\LoggerAwareTrait;
use Slub\Dfgviewer\Common\ValidationHelper as VH;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
    use LoggerAwareTrait;

    /**
     * Excluded host names separated by comma.
     * @var array
     */
    private array $excludeHosts;

    public function __construct(array $configuration=[])
    {
        parent::__construct(DOMDocument::class);
        $this->excludeHosts = [];
        if (isset($configuration["excludeHosts"])) {
            $this->excludeHosts = explode(",", $configuration["excludeHosts"]);
        }
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
        // do not modify original document
        $tempDocument = clone $document;
        $urls = $this->getFileUrlAndRemoveFileGroups($tempDocument);

        // get the urls of document without file group nodes
        preg_match_all('/' . VH::URL_REGEX . '/i', $tempDocument->saveXML(), $matches);
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
        $fileGroups = $xpath->query(VH::XPATH_FILE_SECTION_GROUPS);
        foreach ($fileGroups as $fileGroup) {
            $fLocats = $xpath->query('mets:file/mets:FLocat', $fileGroup);
            foreach ($fLocats as $fLocat) {
                // @phpstan-ignore-next-line
                $url = $fLocat->getAttribute("xlink:href");
                $host = VH::getHost($url);
                if ($host !== '' && !in_array($host, $hosts)) {
                    $hosts[] = $host;
                    $urls[] = $url;
                }
            }
            // reset to check for every file group
            $hosts = [];
            $fileGroup->parentNode->removeChild($fileGroup);
        }
        return $urls;
    }

    private function urlExists($url): bool
    {
        /** @var RequestFactory $requestFactory */
        $requestFactory = GeneralUtility::makeInstance(RequestFactory::class);
        try {
            $response = $requestFactory->request($url);
            $statusCode = $response->getStatusCode();
            return $statusCode >= 200 && $statusCode < 400;
        } catch (ConnectException | RequestException $e) {
            $this->logger->debug($e->getMessage());
        }
        return false;
    }

    private function isExcluded($url): bool
    {
        return in_array(VH::getHost($url), $this->excludeHosts);
    }
}
