<?php

namespace Slub\Dfgviewer\Validation;

use DOMDocument;
use DOMNodeList;
use DOMXPath;
use Kitodo\Dlf\Validation\AbstractDlfValidator;

abstract class DOMDocumentValidator extends AbstractDlfValidator
{
    protected DOMXpath $xpath;

    private string $expression;

    private DOMNodeList $nodeList;

    public function __construct()
    {
        parent::__construct(DOMDocument::class);
    }

    public function query(string $expression)
    {
        $this->expression = $expression;
        $this->nodeList = $this->xpath->query($this->expression);
        return $this;
    }

    public function iterate(callable $callback)
    {
        foreach ($this->nodeList as $node) {
            call_user_func_array($callback, array($node));
        }
        return $this;
    }

    public function validateHasAny()
    {
        if (!$this->nodeList->length > 0) {
            $this->addError('There must be at least one element that matches the XPath expression "' . $this->expression . '"', 1723727164447);
        }
        return $this;
    }

    public function validateHasOne()
    {
        if ($this->nodeList->length != 1) {
            $this->addError('There must be an element that matches the XPath expression "' . $this->expression . '"', 1723727164447);
        }
        return $this;
    }

    public function validateHasNoneOrOne()
    {
        if (!($this->nodeList->length == 0 || $this->nodeList->length == 1)) {
            $this->addError('There must be no more than one element that matches the XPath expression "' . $this->expression . '"', 1723727164447);
        }
        return $this;
    }

    public function validateHasAttributeWithUrl(\DOMNode $node, string $name)
    {
        if ($node->hasAttribute($name)) {
            $value = $node->getAttribute($name);
            if (!filter_var($value, FILTER_VALIDATE_URL)) {
                $this->addError('URL "' . $value . '" in the "' . $name . '" attribute of "' . $this->expression . '" is not valid.', 1724234607);
            }
        } else {
            $this->validateHasAttribute($node, $name);
        }
    }

    public function validateHasAttributeWithValue(\DOMNode $node, string $name, array $values)
    {
        if ($node->hasAttribute($name)) {
            $value = $node->getAttribute($name);
            if (!in_array($value, $values)) {
                $this->addError('Value "' . $value . '" in the "' . $name . '" attribute of "' . $this->expression . '" is not permissible.', 1724234607);
            }
        } else {
            $this->validateHasAttribute($node, $name);
        }
    }

    public function validateHasUniqueId(\DOMNode $node)
    {
        if ($node->hasAttribute("ID")) {
            $id = $node->getAttribute("ID");
            if ($this->xpath->query('//*[@ID="' . $id . '"]')->length > 1) {
                $this->addError('"ID" attribute "' . $id . '" of "' .  $this->expression . '" already exists.', 1724234607);
            }
        } else {
            $this->validateHasAttribute($node, "ID");
        }
    }

    /**
     * @param \DOMNode $node
     * @param string $name
     * @return void
     */
    public function validateHasAttribute(\DOMNode $node, string $name): void
    {
        if (!$node->hasAttribute($name)) {
            $this->addError('Mandatory "' . $name . '" attribute of "' . $this->expression . '" is missing.', 1724234607);
        }
    }

    protected function isValid($value): void
    {
        $this->xpath = new DOMXPath($value);
        $this->isValidDocument();
    }

    protected abstract function isValidDocument();

}
