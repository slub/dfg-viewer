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
use Slub\Dfgviewer\Common\ValidationHelper as VH;
use Slub\Dfgviewer\Validation\Mets\PhysicalStructureValidator;

class PhysicalStructureValidatorTestAbstract extends AbstractDomDocumentValidatorTest
{
    /**
     * Test validation against the rules of chapter "2.2.1 Physical structure - mets:structMap"
     *
     * @return void
     * @throws \DOMException
     */
    public function testMultiplePhysicalDivisions(): void
    {
        $node = $this->doc->createElementNS(VH::NAMESPACE_METS, 'mets:structMap');
        $node->setAttribute('TYPE', 'PHYSICAL');
        $this->addChildNode('/mets:mets', $node);
        $this->assertErrorHasNoneOrOne(VH::XPATH_PHYSICAL_STRUCTURES);
    }

    /**
     * Test validation against the rules of chapter "2.2.2.1 Structural element - mets:div"
     *
     * @return void
     * @throws \DOMException
     */
    public function testStructuralElements(): void
    {
        $this->removeNodes(VH::XPATH_PHYSICAL_STRUCTURAL_ELEMENT_SEQUENCE);
        $this->assertErrorHasOne(VH::XPATH_PHYSICAL_STRUCTURAL_ELEMENT_SEQUENCE);
        $this->resetDocument();

        $this->removeAttribute(VH::XPATH_PHYSICAL_STRUCTURAL_ELEMENT_SEQUENCE, 'TYPE');
        $this->assertErrorHasAttribute('/mets:mets/mets:structMap[2]/mets:div', 'TYPE');

        $this->setAttributeValue(VH::XPATH_PHYSICAL_STRUCTURAL_ELEMENT_SEQUENCE, 'TYPE', 'Test');
        $this->assertErrorHasAttributeWithValue('/mets:mets/mets:structMap[2]/mets:div', 'TYPE', 'Test');
        $this->resetDocument();

        $this->removeNodes(VH::XPATH_PHYSICAL_STRUCTURAL_ELEMENTS);
        $this->assertErrorHasAny(VH::XPATH_PHYSICAL_STRUCTURAL_ELEMENTS);
        $this->resetDocument();

        $this->removeAttribute(VH::XPATH_PHYSICAL_STRUCTURAL_ELEMENTS, 'ID');
        $this->assertErrorHasAttribute('/mets:mets/mets:structMap[2]/mets:div/mets:div', 'ID');
        $this->resetDocument();

        $node = $this->doc->createElementNS(VH::NAMESPACE_METS, 'mets:div');
        $node->setAttribute('ID', 'PHYS_0001');
        $this->addChildNode('//mets:structMap[@TYPE="PHYSICAL"]/mets:div', $node);
        $this->assertErrorHasUniqueId('/mets:mets/mets:structMap[2]/mets:div/mets:div[1]', 'PHYS_0001');
        $this->resetDocument();

        $this->removeAttribute(VH::XPATH_PHYSICAL_STRUCTURAL_ELEMENTS, 'TYPE');
        $this->assertErrorHasAttribute('/mets:mets/mets:structMap[2]/mets:div/mets:div', 'TYPE');

        $this->setAttributeValue(VH::XPATH_PHYSICAL_STRUCTURAL_ELEMENTS, 'TYPE', 'Test');
        $this->assertErrorHasAttributeWithValue('/mets:mets/mets:structMap[2]/mets:div/mets:div', 'TYPE', 'Test');
    }

    protected function createValidator(): AbstractDlfValidator
    {
        return new PhysicalStructureValidator();
    }
}
