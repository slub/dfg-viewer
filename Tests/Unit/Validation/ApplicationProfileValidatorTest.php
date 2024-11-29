<?php

namespace Slub\Dfgviewer\Tests\Unit\Validation;

use DOMDocument;
use DOMXPath;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

abstract class ApplicationProfileValidatorTest extends UnitTestCase
{
    protected $validator;

    public function setUp(): void
    {
        parent::setUp();
        $this->resetSingletonInstances = true;
    }

    protected function initValidator($validator): void
    {
        $this->validator = $validator;
    }

    protected function getDOMDocument(): DOMDocument
    {
        $doc = new DOMDocument();
        $doc->load(__DIR__ . '/../../Fixtures/mets.xml');
        self::assertNotFalse($doc);
        return $doc;
    }

    /**
     * @param DOMDocument $doc
     * @param string $expression
     * @return void
     */
    protected function removeNodes(DOMDocument $doc, string $expression): void
    {
        $xpath = new DOMXPath($doc);
        foreach ($xpath->evaluate($expression) as $node) {
            $node->parentNode->removeChild($node);
        }
    }
}
