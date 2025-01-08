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
use DOMNodeList;
use DOMXPath;
use TYPO3\CMS\Extbase\Error\Error;
use TYPO3\CMS\Extbase\Error\Result;

class DomNodeListValidator
{
    private string $expression;

    private ?DOMNode $contextNode;

    private DOMNodeList $nodeList;

    private Result $result;

    public function __construct(DOMXPath $xpath, Result $result, string $expression, ?DOMNode $contextNode = null)
    {
        $this->expression = $expression;
        $this->contextNode = $contextNode;
        $this->nodeList = $xpath->query($expression, $contextNode);
        $this->result = $result;
    }

    public function iterate(callable $callback): DomNodeListValidator
    {
        foreach ($this->nodeList as $node) {
            call_user_func_array($callback, array($node));
        }
        return $this;
    }

    public function getFirstNode(): ?DOMNode
    {
        return $this->getNode(0);
    }

    public function getNode(int $index): ?DOMNode
    {
        return $this->nodeList->item($index);
    }

    public function validateHasAny(): DomNodeListValidator
    {
        if (!$this->nodeList->length > 0) {
            $this->addError('There must be at least one element');
        }
        return $this;
    }

    public function validateHasOne(): DomNodeListValidator
    {
        if ($this->nodeList->length != 1) {
            $this->addError('There must be an element');
        }
        return $this;
    }

    public function validateHasNoneOrOne(): DomNodeListValidator
    {
        if (!($this->nodeList->length == 0 || $this->nodeList->length == 1)) {
            $this->addError('There must be no more than one element');
        }
        return $this;
    }

    private function addError(string $prefix): void
    {
        $message = $prefix . ' that matches the XPath expression "' . $this->expression . '"';
        if($this->contextNode) {
            $message .= ' under "' . $this->contextNode->getNodePath() . '"';
        }
        $this->result->addError(new Error($message, 23));
    }
}
