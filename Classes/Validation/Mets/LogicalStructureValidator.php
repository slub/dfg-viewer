<?php

namespace Slub\Dfgviewer\Validation\Mets;

use Slub\Dfgviewer\Validation\ApplicationProfileBaseValidator;

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
 * The validator validates against the rules outlined in chapter 2.1 of the METS application profile 2.3.1.
 *
 * @package TYPO3
 * @subpackage dfg-viewer
 *
 * @access public
 */
class LogicalStructureValidator extends ApplicationProfileBaseValidator
{

    const XPATH_LOGICAL_STRUCTURES = '//mets:mets/mets:structMap[@TYPE="LOGICAL"]';

    const XPATH_STRUCTURAL_ELEMENTS = self::XPATH_LOGICAL_STRUCTURES . '/mets:div';

    const XPATH_EXTERNAL_REFERENCES = self::XPATH_STRUCTURAL_ELEMENTS . '/mets:mptr';

    protected function isValidDocument(): void
    {
        // Validates against the rules of chapter "2.1.1 Logical structure - mets:structMap"
        $this->query(self::XPATH_LOGICAL_STRUCTURES)->validateHasAny();

        $this->validateStructuralElements();
        $this->validateExternalReferences();
        $this->validatePeriodicPublishingSequences();
    }

    /**
     * Validates the structural elements.
     *
     * Validates against the rules of chapter "2.1.2.1 Structural element - mets:div"
     *
     * @return void
     */
    protected function validateStructuralElements(): void
    {
        $this->query(self::XPATH_STRUCTURAL_ELEMENTS)
            ->validateHasAny()
            ->iterate(array($this, "validateStructuralElement"));
    }

    protected function validateStructuralElement(\DOMNode $structureElement): void
    {
        $this->validateHasUniqueId($structureElement);
        $this->validateHasAttributeWithValue($structureElement, "TYPE", self::STRUCTURE_DATASET);
    }

    /**
     * Validates the external references.
     *
     * Validates against the rules of chapter "2.1.2.2 Reference to external METS-files - mets:div / mets:mptr"
     *
     * @return void
     */
    protected function validateExternalReferences(): void
    {
        $this->query(self::XPATH_EXTERNAL_REFERENCES)
            ->validateHasNoneOrOne()
            ->iterate(array($this, "validateExternalReference"));
    }

    protected function validateExternalReference(\DOMNode $externalReference): void
    {
        $this->validateHasAttributeWithValue($externalReference, "LOCTYPE", array("URL", "PURL"));
        $this->validateHasAttributeWithUrl($externalReference, "xlink:href");
    }

    /**
     * Validates the periodic publishing sequences.
     *
     * Validates against the rules of chapter "2.1.3 Periodic publishing sequences"
     *
     * @return void
     */
    protected function validatePeriodicPublishingSequences(): void
    {
        // TODO
    }
}
