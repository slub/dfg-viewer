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

use Kitodo\Dlf\Common\MetsDocument;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Controller class for the SRU plugin.
 *
 * Checks if the METS document contains a link to an SRU endpoint, and if so,
 * adds a search form to the pageview.
 *
 * @package TYPO3
 * @subpackage tx_dfgviewer
 * @access public
 */
class SruController extends \Kitodo\Dlf\Controller\AbstractController
{
    /**
     * The main method of the controller
     *
     * @return void
     */
    public function mainAction()
    {
        // Load current document.
        $this->loadDocument();
        if (
            $this->isDocMissing()
            || !$this->document->getCurrentDocument() instanceof MetsDocument
        ) {
            // Quit without doing anything if required variables are not set.
            return;
        }

        // Get digital provenance information.
        $digiProv = $this->document->getCurrentDocument()->mets->xpath('//mets:amdSec/mets:digiprovMD/mets:mdWrap[@OTHERMDTYPE="DVLINKS"]/mets:xmlData');

        if ($digiProv) {
            $links = $digiProv[0]->children('http://dfg-viewer.de/')->links;

            // if no children found with given namespace, skip the following section
            if ($links && $links->sru) {
                $sruLink = htmlspecialchars(trim((string)$links->sru));
            }
        }

        if (empty($sruLink)) {
            // Quit without doing anything if link is not set.
            return;
        }

        $actionUrl = $this->uriBuilder->reset()
            ->setTargetPageUid($GLOBALS['TSFE']->id)
            ->setCreateAbsoluteUri(true)
            ->build();

        $this->addSruResultsJS();

        $this->view->assign('sruLink', $sruLink);
        $this->view->assign('currentDocument', $this->document->getLocation());
        $this->view->assign('actionUrl', $actionUrl);
    }

    /**
     * Adds SRU Search result javascript
     *
     * @access protected
     *
     * @return void
     */
    protected function addSruResultsJS()
    {
        if (!empty($this->requestData['highlight']) && !empty($this->requestData['origimage'])) {
            $highlight = unserialize(urldecode($this->requestData['highlight']));
            $origImage = $this->requestData['origimage'];

            // Add SRU Results if any
            $javascriptFooter = '$(document).ready(function(){';
            foreach ($highlight as $field) {
                $javascriptFooter .= 'tx_dlf_viewer.addHighlightField([' . $field . '],' . $origImage . ');';
            }
            $javascriptFooter .= '})';

            $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
            $pageRenderer->addJsFooterInlineCode('tx-dfgviewer-footer', $javascriptFooter);
        }
    }
}
