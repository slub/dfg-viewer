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

use DOMDocument;
use DOMElement;
use DOMXPath;
use Kitodo\Dlf\Validation\AbstractDlfValidator;
use TYPO3\CMS\Extbase\Error\Result;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

abstract class ApplicationProfileValidatorTest extends UnitTestCase
{
    const NAMESPACE_METS = 'http://www.loc.gov/METS/';

    protected $validator;

    protected $doc;

    abstract protected function createValidator(): AbstractDlfValidator;

    public function setUp(): void
    {
        parent::setUp();
        $this->resetSingletonInstances = true;
        $this->doc = $this->getDOMDocument();
        $this->validator = $this->createValidator();
    }

    /**
     * Validates the document using the validator.
     *
     * @return void
     */
    public function testDocument()
    {
        $result = $this->validate();
        if ($result->hasErrors()) {
            self::assertEquals('', $result->getFirstError()->getMessage());
        }
        self::assertFalse($result->hasErrors());
    }

    /**
     * Validates using validator and DOMDocument
     *
     * @return mixed|Result
     */
    public function validate(): Result
    {
        return $this->validator->validate($this->doc);
    }

    /**
     * Validates using validator and DOMDocument and assert result error message for equality.
     *
     * Validates using a validator and DOMDocument, then asserts that the resulting error message matches the expected value.
     *
     * @param $message
     * @return void
     */
    public function validateAndAssertEquals(string $message): void
    {
        $result = $this->validator->validate($this->doc);
        self::assertEquals($message, $result->getFirstError()->getMessage());
    }

    protected function resetDocument(): void
    {
        $this->doc = $this->getDOMDocument();
    }

    /**
     * Add child node with name and namespace to DOMDocument.
     *
     * @param string $expression
     * @param string $namespace
     * @param string $name
     * @return void
     * @throws \DOMException
     */
    protected function addChildNodeNS(string $expression, string $namespace, string $name): void
    {
        $this->addChildNode($expression, $this->doc->createElementNS($namespace, $name));
    }

    /**
     * Add node as child node to DOMDocument.
     *
     * @param string $expression
     * @param DOMElement $newNode
     * @return void
     */
    protected function addChildNode(string $expression, DOMElement $newNode): void
    {
        $xpath = new DOMXPath($this->doc);
        foreach ($xpath->evaluate($expression) as $node) {
            $node->appendChild($newNode);
        }
    }

    /**
     * Remove notes found by node expression in DOMDocument.
     *
     * @param string $expression
     * @return void
     */
    protected function removeNodes(string $expression): void
    {
        $xpath = new DOMXPath($this->doc);
        foreach ($xpath->query($expression) as $node) {
            $node->parentNode->removeChild($node);
        }
    }

    /**
     * Set value of attribute found by node expression in DOMDocument.
     *
     * @param string $expression
     * @param string $attribute
     * @param string $value
     * @return void
     */
    protected function setAttributeValue(string $expression, string $attribute, string $value): void
    {
        $xpath = new DOMXPath($this->doc);
        foreach ($xpath->evaluate($expression) as $node) {
            $node->setAttribute($attribute, $value);
        }
    }

    /**
     * Remove attribute found by node expression in DOMDocument.
     *
     * @param string $expression
     * @param string $attribute
     * @return void
     */
    protected function removeAttribute(string $expression, string $attribute): void
    {
        $xpath = new DOMXPath($this->doc);
        foreach ($xpath->evaluate($expression) as $node) {
            $node->removeAttribute($attribute);
        }
    }

    protected function getDOMDocument(): DOMDocument
    {
        $doc = new DOMDocument();
        $doc->load(__DIR__ . '/../../Fixtures/mets.xml');
        self::assertNotFalse($doc);
        return $doc;
    }

    protected function assertErrorHasAny(string $expression, string $context = ''): void
    {
        $message = 'There must be at least one element that matches the XPath expression "' . $expression . '"';
        if ($context != '') {
            $message .= ' under "' . $context . '"';
        }
        $this->validateAndAssertEquals($message);
    }

    protected function assertErrorHasOne(string $expression, string $context = ''): void
    {
        $message = 'There must be an element that matches the XPath expression "' . $expression . '"';
        if ($context != '') {
            $message .= ' under "' . $context . '"';
        }
        $this->validateAndAssertEquals($message);
    }

    protected function assertErrorHasNoneOrOne(string $expression, string $context = ''): void
    {
        $message = 'There must be no more than one element that matches the XPath expression "' . $expression . '"';
        if ($context != '') {
            $message .= ' under "' . $context . '"';
        }
        $this->validateAndAssertEquals($message);
    }

    protected function assertErrorHasAttribute(string $expression, string $name): void
    {
        $this->validateAndAssertEquals('Mandatory "' . $name . '" attribute of "' . $expression . '" is missing.');
    }

    protected function assertErrorHasAttributeWithValue(string $expression, string $name, string $value): void
    {
        $this->validateAndAssertEquals('Value "' . $value . '" in the "' . $name . '" attribute of "' . $expression . '" is not permissible.');
    }

    protected function assertErrorHasAttributeWithUrl(string $expression, string $name, string $value): void
    {
        $this->validateAndAssertEquals('URL "' . $value . '" in the "' . $name . '" attribute of "' . $expression . '" is not valid.');
    }

    protected function assertErrorHasRefToOne(string $expression, string $name, string $value, string $targetContextExpression)
    {
        $this->validateAndAssertEquals('Value "' . $value . '" in the "' . $name . '" attribute of "' . $expression . '" must reference one element under XPath expression "' . $targetContextExpression);
    }

    protected function assertErrorHasUniqueId(string $expression, string $value): void
    {
        $this->validateAndAssertEquals('"ID" attribute "' . $value . '" of "' . $expression . '" already exists.');
    }

}
