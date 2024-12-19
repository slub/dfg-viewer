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
 * The validator validates against the rules outlined in chapter 2.4 of the METS application profile 2.3.1.
 *
 * @package TYPO3
 * @subpackage dfg-viewer
 *
 * @access public
 */
class DigitalRepresentationValidator extends ApplicationProfileBaseValidator
{

    const XPATH_FILE_SECTIONS = '//mets:mets/mets:fileSec';

    const XPATH_FILE_GROUPS = self::XPATH_FILE_SECTIONS . '/mets:fileGroup';

    const XPATH_FILES = self::XPATH_FILE_GROUPS . '/mets:file';

    protected function isValidDocument(): void
    {
        // Validates against the rules of chapter "2.4.1 Dateisektion – mets:fileSec"
        $this->createNodeListValidator(self::XPATH_FILE_SECTIONS)
            ->validateHasNoneOrOne();

        // If a physical structure is present, there must be one file section.
        if($this->xpath->query(PhysicalStructureValidator::XPATH_PHYSICAL_STRUCTURES)->length > 0){
            $this->createNodeListValidator(self::XPATH_FILE_SECTIONS)
                ->validateHasOne();
        }

        $this->validateFileGroups();
        $this->validateFiles();
    }

    /**
     * Validates the digital provenance metadata.
     *
     * Validates against the rules of chapters "2.6.2.5 Herstellung – mets:digiprovMD" and "2.6.2.6 Eingebettete Verweise – mets:digiprovMD/mets:mdWrap"
     *
     * @return void
     */
    protected function validateFileGroups(): void
    {
        $this->createNodeListValidator(self::XPATH_FILE_GROUPS)
            ->validateHasAny()
            ->iterate(array($this, "validateFileGroup"));

        $this->createNodeListValidator(self::XPATH_FILE_GROUPS . '[@USE="DEFAULT"]')
            ->validateHasOne();
    }

    public function validateFileGroup(\DOMNode $fileGroup): void
    {
        $this->createNodeValidator($fileGroup)
            ->validateHasUniqueAttribute("USE", self::XPATH_FILE_GROUPS);
    }

    protected function validateFiles(): void
    {
        $this->createNodeListValidator(self::XPATH_FILES)
            ->validateHasAny()
            ->iterate(array($this, "validateFile"));
    }

    public function validateFile(\DOMNode $file): void
    {
        $this->createNodeValidator($file)
            ->validateHasUniqueId();

        $fLocat = $this->createNodeListValidator('/mets:FLocat', $file)
            ->validateHasOne()
            ->getFirstNode();

        $this->createNodeValidator($fLocat)
            ->validateHasAttribute("xlink:href")
            ->validateHasAttributeWithValue("LOCTYPE", array("URL", "PURL"));
    }
}
