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
use Slub\Dfgviewer\Validation\Mets\DigitalRepresentationValidator;

class DigitalRepresentationValidatorTest extends AbstractDomDocumentValidatorTest
{
    /**
     * Test validation against the rules of chapter "2.4.1 Dateisektion – mets:fileSec"
     *
     * @return void
     * @throws \DOMException
     */
    public function testFileSections(): void
    {
        $this->addChildNodeWithNamespace('/mets:mets', VH::NAMESPACE_METS, 'mets:fileSec');
        $this->assertErrorHasNoneOrOne(VH::XPATH_FILE_SECTIONS);
        $this->resetDocument();

        $this->removeNodes(VH::XPATH_FILE_SECTIONS);
        $this->assertErrorHasOne(VH::XPATH_FILE_SECTIONS);
        $this->resetDocument();

        $this->removeNodes(VH::XPATH_PHYSICAL_STRUCTURES);
        $this->removeNodes(VH::XPATH_FILE_SECTIONS);
        $this->assertNoError();
    }

    /**
     * Test validation against the rules of chapter "2.4.2.1 Dateigruppen – mets:fileGrp"
     *
     * @return void
     */
    public function testFileGroups(): void
    {
        $this->removeNodes(VH::XPATH_FILE_SECTION_GROUPS);
        $this->assertErrorHasAny(VH::XPATH_FILE_SECTION_GROUPS);
        $this->resetDocument();

        $this->removeNodes(VH::XPATH_FILE_SECTION_GROUPS . '[@USE="DEFAULT"]');
        $this->assertErrorHasOne(VH::XPATH_FILE_SECTION_GROUPS . '[@USE="DEFAULT"]');
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_FILE_SECTION_GROUPS . '[@USE="THUMBS"]', 'USE', 'DEFAULT');
        $this->assertErrorHasUniqueAttribute(VH::trimDoubleSlash(VH::XPATH_FILE_SECTION_GROUPS) . '[1]', 'USE', 'DEFAULT');
    }

    /**
     * Test validation against the rules of chapter "2.4.2.2 Datei – mets:fileGrp/mets:file" and "2.4.2.3 Dateilink – mets:fileGrp/mets:file/mets:FLocat"
     *
     * @return void
     */
    public function testFiles(): void
    {
        $this->removeNodes(VH::XPATH_FILE_SECTION_FILES);
        $this->assertErrorHasAny(VH::XPATH_FILE_SECTION_FILES);
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_FILE_SECTION_FILES, 'ID', 'DMDLOG_0001');
        $this->assertErrorHasUniqueId(VH::trimDoubleSlash(VH::XPATH_FILE_SECTION_GROUPS) . '[1]/mets:file', 'DMDLOG_0001');
        $this->resetDocument();

        $this->removeAttribute(VH::XPATH_FILE_SECTION_FILES, 'MIMETYPE');
        $this->assertErrorHasAttribute(VH::trimDoubleSlash(VH::XPATH_FILE_SECTION_GROUPS) . '[1]/mets:file', 'MIMETYPE');
        $this->resetDocument();

        $this->removeNodes(VH::XPATH_FILE_SECTION_FILES . '/mets:FLocat');
        $this->assertErrorHasOne('mets:FLocat', VH::trimDoubleSlash(VH::XPATH_FILE_SECTION_GROUPS) . '[1]/mets:file');
        $this->resetDocument();

        $this->removeAttribute(VH::XPATH_FILE_SECTION_FILES . '/mets:FLocat', 'LOCTYPE');
        $this->assertErrorHasAttribute(VH::trimDoubleSlash(VH::XPATH_FILE_SECTION_GROUPS) . '[1]/mets:file/mets:FLocat', 'LOCTYPE');
        $this->resetDocument();

        $this->setAttributeValue(VH::XPATH_FILE_SECTION_FILES . '/mets:FLocat', 'LOCTYPE', 'Test');
        $this->assertErrorHasAttributeWithValue(VH::trimDoubleSlash(VH::XPATH_FILE_SECTION_GROUPS) . '[1]/mets:file/mets:FLocat', 'LOCTYPE', 'Test');
        $this->resetDocument();

        $this->removeAttribute(VH::XPATH_FILE_SECTION_FILES . '/mets:FLocat', 'xlink:href');
        $this->assertErrorHasAttribute(VH::trimDoubleSlash(VH::XPATH_FILE_SECTION_GROUPS) . '[1]/mets:file/mets:FLocat', 'xlink:href');

        $this->setAttributeValue(VH::XPATH_FILE_SECTION_FILES . '/mets:FLocat', 'xlink:href', 'Test');
        $this->assertErrorHasAttributeWithUrl(VH::trimDoubleSlash(VH::XPATH_FILE_SECTION_GROUPS) . '[1]/mets:file/mets:FLocat', 'xlink:href', 'Test');
    }

    protected function createValidator(): AbstractDlfValidator
    {
        return new DigitalRepresentationValidator();
    }
}
