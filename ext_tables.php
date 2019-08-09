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

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// Plugin "gridpager".
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_gridpager'] = 'layout,select_key,pages,recursive';

$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_gridpager'] = 'pi_flexform';

ExtensionManagementUtility::addPlugin(array('LLL:EXT:dfgviewer/locallang.xml:tt_content.dfgviewer_gridpager', $_EXTKEY.'_gridpager'), 'list_type');

ExtensionManagementUtility::addPiFlexFormValue($_EXTKEY.'_gridpager', 'FILE:EXT:'.$_EXTKEY.'/Configuration/Flexforms/GridPager.xml');

// Plugin "uri".
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_uri'] = 'layout,select_key,pages,recursive';

$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_uri'] = 'pi_flexform';

ExtensionManagementUtility::addPlugin(array('LLL:EXT:dfgviewer/locallang.xml:tt_content.dfgviewer_uri', $_EXTKEY.'_uri'), 'list_type');

ExtensionManagementUtility::addPiFlexFormValue($_EXTKEY.'_uri', 'FILE:EXT:'.$_EXTKEY.'/Configuration/Flexforms/Uri.xml');
