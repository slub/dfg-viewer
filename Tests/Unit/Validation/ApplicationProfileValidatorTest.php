<?php

namespace Slub\Dfgviewer\Tests\Unit\Validation;

use DOMDocument;
use DOMXPath;
use Kitodo\Dlf\Validation\AbstractDlfValidator;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

abstract class ApplicationProfileValidatorTest extends UnitTestCase
{
    protected $validator;

    protected $doc;

    private $originalDoc;

    abstract protected function createValidator(): AbstractDlfValidator;

    public function setUp(): void
    {
        parent::setUp();
        $this->resetSingletonInstances = true;
        $this->doc = $this->getDOMDocument();
        $this->originalDoc = $this->doc;
        $this->validator = $this->createValidator();
    }

    /**
     * Validates the document using the validator.
     *
     * @return void
     */
    public function testDocument()
    {
        $result = $this->validator->validate($this->doc);
        self::assertFalse($result->hasErrors());
    }

    protected function resetDoc(): void
    {
        $this->doc = $this->getDOMDocument();
    }

    /**
     * Remove notes found by node expression in document.
     *
     * @param DOMDocument $doc
     * @param string $expression
     * @param string $namespace
     * @param string $name
     * @return void
     * @throws \DOMException
     */
    protected function addChildNode(DOMDocument $doc, string $expression, string $namespace, string $name): void
    {
        $xpath = new DOMXPath($doc);
        $newNode = $doc->createElementNS($namespace, $name);
        foreach ($xpath->evaluate($expression) as $node) {
            $node->appendChild($newNode);
        }
    }

    /**
     * Remove notes found by node expression in document.
     *
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

    /**
     * Set value of attribute found by node expression in document.
     *
     * @param DOMDocument $doc
     * @param string $expression
     * @param string $attribute
     * @param string $value
     * @return void
     */
    protected function setAttributeValue(DOMDocument $doc, string $expression, string $attribute, string $value): void
    {
        $xpath = new DOMXPath($doc);
        foreach ($xpath->evaluate($expression) as $node) {
            $node->setAttribute($attribute, $value);
        }
    }

    /**
     * Remove attribute found by node expression in document.
     *
     * @param DOMDocument $doc
     * @param string $expression
     * @param string $attribute
     * @return void
     */
    protected function removeAttribute(DOMDocument $doc, string $expression, string $attribute): void
    {
        $xpath = new DOMXPath($doc);
        foreach ($xpath->evaluate($expression) as $node) {
            $node->removeAttribute($attribute);
        }
    }

    private function getDOMDocument(): DOMDocument
    {
        $doc = new DOMDocument();
        $doc->load(__DIR__ . '/../../Fixtures/mets.xml');
        self::assertNotFalse($doc);
        return $doc;
    }
}
