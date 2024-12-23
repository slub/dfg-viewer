<?php

namespace Slub\Dfgviewer\Validation;

use Slub\Dfgviewer\Validation\Mets\AdministrativeMetadataValidator;

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

/**
 * The validator validates against the rules outlined in chapter 2.7 of the METS application profile 2.3.1.
 *
 * @package TYPO3
 * @subpackage dfg-viewer
 *
 * @access public
 */
class DVMetadataValidator extends ApplicationProfileBaseValidator
{
    const XPATH_DV_RIGHTS = AdministrativeMetadataValidator::XPATH_RIGHTS_METADATA . '/mets:mdWrap[@MDTYPE="OTHER" and @OTHERMDTYPE="DVRIGHTS"]/mets:xmlData/dv:rights';

    const XPATH_DV_LINKS = AdministrativeMetadataValidator::XPATH_RIGHTS_METADATA . '/mets:mdWrap[@MDTYPE="OTHER" and @OTHERMDTYPE="DVLINKS"]/mets:xmlData/dv:links';

    protected function isValidDocument(): void
    {
        // Validates against the rules of chapter "2.7.1 Rechteangaben – dv:rights"
        $this->createNodeListValidator(self::XPATH_DV_RIGHTS)
            ->validateHasOne();

        $this->validateDvRights();

        $this->createNodeListValidator(self::XPATH_DV_LINKS)
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
    public function validateDvLinks(): void
    {
        $this->createNodeListValidator(self::XPATH_DV_LINKS . '/dv:reference')
            ->validateHasAny()
            ->iterate(array($this, "validateReferences"));

        $this->createNodeListValidator(self::XPATH_DV_LINKS . '/dv:presentation')
            ->validateHasNoneOrOne();

        $sruNode = $this->createNodeListValidator(self::XPATH_DV_LINKS . '/dv:sru')
            ->validateHasNoneOrOne()->getFirstNode();
        $this->createNodeValidator($sruNode)->validateHasContentWithUrl();

        $iiifNode = $this->createNodeListValidator(self::XPATH_DV_LINKS . '/dv:iiif')
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
    public function validateDvRights(): void
    {
        $this->createNodeListValidator(self::XPATH_DV_RIGHTS . '/dv:owner')
            ->validateHasOne();

        $this->validateNodeContent(self::XPATH_DV_RIGHTS . '/dv:ownerLogo');
        $this->validateNodeContent(self::XPATH_DV_RIGHTS . '/dv:ownerSiteURL');
        $this->validateNodeContent(self::XPATH_DV_RIGHTS . '/dv:ownerContact');

        $this->createNodeListValidator(self::XPATH_DV_RIGHTS . '/dv:aggregator')->validateHasNoneOrOne();
        $this->validateNodeContent(self::XPATH_DV_RIGHTS . '/dv:aggregatorLogo', true);
        $this->validateNodeContent(self::XPATH_DV_RIGHTS . '/dv:aggregatorSiteURL', true);

        $this->createNodeListValidator(self::XPATH_DV_RIGHTS . '/dv:sponsor')->validateHasNoneOrOne();
        $this->validateNodeContent(self::XPATH_DV_RIGHTS . '/dv:sponsorLogo', true);
        $this->validateNodeContent(self::XPATH_DV_RIGHTS . '/dv:sponsorSiteURL', true);

        $licenseNode = $this->createNodeListValidator(self::XPATH_DV_RIGHTS . '/dv:license')
            ->validateHasNoneOrOne()
            ->getFirstNode();
        if ($licenseNode && !in_array($licenseNode->nodeValue, array('pdm', 'cc0', 'cc-by', 'cc-by-sa', 'cc-by-nd', 'cc-by-nc', 'cc-by-nc-sa', 'cc-by-nc-nd', 'reserved'))) {
            $this->createNodeValidator($licenseNode)->validateHasContentWithUrl();
        }
    }

    /**
     * Validates the references.
     *
     * Validates against the rules of chapter "2.7.4.1 Katalog- bzw. Findbuchnachweis – dv:reference"
     *
     * @return void
     */
    public function validateReferences(\DOMNode $reference): void
    {
        if ($this->xpath->query('dv:reference', $reference->parentNode)->length > 1) {
            $this->createNodeValidator($reference)
                ->validateHasAttribute('linktext');
        }
    }

    private function validateNodeContent(string $expression, bool $optional = false): void
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
        $nodeValidator = $this->createNodeValidator($node);
        if (str_starts_with(strtolower($node->nodeValue), 'mailto:')) {
            $nodeValidator->validateHasContentWithEmail();
        } else {
            $nodeValidator->validateHasContentWithUrl();
        }
    }
}
