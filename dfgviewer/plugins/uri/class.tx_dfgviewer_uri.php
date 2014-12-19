<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Sebastian Meyer <sebastian.meyer@slub-dresden.de>
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

/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 */

/**
 * Plugin 'DFG-Viewer: Persistent Identifier' for the 'dfgviewer' extension.
 *
 * @author	Sebastian Meyer <sebastian.meyer@slub-dresden.de>
 * @copyright	Copyright (c) 2012, Sebastian Meyer, SLUB Dresden
 * @package	TYPO3
 * @subpackage	tx_dfgviewer
 * @access	public
 */
class tx_dfgviewer_uri extends tx_dlf_plugin {

	public $extKey = 'dfgviewer';

	public $scriptRelPath = 'plugins/uri/class.tx_dfgviewer_uri.php';

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
	public function main($content, $conf) {

		$this->init($conf);

		// Load current document.
		$this->loadDocument();

		if ($this->doc === NULL) {

			// Quit without doing anything if required variables are not set.
			return $content;

		} else {

			// Set default values if not set.
			// page may be integer or string (pyhsical page attribute)
			if ( (int)$this->piVars['page'] > 0 || empty($this->piVars['page'])) {

				$this->piVars['page'] = tx_dlf_helper::intInRange((int)$this->piVars['page'], 1, $this->doc->numPages, 1);

			} else {

				$this->piVars['page'] = array_search($this->piVars['page'], $this->doc->physicalPages);

			}

		}

		// Load template file.
		if (!empty($this->conf['templateFile'])) {

			$this->template = $this->cObj->getSubpart($this->cObj->fileResource($this->conf['templateFile']), '###TEMPLATE###');

		} else {

			$this->template = $this->cObj->getSubpart($this->cObj->fileResource('EXT:dfgviewer/plugins/uri/template.tmpl'), '###TEMPLATE###');

		}

		$markerArray = array (
			'###URIBOOK###' => '',
			'###URIPAGE###' => ''
		);

		// Get persistent identifier of book.
		$uriBook = t3lib_div::trimExplode(' ', $this->doc->physicalPagesInfo[$this->doc->physicalPages[0]]['contentIds'], TRUE);

		if (empty($uriBook)) {

			$uriBook = $this->doc->getLogicalStructure($this->doc->toplevelId);

			$uriBook = t3lib_div::trimExplode(' ', $uriBook['contentIds'], TRUE);

		}

		if (!empty($uriBook)) {

			$uris = array ();

			foreach ($uriBook as $uri) {

				if (strpos($uri, 'http:') === 0 || strpos($uri, 'https:') === 0) {

					$uris[] = '<a href="'.htmlspecialchars($uri).'">'.htmlspecialchars($uri).'</a>';

				} elseif (strpos($uri, 'urn:') === 0) {

					$uris[] = '<a href="http://nbn-resolving.de/'.urlencode($uri).'">'.htmlspecialchars($uri).'</a>';

				}

			}

			if (!empty($uris)) {

				$markerArray['###URIBOOK###'] = $this->pi_getLL('uriBook', '', TRUE).implode(', ', $uris);

			}

		}

		// Get persistent identifier of page.
		$uriPage = t3lib_div::trimExplode(' ', $this->doc->physicalPagesInfo[$this->doc->physicalPages[$this->piVars['page']]]['contentIds'], TRUE);

		if (!empty($uriPage)) {

			$uris = array ();

			foreach ($uriPage as $uri) {

				if (strpos($uri, 'http:') === 0 || strpos($uri, 'https:') === 0) {

					$uris[] = '<a href="'.htmlspecialchars($uri).'">'.htmlspecialchars($uri).'</a>';

				} elseif (strpos($uri, 'urn:') === 0) {

					$uris[] = '<a href="http://nbn-resolving.de/'.htmlspecialchars($uri).'">'.htmlspecialchars($uri).'</a>';

				}

			}

			if (!empty($uris)) {

				$markerArray['###URIPAGE###'] = $this->pi_getLL('uriPage', '', TRUE).implode(', ', $uris);

			}

		}

		return $this->cObj->substituteMarkerArray($this->template, $markerArray);

	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dfgviewer/plugins/uri/class.tx_dfgviewer_uri.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dfgviewer/plugins/uri/class.tx_dfgviewer_uri.php']);
}

?>
