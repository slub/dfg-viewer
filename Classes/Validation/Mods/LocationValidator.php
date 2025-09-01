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
 * The validator validates against the rules outlined in chapter 2.13 of the MODS application profile 2.4.
 *
 * @package TYPO3
 * @subpackage dfg-viewer
 *
 * @access public
 */
class LocationValidator extends AbstractModsValidator
{
    public function isValidDocument(): void
    {
        $locations = $this->createNodeListValidator(VH::XPATH_MODS_LOCATION)
            ->getNodeList();
        foreach ($locations as $location) {
            $physicalLocation = $this->createNodeListValidator('mods:physicalLocation', $location)
                ->validateHasNoneOrOne()
                ->getFirstNode();

            if ($physicalLocation != null) {
                self::checkUriAttributes($this->createNodeAttributeValidator($physicalLocation));
            }

            $this->createNodeListValidator('mods:url | mods:physicalLocation', $location)
                ->validateHasAny();

            $urls = $this->createNodeListValidator('mods:url', $location)->getNodeList();
            foreach ($urls as $url) {
                $this->createNodeAttributeValidator($url, SeverityLevel::NOTICE)->validateValue('access', ['preview', 'raw object', 'object in context']);
            }

            $this->createNodeListValidator('mods:shelfLocator', $location)
                ->validateHasNoneOrOne();
        }
    }
}
