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


class GetDoc {

	public $extKey = 'dfgviewer';

	public $scriptRelPath = 'Classes/Helpers/GetDoc.php';

  /**
   * The main method of the PlugIn
   *
   * @access	public
   *
   * @param	string		$content: The PlugIn content
   * @param	array		$conf: The PlugIn configuration
   *
   * @return	string		The content that is displayed on the website
   */
  public function getXpath($xpath) {

//    $this->init($conf);

    // Load current document.
    $this->loadDocument();

    if ($this->doc === NULL) {
//debug('doc is empty');
      // Quit without doing anything if required variables are not set.
      return $content;

    }

//		$this->doc->mets->registerXPathNamespace('mets', 'http://www.loc.gov/METS/');
//		$this->doc->mets->registerXPathNamespace('mods', 'http://www.loc.gov/mods/v3');
		$this->doc->mets->registerXPathNamespace('dv', 'http://dfg-viewer.de/');

    return $this->doc->mets->xpath($xpath);

    //return $this;

  }

  /**
  	 * Loads the current document into $this->doc
  	 *
  	 * @access	protected
  	 *
  	 * @return	void
  	 */
  	protected function loadDocument() {

      $piVars = GeneralUtility::_GP('tx_dlf');

  		// Check for required variable.
  		if (!empty($piVars['id'])) {

  			// Should we exclude documents from other pages than $this->conf['pages']?
  			$pid = (!empty($this->conf['excludeOther']) ? intval($this->conf['pages']) : 0);

  			// Get instance of tx_dlf_document.
  			$this->doc =& \tx_dlf_document::getInstance($piVars['id'], $pid);

  			if (!$this->doc->ready) {

  				// Destroy the incomplete object.
  				$this->doc = NULL;

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
