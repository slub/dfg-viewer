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

use Slub\Dfgviewer\Validation\ApplicationProfileBaseValidator;

/**
 * The validator validates against the rules outlined in chapter 2.3 of the METS application profile 2.3.1.
 *
 * @package TYPO3
 * @subpackage dfg-viewer
 *
 * @access public
 */
class LinkingLogicalPhysicalStructureValidator extends ApplicationProfileBaseValidator
{

    const XPATH_STRUCT_LINK = '//mets:mets/mets:structLink';

    const XPATH_LINK_ELEMENTS = self::XPATH_STRUCT_LINK . '/mets:smLink' ;

    protected function isValidDocument(): void
    {
        // Validates against the rules of chapter "2.3.1 Structure links - mets:structLink"
        $this->createNodeListValidator(self::XPATH_STRUCT_LINK)
            ->validateHasNoneOrOne();

        $this->validateLinkElements();
    }

    /**
     * Validates the linking elements.
     *
     * Validates against the rules of chapter "2.3.2.1 Linking – mets:smLink"
     *
     * @return void
     */
    protected function validateLinkElements(): void
    {
        $this->createNodeListValidator(self::XPATH_LINK_ELEMENTS)
            ->validateHasAny()
            ->iterate(array($this, "validateLinkElement"));
    }

    public function validateLinkElement(\DOMNode $linkElement): void
    {
        $this->createNodeValidator($linkElement)
            ->validateHasRefToOne("xlink:from", LogicalStructureValidator::XPATH_LOGICAL_STRUCTURES)
            ->validateHasRefToOne("xlink:to", PhysicalStructureValidator::XPATH_PHYSICAL_STRUCTURES);
    }

}
