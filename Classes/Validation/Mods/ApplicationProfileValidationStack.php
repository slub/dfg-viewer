<?php

declare(strict_types=1);

namespace Slub\Dfgviewer\Validation\Mods;

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

use Kitodo\Dlf\Validation\AbstractDlfValidationStack;

/**
 * The validator stack validates against the rules of the MODS application profile 2.4.
 *
 * @package TYPO3
 * @subpackage dfg-viewer
 *
 * @access public
 */
class ApplicationProfileValidationStack extends AbstractDlfValidationStack
{
    public function __construct()
    {
        parent::__construct(\DOMDocument::class);
        // codacy:disable
        $this->addValidator(TitleValidator::class);
        $this->addValidator(NameValidator::class);
        $this->addValidator(GenreValidator::class);
        $this->addValidator(OriginValidator::class);
        $this->addValidator(LanguageValidator::class);
        $this->addValidator(PhysicalDescriptionValidator::class);
        // Validation of chapter "2.7 Abstract" already covered by MODS XML schema validation
        $this->addValidator(NoteValidator::class);
        $this->addValidator(SubjectsValidator::class);
        $this->addValidator(ClassificationValidator::class);
        $this->addValidator(RelatedItemValidator::class);
        $this->addValidator(IdentifierValidator::class);
        $this->addValidator(LocationValidator::class);
        // Chapter "2.14 Zugriffs- und Verarbeitungsrechte" currently not formulated
        $this->addValidator(PartValidator::class);
        $this->addValidator(RecordInfoValidator::class);
        // Validation of chapter "3.1 Erweiterung – mods:extension" already covered by MODS XML schema validation
        // codacy:enable
    }
}
