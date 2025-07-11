<?php

namespace Slub\Dfgviewer\Tests\Unit\Validation;

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

use Kitodo\Dlf\Validation\AbstractDlfValidator;
use Slub\Dfgviewer\Common\ValidationHelper as VH;
use Slub\Dfgviewer\Validation\Mods\TitleValidator;

class TitleValidatorTest extends AbstractModsValidatorTest
{
    /**
     * Test validation against the rules of chapter "2.1 Titel"
     *
     * @return void
     */
    public function testTitle(): void
    {
        $this->setAttributeValue(VH::XPATH_MODS_TITLEINFO, 'type', 'alternative');
        $this->hasMessageOne(VH::XPATH_MODS_TITLEINFO . '[not(@type)]');

        // validate title info
        // add empty title info to prevent error of not existing none type mods:titleInfos
        $this->addChildNodeWithNamespace(self::MODS_BASEPATH, VH::NAMESPACE_MODS, 'mods:titleInfo');
        $this->setAttributeValue(VH::XPATH_MODS_TITLEINFO . '[@type="alternative"]', 'type', 'Test');
        $this->hasMessageAttributeWithValue(self::MODS_BASEPATH . '/mods:titleInfo[1]', 'type', 'Test');
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MODS_TITLEINFO, 'lang', 'Test');
        $this->hasMessageAttributeWithIso6392B(self::MODS_BASEPATH . '/mods:titleInfo', 'lang', 'Test');
        $this->setAttributeValue(VH::XPATH_MODS_TITLEINFO, 'lang', 'eng');
        $this->hasNoMessage();
        $this->resetDocument();

        // validate title info sub elements
        $this->removeNodes(VH::XPATH_MODS_TITLEINFO . '/mods:title');
        $this->hasMessageOne('mods:title', self::MODS_BASEPATH . '/mods:titleInfo');
    }

    protected function createValidator(): AbstractDlfValidator
    {
        return new TitleValidator();
    }
}
