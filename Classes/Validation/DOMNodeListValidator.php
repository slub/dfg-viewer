<?php

namespace Slub\Dfgviewer\Validation;

use DOMXPath;
use DOMNode;
use DOMNodeList;
use TYPO3\CMS\Extbase\Error\Error;
use TYPO3\CMS\Extbase\Error\Result;

class DOMNodeListValidator
{
    private string $expression;

    private DOMNodeList $nodeList;

    private Result $result;

    public function __construct(DOMXPath $xpath, Result $result, string $expression, ?DOMNode $contextNode = null)
    {
        $this->expression = $expression;
        $this->nodeList = $xpath->query($expression, $contextNode);
        $this->result = $result;
    }

    public function iterate(callable $callback): DOMNodeListValidator
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

    public function validateHasAny(): DOMNodeListValidator
    {
        if (!$this->nodeList->length > 0) {
            $this->result->addError(new Error('There must be at least one element that matches the XPath expression "' . $this->expression . '"', 23));
        }
        return $this;
    }

    public function validateHasOne(): DOMNodeListValidator
    {
        if ($this->nodeList->length != 1) {
            $this->result->addError(new Error('There must be an element that matches the XPath expression "' . $this->expression . '"', 434));
        }
        return $this;
    }

    public function validateHasNoneOrOne(): DOMNodeListValidator
    {
        if (!($this->nodeList->length == 0 || $this->nodeList->length == 1)) {
            $this->result->addError(new Error('There must be no more than one element that matches the XPath expression "' . $this->expression . '"', 43));
        }
        return $this;
    }

}
