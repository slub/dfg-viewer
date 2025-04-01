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
use Slub\Dfgviewer\Validation\Common\DomNodeValidator;

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
        $this->validateOrigin();
        $this->validateLanguage();

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
                static::validateUriAttributes($typedTitleInfo, $nodeValidator);
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
            $this->validateNameOrAgent($name);
        }
    }

    /**
     * Validates the name subelements.
     *
     * Validates against the rules of chapter "2.2.2 Unterelemente zu mods:name"
     *
     * @return void
     */
    protected function validateNameOrAgentSubElements(\DOMNode $name): void
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
        $genres = $this->createNodeListValidator(VH::XPATH_MODS_GENRES)
            ->getNodeList();
        foreach ($genres as $genre) {
            static::validateUriAttributes($genre, $this->createNodeValidator($genre));
        }
    }

    /**
     * @param mixed $name
     * @return void
     */
    public function validateNameOrAgent(\DOMElement $name): void
    {
        $nodeValidator = $this->createNodeValidator($name)
            ->validateHasAttributeWithValue('TYPE', ['personal', 'corporate', 'conference', 'family']);
        if ($name->hasAttribute('TYPE') && ($name->getAttribute('TYPE') == 'personal' || $name->getAttribute('TYPE') == 'corporate')) {
            $nodeValidator->validateHasAttributeWithUrl('valueURI');
        }
        if ($name->hasAttribute('authorityURI')) {
            $nodeValidator->validateHasAttributeWithUrl('authorityURI');
        }
        $this->validateNameOrAgentSubElements($name);
    }

    /**
     * Validates the genre.
     *
     * Validates against the rules of chapter "2.4 Angaben zu Entstehung und Lebenszyklus"
     *
     * @return void
     */
    protected function validateOrigin(): void
    {
        $originInfos = $this->createNodeListValidator(VH::XPATH_MODS_ORIGININFO)
            ->getNodeList();
        foreach ($originInfos as $originInfo) {
             $this->createNodeValidator($originInfo)
                 ->validateHasUniqueAttribute('eventType', VH::XPATH_MODS_ORIGININFO);

            $places = $this->createNodeListValidator('mods:place', $originInfo)
                ->getNodeList();
            foreach ($places as $place) {
                $placeTerms = $this->createNodeListValidator('mods:placeTerm', $place)
                    ->validateHasAny()
                    ->getNodeList();
                // TODO Einzigartigkeits check TYPE Attribute

                foreach ($placeTerms as $placeTerm) {
                    $nodeValidator = $this->createNodeValidator($placeTerm)
                        ->validateHasAttributeWithValue('TYPE', ['text', 'code']);
                    static::validateUriAttributes($placeTerm, $nodeValidator);
                }
            }

            $agents = $this->createNodeListValidator('mods:agent', $originInfo)
                ->getNodeList();
            // TODO Check Agents by person and or cooperation
            foreach ($agents as $agent) {
                // validate mods:agent like mods:name
                $this->validateNameOrAgent($agent);
            }

            // Validates against the rules of chapters 2.4.2.4 - 2.4.2.8
            $this->validateOriginDate('mods:dateIssued', $originInfo);
            $this->validateOriginDate('mods:dateCreated', $originInfo);
            $this->validateOriginDate('mods:dateValid', $originInfo);
            $this->validateOriginDate('mods:dateOther', $originInfo);

            // Validates against the rules of chapter 2.4.2.9
            $this->createNodeListValidator(' mods:edition', $originInfo)
                ->validateHasNoneOrOne();
        }
    }

    /**
     * Validates the language and font.
     *
     * Validates against the rules of chapter "2.5 Sprache und Schrift"
     *
     * @return void
     */
    protected function validateLanguage(): void
    {
        $languages = $this->createNodeListValidator(VH::XPATH_MODS_LANGUAGE)
            ->getNodeList();
        foreach ($languages as $language) {
            $languageTerms = $this->createNodeListValidator('mods:languageTerm', $language)
                ->validateHasAny()
                ->getNodeList();
            foreach ($languageTerms as $languageTerm) {
                $nodeValidator = $this->createNodeValidator($languageTerm)
                    ->validateHasAttributeWithIso6392B('code');
                self::validateUriAttributes($languageTerm, $nodeValidator);
            }

            $scriptTerms = $this->createNodeListValidator('mods:scriptTerm', $language)
                ->validateHasAny()
                ->getNodeList();
            foreach ($scriptTerms as $scriptTerm) {
                // TODO Code validation ISO 15924
                self::validateUriAttributes($scriptTerm, $nodeValidator);
            }
        }
    }

    private static function validateUriAttributes(\DOMElement $node, DomNodeValidator $nodeValidator): void
    {
        if ($node->hasAttribute('authorityURI')) {
            $nodeValidator->validateHasAttributeWithUrl('authorityURI');
        }
        if ($node->hasAttribute('valueURI')) {
            $nodeValidator->validateHasAttributeWithUrl('valueURI');
        }
    }

    private function validateOriginDate(string $expression,mixed $originInfo): void
    {
        $dates = $this->createNodeListValidator($expression, $originInfo);
        foreach ($dates as $date) {
            if ($date instanceof \DOMElement) {
                if ($date->hasAttribute('authorityURI')) {
                    $this->createNodeValidator($date)
                        ->validateHasAttributeWithValue('qualifier', ['approximate', 'inferred', 'questionable']);
                }
            }
        }
    }

}
