<?php
namespace Slub\Dfgviewer\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Sebastian Meyer <sebastian.meyer@slub-dresden.de>
 *  (c) 2022 Alexander Bigga <typo3@slub-dresden.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Kitodo\Dlf\Common\Helper;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Controller class for plugin 'Uri'.
 *
 * @author Sebastian Meyer <sebastian.meyer@slub-dresden.de>
 * @package TYPO3
 * @subpackage dlf
 * @access public
 */
class UriController extends \Kitodo\Dlf\Controller\AbstractController
{
    /**
     * The main method of the plugin
     *
     * @return void
     */
    public function mainAction()
    {
        // Load current document.
        $this->loadDocument();

        if ($this->isDocMissingOrEmpty()) {
            // Quit without doing anything if required variables are not set.
            return;
        }

        $this->setPage();

        $doc = $this->document->getCurrentDocument();

        // Get persistent identifier of book.
        $uriBook = GeneralUtility::trimExplode(' ', $doc->physicalStructureInfo[$doc->physicalStructure[0]]['contentIds'], TRUE);

        if (empty($uriBook)) {
            $uriBook = $doc->getLogicalStructure($doc->toplevelId);
            $uriBook = GeneralUtility::trimExplode(' ', $uriBook['contentIds'], TRUE);
        }

        if (!empty($uriBook)) {
            $uris = [];
            foreach ($uriBook as $uri) {
                if (Helper::isValidHttpUrl($uri)) {
                    $uris[] = $uri;
                } elseif (strpos($uri, 'urn:') === 0) {
                    if (strpos($uri, '/fragment/') === false) {
                        $uris[] = 'https://nbn-resolving.de/' . $uri;
                    } else {
                        $uris[] = 'https://nbn-resolving.org/' . $uri;
                    }
                }
            }
            if (!empty($uris)) {
                $this->view->assign('uriBooks', $uris);
            }
        }

        // Get persistent identifier of page.
        $uriPage = GeneralUtility::trimExplode(' ', $doc->physicalStructureInfo[$doc->physicalStructure[$this->requestData['page']]]['contentIds'], TRUE);

        if (!empty($uriPage)) {
            $uris = [];

            foreach ($uriPage as $uri) {
                if (Helper::isValidHttpUrl($uri)) {
                    $uris[] = $uri;
                } elseif (strpos($uri, 'urn:') === 0) {
                    if (strpos($uri, '/fragment/') === false) {
                        $uris[] = 'https://nbn-resolving.de/' . $uri;
                    } else {
                        $uris[] = 'https://nbn-resolving.org/' . $uri;
                    }
                }
            }
            if (!empty($uris)) {
                $this->view->assign('uriPages', $uris);
            }
        }
    }
}
