<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Sebastian Meyer <sebastian.meyer@slub-dresden.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

$EM_CONF[$_EXTKEY] = array(
    'title' => 'DFG Viewer',
    'description' => 'Remote resources navigator for digital libraries. Reads METS/MODS and METS/TEI via OAI-PMH.',
    'category' => 'plugin',
    'author' => 'Sebastian Meyer',
    'author_email' => 'sebastian.meyer@slub-dresden.de',
    'author_company' => 'Saxon State and University Library Dresden (SLUB)',
    'shy' => '',
    'priority' => '',
    'module' => '',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => false,
    'createDirs' => '',
    'modify_tables' => '',
    'clearCacheOnLoad' => false,
    'lockType' => '',
    'version' => '4.0.0',
    'constraints' => array(
        'depends' => array(
            'php' => '5.6.0-',
            'typo3' => '6.2.0-7.6.99',
            'dlf' => '2.2.0-',
        ),
        'conflicts' => array(
        ),
        'suggests' => array(
        ),
    ),
    '_md5_values_when_last_written' => '',
);
