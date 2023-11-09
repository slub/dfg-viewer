<?php
namespace Slub\Dfgviewer\ViewHelpers;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 Alexander Bigga <typo3@slub-dresden.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper to calculate two integers
 *
 * # Example: Basic example
 * <code>
 * <si:calc val1="1" val2="2" operator="+" />
 * </code>
 * <output>
 * Will output "3"
 * </output>
 *
 * @package TYPO3
 */
class CalcViewHelper extends AbstractViewHelper
{
    /**
     * Initialize arguments.
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('val1', 'integer', 'first value', true);
        $this->registerArgument('val2', 'integer', 'second value', true);
        $this->registerArgument('operator', 'string', 'operator', false, '+');
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $val1 = $arguments['val1'];
        $val2 = $arguments['val2'];
        $operator = $arguments['operator'];

        switch ($operator) {
          case '+': $result = (int)$val1 + (int)$val2;
                    break;
          case '-': $result = (int)$val1 - (int)$val2;
                    break;
          case '*': $result = (int)$val1 * (int)$val2;
                    break;
          case '/': $result = (int)((int)$val1 / (int)$val2);
                    break;
        }

        return $result;
    }
}
