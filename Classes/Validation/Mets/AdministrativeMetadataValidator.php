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
    protected function isValidDocument(): void
    {
        // Validates against the rules of chapter "2.6.1 Metadatensektion – mets:amdSec"
        $admSections = $this->xpath->query('//mets:amdSec');
        if ($admSections === false || $admSections->length == 0) {
            $this->addError('Every METS file has to have at least one administrative metadata section.', 1723727164447);
        }

        $hasDFGViewerSpecifics = false;
        foreach ($admSections as $admSection) {
            $this->validateTechnicalMetadata($admSection);
            $hasDFGViewerSpecifics = ($this->xpath->query('/mets:rightsMD', $admSection)->length != 0 ||
                $this->xpath->query('/mets:digiprovMD', $admSection)->length != 0);
        }

        if ($hasDFGViewerSpecifics) {
            $this->addError('Every METS file must include at least one administrative metadata section containing "mets:rightsMD" and "mets:digiprovMD" as child elements.', 1723727164447);
        }
    }

    /**
     * Validates the technical metadata.
     *
     * Validates against the rules of chapters "2.6.2.1 Technische Metadaten – mets:techMD" and "2.6.2.2 Eingebettete technische Daten – mets:techMD/mets:mdWrap"
     *
     * @return void
     */
    private function validateTechnicalMetadata(\DOMNode $amdSection): void
    {
        $technicalElements = $this->xpath->query('/mets:techMD', $amdSection);
        if ($technicalElements === false || $technicalElements->length == 0) {
            return;
        }

        if ($technicalElements->length > 1) {
            $this->addError('Every "mets:amdSec" has to consist none or one "mets:techMD".', 1724234607);
        }

        if ($technicalElements->length == 1) {
            $mdWrap = $this->xpath->query('/mets:mdWrap', $technicalElements->item(0));
            if( $mdWrap === false || $mdWrap->length != 1 ) {
                $this->addError('Every "mets:techMD" has to consist one "mets:mdWrap".', 1724234607);
            } else {
                if (!$mdWrap->hasAttribute("MDTYPE") || $mdWrap->hasAttribute("OTHERMDTYPE")) {
                    $this->addError('Mandatory attribute "MDTYPE" or "OTHERMDTYPE" of "mets:techMD/mets:mdWrap" is missing', 1724234607);
                }

                $xmlData = $this->xpath->query('/mets:xmlData', $mdWrap);
                if ($xmlData === false || $xmlData->length == 0) {
                    $this->addError('Every "mets:techMD/mets:mdWrap" has to consist one "mets:xmlData"', 1724234607);
                }
            }
        }
    }
}
