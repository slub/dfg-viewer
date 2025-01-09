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

abstract class AbstractDomDocumentValidatorTest extends UnitTestCase
{
    /**
     * @var AbstractDlfValidator
     */
    protected $validator;

    /**
     * @var DOMDocument
     */
    protected $doc;

    abstract protected function createValidator(): AbstractDlfValidator;

    public function setUp(): void
    {
        parent::setUp();
        $this->resetSingletonInstances = true;
        $this->doc = $this->getDomDocument();
        $this->validator = $this->createValidator();
    }

    /**
     * Validates the document using the created validator.
     *
     * @return void
     */
    public function testDocument()
    {
        $this->assertNoError();
    }

    /**
     * Validates using validator and DOMDocument
     *
     * @return Result
     */
    protected function validate(): Result
    {
        return $this->validator->validate($this->doc);
    }

    /**
     * Validates using validator and DOMDocument and assert result error message for equality.
     *
     * Validates using a validator and DOMDocument, then asserts that the resulting error message matches the expected value.
     *
     * @param $message string
     * @return void
     */
    protected function validateAndAssertEquals(string $message): void
    {
        $result = $this->validator->validate($this->doc);
        self::assertEquals($message, $result->getFirstError()->getMessage());
    }

    /**
     * Reset the document.
     *
     * @return void
     */
    protected function resetDocument(): void
    {
        $this->doc = $this->getDomDocument();
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
    protected function addChildNodeWithNamespace(string $expression, string $namespace, string $name): void
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

    /**
     * Set value of content found by node expression in DOMDocument.
     *
     * @param string $expression
     * @param string $value
     * @return void
     */
    protected function setContentValue(string $expression, string $value): void
    {
        $xpath = new DOMXPath($this->doc);
        foreach ($xpath->evaluate($expression) as $node) {
            $node->nodeValue = $value;
        }
    }

    /**
     * Gets the doc from xml file.
     *
     * @return DOMDocument
     */
    protected function getDomDocument(): DOMDocument
    {
        $doc = new DOMDocument();
        $doc->load(__DIR__ . '/../../Fixtures/mets.xml');
        self::assertNotFalse($doc);
        return $doc;
    }

    /**
     * Assert validation has no error.
     *
     * @return void
     */
    protected function assertNoError(): void
    {
        $result = $this->validate();
        $this->assertFalse($result->hasErrors());
    }

    /**
     * Assert validation error has any.
     *
     * @param string $expression The expression in error message
     * @param string $context The context in error message
     * @return void
     */
    protected function assertErrorHasAny(string $expression, string $context=''): void
    {
        $message = 'There must be at least one element that matches the XPath expression "' . $expression . '"';
        if ($context != '') {
            $message .= ' under "' . $context . '"';
        }
        $this->validateAndAssertEquals($message);
    }

    /**
     * Assert validation error has one.
     *
     * @param string $expression The expression in error message
     * @param string $context The context in error message
     * @return void
     */
    protected function assertErrorHasOne(string $expression, string $context=''): void
    {
        $message = 'There must be an element that matches the XPath expression "' . $expression . '"';
        if ($context != '') {
            $message .= ' under "' . $context . '"';
        }
        $this->validateAndAssertEquals($message);
    }

    /**
     * Assert validation error has none or one.
     *
     * @param string $expression The expression in error message
     * @param string $context The context in error message
     * @return void
     */
    protected function assertErrorHasNoneOrOne(string $expression, string $context=''): void
    {
        $message = 'There must be no more than one element that matches the XPath expression "' . $expression . '"';
        if ($context != '') {
            $message .= ' under "' . $context . '"';
        }
        $this->validateAndAssertEquals($message);
    }

    /**
     * Assert validation error has attribute.
     *
     * @param string $expression The expression in error message
     * @param string $name The attribute name
     * @return void
     */
    protected function assertErrorHasAttribute(string $expression, string $name): void
    {
        $this->validateAndAssertEquals('Mandatory "' . $name . '" attribute of "' . $expression . '" is missing.');
    }

    /**
     * Assert validation error has attribute with value.
     *
     * @param string $expression The expression in error message
     * @param string $name The attribute name
     * @param string $value The attribute value
     * @return void
     */
    protected function assertErrorHasAttributeWithValue(string $expression, string $name, string $value): void
    {
        $this->validateAndAssertEquals('Value "' . $value . '" in the "' . $name . '" attribute of "' . $expression . '" is not permissible.');
    }

    /**
     * Assert validation error has attribute with URL value.
     *
     * @param string $expression The expression in error message
     * @param string $name The attribute name
     * @param string $value The attribute value
     * @return void
     */
    protected function assertErrorHasAttributeWithUrl(string $expression, string $name, string $value): void
    {
        $this->validateAndAssertEquals('URL "' . $value . '" in the "' . $name . '" attribute of "' . $expression . '" is not valid.');
    }

    /**
     * Assert validation error has content with Email.
     *
     * @param string $expression The expression in error message
     * @param string $name The attribute name
     * @param string $value The attribute value
     * @param string $targetExpression The target context expression
     * @return void
     */
    protected function assertErrorHasAttributeRefToOne(string $expression, string $name, string $value, string $targetExpression): void
    {
        $this->validateAndAssertEquals('Value "' . $value . '" in the "' . $name . '" attribute of "' . $expression . '" must reference one element under XPath expression "' . $targetExpression);
    }

    /**
     * Assert validation error has content with Email.
     *
     * @param string $expression The expression in error message
     * @param string $value The content value
     * @return void
     */
    protected function assertErrorHasContentWithEmail(string $expression, string $value): void
    {
        $this->validateAndAssertEquals('Email "' . $value . '" in the content of "' . $expression . '" is not valid.');
    }

    /**
     * Assert validation error has content with URL.
     *
     * @param string $expression The expression in error message
     * @param string $value The content value
     * @return void
     */
    protected function assertErrorHasContentWithUrl(string $expression, string $value): void
    {
        $this->validateAndAssertEquals('URL "' . $value . '" in the content of "' . $expression . '" is not valid.');
    }

    /**
     * Assert validation error has unique identifier.
     *
     * @param string $expression The expression in error message
     * @param string $value The attribute value
     * @return void
     */
    protected function assertErrorHasUniqueId(string $expression, string $value): void
    {
        $this->assertErrorHasUniqueAttribute($expression, 'ID', $value);
    }

    /**
     * Assert validation error has unique attribute with value.
     *
     * @param string $expression The expression in error message
     * @param string $name The attribute name
     * @param string $value The attribute value
     * @return void
     */
    protected function assertErrorHasUniqueAttribute(string $expression, string $name, string $value): void
    {
        $this->validateAndAssertEquals('"' . $name . '" attribute with value "' . $value . '" of "' . $expression . '" already exists.');
    }
}
