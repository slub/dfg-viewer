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
use Slub\Dfgviewer\Validation\Common\SeverityLevel;

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
        $this->validatePhysicalDescription();
        // Validation of chapter "2.7 Abstract" already covered by MODS XML schema validation
        $this->validateNotes();
        $this->validateSubjects();
        $this->validateClassification();
        $this->validateRelatedItem();
        $this->validateIdentifier();
        $this->validateLocation();
        // Chapter "2.14 Zugriffs- und Verarbeitungsrechte" currently not formulated
        $this->validatePart();
        $this->validateRecordInfo();
        // Validation of chapter "3.1 Erweiterung – mods:extension" already covered by MODS XML schema validation
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
        $this->createNodeListValidator(VH::XPATH_MODS_TITLEINFO . '[not(@type)]')
            ->validateHasOne();

        // TODO Mehrbändigkeit

        $titleInfos = $this->createNodeListValidator(VH::XPATH_MODS_TITLEINFO )
            ->getNodeList();

        foreach ($titleInfos as $titleInfo) {
            $this->validateTitleInfo($titleInfo);
        }
    }

    /**
     * Validates the title info.
     *
     * Validates against the rules of chapter "2.1.1 Titelangaben – mods:titleInfo"
     *
     * @return void
     */
    public function validateTitleInfo(mixed $titleInfo): void
    {
        if ($titleInfo instanceof \DOMElement) {
            $nodeValidator = $this->createNodeValidator($titleInfo);
            if ($titleInfo->hasAttribute('type')) {
                $nodeValidator->validateHasAttributeValue('type', ['abbreviated', 'translated', 'alternative', 'uniform']);
            }
            static::validateUriAttributes($nodeValidator);
            if ($titleInfo->hasAttribute('lang')) {
                $nodeValidator->validateHasAttributeWithIso6392B('lang');
            }
        }
        $this->validateTitleInfoSubElements($titleInfo);
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
            $this->validateName($name);
        }
    }

    /**
     * Validates the name.
     *
     * Validates against the rules of chapter "2.2.1 Namensangaben – mods:name"
     *
     * @return void
     */
    public function validateName(\DOMElement $name): void
    {
        $nodeValidator = $this->createNodeValidator($name)
            ->validateHasAttributeValue('type', ['personal', 'corporate', 'conference', 'family']);
        if ($name->hasAttribute('type') && ($name->getAttribute('type') == 'personal' || $name->getAttribute('type') == 'corporate')) {
            $nodeValidator->validateHasUrlAttribute('valueURI');
        }
        if ($name->hasAttribute('authorityURI')) {
            $nodeValidator->validateHasUrlAttribute('authorityURI');
        }
        $this->validateNameSubElements($name);
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
            $nodeValidator = $this->createNodeValidator($namePart);
            // TODO Einzigartigkeits check type Attribute
            if ($name instanceof \DOMElement && $name->hasAttribute('type')) {
                if ($name->getAttribute('type') == 'personal') {
                    $nodeValidator
                        ->validateHasAttributeValue('type', ['family', 'given', 'date', 'termsOfAddress']);
                }
                if ($name->getAttribute('type') == 'corporate') {
                    $nodeValidator
                        ->validateHasNoneAttribute('type');
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
            static::validateUriAttributes($this->createNodeValidator($genre));
        }
    }

    /**
     * Validates the origin.
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
                // TODO Einzigartigkeits check type Attribute
                foreach ($placeTerms as $placeTerm) {
                    $nodeValidator = $this->createNodeValidator($placeTerm)
                        ->validateHasAttributeValue('type', ['text', 'code']);
                    static::validateUriAttributes($nodeValidator);
                }
            }

            $agents = $this->createNodeListValidator('mods:agent', $originInfo)
                ->getNodeList();
            // TODO Check Agents by person and or cooperation
            foreach ($agents as $agent) {
                // validate mods:agent like mods:name
                $this->validateName($agent);
            }

            // Validates against the rules of chapters 2.4.2.4 - 2.4.2.8
            $dates = $this->createNodeListValidator('mods:dateIssued or mods:dateCreated or mods:dateValid or mods:dateOther', $originInfo);
            foreach ($dates as $date) {
                if ($date instanceof \DOMElement) {
                    if ($date->hasAttribute('authorityURI')) {
                        $this->createNodeValidator($date)
                            ->validateHasAttributeValue('qualifier', ['approximate', 'inferred', 'questionable']);
                    }
                }
            }

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
                    ->validateHasAttributeValue('type', ['code', 'text']);
                self::validateUriAttributes($nodeValidator);

                $this->createNodeValidator($languageTerm, SeverityLevel::NOTICE)
                    ->validateHasIso6392BContent();
            }

            $scriptTerms = $this->createNodeListValidator('mods:scriptTerm', $language)
                ->validateHasAny()
                ->getNodeList();
            foreach ($scriptTerms as $scriptTerm) {
                $nodeValidator = $this->createNodeValidator($scriptTerm)
                    ->validateHasAttributeValue('type', ['code', 'text']);
                self::validateUriAttributes($nodeValidator);

                $this->createNodeValidator($scriptTerm, SeverityLevel::NOTICE)
                    ->validateHasIso15924Content();
            }
        }
    }

    /**
     * Validates the physical description.
     *
     * Validates against the rules of chapter "2.6 Physische Beschreibung"
     *
     * @return void
     */
    protected function validatePhysicalDescription(): void
    {
        $this->createNodeListValidator(VH::XPATH_MODS_PHYSICAL_DESCRIPTION)
            ->validateHasNoneOrOne()
            ->getNodeList();
        // Validation of chapters "2.6.2.1 Form – mods:form" and "2.6.2.2 Umfang – mods:extent" already covered by MODS XML schema validation
    }

    /**
     * Validates the notes.
     *
     * Validates against the rules of chapter "2.8 Anmerkungen"
     *
     * @return void
     */
    protected function validateNotes(): void
    {
        $notes = $this->createNodeListValidator(VH::XPATH_MODS_NOTE)
            ->getNodeList();
        foreach ($notes as $note) {
            $this->createNodeValidator($note, SeverityLevel::NOTICE)->validateHasAttribute('type');
        }
    }

    /**
     * Validates the subjects.
     *
     * Validates against the rules of chapter "2.9 Schlagwörter"
     *
     * @return void
     */
    protected function validateSubjects(): void
    {
        $subjects = $this->createNodeListValidator(VH::XPATH_MODS_SUBJECT)
            ->getNodeList();
        foreach ($subjects as $subject) {
            $subjectValidator = $this->createNodeValidator($subject);
            if ($subject->hasAttribute('authorityURI')) {
                $subjectValidator->validateHasUrlAttribute('authorityURI');
            }

            $subjectsSubElements = $this->createNodeListValidator('mods:topic or mods:geographic or mods:temporal or mods:titleInfo or mods:name', $subject)
                ->getNodeList();

            foreach ($subjectsSubElements as $subjectsSubElement) {
                $subjectsSubElementValidator = $this->createNodeValidator($subjectsSubElement);
                if ($subjectsSubElement instanceof \DOMElement) {
                    if ($subjectsSubElement->hasAttribute('valueURI')) {
                        $subjectsSubElementValidator->validateHasUrlAttribute('valueURI');
                    }

                    if ($subjectsSubElement->nodeName == 'mods:titleInfo') {
                        $this->validateTitleInfo($subjectsSubElement);
                        if ($subjectsSubElement->hasAttribute('nameTitleGroup')) {
                            $nameTitleGroup = $subjectsSubElement->getAttribute('nameTitleGroup');
                            $this->createNodeListValidator('mods:name[@nameTitleGroup="' . $nameTitleGroup . '"]', $subject, SeverityLevel::NOTICE)
                                ->validateHasOne();
                        }
                    } elseif ($subjectsSubElement->nodeName == 'mods:name') {
                        $this->validateName($subjectsSubElement);
                        $nameTitleGroup = $subjectsSubElement->getAttribute('nameTitleGroup');
                        $this->createNodeListValidator('mods:titleInfo[@nameTitleGroup="' . $nameTitleGroup . '"]', $subject, SeverityLevel::NOTICE)
                            ->validateHasOne();
                    }
                }
            }
        }
    }

    /**
     * Validates the classification.
     *
     * Validates against the rules of chapter "2.10 Klassifikationen"
     *
     * @return void
     */
    protected function validateClassification(): void
    {
        $classifications = $this->createNodeListValidator(VH::XPATH_MODS_CLASSIFICATION)
            ->getNodeList();
        foreach ($classifications as $classification) {
            static::validateUriAttributes($this->createNodeValidator($classification));
        }
    }

    /**
     * Validates the related items.
     *
     * Validates against the rules of chapter "2.11 Beziehungen zu anderen Ressourcen"
     *
     * @return void
     */
    protected function validateRelatedItem(): void
    {
        $relatedItems = $this->createNodeListValidator(VH::XPATH_MODS_RELATEDITEM)
            ->getNodeList();
        foreach ($relatedItems as $relatedItem) {
            $this->createNodeValidator($relatedItem)
                ->validateHasAttributeValue('type', ['host', 'preceding', 'succeeding', 'series', 'original']);

            // Validation of chapter "2.11.2.1 Titelangaben – mods:titleInfo"
            $titleInfoListValidator = $this->createNodeListValidator('mods:titleInfo', $relatedItem);
            $recordInfoListValidator = $this->createNodeListValidator('mods:recordInfo', $relatedItem);

            if ($recordInfoListValidator->getNodeList()->length == 0) {
                $titleInfoListValidator->validateHasAny();
            }

            // TODO Sebstian nachfrage wegen "host" Prüfung in related Item wenn nicht vorhanden -> L451
            $titleInfos = $titleInfoListValidator->getNodeList();
            foreach ($titleInfos as $titleInfo) {
                $this->validateTitleInfo($titleInfo);
            }

            // Validation of chapter "2.11.2.2 Zählung – mods:part"
            $parts = $this->createNodeListValidator('mods:part', $relatedItem)
                ->validateHasNoneOrOne()
                ->getNodeList();

            // Validation of chapter "2.11.2.3 Unterelemente zu mods:part"
            foreach ($parts as $part) {
                $details = $this->createNodeListValidator('mods:detail', $part)
                    ->validateHasAny()
                    ->getNodeList();
                foreach ($details as $detail) {
                    // one detail without type attribute can exist
                    if ($details->length > 1 || ($detail instanceof \DOMElement && $detail->hasAttribute('type'))) {
                        $this->createNodeValidator($detail)
                            ->validateHasAttributeValue('type', ['volume', 'issue', 'chapter', 'collection', 'class', 'series', 'file']);
                        // type attribute can only be used once within a mods:part
                        $this->createNodeListValidator('mods:detail[@type="' . $detail->hasAttribute('type') . '"]', $part)
                            ->validateHasNoneOrOne();
                    }

                    // Validation of chapter "2.11.2.3.2.1 mods:number"
                    $this->createNodeListValidator('mods:number', $detail)->validateHasOne();
                }
            }

            // Validation of chapter "2.11.2.4 Ressource – mods:recordInfo"
            if (count($titleInfos) == 0) {
                $recordInfoListValidator->validateHasOne();
            } else {
                $recordInfoListValidator->validateHasNoneOrOne();
            }

            if ($recordInfoListValidator->getNodeList()->count() == 1) {
                $this->createNodeListValidator('mods:recordIdentifier', $recordInfoListValidator->getFirstNode())
                    ->validateHasOne();
            }
        }
    }

    /**
     * Validates the identifier.
     *
     * Validates against the rules of chapter "2.12 Identifier"
     *
     * @return void
     */
    protected function validateIdentifier(): void
    {
        $identifiers = $this->createNodeListValidator(VH::XPATH_MODS_IDENTIFIER)
            ->getNodeList();
        foreach ($identifiers as $identifier) {
            $nodeValidator = $this->createNodeValidator($identifier)->validateHasAttribute('type');
            if ($identifier instanceof \DOMElement && $identifier->hasAttribute("invalid")) {
                $nodeValidator->validateHasAttributeValue('invalid', ['yes']);
            }
        }
    }

    /**
     * Validates the location.
     *
     * Validates against the rules of chapter "2.13 Zugang zur Ressource"
     *
     * @return void
     */
    protected function validateLocation(): void
    {
        $locations = $this->createNodeListValidator(VH::XPATH_MODS_LOCATION)
            ->getNodeList();
        foreach ($locations as $location) {
            $physicalLocation = $this->createNodeListValidator('mods:physicalLocation', $location)
                ->validateHasNoneOrOne()
                ->getFirstNode();

            if ($physicalLocation != null) {
                self::validateUriAttributes($this->createNodeValidator($physicalLocation));
            }

            $this->createNodeListValidator('mods:url or mods:physicalLocation', $location)
                ->validateHasOne();

            $urls = $this->createNodeListValidator('mods:url', $location);
            foreach ($urls as $url) {
                $this->createNodeValidator($url, SeverityLevel::NOTICE)->validateHasAttributeValue('access', ['preview','raw object', 'object in context']);
            }

            $this->createNodeListValidator('mods:shelfLocator', $location)
                ->validateHasNoneOrOne();
        }
    }

    /**
     * Validates the part.
     *
     * Validates against the rules of chapter "2.15 Angabe von Bänden und anderen Teilen"
     *
     * @return void
     */
    protected function validatePart(): void
    {
        $nodeListValidator = $this->createNodeListValidator(VH::XPATH_MODS_PART);

        $this->createNodeListValidator(VH::XPATH_MODS_RELATEDITEM . '[@type="host"]')->getNodeList()->count() > 0 ? $nodeListValidator->validateHasOne() : $nodeListValidator->validateHasNoneOrOne();

        $part = $nodeListValidator->getFirstNode();
        if ($part != null) {
            $nodeValidator = $this->createNodeValidator($part);
            $orderValue = $nodeValidator
                ->validateHasAttribute('order')
                ->getDomElement()->getAttribute('order');
            if (!(is_int($orderValue) && $orderValue >= 0)) {
                $nodeValidator->addSeverityMessage('Value "' . $orderValue . '" in the "order" attribute of "' . $part->getNodePath() . '" is not a positiv integer.', 1746779788);
            }

            // Validation of chapter "2.15.2 Unterelemente zu mods:part"
            $details = $this->createNodeListValidator('mods:detail', $part)
                ->validateHasAny()
                ->getNodeList();
            foreach ($details as $detail) {
                $this->createNodeValidator($detail)->validateHasAttributeValue('type', ['volume', 'issue', 'chapter', 'album']);

                $this->createNodeListValidator('mods:number', $detail)->validateHasOne();
            }
        }
    }

    /**
     * Validates the record info.
     *
     * Validates against the rules of chapter "2.16 Informationen zum Metadatensatz"
     *
     * @return void
     */
    protected function validateRecordInfo(): void
    {
        // Validation of chapter "2.16.1 Datensatzinformationen – mods:recordInfo"
        $recordInfo = $this->createNodeListValidator(VH::XPATH_MODS_RECORDINFO)
            ->validateHasOne()
            ->getFirstNode();
        if ($recordInfo != null) {
            // Validation of chapter "2.16.2.1 Identifier – mods:recordIdentifier"
            $this->createNodeListValidator('mods:recordIdentifier', $recordInfo)
                ->validateHasOne();

            // Validation of chapter "2.16.2.2 Erschließungsstandard – mods:descriptionStandard"
            $this->createNodeListValidator('mods:descriptionStandard', $recordInfo)
                ->validateHasNoneOrOne();
        }
    }

    private static function validateUriAttributes(DomNodeValidator $nodeValidator): void
    {
        $element = $nodeValidator->getDomElement();
        if ($element->hasAttribute('authorityURI')) {
            $nodeValidator->validateHasUrlAttribute('authorityURI');
        }
        if ($element->hasAttribute('valueURI')) {
            $nodeValidator->validateHasUrlAttribute('valueURI');
        }
    }

}
