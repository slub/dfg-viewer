<?php

namespace Slub\Dfgviewer\Validation\Mods;

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
use Slub\Dfgviewer\Validation\Common\AbstractNodeValidator;
use Slub\Dfgviewer\Validation\Common\NodeAttributeValidator;
use Slub\Dfgviewer\Validation\Common\SeverityLevel;

abstract class AbstractModsValidator extends AbstractDomDocumentValidator
{
    protected function hasRelatedItemWithTypeHost(): bool
    {
        return $this->createNodeListValidator(VH::XPATH_MODS_RELATEDITEM . '[@type="host"]')->getNodeList()->count() > 0;
    }

    protected static function checkUriAttributes(NodeAttributeValidator $attributeValidator): void
    {
        $element = $attributeValidator->getDomElement();
        if ($element->hasAttribute('authorityURI')) {
            $attributeValidator->validateUrl('authorityURI');
        }
        if ($element->hasAttribute('valueURI')) {
            $attributeValidator->validateUrl('valueURI');
        }
    }

    protected function checkUniqueAttributeUnderParent(AbstractNodeValidator $nodeValidator, string $attribute): void
    {
        $element = $nodeValidator->getDomElement();
        $this->createNodeListValidator($element->tagName . '[@' . $attribute . '="' . $element->getAttribute($attribute) . '"]', $element->parentNode)
            ->validateHasOne();
    }

    /**
     * Validates the title info.
     *
     * Validates against the rules of chapter "2.1.1 Titelangaben – mods:titleInfo".
     * Is needed in several validators and is therefore found in this class.
     *
     * Genera
     *
     * @return void
     */
    protected function validateTitleInfo(mixed $titleInfo): void
    {
        if ($titleInfo instanceof \DOMElement) {
            $nodeValidator = $this->createNodeAttributeValidator($titleInfo);
            if ($titleInfo->hasAttribute('type')) {
                $nodeValidator->validateValue('type', ['abbreviated', 'translated', 'alternative', 'uniform']);
            }
            static::checkUriAttributes($nodeValidator);
            if ($titleInfo->hasAttribute('lang')) {
                $nodeValidator->validateIso6392B('lang');
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
     * Validates the name.
     *
     * Validates against the rules of chapter "2.2.1 Namensangaben – mods:name"
     * Is needed in several validators and is therefore found in this class.
     *
     * @return void
     */
    protected function validateName(\DOMNode $name): void
    {
        $nodeValidator = $this->createNodeAttributeValidator($name)
            ->validateValue('type', ['personal', 'corporate', 'conference', 'family']);

        $this->createNodeAttributeValidator($name, SeverityLevel::NOTICE)->validateHas('valueURI');

        self::checkUriAttributes($nodeValidator);

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
            $nodeValidator = $this->createNodeAttributeValidator($namePart);
            if ($this->createNodeAttributeValidator($name)->getDomElement()->getAttribute('type') == 'personal') {
                $nodeValidator->validateValue('type', ['family', 'given', 'date', 'termsOfAddress']);
                $this->checkUniqueAttributeUnderParent($nodeValidator, 'type');
            } else {
                $nodeValidator
                    ->validateHasNone('type');
            }
        }

        // Validates against the rules of chapter "2.2.2.2 Anzeigeform – mods:displayForm"
        $this->createNodeListValidator('mods:displayForm', $name)
            ->validateHasNoneOrOne();

        // Validates against the rules of chapter "2.2.2.3 Rollenangabe – mods:role"
        $roles = $this->createNodeListValidator('mods:role', $name)
            ->validateHasAny()
            ->getNodeList();

        // Validates against the rules of chapter "2.2.2.4 Unterelemente zu mods:role"
        foreach ($roles as $role) {
            $roleTerms = $this->createNodeListValidator('mods:roleTerm', $role)
                ->validateHasAny()
                ->getNodeList();

            $this->createNodeListValidator('mods:roleTerm[@type="code"]', $role)
                ->validateHasOne();

            foreach ($roleTerms as $roleTerm) {
                $nodeValidator = $this->createNodeAttributeValidator($roleTerm)
                    ->validateValue('type', ['text', 'code']);
                $this->checkUniqueAttributeUnderParent($nodeValidator, 'type');
                if ($nodeValidator->getDomElement()->getAttribute('type') == 'code') {
                    $this->createNodeAttributeValidator($roleTerm, SeverityLevel::NOTICE)
                        ->validateValue('authority', ['marcrelator'])
                        ->validateValue('authorityURI', ['http://id.loc.gov/vocabulary/relators', 'https://id.loc.gov/vocabulary/relators']);
                }
                self::checkUriAttributes($nodeValidator);
            }
        }
    }

}
