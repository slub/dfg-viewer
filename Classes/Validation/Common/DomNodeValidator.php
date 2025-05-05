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
use Slub\Dfgviewer\Common\IsoLanguageHelper;
use Slub\Dfgviewer\Common\IsoScriptHelper;
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
    public function validateHasEmailContent(): DomNodeValidator
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
     * Validate that the node's content contains a ISO 639-2b.
     *
     * @return $this
     */
    public function validateHasIso6392BContent(): DomNodeValidator
    {
        if (!isset($this->node) || !$this->node->nodeValue) {
            return $this;
        }

        if (!IsoLanguageHelper::iso6392BCodeExists($this->node->nodeValue)) {
            $this->result->addError(new Error('Value "' . $this->node->nodeValue . '" in the content of "' . $this->node->getNodePath() . '" is not a valid ISO 639-2/B code. For more information, please consider https://www.loc.gov/standards/iso639-2/php/code_list.php.', 1746455012));
        }

        return $this;
    }

    /**
     * Validate that the node's content contains a ISO 15924 value.
     *
     * @return $this
     */
    public function validateHasIso15924Content(): DomNodeValidator
    {
        if (!isset($this->node) || !$this->node->nodeValue) {
            return $this;
        }

        if (!array_key_exists($this->node->nodeValue, IsoScriptHelper::ISO_15924)) {
            $this->result->addError(new Error('Value "' . $this->node->nodeValue . '" in the content of "' . $this->node->getNodePath() . '" is not a valid ISO 15924 code. For more information, please consider https://unicode.org/iso15924/iso15924-codes.html.', 1746455012));
        }

        return $this;
    }


    /**
     * Validate that the node's content contains a URL.
     *
     * @return $this
     */
    public function validateHasUrlContent(): DomNodeValidator
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
     * Validate that the node has an attribute with a ISO 639-2/B value.
     *
     * @param string $name The attribute name
     * @return $this
     */
    public function validateHasAttributeWithIso6392B(string $name): DomNodeValidator
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

        if (!IsoLanguageHelper::iso6392BCodeExists($value)) {
            $this->result->addError(new Error('Value "' . $value . '" in the "' . $name . '" attribute of node "' . $this->node->getNodePath() . '" is not a valid ISO 639-2/B code. For more information, please consider https://www.loc.gov/standards/iso639-2/php/code_list.php.', 1743159957));
        }

        return $this;
    }

    /**
     * Validate that the node has an attribute with a ISO 15924 value.
     *
     * @param string $name The attribute name
     * @return $this
     */
    public function validateHasAttributeWithIso15924(string $name): DomNodeValidator
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

        if (!array_key_exists($value, IsoScriptHelper::ISO_15924)) {
            $this->result->addError(new Error('Value "' . $value . '" in the "' . $name . '" attribute of node "' . $this->node->getNodePath() . '" is not a valid ISO 15924 code. For more information, please consider https://unicode.org/iso15924/iso15924-codes.html.', 1743588592));
        }

        return $this;
    }

    /**
     * Validate that the node has an attribute with a URL value.
     *
     * @param string $name The attribute name
     * @return $this
     */
    public function validateHasUrlAttribute(string $name): DomNodeValidator
    {
        if (!isset($this->node) || !$this->isElementType()) {
            return $this;
        }

        if (!$this->getDomElement()->hasAttribute($name)) {
            return $this->validateHasAttribute($name);
        }

        $value = $this->getDomElement()->getAttribute($name);

        if (!preg_match('/^' . ValidationHelper::URL_REGEX . '$/i', $value)) {
            $this->result->addError(new Error('URL "' . $value . '" in the "' . $name . '" attribute of node "' . $this->node->getNodePath() . '" is not valid.', 1736504189));
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
    public function validateHasAttributeValue(string $name, array $values): DomNodeValidator
    {
        if (!isset($this->node) || !$this->isElementType()) {
            return $this;
        }

        if (!$this->getDomElement()->hasAttribute($name)) {
            return $this->validateHasAttribute($name);
        }

        $attrValue = $this->getDomElement()->getAttribute($name);
        $match = false;
        foreach ($values as $value) {
            if (str_starts_with($attrValue, $value)) {
                $match = true;
                break;
            }
        }

        if (!$match) {
            $this->result->addError(new Error('Value "' . $attrValue . '" in the "' . $name . '" attribute of "' . $this->node->getNodePath() . '" is not permissible.', 1736504197));
        }

        return $this;
    }

    /**
     * Validate that the node has an attribute with a numeric value.
     *
     * @param string $name The attribute name
     * @return $this
     */
    public function validateHasNumericAttribute(string $name): DomNodeValidator
    {
        if (!isset($this->node) || !$this->isElementType()) {
            return $this;
        }

        if (!$this->getDomElement()->hasAttribute($name)) {
            return $this->validateHasAttribute($name);
        }

        $value = $this->getDomElement()->getAttribute($name);
        if (!is_numeric($value)) {
            $this->result->addError(new Error('Value "' . $value . '" in the "' . $name . '" attribute of "' . $this->node->getNodePath() . '" is not numeric.', 1736504203));
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

        if (!$this->getDomElement()->hasAttribute($name)) {
            return $this->validateHasAttribute($name);
        }

        $value = $this->getDomElement()->getAttribute($name);
        if ($this->xpath->query($contextExpression . '[@' . $name . '="' . $value . '"]')->length > 1) {
            $this->result->addError(new Error('Value "' . $value . '" in the "' . $name . '" attribute of "' . $this->node->getNodePath() . '" already exists.', 1736504203));
        }

        return $this;
    }

    /**
     * Validate that the node has a regex attribute with name.
     *
     * @return $this
     */
    public function validateHasRegexAttribute(string $name, string $regex): DomNodeValidator
    {
        if (!isset($this->node) || !$this->isElementType()) {
            return $this;
        }

        if (!$this->getDomElement()->hasAttribute($name)) {
            return $this->validateHasAttribute($name);
        }

        $value = $this->getDomElement()->getAttribute($name);
        $pattern = '/^' . $regex . '$/i';
        if (!preg_match('/^' . $regex . '$/i', $value)) {
            $this->result->addError(new Error('Value "' . $value . '" in the "' . $name . '" attribute of "' . $this->node->getNodePath() . '" does not match the pattern "' . $pattern . '".', 1742208208));
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

        if (!$this->getDomElement()->hasAttribute($name)) {
            $this->result->addError(new Error('Mandatory "' . $name . '" attribute of "' . $this->node->getNodePath() . '" is missing.', 1736504217));
        }
        return $this;
    }

    /**
     * Validate that the node does not have any attribute with the name.
     *
     * @param string $name The attribute name
     * @return $this
     */
    public function validateHasNoneAttribute(string $name): DomNodeValidator
    {
        if (!isset($this->node) || !$this->isElementType()) {
            return $this;
        }

        // @phpstan-ignore-next-line
        if ($this->node->hasAttribute($name)) {
            $this->result->addError(new Error('Attribute "' . $name . '" is not allowed on node "' . $this->node->getNodePath() . '".', 1736504217));
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

        if (!$this->getDomElement()->hasAttribute($name)) {
            return $this->validateHasAttribute($name);
        }

        $identifier = $this->getDomElement()->getAttribute($name);

        $foundElements = 0;
        $targetNodes = $this->xpath->query($targetExpression);
        foreach ($targetNodes as $targetNode) {
            if ($targetNode instanceof \DOMElement && $targetNode->getAttribute('ID') == $identifier) {
                $foundElements++;
            }
        }

        if ($foundElements !== 1) {
            $this->result->addError(new Error('Value "' . $identifier . '" in the "' . $name . '" attribute of "' . $this->node->getNodePath() . '" must reference an element within the XPath expression "' . $targetExpression . '"', 1736504228));
        }

        return $this;
    }

    /**
     * Checks if node type is DOMElement.
     *
     * @return bool True if is element node
     */
    public function isElementType(): bool
    {
        return $this->node instanceof \DOMElement;
    }

    /**
     * Get the DOMElement.
     *
     * @return \DOMElement The element node
     */
    public function getDomElement(): ?\DOMElement
    {
        if ($this->node instanceof \DOMElement) {
            return $this->node;
        }
        return null;
    }
}
