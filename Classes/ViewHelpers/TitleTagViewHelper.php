<?php
namespace Slub\Dfgviewer\ViewHelpers;

/**
 * (c) Kitodo. Key to digital objects e.V. <contact@kitodo.org>
 *
 * This file is part of the Kitodo and TYPO3 projects.
 *
 * @license GNU General Public License version 3 or later.
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * ViewHelper to overwrite the page title tag
 *
 * # Example: Basic example
 * <code>
 * <dv:titleTag title="New Page Title">
 *	<title>New Page Title</title>
 * </code>
 * <output>
 * Will output the given string inside title tags.
 * </output>
 *
 * @package TYPO3
 */
class TitleTagViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * Return elements found
     *
     * @param string $title the new page title
     *
     * @return void
     */
    public function render($title)
    {
        $GLOBALS['TSFE']->page['title'] = $title;
        // return first found result
        return;
    }
}
