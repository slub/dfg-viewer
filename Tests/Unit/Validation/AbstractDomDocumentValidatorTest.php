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
        $this->hasNoError();
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
    protected function hasNoError(): void
    {
        $result = $this->validate();
        $this->assertFalse($result->hasErrors());
    }

    /**
     * Assert error of has any validation.
     *
     * @param string $expression The expression in error message
     * @param string $context The context in error message
     * @return void
     */
    protected function hasErrorAny(string $expression, string $context=''): void
    {
        $message = 'There must be at least one element that matches the XPath expression "' . $expression . '"';
        if ($context != '') {
            $message .= ' under "' . $context . '"';
        }
        $this->validateAndAssertEquals($message);
    }

    /**
     * Assert error of has one validation.
     *
     * @param string $expression The expression in error message
     * @param string $context The context in error message
     * @return void
     */
    protected function hasErrorOne(string $expression, string $context=''): void
    {
        $message = 'There must be an element that matches the XPath expression "' . $expression . '"';
        if ($context != '') {
            $message .= ' under "' . $context . '"';
        }
        $this->validateAndAssertEquals($message);
    }

    /**
     * Assert error of has none or one validation.
     *
     * @param string $expression The expression in error message
     * @param string $context The context in error message
     * @return void
     */
    protected function hasErrorNoneOrOne(string $expression, string $context=''): void
    {
        $message = 'There must be no more than one element that matches the XPath expression "' . $expression . '"';
        if ($context != '') {
            $message .= ' under "' . $context . '"';
        }
        $this->validateAndAssertEquals($message);
    }

    /**
     * Assert error of has attribute validation.
     *
     * @param string $expression The expression in error message
     * @param string $name The attribute name
     * @return void
     */
    protected function hasErrorAttribute(string $expression, string $name): void
    {
        $this->validateAndAssertEquals('Mandatory "' . $name . '" attribute of "' . $expression . '" is missing.');
    }

    /**
     * Assert error of has attribute with value validation.
     *
     * @param string $expression The expression in error message
     * @param string $name The attribute name
     * @param string $value The attribute value
     * @return void
     */
    protected function hasErrorAttributeWithValue(string $expression, string $name, string $value): void
    {
        $this->validateAndAssertEquals('Value "' . $value . '" in the "' . $name . '" attribute of "' . $expression . '" is not permissible.');
    }

    /**
     * Assert error of has attribute with URL value validation.
     *
     * @param string $expression The expression in error message
     * @param string $name The attribute name
     * @param string $value The attribute value
     * @return void
     */
    protected function hasErrorAttributeWithUrl(string $expression, string $name, string $value): void
    {
        $this->validateAndAssertEquals('URL "' . $value . '" in the "' . $name . '" attribute of "' . $expression . '" is not valid.');
    }

    /**
     * Assert error of has attribute reference to one validation.
     *
     * @param string $expression The expression in error message
     * @param string $name The attribute name
     * @param string $value The attribute value
     * @param string $targetExpression The target context expression
     * @return void
     */
    protected function hasErrorAttributeRefToOne(string $expression, string $name, string $value, string $targetExpression): void
    {
        $this->validateAndAssertEquals('Value "' . $value . '" in the "' . $name . '" attribute of "' . $expression . '" must reference one element under XPath expression "' . $targetExpression);
    }

    /**
     * Assert error of has content with Email validation.
     *
     * @param string $expression The expression in error message
     * @param string $value The content value
     * @return void
     */
    protected function hasErrorContentWithEmail(string $expression, string $value): void
    {
        $this->validateAndAssertEquals('Email "' . $value . '" in the content of "' . $expression . '" is not valid.');
    }

    /**
     * Assert error of has content with URL.
     *
     * @param string $expression The expression in error message
     * @param string $value The content value
     * @return void
     */
    protected function hasErrorContentWithUrl(string $expression, string $value): void
    {
        $this->validateAndAssertEquals('URL "' . $value . '" in the content of "' . $expression . '" is not valid.');
    }

    /**
     * Assert error of has unique identifier.
     *
     * @param string $expression The expression in error message
     * @param string $value The attribute value
     * @return void
     */
    protected function hasErrorUniqueId(string $expression, string $value): void
    {
        $this->hasErrorUniqueAttribute($expression, 'ID', $value);
    }

    /**
     * Assert error of has unique attribute with value.
     *
     * @param string $expression The expression in error message
     * @param string $name The attribute name
     * @param string $value The attribute value
     * @return void
     */
    protected function hasErrorUniqueAttribute(string $expression, string $name, string $value): void
    {
        $this->validateAndAssertEquals('"' . $name . '" attribute with value "' . $value . '" of "' . $expression . '" already exists.');
    }
}
