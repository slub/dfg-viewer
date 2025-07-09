<?php

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

namespace Slub\Dfgviewer\Tests\Unit\Validation;

use Kitodo\Dlf\Validation\AbstractDlfValidator;
use Slub\Dfgviewer\Common\ValidationHelper;
use Slub\Dfgviewer\Validation\MetsUrlExistenceValidator;

class MetsUrlExistenceValidatorTest extends AbstractDomDocumentValidatorTest
{

    public function testBrokenUrlError(): void
    {
        // check a not existing notExistingUrl
        $notExistingUrl = 'http://c6fc9656-6c9f-4a62-bbe0-40522748192a.test/';
        $this->setAttributeValue(ValidationHelper::XPATH_FILE_SECTION_FILES . '/mets:FLocat','xlink:href', $notExistingUrl);
        $this->validateAndAssertEquals('URL "'.$notExistingUrl.'" could not be found.');

        $notFoundUrl = 'https://httpbin.org/status/404';
        $this->setAttributeValue(ValidationHelper::XPATH_FILE_SECTION_FILES . '/mets:FLocat','xlink:href', $notFoundUrl);
        $this->validateAndAssertEquals('URL "'.$notFoundUrl.'" could not be found.');

        $this->setAttributeValue(ValidationHelper::XPATH_FILE_SECTION_FILES . '/mets:FLocat','xlink:href', 'https://picsum.photos/1');
        $this->hasNoMessage();
    }

    protected function createValidator(): AbstractDlfValidator
    {
        // ignore all urls of fixture xml to test specifically
        $excludeHosts = 'www.loc.gov,id.loc.gov,example.com,dfg-viewer.de,www.w3.org';
        return new MetsUrlExistenceValidator(['excludeHosts' => $excludeHosts]);
    }
}
