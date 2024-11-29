<?php

declare(strict_types=1);

namespace Slub\Dfgviewer\Validation;

use Kitodo\Dlf\Validation\AbstractDlfValidationStack;

class ApplicationProfileValidationStack extends AbstractDlfValidationStack {
    public function __construct(array $options = [])
    {
        parent::__construct(\DOMDocument::class, $options);

        $this->addValidator(LogicalStructureValidator::class, "Specifications for the logical document structure", false);
        $this->addValidator(PhysicalStructureValidator::class, "Specifications for the physical document structure", false);

    }
}
