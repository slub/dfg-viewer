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

$extensionPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('dfgviewer');

return array (
	'tx_dfgviewer_modSetup' => $extensionPath.'modules/setup/index.php',
	'tx_dfgviewer_amd' => $extensionPath.'plugins/amd/class.tx_dfgviewer_amd.php',
	'tx_dfgviewer_gridpager' => $extensionPath.'plugins/gridpager/class.tx_dfgviewer_gridpager.php',
	'tx_dfgviewer_uri' => $extensionPath.'plugins/uri/class.tx_dfgviewer_uri.php'
);
