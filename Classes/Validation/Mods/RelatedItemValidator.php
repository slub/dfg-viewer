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

/**
 * The validator validates against the rules outlined in chapter 2.11 of the MODS application profile 2.4.
 *
 * @package TYPO3
 * @subpackage dfg-viewer
 *
 * @access public
 */
class RelatedItemValidator extends AbstractModsValidator
{
    public function isValidDocument(): void
    {
        $relatedItems = $this->createNodeListValidator(VH::XPATH_MODS_RELATEDITEM)
            ->getNodeList();
        foreach ($relatedItems as $relatedItem) {
            $this->createNodeAttributeValidator($relatedItem)
                ->validateValue('type', ['host', 'preceding', 'succeeding', 'series', 'original']);

            // Validation of chapter "2.11.2.1 Titelangaben – mods:titleInfo"
            $titleInfoValidator = $this->createNodeListValidator('mods:titleInfo', $relatedItem);
            $recordInfoValidator = $this->createNodeListValidator('mods:recordInfo', $relatedItem);

            if ($recordInfoValidator->getNodeList()->length == 0) {
                $titleInfoValidator->validateHasAny();
            }

            $titleInfos = $titleInfoValidator->getNodeList();
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
                        $nodeValidator = $this->createNodeAttributeValidator($detail)
                            ->validateValue('type', ['volume', 'issue', 'chapter', 'collection', 'class', 'series', 'file']);
                        // type attribute can only be used once within a mods:part
                        $this->createNodeListValidator('mods:detail[@type="' . $nodeValidator->getDomElement()->getAttribute('type') . '"]', $part)
                            ->validateHasOne();
                    }

                    // Validation of chapter "2.11.2.3.2.1 mods:number"
                    $this->createNodeListValidator('mods:number', $detail)->validateHasOne();
                }
            }

            // Validation of chapter "2.11.2.4 Ressource – mods:recordInfo"
            $recordInfoValidator->validateHasNoneOrOne();

            if ($recordInfoValidator->getNodeList()->count() == 1) {
                $this->createNodeListValidator('mods:recordIdentifier', $recordInfoValidator->getFirstNode())
                    ->validateHasOne();
            }
        }
    }
}
