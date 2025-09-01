<?php

namespace Slub\Dfgviewer\Validation\Mods;

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

use Slub\Dfgviewer\Common\ValidationHelper as VH;

/**
 * The validator validates against the rules outlined in chapter 2.6 of the MODS application profile 2.4.
 *
 * @package TYPO3
 * @subpackage dfg-viewer
 *
 * @access public
 */
class PhysicalDescriptionValidator extends AbstractModsValidator
{
    public function isValidDocument(): void
    {
        $this->createNodeListValidator(VH::XPATH_MODS_PHYSICAL_DESCRIPTION)
            ->validateHasNoneOrOne();
        // Validation of chapters "2.6.2.1 Form – mods:form" and "2.6.2.2 Umfang – mods:extent" already covered by MODS XML schema validation
    }
}
