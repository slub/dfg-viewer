<?php

namespace Slub\Dfgviewer\Common;

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
 * The validator helper contains constants and functions to support the validation process.
 *
 * @package TYPO3
 * @subpackage dfg-viewer
 *
 * @access public
 */
class ValidationHelper
{
    const NAMESPACE_DV = 'http://dfg-viewer.de/';

    const NAMESPACE_METS = 'http://www.loc.gov/METS/';

    const NAMESPACE_MODS = 'http://www.loc.gov/mods/v3';

    const URL_REGEX = '(http|https):\/\/([\w_-]+(?:\.[\w_-]+)+)([\w.,@?^=%&:\/~+#-]*[\w@?^=%&\/~+#-])?';

    const COORDS_REGEX = '\d+,\d+,\d+,\d+';

    const STRUCTURE_DATASET = [
        'section', 'file', 'album', 'register', 'annotation', 'address', 'article', 'atlas', 'issue', 'bachelor_thesis', 'volume', 'contained_work', 'additional', 'report', 'official_notification', 'provenance', 'inventory', 'image', 'collation', 'ornament', 'letter', 'cover', 'cover_front', 'cover_back', 'diploma_thesis', 'doctoral_thesis', 'document', 'printers_mark', 'printed_archives', 'binding', 'entry', 'corrigenda', 'bookplate', 'fascicle', 'leaflet', 'research_paper', 'photograph', 'fragment', 'land_register', 'ground_plan', 'habilitation_thesis', 'manuscript', 'illustration', 'imprint', 'contents', 'initial_decoration', 'year', 'chapter', 'map', 'cartulary', 'colophon', 'ephemera', 'engraved_titlepage', 'magister_thesis', 'folder', 'master_thesis', 'multivolume_work', 'month', 'monograph', 'musical_notation', 'periodical', 'poster', 'plan', 'privileges', 'index', 'spine', 'scheme', 'edge', 'seal', 'paste down', 'stamp', 'study', 'table', 'day', 'proceeding', 'text', 'title_page', 'subinventory', 'act', 'judgement', 'verse', 'note', 'preprint', 'dossier', 'lecture', 'endsheet', 'paper', 'preface', 'dedication', 'newspaper'
    ];

    const SUPPORTED_FILEGROUPS = [
        'DEFAULT', 'DOWNLOAD', 'THUMBS', 'TEASER', 'AUDIO', 'FULLTEXT', 'SCORE'
    ];

    const SUPPORTED_MIMETYPES = [
        'image/', 'audio/', 'video/', 'application/vnd.kitodo.iiif', 'application/vnd.netfpx', 'application/vnd.kitodo.zoomify', 'model/gltf-binary', 'model/gltf+json', 'text/xml', 'application/mei+xml', 'application/pdf'
    ];

    const XPATH_METS = '//mets:mets';

    const XPATH_MODS = '//mods:mods';

    const XPATH_MODS_TITLEINFO =  self::XPATH_MODS . '/mods:titleInfo';

    const XPATH_MODS_NAMES =  self::XPATH_MODS . '/mods:name';

    const XPATH_MODS_GENRES =  self::XPATH_MODS . '/mods:genre';

    const XPATH_MODS_ORIGININFO =  self::XPATH_MODS . '/mods:originInfo';

    const XPATH_MODS_LANGUAGE =  self::XPATH_MODS . '/mods:language';

    const XPATH_MODS_PHYSICAL_DESCRIPTION =  self::XPATH_MODS . '/mods:physicalDescription';

    const XPATH_MODS_SUBJECT =  self::XPATH_MODS . '/mods:subject';

    const XPATH_MODS_CLASSIFICATION =  self::XPATH_MODS . '/mods:classification';

    const XPATH_ADMINISTRATIVE_METADATA = self::XPATH_METS . '/mets:amdSec';

    const XPATH_ADMINISTRATIVE_TECHNICAL_METADATA = self::XPATH_ADMINISTRATIVE_METADATA . '/mets:techMD';

    const XPATH_ADMINISTRATIVE_RIGHTS_METADATA = self::XPATH_ADMINISTRATIVE_METADATA . '/mets:rightsMD';

    const XPATH_ADMINISTRATIVE_DIGIPROV_METADATA = self::XPATH_ADMINISTRATIVE_METADATA . '/mets:digiprovMD';

    const XPATH_DESCRIPTIVE_METADATA_SECTIONS = self::XPATH_METS . '/mets:dmdSec';

    const XPATH_FILE_SECTIONS = self::XPATH_METS . '/mets:fileSec';

    const XPATH_FILE_SECTION_GROUPS = self::XPATH_FILE_SECTIONS . '/mets:fileGrp';

    const XPATH_FILE_SECTION_FILES = self::XPATH_FILE_SECTION_GROUPS . '/mets:file';

    const XPATH_STRUCT_LINK = self::XPATH_METS . '/mets:structLink';

    const XPATH_STRUCT_LINK_ELEMENTS = self::XPATH_STRUCT_LINK . '/mets:smLink';

    const XPATH_LOGICAL_STRUCTURES = self::XPATH_METS . '/mets:structMap[@TYPE="LOGICAL"]';

    const XPATH_LOGICAL_STRUCTURAL_ELEMENTS = self::XPATH_LOGICAL_STRUCTURES . '/mets:div';

    const XPATH_LOGICAL_EXTERNAL_REFERENCES = self::XPATH_LOGICAL_STRUCTURAL_ELEMENTS . '/mets:mptr';

    const XPATH_MUSICAL_STRUCTURES = self::XPATH_METS . '/mets:structMap[@TYPE="MUSICAL"]';

    const XPATH_MUSICAL_STRUCTURAL_ELEMENT = self::XPATH_MUSICAL_STRUCTURES . '/mets:div';

    const XPATH_MUSICAL_STRUCTURAL_MEASURE = self::XPATH_MUSICAL_STRUCTURAL_ELEMENT . '/mets:div';

    const XPATH_PHYSICAL_STRUCTURES = self::XPATH_METS . '/mets:structMap[@TYPE="PHYSICAL"]';

    const XPATH_PHYSICAL_STRUCTURAL_ELEMENT = self::XPATH_PHYSICAL_STRUCTURES . '/mets:div';

    const XPATH_PHYSICAL_STRUCTURAL_SEQUENCE = self::XPATH_PHYSICAL_STRUCTURAL_ELEMENT . '/mets:div';

    const XPATH_DVRIGHTS = self::XPATH_ADMINISTRATIVE_RIGHTS_METADATA . '/mets:mdWrap[@MDTYPE="OTHER" and @OTHERMDTYPE="DVRIGHTS"]/mets:xmlData/dv:rights';

    const XPATH_DVLINKS = self::XPATH_ADMINISTRATIVE_DIGIPROV_METADATA . '/mets:mdWrap[@MDTYPE="OTHER" and @OTHERMDTYPE="DVLINKS"]/mets:xmlData/dv:links';

    public static function trimDoubleSlash(string $value): string
    {
        if (str_starts_with($value, '//')) {
            return substr($value, 1);
        }
        return $value;
    }

    public static function getHost($url): string
    {
        preg_match('/' . ValidationHelper::URL_REGEX . '/i', $url, $matches);
        if (is_array($matches) && count($matches) > 2) {
            return $matches[2];
        }
        return '';
    }
}
