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

use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

// Register plugins.
ExtensionManagementUtility::addPItoST43($_EXTKEY, 'Classes/Plugins/GridPager.php', '_gridpager', 'list_type', TRUE);

ExtensionManagementUtility::addPItoST43($_EXTKEY, 'Classes/Plugins/Uri.php', '_uri', 'list_type', TRUE);

ExtensionManagementUtility::addPItoST43($_EXTKEY, 'Classes/Plugins/NewspaperCalendar.php', '_newspapercalendar', 'list_type', TRUE);

ExtensionManagementUtility::addPItoST43($_EXTKEY, 'Classes/Plugins/NewspaperYears.php', '_newspaperyears', 'list_type', TRUE);

ExtensionManagementUtility::addPItoST43($_EXTKEY, 'Classes/Plugins/Sru/Sru.php', '_sru', 'list_type', TRUE);

// Register eID handlers.
$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['tx_dfgviewer_sru_eid'] = \Slub\Dfgviewer\Plugins\Sru\SruEid::class . '::main';

//register rte settings
$GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['dfgviewer'] =
    'EXT:' . $_EXTKEY . '/Configuration/Yaml/Rte/Default.yaml';
