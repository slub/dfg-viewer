<?php

namespace Slub\Dfgviewer\Validation;

use DOMDocument;
use DOMNode;
use DOMNodeList;
use DOMXPath;
use Kitodo\Dlf\Validation\AbstractDlfValidator;

abstract class DOMDocumentValidator extends AbstractDlfValidator
{
    protected DOMXpath $xpath;

    private string $expression;

    private DOMNode $node;

    private DOMNodeList $nodeList;

    public function __construct()
    {
        parent::__construct(DOMDocument::class);
    }

    public function query(string $expression): DOMDocumentValidator
    {
        $this->expression = $expression;
        $this->nodeList = $this->xpath->query($this->expression);
        return $this;
    }

    public function iterate(callable $callback): DOMDocumentValidator
    {
        foreach ($this->nodeList as $node) {
            call_user_func_array($callback, array($node));
        }
        return $this;
    }

    public function setNode(DOMNode $node): DOMDocumentValidator
    {
        $this->node = $node;
        return $this;
    }

    public function getFirst(): DOMDocumentValidator
    {
        $this->selectNode(0);
        return $this;
    }

    public function selectNode(int $index): DOMDocumentValidator
    {
        if($this->nodeList->count() > $index) {
            $this->node = $this->nodeList->item($index);
        }
        return $this;
    }

    public function validateHasAny(): DOMDocumentValidator
    {
        if (!$this->nodeList->length > 0) {
            $this->addError('There must be at least one element that matches the XPath expression "' . $this->expression . '"', 1723727164447);
        }
        return $this;
    }

    public function validateHasOne(): DOMDocumentValidator
    {
        if ($this->nodeList->length != 1) {
            $this->addError('There must be an element that matches the XPath expression "' . $this->expression . '"', 1723727164447);
        }
        return $this;
    }

    public function validateHasNoneOrOne(): DOMDocumentValidator
    {
        if (!($this->nodeList->length == 0 || $this->nodeList->length == 1)) {
            $this->addError('There must be no more than one element that matches the XPath expression "' . $this->expression . '"', 1723727164447);
        }
        return $this;
    }

    public function validateHasAttributeWithUrl(string $name): DOMDocumentValidator
    {
        if(!isset($this->node)) {
            return $this;
        }

        if ($this->node->hasAttribute($name)) {
            $value = $this->node->getAttribute($name);
            if (!filter_var($value, FILTER_VALIDATE_URL)) {
                $this->addError('URL "' . $value . '" in the "' . $name . '" attribute of "' . $this->expression . '" is not valid.', 1724234607);
            }
        } else {
            $this->validateHasAttribute($name);
        }
        return $this;
    }

    public function validateHasAttributeWithValue(string $name, array $values): DOMDocumentValidator
    {
        if(!isset($this->node)) {
            return $this;
        }

        if ($this->node->hasAttribute($name)) {
            $value = $this->node->getAttribute($name);
            if (!in_array($value, $values)) {
                $this->addError('Value "' . $value . '" in the "' . $name . '" attribute of "' . $this->expression . '" is not permissible.', 1724234607);
            }
        } else {
            $this->validateHasAttribute($name);
        }
        return $this;
    }

    public function validateHasUniqueId(): DOMDocumentValidator
    {
        if(!isset($this->node)) {
            return $this;
        }

        if ($this->node->hasAttribute("ID")) {
            $id = $this->node->getAttribute("ID");
            if ($this->xpath->query('//*[@ID="' . $id . '"]')->length > 1) {
                $this->addError('"ID" attribute "' . $id . '" of "' .  $this->expression . '" already exists.', 1724234607);
            }
        } else {
            $this->validateHasAttribute("ID");
        }
        return $this;
    }

    public function validateHasAttribute(string $name): DOMDocumentValidator
    {
        if(!isset($this->node)) {
            return $this;
        }

        if (!$this->node->hasAttribute($name)) {
            $this->addError('Mandatory "' . $name . '" attribute of "' . $this->expression . '" is missing.', 1724234607);
        }
        return $this;
    }

    public function validateHasRefToOne(string $name, string $targetContextExpression): DOMDocumentValidator
    {
        if (!isset($this->node)) {
            return $this;
        }

        $targetNodes = $this->xpath->query($targetContextExpression);
        $id = $this->node->getAttribute($name);

        $foundElements = 0;
        foreach ($targetNodes as $targetNode) {
            $foundElements += $this->xpath->query('//*[@ID="' . $id . '"]', $targetNode)->length;
        }

        if ($foundElements !== 1) {
            $this->addError('Value "' . $id . '" in the "' . $name . '" attribute of "' . $this->expression . '" must reference one element under XPath expression "' . $targetContextExpression, 1724234607);
        }

        return $this;
    }

    protected function isValid($value): void
    {
        $this->xpath = new DOMXPath($value);
        $this->isValidDocument();
    }

    protected abstract function isValidDocument();

}
