<?php
namespace Slub\Dfgviewer\ViewHelpers;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 Alexander Bigga <alexander.bigga@slub-dresden.de>
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

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper to calculate two integers
 *
 * # Example: Basic example
 * <code>
 * <si:piVars var="page">
 *	<span>1</span>
 * </code>
 * <output>
 * Will output the value of tx_dlf[page]
 * </output>
 *
 * @package TYPO3
 */
class CalcViewHelper extends AbstractViewHelper
{

    use CompileWithRenderStatic;

    /**
     * initialize arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('val1', 'string', 'first value', true);
        $this->registerArgument('val2', 'string', 'second value', true);
        $this->registerArgument('operator', 'string', 'operator', false, '+');
    }

    /**
     * Return result of calculation
     * @return float|int
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    )
    {
        switch ($arguments['operator']) {
            case '+': return (int)$arguments['val1'] + (int)$arguments['val2'];
                    break;
            case '-': return (int)$arguments['val1'] - (int)$arguments['val2'];
                    break;
            case '*': return (int)$arguments['val1'] * (int)$arguments['val2'];
                    break;
            case '/': return (int)((int)$arguments['val1'] / (int)$arguments['val2']);
                    break;
        }
        return 0;
    }
}
