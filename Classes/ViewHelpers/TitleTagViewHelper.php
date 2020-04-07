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

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

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
class TitleTagViewHelper extends AbstractViewHelper
{

    /**
     * initialize arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('title', 'string', 'the new page title', true);
    }

    /**
     * Return elements found
     *
     * @return void
     */
    public function render()
    {

        $GLOBALS['TSFE']->page['title'] = $this->arguments['title'];
        // return first found result
        return;
    }
}
