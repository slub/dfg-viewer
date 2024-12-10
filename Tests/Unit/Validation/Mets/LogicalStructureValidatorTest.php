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
        $this->removeNodes( '//mets:structMap[@TYPE="LOGICAL"]');
        $this->validateAndAssertEquals('Every METS file has to have at least one logical structural element.');
    }

    /**
     * Test validation against the rules of chapter "2.1.2.1 Structural element - mets:div"
     * @return void
     */
    public function testStructuralElements(): void
    {
        $this->removeNodes('//mets:structMap[@TYPE="LOGICAL"]/mets:div');
        $this->validateAndAssertEquals('Every logical structure has to consist of at least one mets:div.', true);

        $this->removeAttribute('//mets:structMap[@TYPE="LOGICAL"]/mets:div', 'ID');
        $this->validateAndAssertEquals('Mandatory "ID" attribute of mets:div in the logical structure is missing.', true);

        $node = $this->doc->createElementNS(self::NAMESPACE_METS, 'mets:div');
        $node->setAttribute('ID', 'LOG_0001');
        $this->addChildNode('//mets:structMap[@TYPE="LOGICAL"]/mets:div', $node);
        $this->validateAndAssertEquals('Logical structure "ID" "LOG_0001" already exists in document.', true);

        $this->removeAttribute('//mets:structMap[@TYPE="LOGICAL"]/mets:div', 'TYPE');
        $this->validateAndAssertEquals('Mandatory "TYPE" attribute of mets:div in the logical structure is missing.', true);

        $this->setAttributeValue('//mets:structMap[@TYPE="LOGICAL"]/mets:div', 'TYPE', 'Test');
        $this->validateAndAssertEquals('Value "Test" of "TYPE" attribute of mets:div in the logical structure is not permissible.', true);
    }



    /**
     * Test validation against the rules of chapter "2.1.2.2 Reference to external METS-files - mets:div / mets:mptr"
     * @return void
     */
    public function testExternalReference(): void
    {
        $this->addChildNodeNS('//mets:structMap[@TYPE="LOGICAL"]/mets:div', self::NAMESPACE_METS, 'mets:mptr');
        $this->addChildNodeNS('//mets:structMap[@TYPE="LOGICAL"]/mets:div', self::NAMESPACE_METS, 'mets:mptr');
        $this->validateAndAssertEquals('Every mets:div in the logical structure may only contain one mets:mptr.', true);

        $this->addChildNodeNS('//mets:structMap[@TYPE="LOGICAL"]/mets:div', self::NAMESPACE_METS, 'mets:mptr');
        $this->validateAndAssertEquals('Mandatory "LOCTYPE" attribute of mets:mptr in the logical structure is missing.');

        $this->setAttributeValue('//mets:structMap[@TYPE="LOGICAL"]/mets:div/mets:mptr', 'LOCTYPE', 'Test');
        $this->validateAndAssertEquals('Value "Test" of "LOCTYPE" attribute of mets:mptr in the logical structure is not permissible.');

        $this->setAttributeValue('//mets:structMap[@TYPE="LOGICAL"]/mets:div/mets:mptr', 'LOCTYPE', 'URL');
        $this->validateAndAssertEquals('Mandatory "xlink:href" attribute of mets:mptr in the logical structure is missing.');

        $this->setAttributeValue('//mets:structMap[@TYPE="LOGICAL"]/mets:div/mets:mptr', 'xlink:href', 'Test');
        $this->validateAndAssertEquals('URL of attribute value "xlink:href" of mets:mptr in the logical structure is not valid.');

        $this->setAttributeValue('//mets:structMap[@TYPE="LOGICAL"]/mets:div/mets:mptr', 'xlink:href', 'http://example.com/periodical.xml');
        $result = $this->validate();
        self::assertFalse($result->hasErrors());
    }

    protected function createValidator(): AbstractDlfValidator
    {
        return new LogicalStructureValidator();
    }
}
