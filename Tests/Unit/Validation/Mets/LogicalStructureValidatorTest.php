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
use Slub\Dfgviewer\Validation\Mets\LogicalStructureValidator;

class LogicalStructureValidatorTest extends ApplicationProfileValidatorTest
{

    /**
     * Test validation against the rules of chapter "2.1.1 Logical structure - mets:structMap"
     *
     * @return void
     */
    public function testNotExistingLogicalStructureElement(): void
    {
        $this->removeNodes(LogicalStructureValidator::XPATH_LOGICAL_STRUCTURES);
        $this->assertErrorHasAny(LogicalStructureValidator::XPATH_LOGICAL_STRUCTURES);
    }

    /**
     * Test validation against the rules of chapter "2.1.2.1 Structural element - mets:div"
     * @return void
     */
    public function testStructuralElements(): void
    {
        $this->removeNodes(LogicalStructureValidator::XPATH_STRUCTURAL_ELEMENTS);
        $this->assertErrorHasAny(LogicalStructureValidator::XPATH_STRUCTURAL_ELEMENTS);
        $this->resetDocument();

        $this->removeAttribute(LogicalStructureValidator::XPATH_STRUCTURAL_ELEMENTS, 'ID');
        $this->assertErrorHasAttribute('/mets:mets/mets:structMap[1]/mets:div', 'ID');
        $this->resetDocument();

        $node = $this->doc->createElementNS(self::NAMESPACE_METS, 'mets:div');
        $node->setAttribute('ID', 'LOG_0001');
        $this->addChildNode(LogicalStructureValidator::XPATH_STRUCTURAL_ELEMENTS, $node);
        $this->validateErrorHasUniqueId('/mets:mets/mets:structMap[1]/mets:div', 'LOG_0001');
        $this->resetDocument();

        $this->removeAttribute(LogicalStructureValidator::XPATH_STRUCTURAL_ELEMENTS, 'TYPE');
        $this->assertErrorHasAttribute('/mets:mets/mets:structMap[1]/mets:div', 'TYPE');

        $this->setAttributeValue(LogicalStructureValidator::XPATH_STRUCTURAL_ELEMENTS, 'TYPE', 'Test');
        $this->assertErrorHasAttributeWithValue('/mets:mets/mets:structMap[1]/mets:div', 'TYPE', 'Test');
    }

    /**
     * Test validation against the rules of chapter "2.1.2.2 Reference to external METS-files - mets:div / mets:mptr"
     * @return void
     * @throws \DOMException
     */
    public function testExternalReference(): void
    {
        $this->addChildNodeNS(LogicalStructureValidator::XPATH_STRUCTURAL_ELEMENTS, self::NAMESPACE_METS, 'mets:mptr');
        $this->addChildNodeNS(LogicalStructureValidator::XPATH_STRUCTURAL_ELEMENTS, self::NAMESPACE_METS, 'mets:mptr');
        $this->assertErrorHasNoneOrOne(LogicalStructureValidator::XPATH_EXTERNAL_REFERENCES);
        $this->resetDocument();

        $this->addChildNodeNS(LogicalStructureValidator::XPATH_STRUCTURAL_ELEMENTS, self::NAMESPACE_METS, 'mets:mptr');
        $this->assertErrorHasAttribute('/mets:mets/mets:structMap[1]/mets:div/mets:mptr', 'LOCTYPE');

        $this->setAttributeValue(LogicalStructureValidator::XPATH_EXTERNAL_REFERENCES, 'LOCTYPE', 'Test');
        $this->assertErrorHasAttributeWithValue('/mets:mets/mets:structMap[1]/mets:div/mets:mptr', 'LOCTYPE', 'Test');

        $this->setAttributeValue(LogicalStructureValidator::XPATH_EXTERNAL_REFERENCES, 'LOCTYPE', 'URL');
        $this->assertErrorHasAttribute('/mets:mets/mets:structMap[1]/mets:div/mets:mptr', 'xlink:href');

        $this->setAttributeValue(LogicalStructureValidator::XPATH_EXTERNAL_REFERENCES, 'xlink:href', 'Test');
        $this->assertErrorHasAttributeWithUrl('/mets:mets/mets:structMap[1]/mets:div/mets:mptr', 'xlink:href', 'Test');

        $this->setAttributeValue(LogicalStructureValidator::XPATH_EXTERNAL_REFERENCES, 'xlink:href', 'http://example.com/periodical.xml');
        $result = $this->validate();
        self::assertFalse($result->hasErrors());
    }

    protected function createValidator(): AbstractDlfValidator
    {
        return new LogicalStructureValidator();
    }
}
