<?php
namespace Slub\Dfgviewer\Plugins;

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

use Kitodo\Dlf\Common\AbstractPlugin;
use TYPO3\CMS\Core\Service\MarkerBasedTemplateService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 */

/**
 * Plugin 'DFG-Viewer: Grid Pager' for the 'dfgviewer' extension.
 *
 * @author  Sebastian Meyer <sebastian.meyer@slub-dresden.de>
 * @copyright  Copyright (c) 2012, Sebastian Meyer, SLUB Dresden
 * @package  TYPO3
 * @subpackage  tx_dfgviewer
 * @access  public
 */
class GridPager extends AbstractPlugin {

  public $extKey = 'dfgviewer';

  public $scriptRelPath = 'Classes/Plugins/GridPager.php';

  /**
   * The main method of the PlugIn
   *
   * @access  public
   *
   * @param  string    $content: The PlugIn content
   * @param  array    $conf: The PlugIn configuration
   *
   * @return  string    The content that is displayed on the website
   */
  public function main($content, $conf) {

    $this->init($conf);

    // Load current document.
    $this->loadDocument();

    if ($this->doc === NULL || $this->doc->numPages < 1) {

      // Quit without doing anything if required variables are not set.
      return $content;

    } else {

      // Get overall number of pages.
      $maxPointer = intval(ceil($this->doc->numPages / $this->conf['limit'])) - 1;

      // Set some variable defaults.
      // $this->piVars['page'] may be integer or string (physical structure @ID)
      if ( (int)$this->piVars['page'] > 0 || empty($this->piVars['page'])) {

        $this->piVars['page'] = MathUtility::forceIntegerInRange((int)$this->piVars['page'], 1, $this->doc->numPages, 1);

      } else {

        $this->piVars['page'] = array_search($this->piVars['page'], $this->doc->physicalStructure);

      }

      if (!empty($this->piVars['page'])) {

        $this->piVars['pointer'] = intval(floor(($this->piVars['page'] - 1) / $this->conf['limit']));

      }

      if (!empty($this->piVars['pointer']) && (($this->piVars['pointer'] * $this->conf['limit']) + 1) <= $this->doc->numPages) {

        $this->piVars['pointer'] = max(intval($this->piVars['pointer']), 0);

      } else {

        $this->piVars['pointer'] = 0;

      }

    }

        $templateService = GeneralUtility::makeInstance(MarkerBasedTemplateService::class);

    // Load template file.
    if (!empty($this->conf['templateFile'])) {
      $this->template = $templateService->getSubpart($GLOBALS['TSFE']->tmpl->getFileName($this->conf['templateFile']), '###TEMPLATE###');
    } else {
      $this->template = $templateService->getSubpart($GLOBALS['TSFE']->tmpl->getFileName('EXT:dfgviewer/Resources/Private/Templates/Plugins/Dfgviewer/GridPager.tmpl'), '###TEMPLATE###');
    }

    // Link to first page.
    if ($this->piVars['pointer'] > 0) {

      $markerArray['###FIRST###'] = $this->pi_linkTP_keepPIvars($this->pi_getLL('firstPage', '', TRUE),
        array (
          'pointer' => 0,
          'page' => NULL
        ), TRUE);
    } else {

      $markerArray['###FIRST###'] = '<span>'.$this->pi_getLL('firstPage', '', TRUE).'</span>';

    }

    // Link back X pages.
    if ($this->piVars['pointer'] >= $this->conf['pageStep']) {

      $markerArray['###BACK###'] = $this->pi_linkTP_keepPIvars(sprintf($this->pi_getLL('backXPages', '', TRUE), $this->conf['pageStep']),
        array (
          'pointer' => $this->piVars['pointer'] - $this->conf['pageStep'],
          'page' => ($this->piVars['pointer'] - $this->conf['pageStep']) * $this->conf['limit'] + 1
        ), TRUE);
    } else {

      $markerArray['###BACK###'] = '<span>'.sprintf($this->pi_getLL('backXPages', '', TRUE), $this->conf['pageStep']).'</span>';

    }

    // Link to previous page.
    if ($this->piVars['pointer'] > 0) {

      $markerArray['###PREVIOUS###'] = $this->pi_linkTP_keepPIvars($this->pi_getLL('prevPage', '', TRUE),
        array (
          'pointer' => $this->piVars['pointer'] - 1,
          'page' => (($this->piVars['pointer'] - 1) * $this->conf['limit']) + 1
        ), TRUE);
    } else {

      $markerArray['###PREVIOUS###'] = '<span>'.$this->pi_getLL('prevPage', '', TRUE).'</span>';

    }

    // Link to next page.
    if ($this->piVars['pointer'] < $maxPointer) {

      $markerArray['###NEXT###'] = $this->pi_linkTP_keepPIvars($this->pi_getLL('nextPage', '', TRUE),
        array (
          'pointer' => $this->piVars['pointer'] + 1,
          'page' => ($this->piVars['pointer'] + 1) * $this->conf['limit'] + 1
        ), TRUE);
    } else {

      $markerArray['###NEXT###'] = '<span>'.$this->pi_getLL('nextPage', '', TRUE).'</span>';

    }

    // Link forward X pages.
    if ($this->piVars['pointer'] < $maxPointer - $this->conf['pageStep']) {

      $markerArray['###FORWARD###'] = $this->pi_linkTP_keepPIvars(sprintf($this->pi_getLL('forwardXPages', '', TRUE), $this->conf['pageStep']),
        array (
          'pointer' => $this->piVars['pointer'] + $this->conf['pageStep'],
          'page' => ($this->piVars['pointer'] + $this->conf['pageStep']) * $this->conf['limit'] + 1
        ), TRUE);
    } else {

      $markerArray['###FORWARD###'] = '<span>'.sprintf($this->pi_getLL('forwardXPages', '', TRUE), $this->conf['pageStep']).'</span>';

    }

    // Link to last page.
    if ($this->piVars['pointer'] < $maxPointer) {

      $markerArray['###LAST###'] = $this->pi_linkTP_keepPIvars($this->pi_getLL('lastPage', '', TRUE),
        array (
          'pointer' => $maxPointer,
          'page' => $maxPointer * $this->conf['limit'] + 1
        ), TRUE);

    } else {

      $markerArray['###LAST###'] = '<span>'.$this->pi_getLL('lastPage', '', TRUE).'</span>';

    }

    $content .= $templateService->substituteMarkerArray($this->template, $markerArray);

    return $this->pi_wrapInBaseClass($content);

  }

}
