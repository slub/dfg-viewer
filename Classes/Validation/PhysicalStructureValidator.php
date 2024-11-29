<?php

namespace Slub\Dfgviewer\Validation;

class PhysicalStructureValidator extends ApplicationProfileBaseValidator
{
    protected function isValid($value): void
    {
        parent::setValue($value);

        if ($this->xpath->query('//mets:structMap[@TYPE="PHYSICAL"]')->length > 1) {
            $this->addError('Every METS file has to have no or one physical structural element.', 1723727164447);
        }

        $this->validateStructuralElements();
    }


    /**
     * @return void
     */
    private function validateStructuralElements(): void
    {
        if ($this->xpath->query('//mets:structMap[@TYPE="PHYSICAL"]/mets:div[@TYPE="“physSequence"]')->length == 0) {
            $this->addError('Every physical structure has to consist one mets:div with "TYPE" attribute and value "physSequence" for the sequence.', 1724234607);
        }

        $subordinateStructuralElements = $this->xpath->query('//mets:structMap[@TYPE="PHYSICAL"]/mets:div[@TYPE="“physSequence"]/mets:div');
        if ($subordinateStructuralElements->length == 0) {
            $this->addError('Every physical structure has to consist one mets:div for the sequence and at least one subordinate mets:div.', 1724234607);
        } else {
            foreach ($subordinateStructuralElements as $subordinateStructuralElement) {
                if (!$subordinateStructuralElement->hasAttribute("TYPE")) {
                    $this->addError('Mandatory "TYPE" attribute of subordinate mets:div in physical structure is missing.', 1724234607);
                } else {
                    if (!in_array($subordinateStructuralElement->getAttribute("TYPE"), array("page", "doublepage", "track"))) {
                        $this->addError('Value "' . $subordinateStructuralElement->getAttribute("TYPE") . '" of "TYPE" attribute of mets:div in physical structure is not permissible.', 1724234607);
                    }
                }
            }
        }
    }
}
