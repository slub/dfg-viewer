<?php

namespace Slub\Dfgviewer\Validation;

/**
 * The validator validates against the rules outlined in chapter 2.1 of the application profile 2.3.1.
 *
 * @package TYPO3
 * @subpackage dfg-viewer
 *
 * @access public
 */
class LogicalStructureValidator extends ApplicationProfileBaseValidator
{
    protected function isValid($value): void
    {
        $this->setupIsValid($value);

        // Validates against the rules of chapter "2.1.1 Logical structure - mets:structMap"
        if ($this->xpath->query('//mets:structMap[@TYPE="LOGICAL"]')->length == 0) {
            $this->addError('Every METS file has to have at least one logical structural element.', 1723727164447);
        }

        $this->validateStructuralElements();

        $this->validateExternalReference();

        $this->validatePeriodicPublishingSequences();
    }

    /**
     * Validates the structural elements.
     *
     * Validates against the rules of chapter "2.1.2.1 Structural element - mets:div"
     *
     * @return void
     */
    private function validateStructuralElements(): void
    {
        $structuralElements = $this->xpath->query('//mets:structMap[@TYPE="LOGICAL"]/mets:div');
        if ($structuralElements->length == 0) {
            $this->addError('Every logical structure has to consist of at least one mets:div.', 1724234607);
        } else {
            foreach ($structuralElements as $structuralElement) {
                if (!$structuralElement->hasAttribute("ID")) {
                    $this->addError('Mandatory "ID" attribute of mets:div in the logical structure is missing.', 1724234607);
                } else {
                    $id = $structuralElement->getAttribute("ID");
                    if ($this->xpath->query('//*[@ID="' . $id . '"]')->length > 1) {
                        $this->addError('Logical structure "ID" "' . $id . '" already exists in document.', 1724234607);
                    }
                }
                if (!$structuralElement->hasAttribute("TYPE")) {
                    $this->addError('Mandatory "TYPE" attribute of mets:div in the logical structure is missing.', 1724234607);
                } else {
                    if (!in_array($structuralElement->getAttribute("TYPE"), self::STRUCTURE_DATASET)) {
                        $this->addError('Value "' . $structuralElement->getAttribute("TYPE") . '" of "TYPE" attribute of mets:div in the logical structure is not permissible.', 1724234607);
                    }
                }
            }
        }
    }

    /**
     * Validates the external references.
     *
     * Validates against the rules of chapter "2.1.2.2 Reference to external METS-files - mets:div / mets:mptr"
     *
     * @return void
     */
    private function validateExternalReference(): void
    {
        $externalReferences = $this->xpath->query('//mets:structMap[@TYPE="LOGICAL"]/mets:div/mets:mptr');
        if ($externalReferences->length > 1) {
            $this->addError('Every mets:div in the logical structure may only contain one mets:mptr.', 1724234607);
        } else if ($externalReferences->length == 1) {
            $externalReference = $externalReferences->item(0);
            if (!$externalReference->hasAttribute("LOCTYPE")) {
                $this->addError('Mandatory "LOCTYPE" attribute of mets:mptr in the logical structure is missing.', 1724234607);
            } else {
                if (!in_array($externalReference->getAttribute("LOCTYPE"), array("URL", "PURL"))) {
                    $this->addError('Value "' . $externalReference->getAttribute("LOCTYPE") . '" of "LOCTYPE" attribute of mets:mptr in the logical structure is not permissible.', 1724234607);
                }
            }
            if (!$externalReference->hasAttribute("xlink:href")) {
                $this->addError('Mandatory "xlink:href" attribute of mets:mptr in the logical structure is missing.', 1724234607);
            } else {
                if (!filter_var($externalReference->getAttribute("xlink:href"), FILTER_VALIDATE_URL)) {
                    $this->addError('URL of attribute value "xlink:href" of mets:mptr in the logical structure is not valid.', 1727792902);
                }
            }

            //*[@id="button"]

        }
    }

    /**
     * Validates the periodic publishing sequences.
     *
     * Validates against the rules of chapter "2.1.3 Periodic publishing sequences"
     *
     * @return void
     */
    private function validatePeriodicPublishingSequences(): void
    {
        // TODO
    }
}
