<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// Plugin "gridpager".
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$extKey.'_gridpager'] = 'layout,select_key,pages,recursive';

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$extKey.'_gridpager'] = 'pi_flexform';

$extKey = 'dfgviewer';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(
    [
        'LLL:EXT:dfgviewer/locallang.xml:tt_content.dfgviewer_gridpager',
        $extKey.'_gridpager'
    ],
    'list_type',
    $extKey
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($extKey.'_gridpager', 'FILE:EXT:'.$extKey.'/Configuration/Flexforms/GridPager.xml');

// Plugin "uri".
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$extKey.'_uri'] = 'layout,select_key,pages,recursive';

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$extKey.'_uri'] = 'pi_flexform';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(
    [
        'LLL:EXT:dfgviewer/locallang.xml:tt_content.dfgviewer_uri',
        $extKey.'_uri'
    ],
    'list_type',
    $extKey
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($extKey.'_uri', 'FILE:EXT:'.$extKey.'/Configuration/Flexforms/Uri.xml');
