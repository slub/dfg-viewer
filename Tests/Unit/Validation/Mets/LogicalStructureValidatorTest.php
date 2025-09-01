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
use Slub\Dfgviewer\Validation\Mets\LogicalStructureValidator;

class LogicalStructureValidatorTest extends AbstractDomDocumentValidatorTest
{
    /**
     * Test validation against the rules of chapter "2.1.1 Logical structure - mets:structMap"
     *
     * @return void
     */
    public function testNotExistingLogicalStructureElement(): void
    {
        $this->removeNodes(VH::XPATH_LOGICAL_STRUCTURES);
        $this->hasMessageAny(VH::XPATH_LOGICAL_STRUCTURES);
    }

    /**
     * Test validation against the rules of chapter "2.1.2.1 Structural element - mets:div"
     * @return void
     */
    public function testStructuralElements(): void
    {
        $this->removeNodes(VH::XPATH_LOGICAL_STRUCTURAL_ELEMENTS);
        $this->hasMessageAny(VH::XPATH_LOGICAL_STRUCTURAL_ELEMENTS);
        $this->resetDocument();

        $this->removeAttribute(VH::XPATH_LOGICAL_STRUCTURAL_ELEMENTS, 'ID');
        $this->hasMessageAttribute('/mets:mets/mets:structMap[1]/mets:div', 'ID');
        $this->resetDocument();

        $node = $this->doc->createElementNS(VH::NAMESPACE_METS, 'mets:div');
        $node->setAttribute('ID', 'LOG_0001');
        $this->addChildNode(VH::XPATH_LOGICAL_STRUCTURAL_ELEMENTS, $node);
        $this->hasMessageUniqueId('/mets:mets/mets:structMap[1]/mets:div', 'LOG_0001');
        $this->resetDocument();

        $this->removeAttribute(VH::XPATH_LOGICAL_STRUCTURAL_ELEMENTS, 'TYPE');
        $this->hasMessageAttribute('/mets:mets/mets:structMap[1]/mets:div', 'TYPE');

        $this->setAttributeValue(VH::XPATH_LOGICAL_STRUCTURAL_ELEMENTS, 'TYPE', 'Test');
        $this->hasMessageAttributeWithValue('/mets:mets/mets:structMap[1]/mets:div', 'TYPE', 'Test');
    }

    /**
     * Test validation against the rules of chapter "2.1.2.2 Reference to external METS-files - mets:div / mets:mptr"
     * @return void
     * @throws \DOMException
     */
    public function testExternalReference(): void
    {
        $this->addChildNodeWithNamespace(VH::XPATH_LOGICAL_STRUCTURAL_ELEMENTS, VH::NAMESPACE_METS, 'mets:mptr');
        $this->addChildNodeWithNamespace(VH::XPATH_LOGICAL_STRUCTURAL_ELEMENTS, VH::NAMESPACE_METS, 'mets:mptr');
        $this->hasMessageNoneOrOne(VH::XPATH_LOGICAL_EXTERNAL_REFERENCES);
        $this->resetDocument();

        $this->addChildNodeWithNamespace(VH::XPATH_LOGICAL_STRUCTURAL_ELEMENTS, VH::NAMESPACE_METS, 'mets:mptr');
        $this->hasMessageAttribute('/mets:mets/mets:structMap[1]/mets:div/mets:mptr', 'LOCTYPE');

        $this->setAttributeValue(VH::XPATH_LOGICAL_EXTERNAL_REFERENCES, 'LOCTYPE', 'Test');
        $this->hasMessageAttributeWithValue('/mets:mets/mets:structMap[1]/mets:div/mets:mptr', 'LOCTYPE', 'Test');

        $this->setAttributeValue(VH::XPATH_LOGICAL_EXTERNAL_REFERENCES, 'LOCTYPE', 'URL');
        $this->hasMessageAttribute('/mets:mets/mets:structMap[1]/mets:div/mets:mptr', 'xlink:href');

        $this->setAttributeValue(VH::XPATH_LOGICAL_EXTERNAL_REFERENCES, 'xlink:href', 'Test');
        $this->hasMessageUrlAttribute('/mets:mets/mets:structMap[1]/mets:div/mets:mptr', 'xlink:href', 'Test');

        $this->setAttributeValue(VH::XPATH_LOGICAL_EXTERNAL_REFERENCES, 'xlink:href', 'http://example.com/periodical.xml');
        $this->hasNoMessage();
    }

    protected function createValidator(): AbstractDlfValidator
    {
        return new LogicalStructureValidator();
    }
}
