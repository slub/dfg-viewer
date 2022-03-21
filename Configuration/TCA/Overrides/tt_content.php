<?php
defined('TYPO3_MODE') or die();

// Plugin "uri".
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['dfgviewer_uri'] = 'layout,select_key,pages,recursive';

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['dfgviewer_uri'] = 'pi_flexform';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(
    [
        'LLL:EXT:dfgviewer/locallang.xml:tt_content.dfgviewer_uri',
        'dfgviewer_uri'
    ],
    'list_type',
    'dfgviewer'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue('dfgviewer_uri', 'FILE:EXT:'.'dfgviewer/Configuration/Flexforms/Uri.xml');
