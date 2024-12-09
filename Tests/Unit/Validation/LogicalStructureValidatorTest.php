<?php

namespace Slub\Dfgviewer\Tests\Unit\Validation;

use Kitodo\Dlf\Validation\AbstractDlfValidator;
use Slub\Dfgviewer\Validation\LogicalStructureValidator;

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
        $result = $this->validate();
        self::assertEquals('Every METS file has to have at least one logical structural element.', $result->getFirstError()->getMessage());
    }

    /**
     * Test validation against the rules of chapter "2.1.2.1 Structural element - mets:div"
     * @return void
     */
    public function testStructuralElements(): void
    {
        $this->removeNodes('//mets:structMap[@TYPE="LOGICAL"]/mets:div');
        $result = $this->validate();
        self::assertEquals('Every logical structure has to consist of at least one mets:div.', $result->getFirstError()->getMessage());

        $this->resetDoc();

        $this->removeAttribute('//mets:structMap[@TYPE="LOGICAL"]/mets:div', 'ID');
        $result = $this->validate();
        self::assertEquals($result->getFirstError()->getMessage(), 'Mandatory "ID" attribute of mets:div in the logical structure is missing.');

        $this->resetDoc();

        $this->removeAttribute('//mets:structMap[@TYPE="LOGICAL"]/mets:div', 'TYPE');
        $result = $this->validate();
        self::assertEquals($result->getFirstError()->getMessage(), 'Mandatory "TYPE" attribute of mets:div in the logical structure is missing.');

        $this->resetDoc();

        $this->setAttributeValue('//mets:structMap[@TYPE="LOGICAL"]/mets:div', 'TYPE', 'Test');
        $result = $this->validate();
        self::assertEquals($result->getFirstError()->getMessage(), 'Value "Test" of "TYPE" attribute of mets:div in the logical structure is not permissible.');
    }



    /**
     * Test validation against the rules of chapter "2.1.2.2 Reference to external METS-files - mets:div / mets:mptr"
     * @return void
     */
    public function testExternalReference(): void
    {
        $this->addChildNodeNS('//mets:structMap[@TYPE="LOGICAL"]/mets:div', self::NAMESPACE_METS, 'mets:mptr');
        $this->addChildNodeNS('//mets:structMap[@TYPE="LOGICAL"]/mets:div', self::NAMESPACE_METS, 'mets:mptr');
        $result = $this->validate();
        self::assertEquals('Every mets:div in the logical structure may only contain one mets:mptr.', $result->getFirstError()->getMessage());

        $this->resetDoc();

        $this->addChildNodeNS('//mets:structMap[@TYPE="LOGICAL"]/mets:div', self::NAMESPACE_METS, 'mets:mptr');
        $result = $this->validate();
        self::assertEquals('Mandatory "LOCTYPE" attribute of mets:mptr in the logical structure is missing.', $result->getFirstError()->getMessage());

        $this->setAttributeValue('//mets:structMap[@TYPE="LOGICAL"]/mets:div/mets:mptr', 'LOCTYPE', 'Test');
        $result = $this->validate();
        self::assertEquals($result->getFirstError()->getMessage(), 'Value "Test" of "LOCTYPE" attribute of mets:mptr in the logical structure is not permissible.');

        $this->setAttributeValue('//mets:structMap[@TYPE="LOGICAL"]/mets:div/mets:mptr', 'LOCTYPE', 'URL');
        $result = $this->validate();
        self::assertEquals($result->getFirstError()->getMessage(), 'Mandatory "xlink:href" attribute of mets:mptr in the logical structure is missing.');

        $this->setAttributeValue('//mets:structMap[@TYPE="LOGICAL"]/mets:div/mets:mptr', 'xlink:href', 'Test');
        $result = $this->validate();
        self::assertEquals($result->getFirstError()->getMessage(), 'URL of attribute value "xlink:href" of mets:mptr in the logical structure is not valid.');

        $this->setAttributeValue('//mets:structMap[@TYPE="LOGICAL"]/mets:div/mets:mptr', 'xlink:href', 'http://example.com/periodical.xml');
        $result = $this->validate();
        self::assertFalse($result->hasErrors());
    }

    protected function createValidator(): AbstractDlfValidator
    {
        return new LogicalStructureValidator();
    }
}
