<?php

namespace Slub\Dfgviewer\Controller;

use Kitodo\Dlf\Domain\Model\Document;
use Kitodo\Dlf\Common\MetsDocument;
use Kitodo\Dlf\Domain\Repository\StructureRepository;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Controller class for the SRU plugin.
 *
 * Checks if the METS document contains a link to an SRU endpoint, and if so,
 * adds a search form to the pageview.
 *
 * @package    TYPO3
 * @subpackage    tx_dfgviewer
 * @access    public
 */
class SruController extends \Kitodo\Dlf\Controller\AbstractController
{
    public function mainAction()
    {
        // Load current document.
        $this->loadDocument($this->requestData);
        if (
            $this->document === null
            || $this->document->getDoc() === null
            || !$this->document->getDoc() instanceof MetsDocument
        ) {
            // Quit without doing anything if required variables are not set.
            return '';
        }

        // Get digital provenance information.
        $digiProv = $this->document->getDoc()->mets->xpath('//mets:amdSec/mets:digiprovMD/mets:mdWrap[@OTHERMDTYPE="DVLINKS"]/mets:xmlData');

        if ($digiProv) {
            $links = $digiProv[0]->children('http://dfg-viewer.de/')->links;

            // if no children found with given namespace, skip the following section
            if ($links && $links->sru) {
                $sruLink = htmlspecialchars(trim((string)$links->sru));
            }
        }

        if (empty($sruLink)) {
            // Quit without doing anything if required variables are not set.
            return '';
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
     * @access    protected
     *
     * @return    string        Viewer script tags ready for output
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

            $pageRenderer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Page\PageRenderer::class);
            $pageRenderer->addJsFooterInlineCode('tx-dfgviewer-footer', $javascriptFooter);
        }
    }
}
