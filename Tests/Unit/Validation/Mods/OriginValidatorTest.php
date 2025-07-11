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
use Slub\Dfgviewer\Validation\Mods\OriginValidator;

class OriginValidatorTest extends AbstractModsValidatorTest
{
    /**
     * Test validation against the rules of chapter "2.4 Angaben zu Entstehung und Lebenszyklus"
     *
     * @return void
     */
    public function testOrigin(): void
    {
        $this->removeAttribute(VH::XPATH_MODS_ORIGININFO,'eventType');
        $this->hasMessageAttribute(self::MODS_BASEPATH . '/mods:originInfo', 'eventType');
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MODS_ORIGININFO,'eventType', 'Test');
        $this->addChildNodeWithNamespace(self::MODS_BASEPATH, VH::NAMESPACE_MODS, 'mods:originInfo');
        $this->setAttributeValue(VH::XPATH_MODS_ORIGININFO . '[not(@eventType)]','eventType', 'Test');
        $this->hasMessageOne('mods:originInfo[@eventType="Test"]', self::MODS_BASEPATH);
        $this->resetDocument();

        // check mods:place
        $this->removeNodes(VH::XPATH_MODS_ORIGININFO . '/mods:place/mods:placeTerm');
        $this->hasMessageAny('mods:placeTerm', self::MODS_BASEPATH  . '/mods:originInfo/mods:place');
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MODS_ORIGININFO . '/mods:place/mods:placeTerm','type', 'Test');
        $this->hasMessageAttributeWithValue(self::MODS_BASEPATH . '/mods:originInfo/mods:place/mods:placeTerm', 'type', 'Test');
        $this->resetDocument();

        // check mods:agent
        // TODO validate ref name

        // check dates
        $this->setAttributeValue(VH::XPATH_MODS_ORIGININFO . '/mods:dateIssued','qualifier', 'Test');
        $this->hasMessageAttributeWithValue(self::MODS_BASEPATH . '/mods:originInfo/mods:dateIssued', 'qualifier', 'Test');
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MODS_ORIGININFO . '/mods:dateCreated','point', 'Test');
        $this->hasMessageAttributeWithValue(self::MODS_BASEPATH . '/mods:originInfo/mods:dateCreated[1]', 'point', 'Test');
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MODS_ORIGININFO . '/mods:dateCreated','keyDate', 'Test');
        $this->hasMessageAttributeWithValue(self::MODS_BASEPATH . '/mods:originInfo/mods:dateCreated[1]', 'keyDate', 'Test');
        $this->setAttributeValue(VH::XPATH_MODS_ORIGININFO . '/mods:dateCreated','keyDate', 'yes');
        $this->removeAttribute(VH::XPATH_MODS_ORIGININFO . '/mods:dateCreated','encoding');
        $this->hasMessageAttribute(self::MODS_BASEPATH . '/mods:originInfo/mods:dateCreated[1]', 'encoding');
        $this->setAttributeValue(self::MODS_BASEPATH . '/mods:originInfo/mods:dateCreated[1]','encoding', 'Test');
        $this->hasMessageAttributeWithValue(self::MODS_BASEPATH . '/mods:originInfo/mods:dateCreated[1]', 'encoding', 'Test');
        $this->resetDocument();

        // check duplicate key date
        $this->setAttributeValue(VH::XPATH_MODS_ORIGININFO . '/mods:dateCreated','keyDate', 'yes');
        $this->setAttributeValue(VH::XPATH_MODS_ORIGININFO . '/mods:dateIssued','keyDate', 'yes');
        $this->hasMessageNoneOrOne('mods:dateIssued[@keyDate="yes"] | mods:dateCreated[@keyDate="yes"] | mods:dateValid[@keyDate="yes"] | mods:dateOther[@keyDate="yes"]', self::MODS_BASEPATH . '/mods:originInfo');
        $this->resetDocument();

        // check mods:edition
        $this->addChildNodeWithNamespace(self::MODS_BASEPATH . '/mods:originInfo', VH::NAMESPACE_MODS, 'mods:edition');
        $this->hasMessageNoneOrOne('mods:edition', self::MODS_BASEPATH . '/mods:originInfo');
    }

    protected function createValidator(): AbstractDlfValidator
    {
        return new OriginValidator();
    }
}
