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

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

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

    /**
     * Return result of calculation
     *
     * @param string $val1 first value
     * @param string $val2 second value
     * @param string $operator operator
     * @return string
     */
    public function render($val1, $val2, $operator = '+')
    {
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
