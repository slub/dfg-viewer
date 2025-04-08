<?php

namespace Slub\Dfgviewer\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class LanguageSlugViewHelper extends AbstractViewHelper {

    public function initializeArguments()
    {
        $this->registerArgument('pageId', 'int', 'The page ID', true, 0);
        $this->registerArgument('languageId', 'int', 'The language ID to use', true, 0);
    }
    public function render(): string {
        $site = GeneralUtility::makeInstance(SiteFinder::class)->getSiteByPageId($this->arguments['pageId']);
        $language = $site->getLanguageById( $this->arguments['languageId']);
        return  $language->getBase(); // or (string)$uri for full URL
    }
}
