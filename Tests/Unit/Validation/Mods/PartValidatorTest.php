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
use Slub\Dfgviewer\Validation\Mods\PartValidator;

class PartValidatorTest extends AbstractModsValidatorTest
{
    /**
     * Test validation against the rules of chapter "2.15 Angabe von Bänden und anderen Teilen"
     *
     * @return void
     */
    public function testPart(): void
    {
        $this->addChildNodeWithNamespace(VH::XPATH_MODS, VH::NAMESPACE_MODS, 'mods:part');
        $this->addChildNodeWithNamespace(VH::XPATH_MODS, VH::NAMESPACE_MODS, 'mods:part');
        $this->hasMessageNoneOrOne('//mods:mods/mods:part');
        $this->resetDocument();

        $this->removeNodes(VH::XPATH_MODS_PART);
        $this->setAttributeValue(VH::XPATH_MODS_RELATEDITEM, 'type', 'host');
        $this->hasMessageOne('//mods:mods/mods:part');
        $this->resetDocument();

        $this->removeAttribute(VH::XPATH_MODS_PART, 'order');
        $this->hasMessageAttribute(self::MODS_BASEPATH . '/mods:part', 'order');
        $this->setAttributeValue(VH::XPATH_MODS_PART, 'order', '-1');
        $this->validateAndAssertEquals('Value "-1" in the "order" attribute of "' . self::MODS_BASEPATH . '/mods:part" is not a positiv integer.');
        $this->resetDocument();

        // check mods:detail
        $this->removeNodes(VH::XPATH_MODS_PART .  '/mods:detail');
        $this->hasMessageAny('mods:detail', self::MODS_BASEPATH . '/mods:part');
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MODS_PART.  '/mods:detail', 'type', 'Test');
        $this->hasMessageAttributeWithValue(self::MODS_BASEPATH . '/mods:part/mods:detail', 'type', 'Test');
        $this->resetDocument();

        $this->removeNodes(VH::XPATH_MODS_PART .  '/mods:detail/mods:number');
        $this->hasMessageOne('mods:number', self::MODS_BASEPATH . '/mods:part/mods:detail');
    }

    protected function createValidator(): AbstractDlfValidator
    {
        return new PartValidator();
    }
}
