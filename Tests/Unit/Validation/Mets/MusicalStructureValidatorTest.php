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
use Slub\Dfgviewer\Validation\Mets\MusicalStructureValidator;

class MusicalStructureValidatorTest extends AbstractDomDocumentValidatorTest
{
    /**
     * Test validation against the rules of chapter "2.3.1 Musical structure - mets:structMap"
     *
     * @return void
     * @throws \DOMException
     */
    public function testMultipleMusicalDivisions(): void
    {
        $node = $this->doc->createElementNS(VH::NAMESPACE_METS, 'mets:structMap');
        $node->setAttribute('TYPE', 'MUSICAL');
        $this->addChildNode('/mets:mets', $node);
        $this->hasErrorNoneOrOne(VH::XPATH_MUSICAL_STRUCTURES);
    }

    protected function createValidator(): AbstractDlfValidator
    {
        return new MusicalStructureValidator();
    }
}
