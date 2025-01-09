<?php

namespace Slub\Dfgviewer\Validation;

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

use Slub\Dfgviewer\Common\ValidationHelper as VH;

/**
 * The validator validates against the rules outlined in chapter 2.7 of the METS application profile 2.3.1.
 *
 * @package TYPO3
 * @subpackage dfg-viewer
 *
 * @access public
 */
class DvMetadataValidator extends AbstactDomDocumentValidator
{
    public function isValidDocument(): void
    {
        // Validates against the rules of chapter "2.7.1 Rechteangaben – dv:rights"
        $this->createNodeListValidator(VH::XPATH_DVRIGHTS)
            ->validateHasOne();

        $this->validateDvRights();

        // Validates against the rules of chapter "2.7.3 Verweise – dv:links"
        $this->createNodeListValidator(VH::XPATH_DVLINKS)
            ->validateHasOne();

        $this->validateDvLinks();
    }

    /**
     * Validates the DFG-Viewer links.
     *
     * Validates against the rules of chapter "2.7.4 Unterelemente zu dv:links"
     *
     * @return void
     */
    protected function validateDvLinks(): void
    {
        $references = $this->createNodeListValidator(VH::XPATH_DVLINKS . '/dv:reference')
            ->validateHasAny()
            ->getNodeList();
        foreach ($references as $reference) {
            $this->validateReference($reference);
        }

        $this->createNodeListValidator(VH::XPATH_DVLINKS . '/dv:presentation')
            ->validateHasNoneOrOne();

        $sruNode = $this->createNodeListValidator(VH::XPATH_DVLINKS . '/dv:sru')
            ->validateHasNoneOrOne()->getFirstNode();
        $this->createNodeValidator($sruNode)->validateHasContentWithUrl();

        $iiifNode = $this->createNodeListValidator(VH::XPATH_DVLINKS . '/dv:iiif')
            ->validateHasNoneOrOne()->getFirstNode();
        $this->createNodeValidator($iiifNode)->validateHasContentWithUrl();
    }

    /**
     * Validates the DFG-Viewer rights.
     *
     * Validates against the rules of chapter "2.7.2 Unterelemente zu dv:rights"
     *
     * @return void
     */
    protected function validateDvRights(): void
    {
        $this->createNodeListValidator(VH::XPATH_DVRIGHTS . '/dv:owner')
            ->validateHasOne();

        $this->validateNodeContent(VH::XPATH_DVRIGHTS . '/dv:ownerLogo');
        $this->validateNodeContent(VH::XPATH_DVRIGHTS . '/dv:ownerSiteURL');
        $this->validateNodeContent(VH::XPATH_DVRIGHTS . '/dv:ownerContact');

        $this->createNodeListValidator(VH::XPATH_DVRIGHTS . '/dv:aggregator')->validateHasNoneOrOne();
        $this->validateNodeContent(VH::XPATH_DVRIGHTS . '/dv:aggregatorLogo', true);
        $this->validateNodeContent(VH::XPATH_DVRIGHTS . '/dv:aggregatorSiteURL', true);

        $this->createNodeListValidator(VH::XPATH_DVRIGHTS . '/dv:sponsor')->validateHasNoneOrOne();
        $this->validateNodeContent(VH::XPATH_DVRIGHTS . '/dv:sponsorLogo', true);
        $this->validateNodeContent(VH::XPATH_DVRIGHTS . '/dv:sponsorSiteURL', true);

        $licenseNode = $this->createNodeListValidator(VH::XPATH_DVRIGHTS . '/dv:license')
            ->validateHasNoneOrOne()
            ->getFirstNode();
        if ($licenseNode && !in_array($licenseNode->nodeValue, ['pdm', 'cc0', 'cc-by', 'cc-by-sa', 'cc-by-nd', 'cc-by-nc', 'cc-by-nc-sa', 'cc-by-nc-nd', 'reserved'])) {
            $this->createNodeValidator($licenseNode)->validateHasContentWithUrl();
        }
    }

    /**
     * Validates the reference.
     *
     * Validates against the rules of chapter "2.7.4.1 Katalog- bzw. Findbuchnachweis – dv:reference"
     *
     * @param \DOMNode $reference
     * @return void
     */
    protected function validateReference(\DOMNode $reference): void
    {
        if ($this->xpath->query('dv:reference', $reference->parentNode)->length > 1) {
            $this->createNodeValidator($reference)
                ->validateHasAttribute('linktext');
        }
    }

    private function validateNodeContent(string $expression, bool $optional=false): void
    {
        $nodeListValidator = $this->createNodeListValidator($expression);

        if ($optional) {
            $nodeListValidator
                ->validateHasNoneOrOne();
        } else {
            $nodeListValidator
                ->validateHasOne();
        }

        $node = $nodeListValidator->getFirstNode();
        if (!isset($node)) {
            return;
        }

        $nodeValidator = $this->createNodeValidator($node);
        if (str_starts_with(strtolower($node->nodeValue), 'mailto:')) {
            $nodeValidator->validateHasContentWithEmail();
        } else {
            $nodeValidator->validateHasContentWithUrl();
        }
    }
}
