<?php

namespace Slub\Dfgviewer\Tests\Unit\Validation;

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

use Kitodo\Dlf\Validation\AbstractDlfValidator;
use Slub\Dfgviewer\Common\ValidationHelper as VH;
use Slub\Dfgviewer\Validation\Mets\MusicalStructureValidator;

class MusicalStructureValidatorTest extends AbstractDomDocumentValidatorTest
{
    /**
     * Test validation against the rules of chapter "2.3.1 Musical structure - mets:structMap"
     *
     * @return void
     * @throws \DOMException
     */
    public function testMultipleMusicalDivisions(): void
    {
        $node = $this->doc->createElementNS(VH::NAMESPACE_METS, 'mets:structMap');
        $node->setAttribute('TYPE', 'MUSICAL');
        $this->addChildNode('/mets:mets', $node);
        $this->hasErrorNoneOrOne(VH::XPATH_MUSICAL_STRUCTURES);
    }

    /**
     * Test validation against the rules of chapter "2.3.2.1 Structural element - mets:div"
     *
     * @return void
     * @throws \DOMException
     */
    public function testStructuralElement(): void
    {
        $this->removeNodes(VH::XPATH_MUSICAL_STRUCTURAL_ELEMENT);
        $this->hasErrorOne(VH::XPATH_MUSICAL_STRUCTURAL_ELEMENT);
        $this->resetDocument();

        $this->removeAttribute(VH::XPATH_MUSICAL_STRUCTURAL_ELEMENT, 'ID');
        $this->hasErrorAttribute('/mets:mets/mets:structMap[3]/mets:div', 'ID');
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MUSICAL_STRUCTURAL_ELEMENT, 'ID', 'MUS_0001');
        $this->hasErrorUniqueId('/mets:mets/mets:structMap[3]/mets:div', 'MUS_0001');
        $this->resetDocument();

        $this->removeAttribute(VH::XPATH_MUSICAL_STRUCTURAL_ELEMENT, 'TYPE');
        $this->hasErrorAttribute('/mets:mets/mets:structMap[3]/mets:div', 'TYPE');

        $this->setAttributeValue(VH::XPATH_MUSICAL_STRUCTURAL_ELEMENT, 'TYPE', 'Test');
        $this->hasErrorAttributeWithValue('/mets:mets/mets:structMap[3]/mets:div', 'TYPE', 'Test');
        $this->resetDocument();
    }

    /**
     * Test validation against the rules of chapter "2.3.2.1 Structural element - mets:div"
     *
     * @return void
     * @throws \DOMException
     */
    public function testMeasureElement(): void
    {
        $this->removeNodes(VH::XPATH_MUSICAL_STRUCTURAL_MEASURE);
        $this->hasErrorAny(VH::XPATH_MUSICAL_STRUCTURAL_MEASURE);
        $this->resetDocument();

        $this->removeAttribute(VH::XPATH_MUSICAL_STRUCTURAL_ELEMENT, 'ID');
        $this->hasErrorAttribute('/mets:mets/mets:structMap[3]/mets:div', 'ID');
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MUSICAL_STRUCTURAL_ELEMENT, 'ID', 'MUS_0000');
        $this->hasErrorUniqueId('/mets:mets/mets:structMap[3]/mets:div', 'MUS_0000');
        $this->resetDocument();

        $this->removeAttribute(VH::XPATH_MUSICAL_STRUCTURAL_MEASURE, 'TYPE');
        $this->hasErrorAttribute('/mets:mets/mets:structMap[3]/mets:div/mets:div[1]', 'TYPE');
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MUSICAL_STRUCTURAL_MEASURE, 'ORDER', 'MUS_0000');
        $this->hasErrorNumericAttribute('/mets:mets/mets:structMap[3]/mets:div/mets:div[1]', 'ORDER', 'MUS_0000');
    }

    /**
     * Test validation against the rules of chapter "2.3.2.2 Verweis auf digitale Repräsentation – mets:div/mets:fptr"
     *
     * @return void
     * @throws \DOMException
     */
    public function testMeasureDigitalRepresentation(): void
    {
        $this->removeNodes(VH::XPATH_MUSICAL_STRUCTURAL_MEASURE . '/mets:fptr');
        $this->hasErrorAny('mets:fptr', '/mets:mets/mets:structMap[3]/mets:div/mets:div[1]');
        $this->resetDocument();
    }

    /**
     * Test validation against the rules of chapter "2.3.2.3 Verweis auf Substrukturen – mets:div/mets:fptr/mets:area"
     *
     * @return void
     * @throws \DOMException
     */
    public function testMeasureLink(): void
    {
        $this->removeNodes(VH::XPATH_MUSICAL_STRUCTURAL_MEASURE . '/mets:fptr/mets:area');
        $this->hasErrorAny('mets:area', '/mets:mets/mets:structMap[3]/mets:div/mets:div[1]/mets:fptr[1]');
        $this->resetDocument();

        $node = $this->doc->createElementNS(VH::NAMESPACE_METS, 'mets:area');
        $node->setAttribute('FILEID', 'FILE_0002_DEFAULT');
        $this->addChildNode(VH::XPATH_MUSICAL_STRUCTURAL_MEASURE . '/mets:fptr', $node);
        $this->validateAndAssertEquals('"FILEID" attribute value under "/mets:mets/mets:structMap[3]/mets:div/mets:div[4]/mets:fptr[2]/mets:area[2]" can only refer to the same file within one "mets:fptr" element.');
        $this->resetDocument();

        // test MEI notation
        $this->removeAttribute(VH::XPATH_MUSICAL_STRUCTURAL_MEASURE . '/mets:fptr/mets:area', 'BEGIN');
        $this->hasErrorAttribute('/mets:mets/mets:structMap[3]/mets:div/mets:div[1]/mets:fptr[1]/mets:area', 'BEGIN');
        $this->resetDocument();

        $this->removeAttribute(VH::XPATH_MUSICAL_STRUCTURAL_MEASURE . '/mets:fptr/mets:area', 'END');
        $this->hasErrorAttribute('/mets:mets/mets:structMap[3]/mets:div/mets:div[1]/mets:fptr[1]/mets:area', 'END');
        $this->resetDocument();

        $this->removeAttribute(VH::XPATH_MUSICAL_STRUCTURAL_MEASURE . '/mets:fptr/mets:area', 'BETYPE');
        $this->hasErrorAttribute('/mets:mets/mets:structMap[3]/mets:div/mets:div[1]/mets:fptr[1]/mets:area', 'BETYPE');
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MUSICAL_STRUCTURAL_MEASURE . '/mets:fptr/mets:area', 'BETYPE', 'Test');
        $this->hasErrorAttributeWithValue('/mets:mets/mets:structMap[3]/mets:div/mets:div[1]/mets:fptr[1]/mets:area', 'BETYPE', 'Test');

        // change mimetype of MEI and test image derivative handling
        $this->setAttributeValue(VH::XPATH_FILE_SECTION_FILES . '[@ID="FILE_0001_SCORE"]', 'MIMETYPE', 'image/jpeg');
        $this->hasErrorAttribute('/mets:mets/mets:structMap[3]/mets:div/mets:div[1]/mets:fptr[1]/mets:area', 'COORDS');
        $this->resetDocument();

        $this->removeAttribute(VH::XPATH_MUSICAL_STRUCTURAL_MEASURE . '/mets:fptr/mets:area', 'COORDS');
        $this->hasErrorAttribute('/mets:mets/mets:structMap[3]/mets:div/mets:div[1]/mets:fptr[2]/mets:area', 'COORDS');
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MUSICAL_STRUCTURAL_MEASURE . '/mets:fptr/mets:area', 'COORDS', 'Test');
        $this->hasErrorRegexAttribute('/mets:mets/mets:structMap[3]/mets:div/mets:div[1]/mets:fptr[2]/mets:area', 'COORDS',  'Test', VH::COORDS_REGEX);
        $this->resetDocument();

        $this->removeAttribute(VH::XPATH_MUSICAL_STRUCTURAL_MEASURE . '/mets:fptr/mets:area', 'SHAPE');
        $this->hasErrorAttribute('/mets:mets/mets:structMap[3]/mets:div/mets:div[1]/mets:fptr[2]/mets:area', 'SHAPE');
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MUSICAL_STRUCTURAL_MEASURE . '/mets:fptr/mets:area', 'SHAPE', 'Test');
        $this->hasErrorAttributeWithValue('/mets:mets/mets:structMap[3]/mets:div/mets:div[1]/mets:fptr[2]/mets:area', 'SHAPE', 'Test');
    }

    protected function createValidator(): AbstractDlfValidator
    {
        return new MusicalStructureValidator();
    }
}
