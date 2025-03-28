<?php

namespace Slub\Dfgviewer\Validation;

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

/**
 * The validator validates against the rules of the MODS application profile 2.4.
 *
 * @package TYPO3
 * @subpackage dfg-viewer
 *
 * @access public
 */
class ModsMetadataValidator extends AbstractDomDocumentValidator
{
    public function isValidDocument(): void
    {
        $this->validateTitle();
        $this->validateNames();
        $this->validateGenre();

        // Validation of chapter "2.7 Abstract" already covered by MODS XML schema validation

        // TODO überarbeiten
        $notes = $this->createNodeListValidator('//mods:note', VH::XPATH_MODS)
            ->getNodeList();
        foreach ($notes as $note) {
            $this->createNodeValidator($note)->validateHasAttribute('TYPE');
        }
    }

    /**
     * Validates the title.
     *
     * Validates against the rules of chapter "2.1 Titel"
     *
     * @return void
     */
    protected function validateTitle(): void
    {
        $this->createNodeListValidator(VH::XPATH_MODS_TITLEINFO . '[not(@TYPE)]')
            ->validateHasOne();

        $typedTitleInfos = $this->createNodeListValidator(VH::XPATH_MODS_TITLEINFO . '[@TYPE]')
            ->getNodeList();

        foreach ($typedTitleInfos as $typedTitleInfo) {
            $nodeValidator = $this->createNodeValidator($typedTitleInfo)
                ->validateHasAttributeWithValue('TYPE', ['abbreviated', 'translated', 'alternative', 'uniform']);
            if ($typedTitleInfo instanceof \DOMElement) {
                if ($typedTitleInfo->hasAttribute('authorityURI')) {
                    $nodeValidator->validateHasAttributeWithUrl('authorityURI');
                }
                if ($typedTitleInfo->hasAttribute('valueURI')) {
                    $nodeValidator->validateHasAttributeWithUrl('valueURI');
                }
                if ($typedTitleInfo->hasAttribute('lang')) {
                    $nodeValidator->validateHasAttributeWithIso6392B('lang');
                }
            }

            $this->validateTitleInfoSubElements($typedTitleInfo);
        }
    }

    /**
     * Validates the title subelements.
     *
     * Validates against the rules of chapter "2.1.2 Unterelemente zu mods:titleInfo"
     *
     * @return void
     */
    protected function validateTitleInfoSubElements(\DOMNode $titleInfo): void
    {
        // Validates against the rules of chapter "2.1.2.1 Haupttitel – mods:title"
        $this->createNodeListValidator('mods:title', $titleInfo)
            ->validateHasOne();

        // Validations of 2.1.2.2 - 2.1.2.5 already covered by MODS XML schema validation
    }

    /**
     * Validates the names.
     *
     * Validates against the rules of chapter "2.2 Namen von Personen oder Körperschaften"
     *
     * @return void
     */
    protected function validateNames(): void
    {
        $names = $this->createNodeListValidator(VH::XPATH_MODS_NAMES)
            ->getNodeList();

        foreach ($names as $name) {
            $nodeValidator = $this->createNodeValidator($name)
                ->validateHasAttributeWithValue('TYPE', ['personal', 'corporate', 'conference', 'family']);
            if ($name instanceof \DOMElement) {
                if ($name->hasAttribute('TYPE') && ($name->getAttribute('TYPE') == 'personal' || $name->getAttribute('TYPE') == 'corporate')) {
                    $nodeValidator->validateHasAttributeWithUrl('valueURI');
                }
                if ($name->hasAttribute('authorityURI')) {
                    $nodeValidator->validateHasAttributeWithUrl('authorityURI');
                }
                $this->validateNameSubElements($name);
            }
        }
    }

    /**
     * Validates the name subelements.
     *
     * Validates against the rules of chapter "2.2.2 Unterelemente zu mods:name"
     *
     * @return void
     */
    protected function validateNameSubElements(\DOMNode $name): void
    {
        // Validates against the rules of chapter "2.2.2.1 Namensbestandteil – mods:namePart"
        $nameParts = $this->createNodeListValidator('mods:namePart', $name)
            ->validateHasAny()
            ->getNodeList();

        foreach ($nameParts as $namePart) {
            $namePartValidator = $this->createNodeValidator($namePart);
            // TODO Einzigartigkeits check TYPE Attribute
            if ($name instanceof \DOMElement && $name->hasAttribute('TYPE')) {
                if ($name->getAttribute('TYPE') == 'personal') {
                    $namePartValidator
                        ->validateHasAttributeWithValue('TYPE', ['family', 'given', 'date', 'termsOfAddress']);
                }
                if ($name->getAttribute('TYPE') == 'corporate') {
                    $namePartValidator
                        ->validateHasNoneAttribute('TYPE');
                }
            }
        }

        // Validates against the rules of chapter "2.2.2.2 Anzeigeform – mods:displayForm"
        $this->createNodeListValidator('mods:displayForm', $name)
            ->validateHasNoneOrOne();

        // Validates against the rules of chapter "2.2.2.3 Rollenangabe – mods:role"
        $this->createNodeListValidator('mods:role', $name)
            ->validateHasAny();
        // TODO Rückfrage
        // TODO 2.2.2.4.1 Rolle – mods:roleTerm
    }

    /**
     * Validates the genre.
     *
     * Validates against the rules of chapter "2.3 Gattung/Genre"
     *
     * @return void
     */
    protected function validateGenre(): void
    {

    }

}
