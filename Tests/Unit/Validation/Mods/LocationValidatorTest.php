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
use Slub\Dfgviewer\Validation\Common\SeverityLevel;
use Slub\Dfgviewer\Validation\Mods\LocationValidator;

class LocationValidatorTest extends AbstractModsValidatorTest
{
    /**
     * Test validation against the rules of chapter "2.13 Zugang zur Ressource"
     *
     * @return void
     */
    public function testLocation(): void
    {
        $this->addChildNodeWithNamespace(VH::XPATH_MODS_LOCATION, VH::NAMESPACE_MODS, 'mods:physicalLocation');
        $this->hasMessageNoneOrOne('mods:physicalLocation', self::MODS_BASEPATH . '/mods:location');
        $this->resetDocument();

        $this->removeNodes(VH::XPATH_MODS_LOCATION . '/mods:physicalLocation');
        $this->removeNodes(VH::XPATH_MODS_LOCATION . '/mods:url');
        $this->hasMessageAny('mods:url | mods:physicalLocation', self::MODS_BASEPATH . '/mods:location');
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MODS_LOCATION . '/mods:url', 'access', 'Test');
        $this->hasMessageAttributeWithValue(self::MODS_BASEPATH . '/mods:location/mods:url', 'access', 'Test', SeverityLevel::NOTICE);
        $this->resetDocument();

        $this->addChildNodeWithNamespace(VH::XPATH_MODS_LOCATION, VH::NAMESPACE_MODS, 'mods:shelfLocator');
        $this->hasMessageNoneOrOne('mods:shelfLocator', self::MODS_BASEPATH . '/mods:location');
    }

    protected function createValidator(): AbstractDlfValidator
    {
        return new LocationValidator();
    }
}
