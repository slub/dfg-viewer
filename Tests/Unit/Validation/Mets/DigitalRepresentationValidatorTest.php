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
use Slub\Dfgviewer\Validation\Mets\DigitalRepresentationValidator;
use Slub\Dfgviewer\Validation\Mets\PhysicalStructureValidator;

class DigitalRepresentationValidatorTest extends ApplicationProfileValidatorTest
{

    /**
     * Test validation against the rules of chapter "2.4.1 Dateisektion – mets:fileSec"
     *
     * @return void
     */
    public function testFileSections(): void
    {
        $this->addChildNodeNS('/mets:mets', self::NAMESPACE_METS, 'mets:fileSec');
        $this->assertErrorHasNoneOrOne(DigitalRepresentationValidator::XPATH_FILE_SECTIONS);
        $this->resetDocument();

        $this->removeNodes(DigitalRepresentationValidator::XPATH_FILE_SECTIONS);
        $this->assertErrorHasOne(DigitalRepresentationValidator::XPATH_FILE_SECTIONS);
        $this->resetDocument();

        $this->removeNodes(PhysicalStructureValidator::XPATH_PHYSICAL_STRUCTURES);
        $this->removeNodes(DigitalRepresentationValidator::XPATH_FILE_SECTIONS);
        $this->assertNoError();
    }

    /**
     * Test validation against the rules of chapter "2.4.2.1 Dateigruppen – mets:fileGrp"
     *
     * @return void
     */
    public function testFileGroups(): void
    {
        $this->removeNodes(DigitalRepresentationValidator::XPATH_FILE_GROUPS);
        $this->assertErrorHasAny(DigitalRepresentationValidator::XPATH_FILE_GROUPS);
        $this->resetDocument();

        $this->removeNodes(DigitalRepresentationValidator::XPATH_FILE_GROUPS . '[@USE="DEFAULT"]');
        $this->assertErrorHasOne(DigitalRepresentationValidator::XPATH_FILE_GROUPS . '[@USE="DEFAULT"]');
        $this->resetDocument();

        $this->setAttributeValue(DigitalRepresentationValidator::XPATH_FILE_GROUPS . '[@USE="THUMBS"]', 'USE', 'DEFAULT');
        $this->assertErrorHasUniqueAttribute('/mets:mets/mets:fileSec/mets:fileGrp[1]', 'USE', 'DEFAULT');
    }

    /**
     * Test validation against the rules of chapter "2.4.2.2 Datei – mets:fileGrp/mets:file" and "2.4.2.3 Dateilink – mets:fileGrp/mets:file/mets:FLocat"
     *
     * @return void
     */
    public function testFiles(): void
    {
        $this->removeNodes(DigitalRepresentationValidator::XPATH_FILES);
        $this->assertErrorHasAny(DigitalRepresentationValidator::XPATH_FILES);
        $this->resetDocument();

        $this->setAttributeValue(DigitalRepresentationValidator::XPATH_FILES, 'ID', 'DMDLOG_0001');
        $this->assertErrorHasUniqueId('/mets:mets/mets:fileSec/mets:fileGrp[1]/mets:file', 'DMDLOG_0001');
        $this->resetDocument();

        $this->removeAttribute(DigitalRepresentationValidator::XPATH_FILES, 'MIMETYPE');
        $this->assertErrorHasAttribute('/mets:mets/mets:fileSec/mets:fileGrp[1]/mets:file', 'MIMETYPE');
        $this->resetDocument();

        $this->removeNodes(DigitalRepresentationValidator::XPATH_FILES . '/mets:FLocat');
        $this->assertErrorHasOne('mets:FLocat', '/mets:mets/mets:fileSec/mets:fileGrp[1]/mets:file');
        $this->resetDocument();

        $this->removeAttribute(DigitalRepresentationValidator::XPATH_FILES . '/mets:FLocat', 'LOCTYPE');
        $this->assertErrorHasAttribute('/mets:mets/mets:fileSec/mets:fileGrp[1]/mets:file/mets:FLocat', 'LOCTYPE');
        $this->resetDocument();

        $this->setAttributeValue(DigitalRepresentationValidator::XPATH_FILES . '/mets:FLocat', 'LOCTYPE', 'Test');
        $this->assertErrorHasAttributeWithValue('/mets:mets/mets:fileSec/mets:fileGrp[1]/mets:file/mets:FLocat', 'LOCTYPE','Test');
        $this->resetDocument();

        $this->removeAttribute(DigitalRepresentationValidator::XPATH_FILES . '/mets:FLocat', 'xlink:href');
        $this->assertErrorHasAttribute('/mets:mets/mets:fileSec/mets:fileGrp[1]/mets:file/mets:FLocat', 'xlink:href');

        $this->setAttributeValue(DigitalRepresentationValidator::XPATH_FILES . '/mets:FLocat', 'xlink:href', 'Test');
        $this->assertErrorHasAttributeWithUrl('/mets:mets/mets:fileSec/mets:fileGrp[1]/mets:file/mets:FLocat', 'xlink:href','Test');
    }

    protected function createValidator(): AbstractDlfValidator
    {
        return new DigitalRepresentationValidator();
    }
}
