<?php

namespace Slub\Dfgviewer\Validation\Common;

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

use DOMNode;
use DOMXPath;
use Slub\Dfgviewer\Common\ValidationHelper;
use TYPO3\CMS\Extbase\Error\Error;
use TYPO3\CMS\Extbase\Error\Result;

/**
 * The validator contains functions to validate a DOMNode.
 *
 * @package TYPO3
 * @subpackage dfg-viewer
 *
 * @access public
 */
class DomNodeValidator
{

    /**
     * @var DOMXPath The XPath of document to validate
     */
    private DOMXPath $xpath;

    /**
     * @var DOMNode|null The node to validate
     */
    private ?DOMNode $node;

    /**
     * @var Result The result containing errors of validation
     */
    private Result $result;

    public function __construct(DOMXPath $xpath, Result $result, ?DOMNode $node)
    {
        $this->xpath = $xpath;
        $this->result = $result;
        $this->node = $node;
    }

    /**
     * Validate that the node's content contains an Email.
     *
     * @return $this
     */
    public function validateHasContentWithEmail(): DomNodeValidator
    {
        if (!isset($this->node) || !$this->node->nodeValue) {
            return $this;
        }

        $email = $this->node->nodeValue;

        if (str_starts_with(strtolower($email), 'mailto:')) {
            $email = substr($email, 7);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->result->addError(new Error('Email "' . $this->node->nodeValue . '" in the content of "' . $this->node->getNodePath() . '" is not valid.', 1736504169));
        }

        return $this;
    }

    /**
     * Validate that the node's content contains a URL.
     *
     * @return $this
     */
    public function validateHasContentWithUrl(): DomNodeValidator
    {
        if (!isset($this->node) || !$this->node->nodeValue) {
            return $this;
        }

        if (!preg_match('/^' . ValidationHelper::URL_REGEX . '$/i', $this->node->nodeValue)) {
            $this->result->addError(new Error('URL "' . $this->node->nodeValue . '" in the content of "' . $this->node->getNodePath() . '" is not valid.', 1736504177));
        }

        return $this;
    }

    /**
     * Validate that the node has an attribute with a URL value.
     *
     * @param string $name The attribute name
     * @return $this
     */
    public function validateHasAttributeWithUrl(string $name): DomNodeValidator
    {
        if (!isset($this->node) || !$this->isElementType()) {
            return $this;
        }

        // @phpstan-ignore-next-line
        if (!$this->node->hasAttribute($name)) {
            return $this->validateHasAttribute($name);
        }

        // @phpstan-ignore-next-line
        $value = $this->node->getAttribute($name);

        if (!preg_match('/^' . ValidationHelper::URL_REGEX . '$/i', $value)) {
            $this->result->addError(new Error('URL "' . $value . '" in the "' . $name . '" attribute of "' . $this->node->getNodePath() . '" is not valid.', 1736504189));
        }

        return $this;
    }

    /**
     * Validate that the node has an attribute with a specific value.
     *
     * @param string $name The attribute name
     * @param array $values The allowed values
     * @return $this
     */
    public function validateHasAttributeWithValue(string $name, array $values): DomNodeValidator
    {
        if (!isset($this->node) || !$this->isElementType()) {
            return $this;
        }

        // @phpstan-ignore-next-line
        if (!$this->node->hasAttribute($name)) {
            return $this->validateHasAttribute($name);
        }

        // @phpstan-ignore-next-line
        $value = $this->node->getAttribute($name);
        if (!in_array($value, $values)) {
            $this->result->addError(new Error('Value "' . $value . '" in the "' . $name . '" attribute of "' . $this->node->getNodePath() . '" is not permissible.', 1736504197));
        }

        return $this;
    }

    /**
     * Validate that the node has a unique attribute with name.
     *
     * @param string $name The attribute name
     * @param string $contextExpression The context expression to determine uniqueness.
     * @return $this
     */
    public function validateHasUniqueAttribute(string $name, string $contextExpression): DomNodeValidator
    {
        if (!isset($this->node) || !$this->isElementType()) {
            return $this;
        }

        // @phpstan-ignore-next-line
        if (!$this->node->hasAttribute($name)) {
            return $this->validateHasAttribute($name);
        }

        // @phpstan-ignore-next-line
        $value = $this->node->getAttribute($name);
        if ($this->xpath->query($contextExpression . '[@' . $name . '="' . $value . '"]')->length > 1) {
            $this->result->addError(new Error('"' . $name . '" attribute with value "' . $value . '" of "' . $this->node->getNodePath() . '" already exists.', 1736504203));
        }

        return $this;
    }

    /**
     * Validate that the node has a unique identifier.
     *
     * @return $this
     */
    public function validateHasUniqueId(): DomNodeValidator
    {
        $this->validateHasUniqueAttribute("ID", "//*");
        return $this;
    }

    /**
     * Validate that the node has attribute with name.
     *
     * @param string $name The attribute name
     * @return $this
     */
    public function validateHasAttribute(string $name): DomNodeValidator
    {
        if (!isset($this->node) || !$this->isElementType()) {
            return $this;
        }

        // @phpstan-ignore-next-line
        if (!$this->node->hasAttribute($name)) {
            $this->result->addError(new Error('Mandatory "' . $name . '" attribute of "' . $this->node->getNodePath() . '" is missing.', 1736504217));
        }
        return $this;
    }

    /**
     * Validate that the node's resolvable identifier attribute points to a target with the specified "ID" attribute.
     *
     * @param string $name The attribute name containing the reference id as value
     * @param string $targetExpression The context expression to the target reference
     * @return $this
     */
    public function validateHasReferenceToId(string $name, string $targetExpression): DomNodeValidator
    {
        if (!isset($this->node) || !$this->isElementType()) {
            return $this;
        }

        // @phpstan-ignore-next-line
        if (!$this->node->hasAttribute($name)) {
            return $this->validateHasAttribute($name);
        }

        $targetNodes = $this->xpath->query($targetExpression);
        // @phpstan-ignore-next-line
        $identifier = $this->node->getAttribute($name);

        $foundElements = 0;
        foreach ($targetNodes as $targetNode) {
            $foundElements += $this->xpath->query('//*[@ID="' . $identifier . '"]', $targetNode)->length;
        }

        if ($foundElements !== 1) {
            $this->result->addError(new Error('Value "' . $identifier . '" in the "' . $name . '" attribute of "' . $this->node->getNodePath() . '" must reference one element under XPath expression "' . $targetExpression, 1736504228));
        }

        return $this;
    }

    /**
     * Checks if node type is of type XML_ELEMENT_NODE.
     *
     * @return bool True if is element node
     */
    public function isElementType(): bool
    {
        return $this->node->nodeType === XML_ELEMENT_NODE;
    }
}
