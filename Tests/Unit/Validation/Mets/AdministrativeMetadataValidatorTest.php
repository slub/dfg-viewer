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
use Slub\Dfgviewer\Validation\Mets\AdministrativeMetadataValidator;

class AdministrativeMetadataValidatorTest extends ApplicationProfileValidatorTest
{

    /**
     * Test validation against the rules of chapter "2.6.1 Metadatensektion – mets:amdSec"
     *
     * @return void
     */
    public function testAdministrativeMetadata(): void
    {
        $this->removeNodes(AdministrativeMetadataValidator::XPATH_ADMINISTRATIVE_METADATA);
        $this->assertErrorHasAny(AdministrativeMetadataValidator::XPATH_ADMINISTRATIVE_METADATA);
        $this->resetDocument();

        $this->removeNodes(AdministrativeMetadataValidator::XPATH_ADMINISTRATIVE_METADATA . '/mets:rightsMD');
        $this->assertErrorHasOne(AdministrativeMetadataValidator::XPATH_ADMINISTRATIVE_METADATA . '[mets:rightsMD and mets:digiprovMD]');
        $this->resetDocument();

        $this->setAttributeValue(AdministrativeMetadataValidator::XPATH_ADMINISTRATIVE_METADATA, 'ID', 'DMDLOG_0001');
        $this->assertErrorHasUniqueId('/mets:mets/mets:amdSec', 'DMDLOG_0001');

        $this->removeAttribute(AdministrativeMetadataValidator::XPATH_ADMINISTRATIVE_METADATA, 'ID');
        $this->assertErrorHasAttribute('/mets:mets/mets:amdSec', 'ID');
    }

    /**
     * Test validation against the rules of chapters "2.6.2.5 Herstellung – mets:digiprovMD" and "2.6.2.6 Eingebettete Verweise – mets:digiprovMD/mets:mdWrap"
     *
     * @return void
     */
    public function testDigitalProvenanceMetadataStructure(): void
    {
        $this->setAttributeValue(AdministrativeMetadataValidator::XPATH_DIGIPROV_METADATA, 'ID', 'DMDLOG_0001');
        $this->assertErrorHasUniqueId('/mets:mets/mets:amdSec/mets:digiprovMD', 'DMDLOG_0001');
        $this->resetDocument();

        $this->removeNodes(AdministrativeMetadataValidator::XPATH_DIGIPROV_METADATA . '/mets:mdWrap');
        $this->assertErrorHasOne('mets:mdWrap', '/mets:mets/mets:amdSec/mets:digiprovMD');
        $this->resetDocument();

        $this->removeAttribute(AdministrativeMetadataValidator::XPATH_DIGIPROV_METADATA . '/mets:mdWrap', 'MDTYPE');
        $this->assertErrorHasAttribute('/mets:mets/mets:amdSec/mets:digiprovMD/mets:mdWrap', 'MDTYPE');

        $this->setAttributeValue(AdministrativeMetadataValidator::XPATH_DIGIPROV_METADATA . '/mets:mdWrap', 'MDTYPE', 'Test');
        $this->assertErrorHasAttributeWithValue('/mets:mets/mets:amdSec/mets:digiprovMD/mets:mdWrap', 'MDTYPE', 'Test');
        $this->resetDocument();

        $this->removeAttribute(AdministrativeMetadataValidator::XPATH_DIGIPROV_METADATA . '/mets:mdWrap', 'OTHERMDTYPE');
        $this->assertErrorHasAttribute('/mets:mets/mets:amdSec/mets:digiprovMD/mets:mdWrap', 'OTHERMDTYPE');

        $this->setAttributeValue(AdministrativeMetadataValidator::XPATH_DIGIPROV_METADATA . '/mets:mdWrap', 'OTHERMDTYPE', 'Test');
        $this->assertErrorHasAttributeWithValue('/mets:mets/mets:amdSec/mets:digiprovMD/mets:mdWrap', 'OTHERMDTYPE', 'Test');
        $this->resetDocument();

        $this->removeNodes(AdministrativeMetadataValidator::XPATH_DIGIPROV_METADATA . '/mets:mdWrap/mets:xmlData/dv:links');
        $this->assertErrorHasOne('mets:xmlData[dv:links]', '/mets:mets/mets:amdSec/mets:digiprovMD/mets:mdWrap');
    }


    /**
     * Test validation against the rules of chapters "2.6.2.4 Rechtedeklaration – mets:rightsMD" and "2.6.2.4 Eingebettete Rechteangaben – mets:rightsMD/mets:mdWrap"
     *
     * @return void
     */
    public function testRightsMetadataStructure(): void
    {
        $this->setAttributeValue(AdministrativeMetadataValidator::XPATH_RIGHTS_METADATA, 'ID', 'DMDLOG_0001');
        $this->assertErrorHasUniqueId('/mets:mets/mets:amdSec/mets:rightsMD', 'DMDLOG_0001');
        $this->resetDocument();

        $this->removeNodes(AdministrativeMetadataValidator::XPATH_RIGHTS_METADATA . '/mets:mdWrap');
        $this->assertErrorHasOne('mets:mdWrap', '/mets:mets/mets:amdSec/mets:rightsMD');
        $this->resetDocument();

        $this->removeAttribute(AdministrativeMetadataValidator::XPATH_RIGHTS_METADATA . '/mets:mdWrap', 'MDTYPE');
        $this->assertErrorHasAttribute('/mets:mets/mets:amdSec/mets:rightsMD/mets:mdWrap', 'MDTYPE');

        $this->setAttributeValue(AdministrativeMetadataValidator::XPATH_RIGHTS_METADATA . '/mets:mdWrap', 'MDTYPE', 'Test');
        $this->assertErrorHasAttributeWithValue('/mets:mets/mets:amdSec/mets:rightsMD/mets:mdWrap', 'MDTYPE', 'Test');
        $this->resetDocument();

        $this->removeAttribute(AdministrativeMetadataValidator::XPATH_RIGHTS_METADATA . '/mets:mdWrap', 'OTHERMDTYPE');
        $this->assertErrorHasAttribute('/mets:mets/mets:amdSec/mets:rightsMD/mets:mdWrap', 'OTHERMDTYPE');

        $this->setAttributeValue(AdministrativeMetadataValidator::XPATH_RIGHTS_METADATA . '/mets:mdWrap', 'OTHERMDTYPE', 'Test');
        $this->assertErrorHasAttributeWithValue('/mets:mets/mets:amdSec/mets:rightsMD/mets:mdWrap', 'OTHERMDTYPE', 'Test');
        $this->resetDocument();

        $this->removeNodes(AdministrativeMetadataValidator::XPATH_RIGHTS_METADATA . '/mets:mdWrap/mets:xmlData/dv:rights');
        $this->assertErrorHasOne('mets:xmlData[dv:rights]', '/mets:mets/mets:amdSec/mets:rightsMD/mets:mdWrap');
    }

    /**
     * Test validation against the rules of chapters "2.6.2.1 Technische Metadaten – mets:techMD" and "2.6.2.2 Eingebettete technische Daten – mets:techMD/mets:mdWrap"
     *
     * @return void
     */
    public function testTechnicalMetadataStructure(): void
    {
        $this->addChildNodeNS(AdministrativeMetadataValidator::XPATH_ADMINISTRATIVE_METADATA, self::NAMESPACE_METS, 'mets:techMD');
        $this->assertErrorHasAttribute('/mets:mets/mets:amdSec/mets:techMD','ID');

        $this->setAttributeValue(AdministrativeMetadataValidator::XPATH_TECHNICAL_METADATA, 'ID', 'DMDLOG_0001');
        $this->assertErrorHasUniqueId('/mets:mets/mets:amdSec/mets:techMD','DMDLOG_0001');

        $this->setAttributeValue(AdministrativeMetadataValidator::XPATH_TECHNICAL_METADATA, 'ID', 'TECH_0001');
        $this->assertErrorHasOne('mets:mdWrap','/mets:mets/mets:amdSec/mets:techMD');

        $this->addChildNodeNS(AdministrativeMetadataValidator::XPATH_TECHNICAL_METADATA, self::NAMESPACE_METS, 'mets:mdWrap');
        $this->assertErrorHasAttribute('/mets:mets/mets:amdSec/mets:techMD/mets:mdWrap','MDTYPE');

        $this->setAttributeValue(AdministrativeMetadataValidator::XPATH_TECHNICAL_METADATA . '/mets:mdWrap', 'MDTYPE', '');
        $this->assertErrorHasAttribute('/mets:mets/mets:amdSec/mets:techMD/mets:mdWrap','OTHERMDTYPE');

        $this->setAttributeValue(AdministrativeMetadataValidator::XPATH_TECHNICAL_METADATA . '/mets:mdWrap', 'OTHERMDTYPE', '');
        $this->assertErrorHasOne('mets:xmlData','/mets:mets/mets:amdSec/mets:techMD/mets:mdWrap');
    }


    protected function createValidator(): AbstractDlfValidator
    {
        return new AdministrativeMetadataValidator();
    }
}
