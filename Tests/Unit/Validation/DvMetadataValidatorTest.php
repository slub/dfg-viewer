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
use Slub\Dfgviewer\Common\ValidationHelper;
use Slub\Dfgviewer\Validation\DvMetadataValidator;

class DvMetadataValidatorTest extends ApplicationProfileValidatorTest
{
    /**
     * Test validation against the rules of chapter "2.7.1 Rechteangaben – dv:rights"
     *
     * @return void
     */
    public function testDvRights(): void
    {
        $this->removeNodes(ValidationHelper::XPATH_DVRIGHTS);
        $this->assertErrorHasOne(ValidationHelper::XPATH_DVRIGHTS);
    }

    /**
     * Test validation against the rules of chapter "2.7.2 Unterelemente zu dv:rights"
     *
     * @return void
     */
    public function testDvRightsSubelements(): void
    {
        $this->removeNodes(ValidationHelper::XPATH_DVRIGHTS . '/dv:owner');
        $this->assertErrorHasOne(ValidationHelper::XPATH_DVRIGHTS . '/dv:owner');
        $this->resetDocument();

        $this->assertNodeContent(ValidationHelper::XPATH_DVRIGHTS . '/dv:ownerLogo', $this->trimDoubleSlash(ValidationHelper::XPATH_ADMINISTRATIVE_RIGHTS_METADATA) . '/mets:mdWrap/mets:xmlData/dv:rights/dv:ownerLogo');
        $this->assertNodeContent(ValidationHelper::XPATH_DVRIGHTS . '/dv:ownerSiteURL', $this->trimDoubleSlash(ValidationHelper::XPATH_ADMINISTRATIVE_RIGHTS_METADATA) . '/mets:mdWrap/mets:xmlData/dv:rights/dv:ownerSiteURL');
        $this->assertNodeContent(ValidationHelper::XPATH_DVRIGHTS . '/dv:ownerContact', $this->trimDoubleSlash(ValidationHelper::XPATH_ADMINISTRATIVE_RIGHTS_METADATA) . '/mets:mdWrap/mets:xmlData/dv:rights/dv:ownerContact');
        $this->setContentValue(ValidationHelper::XPATH_DVRIGHTS . '/dv:ownerContact', 'mailto:Test');
        $this->assertErrorHasContentWithEmail($this->trimDoubleSlash(ValidationHelper::XPATH_ADMINISTRATIVE_RIGHTS_METADATA) . '/mets:mdWrap/mets:xmlData/dv:rights/dv:ownerContact', 'mailto:Test');
        $this->resetDocument();

        $this->addChildNodeWithNamespace(ValidationHelper::XPATH_DVRIGHTS, self::NAMESPACE_DV, 'dv:aggregator');
        $this->assertErrorHasNoneOrOne(ValidationHelper::XPATH_DVRIGHTS . '/dv:aggregator');
        $this->resetDocument();
        $this->assertOptionalNodeContent(ValidationHelper::XPATH_DVRIGHTS, 'dv:aggregatorLogo', $this->trimDoubleSlash(ValidationHelper::XPATH_ADMINISTRATIVE_RIGHTS_METADATA) . '/mets:mdWrap/mets:xmlData/dv:rights/dv:aggregatorLogo');
        $this->assertOptionalNodeContent(ValidationHelper::XPATH_DVRIGHTS, 'dv:aggregatorSiteURL', $this->trimDoubleSlash(ValidationHelper::XPATH_ADMINISTRATIVE_RIGHTS_METADATA) . '/mets:mdWrap/mets:xmlData/dv:rights/dv:aggregatorSiteURL');

        $this->addChildNodeWithNamespace(ValidationHelper::XPATH_DVRIGHTS, self::NAMESPACE_DV, 'dv:sponsor');
        $this->assertErrorHasNoneOrOne(ValidationHelper::XPATH_DVRIGHTS . '/dv:sponsor');
        $this->resetDocument();
        $this->assertOptionalNodeContent(ValidationHelper::XPATH_DVRIGHTS, 'dv:sponsorLogo', $this->trimDoubleSlash(ValidationHelper::XPATH_ADMINISTRATIVE_RIGHTS_METADATA) . '/mets:mdWrap/mets:xmlData/dv:rights/dv:sponsorLogo');
        $this->assertOptionalNodeContent(ValidationHelper::XPATH_DVRIGHTS, 'dv:sponsorSiteURL', $this->trimDoubleSlash(ValidationHelper::XPATH_ADMINISTRATIVE_RIGHTS_METADATA) . '/mets:mdWrap/mets:xmlData/dv:rights/dv:sponsorSiteURL');

        $this->setContentValue(ValidationHelper::XPATH_DVRIGHTS . '/dv:license', 'Test');
        $this->assertErrorHasContentWithUrl($this->trimDoubleSlash(ValidationHelper::XPATH_ADMINISTRATIVE_RIGHTS_METADATA) . '/mets:mdWrap/mets:xmlData/dv:rights/dv:license', 'Test');
    }

    /**
     * Test validation against the rules of chapter "2.7.3 Verweise – dv:links"
     *
     * @return void
     */
    public function testDvLinks(): void
    {
        $this->removeNodes(ValidationHelper::XPATH_DVLINKS);
        $this->assertErrorHasOne(ValidationHelper::XPATH_DVLINKS);
    }

    /**
     * Test validation against the rules of chapter "2.7.4 Unterelemente zu dv:links"
     *
     * @return void
     */
    public function testDvLinksSubelements(): void
    {
        $this->removeNodes(ValidationHelper::XPATH_DVLINKS . '/dv:reference');
        $this->assertErrorHasAny(ValidationHelper::XPATH_DVLINKS . '/dv:reference');
        $this->resetDocument();

        // if there are multiple `dv:references`, the `linktext` attribute must be present.
        $this->addChildNodeWithNamespace(ValidationHelper::XPATH_DVLINKS, self::NAMESPACE_DV, 'dv:reference');
        $this->assertErrorHasAttribute($this->trimDoubleSlash(ValidationHelper::XPATH_ADMINISTRATIVE_DIGIPROV_METADATA) . '/mets:mdWrap/mets:xmlData/dv:links/dv:reference[1]', 'linktext');
        $this->resetDocument();

        $this->addChildNodeWithNamespace(ValidationHelper::XPATH_DVLINKS, self::NAMESPACE_DV, 'dv:presentation');
        $this->assertErrorHasNoneOrOne(ValidationHelper::XPATH_DVLINKS . '/dv:presentation');
        $this->resetDocument();

        $this->addChildNodeWithNamespace(ValidationHelper::XPATH_DVLINKS, self::NAMESPACE_DV, 'dv:sru');
        $this->assertErrorHasNoneOrOne(ValidationHelper::XPATH_DVLINKS . '/dv:sru');
        $this->resetDocument();

        $this->setContentValue(ValidationHelper::XPATH_DVLINKS . '/dv:sru', 'Test');
        $this->assertErrorHasContentWithUrl($this->trimDoubleSlash(ValidationHelper::XPATH_ADMINISTRATIVE_DIGIPROV_METADATA) . '/mets:mdWrap/mets:xmlData/dv:links/dv:sru', 'Test');
        $this->resetDocument();

        $this->addChildNodeWithNamespace(ValidationHelper::XPATH_DVLINKS, self::NAMESPACE_DV, 'dv:iiif');
        $this->assertErrorHasNoneOrOne(ValidationHelper::XPATH_DVLINKS . '/dv:iiif');
        $this->resetDocument();

        $this->setContentValue(ValidationHelper::XPATH_DVLINKS . '/dv:iiif', 'Test');
        $this->assertErrorHasContentWithUrl($this->trimDoubleSlash(ValidationHelper::XPATH_ADMINISTRATIVE_DIGIPROV_METADATA) . '/mets:mdWrap/mets:xmlData/dv:links/dv:iiif', 'Test');
    }

    protected function assertNodeContent(string $expression, string $expectedErrorExpression): void
    {
        $this->removeNodes($expression);
        $this->assertErrorHasOne($expression);
        $this->resetDocument();

        $this->setContentValue($expression, 'Test');
        $this->assertErrorHasContentWithUrl($expectedErrorExpression, 'Test');
        $this->resetDocument();
    }

    protected function assertOptionalNodeContent(string $expression, string $name, string $expectedErrorExpression): void
    {
        $this->addChildNodeWithNamespace($expression, self::NAMESPACE_DV, $name);
        $this->assertErrorHasNoneOrOne($expression . '/' . $name);
        $this->resetDocument();

        $this->setContentValue($expression . '/' . $name, 'Test');
        $this->assertErrorHasContentWithUrl($expectedErrorExpression, 'Test');
        $this->resetDocument();
    }

    protected function createValidator(): AbstractDlfValidator
    {
        return new DvMetadataValidator();
    }
}
