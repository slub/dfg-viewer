<?php
defined('TYPO3') or die();

// Plugin "uri".
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['dfgviewer_uri'] = 'layout,select_key,pages,recursive';

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Dfgviewer',
    'Uri',
    'LLL:EXT:dlf/Resources/Private/Language/locallang_be.xlf:plugins.search.title',
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Dfgviewer',
    'SRU',
    'LLL:EXT:dfgviewer/Resources/Private/Language/locallang_be.xlf:plugins.sru.title',
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
    'tt_content',
    'CType',
    [
        // title
        'Example',
        // plugin signature: extkey_identifier
        'dfgviewer_metsvalidatior',
        // icon identifier
        'content-form',
    ],
    'textmedia',
    'after'
);
