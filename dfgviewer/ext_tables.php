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

if (!defined ('TYPO3_MODE')) die ('Access denied.');

// Register static typoscript.
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'DFG Viewer');

// Register plugins.
\TYPO3\CMS\Core\Utility\GeneralUtility::loadTCA('tt_content');

// Plugin "amd".
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_amd'] = 'layout,select_key,pages,recursive';

$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_amd'] = 'pi_flexform';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(array('LLL:EXT:dfgviewer/locallang.xml:tt_content.dfgviewer_amd', $_EXTKEY.'_amd'), 'list_type');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($_EXTKEY.'_amd', 'FILE:EXT:'.$_EXTKEY.'/plugins/amd/flexform.xml');

// Plugin "gridpager".
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_gridpager'] = 'layout,select_key,pages,recursive';

$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_gridpager'] = 'pi_flexform';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(array('LLL:EXT:dfgviewer/locallang.xml:tt_content.dfgviewer_gridpager', $_EXTKEY.'_gridpager'), 'list_type');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($_EXTKEY.'_gridpager', 'FILE:EXT:'.$_EXTKEY.'/plugins/gridpager/flexform.xml');

// Plugin "uri".
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_uri'] = 'layout,select_key,pages,recursive';

$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_uri'] = 'pi_flexform';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(array('LLL:EXT:dfgviewer/locallang.xml:tt_content.dfgviewer_uri', $_EXTKEY.'_uri'), 'list_type');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($_EXTKEY.'_uri', 'FILE:EXT:'.$_EXTKEY.'/plugins/uri/flexform.xml');

// Register modules.
if (TYPO3_MODE == 'BE')	{

	// Module "setup".
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addModule('txdlfmodules', 'txdfgviewersetup', '', \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY).'modules/setup/');

	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('_MOD_txdlfmodules_txdfgviewersetup','EXT:dfgviewer/modules/setup/locallang_mod.xml');

}
