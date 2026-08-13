<?php
namespace Slub\Dfgviewer\ViewHelpers;

/**
 * Copyright notice
 *
 * (c) Saxon State and University Library Dresden <typo3@slub-dresden.de>
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
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
     * Initialize arguments.
     */
    public function initializeArguments()
    {
        $this->registerArgument('title', 'string', 'New page title', true);
    }

    /**
     * Set the page title in the current frontend context.
     */
    public function render()
    {
        if ($this->arguments['title'] !== null) {
            $GLOBALS['TSFE']->page['title'] = $this->arguments['title'];
        }
    }
}
