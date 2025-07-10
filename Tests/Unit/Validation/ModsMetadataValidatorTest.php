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

        // validate name subelements
        // check mods:namePart
        $this->removeNodes(VH::XPATH_MODS_NAMES . '/mods:namePart');
        $this->hasMessageAny('mods:namePart', self::MODS_BASEPATH . '/mods:name[1]');
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MODS_NAMES . '[@type="personal"]/mods:namePart', 'type', 'Test');
        $this->hasMessageAttributeWithValue(self::MODS_BASEPATH . '/mods:name[1]/mods:namePart[1]', 'type', 'Test');
        $this->setAttributeValue(VH::XPATH_MODS_NAMES . '[@type="personal"]/mods:namePart', 'type', 'family');
        $this->hasMessageOne('mods:namePart[@type="family"]', self::MODS_BASEPATH . '/mods:name[1]');
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MODS_NAMES . '[@type="corporate"]/mods:namePart', 'type', 'Test');
        $this->hasMessageNoneAttribute(self::MODS_BASEPATH . '/mods:name[2]/mods:namePart', 'type');
        $this->resetDocument();

        // check mods:displayForm
        $this->addChildNodeWithNamespace(VH::XPATH_MODS_NAMES . '[@type="personal"]', VH::NAMESPACE_MODS, 'mods:displayForm');
        $this->hasMessageNoneOrOne('mods:displayForm', self::MODS_BASEPATH . '/mods:name[1]');
        $this->resetDocument();

        // check mods:role
        $this->removeNodes(VH::XPATH_MODS_NAMES . '/mods:role');
        $this->hasMessageAny('mods:role', self::MODS_BASEPATH . '/mods:name[1]');
        $this->resetDocument();

        // checko mods:role/mods:roleTerm
        $this->removeNodes(VH::XPATH_MODS_NAMES . '/mods:role/mods:roleTerm');
        $this->hasMessageAny('mods:roleTerm', self::MODS_BASEPATH . '/mods:name[1]/mods:role');
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MODS_NAMES . '/mods:role/mods:roleTerm', 'type', 'Test');
        $this->hasMessageOne('mods:roleTerm[@type="code"]', self::MODS_BASEPATH . '/mods:name[1]/mods:role');
        $this->resetDocument();

        $this->addChildNodeWithNamespace(VH::XPATH_MODS_NAMES . '[@type="personal"]/mods:role', VH::NAMESPACE_MODS, 'mods:roleTerm');
        $this->setAttributeValue(VH::XPATH_MODS_NAMES . '[@type="personal"]/mods:role/mods:roleTerm[not(@type)]', 'type', 'Test');
        $this->hasMessageAttributeWithValue(self::MODS_BASEPATH . '/mods:name[1]/mods:role/mods:roleTerm[2]', 'type', 'Test');
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MODS_NAMES . '/mods:role/mods:roleTerm[@type="code"]', 'authority', 'Test');
        $this->hasMessageAttributeWithValue(self::MODS_BASEPATH . '/mods:name[1]/mods:role/mods:roleTerm', 'authority', 'Test',SeverityLevel::NOTICE);
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MODS_NAMES . '/mods:role/mods:roleTerm[@type="code"]', 'authorityURI', 'Test');
        $this->hasMessageAttributeWithValue(self::MODS_BASEPATH . '/mods:name[1]/mods:role/mods:roleTerm', 'authorityURI', 'Test',SeverityLevel::NOTICE);
        $this->resetDocument();

        $this->checkUriAttributes(VH::XPATH_MODS_NAMES . '/mods:role/mods:roleTerm', self::MODS_BASEPATH . '/mods:name[1]/mods:role/mods:roleTerm');
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
        $this->removeAttribute(VH::XPATH_MODS_ORIGININFO,'eventType');
        $this->hasMessageAttribute(self::MODS_BASEPATH . '/mods:originInfo', 'eventType');
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MODS_ORIGININFO,'eventType', 'Test');
        $this->addChildNodeWithNamespace(self::MODS_BASEPATH, VH::NAMESPACE_MODS, 'mods:originInfo');
        $this->setAttributeValue(VH::XPATH_MODS_ORIGININFO . '[not(@eventType)]','eventType', 'Test');
        $this->hasMessageOne('mods:originInfo[@eventType="Test"]', self::MODS_BASEPATH);
        $this->resetDocument();

        // check mods:place
        $this->removeNodes(VH::XPATH_MODS_ORIGININFO . '/mods:place/mods:placeTerm');
        $this->hasMessageAny('mods:placeTerm', self::MODS_BASEPATH  . '/mods:originInfo/mods:place');
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MODS_ORIGININFO . '/mods:place/mods:placeTerm','type', 'Test');
        $this->hasMessageAttributeWithValue(self::MODS_BASEPATH . '/mods:originInfo/mods:place/mods:placeTerm', 'type', 'Test');
        $this->resetDocument();

        // check mods:agent
        // TODO validate ref name

        // check dates
        $this->setAttributeValue(VH::XPATH_MODS_ORIGININFO . '/mods:dateIssued','qualifier', 'Test');
        $this->hasMessageAttributeWithValue(self::MODS_BASEPATH . '/mods:originInfo/mods:dateIssued', 'qualifier', 'Test');
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MODS_ORIGININFO . '/mods:dateCreated','point', 'Test');
        $this->hasMessageAttributeWithValue(self::MODS_BASEPATH . '/mods:originInfo/mods:dateCreated[1]', 'point', 'Test');
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MODS_ORIGININFO . '/mods:dateCreated','keyDate', 'Test');
        $this->hasMessageAttributeWithValue(self::MODS_BASEPATH . '/mods:originInfo/mods:dateCreated[1]', 'keyDate', 'Test');
        $this->setAttributeValue(VH::XPATH_MODS_ORIGININFO . '/mods:dateCreated','keyDate', 'yes');
        $this->removeAttribute(VH::XPATH_MODS_ORIGININFO . '/mods:dateCreated','encoding');
        $this->hasMessageAttribute(self::MODS_BASEPATH . '/mods:originInfo/mods:dateCreated[1]', 'encoding');
        $this->setAttributeValue(self::MODS_BASEPATH . '/mods:originInfo/mods:dateCreated[1]','encoding', 'Test');
        $this->hasMessageAttributeWithValue(self::MODS_BASEPATH . '/mods:originInfo/mods:dateCreated[1]', 'encoding', 'Test');
        $this->resetDocument();

        // check duplicate key date
        $this->setAttributeValue(VH::XPATH_MODS_ORIGININFO . '/mods:dateCreated','keyDate', 'yes');
        $this->setAttributeValue(VH::XPATH_MODS_ORIGININFO . '/mods:dateIssued','keyDate', 'yes');
        $this->hasMessageNoneOrOne('mods:dateIssued[@keyDate="yes"] | mods:dateCreated[@keyDate="yes"] | mods:dateValid[@keyDate="yes"] | mods:dateOther[@keyDate="yes"]', self::MODS_BASEPATH . '/mods:originInfo');
        $this->resetDocument();

        // check mods:edition
        $this->addChildNodeWithNamespace(self::MODS_BASEPATH . '/mods:originInfo', VH::NAMESPACE_MODS, 'mods:edition');
        $this->hasMessageNoneOrOne('mods:edition', self::MODS_BASEPATH . '/mods:originInfo');
    }

    /**
     * Test validation against the rules of chapter "2.5 Sprache und Schrift"
     *
     * @return void
     */
    public function testLanguage(): void
    {
        $this->removeNodes(VH::XPATH_MODS_LANGUAGE . '/mods:languageTerm');
        $this->hasMessageAny('mods:languageTerm', self::MODS_BASEPATH . '/mods:language');
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
        $this->hasMessageIso15924Content(self::MODS_BASEPATH . '/mods:language/mods:scriptTerm', 'Test', SeverityLevel::NOTICE);
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
        $this->checkUriAttributes(VH::XPATH_MODS_SUBJECT, self::MODS_BASEPATH . '/mods:subject[1]', ['authorityURI']);

        // check subelements
        $this->checkUriAttributes(VH::XPATH_MODS_SUBJECT . '/mods:topic', self::MODS_BASEPATH . '/mods:subject[1]/mods:topic', ['valueURI']);

        // TODO validate ref title info
        $this->setAttributeValue(VH::XPATH_MODS_SUBJECT . '/mods:titleInfo', 'nameTitleGroup', '0');
        $this->hasMessageOne('mods:name[@nameTitleGroup="0"]', self::MODS_BASEPATH . '/mods:subject[2]', SeverityLevel::NOTICE);
        $this->resetDocument();

        // TODO validate ref name
        $this->setAttributeValue(VH::XPATH_MODS_SUBJECT . '/mods:name', 'nameTitleGroup', '0');
        $this->hasMessageOne('mods:titleInfo[@nameTitleGroup="0"]', self::MODS_BASEPATH . '/mods:subject[2]', SeverityLevel::NOTICE);
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
     * Test validation against the rules of chapter "2.11 Beziehungen zu anderen Ressourcen"
     *
     * @return void
     */
    public function testRelatedItem(): void
    {
        $this->setAttributeValue(VH::XPATH_MODS_RELATEDITEM, 'type', 'Test');
        $this->hasMessageAttributeWithValue(self::MODS_BASEPATH . '/mods:relatedItem', 'type', 'Test');
        $this->resetDocument();

        $this->removeNodes(VH::XPATH_MODS_RELATEDITEM . '/mods:titleInfo');
        $this->hasMessageAny('mods:titleInfo', self::MODS_BASEPATH . '/mods:relatedItem');
        $this->resetDocument();

        // TODO validate mods:titleInfo reference

        // check mods:part
        $this->addChildNodeWithNamespace(VH::XPATH_MODS_RELATEDITEM, VH::NAMESPACE_MODS, 'mods:part');
        $this->hasMessageNoneOrOne('mods:part', self::MODS_BASEPATH . '/mods:relatedItem');
        $this->resetDocument();

        $this->removeNodes(VH::XPATH_MODS_RELATEDITEM . '/mods:part/mods:detail');
        $this->hasMessageAny("mods:detail", self::MODS_BASEPATH . '/mods:relatedItem/mods:part');
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MODS_RELATEDITEM . '/mods:part/mods:detail', 'type', 'Test');
        $this->hasMessageAttributeWithValue(self::MODS_BASEPATH . '/mods:relatedItem/mods:part/mods:detail', 'type', 'Test');
        $this->resetDocument();

        $this->addChildNodeWithNamespace(VH::XPATH_MODS_RELATEDITEM . '/mods:part', VH::NAMESPACE_MODS, 'mods:detail');
        $this->setAttributeValue(VH::XPATH_MODS_RELATEDITEM . '/mods:part/mods:detail', 'type', 'volume');
        $this->hasMessageOne('mods:detail[@type="volume"]', self::MODS_BASEPATH . '/mods:relatedItem/mods:part');
        $this->resetDocument();

        $this->removeNodes(VH::XPATH_MODS_RELATEDITEM . '/mods:part/mods:detail/mods:number');
        $this->hasMessageOne("mods:number", self::MODS_BASEPATH . '/mods:relatedItem/mods:part/mods:detail');
        $this->resetDocument();

        // check mods:recordInfo
        $this->addChildNodeWithNamespace(VH::XPATH_MODS_RELATEDITEM, VH::NAMESPACE_MODS, 'mods:recordInfo');
        $this->addChildNodeWithNamespace(VH::XPATH_MODS_RELATEDITEM, VH::NAMESPACE_MODS, 'mods:recordInfo');
        $this->hasMessageNoneOrOne('mods:recordInfo', self::MODS_BASEPATH . '/mods:relatedItem');
        $this->resetDocument();

        $this->addChildNodeWithNamespace(VH::XPATH_MODS_RELATEDITEM, VH::NAMESPACE_MODS, 'mods:recordInfo');
        $this->hasMessageOne('mods:recordIdentifier', self::MODS_BASEPATH . '/mods:relatedItem/mods:recordInfo');
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

        $this->setAttributeValue(VH::XPATH_MODS_LOCATION . '/mods:url', 'access', 'Test');
        $this->hasMessageAttributeWithValue(self::MODS_BASEPATH . '/mods:location/mods:url', 'access', 'Test', SeverityLevel::NOTICE);
        $this->resetDocument();

        $this->addChildNodeWithNamespace(VH::XPATH_MODS_LOCATION, VH::NAMESPACE_MODS, 'mods:shelfLocator');
        $this->hasMessageNoneOrOne('mods:shelfLocator', self::MODS_BASEPATH . '/mods:location');
    }

    /**
     * Test validation against the rules of chapter "2.15 Angabe von Bänden und anderen Teilen"
     *
     * @return void
     */
    public function testPart(): void
    {
        $this->addChildNodeWithNamespace(VH::XPATH_MODS, VH::NAMESPACE_MODS, 'mods:part');
        $this->addChildNodeWithNamespace(VH::XPATH_MODS, VH::NAMESPACE_MODS, 'mods:part');
        $this->hasMessageNoneOrOne('//mods:mods/mods:part');
        $this->resetDocument();

        $this->removeNodes(VH::XPATH_MODS_PART);
        $this->setAttributeValue(VH::XPATH_MODS_RELATEDITEM, 'type', 'host');
        $this->hasMessageOne('//mods:mods/mods:part');
        $this->resetDocument();

        $this->removeAttribute(VH::XPATH_MODS_PART, 'order');
        $this->hasMessageAttribute(self::MODS_BASEPATH . '/mods:part', 'order');
        $this->setAttributeValue(VH::XPATH_MODS_PART, 'order', '-1');
        $this->validateAndAssertEquals('Value "-1" in the "order" attribute of "' . self::MODS_BASEPATH . '/mods:part" is not a positiv integer.');
        $this->resetDocument();

        // check mods:detail
        $this->removeNodes(VH::XPATH_MODS_PART .  '/mods:detail');
        $this->hasMessageAny('mods:detail', self::MODS_BASEPATH . '/mods:part');
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MODS_PART.  '/mods:detail', 'type', 'Test');
        $this->hasMessageAttributeWithValue(self::MODS_BASEPATH . '/mods:part/mods:detail', 'type', 'Test');
        $this->resetDocument();

        $this->removeNodes(VH::XPATH_MODS_PART .  '/mods:detail/mods:number');
        $this->hasMessageOne('mods:number', self::MODS_BASEPATH . '/mods:part/mods:detail');
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

    protected function checkUriAttributes(string $expression, string $expectedExpression, array $attributes=['authorityURI', 'valueURI']): void
    {
        foreach ($attributes as $attribute) {
            $this->setAttributeValue($expression, $attribute, 'Test');
            $this->hasMessageUrlAttribute($expectedExpression, $attribute, 'Test');
            $this->resetDocument();
        }
    }

    protected function createValidator(): AbstractDlfValidator
    {
        return new ModsMetadataValidator();
    }
}
