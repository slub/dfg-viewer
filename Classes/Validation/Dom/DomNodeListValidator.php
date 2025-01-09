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

/**
 * The validator contains functions to validate a DOMNodeList.
 *
 * @package TYPO3
 * @subpackage dfg-viewer
 *
 * @access public
 */
class DomNodeListValidator
{
    /**
     * @var string The expression of XPath query
     */
    private string $expression;

    /**
     * @var DOMNode|null The context node of XPath query
     */
    private ?DOMNode $contextNode;

    /**
     * @var DOMNodeList|false|mixed The node list result of XPath query
     */
    private DOMNodeList $nodeList;

    /**
     * @var Result The result containing errors of validation
     */
    private Result $result;

    public function __construct(DOMXPath $xpath, Result $result, string $expression, ?DOMNode $contextNode = null)
    {
        $this->expression = $expression;
        $this->contextNode = $contextNode;
        $this->nodeList = $xpath->query($expression, $contextNode);
        $this->result = $result;
    }

    /**
     * Get the first node from the node list.
     *
     * @return DOMNode|null
     */
    public function getFirstNode(): ?DOMNode
    {
        return $this->getNode(0);
    }

    /**
     * Get a node from the node list at a specific index.
     *
     * @param int $index The index to retrieve the node
     * @return DOMNode|null
     */
    public function getNode(int $index): ?DOMNode
    {
        return $this->nodeList->item($index);
    }

    /**
     * Get the node list.
     *
     * @return DOMNodeList
     */
    public function getNodeList(): DOMNodeList
    {
        return $this->nodeList;
    }

    /**
     * Validates the node list has any node.
     *
     * @return $this
     */
    public function validateHasAny(): DomNodeListValidator
    {
        if (!$this->nodeList->length > 0) {
            $this->addError('There must be at least one element');
        }
        return $this;
    }

    /**
     * Validates the node list has one node.
     *
     * @return $this
     */
    public function validateHasOne(): DomNodeListValidator
    {
        if ($this->nodeList->length != 1) {
            $this->addError('There must be an element');
        }
        return $this;
    }

    /**
     * Validates the node list has none or one node.
     *
     * @return $this
     */
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
        if ($this->contextNode) {
            $message .= ' under "' . $this->contextNode->getNodePath() . '"';
        }
        $this->result->addError(new Error($message, 23));
    }
}
