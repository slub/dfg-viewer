<?php
namespace Slub\Dfgviewer\ViewHelpers\Format;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2019 Alexander Bigga <alexander.bigga@slub-dresden.de>
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

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Returns a substring up to last occurence of needle.
 *
 */
class SubStringLastViewHelper extends AbstractViewHelper
{

    /**
     * initialize arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('string', 'string', 'string', true);
        $this->registerArgument('needle', 'string', 'needle', true);
    }

    /**
     * Returns a substring up to last occurence of needle.
     *
     * @return string
     */
    public function render()
    {
        return substr($this->arguments['string'], 0, strrpos($this->arguments['string'], $this->arguments['needle']));
    }
}
