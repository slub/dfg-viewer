<?php

namespace Slub\Dfgviewer\Validation;

use DOMDocument;
use DOMNode;
use DOMXPath;
use Kitodo\Dlf\Validation\AbstractDlfValidator;

abstract class DOMDocumentValidator extends AbstractDlfValidator
{
    protected DOMXpath $xpath;

    public function __construct()
    {
        parent::__construct(DOMDocument::class);
    }

    protected function isValid($value): void
    {
        $this->xpath = new DOMXPath($value);
        $this->isValidDocument();
    }

    protected function createNodeListValidator(string $expression, ?DOMNode $contextNode = null): DOMNodeListValidator
    {
        return new DOMNodeListValidator($this->xpath, $this->result, $expression, $contextNode);
    }

    protected function createNodeValidator(?DOMNode $node): DOMNodeValidator
    {
        return new DOMNodeValidator($this->xpath, $this->result, $node);
    }

    protected abstract function isValidDocument();



}
