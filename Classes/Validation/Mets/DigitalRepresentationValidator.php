<?php

namespace Slub\Dfgviewer\Validation\Mets;

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

use Slub\Dfgviewer\Common\ValidationHelper as VH;
use Slub\Dfgviewer\Validation\AbstractDomDocumentValidator;

/**
 * The validator validates against the rules outlined in chapter 2.5 of the METS application profile 2.4.
 *
 * @package TYPO3
 * @subpackage dfg-viewer
 *
 * @access public
 */
class DigitalRepresentationValidator extends AbstractDomDocumentValidator
{
    public function isValidDocument(): void
    {
        // Validates against the rules of chapter "2.4.1 Dateisektion – mets:fileSec"
        $this->createNodeListValidator(VH::XPATH_FILE_SECTIONS)
            ->validateHasNoneOrOne();

        // If a physical structure is present, there must be one file section.
        if ($this->xpath->query(VH::XPATH_PHYSICAL_STRUCTURES)->length > 0) {
            $this->createNodeListValidator(VH::XPATH_FILE_SECTIONS)
                ->validateHasOne();
        }

        if ($this->xpath->query(VH::XPATH_FILE_SECTIONS)->length > 0) {
            $this->validateFileGroups();
            $this->validateFiles();
        }
    }

    /**
     * Validates the file groups.
     *
     * Validates against the rules of chapter "2.5.2.1 Dateigruppen – mets:fileGrp"
     *
     * @return void
     */
    protected function validateFileGroups(): void
    {
        $fileSectionGroups = $this->createNodeListValidator(VH::XPATH_FILE_SECTION_GROUPS)
            ->validateHasAny()
            ->getNodeList();
        foreach ($fileSectionGroups as $fileSectionGroup) {
            $this->validateFileGroup($fileSectionGroup);
        }

        $this->createNodeListValidator(VH::XPATH_FILE_SECTION_GROUPS . '[@USE="DEFAULT"]')
            ->validateHasOne();
    }

    protected function validateFileGroup(\DOMNode $fileGroup): void
    {
        $this->createNodeValidator($fileGroup)
            ->validateHasUniqueAttribute("USE", VH::XPATH_FILE_SECTION_GROUPS);
    }

    /**
     * Validates the files.
     *
     * Validates against the rules of chapters "2.5.2.2 Datei – mets:fileGrp/mets:file" and "2.4.2.3 Dateilink – mets:fileGrp/mets:file/mets:FLocat"
     *
     * @return void
     */
    protected function validateFiles(): void
    {
        $files = $this->createNodeListValidator(VH::XPATH_FILE_SECTION_FILES)
            ->validateHasAny()
            ->getNodeList();
        foreach ($files as $file) {
            $this->validateFile($file);
        }
    }

    protected function validateFile(\DOMNode $file): void
    {
        $this->createNodeValidator($file)
            ->validateHasUniqueId()
            ->validateHasAttribute('MIMETYPE');

        $fLocat = $this->createNodeListValidator('mets:FLocat', $file)
            ->validateHasOne()
            ->getFirstNode();

        $this->createNodeValidator($fLocat)
            ->validateHasAttributeValue('LOCTYPE', ['URL', 'PURL'])
            ->validateHasUrlAttribute('xlink:href');
    }
}
