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
use Slub\Dfgviewer\Validation\Mods\RelatedItemValidator;

class RelatedItemValidatorTest extends AbstractModsValidatorTest
{
    /**
     * Test validation against the rules of chapter "2.11 Beziehungen zu anderen Ressourcen"
     *
     * @return void
     */
    public function testRelatedItem(): void
    {
        $this->setAttributeValue(VH::XPATH_MODS_RELATEDITEM, 'type', 'Test');
        $this->hasMessageAttributeWithValue(self::MODS_BASEPATH . '/mods:relatedItem', 'type', 'Test');
        $this->resetDocument();

        $this->removeNodes(VH::XPATH_MODS_RELATEDITEM . '/mods:titleInfo');
        $this->hasMessageAny('mods:titleInfo', self::MODS_BASEPATH . '/mods:relatedItem');
        $this->resetDocument();

        // TODO validate mods:titleInfo reference

        // check mods:part
        $this->addChildNodeWithNamespace(VH::XPATH_MODS_RELATEDITEM, VH::NAMESPACE_MODS, 'mods:part');
        $this->hasMessageNoneOrOne('mods:part', self::MODS_BASEPATH . '/mods:relatedItem');
        $this->resetDocument();

        $this->removeNodes(VH::XPATH_MODS_RELATEDITEM . '/mods:part/mods:detail');
        $this->hasMessageAny("mods:detail", self::MODS_BASEPATH . '/mods:relatedItem/mods:part');
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MODS_RELATEDITEM . '/mods:part/mods:detail', 'type', 'Test');
        $this->hasMessageAttributeWithValue(self::MODS_BASEPATH . '/mods:relatedItem/mods:part/mods:detail', 'type', 'Test');
        $this->resetDocument();

        $this->addChildNodeWithNamespace(VH::XPATH_MODS_RELATEDITEM . '/mods:part', VH::NAMESPACE_MODS, 'mods:detail');
        $this->setAttributeValue(VH::XPATH_MODS_RELATEDITEM . '/mods:part/mods:detail', 'type', 'volume');
        $this->hasMessageOne('mods:detail[@type="volume"]', self::MODS_BASEPATH . '/mods:relatedItem/mods:part');
        $this->resetDocument();

        $this->removeNodes(VH::XPATH_MODS_RELATEDITEM . '/mods:part/mods:detail/mods:number');
        $this->hasMessageOne("mods:number", self::MODS_BASEPATH . '/mods:relatedItem/mods:part/mods:detail');
        $this->resetDocument();

        // check mods:recordInfo
        $this->addChildNodeWithNamespace(VH::XPATH_MODS_RELATEDITEM, VH::NAMESPACE_MODS, 'mods:recordInfo');
        $this->addChildNodeWithNamespace(VH::XPATH_MODS_RELATEDITEM, VH::NAMESPACE_MODS, 'mods:recordInfo');
        $this->hasMessageNoneOrOne('mods:recordInfo', self::MODS_BASEPATH . '/mods:relatedItem');
        $this->resetDocument();

        $this->addChildNodeWithNamespace(VH::XPATH_MODS_RELATEDITEM, VH::NAMESPACE_MODS, 'mods:recordInfo');
        $this->hasMessageOne('mods:recordIdentifier', self::MODS_BASEPATH . '/mods:relatedItem/mods:recordInfo');
    }

    protected function createValidator(): AbstractDlfValidator
    {
        return new RelatedItemValidator();
    }
}
