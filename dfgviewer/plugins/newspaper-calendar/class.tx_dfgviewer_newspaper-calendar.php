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
 * Plugin 'DFG-Viewer: Newspaper Calendar' for the 'dfgviewer' extension.
 *
 * @author	Alexander Bigga <alexander.bigga@slub-dresden.de>
 * @copyright	Copyright (c) 2014, Alexander Bigga, SLUB Dresden
 * @package	TYPO3
 * @subpackage	tx_dfgviewer
 * @access	public
 */
class tx_dfgviewer_newspapercalendar extends tx_dlf_plugin {

	public $extKey = 'dfgviewer';

	public $scriptRelPath = 'plugins/newspaper-calendar/class.tx_dfgviewer_newspaper-calendar.php';

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
			$this->piVars['page'] = tx_dlf_helper::intInRange($this->piVars['page'], 1, $this->doc->numPages, 1);

		}

		$toc = $this->doc->tableOfContents;
//~ t3lib_utility_Debug::debug($toc, 'tx_dfgviewer_newspaperyear: conf... ');
//~ t3lib_utility_Debug::debug($conf, 'tx_dfgviewer_newspaperyear: conf... ');
//~ t3lib_utility_Debug::debug($this->piVars, 'tx_dfgviewer_newspaperyear: prefixId... ');

		// Load template file.
		if (!empty($this->conf['templateFile'])) {

			$this->template = $this->cObj->getSubpart($this->cObj->fileResource($this->conf['templateFile']), '###TEMPLATE###');

		} else {

			$this->template = $this->cObj->getSubpart($this->cObj->fileResource('EXT:dfgviewer/plugins/newspaper-calendar/template.tmpl'), '###TEMPLATE###');

		}

		// Get subpart templates
		$subparts['template'] = $this->template;
		$subparts['month'] = $this->cObj->getSubpart($subparts['template'], '###CALMONTH###');
		$subparts['week'] = $this->cObj->getSubpart($subparts['singlerow'], '###CALWEEK###');

		$year = (int)$toc[0]['children'][0]['label'];

		$subPartContent = '';

		foreach($toc[0]['children'][0]['children'] as $id => $mo) {
			$month[$mo['label']] = $id;
		}

		for ($i = 1; $i <= 12; $i++) {

			$markerArray = array (
				'###DAYMON_NAME###' => strftime('%a', strtotime('last Monday')),
				'###DAYTUE_NAME###' => strftime('%a', strtotime('last Tuesday')),
				'###DAYWED_NAME###' => strftime('%a', strtotime('last Wednesday')),
				'###DAYTHU_NAME###' => strftime('%a', strtotime('last Thursday')),
				'###DAYFRI_NAME###' => strftime('%a', strtotime('last Friday')),
				'###DAYSAT_NAME###' => strftime('%a', strtotime('last Saturday')),
				'###DAYSUN_NAME###' => strftime('%a', strtotime('last Sunday')),
				'###MONTHNAME###' => strftime('%B', strtotime($year . '-' . $i . '-1'))
			);

			// Get week subpart template
			$subWeekTemplate = $this->cObj->getSubpart($subparts['month'], '###CALWEEK###');
			$subWeekPartContent = '';

			$firstOfMonth = strtotime($year . '-' . $i . '-1');
			$lastOfMonth = strtotime('last day of', ($firstOfMonth));
			$firstOfMonthStart = strtotime('last Monday', $firstOfMonth);

			// max 6 calendar weeks in a month
			for ($j = 0; $j <= 5; $j++) {

				$weekArray = array();

				$firstDayOfWeek = strtotime('+ ' . $j . ' Week', $firstOfMonthStart);

				$weekArray = array(
					'###DAYMON###' => '&nbsp;',
					'###DAYTUE###' => '&nbsp;',
					'###DAYWED###' => '&nbsp;',
					'###DAYTHU###' => '&nbsp;',
					'###DAYFRI###' => '&nbsp;',
					'###DAYSAT###' => '&nbsp;',
					'###DAYSUN###' => '&nbsp;',
				);

				// 7 days per week ;-)
				for ($k = 0; $k <= 6; $k++) {

					$currentDayTime = strtotime('+ '.$k.' Day', $firstDayOfWeek);

					if ( $currentDayTime >= $firstOfMonth && $currentDayTime <= $lastOfMonth) {

						$currentMonth = strftime('%m', $currentDayTime);

						if ($month[$currentMonth] == ($i - 1)) {
							// $i == month index is wrong, but for now...
							$dayLinks     = $toc[0]['children'][0]['children'][($i -1)]['children'][0]['label'];
							$dayPoints    = $toc[0]['children'][0]['children'][($i -1)]['children'][0]['children'][0]['points'];
							$dayLinkLabel = $toc[0]['children'][0]['children'][($i -1)]['children'][0]['children'][0]['label'];
						}

						if (strlen($dayLinks) > 0) {

							$linkConf = array (
								'useCacheHash' => 1,
								'parameter' => $this->conf['targetPid'],
								'additionalParams' => '&' . $this->prefixId . '[id]=' . $dayPoints,
								'title' => $dayLinkLabel
							);
							$dayLinksText = $this->cObj->typoLink($dayLinks, $linkConf);
						}

						switch (strftime('%u', strtotime('+ '.$k.' Day', $firstDayOfWeek))) {
							case '1': $weekArray['###DAYMON###'] = ($dayLinks === strftime('%d', $currentDayTime)) ? $dayLinksText : strftime('%d', $currentDayTime);
									break;
							case '2': $weekArray['###DAYTUE###'] = ($dayLinks === strftime('%d', $currentDayTime)) ? $dayLinksText : strftime('%d', $currentDayTime);
									break;
							case '3': $weekArray['###DAYWED###'] = ($dayLinks === strftime('%d', $currentDayTime)) ? $dayLinksText : strftime('%d', $currentDayTime);
									break;
							case '4': $weekArray['###DAYTHU###'] = ($dayLinks === strftime('%d', $currentDayTime)) ? $dayLinksText : strftime('%d', $currentDayTime);
									break;
							case '5': $weekArray['###DAYFRI###'] = ($dayLinks === strftime('%d', $currentDayTime)) ? $dayLinksText : strftime('%d', $currentDayTime);
									break;
							case '6': $weekArray['###DAYSAT###'] = ($dayLinks === strftime('%d', $currentDayTime)) ? $dayLinksText : strftime('%d', $currentDayTime);
									break;
							case '7': $weekArray['###DAYSUN###'] = ($dayLinks === strftime('%d', $currentDayTime)) ? $dayLinksText : strftime('%d', $currentDayTime);
									break;
						}
					}
				}
				//~ // fill the weeks
				$subWeekPartContent .= $this->cObj->substituteMarkerArray($subWeekTemplate, $weekArray);
			}

			// fill the month markers
			$subPartContent .= $this->cObj->substituteMarkerArray($subparts['month'], $markerArray);
			// fill the week markers
			$subPartContent = $this->cObj->substituteSubpart($subPartContent, '###CALWEEK###', $subWeekPartContent);
		}

		$markerArray = array (
			'###CALYEAR###' => $year
		);
		$this->template = $this->cObj->substituteMarkerArray($this->template, $markerArray);

		return $this->cObj->substituteSubpart($this->template, '###CALMONTH###', $subPartContent);
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dfgviewer/plugins/newspaper-calendar/class.tx_dfgviewer_newspaper-calendar.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dfgviewer/plugins/newspaper-calendar/class.tx_dfgviewer_newspaper-calendar.php']);
}

?>
