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
use Slub\Dfgviewer\Validation\Mods\SubjectsValidator;

class SubjectValidatorTest extends AbstractModsValidatorTest
{
    /**
     * Test validation against the rules of chapter "2.9 Schlagwörter"
     *
     * @return void
     */
    public function testSubjects(): void
    {
        $this->checkUriAttributes(VH::XPATH_MODS_SUBJECT, self::MODS_BASEPATH . '/mods:subject[1]', ['authorityURI']);

        // check subelements
        $this->checkUriAttributes(VH::XPATH_MODS_SUBJECT . '/mods:topic', self::MODS_BASEPATH . '/mods:subject[1]/mods:topic', ['valueURI']);

        // TODO validate ref title info
        $this->setAttributeValue(VH::XPATH_MODS_SUBJECT . '/mods:titleInfo', 'nameTitleGroup', '0');
        $this->hasMessageOne('mods:name[@nameTitleGroup="0"]', self::MODS_BASEPATH . '/mods:subject[2]', SeverityLevel::NOTICE);
        $this->resetDocument();

        // TODO validate ref name
        $this->setAttributeValue(VH::XPATH_MODS_SUBJECT . '/mods:name', 'nameTitleGroup', '0');
        $this->hasMessageOne('mods:titleInfo[@nameTitleGroup="0"]', self::MODS_BASEPATH . '/mods:subject[2]', SeverityLevel::NOTICE);
    }

    protected function createValidator(): AbstractDlfValidator
    {
        return new SubjectsValidator();
    }
}
