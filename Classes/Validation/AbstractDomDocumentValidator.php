<?php

namespace Slub\Dfgviewer\Validation;

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
use DOMNode;
use DOMXPath;
use Kitodo\Dlf\Validation\AbstractDlfValidator;
use Slub\Dfgviewer\Validation\Common\DomNodeListValidator;
use Slub\Dfgviewer\Validation\Common\DomNodeValidator;

abstract class AbstractDomDocumentValidator extends AbstractDlfValidator
{

    /**
     * @var DOMXPath The XPath of DOMDocument value.
     */
    protected DOMXpath $xpath;

    public function __construct()
    {
        parent::__construct(DOMDocument::class);
    }

    /**
     * Performs the actual validator-specific validation.
     *
     * This function must be implemented in the inherited validator.
     *
     * @return mixed
     */
    abstract public function isValidDocument();

    /**
     * Check if $value is valid.
     *
     * This function overwrites the isValid function and initializes common necessary functionalities based on the current document.
     * After that, the isValidDocument function is called, which must be implemented by each validator and performs the actual validator-specific validation.
     * If it is not valid, errors are added to the result.
     *
     * @param mixed $value
     */
    protected function isValid($value): void
    {
        $this->xpath = new DOMXPath($value);
        $this->isValidDocument();
    }

    protected function createNodeListValidator(string $expression, ?DOMNode $contextNode=null): DomNodeListValidator
    {
        return new DomNodeListValidator($this->xpath, $this->result, $expression, $contextNode);
    }

    protected function createNodeValidator(?DOMNode $node): DomNodeValidator
    {
        return new DomNodeValidator($this->xpath, $this->result, $node);
    }
}
