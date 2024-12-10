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
use Slub\Dfgviewer\Validation\Mets\LinkingLogicalPhysicalStructureValidator;

class LinkingLogicalPhysicalStructureValidatorTest extends ApplicationProfileValidatorTest
{

    /**
     * Test validation against the rules of chapter "2.3.1 Structure links - mets:structLink"
     *
     * @return void
     */
    public function testMultipleStructLinks(): void
    {
        $this->addChildNodeNS('/mets:mets', self::NAMESPACE_METS, 'mets:structLink');
        $this->validateAndAssertEquals('Every METS file has to have no or one struct link element.');
    }

    protected function createValidator(): AbstractDlfValidator
    {
        return new LinkingLogicalPhysicalStructureValidator();
    }
}
