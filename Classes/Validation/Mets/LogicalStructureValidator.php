<?php

namespace Slub\Dfgviewer\Validation\Mets;

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
use Slub\Dfgviewer\Validation\AbstractDomDocumentValidator;

/**
 * The validator validates against the rules outlined in chapter 2.1 of the METS application profile 2.4.
 *
 * @package TYPO3
 * @subpackage dfg-viewer
 *
 * @access public
 */
class LogicalStructureValidator extends AbstractDomDocumentValidator
{
    public function isValidDocument(): void
    {
        // Validates against the rules of chapter "2.1.1 Logical structure - mets:structMap"
        $this->createNodeListValidator(VH::XPATH_LOGICAL_STRUCTURES)
            ->validateHasAny();

        $this->validateStructuralElements();
        $this->validateExternalReferences();
        $this->validatePeriodical();
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
        $structuralElements = $this->createNodeListValidator(VH::XPATH_LOGICAL_STRUCTURAL_ELEMENTS)
            ->validateHasAny()
            ->getNodeList();
        foreach ($structuralElements as $structuralElement) {
            $this->validateStructuralElement($structuralElement);
        }
    }

    protected function validateStructuralElement(\DOMNode $structureElement): void
    {
        $this->createNodeValidator($structureElement)
            ->validateHasUniqueId()
            ->validateHasAttributeValue("TYPE", VH::STRUCTURE_DATASET);
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
        $externalReferences = $this->createNodeListValidator(VH::XPATH_LOGICAL_EXTERNAL_REFERENCES)
            ->validateHasNoneOrOne()
            ->getNodeList();
        foreach ($externalReferences as $externalReference) {
            $this->validateExternalReference($externalReference);
        }
    }

    protected function validateExternalReference(\DOMNode $externalReference): void
    {
        $this->createNodeValidator($externalReference)
            ->validateHasAttributeValue("LOCTYPE", ["URL", "PURL"])
            ->validateHasUrlAttribute("xlink:href");
    }

    /**
     * Validates the periodic publishing sequences.
     *
     * Validates against the rules of chapter "2.1.3 Periodic publishing sequences"
     *
     * @return void
     */
    protected function validatePeriodical(): void
    {
       $this->createNodeListValidator(VH::XPATH_LOGICAL_STRUCTURAL_ELEMENTS . '/mets:div[@TYPE="periodical"]');
    }
}
