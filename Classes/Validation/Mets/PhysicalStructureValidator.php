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
 * The validator validates against the rules outlined in chapter 2.2 of the METS application profile 2.3.1.
 *
 * @package TYPO3
 * @subpackage dfg-viewer
 *
 * @access public
 */
class PhysicalStructureValidator extends ApplicationProfileBaseValidator
{
    protected function isValid($value): void
    {
        $this->setupIsValid($value);

        // Validates against the rules of chapter "2.2.1 Physical structure - mets:structMap"
        if ($this->xpath->query('//mets:structMap[@TYPE="PHYSICAL"]')->length > 1) {
            $this->addError('Every METS file has to have no or one physical structural element.', 1723727164447);
        }
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
    private function validateStructuralElements(): void
    {
        if ($this->xpath->query('//mets:structMap[@TYPE="PHYSICAL"]/mets:div[@TYPE="physSequence"]')->length == 0) {
            $this->addError('Every physical structure has to consist of one mets:div with "TYPE" attribute and value "physSequence" for the sequence.', 1724234607);
        }

        $subordinateStructuralElements = $this->xpath->query('//mets:structMap[@TYPE="PHYSICAL"]/mets:div[@TYPE="physSequence"]/mets:div');
        if ($subordinateStructuralElements->length == 0) {
            $this->addError('Every physical structure has to consist of one mets:div for the sequence and at least of one subordinate mets:div.', 1724234607);
        } else {
            foreach ($subordinateStructuralElements as $subordinateStructuralElement) {
                if (!$subordinateStructuralElement->hasAttribute("ID")) {
                    $this->addError('Mandatory "ID" attribute of mets:div in the physical structure is missing.', 1724234607);
                } else {
                    $id = $subordinateStructuralElement->getAttribute("ID");
                    if ($this->xpath->query('//*[@ID="' . $id . '"]')->length > 1) {
                        $this->addError('Physical structure "ID" "' . $id . '" already exists in document.', 1724234607);
                    }
                }
                if (!$subordinateStructuralElement->hasAttribute("TYPE")) {
                    $this->addError('Mandatory "TYPE" attribute of subordinate mets:div in physical structure is missing.', 1724234607);
                } else {
                    if (!in_array($subordinateStructuralElement->getAttribute("TYPE"), array("page", "doublepage", "track"))) {
                        $this->addError('Value "' . $subordinateStructuralElement->getAttribute("TYPE") . '" of "TYPE" attribute of mets:div in physical structure is not permissible.', 1724234607);
                    }
                }
            }
        }
    }
}
