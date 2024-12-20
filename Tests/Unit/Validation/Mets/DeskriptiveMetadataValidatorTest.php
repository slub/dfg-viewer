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
use Slub\Dfgviewer\Validation\Mets\DeskriptiveMetadataValidator;
use Slub\Dfgviewer\Validation\Mets\LogicalStructureValidator;

class DeskriptiveMetadataValidatorTest extends ApplicationProfileValidatorTest
{

    /**
     * Test validation against the rules of chapter "2.5.1 Metadatensektion – mets:dmdSec"
     *
     * @return void
     */
    public function testDescriptiveMetadata(): void
    {
        $this->removeNodes(DeskriptiveMetadataValidator::XPATH_DESCRIPTIVE_METADATA_SECTIONS);
        $this->assertErrorHasAny(DeskriptiveMetadataValidator::XPATH_DESCRIPTIVE_METADATA_SECTIONS);
        $this->resetDocument();

        $this->removeNodes(LogicalStructureValidator::XPATH_STRUCTURAL_ELEMENTS);
        $this->assertErrorHasOne(LogicalStructureValidator::XPATH_STRUCTURAL_ELEMENTS);
        $this->resetDocument();

        $this->setAttributeValue(LogicalStructureValidator::XPATH_STRUCTURAL_ELEMENTS, 'DMDID', 'Test');
        $this->assertErrorHasRefToOne('/mets:mets/mets:structMap[1]/mets:div', 'DMDID', 'Test', DeskriptiveMetadataValidator::XPATH_DESCRIPTIVE_METADATA_SECTIONS);
    }

    /**
     * Test validation against the rules of chapter "2.5.2.1 Eingebettete Metadaten – mets:mdWrap"
     *
     * @return void
     */
    public function testEmbeddedMetadata(): void
    {
        $this->removeNodes(DeskriptiveMetadataValidator::XPATH_DESCRIPTIVE_METADATA_SECTIONS . '/mets:mdWrap');
        $this->assertErrorHasOne('mets:mdWrap', '/mets:mets/mets:dmdSec');
        $this->resetDocument();

        $this->setAttributeValue(DeskriptiveMetadataValidator::XPATH_DESCRIPTIVE_METADATA_SECTIONS . '/mets:mdWrap', 'MDTYPE', 'Test');
        $this->assertErrorHasAttributeWithValue('/mets:mets/mets:dmdSec/mets:mdWrap', 'MDTYPE', 'Test');
        $this->resetDocument();

        $this->removeNodes(DeskriptiveMetadataValidator::XPATH_DESCRIPTIVE_METADATA_SECTIONS . '/mets:mdWrap/mets:xmlData/mods:mods');
        $this->assertErrorHasOne('mets:xmlData[mods:mods]', '/mets:mets/mets:dmdSec/mets:mdWrap');
    }


    protected function createValidator(): AbstractDlfValidator
    {
        return new DeskriptiveMetadataValidator();
    }
}
