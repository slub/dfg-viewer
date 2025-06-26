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
use Slub\Dfgviewer\Validation\Common\SeverityLevel;
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
        // TODO ausschließen wenn host type exist
        $this->setAttributeValue(VH::XPATH_MODS_TITLEINFO, 'type', 'alternative');
        $this->hasMessageOne(VH::XPATH_MODS_TITLEINFO . '[not(@type)]');

        // validate title info
        // add empty title info to prevent error of not existing none type mods:titleInfos
        $this->addChildNodeWithNamespace(self::MODS_BASEPATH, VH::NAMESPACE_MODS, 'mods:titleInfo');
        $this->setAttributeValue(VH::XPATH_MODS_TITLEINFO . '[@type="alternative"]', 'type', 'Test');
        $this->hasMessageAttributeWithValue(self::MODS_BASEPATH . '/mods:titleInfo[1]', 'type', 'Test');
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MODS_TITLEINFO, 'lang', 'Test');
        $this->hasMessageAttributeWithIso6392B(self::MODS_BASEPATH . '/mods:titleInfo', 'lang', 'Test');
        $this->setAttributeValue(VH::XPATH_MODS_TITLEINFO, 'lang', 'eng');
        $this->hasNoMessage();
        $this->resetDocument();

        // validate title info sub elements
        $this->removeNodes(VH::XPATH_MODS_TITLEINFO . '/mods:title');
        $this->hasMessageOne('mods:title', self::MODS_BASEPATH . '/mods:titleInfo');
    }

    /**
     * Test validation against the rules of chapter "2.2 Namen von Personen oder Körperschaften"
     *
     * @return void
     */
    public function testNames(): void
    {
        // validate name
        $this->setAttributeValue(VH::XPATH_MODS_NAMES, 'type', 'Test');
        $this->hasMessageAttributeWithValue(self::MODS_BASEPATH . '/mods:name[1]', 'type', 'Test');
        $this->resetDocument();

        $this->removeAttribute(VH::XPATH_MODS_NAMES . '[@type="personal"]', 'valueURI');
        $this->hasMessageAttribute(self::MODS_BASEPATH . '/mods:name[1]', 'valueURI', SeverityLevel::NOTICE);
        $this->checkUriAttributes(VH::XPATH_MODS_NAMES . '[@type="personal"]', self::MODS_BASEPATH . '/mods:name[1]');

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
        $this->removeNodes(VH::XPATH_MODS_LANGUAGE . '/mods:languageTerm');
        $this->hasMessageAny( 'mods:languageTerm', self::MODS_BASEPATH . '/mods:language');
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MODS_LANGUAGE . '/mods:languageTerm', 'type', 'Test');
        $this->hasMessageAttributeWithValue(self::MODS_BASEPATH . '/mods:language/mods:languageTerm', 'type', 'Test');
        $this->resetDocument();

        $this->setContentValue(VH::XPATH_MODS_LANGUAGE . '/mods:languageTerm', 'Test');
        $this->hasMessageIso6392BContent(self::MODS_BASEPATH . '/mods:language/mods:languageTerm', 'Test', SeverityLevel::NOTICE);
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MODS_LANGUAGE . '/mods:scriptTerm', 'type', 'Test');
        $this->hasMessageAttributeWithValue(self::MODS_BASEPATH . '/mods:language/mods:scriptTerm', 'type', 'Test');
        $this->resetDocument();

        $this->setContentValue(VH::XPATH_MODS_LANGUAGE . '/mods:scriptTerm', 'Test');
        $this->hasMessageIso15924Content( self::MODS_BASEPATH . '/mods:language/mods:scriptTerm', 'Test', SeverityLevel::NOTICE);
    }

    /**
     * Test validation against the rules of chapter "2.6 Physische Beschreibung"
     *
     * @return void
     */
    public function testPhysicalDescription(): void
    {
        $this->addChildNodeWithNamespace(self::MODS_BASEPATH, VH::NAMESPACE_MODS, 'mods:physicalDescription');
        $this->addChildNodeWithNamespace(self::MODS_BASEPATH, VH::NAMESPACE_MODS, 'mods:physicalDescription');
        $this->hasMessageNoneOrOne('//mods:mods/mods:physicalDescription');
    }

    /**
     * Test validation against the rules of chapter "2.8 Anmerkungen"
     *
     * @return void
     */
    public function testNotes(): void
    {
        $this->removeAttribute(VH::XPATH_MODS_NOTE, 'type');
        $this->hasMessageAttribute(self::MODS_BASEPATH . '/mods:note', 'type', SeverityLevel::NOTICE);
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

    /**
     * Test validation against the rules of chapter "2.12 Identifier"
     *
     * @return void
     */
    public function testIdentifier(): void
    {
        $this->removeAttribute(VH::XPATH_MODS_IDENTIFIER, 'type');
        $this->hasMessageAttribute(self::MODS_BASEPATH . '/mods:identifier', 'type');
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MODS_IDENTIFIER, 'invalid', 'Test');
        $this->hasMessageAttributeWithValue(self::MODS_BASEPATH . '/mods:identifier', 'invalid', 'Test');
        $this->setAttributeValue(VH::XPATH_MODS_IDENTIFIER, 'invalid', 'yes');
        $this->hasNoMessage();
    }

    /**
     * Test validation against the rules of chapter "2.13 Zugang zur Ressource"
     *
     * @return void
     */
    public function testLocation(): void
    {
        $this->addChildNodeWithNamespace(VH::XPATH_MODS_LOCATION, VH::NAMESPACE_MODS, 'mods:physicalLocation');
        $this->hasMessageNoneOrOne('mods:physicalLocation', self::MODS_BASEPATH . '/mods:location');
        $this->resetDocument();

        $this->removeNodes(VH::XPATH_MODS_LOCATION . '/mods:physicalLocation');
        $this->removeNodes(VH::XPATH_MODS_LOCATION . '/mods:url');
        $this->hasMessageAny('mods:url | mods:physicalLocation', self::MODS_BASEPATH . '/mods:location');
        $this->resetDocument();

        $this->setAttributeValue( VH::XPATH_MODS_LOCATION . '/mods:url', 'access', 'Test');
        $this->hasMessageAttributeWithValue(self::MODS_BASEPATH . '/mods:location/mods:url', 'access', 'Test', SeverityLevel::NOTICE);
        $this->resetDocument();

        $this->addChildNodeWithNamespace(VH::XPATH_MODS_LOCATION, VH::NAMESPACE_MODS, 'mods:shelfLocator');
        $this->hasMessageNoneOrOne('mods:shelfLocator', self::MODS_BASEPATH . '/mods:location');
    }

    /**
     * Test validation against the rules of chapter "2.16 Informationen zum Metadatensatz"
     *
     * @return void
     */
    public function testRecordInfo(): void
    {
        $this->removeNodes(VH::XPATH_MODS_RECORDINFO);
        $this->hasMessageOne('//mods:mods/mods:recordInfo');
        $this->resetDocument();

        $this->removeNodes(VH::XPATH_MODS_RECORDINFO . '/mods:recordIdentifier');
        $this->hasMessageOne('mods:recordIdentifier',self::MODS_BASEPATH . '/mods:recordInfo');
        $this->resetDocument();

        $this->addChildNodeWithNamespace(VH::XPATH_MODS_RECORDINFO, VH::NAMESPACE_MODS, 'mods:descriptionStandard');
        $this->hasMessageNoneOrOne('mods:descriptionStandard',self::MODS_BASEPATH . '/mods:recordInfo');
    }

    protected function checkUriAttributes(string $expression, string $expectedExpression): void
    {
        $this->setAttributeValue($expression, 'authorityURI', 'Test');
        $this->hasMessageUrlAttribute($expectedExpression, 'authorityURI', 'Test');
        $this->resetDocument();

        $this->setAttributeValue($expression, 'valueURI', 'Test');
        $this->hasMessageUrlAttribute($expectedExpression, 'valueURI', 'Test');
    }

    protected function createValidator(): AbstractDlfValidator
    {
        return new ModsMetadataValidator();
    }
}
