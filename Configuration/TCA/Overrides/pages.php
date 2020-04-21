<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

if (TYPO3_MODE === 'BE') {

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerPageTSConfigFile (
   'dfgviewer',
   'Configuration/TsConfig/Page.ts',
   'EXT:dfgviewer: Page TS');
}
