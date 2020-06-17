<?php
namespace Slub\Dfgviewer\ViewHelpers;

/***************************************************************
 * TODO: remove this viewhelper in production version. DEBUG ONLY
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
class DieViewHelper extends AbstractViewHelper
{

    use CompileWithRenderStatic;

    /**
     * initialize arguments
     */
    public function initializeArguments()
    {
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
        die();
    }
}
