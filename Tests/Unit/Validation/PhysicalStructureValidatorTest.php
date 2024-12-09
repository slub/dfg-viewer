<?php

namespace Slub\Dfgviewer\Tests\Unit\Validation;

use Kitodo\Dlf\Validation\AbstractDlfValidator;
use Slub\Dfgviewer\Validation\LogicalStructureValidator;
use Slub\Dfgviewer\Validation\PhysicalStructureValidator;

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
        $result = $this->validate();
        self::assertEquals('Every METS file has to have no or one physical structural element.', $result->getFirstError()->getMessage());
    }

    /**
     * Test validation against the rules of chapter "2.2.2.1 Structural element - mets:div"
     *
     * @return void
     */
    public function testStructuralElements(): void
    {
        $this->removeNodes('//mets:structMap[@TYPE="PHYSICAL"]/mets:div');
        $result = $this->validate();
        self::assertEquals('Every physical structure has to consist of one mets:div with "TYPE" attribute and value "physSequence" for the sequence.', $result->getFirstError()->getMessage());

        $this->resetDoc();

        $this->removeAttribute('//mets:structMap[@TYPE="PHYSICAL"]/mets:div', 'TYPE');
        $result = $this->validate();
        self::assertEquals('Every physical structure has to consist of one mets:div with "TYPE" attribute and value "physSequence" for the sequence.', $result->getFirstError()->getMessage());

        $this->resetDoc();

        $this->removeNodes('//mets:structMap[@TYPE="PHYSICAL"]/mets:div/mets:div');
        $result = $this->validate();
        self::assertEquals('Every physical structure has to consist of one mets:div for the sequence and at least of one subordinate mets:div.', $result->getFirstError()->getMessage());

        $this->resetDoc();

        $this->removeAttribute('//mets:structMap[@TYPE="PHYSICAL"]/mets:div/mets:div', 'TYPE');
        $result = $this->validate();
        self::assertEquals('Mandatory "TYPE" attribute of subordinate mets:div in physical structure is missing.', $result->getFirstError()->getMessage());

        $this->resetDoc();

        $this->setAttributeValue('//mets:structMap[@TYPE="PHYSICAL"]/mets:div/mets:div', 'TYPE', 'Test');
        $result = $this->validate();
        self::assertEquals('Value "Test" of "TYPE" attribute of mets:div in physical structure is not permissible.', $result->getFirstError()->getMessage());
    }

    protected function createValidator(): AbstractDlfValidator
    {
        return new PhysicalStructureValidator();
    }
}
