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
use TYPO3\CMS\Extbase\Error\Error;

/**
 * The validator validates against the rules outlined in chapter 2.3 of the METS application profile 2.4.
 *
 * @package TYPO3
 * @subpackage dfg-viewer
 *
 * @access public
 */
class MusicalStructureValidator extends AbstractDomDocumentValidator
{
    public function isValidDocument(): void
    {
        // Validates against the rules of chapter "2.3.1 Musikalische Struktur – mets:structMap"
        $this->createNodeListValidator(VH::XPATH_MUSICAL_STRUCTURES)
            ->validateHasNoneOrOne();

        $this->validateStructuralElement();
    }

    /**
     *
     * Validates the structural element.
     *
     * Validates against the rules of chapter "2.3.2.1 Strukturelement – mets:div"
     *
     * @return void
     */
    protected function validateStructuralElement(): void
    {
        $node = $this->createNodeListValidator(VH::XPATH_MUSICAL_STRUCTURAL_ELEMENT)
            ->validateHasOne()
            ->getFirstNode();

        $this->createNodeValidator($node)
            ->validateHasUniqueId()
            ->validateHasAttributeValue('TYPE', ['measures']);

        $measureElements = $this->createNodeListValidator(VH::XPATH_MUSICAL_STRUCTURAL_MEASURE)
            ->validateHasAny()
            ->getNodeList();
        foreach ($measureElements as $measureElement) {
            $this->validateMeasureElement($measureElement);
        }
    }

    /**
     *
     * Validates the measure element.
     *
     * Validates against the rules of chapter "2.3.2.1 Strukturelement – mets:div"
     *
     * @return void
     */
    protected function validateMeasureElement(\DOMNode $measureElement): void
    {
        $this->createNodeValidator($measureElement)
            ->validateHasUniqueId()
            ->validateHasAttributeValue('TYPE', ['measure'])
            ->validateHasNumericAttribute('ORDER');

        $digitalRepresentations = $this->createNodeListValidator('mets:fptr', $measureElement)
            ->validateHasAny()
            ->getNodeList();

        foreach ($digitalRepresentations as $digitalRepresentation) {
            $this->validateMeasureDigitalRepresentation($digitalRepresentation);
        }
    }

    /**
     *
     * Validates the digital representation of measure.
     *
     * Validates against the rules of chapter "2.3.2.2 Verweis auf digitale Repräsentation – mets:div/mets:fptr"
     *
     * @return void
     */
    protected function validateMeasureDigitalRepresentation(\DOMNode $digitalRepresentation): void
    {
        $measureLinks = $this->createNodeListValidator('mets:area', $digitalRepresentation)
            ->validateHasAny()
            ->getNodeList();
        $measureLinkFileId = '';
        foreach ($measureLinks as $measureLink) {
            $this->validateMeasureLink($measureLink, $measureLinkFileId);
        }
    }

    /**
     *
     * Validates the measure link.
     *
     * Validates against the rules of chapter "2.3.2.3 Verweis auf Substrukturen – mets:div/mets:fptr/mets:area"
     *
     * @return void
     */
    protected function validateMeasureLink(\DOMNode $measureLink, string &$measureLinkFileId): void
    {
        $nodeValidator = $this->createNodeValidator($measureLink);
        $nodeValidator->validateHasReferenceToId("FILEID", VH::XPATH_FILE_SECTION_FILES);

        $fileId = $measureLink->getAttribute('FILEID');

        // validates file identifier measure link under digital representation
        if ( $measureLinkFileId === '' ) {
            $measureLinkFileId = $fileId;
        }
        if ( $measureLinkFileId !== $fileId ) {
            $this->result->addError(new Error('"FILEID" attribute value under "' . $measureLink->getNodePath() . '" can only refer to the same file within one "mets:fptr" element.', 1741860129));
        }

        $files = $this->xpath->query(VH::XPATH_FILE_SECTION_FILES . '[@ID="' . $fileId . '"]');
        if ($files->length > 0 && $this->createNodeValidator($files->item(0))->isElementType()) {
            $file = $files->item(0);
            // check if measure linked file is an image derivative
            if ($file->hasAttribute('MIMETYPE') && str_starts_with($file->getAttribute('MIMETYPE'), 'image')) {
                $nodeValidator->validateHasRegexAttribute('COORDS',VH::COORDS_REGEX);
                $nodeValidator->validateHasAttributeValue('SHAPE', ['RECT']);
            } else {
                // validate as MEI derivative
                $nodeValidator->validateHasAttribute('BEGIN');
                $nodeValidator->validateHasAttribute('END');
                $nodeValidator->validateHasAttributeValue('BETYPE', ['IDREF']);
            }
        }
    }
}
