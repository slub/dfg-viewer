<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerPageTSConfigFile (
'dfgviewer',
'Configuration/TsConfig/Page.tsconfig',
'EXT:dfgviewer: Page TS');
