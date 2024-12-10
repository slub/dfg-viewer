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

class AdministrativeMetadataValidator extends ApplicationProfileBaseValidator
{
    protected function isValid($value): void
    {
        parent::setValue($value);

        if ($this->xpath->query('//mets:structMap[@TYPE="LOGICAL"]')->length == 0) {
            $this->addError('Every METS file has to have at least one logical structural element.', 1723727164447);
        }

        $this->validateStructuralElements();

        $this->validateExternalReference();
    }

    /**
     * @return void
     */
    private function validateStructuralElements(): void
    {
        $structuralElements = $this->xpath->query('//mets:structMap[@TYPE="LOGICAL"]/mets:div');
        if ($structuralElements->length == 0) {
            $this->addError('Every logical structure has to consist of at least on mets:div.', 1724234607);
        } else {
            foreach ($structuralElements as $structuralElement) {
                if (!$structuralElement->hasAttribute("ID")) {
                    $this->addError('Mandatory "ID" attribute of mets:div in the logical structure is missing.', 1724234607);
                }

                if (!$structuralElement->hasAttribute("TYPE")) {
                    $this->addError('Mandatory "TYPE" attribute of mets:div in the logical structure is missing.', 1724234607);
                } else {
                    if (!in_array($structuralElement->getAttribute("TYPE"), self::STRUCTURE_DATASET)) {
                        $this->addError('Value "' . $structuralElement->getAttribute("TYPE") . '" of "TYPE" attribute of mets:div in the logical structure is not permissible.', 1724234607);
                    }
                }
            }
        }
    }

    /**
     * @return void
     */
    private function validateExternalReference(): void
    {
        $externalReference = $this->xpath->query('//mets:structMap[@TYPE="LOGICAL"]/mets:div/mets:mptr');
        if ($externalReference->length > 1) {
            $this->addError('Every mets:div in the logical structure may only contain one mets:mptr.', 1724234607);
        } else if ($externalReference->length == 1) {
            if (!$externalReference->hasAttribute("LOCTYPE")) {
                $this->addError('Mandatory "LOCTYPE" attribute of mets:mptr in the logical structure is missing.', 1724234607);
            } else {
                if (!in_array($externalReference->getAttribute("LOCTYPE"), array("URL", "PURL"))) {
                    $this->addError('Value "' . $externalReference->getAttribute("LOCTYPE") . '" of "LOCTYPE" attribute of mets:mptr in the logical structure is not permissible.', 1724234607);
                }
            }

            if (!$externalReference->hasAttribute("xlink:href")) {
                $this->addError('Mandatory "xlink:href" attribute of mets:mptr in the logical structure is missing.', 1724234607);
            }
        }
    }
}
