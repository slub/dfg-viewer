<?php

namespace Slub\Dfgviewer\Validation;

use DOMNode;

/**
 * The validator validates against the rules outlined in chapter 2.1 of the application profile 2.3.1.
 *
 * @package TYPO3
 * @subpackage dfg-viewer
 *
 * @access public
 */
class LinkingLogicalPhysicalStructureValidator extends ApplicationProfileBaseValidator
{

    protected function isValid($value): void
    {
        $this->setupIsValid($value);

        // Validates against the rules of chapter "2.3.1 Structure links - mets:structLink"
        if ($this->xpath->query('//mets:structLink')->length > 1) {
            $this->addError('Every METS file has to have no or one struct link element.', 1723727164447);
        }

        $this->validateLinkingElements();
    }

    /**
     * Validates the linking elements.
     *
     * Validates against the rules of chapter "2.3.2.1 Linking – mets:smLink"
     *
     * @return void
     */
    private function validateLinkingElements(): void
    {
        $linkingElements = $this->xpath->query('//mets:structLink/mets:smLink');

        foreach ($linkingElements as $linkingElement) {
            $this->validateLinkingElement($linkingElement, "xlink:from", "LOGICAL");
            $this->validateLinkingElement($linkingElement, "xlink:to", "PHYSICAL");
        }
    }

    /**
     * Validate linking element.
     *
     * @param DOMNode $linkingElement
     * @param string $attribute
     * @param string $structMapType
     * @return void
     */
    private function validateLinkingElement(DOMNode $linkingElement, string $attribute, string $structMapType): void
    {
        if (!$linkingElement->hasAttribute($attribute)) {
            $this->addError('Mandatory "' . $attribute . ' attribute of mets:div in the logical structure is missing.', 1724234607);
        } else {
            $id = $linkingElement->getAttribute($attribute);
            if ($this->xpath->query('//mets:structMap[@TYPE="' . $structMapType . '"]/mets:div/mets:div[@ID = \'' . $id . '\']')->length !== 1) {
                $this->addError('None or multiple ids found for "' . $id . '" in struct map type "' . $structMapType . '".', 1724234607);
            }
        }
    }
}
