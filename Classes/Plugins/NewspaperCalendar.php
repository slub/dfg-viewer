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

use Kitodo\Dlf\Common\AbstractPlugin;
use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * Plugin 'DFG-Viewer: Newspaper Calendar' for the 'dfgviewer' extension.
 *
 * @author	Alexander Bigga <alexander.bigga@slub-dresden.de>
 * @copyright	Copyright (c) 2014, Alexander Bigga, SLUB Dresden
 * @package	TYPO3
 * @subpackage	tx_dfgviewer
 * @access	public
 */
class NewspaperCalendar extends AbstractPlugin {

    public $extKey = 'dfgviewer';

    public $scriptRelPath = 'Classes/Plugins/NewspaperCalendar.php';

    const CHILDREN = 'children';
    const LABEL = 'label';
    const ORDERLABEL = 'orderlabel';
    const NBSP = '&nbsp;';

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

        $months = array();
        $monthLabel = '';

        foreach($toc[0][self::CHILDREN][0][self::CHILDREN] as $id => $month) {
            // this variable is needed only for case where there is one month
            $monthLabel = $this->getMonthLabel($month);
            $monthNum = $this->getMonth($month);
            $months[$monthNum] = $id;
            $allIssuesCount += count($month[self::CHILDREN]);
        }

        // Load template file.
        $this->getTemplate('###TEMPLATE###');

        // Get subpart templates
        $subparts['template'] = $this->template;

        $subPartContent = '';

        $years = $this->getYears($toc);

        if (count($years) == 1) {
            $subPartContent .= $this->getTemplateForYear($subparts, $toc, (int)$years[0], $months, 1, 12, false);
        } else if (count($years) == 2) {
            if (count($months) == 1) {
                $year = strpos($monthLabel, $years[0]) !== false ? $years[0] : $years[1];
                $subPartContent .= $this->getTemplateForYear($subparts, $toc, (int)$year, $months, key($months), key($months));
            } else {
                $firstMonth = $this->getMonthForTemplate(key($months), true);
                end($months);
                $lastMonth = $this->getMonthForTemplate(key($months), false);

                $subPartContent .= $this->getTemplateForYear($subparts, $toc, (int)$years[0], $months, $firstMonth, 12);
                $subPartContent .= $this->getTemplateForYear($subparts, $toc, (int)$years[1], $months, 1, $lastMonth);
            }
        }

        return $this->templateService->substituteSubpart($this->template, '###CALYEARWRAPPER###', $subPartContent);
    }

    /**
     * Get template for given year.
     *
     * @access	private
     *
     * @param array $subparts of template
     * @param array $toc table of content
     * @param int $year for which template is going to be build
     * @param array $months for which issues were found
     * @param int $firstMonth - January - 1, February - 2, ..., December - 12
     * @param int $lastMonth - January - 1, February - 2, ..., December - 12
     * @param bool $hasMoreYears
     *
     * @return string
     */
    private function getTemplateForYear($subparts, $toc, $year, $months, $firstMonth, $lastMonth, $hasMoreYears = true) {
        $subparts["year"] = $this->templateService->getSubpart($subparts['template'], '###CALYEARWRAPPER###');
        $subparts['month'] = $this->templateService->getSubpart($subparts['year'], '###CALMONTH###');
        $subparts['singleissue'] = $this->templateService->getSubpart($subparts['issuelist'], '###SINGLEISSUE###');

        $allIssues[] = array();
        $subPartContent = '';

        for ($i = $firstMonth; $i <= $lastMonth; $i++) {

            $markerArray = array (
                '###DAYMON_NAME###' => strftime('%a', strtotime('last Monday')),
                '###DAYTUE_NAME###' => strftime('%a', strtotime('last Tuesday')),
                '###DAYWED_NAME###' => strftime('%a', strtotime('last Wednesday')),
                '###DAYTHU_NAME###' => strftime('%a', strtotime('last Thursday')),
                '###DAYFRI_NAME###' => strftime('%a', strtotime('last Friday')),
                '###DAYSAT_NAME###' => strftime('%a', strtotime('last Saturday')),
                '###DAYSUN_NAME###' => strftime('%a', strtotime('last Sunday')),
                '###MONTHNAME###' 	=> strftime('%B', strtotime($year . '-' . $i . '-1'))
            );

            // Get week subpart template
            $subWeekTemplate = $this->templateService->getSubpart($subparts['month'], '###CALWEEK###');
            $subWeekPartContent = '';

            $firstOfMonth = strtotime($year . '-' . $i . '-1');
            $lastOfMonth = strtotime('last day of', ($firstOfMonth));
            $firstOfMonthStart = strtotime('last Monday', $firstOfMonth);

            // max 6 calendar weeks in a month
            for ($j = 0; $j <= 5; $j++) {

                $firstDayOfWeek = strtotime('+ ' . $j . ' Week', $firstOfMonthStart);

                $weekArray = array(
                    '###DAYMON###' => self::NBSP,
                    '###DAYTUE###' => self::NBSP,
                    '###DAYWED###' => self::NBSP,
                    '###DAYTHU###' => self::NBSP,
                    '###DAYFRI###' => self::NBSP,
                    '###DAYSAT###' => self::NBSP,
                    '###DAYSUN###' => self::NBSP,
                );

                // 7 days per week ;-)
                for ($k = 0; $k <= 6; $k++) {

                    $currentDayTime = strtotime('+ '.$k.' Day', $firstDayOfWeek);

                    if ( $currentDayTime >= $firstOfMonth && $currentDayTime <= $lastOfMonth ) {

                        $dayLinks = '';
                        $dayLinksText = [];
                        $currentMonth = date('n', $currentDayTime);

                        if ($toc[0][self::CHILDREN][0][self::CHILDREN][$months[$currentMonth]][self::CHILDREN]) {

                            foreach($toc[0][self::CHILDREN][0][self::CHILDREN][$months[$currentMonth]][self::CHILDREN] as $id => $day) {

                                $dayNum = $this->getDay($day);

                                if ($dayNum === (int)date('j', $currentDayTime) && $day['type'] === 'day') {
                                    if ($hasMoreYears && strpos($this->getDayLabel($day), (string)$year) === false) {
                                        continue;
                                    }

                                    $dayLinks = $dayNum;

                                    foreach($day[self::CHILDREN] as $id => $issue) {
                                        $dayPoints	= $issue['points'];
                                        $dayLinkLabel = empty($issue[self::LABEL]) ? strftime('%x', $currentDayTime) : $issue[self::LABEL];

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

                            $dayLinkDiv = '<div class="contains-issues">' . strftime('%d', $currentDayTime) . '</div>' . $this->renderIssues($dayLinksText);
                        }

                        switch (strftime('%u', strtotime('+ '.$k.' Day', $firstDayOfWeek))) {
                            case '1':
                                $weekArray['###DAYMON###'] = ((int)$dayLinks === (int)date('j', $currentDayTime)) ? $dayLinkDiv : strftime('%d', $currentDayTime);
                                break;
                            case '2':
                                $weekArray['###DAYTUE###'] = ((int)$dayLinks === (int)date('j', $currentDayTime)) ? $dayLinkDiv : strftime('%d', $currentDayTime);
                                break;
                            case '3':
                                $weekArray['###DAYWED###'] = ((int)$dayLinks === (int)date('j', $currentDayTime)) ? $dayLinkDiv : strftime('%d', $currentDayTime);
                                break;
                            case '4':
                                $weekArray['###DAYTHU###'] = ((int)$dayLinks === (int)date('j', $currentDayTime)) ? $dayLinkDiv : strftime('%d', $currentDayTime);
                                break;
                            case '5':
                                $weekArray['###DAYFRI###'] = ((int)$dayLinks === (int)date('j', $currentDayTime)) ? $dayLinkDiv : strftime('%d', $currentDayTime);
                                break;
                            case '6':
                                $weekArray['###DAYSAT###'] = ((int)$dayLinks === (int)date('j', $currentDayTime)) ? $dayLinkDiv : strftime('%d', $currentDayTime);
                                break;
                            case '7':
                                $weekArray['###DAYSUN###'] = ((int)$dayLinks === (int)date('j', $currentDayTime)) ? $dayLinkDiv : strftime('%d', $currentDayTime);
                                break;
                        }
                    }
                }
                // fill the weeks
                $subWeekPartContent .= $this->templateService->substituteMarkerArray($subWeekTemplate, $weekArray);
            }

            // fill the month markers
            $subPartContent .= $this->templateService->substituteMarkerArray($subparts['month'], $markerArray);

            // fill the week markers
            $subPartContent = $this->templateService->substituteSubpart($subPartContent, '###CALWEEK###', $subWeekPartContent);
        }

        // link to years overview
        $linkConf = array (
            'useCacheHash' => 1,
            'parameter' => $this->conf['targetPid'],
            'additionalParams' => '&' . $this->prefixId . '[id]=' . urlencode($toc[0]['points']),
        );
        $allYearsLink = $this->cObj->typoLink(htmlspecialchars($this->pi_getLL('allYears', '')) . ' ' .$toc[0][self::LABEL], $linkConf);

        // link to this year itself
        $linkConf = array (
            'useCacheHash' => 1,
            'parameter' => $this->conf['targetPid'],
            'additionalParams' => '&' . $this->prefixId . '[id]=' . urlencode($this->piVars['id']),
        );
        $yearLink = $this->cObj->typoLink($year, $linkConf);


        // prepare list as alternative of the calendar view
        $issueListTemplate = $this->templateService->getSubpart($subparts['year'], '###ISSUELIST###');

        $subparts['singleissue'] = $this->templateService->getSubpart($issueListTemplate, '###SINGLEISSUE###');

        $allDaysList = array();
        $subPartContentList = '';

        foreach($allIssues as $id => $issue) {

            // only add date output, if not already done (multiple issues per day)
            if (! in_array($issue[0], $allDaysList)) {
                $allDaysList[] = $issue[0];
                $subPartContentList .= $issue[0];
            }

            $subPartContentList .= $this->templateService->substituteMarker($subparts['singleissue'], '###ITEM###', $issue[1]);
        }

        $issueListTemplate = $this->templateService->substituteSubpart($issueListTemplate, '###SINGLEISSUE###', $subPartContentList);

        $subparts['year'] = $this->templateService->substituteSubpart($subparts['year'], '###ISSUELIST###', $issueListTemplate);

        $markerArray = array (
            '###CALYEAR###' => $yearLink,
            '###CALALLYEARS###' => $allYearsLink
        );

        $subparts['year'] = $this->templateService->substituteMarkerArray($subparts['year'], $markerArray);

        return $this->templateService->substituteSubpart($subparts['year'], '###CALMONTH###', $subPartContent);
    }

    /**
     * Get array for years. For concert programs it extracts 2 years from label.
     *
     * @access	private
     *
     * @param array $toc
     *
     * @return array
     */
    private function getYears($toc) {
        $year = $toc[0][self::CHILDREN][0][self::LABEL];
        if (empty($year)) {
            $year = $toc[0][self::CHILDREN][0][self::ORDERLABEL];
            if (strpos($year, '/') !== false) {
                return explode('/', $year);
            }
        }
        return array($year);
    }

    /**
     * Get month number. For concert programs it extracts number from label.
     *
     * @access	private
     *
     * @param array $month
     *
     * @return int
     */
    private function getMonth($month) {
        $monthLabel = $this->getMonthLabel($month);

        if (strpos($monthLabel, '-') !== false) {
            $monthLabel = explode('-', $monthLabel)[1];
        }

        return (int)$monthLabel;
    }

    /**
     * Get label for a month. It prefers orderlabel over label.
     *
     * @access	private
     *
     * @param array $month
     *
     * @return string
     */
    private function getMonthLabel($month) {
        return isset($month[self::ORDERLABEL]) ? $month[self::ORDERLABEL] : $month[self::LABEL];
    }

    /**
     * Get day number. For concert programs it extracts number from label.
     *
     * @access	private
     *
     * @param array $day
     *
     * @return int
     */
    private function getDay($day) {
        $dayLabel = $this->getDayLabel($day);

        if (strpos($dayLabel, '-') !== false) {
            $dayLabel = explode('-', $dayLabel)[2];
        }

        return (int)$dayLabel;
    }

    /**
     * Get label for a day. It prefers orderlabel over label.
     *
     * @access	private
     *
     * @param array $day
     *
     * @return string
     */
    private function getDayLabel($day) {
        return isset($day[self::ORDERLABEL]) ? $day[self::ORDERLABEL] : $day[self::LABEL];
    }

    /**
     * Get month to begin or end in calendar view.
     *
     * @access	private
     *
     * @param int $month
     * @param bool $isFirstMonth
     *
     * @return string
     */
    private function getMonthForTemplate($month, $isFirstMonth) {
        if ($month <= 4) {
            return $isFirstMonth ? 1 : 4;
        } else if ($month <= 8) {
            return $isFirstMonth ? 5 : 8;
        } else {
            return $isFirstMonth ? 9 : 12;
        }
    }

    /**
     * Render issues from that day in an unordered list.
     *
     * @access	private
     *
     * @param array $dayLinksText
     *
     * @return string
     */
    private function renderIssues($dayLinksText) {
        $dayLinksList = '';

        if (is_array($dayLinksText) && count($dayLinksText) > 0) {
            $dayLinksList = '<ul class="issues">';
            foreach ($dayLinksText as $link) {
                $dayLinksList .= '<li>'.$link.'</li>';
            }
            $dayLinksList .= '</ul>';
        }

        return $dayLinksList;
    }
}
