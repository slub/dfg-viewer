<?php
namespace Slub\Dfgviewer\Plugins\Sru;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Plugin\AbstractPlugin;

/**
 * Plugin 'DFG-Viewer: SRU Client eID script' for the 'dfgviewer' extension.
 *
 * @author	Alexander Bigga <alexander.bigga@slub-dresden.de>
 * @copyright	Copyright (c) 2014, Alexander Bigga, SLUB Dresden
 * @package	TYPO3
 * @subpackage	tx_dfgviewer
 * @access	public
 */
class SruEid extends AbstractPlugin {

	/**
	 *
	 */
	public $cObj;


	/**
	 * The main method of the eID-Script
	 *
	 * @access	public
	 *
	 * @return string JSON encoded return value
	 */
	public function main() {

		$this->cObj = GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\ContentObject\\ContentObjectRenderer');

		$this->extKey = 'dfgviewer';

		$this->scriptRelPath = 'Classes/Plugins/Sru/SruEid.php';

		$this->LLkey = GeneralUtility::_GP('L') ? GeneralUtility::_GP('L') : 'default';

		$this->pi_loadLL();

		$url = GeneralUtility::_GP('sru') . '?operation=searchRetrieve&version=1.2&startRecord=1&maximumRecords=10&amp;recordSchema=dfg-viewer/page&amp;query=' . urlencode(GeneralUtility::_GP('q'));

		// make request to SRU service
		$sruXML = simplexml_load_file($url);

		if ($sruXML !== FALSE) {

			// the result may be a valid <srw:searchRetrieveResponse> or some HTML code

			$sruResponse = $sruXML->xpath('/srw:searchRetrieveResponse');

			if ($sruResponse === FALSE) {

				$results['error'] =  $this->pi_getLL('label.noresults') . ' ' . GeneralUtility::_GP('q') ;

			} else {

				$sruRecords = $sruXML->xpath('/srw:searchRetrieveResponse/srw:records/srw:record');

				if ($sruRecords === FALSE || empty($sruRecords) ) {

					$results['error'] =  $this->pi_getLL('label.noresults') . ' ' . GeneralUtility::_GP('q') ;

				}

				foreach ($sruRecords as $id => $record) {

					$fullTextHit = $record->xpath('//srw:recordData');

					$pageAttributes = '';

					foreach($fullTextHit[$id]->children('http://dfg-viewer.de/')->page->attributes() as $key => $val) {

						$pageAttributes[$key] = $val;

					}

					$hitFound = array();

					// there may be multiple hits on a page per search query
					foreach ($fullTextHit[$id]->children('http://dfg-viewer.de/')->page->fulltexthit as $hit) {

						$hitAttributes = array();

						foreach ($hit->attributes() as $key => $val) {

							$hitAttributes[$key] = $val;

						}

						$hitFound[] = array('text' => $hit->span, 'attributes' => $hitAttributes);
					}

					$page = (string)$pageAttributes['id'];

					// get METS file of search hit
					$parentUrl = (string)$fullTextHit[$id]->children('http://dfg-viewer.de/')->page->parent->attributes()->url;

					// unset $hightlightParams but make sure, it's an array()
					$highlightParams = array();

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

						$data['link'] = $parentUrl;
						$data['page'] = $page;
						$data['text'] = $spanText;
						$data['previewImage'] = $spanPreview;
						$data['previewText'] = $spanText;
						$data['origImage'] = $origImageParams;
						$data['highlight'] = urlencode(serialize($highlightParams));

						$results[] = $data;
					}
				}

			}

		} else {

			$results['error'] =  $this->pi_getLL('label.noresults') . ' ' . GeneralUtility::_GP('q') ;

		}

		echo json_encode($results);

	}

}

$cObj = GeneralUtility::makeInstance('tx_dfgviewer_sru_eid');

$cObj->main();
