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
 * The validator validates against the rules outlined in chapter 2.6 of the METS application profile 2.3.1.
 *
 * @package TYPO3
 * @subpackage dfg-viewer
 *
 * @access public
 */
class AdministrativeMetadataValidator extends ApplicationProfileBaseValidator
{

    const XPATH_ADMINISTRATIVE_METADATA = '//mets:mets/mets:amdSec';

    const XPATH_TECHNICAL_METADATA = self::XPATH_ADMINISTRATIVE_METADATA . '/mets:techMD';

    const XPATH_RIGHTS_METADATA = self::XPATH_ADMINISTRATIVE_METADATA . '/mets:rightsMD';

    const XPATH_DIGIPROV_METADATA = self::XPATH_ADMINISTRATIVE_METADATA . '/mets:digiprovMD';

    protected function isValidDocument(): void
    {
        // Validates against the rules of chapter "2.6.1 Metadatensektion – mets:amdSec"
        $this->createNodeListValidator(self::XPATH_ADMINISTRATIVE_METADATA)
            ->validateHasAny()
            ->iterate(array($this, "validateAdministrativMetadata"));

        // Check if one administrative metadata exist with "mets:rightsMD" and "mets:digiprovMD" as children
        $this->createNodeListValidator(self::XPATH_ADMINISTRATIVE_METADATA . '[mets:rightsMD and mets:digiprovMD]')
            ->validateHasOne();

        $this->validateTechnicalMetadataStructure();
        $this->validateRightsMetadataStructure();
        $this->validateDigitalProvenanceMetadataStructure();
    }

    public function validateAdministrativMetadata(\DOMNode $administrativeMetadata): void
    {
        $this->createNodeValidator($administrativeMetadata)
            ->validateHasUniqueId();
    }

    /**
     * Validates the digital provenance metadata.
     *
     * Validates against the rules of chapters "2.6.2.5 Herstellung – mets:digiprovMD" and "2.6.2.6 Eingebettete Verweise – mets:digiprovMD/mets:mdWrap"
     *
     * @return void
     */
    protected function validateDigitalProvenanceMetadataStructure(): void
    {
        $this->createNodeListValidator(self::XPATH_DIGIPROV_METADATA)
            ->iterate(array($this, "validateDigitalProvenanceMetadata"));
    }

    public function validateDigitalProvenanceMetadata(\DOMNode $digitalProvenanceMetadata): void
    {
        $this->createNodeValidator($digitalProvenanceMetadata)
            ->validateHasUniqueId();

        $mdWrap = $this->createNodeListValidator('mets:mdWrap', $digitalProvenanceMetadata)
            ->validateHasOne()
            ->getFirstNode();

        $this->createNodeValidator($mdWrap)
            ->validateHasAttributeWithValue('MDTYPE', array('OTHER'))
            ->validateHasAttributeWithValue('OTHERMDTYPE', array('DVLINKS'));

        $this->createNodeListValidator('mets:xmlData[dv:links]', $mdWrap)
            ->validateHasOne();
    }

    /**
     * Validates the rights metadata.
     *
     * Validates against the rules of chapters "2.6.2.4 Rechtedeklaration – mets:rightsMD" and "2.6.2.4 Eingebettete Rechteangaben – mets:rightsMD/mets:mdWrap"
     *
     * @return void
     */
    protected function validateRightsMetadataStructure(): void
    {
        $this->createNodeListValidator(self::XPATH_RIGHTS_METADATA)
            ->iterate(array($this, "validateRightsMetadata"));
    }

    public function validateRightsMetadata(\DOMNode $rightsMetadata): void
    {
        $this->createNodeValidator($rightsMetadata)
            ->validateHasUniqueId();

        $mpWrap = $this->createNodeListValidator('mets:mdWrap', $rightsMetadata)
            ->validateHasOne()
            ->getFirstNode();

        $this->createNodeValidator($mpWrap)
            ->validateHasAttributeWithValue('MDTYPE', array('OTHER'))
            ->validateHasAttributeWithValue('OTHERMDTYPE', array('DVRIGHTS'));

        $this->createNodeListValidator('mets:xmlData[dv:rights]', $mpWrap)
            ->validateHasOne();
    }

    /**
     * Validates the technical metadata.
     *
     * Validates against the rules of chapters "2.6.2.1 Technische Metadaten – mets:techMD" and "2.6.2.2 Eingebettete technische Daten – mets:techMD/mets:mdWrap"
     *
     * @return void
     */
    protected function validateTechnicalMetadataStructure(): void
    {
        $this->createNodeListValidator(self::XPATH_TECHNICAL_METADATA)
            ->iterate(array($this, "validateTechnicalMetadata"));
    }


    public function validateTechnicalMetadata(\DOMNode $technicalMetadata): void
    {
        $this->createNodeValidator($technicalMetadata)
            ->validateHasUniqueId();

        $mdWrap = $this->createNodeListValidator('mets:mdWrap', $technicalMetadata)
            ->validateHasOne()
            ->getFirstNode();

        $this->createNodeValidator($mdWrap)
            ->validateHasAttribute("MDTYPE")
            ->validateHasAttribute("OTHERMDTYPE");

        $this->createNodeListValidator('mets:xmlData', $mdWrap)
            ->validateHasOne();
    }

}
