<?php

namespace Slub\Dfgviewer\Controller;

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
