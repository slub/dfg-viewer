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
	'description' => 'Remote resources navigator for digital libraries. Reads METS/MODS and METS/TEI via OAI2.',
	'category' => 'plugin',
	'author' => 'Sebastian Meyer',
	'author_email' => 'sebastian.meyer@slub-dresden.de',
	'author_company' => '<br /><a href="http://www.slub-dresden.de/en/" target="_blank">Saxon State and University Library Dresden</a><br /><a href="http://www.dfg.de/en/" target="_blank">German Research Foundation</a>',
	'shy' => '',
	'priority' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => FALSE,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => FALSE,
	'lockType' => '',
	'version' => '3.1.0',
	'constraints' => array(
		'depends' => array(
			'php' => '5.3.0-',
			'typo3' => '4.5.0-6.2.99',
			'dlf' => '1.2.1-',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => '',
);

?>
