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

class AbstractModsValidatorTest extends AbstractDomDocumentValidatorTest
{
    const MODS_BASEPATH = '/mets:mets/mets:dmdSec/mets:mdWrap/mets:xmlData/mods:mods';

    protected function checkUriAttributes(string $expression, string $expectedExpression, array $attributes=['authorityURI', 'valueURI']): void
    {
        foreach ($attributes as $attribute) {
            $this->setAttributeValue($expression, $attribute, 'Test');
            $this->hasMessageUrlAttribute($expectedExpression, $attribute, 'Test');
            $this->resetDocument();
        }
    }
}
