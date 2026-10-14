<?php
namespace Slub\Dfgviewer\Controller;

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

use Kitodo\Dlf\Common\AbstractDocument;
use Kitodo\Dlf\Common\Helper;
use Kitodo\Dlf\Controller\AbstractController;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Controller class for plugin 'Uri'.
 *
 * @author Sebastian Meyer <sebastian.meyer@slub-dresden.de>
 * @package TYPO3
 * @subpackage dlf
 * @access public
 */
class UriController extends AbstractController
{
    /**
     * The main method of the plugin
     *
     * @access public
     *
     * @return ResponseInterface
     */
    public function mainAction(): ResponseInterface
    {
        // Load current document.
        $this->loadDocument();

        if ($this->isDocMissingOrEmpty()) {
            // Quit without doing anything if required variables are not set.
            return $this->htmlResponse();
        }

        $this->setPage();

        $doc = $this->document->getCurrentDocument();

        $this->assignUriBook($doc);
        $this->assignUriPage($doc);

        return $this->htmlResponse();
    }

    /**
     * Assign the uri book.
     *
     * @access private
     *
     * @param AbstractDocument|null $doc
     *
     * @return void
     */
    private function assignUriBook(?AbstractDocument $doc): void
    {
        // Get persistent identifier of book.
        $uriBook = GeneralUtility::trimExplode(' ', $doc->physicalStructureInfo[$doc->physicalStructure[0]]['contentIds'], TRUE);

        if (empty($uriBook)) {
            $uriBook = $doc->getLogicalStructure($doc->getToplevelId());
            $uriBook = GeneralUtility::trimExplode(' ', $uriBook['contentIds'], TRUE);
        }

        if (!empty($uriBook)) {
            $uris = $this->getUris($uriBook);
            if (!empty($uris)) {
                $this->view->assign('uriBooks', $uris);
            }
        }
    }

    /**
     * Assign the uri page.
     *
     * @access private
     *
     * @param AbstractDocument|null $doc
     *
     * @return void
     */
    private function assignUriPage(?AbstractDocument $doc): void
    {
        if (!isset($this->requestData['page'])) {
            return;
        }

        // Get persistent identifier of page.
        $uriPage = GeneralUtility::trimExplode(' ', $doc->physicalStructureInfo[$doc->physicalStructure[$this->requestData['page']]]['contentIds'], TRUE);

        if (!empty($uriPage)) {
            $uris = $this->getUris($uriPage);
            if (!empty($uris)) {
                $this->view->assign('uriPages', $uris);
            }
        }
    }

    /**
     * Get the URIs from the given array.
     *
     * @access private
     *
     * @param string[] $uris
     *
     * @return string[]
     */
    private function getUris(array $uris): array
    {
        $filteredUris = [];
        foreach ($uris as $uri) {
            if (Helper::isValidHttpUrl($uri)) {
                $filteredUris[] = $uri;
            } else if (str_starts_with($uri, 'urn:')) {
                if (!str_contains($uri, '/fragment/')) {
                    $filteredUris[] = 'https://nbn-resolving.de/' . $uri;
                } else {
                    $filteredUris[] = 'https://nbn-resolving.org/' . $uri;
                }
            }
        }
        return $filteredUris;
    }
}
