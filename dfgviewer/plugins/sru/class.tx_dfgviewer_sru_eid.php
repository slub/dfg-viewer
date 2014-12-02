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

		$this->LLkey = t3lib_div::_GP('L') ? t3lib_div::_GP('L') : 'default';
		 $this->extKey = 'dfgviewer';
		 $this->scriptRelPath = 'plugins/sru/class.tx_dfgviewer_sru_eid.php';
		$this->pi_loadLL();

		$url = t3lib_div::_GP('sru').t3lib_div::_GP('q');

//~ $fp = fopen('/home/ab/public_html/sru_eid.txt', 'a');
//~ fwrite($fp, $url . "\n");
		// make request to SRU service
		$sruXML = simplexml_load_file($url);

		if ($sruXML !== FALSE) {

			// the result may be a valid <srw:searchRetrieveResponse> or some HTML code

			$sruResponse = $sruXML->xpath('/srw:searchRetrieveResponse');

			if ($sruResponse === FALSE) {

				$results[] =  $this->pi_getLL('label.noresults') . ' ' . t3lib_div::_GP('q');

			} else {

				$sruRecords = $sruXML->xpath('/srw:searchRetrieveResponse/srw:records/srw:record');

				foreach ($sruRecords as $id => $record) {

					$fullTextHit = $record->xpath('//srw:recordData');

					$text = $fullTextHit[$id]->children('http://dfg-viewer.de/')->page->fulltexthit->span;

					$hitAttributes = '';

					foreach($fullTextHit[$id]->children('http://dfg-viewer.de/')->page->fulltexthit[0]->attributes() as $key => $val) {

						$hitAttributes[$key] = $val;

					}

					$page = $fullTextHit[$id]->children('http://dfg-viewer.de/')->page->pagination;

					if (!empty($hitAttributes['preview'])) {

						$spanPreview = '<span class="sru-preview"><img src="'.$hitAttributes['preview'].'"></span>';

					}

					if (is_object($text)) {

						$spanText = '<span class="sru-textsnippet">' . $text[0] . '<span class="sru-searchquery">'.$text[1].'</span>' . $text[2] . '</span>';

					}

					$results[] = '<a href="' . t3lib_div::_GP('action') . '?' . 'tx_dlf[id]=' . urlencode(t3lib_div::_GP('id')) . '&tx_dlf[page]=' . $page  . '" '.$style.' title="'.$coo['x1'].'">'.$spanPreview . ' ' . $spanText.'</a> ';

				}

			}


		} else {

			$results[] =  $this->pi_getLL('label.noresults') . ' ' . t3lib_div::_GP('q');

		}

//~ fwrite($fp, $content . "\n");
//~ fwrite($fp, $sruXML->asXML() . "\n");
//~ fclose($fp);

		$content = '<ul>';
		foreach ($results as $result) {

			$content .= '<li>';
			$content .= $result;

			foreach ($result as $item) {
				$content .= $item . '<br />';
			}

			$content .= '</li>';

		}
		$content .= '</ul>';

		echo $content;

	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dfgviewer/plugins/search/class.tx_dfgviewer_sru_eid.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dfgviewer/plugins/search/class.tx_dfgviewer_sru_eid.php']);
}

$cObj = t3lib_div::makeInstance('tx_dfgviewer_sru_eid');

$cObj->main();

?>
