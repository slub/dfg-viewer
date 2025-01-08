<?php

namespace Slub\Dfgviewer\Validation\Dom;

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
use TYPO3\CMS\Extbase\Error\Error;
use TYPO3\CMS\Extbase\Error\Result;

class DomNodeValidator
{
    protected DOMXPath $xpath;

    public function __construct(DOMXPath $xpath, Result $result, ?DOMNode $node)
    {
        $this->xpath = $xpath;
        $this->result = $result;
        $this->node = $node;
    }

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
            $this->result->addError(new Error('Email "' . $this->node->nodeValue . '" in the content of "' . $this->node->getNodePath() . '" is not valid.', 1724234607));
        }

        return $this;
    }

    public function validateHasContentWithUrl(): DomNodeValidator
    {
        if (!isset($this->node) || !$this->node->nodeValue) {
            return $this;
        }

        if (!filter_var($this->node->nodeValue, FILTER_VALIDATE_URL)) {
            $this->result->addError(new Error('URL "' . $this->node->nodeValue . '" in the content of "' . $this->node->getNodePath() . '" is not valid.', 1724234607));
        }

        return $this;
    }

    public function validateHasAttributeWithUrl(string $name): DomNodeValidator
    {
        if (!isset($this->node)) {
            return $this;
        }

        if (!$this->node->hasAttribute($name)) {
            return $this->validateHasAttribute($name);
        }

        $value = $this->node->getAttribute($name);
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            $this->result->addError(new Error('URL "' . $value . '" in the "' . $name . '" attribute of "' . $this->node->getNodePath() . '" is not valid.', 1724234607));
        }

        return $this;
    }

    public function validateHasAttributeWithValue(string $name, array $values): DomNodeValidator
    {
        if (!isset($this->node)) {
            return $this;
        }

        if (!$this->node->hasAttribute($name)) {
            return $this->validateHasAttribute($name);
        }

        $value = $this->node->getAttribute($name);
        if (!in_array($value, $values)) {
            $this->result->addError(new Error('Value "' . $value . '" in the "' . $name . '" attribute of "' . $this->node->getNodePath() . '" is not permissible.', 1724234607));
        }

        return $this;
    }

    public function validateHasUniqueAttribute(string $name, string $contextExpression): DomNodeValidator
    {
        if (!isset($this->node)) {
            return $this;
        }

        if (!$this->node->hasAttribute($name)) {
            return $this->validateHasAttribute($name);
        }

        $value = $this->node->getAttribute($name);
        if ($this->xpath->query($contextExpression . '[@' . $name . '="' . $value . '"]')->length > 1) {
            $this->result->addError(new Error('"' . $name . '" attribute with value "' . $value . '" of "' . $this->node->getNodePath() . '" already exists.', 1724234607));
        }

        return $this;
    }

    public function validateHasUniqueId(): DomNodeValidator
    {
        $this->validateHasUniqueAttribute("ID", "//*");
        return $this;
    }

    public function validateHasAttribute(string $name): DomNodeValidator
    {
        if (!isset($this->node)) {
            return $this;
        }

        if (!$this->node->hasAttribute($name)) {
            $this->result->addError(new Error('Mandatory "' . $name . '" attribute of "' . $this->node->getNodePath() . '" is missing.', 1724234607));
        }
        return $this;
    }

    public function validateHasReferenceToId(string $name, string $targetContextExpression): DomNodeValidator
    {
        if (!isset($this->node)) {
            return $this;
        }

        if (!$this->node->hasAttribute($name)) {
            return $this->validateHasAttribute($name);
        }

        $targetNodes = $this->xpath->query($targetContextExpression);
        $id = $this->node->getAttribute($name);

        $foundElements = 0;
        foreach ($targetNodes as $targetNode) {
            $foundElements += $this->xpath->query('//*[@ID="' . $id . '"]', $targetNode)->length;
        }

        if ($foundElements !== 1) {
            $this->result->addError(new Error('Value "' . $id . '" in the "' . $name . '" attribute of "' . $this->node->getNodePath() . '" must reference one element under XPath expression "' . $targetContextExpression, 1724234607));
        }

        return $this;
    }

}
