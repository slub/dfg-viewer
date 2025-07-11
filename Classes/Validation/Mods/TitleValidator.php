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
 * The validator validates against the rules outlined in chapter 2.1 of the MODS application profile 2.4.
 *
 * @package TYPO3
 * @subpackage dfg-viewer
 *
 * @access public
 */
class TitleValidator extends AbstractModsValidator
{
    public function isValidDocument(): void
    {
        if (!$this->hasRelatedItemWithTypeHost()) {
            // Check for primary title
            $this->createNodeListValidator(VH::XPATH_MODS_TITLEINFO . '[not(@type)]')
                ->validateHasOne();
        }

        $titleInfos = $this->createNodeListValidator(VH::XPATH_MODS_TITLEINFO)->getNodeList();

        foreach ($titleInfos as $titleInfo) {
            $this->validateTitleInfo($titleInfo);
        }
    }

}
