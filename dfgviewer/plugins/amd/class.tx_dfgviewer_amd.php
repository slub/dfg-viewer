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
 * Plugin 'DFG-Viewer: Administrative Metadata' for the 'dfgviewer' extension.
 *
 * @author	Sebastian Meyer <sebastian.meyer@slub-dresden.de>
 * @copyright	Copyright (c) 2012, Sebastian Meyer, SLUB Dresden
 * @package	TYPO3
 * @subpackage	tx_dfgviewer
 * @access	public
 */
class tx_dfgviewer_amd extends tx_dlf_plugin {

	public $extKey = 'dfgviewer';

	public $scriptRelPath = 'plugins/amd/class.tx_dfgviewer_amd.php';

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

		// Turn cache off.
		$this->setCache(FALSE);

		// Load current document.
		$this->loadDocument();

		if ($this->doc === NULL) {

			// Quit without doing anything if required variables are not set.
			return $content;

		}

		// Load template file.
		if (!empty($this->conf['templateFile'])) {

			$this->template = $this->cObj->getSubpart($this->cObj->fileResource($this->conf['templateFile']), '###TEMPLATE###');

		} else {

			$this->template = $this->cObj->getSubpart($this->cObj->fileResource('EXT:dfgviewer/plugins/amd/template.tmpl'), '###TEMPLATE###');

		}

		$markerArray = array (
			'###OWNER###' => '',
			'###OWNERSITEURL###' => '',
			'###OWNERLOGO###' => '',
			'###OWNERCONTACT###' => '',
			'###REFERENCE###' => '',
			'###LOCALVIEW###' => '',
			'###SPONSOR###' => '',
			'###SPONSORSITEURL###' => '',
			'###SPONSORLOGO###' => ''
		);

		$subpart = '';

		// Get legal and contact information.
		$legalContact = $this->doc->mets->xpath('//mets:amdSec/mets:rightsMD/mets:mdWrap[@OTHERMDTYPE="DVRIGHTS"]/mets:xmlData');

		if ($legalContact) {

			$rights = $legalContact[0]->children('http://dfg-viewer.de/')->rights;

			// Get owner.
			$markerArray['###OWNER###'] = htmlspecialchars((string) $rights->owner);

			// Get owner's site URL.
			$markerArray['###OWNERSITEURL###'] = htmlspecialchars((string) $rights->ownerSiteURL);

			// Get owner's logo.
			$markerArray['###OWNERLOGO###'] = htmlspecialchars((string) $rights->ownerLogo);

			// Get owner's contact information.
			$markerArray['###OWNERCONTACT###'] = htmlspecialchars((string) $rights->ownerContact);

			// Get sponsor.
			$markerArray['###SPONSOR###'] = htmlspecialchars((string) $rights->sponsor);

			// Get sponsor's site URL.
			$markerArray['###SPONSORSITEURL###'] = htmlspecialchars((string) $rights->sponsorSiteURL);

			// Get sponsor's logo.
			$markerArray['###SPONSORLOGO###'] = htmlspecialchars((string) $rights->sponsorLogo);

		}

		// Get digital provenance information.
		$digiProv = $this->doc->mets->xpath('//mets:amdSec/mets:digiprovMD/mets:mdWrap[@OTHERMDTYPE="DVLINKS"]/mets:xmlData');

		if ($digiProv) {

			$links = $digiProv[0]->children('http://dfg-viewer.de')->links;

			// Get sub-template.
			$referencesTmpl = $this->cObj->getSubpart($this->template, '###REFERENCES###');

			foreach ($links->reference as $reference) {

				$refMarkerArray = array (
					'###CATALOG###' => '',
					'###REFERENCEURL###' => ''
				);

				// Get catalog references.
				$refMarkerArray['###CATALOG###'] = htmlspecialchars((string) $reference->attributes()->linktext);

				if (empty($refMarkerArray['###CATALOG###'])) {

					$refMarkerArray['###CATALOG###'] = $this->pi_getLL('opac', '', TRUE);

				}

				$refMarkerArray['###REFERENCEURL###'] = htmlspecialchars((string) $reference);

				$subpart .= $this->cObj->substituteMarkerArray($referencesTmpl, $refMarkerArray);

			}

			// Get local view.

			$markerArray['###LOCALVIEW###'] = $this->pi_getLL('localview', '', TRUE);

			$markerArray['###LOCALVIEWURL###'] = htmlspecialchars((string) $links->presentation);

		}

		// Set logo of German Research Foundation as default.
		if (empty($markerArray['###SPONSORLOGO###'])) {

			$markerArray['###SPONSOR###'] = $this->pi_getLL('dfg', '', TRUE);

			$markerArray['###SPONSORSITEURL###'] = $this->pi_getLL('dfgLink', '', TRUE);

			$markerArray['###SPONSORLOGO###'] = t3lib_extMgm::siteRelPath($this->extKey).'res/images/dfglogo.png';

		}

		return $this->cObj->substituteSubpart($this->cObj->substituteMarkerArray($this->template, $markerArray), '###REFERENCES###', $subpart, TRUE);

	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dfgviewer/plugins/amd/class.tx_dfgviewer_amd.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dfgviewer/plugins/amd/class.tx_dfgviewer_amd.php']);
}

?>