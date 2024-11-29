<?php

namespace Slub\Dfgviewer\Tests\Unit\Validation;

use Slub\Dfgviewer\Validation\LogicalStructureValidator;

class LogicalStructureValidatorTest extends ApplicationProfileValidatorTest
{

    public function setUp(): void
    {
        parent::setUp();
        $this->initValidator(new LogicalStructureValidator());
    }

    /**
     * @test
     */
    public function testLogicalStructure()
    {
        $doc = $this->getDOMDocument();
        $result = $this->validator->validate($doc);
        self::assertFalse($result->hasErrors());

        $this->removeNodes($doc,  '//mets:structMap[@TYPE="LOGICAL"]');
        $result = $this->validator->validate($doc);
        self::assertTrue($result->hasErrors());
    }

}
