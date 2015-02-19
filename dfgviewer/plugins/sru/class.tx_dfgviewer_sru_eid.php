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
 * Plugin 'DFG-Viewer: SRU Client eID script' for the 'dfgviewer' extension.
 *
 * @author	Alexander Bigga <alexander.bigga@slub-dresden.de>
 * @copyright	Copyright (c) 2014, Alexander Bigga, SLUB Dresden
 * @package	TYPO3
 * @subpackage	tx_dfgviewer
 * @access	public
 */
class tx_dfgviewer_sru_eid extends tslib_pibase {

	/**
	 *
	 */
	public $cObj;


	/**
	 * The main method of the eID-Script
	 *
	 * @access	public
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 *
	 * @return	void
	 */
	public function main($content = '', $conf = array ()) {

		$this->cObj = t3lib_div::makeInstance('tslib_cObj');

		// Load translation files.
		$LANG = t3lib_div::makeInstance('language');

		$this->extKey = 'dfgviewer';

		$this->scriptRelPath = 'plugins/sru/class.tx_dfgviewer_sru_eid.php';

		$this->LLkey = t3lib_div::_GP('L') ? t3lib_div::_GP('L') : 'default';

		$this->pi_loadLL();

		$url = t3lib_div::_GP('sru').urlencode(t3lib_div::_GP('q'));

		// make request to SRU service
		$sruXML = simplexml_load_file($url);

		if ($sruXML !== FALSE) {

			// the result may be a valid <srw:searchRetrieveResponse> or some HTML code

			$sruResponse = $sruXML->xpath('/srw:searchRetrieveResponse');

			if ($sruResponse === FALSE) {

				$results['error'] =  $this->pi_getLL('label.noresults') . ' ' . t3lib_div::_GP('q') ;

			} else {

				$sruRecords = $sruXML->xpath('/srw:searchRetrieveResponse/srw:records/srw:record');

				if ($sruRecords === FALSE || empty($sruRecords) ) {

					$results['error'] =  $this->pi_getLL('label.noresults') . ' ' . t3lib_div::_GP('q') ;

				}

				foreach ($sruRecords as $id => $record) {

					$fullTextHit = $record->xpath('//srw:recordData');

					$pageAttributes = '';

					foreach($fullTextHit[$id]->children('http://dfg-viewer.de/')->page->attributes() as $key => $val) {

						$pageAttributes[$key] = $val;

					}

					unset($hitFound);

					// there may be multiple hits on a page per search query
					foreach ($fullTextHit[$id]->children('http://dfg-viewer.de/')->page->fulltexthit as $hit) {

						unset($hitAttributes);

						foreach ($hit->attributes() as $key => $val) {

							$hitAttributes[$key] = $val;

						}

						$hitFound[] = array('text' => $hit->span, 'attributes' => $hitAttributes);
					}

					$page = (string)$pageAttributes['id'];

					// get logical page id of search result
					$parentId = (string)$fullTextHit[$id]->children('http://dfg-viewer.de/')->page->parent->attributes()->id;

					unset($highlightParams);
					// get highlight boxes for all results of a page
					foreach ($hitFound as $key => $hit) {

						$highlightField = $hit['attributes']['x1'] . ',' . $hit['attributes']['y1'] . ',' . $hit['attributes']['x2'] . ',' . $hit['attributes']['y2'];
						if (!in_array($highlightField, $highlightParams)) {
							$highlightParams[] = $highlightField;
						}

					}

					foreach ($hitFound as $key => $hit) {

						unset($spanPreview);
						unset($spanText);

						if (!empty($hit['attributes']['preview'])) {

							$spanPreview = '<span class="sru-preview"><img src="'.$hit['attributes']['preview'].'"></span>';

						}

						if (is_object($hit['text'])) {

							$spanText = '<span class="sru-textsnippet">';

							foreach ($hit['text'] as $key => $text) {

								if ($text->attributes()->class[0] == 'highlight') {

									$spanText .= '<span class="highlight">'.$text.'</span>';

								} else {

									$spanText .= $text;

								}

							}
							$spanText .= '</span>';

						}

						$origImageParams = '0,' . $pageAttributes['width'] . ',' . $pageAttributes ['height'];

						unset($data);

						$data['link'] = $parentId;
						$data['page'] = $page;
						$data['text'] = $spanText;
						$data['previewImage'] = $spanPreview;
						$data['previewText'] = $spanText;
						$data['origImage'] = $origImageParams;
						$data['hightlight'] = urlencode(serialize($highlightParams));

						$results[] = $data;
					}
				}

			}

		} else {

			$results['error'] =  $this->pi_getLL('label.noresults') . ' ' . t3lib_div::_GP('q') ;

		}

		echo json_encode($results);

	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dfgviewer/plugins/search/class.tx_dfgviewer_sru_eid.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dfgviewer/plugins/search/class.tx_dfgviewer_sru_eid.php']);
}

$cObj = t3lib_div::makeInstance('tx_dfgviewer_sru_eid');

$cObj->main();

?>
