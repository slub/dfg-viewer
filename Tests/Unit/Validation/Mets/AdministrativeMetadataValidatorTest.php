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
use Slub\Dfgviewer\Validation\Mets\AdministrativeMetadataValidator;

class AdministrativeMetadataValidatorTest extends AbstractDomDocumentValidatorTest
{
    /**
     * Test validation against the rules of chapter "2.7.1 Metadatensektion – mets:amdSec"
     *
     * @return void
     */
    public function testAdministrativeMetadata(): void
    {
        $this->removeNodes(VH::XPATH_ADMINISTRATIVE_METADATA);
        $this->hasErrorAny(VH::XPATH_ADMINISTRATIVE_METADATA);
        $this->resetDocument();

        $this->removeNodes(VH::XPATH_ADMINISTRATIVE_METADATA . '/mets:rightsMD');
        $this->hasErrorOne(VH::XPATH_ADMINISTRATIVE_METADATA . '[mets:rightsMD and mets:digiprovMD]');
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_ADMINISTRATIVE_METADATA, 'ID', 'DMDLOG_0001');
        $this->hasErrorUniqueId(VH::trimDoubleSlash(VH::XPATH_ADMINISTRATIVE_METADATA), 'DMDLOG_0001');

        $this->removeAttribute(VH::XPATH_ADMINISTRATIVE_METADATA, 'ID');
        $this->hasErrorAttribute(VH::trimDoubleSlash(VH::XPATH_ADMINISTRATIVE_METADATA), 'ID');
    }

    /**
     * Test validation against the rules of chapters "2.7.2.5 Herstellung – mets:digiprovMD" and "2.7.2.7 Eingebettete Verweise – mets:digiprovMD/mets:mdWrap"
     *
     * @return void
     */
    public function testDigitalProvenanceMetadataStructure(): void
    {
        $this->setAttributeValue(VH::XPATH_ADMINISTRATIVE_DIGIPROV_METADATA, 'ID', 'DMDLOG_0001');
        $this->hasErrorUniqueId(VH::trimDoubleSlash(VH::XPATH_ADMINISTRATIVE_DIGIPROV_METADATA), 'DMDLOG_0001');
        $this->resetDocument();

        $this->removeNodes(VH::XPATH_ADMINISTRATIVE_DIGIPROV_METADATA . '/mets:mdWrap');
        $this->hasErrorOne('mets:mdWrap', VH::trimDoubleSlash(VH::XPATH_ADMINISTRATIVE_DIGIPROV_METADATA));
        $this->resetDocument();

        $this->removeAttribute(VH::XPATH_ADMINISTRATIVE_DIGIPROV_METADATA . '/mets:mdWrap', 'MDTYPE');
        $this->hasErrorAttribute(VH::trimDoubleSlash(VH::XPATH_ADMINISTRATIVE_DIGIPROV_METADATA) . '/mets:mdWrap', 'MDTYPE');

        $this->setAttributeValue(VH::XPATH_ADMINISTRATIVE_DIGIPROV_METADATA . '/mets:mdWrap', 'MDTYPE', 'Test');
        $this->hasErrorAttributeWithValue(VH::trimDoubleSlash(VH::XPATH_ADMINISTRATIVE_DIGIPROV_METADATA) . '/mets:mdWrap', 'MDTYPE', 'Test');
        $this->resetDocument();

        $this->removeAttribute(VH::XPATH_ADMINISTRATIVE_DIGIPROV_METADATA . '/mets:mdWrap', 'OTHERMDTYPE');
        $this->hasErrorAttribute(VH::trimDoubleSlash(VH::XPATH_ADMINISTRATIVE_DIGIPROV_METADATA) . '/mets:mdWrap', 'OTHERMDTYPE');

        $this->setAttributeValue(VH::XPATH_ADMINISTRATIVE_DIGIPROV_METADATA . '/mets:mdWrap', 'OTHERMDTYPE', 'Test');
        $this->hasErrorAttributeWithValue(VH::trimDoubleSlash(VH::XPATH_ADMINISTRATIVE_DIGIPROV_METADATA) . '/mets:mdWrap', 'OTHERMDTYPE', 'Test');
        $this->resetDocument();

        $this->removeNodes(VH::XPATH_ADMINISTRATIVE_DIGIPROV_METADATA . '/mets:mdWrap/mets:xmlData/dv:links');
        $this->hasErrorOne('mets:xmlData[dv:links]', VH::trimDoubleSlash(VH::XPATH_ADMINISTRATIVE_DIGIPROV_METADATA) . '/mets:mdWrap');
    }

    /**
     * Test validation against the rules of chapters "2.7.2.4 Rechtedeklaration – mets:rightsMD" and "2.7.2.4 Eingebettete Rechteangaben – mets:rightsMD/mets:mdWrap"
     *
     * @return void
     */
    public function testRightsMetadataStructure(): void
    {
        $this->setAttributeValue(VH::XPATH_ADMINISTRATIVE_RIGHTS_METADATA, 'ID', 'DMDLOG_0001');
        $this->hasErrorUniqueId(VH::trimDoubleSlash(VH::XPATH_ADMINISTRATIVE_RIGHTS_METADATA), 'DMDLOG_0001');
        $this->resetDocument();

        $this->removeNodes(VH::XPATH_ADMINISTRATIVE_RIGHTS_METADATA . '/mets:mdWrap');
        $this->hasErrorOne('mets:mdWrap', VH::trimDoubleSlash(VH::XPATH_ADMINISTRATIVE_RIGHTS_METADATA));
        $this->resetDocument();

        $this->removeAttribute(VH::XPATH_ADMINISTRATIVE_RIGHTS_METADATA . '/mets:mdWrap', 'MDTYPE');
        $this->hasErrorAttribute(VH::trimDoubleSlash(VH::XPATH_ADMINISTRATIVE_RIGHTS_METADATA) . '/mets:mdWrap', 'MDTYPE');

        $this->setAttributeValue(VH::XPATH_ADMINISTRATIVE_RIGHTS_METADATA . '/mets:mdWrap', 'MDTYPE', 'Test');
        $this->hasErrorAttributeWithValue(VH::trimDoubleSlash(VH::XPATH_ADMINISTRATIVE_RIGHTS_METADATA) . '/mets:mdWrap', 'MDTYPE', 'Test');
        $this->resetDocument();

        $this->removeAttribute(VH::XPATH_ADMINISTRATIVE_RIGHTS_METADATA . '/mets:mdWrap', 'OTHERMDTYPE');
        $this->hasErrorAttribute(VH::trimDoubleSlash(VH::XPATH_ADMINISTRATIVE_RIGHTS_METADATA) . '/mets:mdWrap', 'OTHERMDTYPE');

        $this->setAttributeValue(VH::XPATH_ADMINISTRATIVE_RIGHTS_METADATA . '/mets:mdWrap', 'OTHERMDTYPE', 'Test');
        $this->hasErrorAttributeWithValue(VH::trimDoubleSlash(VH::XPATH_ADMINISTRATIVE_RIGHTS_METADATA) . '/mets:mdWrap', 'OTHERMDTYPE', 'Test');
        $this->resetDocument();

        $this->removeNodes(VH::XPATH_ADMINISTRATIVE_RIGHTS_METADATA . '/mets:mdWrap/mets:xmlData/dv:rights');
        $this->hasErrorOne('mets:xmlData[dv:rights]', VH::trimDoubleSlash(VH::XPATH_ADMINISTRATIVE_RIGHTS_METADATA) . '/mets:mdWrap');
    }

    /**
     * Test validation against the rules of chapters "2.7.2.1 Technische Metadaten – mets:techMD" and "2.7.2.2 Eingebettete technische Daten – mets:techMD/mets:mdWrap"
     *
     * @return void
     * @throws \DOMException
     */
    public function testTechnicalMetadataStructure(): void
    {
        $this->addChildNodeWithNamespace(VH::XPATH_ADMINISTRATIVE_METADATA, VH::NAMESPACE_METS, 'mets:techMD');
        $this->hasErrorAttribute(VH::trimDoubleSlash(VH::XPATH_ADMINISTRATIVE_TECHNICAL_METADATA), 'ID');

        $this->setAttributeValue(VH::XPATH_ADMINISTRATIVE_TECHNICAL_METADATA, 'ID', 'DMDLOG_0001');
        $this->hasErrorUniqueId(VH::trimDoubleSlash(VH::XPATH_ADMINISTRATIVE_TECHNICAL_METADATA), 'DMDLOG_0001');

        $this->setAttributeValue(VH::XPATH_ADMINISTRATIVE_TECHNICAL_METADATA, 'ID', 'TECH_0001');
        $this->hasErrorOne('mets:mdWrap', VH::trimDoubleSlash(VH::XPATH_ADMINISTRATIVE_TECHNICAL_METADATA));

        $this->addChildNodeWithNamespace(VH::XPATH_ADMINISTRATIVE_TECHNICAL_METADATA, VH::NAMESPACE_METS, 'mets:mdWrap');
        $this->hasErrorAttribute(VH::trimDoubleSlash(VH::XPATH_ADMINISTRATIVE_TECHNICAL_METADATA) . '/mets:mdWrap', 'MDTYPE');

        $this->setAttributeValue(VH::XPATH_ADMINISTRATIVE_TECHNICAL_METADATA . '/mets:mdWrap', 'MDTYPE', '');
        $this->hasErrorAttribute(VH::trimDoubleSlash(VH::XPATH_ADMINISTRATIVE_TECHNICAL_METADATA) . '/mets:mdWrap', 'OTHERMDTYPE');

        $this->setAttributeValue(VH::XPATH_ADMINISTRATIVE_TECHNICAL_METADATA . '/mets:mdWrap', 'OTHERMDTYPE', '');
        $this->hasErrorOne('mets:xmlData', VH::trimDoubleSlash(VH::XPATH_ADMINISTRATIVE_TECHNICAL_METADATA) . '/mets:mdWrap');
    }

    protected function createValidator(): AbstractDlfValidator
    {
        return new AdministrativeMetadataValidator();
    }
}
