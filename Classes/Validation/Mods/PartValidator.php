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
 * The validator validates against the rules outlined in chapter 2.15 of the MODS application profile 2.4.
 *
 * @package TYPO3
 * @subpackage dfg-viewer
 *
 * @access public
 */
class PartValidator extends AbstractModsValidator
{
    public function isValidDocument(): void
    {
        $nodeListValidator = $this->createNodeListValidator(VH::XPATH_MODS_PART);

        $this->hasRelatedItemWithTypeHost() ? $nodeListValidator->validateHasOne() : $nodeListValidator->validateHasNoneOrOne();

        $part = $nodeListValidator->getFirstNode();
        if ($part != null) {
            $nodeValidator = $this->createNodeValidator($part);
            $orderValue = $nodeValidator
                ->validateHasAttribute('order')
                ->getDomElement()->getAttribute('order');
            if (!(ctype_digit($orderValue) && (int) $orderValue >= 0)) {
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
}
