<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2014 Alexander Bigga <alexander.bigga@slub-dresden.de>
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
 * Plugin 'DFG-Viewer: SRU Client' for the 'dfgviewer' extension.
 *
 * @author	Alexander Bigga <alexander.bigga@slub-dresden.de>
 * @copyright	Copyright (c) 2014, Alexander Bigga, SLUB Dresden
 * @package	TYPO3
 * @subpackage	tx_dfgviewer
 * @access	public
 */
class tx_dfgviewer_sru extends tx_dlf_plugin {

	public $extKey = 'dfgviewer';

	public $scriptRelPath = 'plugins/sru/class.tx_dfgviewer_sru.php';

	/**
	 * Holds the OpenLayers files for the syntax-highlightning
	 *
	 * @var	array
	 * @access protected
	 */
	protected $openLayersHighlightning = array (
			// Geometry layer.
			'OpenLayers/Geometry.js',
			'OpenLayers/Geometry/Collection.js',
			'OpenLayers/Geometry/Polygon.js',
			'OpenLayers/Geometry/MultiPolygon.js',
			'OpenLayers/Geometry/MultiPoint.js',
			'OpenLayers/Geometry/Curve.js',
			'OpenLayers/Geometry/LineString.js',
			'OpenLayers/Geometry/LinearRing.js',
			'OpenLayers/Geometry/Point.js',
			'OpenLayers/Feature.js',
			'OpenLayers/Feature/Vector.js',
			'OpenLayers/Layer/Vector.js',
			'OpenLayers/Renderer.js',
			'OpenLayers/Renderer/Elements.js',
			'OpenLayers/Renderer/SVG.js',
			'OpenLayers/StyleMap.js',
			'OpenLayers/Style.js',
	);

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

		}

		// Get digital provenance information.
		$digiProv = $this->doc->mets->xpath('//mets:amdSec/mets:digiprovMD/mets:mdWrap[@OTHERMDTYPE="DVLINKS"]/mets:xmlData');

		if ($digiProv) {

			$links = $digiProv[0]->children('http://dfg-viewer.de/')->links;

			// if no children found with given namespace, skip the following section
			if ($links) {

				if ($links->sru) {

					$sruLink = htmlspecialchars(trim((string) $links->sru));

				}

			}

		}

		if (empty($sruLink)) {

			// Quit without doing anything if required variables are not set.
			return $content;

		}

		// Load template file.
		if (!empty($this->conf['templateFile'])) {

			$this->template = $this->cObj->getSubpart($this->cObj->fileResource($this->conf['templateFile']), '###TEMPLATE###');

		} else {

			$this->template = $this->cObj->getSubpart($this->cObj->fileResource('EXT:dfgviewer/plugins/sru/template.tmpl'), '###TEMPLATE###');

		}

		$this->addSearchFormJS();

		$this->addSruOrigImageJS();

		$this->addSruResultsJS();

		// Configure @action URL for form.
		$linkConf = array (
			'parameter' => $GLOBALS['TSFE']->id,
			'forceAbsoluteUrl' => 1
		);

		// Fill markers.
		$markerArray = array (
			'###ACTION_URL###' => $this->cObj->typoLink_URL($linkConf),
			'###LABEL_QUERY###' => $this->pi_getLL('label.query'),
			'###LABEL_DELETE_SEARCH###' => $this->pi_getLL('label.delete_search'),
			'###LABEL_LOADING###' => $this->pi_getLL('label.loading'),
			'###SRU_URL###' => $sruLink,
			'###LANG_ID###' => $this->LLkey,
			'###LABEL_SUBMIT###' => $this->pi_getLL('label.submit'),
			'###FIELD_QUERY###' => $this->prefixId.'[query]',
			'###QUERY###' => htmlspecialchars($lastQuery),
			'###CURRENT_DOCUMENT###' => $this->doc->location,
		);

		// Display search form.
		$content .= $this->cObj->substituteSubpart($this->cObj->substituteMarkerArray($this->template, $markerArray), '###EXT_SEARCH_ENTRY###', $extendedSearch);

		return $this->pi_wrapInBaseClass($content);

	}

	/**
	 * Adds the JS files necessary for search form
	 *
	 * @access	protected
	 *
	 * @return	void
	 */
	protected function addSearchFormJS() {

		// Add javascript to page header.
		if (tx_dlf_helper::loadJQuery()) {

			$GLOBALS['TSFE']->additionalHeaderData[$this->prefixId.'_sru'] = '<script type="text/javascript" src="'.t3lib_extMgm::siteRelPath($this->extKey).'plugins/sru/tx_dfgviewer_sru.js"></script>';

		}

	}

	/**
	 * Adds SRU Search result javascript
	 *
	 * @access	protected
	 *
	 * @return	string		Viewer script tags ready for output
	 */

	protected function addSruOrigImageJS() {

		if (!empty($this->piVars['origimage'])) {
			$origImage = $this->piVars['origimage'];
			// Add SRU Results if any
			$javascriptFooter[] = '
			<script type="text/javascript">
				if (typeof tx_dlf_viewer !== "undefined") {
					tx_dlf_viewer.setOrigImage('.$origImage.');
				}
			</script>';

			$GLOBALS['TSFE']->additionalFooterData['tx-dfgviewer-footer'] = implode("\n", $javascriptFooter);
		}

	}

	/**
	 * Adds SRU Search result javascript
	 *
	 * @access	protected
	 *
	 * @return	string		Viewer script tags ready for output
	 */

	protected function addSruResultsJS() {


		if (!empty($this->piVars['highlight'])) {

			// add necessary files for syntax highlightning to the header
			// dlf_pageview will concat it with the other files from OpenLayers
			$javascriptHeader = '<script type="text/javascript">var openLayerFilesHighlightning = ["' . implode('", "', $this->openLayersHighlightning) . '"];</script>';

			$GLOBALS['TSFE']->additionalHeaderData['tx-dlf-header-sru'] = $javascriptHeader;

			$highlight = unserialize(urldecode($this->piVars['highlight']));

			// Add SRU Results if any
			$javascriptFooter = '
			<script type="text/javascript">
			if (typeof tx_dlf_viewer !== "undefined") {';

			foreach ($highlight as $field) {
				$javascriptFooter .= 'tx_dlf_viewer.addHighlightField('.$field.');';
			}

			$javascriptFooter .= '}
			</script>';

			$GLOBALS['TSFE']->additionalFooterData['tx-dfgviewer-footer'] .= $javascriptFooter;
		}

	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dfgviewer/plugins/sru/class.tx_dfgviewer_sru.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dfgviewer/plugins/sru/class.tx_dfgviewer_sru.php']);
}

?>
