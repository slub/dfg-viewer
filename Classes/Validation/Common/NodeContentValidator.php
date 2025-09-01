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
use TYPO3\CMS\Extbase\Error\Result;

/**
 * The validator contains functions to validate a DOMNode.
 *
 * @package TYPO3
 * @subpackage dfg-viewer
 *
 * @access public
 */
class NodeContentValidator extends AbstractNodeValidator
{
    /**
     * Validate that the node's content contains an Email.
     *
     * @return $this
     */
    public function validateEmail(): NodeContentValidator
    {
        if (!isset($this->node) || !$this->node->nodeValue) {
            return $this;
        }

        $email = $this->node->nodeValue;

        if (str_starts_with(strtolower($email), 'mailto:')) {
            $email = substr($email, 7);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addSeverityMessage('Email "' . $this->node->nodeValue . '" in the content of "' . $this->node->getNodePath() . '" is not valid.', 1736504169);
        }

        return $this;
    }

    /**
     * Validate that the node's content contains an ISO 639-2b.
     *
     * @return $this
     */
    public function validateIso6392B(): NodeContentValidator
    {
        if (!isset($this->node) || !$this->node->nodeValue) {
            return $this;
        }

        if (!IsoLanguageHelper::iso6392BCodeExists($this->node->nodeValue)) {
            $this->addSeverityMessage('Value "' . $this->node->nodeValue . '" in the content of "' . $this->node->getNodePath() . '" is not a valid ISO 639-2/B code. For more information, please consider https://www.loc.gov/standards/iso639-2/php/code_list.php.', 1746455012);
        }

        return $this;
    }

    /**
     * Validate that the node's content contains a ISO 15924 value.
     *
     * @return $this
     */
    public function validateIso15924(): NodeContentValidator
    {
        if (!isset($this->node) || !$this->node->nodeValue) {
            return $this;
        }

        if (!array_key_exists($this->node->nodeValue, IsoScriptHelper::ISO_15924)) {
            $this->addSeverityMessage('Value "' . $this->node->nodeValue . '" in the content of "' . $this->node->getNodePath() . '" is not a valid ISO 15924 code. For more information, please consider https://unicode.org/iso15924/iso15924-codes.html.', 1746455012);
        }

        return $this;
    }


    /**
     * Validate that the node's content contains a URL.
     *
     * @return $this
     */
    public function validateUrl(): NodeContentValidator
    {
        if (!isset($this->node) || !$this->node->nodeValue) {
            return $this;
        }

        if (!preg_match('/^' . ValidationHelper::URL_REGEX . '$/i', $this->node->nodeValue)) {
            $this->addSeverityMessage('URL "' . $this->node->nodeValue . '" in the content of "' . $this->node->getNodePath() . '" is not valid.', 1736504177);
        }

        return $this;
    }
}
