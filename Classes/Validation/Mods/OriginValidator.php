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
use Slub\Dfgviewer\Validation\Common\SeverityLevel;

/**
 * The validator validates against the rules outlined in chapter 2.4 of the MODS application profile 2.4.
 *
 * @package TYPO3
 * @subpackage dfg-viewer
 *
 * @access public
 */
class OriginValidator extends AbstractModsValidator
{
    public function isValidDocument(): void
    {
        $originInfos = $this->createNodeListValidator(VH::XPATH_MODS_ORIGININFO)
            ->getNodeList();
        foreach ($originInfos as $originInfo) {
            $nodeValidator = $this->createNodeValidator($originInfo)
                ->validateHasAttribute('eventType');

            $this->checkUniqueAttributeUnderParent($nodeValidator, 'eventType');

            $places = $this->createNodeListValidator('mods:place', $originInfo)
                ->getNodeList();
            foreach ($places as $place) {
                $placeTerms = $this->createNodeListValidator('mods:placeTerm', $place)
                    ->validateHasAny()
                    ->getNodeList();
                foreach ($placeTerms as $placeTerm) {
                    $nodeValidator = $this->createNodeValidator($placeTerm)
                        ->validateHasAttributeValue('type', ['text', 'code']);
                    static::checkUriAttributes($nodeValidator);
                }
            }

            $agents = $this->createNodeListValidator('mods:agent', $originInfo)
                ->getNodeList();
            foreach ($agents as $agent) {
                // validate mods:agent like mods:name
                $this->validateName($agent);
            }

            // Validates against the rules of chapters 2.4.2.4 - 2.4.2.8
            $dates = $this->createNodeListValidator('mods:dateIssued | mods:dateCreated | mods:dateValid | mods:dateOther', $originInfo)->getNodeList();
            foreach ($dates as $date) {
                $nodeValidator = $this->createNodeValidator($date);
                if ($date instanceof \DOMElement) {
                    if ($date->hasAttribute('qualifier')) {
                        $nodeValidator->validateHasAttributeValue('qualifier', ['approximate', 'inferred', 'questionable']);
                    }
                    if ($date->hasAttribute('point')) {
                        $nodeValidator->validateHasAttributeValue('point', ['start', 'end']);
                        if ($date->hasAttribute('keyDate')) {
                            $nodeValidator->validateHasAttributeValue('keyDate', ['yes']);
                            $nodeValidator->validateHasAttributeValue('encoding', ['iso8601']);
                        }
                    }

                    if (!$date->hasAttribute('point') || !$date->hasAttribute('keyDate')) {
                        $this->createNodeValidator($date, SeverityLevel::NOTICE)->validateHasAttributeValue('encoding', ['iso8601']);
                    }
                }
            }

            $this->createNodeListValidator('mods:dateIssued[@keyDate="yes"] | mods:dateCreated[@keyDate="yes"] | mods:dateValid[@keyDate="yes"] | mods:dateOther[@keyDate="yes"]', $originInfo)
                ->validateHasNoneOrOne();

            // Validates against the rules of chapter 2.4.2.9
            $this->createNodeListValidator('mods:edition', $originInfo)
                ->validateHasNoneOrOne();
        }
    }
}
