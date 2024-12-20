<?php

namespace Slub\Dfgviewer\Validation\Mets;

use Slub\Dfgviewer\Validation\ApplicationProfileBaseValidator;

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

/**
 * The validator validates against the rules outlined in chapter 2.5 of the METS application profile 2.3.1.
 *
 * @package TYPO3
 * @subpackage dfg-viewer
 *
 * @access public
 */
class DeskriptiveMetadataValidator extends ApplicationProfileBaseValidator
{

    const XPATH_DESCRIPTIVE_METADATA_SECTIONS = '//mets:mets/mets:dmdSec';

    protected function isValidDocument(): void
    {
        // Validates against the rules of chapter "2.5.1 Metadatensektion – mets:dmdSec"
        $this->createNodeListValidator(self::XPATH_DESCRIPTIVE_METADATA_SECTIONS)
            ->validateHasAny()
            ->iterate(array($this, 'validateDescriptiveMetadataSection'));

        // If a physical structure is present, there must be one file section.
        $logicalStructureElement = $this->createNodeListValidator(LogicalStructureValidator::XPATH_STRUCTURAL_ELEMENTS)
            ->validateHasOne()
            ->getFirstNode();

        $this->createNodeValidator($logicalStructureElement)
            ->validateHasReferenceToId('DMDID', self::XPATH_DESCRIPTIVE_METADATA_SECTIONS);
    }

    /**
     * Validates the embedded metadata.
     *
     * Validates against the rules of chapter "2.5.2.1 Eingebettete Metadaten – mets:mdWrap"
     *
     * @return void
     */
    public function validateDescriptiveMetadataSection(\DOMNode $descriptiveMetadataSection): void
    {
        $mdWrap = $this->createNodeListValidator('/mets:mdWrap', $descriptiveMetadataSection)
            ->validateHasOne()
            ->getFirstNode();

        $this->createNodeValidator($mdWrap)
            ->validateHasAttributeWithValue('MDTYPE', array('MODS', 'TEIHDR'));

        $mdType = $mdWrap->getAttribute('MDTYPE');
        if ($mdType == 'TEIHDR' || $mdType == 'MODS') {
            $childNode = $mdType == 'TEIHDR' ? 'tei:teiHeader' : 'mods:mods';
            $this->createNodeListValidator('/mets:xmlData[' . $childNode . ']', $mdWrap)
                ->validateHasOne();
        }
    }
}
