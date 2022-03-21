<?php
defined('TYPO3_MODE') or die();

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
