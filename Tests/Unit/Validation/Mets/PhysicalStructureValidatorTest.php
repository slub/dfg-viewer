<?php

namespace Slub\Dfgviewer\Tests\Unit\Validation;

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

use Kitodo\Dlf\Validation\AbstractDlfValidator;
use Slub\Dfgviewer\Validation\Mets\PhysicalStructureValidator;

class PhysicalStructureValidatorTest extends ApplicationProfileValidatorTest
{

    /**
     * Test validation against the rules of chapter "2.2.1 Physical structure - mets:structMap"
     *
     * @return void
     */
    public function testMultiplePhysicalDivisions(): void
    {
        $node = $this->doc->createElementNS(self::NAMESPACE_METS, 'mets:structMap');
        $node->setAttribute('TYPE', 'PHYSICAL');
        $this->addChildNode('/mets:mets', $node);
        $this->validateAndAssertEquals('Every METS file has to have no or one physical structural element.');
    }

    /**
     * Test validation against the rules of chapter "2.2.2.1 Structural element - mets:div"
     *
     * @return void
     */
    public function testStructuralElements(): void
    {
        $this->removeNodes('//mets:structMap[@TYPE="PHYSICAL"]/mets:div');
        $this->validateAndAssertEquals('Every physical structure has to consist of one mets:div with "TYPE" attribute and value "physSequence" for the sequence.', true);

        $this->removeAttribute('//mets:structMap[@TYPE="PHYSICAL"]/mets:div', 'TYPE');
        $this->validateAndAssertEquals('Every physical structure has to consist of one mets:div with "TYPE" attribute and value "physSequence" for the sequence.', true);

        $this->removeNodes('//mets:structMap[@TYPE="PHYSICAL"]/mets:div/mets:div');
        $this->validateAndAssertEquals('Every physical structure has to consist of one mets:div for the sequence and at least of one subordinate mets:div.', true);

        $this->removeAttribute('//mets:structMap[@TYPE="PHYSICAL"]/mets:div/mets:div', 'ID');
        $this->validateAndAssertEquals('Mandatory "ID" attribute of mets:div in the physical structure is missing.', true);

        $node = $this->doc->createElementNS(self::NAMESPACE_METS, 'mets:div');
        $node->setAttribute('ID', 'PHYS_0001');
        $this->addChildNode('//mets:structMap[@TYPE="PHYSICAL"]/mets:div', $node);
        $this->validateAndAssertEquals('Physical structure "ID" "PHYS_0001" already exists in document.', true);

        $this->removeAttribute('//mets:structMap[@TYPE="PHYSICAL"]/mets:div/mets:div', 'TYPE');
        $this->validateAndAssertEquals('Mandatory "TYPE" attribute of subordinate mets:div in physical structure is missing.', true);

        $this->setAttributeValue('//mets:structMap[@TYPE="PHYSICAL"]/mets:div/mets:div', 'TYPE', 'Test');
        $this->validateAndAssertEquals('Value "Test" of "TYPE" attribute of mets:div in physical structure is not permissible.');
    }

    protected function createValidator(): AbstractDlfValidator
    {
        return new PhysicalStructureValidator();
    }
}
