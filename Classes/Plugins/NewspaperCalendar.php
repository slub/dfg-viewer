<?php
namespace Slub\Dfgviewer\Plugins;

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

use \tx_dlf_plugin;
use \TYPO3\CMS\Core\Utility\MathUtility;

/**
 * Plugin 'DFG-Viewer: Newspaper Calendar' for the 'dfgviewer' extension.
 *
 * @author	Alexander Bigga <alexander.bigga@slub-dresden.de>
 * @copyright	Copyright (c) 2014, Alexander Bigga, SLUB Dresden
 * @package	TYPO3
 * @subpackage	tx_dfgviewer
 * @access	public
 */
class NewspaperCalendar extends tx_dlf_plugin {

	public $extKey = 'dfgviewer';

	public $scriptRelPath = 'Classes/Plugins/NewspaperCalendar.php';

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
			$this->piVars['page'] = MathUtility::forceIntegerInRange($this->piVars['page'], 1, $this->doc->numPages, 1);

		}

		$toc = $this->doc->tableOfContents;

		foreach($toc[0]['children'][0]['children'] as $id => $mo) {

			// prefer oderlabel over label
			$monthNum = isset($mo['orderlabel']) ? (int)$mo['orderlabel'] : $mo['label'];

			$month[$monthNum] = $id;

			$allIssuesCount += count($mo['children']);

		}

		// Load template file.
		if (!empty($this->conf['templateFile'])) {

			$this->template = $this->cObj->getSubpart($this->cObj->fileResource($this->conf['templateFile']), '###TEMPLATE###');

		} else {

			$this->template = $this->cObj->getSubpart($this->cObj->fileResource('EXT:dfgviewer/Resources/Private/Templates/Plugins/Dfgviewer/NewspaperCalendar.tmpl'), '###TEMPLATE###');

		}

		// Get subpart templates
		$subparts['template'] = $this->template;
		$subparts['month'] = $this->cObj->getSubpart($subparts['template'], '###CALMONTH###');

		$subparts['singleissue'] = $this->cObj->getSubpart($subparts['issuelist'], '###SINGLEISSUE###');

		$year = (int)$toc[0]['children'][0]['label'];

		$subPartContent = '';

		for ($i = 0; $i <= 11; $i++) {

			$markerArray = array (
				'###DAYMON_NAME###' => strftime('%a', strtotime('last Monday')),
				'###DAYTUE_NAME###' => strftime('%a', strtotime('last Tuesday')),
				'###DAYWED_NAME###' => strftime('%a', strtotime('last Wednesday')),
				'###DAYTHU_NAME###' => strftime('%a', strtotime('last Thursday')),
				'###DAYFRI_NAME###' => strftime('%a', strtotime('last Friday')),
				'###DAYSAT_NAME###' => strftime('%a', strtotime('last Saturday')),
				'###DAYSUN_NAME###' => strftime('%a', strtotime('last Sunday')),
				'###MONTHNAME###' 	=> strftime('%B', strtotime($year . '-' . ($i + 1) . '-1'))
			);

			// Get week subpart template
			$subWeekTemplate = $this->cObj->getSubpart($subparts['month'], '###CALWEEK###');
			$subWeekPartContent = '';

			$firstOfMonth = strtotime($year . '-' . ($i + 1) . '-1');
			$lastOfMonth = strtotime('last day of', ($firstOfMonth));
			$firstOfMonthStart = strtotime('last Monday', $firstOfMonth);

			// max 6 calendar weeks in a month
			for ($j = 0; $j <= 5; $j++) {

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

					if ( $currentDayTime >= $firstOfMonth && $currentDayTime <= $lastOfMonth ) {

						$dayLinks = '';
						$dayLinksText = '';

						$currentMonth = date('n', $currentDayTime);

						if ($toc[0]['children'][0]['children'][$month[$currentMonth]]['children']) {

							foreach($toc[0]['children'][0]['children'][$month[$currentMonth]]['children'] as $id => $day) {

								// prefer oderlabel over label
								$dayNum = isset($day['orderlabel']) ? (int)$day['orderlabel'] : $day['label'];

								if ($dayNum === (int)date('j', $currentDayTime)
									&& $day['type'] === 'day') {

									$dayLinks = $dayNum;

									foreach($day['children'] as $id => $issue) {

										$dayPoints	= $issue['points'];

										$dayLinkLabel = empty($issue['label']) ? strftime('%x', $currentDayTime) : $issue['label'];

										$linkConf = array (
											'useCacheHash' => 1,
											'parameter' => $this->conf['targetPid'],
											'additionalParams' => '&' . $this->prefixId . '[id]=' . urlencode($dayPoints) . '&' . $this->prefixId . '[page]=1',
											'ATagParams' => 'id=' . $issue['id'],
										);
										$dayLinksText[] = $this->cObj->typoLink($dayLinkLabel, $linkConf);

										$allIssues[] = array(strftime('%A, %x', $currentDayTime), $this->cObj->typoLink($dayLinkLabel, $linkConf));
									}
								}

							}

							// render issues from that day in an unordered list
							if (is_array($dayLinksText)) {
								$dayLinksList = '<ul class="issues">';
								foreach ($dayLinksText as $link) {
									$dayLinksList .= '<li>'.$link.'</li>';
								}
								$dayLinksList .= '</ul>';
							}

							$dayLinkDiv = '<div class="contains-issues">' . strftime('%d', $currentDayTime) . '</div>' . $dayLinksList;


						}

						switch (strftime('%u', strtotime('+ '.$k.' Day', $firstDayOfWeek))) {
							case '1': $weekArray['###DAYMON###'] = ((int)$dayLinks === (int)date('j', $currentDayTime)) ? $dayLinkDiv : strftime('%d', $currentDayTime);
									break;
							case '2': $weekArray['###DAYTUE###'] = ((int)$dayLinks === (int)date('j', $currentDayTime)) ? $dayLinkDiv : strftime('%d', $currentDayTime);
									break;
							case '3': $weekArray['###DAYWED###'] = ((int)$dayLinks === (int)date('j', $currentDayTime)) ? $dayLinkDiv : strftime('%d', $currentDayTime);
									break;
							case '4': $weekArray['###DAYTHU###'] = ((int)$dayLinks === (int)date('j', $currentDayTime)) ? $dayLinkDiv : strftime('%d', $currentDayTime);
									break;
							case '5': $weekArray['###DAYFRI###'] = ((int)$dayLinks === (int)date('j', $currentDayTime)) ? $dayLinkDiv : strftime('%d', $currentDayTime);
									break;
							case '6': $weekArray['###DAYSAT###'] = ((int)$dayLinks === (int)date('j', $currentDayTime)) ? $dayLinkDiv : strftime('%d', $currentDayTime);
									break;
							case '7': $weekArray['###DAYSUN###'] = ((int)$dayLinks === (int)date('j', $currentDayTime)) ? $dayLinkDiv : strftime('%d', $currentDayTime);
									break;
						}
					}
				}
				// fill the weeks
				$subWeekPartContent .= $this->cObj->substituteMarkerArray($subWeekTemplate, $weekArray);
			}

			// fill the month markers
			$subPartContent .= $this->cObj->substituteMarkerArray($subparts['month'], $markerArray);

			// fill the week markers
			$subPartContent = $this->cObj->substituteSubpart($subPartContent, '###CALWEEK###', $subWeekPartContent);
		}

		// link to years overview
		$linkConf = array (
			'useCacheHash' => 1,
			'parameter' => $this->conf['targetPid'],
			'additionalParams' => '&' . $this->prefixId . '[id]=' . urlencode($toc[0]['points']),
		);
		$allYearsLink = $this->cObj->typoLink($this->pi_getLL('allYears', '', TRUE) . ' ' .$toc[0]['label'], $linkConf);

		// link to this year itself
		$linkConf = array (
			'useCacheHash' => 1,
			'parameter' => $this->conf['targetPid'],
			'additionalParams' => '&' . $this->prefixId . '[id]=' . urlencode($this->piVars['id']),
		);
		$yearLink = $this->cObj->typoLink($year, $linkConf);


		// prepare list as alternative of the calendar view
		$issueListTemplate = $this->cObj->getSubpart($subparts['template'], '###ISSUELIST###');

		$subparts['singleissue'] = $this->cObj->getSubpart($issueListTemplate, '###SINGLEISSUE###');

		$allDaysList = array();

		foreach($allIssues as $id => $issue) {

			// only add date output, if not already done (multiple issues per day)
			if (! in_array($issue[0], $allDaysList)) {

				$allDaysList[] = $issue[0];

				$subPartContentList .= $issue[0];

			}

			$subPartContentList .= $this->cObj->substituteMarker($subparts['singleissue'], '###ITEM###', $issue[1]);

		}

		$issueListTemplate = $this->cObj->substituteSubpart($issueListTemplate, '###SINGLEISSUE###', $subPartContentList);

		$this->template = $this->cObj->substituteSubpart($this->template, '###ISSUELIST###', $issueListTemplate);

		$markerArray = array (
			'###CALYEAR###' => $yearLink,
			'###CALALLYEARS###' => $allYearsLink
		);

		$this->template = $this->cObj->substituteMarkerArray($this->template, $markerArray);

		return $this->cObj->substituteSubpart($this->template, '###CALMONTH###', $subPartContent);

	}

}
