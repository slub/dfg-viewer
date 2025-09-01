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
use Slub\Dfgviewer\Validation\Mods\NameValidator;

class NameValidatorTest extends AbstractModsValidatorTest
{
    /**
     * Test validation against the rules of chapter "2.2 Namen von Personen oder Körperschaften"
     *
     * @return void
     */
    public function testName(): void
    {
        // validate name
        $this->setAttributeValue(VH::XPATH_MODS_NAMES, 'type', 'Test');
        $this->hasMessageAttributeWithValue(self::MODS_BASEPATH . '/mods:name[1]', 'type', 'Test');
        $this->resetDocument();

        $this->removeAttribute(VH::XPATH_MODS_NAMES . '[@type="personal"]', 'valueURI');
        $this->hasMessageAttribute(self::MODS_BASEPATH . '/mods:name[1]', 'valueURI', SeverityLevel::NOTICE);
        $this->checkUriAttributes(VH::XPATH_MODS_NAMES . '[@type="personal"]', self::MODS_BASEPATH . '/mods:name[1]');

        // validate name subelements
        // check mods:namePart
        $this->removeNodes(VH::XPATH_MODS_NAMES . '/mods:namePart');
        $this->hasMessageAny('mods:namePart', self::MODS_BASEPATH . '/mods:name[1]');
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MODS_NAMES . '[@type="personal"]/mods:namePart', 'type', 'Test');
        $this->hasMessageAttributeWithValue(self::MODS_BASEPATH . '/mods:name[1]/mods:namePart[1]', 'type', 'Test');
        $this->setAttributeValue(VH::XPATH_MODS_NAMES . '[@type="personal"]/mods:namePart', 'type', 'family');
        $this->hasMessageOne('mods:namePart[@type="family"]', self::MODS_BASEPATH . '/mods:name[1]');
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MODS_NAMES . '[@type="corporate"]/mods:namePart', 'type', 'Test');
        $this->hasMessageNoneAttribute(self::MODS_BASEPATH . '/mods:name[2]/mods:namePart', 'type');
        $this->resetDocument();

        // check mods:displayForm
        $this->addChildNodeWithNamespace(VH::XPATH_MODS_NAMES . '[@type="personal"]', VH::NAMESPACE_MODS, 'mods:displayForm');
        $this->hasMessageNoneOrOne('mods:displayForm', self::MODS_BASEPATH . '/mods:name[1]');
        $this->resetDocument();

        // check mods:role
        $this->removeNodes(VH::XPATH_MODS_NAMES . '/mods:role');
        $this->hasMessageAny('mods:role', self::MODS_BASEPATH . '/mods:name[1]');
        $this->resetDocument();

        // checko mods:role/mods:roleTerm
        $this->removeNodes(VH::XPATH_MODS_NAMES . '/mods:role/mods:roleTerm');
        $this->hasMessageAny('mods:roleTerm', self::MODS_BASEPATH . '/mods:name[1]/mods:role');
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MODS_NAMES . '/mods:role/mods:roleTerm', 'type', 'Test');
        $this->hasMessageOne('mods:roleTerm[@type="code"]', self::MODS_BASEPATH . '/mods:name[1]/mods:role');
        $this->resetDocument();

        $this->addChildNodeWithNamespace(VH::XPATH_MODS_NAMES . '[@type="personal"]/mods:role', VH::NAMESPACE_MODS, 'mods:roleTerm');
        $this->setAttributeValue(VH::XPATH_MODS_NAMES . '[@type="personal"]/mods:role/mods:roleTerm[not(@type)]', 'type', 'Test');
        $this->hasMessageAttributeWithValue(self::MODS_BASEPATH . '/mods:name[1]/mods:role/mods:roleTerm[2]', 'type', 'Test');
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MODS_NAMES . '/mods:role/mods:roleTerm[@type="code"]', 'authority', 'Test');
        $this->hasMessageAttributeWithValue(self::MODS_BASEPATH . '/mods:name[1]/mods:role/mods:roleTerm', 'authority', 'Test',SeverityLevel::NOTICE);
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MODS_NAMES . '/mods:role/mods:roleTerm[@type="code"]', 'authorityURI', 'Test');
        $this->hasMessageAttributeWithValue(self::MODS_BASEPATH . '/mods:name[1]/mods:role/mods:roleTerm', 'authorityURI', 'Test',SeverityLevel::NOTICE);
        $this->resetDocument();

        $this->checkUriAttributes(VH::XPATH_MODS_NAMES . '/mods:role/mods:roleTerm', self::MODS_BASEPATH . '/mods:name[1]/mods:role/mods:roleTerm');
    }

    protected function createValidator(): AbstractDlfValidator
    {
        return new NameValidator();
    }
}
