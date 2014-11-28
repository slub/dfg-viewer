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

		$url = t3lib_div::_GP('sru').t3lib_div::_GP('q');

$fp = fopen('/home/ab/public_html/sru_eid.txt', 'a');
fwrite($fp, $url . "\n");
		// make request to SRU service
		$sruXML = simplexml_load_file($url);

		if ($sruXML !== FALSE) {

			// the result may be a valid <srw:searchRetrieveResponse> or some HTML code
			//~ $content = "super";

			$sruResponse = $sruXML->xpath('/srw:searchRetrieveResponse');

			if ($sruResponse === FALSE) {

				// no <srw:searchRetrieveResponse>
				//~ $content = "error";
				return '<ul><li>kein Ergebnis</li></ul>';

			}

			$sruRecords = $sruXML->xpath('/srw:searchRetrieveResponse/srw:records/srw:record');

			foreach ($sruRecords as $id => $record) {

				$fullTextHit = $record->xpath('//srw:recordData');

				$results[] = $fullTextHit[$id]->children('http://dfg-viewer.de/')->page->fulltexthit->span;
					//~ fwrite($fp, $id . ' --> ' . print_r($fullTextHit, 1) . "\n");

			}



		} else {

			// something went wrong
			//~ $content = "error2";
			return '<ul><li>kein Ergebnis</li></ul>';

		}



fwrite($fp, $content . "\n");
//~ fwrite($fp, "sodelle \n");
//~ fwrite($fp, $sruXML->asXML() . "\n");
fclose($fp);

		$content = '<ul>';
		foreach ($results as $result) {

			$content .= '<li>';

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
