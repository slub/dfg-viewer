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
use Slub\Dfgviewer\Validation\DvMetadataValidator;

class DvMetadataValidatorTestAbstract extends AbstractDomDocumentValidatorTest
{
    /**
     * Test validation against the rules of chapter "2.7.1 Rechteangaben – dv:rights"
     *
     * @return void
     */
    public function testDvRights(): void
    {
        $this->removeNodes(VH::XPATH_DVRIGHTS);
        $this->assertErrorHasOne(VH::XPATH_DVRIGHTS);
    }

    /**
     * Test validation against the rules of chapter "2.7.2 Unterelemente zu dv:rights"
     *
     * @return void
     * @throws \DOMException
     */
    public function testDvRightsSubelements(): void
    {
        $this->removeNodes(VH::XPATH_DVRIGHTS . '/dv:owner');
        $this->assertErrorHasOne(VH::XPATH_DVRIGHTS . '/dv:owner');
        $this->resetDocument();

        $this->assertNodeContent(VH::XPATH_DVRIGHTS . '/dv:ownerLogo', VH::trimDoubleSlash(VH::XPATH_ADMINISTRATIVE_RIGHTS_METADATA) . '/mets:mdWrap/mets:xmlData/dv:rights/dv:ownerLogo');
        $this->assertNodeContent(VH::XPATH_DVRIGHTS . '/dv:ownerSiteURL', VH::trimDoubleSlash(VH::XPATH_ADMINISTRATIVE_RIGHTS_METADATA) . '/mets:mdWrap/mets:xmlData/dv:rights/dv:ownerSiteURL');
        $this->assertNodeContent(VH::XPATH_DVRIGHTS . '/dv:ownerContact', VH::trimDoubleSlash(VH::XPATH_ADMINISTRATIVE_RIGHTS_METADATA) . '/mets:mdWrap/mets:xmlData/dv:rights/dv:ownerContact');
        $this->setContentValue(VH::XPATH_DVRIGHTS . '/dv:ownerContact', 'mailto:Test');
        $this->assertErrorHasContentWithEmail(VH::trimDoubleSlash(VH::XPATH_ADMINISTRATIVE_RIGHTS_METADATA) . '/mets:mdWrap/mets:xmlData/dv:rights/dv:ownerContact', 'mailto:Test');
        $this->resetDocument();

        $this->addChildNodeWithNamespace(VH::XPATH_DVRIGHTS, VH::NAMESPACE_DV, 'dv:aggregator');
        $this->assertErrorHasNoneOrOne(VH::XPATH_DVRIGHTS . '/dv:aggregator');
        $this->resetDocument();
        $this->assertOptionalNodeContent(VH::XPATH_DVRIGHTS, 'dv:aggregatorLogo', VH::trimDoubleSlash(VH::XPATH_ADMINISTRATIVE_RIGHTS_METADATA) . '/mets:mdWrap/mets:xmlData/dv:rights/dv:aggregatorLogo');
        $this->assertOptionalNodeContent(VH::XPATH_DVRIGHTS, 'dv:aggregatorSiteURL', VH::trimDoubleSlash(VH::XPATH_ADMINISTRATIVE_RIGHTS_METADATA) . '/mets:mdWrap/mets:xmlData/dv:rights/dv:aggregatorSiteURL');

        $this->addChildNodeWithNamespace(VH::XPATH_DVRIGHTS, VH::NAMESPACE_DV, 'dv:sponsor');
        $this->assertErrorHasNoneOrOne(VH::XPATH_DVRIGHTS . '/dv:sponsor');
        $this->resetDocument();
        $this->assertOptionalNodeContent(VH::XPATH_DVRIGHTS, 'dv:sponsorLogo', VH::trimDoubleSlash(VH::XPATH_ADMINISTRATIVE_RIGHTS_METADATA) . '/mets:mdWrap/mets:xmlData/dv:rights/dv:sponsorLogo');
        $this->assertOptionalNodeContent(VH::XPATH_DVRIGHTS, 'dv:sponsorSiteURL', VH::trimDoubleSlash(VH::XPATH_ADMINISTRATIVE_RIGHTS_METADATA) . '/mets:mdWrap/mets:xmlData/dv:rights/dv:sponsorSiteURL');

        $this->setContentValue(VH::XPATH_DVRIGHTS . '/dv:license', 'Test');
        $this->assertErrorHasContentWithUrl(VH::trimDoubleSlash(VH::XPATH_ADMINISTRATIVE_RIGHTS_METADATA) . '/mets:mdWrap/mets:xmlData/dv:rights/dv:license', 'Test');
    }

    /**
     * Test validation against the rules of chapter "2.7.3 Verweise – dv:links"
     *
     * @return void
     */
    public function testDvLinks(): void
    {
        $this->removeNodes(VH::XPATH_DVLINKS);
        $this->assertErrorHasOne(VH::XPATH_DVLINKS);
    }

    /**
     * Test validation against the rules of chapter "2.7.4 Unterelemente zu dv:links"
     *
     * @return void
     * @throws \DOMException
     */
    public function testDvLinksSubelements(): void
    {
        $this->removeNodes(VH::XPATH_DVLINKS . '/dv:reference');
        $this->assertErrorHasAny(VH::XPATH_DVLINKS . '/dv:reference');
        $this->resetDocument();

        // if there are multiple `dv:references`, the `linktext` attribute must be present.
        $this->addChildNodeWithNamespace(VH::XPATH_DVLINKS, VH::NAMESPACE_DV, 'dv:reference');
        $this->assertErrorHasAttribute(VH::trimDoubleSlash(VH::XPATH_ADMINISTRATIVE_DIGIPROV_METADATA) . '/mets:mdWrap/mets:xmlData/dv:links/dv:reference[1]', 'linktext');
        $this->resetDocument();

        $this->addChildNodeWithNamespace(VH::XPATH_DVLINKS, VH::NAMESPACE_DV, 'dv:presentation');
        $this->assertErrorHasNoneOrOne(VH::XPATH_DVLINKS . '/dv:presentation');
        $this->resetDocument();

        $this->addChildNodeWithNamespace(VH::XPATH_DVLINKS, VH::NAMESPACE_DV, 'dv:sru');
        $this->assertErrorHasNoneOrOne(VH::XPATH_DVLINKS . '/dv:sru');
        $this->resetDocument();

        $this->setContentValue(VH::XPATH_DVLINKS . '/dv:sru', 'Test');
        $this->assertErrorHasContentWithUrl(VH::trimDoubleSlash(VH::XPATH_ADMINISTRATIVE_DIGIPROV_METADATA) . '/mets:mdWrap/mets:xmlData/dv:links/dv:sru', 'Test');
        $this->resetDocument();

        $this->addChildNodeWithNamespace(VH::XPATH_DVLINKS, VH::NAMESPACE_DV, 'dv:iiif');
        $this->assertErrorHasNoneOrOne(VH::XPATH_DVLINKS . '/dv:iiif');
        $this->resetDocument();

        $this->setContentValue(VH::XPATH_DVLINKS . '/dv:iiif', 'Test');
        $this->assertErrorHasContentWithUrl(VH::trimDoubleSlash(VH::XPATH_ADMINISTRATIVE_DIGIPROV_METADATA) . '/mets:mdWrap/mets:xmlData/dv:links/dv:iiif', 'Test');
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
        $this->addChildNodeWithNamespace($expression, VH::NAMESPACE_DV, $name);
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
