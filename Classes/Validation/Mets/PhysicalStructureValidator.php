<?php

namespace Slub\Dfgviewer\Validation\Mets;

use Slub\Dfgviewer\Common\ValidationHelper;
use Slub\Dfgviewer\Validation\DOMDocumentValidator;

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

/**
 * The validator validates against the rules outlined in chapter 2.2 of the METS application profile 2.3.1.
 *
 * @package TYPO3
 * @subpackage dfg-viewer
 *
 * @access public
 */
class PhysicalStructureValidator extends DOMDocumentValidator
{


    public function isValidDocument(): void
    {
        // Validates against the rules of chapter "2.2.1 Physical structure - mets:structMap"
        $this->createNodeListValidator(ValidationHelper::XPATH_PHYSICAL_STRUCTURES)
            ->validateHasNoneOrOne();

        $this->validateStructuralElements();
    }

    /**
     *
     * Validates the structural elements.
     *
     * Validates against the rules of chapter "2.2.2.1 Structural element - mets:div"
     *
     * @return void
     */
    public function validateStructuralElements(): void
    {
        $node = $this->createNodeListValidator(ValidationHelper::XPATH_PHYSICAL_STRUCTURAL_ELEMENT_SEQUENCE)
            ->validateHasOne()
            ->getFirstNode();

        $this->createNodeValidator($node)
            ->validateHasAttributeWithValue('TYPE', array('physSequence'));

        $this->createNodeListValidator(ValidationHelper::XPATH_PHYSICAL_STRUCTURAL_ELEMENTS)
            ->validateHasAny()
            ->iterate(array($this, "validateStructuralElement"));
    }

    public function validateStructuralElement(\DOMNode $structureElement): void
    {
        $this->createNodeValidator($structureElement)
            ->validateHasUniqueId()
            ->validateHasAttributeWithValue("TYPE", array("page", "doublepage", "track"));
    }
}
