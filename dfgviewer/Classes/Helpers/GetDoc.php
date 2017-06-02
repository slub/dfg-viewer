<?php
namespace Slub\Dfgviewer\Helpers;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 Alexander Bigga <alexander.bigga@slub-dresden.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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
use TYPO3\CMS\Core\Utility\GeneralUtility;

class GetDoc
{
    public $extKey = 'dfgviewer';

  /**
  	 * This holds the current document
  	 *
  	 * @var	tx_dlf_document
  	 * @access protected
  	 */
  	protected $doc;

  /**
   * Get page's download link
   *
   * @access	public
   *
   * @param	integer	$pagenumber:The current page numbert
   *
   * @return	string: The left and right download url
   */
  public function getPageLink($pagenumber)
  {

    if (!$this->init()) {
      return '';
    }

    $details = $this->doc->physicalPagesInfo[$this->doc->physicalPages[$pagenumber]];
		$file = $details['files']['DOWNLOAD'];

		if (!empty($file)) {

			$pageLink = $this->doc->getFileLocation($file);

    }

    return $pageLink;
  }

  /**
   * Get work's download link
   *
   * @access	public
   *
   * @return	string: The left and right download url
   */
  public function getWorkLink()
  {

    if (!$this->init()) {
      return '';
    }

    // Get work link.
  		if (!empty($this->doc->physicalPagesInfo[$this->doc->physicalPages[0]]['files']['DOWNLOAD'])) {

  			$workLink = $this->doc->getFileLocation($this->doc->physicalPagesInfo[$this->doc->physicalPages[0]]['files']['DOWNLOAD']);

  		} else {

  			$details = $this->doc->getLogicalStructure($this->doc->toplevelId);

  			if (!empty($details['files']['DOWNLOAD'])) {

  				$workLink = $this->doc->getFileLocation($details['files']['DOWNLOAD']);

  			}

  		}

      return $workLink;
  }

  /**
   * get xpath result
   *
   * @access	public
   *
   * @param	string		$content: The PlugIn content
   *
   * @return	string		The content that is displayed on the website
   */
  public function getXpath($xpath)
  {
    if (!$this->init()) {
      return '';
    }
      return $this->doc->mets->xpath($xpath);
  }

  /**
     * Initialize and load the document
     *
     * @access	protected
     *
     * @return	boolean
     */
    protected function init()
    {
      // Load current document.
      $this->loadDocument();

      if ($this->doc === null) {

        // Quit without doing anything if required variables are not set.
        return null;

      }

      $this->doc->mets->registerXPathNamespace('mets', 'http://www.loc.gov/METS/');
      $this->doc->mets->registerXPathNamespace('mods', 'http://www.loc.gov/mods/v3');
      $this->doc->mets->registerXPathNamespace('dv', 'http://dfg-viewer.de/');
      $this->doc->mets->registerXPathNamespace('slub', 'http://slub-dresden.de/');

      return true;
    }

  /**
     * Loads the current document into $this->doc
     *
     * @access	protected
     *
     * @return	void
     */
    protected function loadDocument()
    {
      $piVarsSet = GeneralUtility::_GPmerged('set');

      $piVars = GeneralUtility::_GPmerged('tx_dlf');

      // overwrite tx_dlf[] parameters by (old) set[] ones
      if (!empty($piVarsSet['mets'])) {
        $piVars['id'] = $piVarsSet['mets'];
      }
      if (!empty($piVarsSet['double'])) {
        $piVars['double'] = $piVarsSet['double'];
      }
      if (!empty($piVarsSet['image'])) {
        $piVars['page'] = $piVarsSet['image'];
      }

        // Check for required variable.
        if (!empty($piVars['id'])) {

            // Get instance of tx_dlf_document.
            $this->doc =& \tx_dlf_document::getInstance($piVars['id'], 0);

            if (!$this->doc->ready) {

                // Destroy the incomplete object.
                $this->doc = null;

                if (TYPO3_DLOG) {
                    \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('[tx_dlf_plugin->loadDocument()] Failed to load document with UID "'.$piVars['id'].'"', $this->extKey, SYSLOG_SEVERITY_ERROR);
                }
            } else {

                // Set configuration PID.
                $this->doc->cPid = $this->conf['pages'];
            }
        } else {
            if (TYPO3_DLOG) {
                \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('[tx_dlf_plugin->loadDocument()] Failed to load document with record ID "'.$this->piVars['recordId'].'"', $this->extKey, SYSLOG_SEVERITY_ERROR);
            }
        }
    }
}
