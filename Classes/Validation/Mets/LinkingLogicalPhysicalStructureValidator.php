<?php

namespace Slub\Dfgviewer\Validation\Mets;

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

use Slub\Dfgviewer\Common\ValidationHelper as VH;
use Slub\Dfgviewer\Validation\AbstractDomDocumentValidator;

/**
 * The validator validates against the rules outlined in chapter 2.4 of the METS application profile 2.4.
 *
 * @package TYPO3
 * @subpackage dfg-viewer
 *
 * @access public
 */
class LinkingLogicalPhysicalStructureValidator extends AbstractDomDocumentValidator
{
    public function isValidDocument(): void
    {
        // Validates against the rules of chapter "2.4 Structure links - mets:structLink"
        $this->createNodeListValidator(VH::XPATH_STRUCT_LINK)
            ->validateHasNoneOrOne();

        $this->validateLinkElements();
    }

    /**
     * Validates the linking elements.
     *
     * Validates against the rules of chapter "2.4.2.1 Linking – mets:smLink"
     *
     * @return void
     */
    protected function validateLinkElements(): void
    {
        $linkElements = $this->createNodeListValidator(VH::XPATH_STRUCT_LINK_ELEMENTS)
            ->validateHasAny()
            ->getNodeList();
        foreach ($linkElements as $linkElement) {
            $this->validateLinkElement($linkElement);
        }
    }

    protected function validateLinkElement(\DOMNode $linkElement): void
    {
        $this->createNodeAttributeValidator($linkElement)
            ->validateReferenceToId("xlink:from", VH::XPATH_LOGICAL_STRUCTURES . '//mets:div')
            ->validateReferenceToId("xlink:to", VH::XPATH_PHYSICAL_STRUCTURES . '//mets:div');
    }
}
