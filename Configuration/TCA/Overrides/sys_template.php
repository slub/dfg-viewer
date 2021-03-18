<?php
defined('TYPO3_MODE') or die();

// Register static typoscript.
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'dfgviewer',
    'Configuration/TypoScript',
    'DFG Viewer'
);
