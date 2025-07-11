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
use Slub\Dfgviewer\Validation\Mods\LanguageValidator;

class LanguageValidatorTest extends AbstractModsValidatorTest
{
    /**
     * Test validation against the rules of chapter "2.5 Sprache und Schrift"
     *
     * @return void
     */
    public function testLanguage(): void
    {
        $this->removeNodes(VH::XPATH_MODS_LANGUAGE . '/mods:languageTerm');
        $this->hasMessageAny('mods:languageTerm', self::MODS_BASEPATH . '/mods:language');
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MODS_LANGUAGE . '/mods:languageTerm', 'type', 'Test');
        $this->hasMessageAttributeWithValue(self::MODS_BASEPATH . '/mods:language/mods:languageTerm', 'type', 'Test');
        $this->resetDocument();

        $this->setContentValue(VH::XPATH_MODS_LANGUAGE . '/mods:languageTerm', 'Test');
        $this->hasMessageIso6392BContent(self::MODS_BASEPATH . '/mods:language/mods:languageTerm', 'Test', SeverityLevel::NOTICE);
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_MODS_LANGUAGE . '/mods:scriptTerm', 'type', 'Test');
        $this->hasMessageAttributeWithValue(self::MODS_BASEPATH . '/mods:language/mods:scriptTerm', 'type', 'Test');
        $this->resetDocument();

        $this->setContentValue(VH::XPATH_MODS_LANGUAGE . '/mods:scriptTerm', 'Test');
        $this->hasMessageIso15924Content(self::MODS_BASEPATH . '/mods:language/mods:scriptTerm', 'Test', SeverityLevel::NOTICE);
    }

    protected function createValidator(): AbstractDlfValidator
    {
        return new LanguageValidator();
    }
}
