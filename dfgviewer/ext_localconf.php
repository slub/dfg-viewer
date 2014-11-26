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

if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

// Register plugins.
t3lib_extMgm::addPItoST43($_EXTKEY, 'plugins/amd/class.tx_dfgviewer_amd.php', '_amd', 'list_type', TRUE);

t3lib_extMgm::addPItoST43($_EXTKEY, 'plugins/gridpager/class.tx_dfgviewer_gridpager.php', '_gridpager', 'list_type', TRUE);

t3lib_extMgm::addPItoST43($_EXTKEY, 'plugins/uri/class.tx_dfgviewer_uri.php', '_uri', 'list_type', TRUE);

t3lib_extMgm::addPItoST43($_EXTKEY, 'plugins/newspaper-calendar/class.tx_dfgviewer_newspaper-calendar.php', '_newspapercalendar', 'list_type', TRUE);

t3lib_extMgm::addPItoST43($_EXTKEY, 'plugins/newspaper-years/class.tx_dfgviewer_newspaper-years.php', '_newspaperyears', 'list_type', TRUE);

t3lib_extMgm::addPItoST43($_EXTKEY, 'plugins/sru/class.tx_dfgviewer_sru.php', '_sru', 'list_type', TRUE);

// Register eID handlers.
$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['tx_dfgviewer_sru_eid'] = 'EXT:'.$_EXTKEY.'/plugins/sru/class.tx_dfgviewer_sru_eid.php';

?>
