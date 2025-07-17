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
 * The validator validates against the rules outlined in chapter 2.6 of the METS application profile 2.4.
 *
 * @package TYPO3
 * @subpackage dfg-viewer
 *
 * @access public
 */
class DescriptiveMetadataValidator extends AbstractDomDocumentValidator
{
    public function isValidDocument(): void
    {
        // Validates against the rules of chapter "2.5.1 Metadatensektion – mets:dmdSec"
        $descriptiveSections = $this->createNodeListValidator(VH::XPATH_DESCRIPTIVE_METADATA_SECTIONS)
            ->validateHasAny()
            ->getNodeList();
        foreach ($descriptiveSections as $descriptiveSection) {
            $this->validateDescriptiveMetadataSection($descriptiveSection);
        }

        // there must be one primary structural element
        $structureElement = $this->createNodeListValidator(VH::XPATH_LOGICAL_STRUCTURAL_ELEMENTS)
            ->validateHasOne()
            ->getFirstNode();

        $this->createNodeAttributeValidator($structureElement)
            ->validateReferenceToId('DMDID', VH::XPATH_DESCRIPTIVE_METADATA_SECTIONS);
    }

    /**
     * Validates the embedded metadata.
     *
     * Validates against the rules of chapter "2.6.2.1 Eingebettete Metadaten – mets:mdWrap"
     *
     * @return void
     */
    protected function validateDescriptiveMetadataSection(\DOMNode $descriptiveSection): void
    {
        $mdWrap = $this->createNodeListValidator('mets:mdWrap', $descriptiveSection)
            ->validateHasOne()
            ->getFirstNode();

        $nodeValidator = $this->createNodeAttributeValidator($mdWrap)
            ->validateValue('MDTYPE', ['MODS', 'TEIHDR']);

        if (!$mdWrap) {
            return;
        }

        $mdType = $nodeValidator->getDomElement()->getAttribute('MDTYPE');
        if ($mdType == 'TEIHDR' || $mdType == 'MODS') {
            $childNode = 'mods:mods';
            if ($mdType == 'TEIHDR') {
                $childNode = 'tei:teiHeader';
            }
            $this->createNodeListValidator('mets:xmlData[' . $childNode . ']', $mdWrap)
                ->validateHasOne();
        }
    }
}
