<?php
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

$EM_CONF[$_EXTKEY] = [
    'title' => 'DFG Viewer',
    'description' => 'Remote resources navigator for digital libraries. Reads METS/MODS and METS/TEI via OAI-PMH.',
    'category' => 'distribution',
    'author' => 'Beatrycze Volk',
    'author_email' => 'typo3@slub-dresden.de',
    'author_company' => 'Saxon State and University Library Dresden (SLUB)',
    'autoload' => [
        'psr-4' => [
            'Slub\\Dfgviewer\\' => 'Classes/'
        ],
        'classmap' => [
            'vendor/symfony'
        ]
    ],
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
    'version' => '7.0.0',
    'constraints' => [
        'depends' => [
            'php' => '8.1.0-8.3.99',
            'typo3' => '11.5.0-12.4.99'
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
    '_md5_values_when_last_written' => '',
];
