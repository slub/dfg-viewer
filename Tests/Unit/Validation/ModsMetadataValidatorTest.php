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
use Slub\Dfgviewer\Validation\ModsMetadataValidator;

class ModsMetadataValidatorTest extends AbstractDomDocumentValidatorTest
{
    const MODS_BASEPATH = '/mets:mets/mets:dmdSec/mets:mdWrap/mets:xmlData/mods:mods';

    /**
     * Test validation against the rules of chapter "2.1 Titel"
     *
     * @return void
     */
    public function testTitle(): void
    {
        $this->setAttributeValue(VH::XPATH_MODS_TITLEINFO, 'type', 'alternative');
        $this->hasErrorOne(VH::XPATH_MODS_TITLEINFO . '[not(@type)]');

        // validate title info
        $this->addChildNodeWithNamespace(self::MODS_BASEPATH, VH::NAMESPACE_MODS, 'mods:titleInfo');
        $this->setAttributeValue(VH::XPATH_MODS_TITLEINFO . '[@type="alternative"]', 'type', 'Test');
        $this->hasErrorAttributeWithValue(self::MODS_BASEPATH . '/mods:titleInfo[1]', 'type', 'Test');
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MODS_TITLEINFO, 'lang', 'Test');
        $this->hasErrorAttributeWithIso6392B(self::MODS_BASEPATH . '/mods:titleInfo', 'lang', 'Test');
        $this->resetDocument();

        // validate title info sub elements
        $this->removeNodes(VH::XPATH_MODS_TITLEINFO . '/mods:title');
        $this->hasErrorOne('mods:title', self::MODS_BASEPATH . '/mods:titleInfo');
    }

    /**
     * Test validation against the rules of chapter "2.2 Namen von Personen oder Körperschaften"
     *
     * @return void
     */
    public function testName(): void
    {
        // validate name
        $this->setAttributeValue(VH::XPATH_MODS_NAMES, 'type', 'Test');
        $this->hasErrorAttributeWithValue(self::MODS_BASEPATH . '/mods:name[1]', 'type', 'Test');
        $this->resetDocument();

        $this->removeAttribute(VH::XPATH_MODS_NAMES . '[@type="personal"]', 'valueURI');
        $this->hasErrorAttribute(self::MODS_BASEPATH . '/mods:name[1]', 'valueURI');
        $this->setAttributeValue(VH::XPATH_MODS_NAMES . '[@type="personal"]', 'valueURI', 'Test');
        $this->hasErrorUrlAttribute(self::MODS_BASEPATH . '/mods:name[1]', 'valueURI', 'Test');
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MODS_NAMES . '[@type="personal"]', 'authorityURI', 'Test');
        $this->hasErrorUrlAttribute(self::MODS_BASEPATH . '/mods:name[1]', 'authorityURI', 'Test');

        // validate name subelemets
    }

    /**
     * Test validation against the rules of chapter "2.3 Gattung/Genre"
     *
     * @return void
     */
    public function testGenre(): void
    {
        $this->checkUriAttributes(VH::XPATH_MODS_GENRES, self::MODS_BASEPATH . '/mods:genre');
    }

    /**
     * Test validation against the rules of chapter "2.4 Angaben zu Entstehung und Lebenszyklus"
     *
     * @return void
     */
    public function testOrigin(): void
    {

    }

    /**
     * Test validation against the rules of chapter "2.5 Sprache und Schrift"
     *
     * @return void
     */
    public function testLanguage(): void
    {
        $this->setAttributeValue(VH::XPATH_MODS_LANGUAGE . '/mods:languageTerm', 'type', 'Test');
        $this->hasErrorAttributeWithValue(self::MODS_BASEPATH . '/mods:language/mods:languageTerm', 'type', 'Test');
        $this->resetDocument();

        $this->setContentValue(VH::XPATH_MODS_LANGUAGE . '/mods:languageTerm', 'Test');
        $this->hasErrorIso6392BContent(self::MODS_BASEPATH . '/mods:language/mods:languageTerm', 'Test');
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MODS_LANGUAGE . '/mods:scriptTerm', 'type', 'Test');
        $this->hasErrorAttributeWithValue(self::MODS_BASEPATH . '/mods:language/mods:scriptTerm', 'type', 'Test');
        $this->resetDocument();

        $this->setContentValue(VH::XPATH_MODS_LANGUAGE . '/mods:scriptTerm', 'Test');
        $this->hasErrorIso15924Content( self::MODS_BASEPATH . '/mods:language/mods:scriptTerm', 'Test');
    }

    /**
     * Test validation against the rules of chapter "2.6 Physische Beschreibung"
     *
     * @return void
     */
    public function testPhysicalDescription(): void
    {
        $this->addChildNodeWithNamespace(self::MODS_BASEPATH, VH::NAMESPACE_MODS, 'mods:physicalDescription');
        $this->hasErrorNoneOrOne(self::MODS_BASEPATH . '/mods:physicalDescription');
    }

    /**
     * Test validation against the rules of chapter "2.8 Anmerkungen"
     *
     * @return void
     */
    public function testNotes(): void
    {
        $this->removeAttribute(VH::XPATH_MODS . '/mods:note', 'type');
        $this->hasErrorAttribute(self::MODS_BASEPATH . '/mods:note', 'type');
    }

    /**
     * Test validation against the rules of chapter "2.9 Schlagwörter"
     *
     * @return void
     */
    public function testSubjects(): void
    {

    }

    /**
     * Test validation against the rules of chapter "2.10 Klassifikationen"
     *
     * @return void
     */
    public function testClassification(): void
    {
        $this->checkUriAttributes(VH::XPATH_MODS_CLASSIFICATION, self::MODS_BASEPATH . '/mods:classification');
    }

    protected function checkUriAttributes(string $expression, string $expectedExpression): void
    {
        $this->setAttributeValue($expression, 'authorityURI', 'Test');
        $this->hasErrorUrlAttribute($expectedExpression, 'authorityURI', 'Test');
        $this->resetDocument();

        $this->setAttributeValue($expression, 'valueURI', 'Test');
        $this->hasErrorUrlAttribute($expectedExpression, 'valueURI', 'Test');
    }

    protected function createValidator(): AbstractDlfValidator
    {
        return new ModsMetadataValidator();
    }
}
