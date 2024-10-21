<?php
defined('TYPO3') or die('Access denied.');

// Register static typoscript.
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'dfgviewer',
    'Configuration/TypoScript',
    'DFG-Viewer: Main TypoScript'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'dfgviewer',
    'Configuration/TypoScript/Plugins/News/changelog.typoscript',
    'DFG-Viewer: News: Changelog Configuration'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'dfgviewer',
    'Configuration/TypoScript/Plugins/News/faq.typoscript',
    'DFG-Viewer: News: FAQ Configuration'
);
