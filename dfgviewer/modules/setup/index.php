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

/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 */

/**
 * Module 'setup' for the 'dfgviewer' extension.
*
* @author	Sebastian Meyer <sebastian.meyer@slub-dresden.de>
* @copyright	Copyright (c) 2012, Sebastian Meyer, SLUB Dresden
* @package	TYPO3
* @subpackage	tx_dfgviewer
* @access	public
*/
class tx_dfgviewer_modSetup extends tx_dlf_module {

	public $extKey = 'dfgviewer';

	public $prefixId = 'tx_dfgviewer';

	protected $modPath = 'setup/';

	protected $buttonArray = array (
		'SHORTCUT' => '',
	);

	protected $markerArray = array (
		'CSH' => '',
		'MOD_MENU' => '',
		'CONTENT' => '',
	);

	/**
	 * Add metadata configuration
	 *
	 * @access	protected
	 *
	 * @return	void
	 */
	protected function cmdAddMetadata() {

		// Include metadata definition file.
		include_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($this->extKey).'modules/'.$this->modPath.'metadata.inc.php');

		$i = 0;

		// Build data array.
		foreach ($metadata as $index_name => $values) {

			$formatIds = array ();

			foreach ($values['format'] as $format) {

				$formatIds[] = uniqid('NEW');

				$data['tx_dlf_metadataformat'][end($formatIds)] = $format;

				$data['tx_dlf_metadataformat'][end($formatIds)]['pid'] = intval($this->id);

				$i++;

			}

			$data['tx_dlf_metadata'][uniqid('NEW')] = array (
				'pid' => intval($this->id),
				'hidden' => $values['hidden'],
				'label' => $GLOBALS['LANG']->getLL($index_name),
				'index_name' => $index_name,
				'format' => implode(',', $formatIds),
				'default_value' => $values['default_value'],
				'wrap' => (!empty($values['wrap']) ? $values['wrap'] : $GLOBALS['TCA']['tx_dlf_metadata']['columns']['wrap']['config']['default']),
				'tokenized' => 0,
				'stored' => 0,
				'indexed' => 0,
				'boost' => 0.00,
				'is_sortable' => 0,
				'is_facet' => 0,
				'is_listed' => $values['is_listed'],
				'autocomplete' => 0,
			);

			$i++;

		}

		$_ids = tx_dlf_helper::processDBasAdmin($data);

		// Check for failed inserts.
		if (count($_ids) == $i) {

			// Fine.
			$_message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
				'TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
				$GLOBALS['LANG']->getLL('flash.metadataAddedMsg'),
				$GLOBALS['LANG']->getLL('flash.metadataAdded', TRUE),
				\TYPO3\CMS\Core\Messaging\FlashMessage::OK,
				FALSE
			);

		} else {

			// Something went wrong.
			$_message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
				'TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
				$GLOBALS['LANG']->getLL('flash.metadataNotAddedMsg'),
				$GLOBALS['LANG']->getLL('flash.metadataNotAdded', TRUE),
				\TYPO3\CMS\Core\Messaging\FlashMessage::ERROR,
				FALSE
			);

		}

		tx_dlf_helper::addMessage($_message);

	}

	/**
	 * Add structure configuration
	 *
	 * @access	protected
	 *
	 * @return	void
	 */
	protected function cmdAddStructure() {

		// Include structure definition file.
		include_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($this->extKey).'modules/'.$this->modPath.'structures.inc.php');

		// Build data array.
		foreach ($structures as $index_name => $values) {

			$data['tx_dlf_structures'][uniqid('NEW')] = array (
				'pid' => intval($this->id),
				'toplevel' => $values['toplevel'],
				'label' => $GLOBALS['LANG']->getLL($index_name),
				'index_name' => $index_name,
				'oai_name' => $values['oai_name']
			);

		}

		$_ids = tx_dlf_helper::processDBasAdmin($data);

		// Check for failed inserts.
		if (count($_ids) == count($structures)) {

			// Fine.
			$_message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
				'TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
				$GLOBALS['LANG']->getLL('flash.structureAddedMsg'),
				$GLOBALS['LANG']->getLL('flash.structureAdded', TRUE),
				\TYPO3\CMS\Core\Messaging\FlashMessage::OK,
				FALSE
			);

		} else {

			// Something went wrong.
			$_message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
				'TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
				$GLOBALS['LANG']->getLL('flash.structureNotAddedMsg'),
				$GLOBALS['LANG']->getLL('flash.structureNotAdded', TRUE),
				\TYPO3\CMS\Core\Messaging\FlashMessage::ERROR,
				FALSE
			);

		}

		tx_dlf_helper::addMessage($_message);

	}

	/**
	 * Main function of the module
	 *
	 * @access	public
	 *
	 * @return	void
	 */
	public function main() {

		// Is the user allowed to access this page?
		$access = is_array($this->pageInfo) && $GLOBALS['BE_USER']->isAdmin();

		if ($this->id && $access) {

			// Check if page is sysfolder.
			if ($this->pageInfo['doktype'] != 254) {

				$_message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
					'TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
					$GLOBALS['LANG']->getLL('flash.wrongPageTypeMsg'),
					$GLOBALS['LANG']->getLL('flash.wrongPageType', TRUE),
					\TYPO3\CMS\Core\Messaging\FlashMessage::ERROR,
					FALSE
				);

				tx_dlf_helper::addMessage($_message);

				$this->markerArray['CONTENT'] .= tx_dlf_helper::renderFlashMessages();

				$this->printContent();

				return;

			}

			// Should we do something?
			if (!empty($this->CMD)) {

				// Sanitize input...
				$_method = 'cmd'.ucfirst($this->CMD);

				// ...and unset to prevent infinite looping.
				unset ($this->CMD);

				if (method_exists($this, $_method)) {

					$this->$_method();

				}

			}

			// Check for existing structure configuration.
			$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'uid',
				'tx_dlf_structures',
				'pid='.intval($this->id).tx_dlf_helper::whereClause('tx_dlf_structures')
			);

			if ($GLOBALS['TYPO3_DB']->sql_num_rows($result)) {

				// Fine.
				$_message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
					'TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
					$GLOBALS['LANG']->getLL('flash.structureOkayMsg'),
					$GLOBALS['LANG']->getLL('flash.structureOkay', TRUE),
					\TYPO3\CMS\Core\Messaging\FlashMessage::OK,
					FALSE
				);

			} else {

				// Configuration missing.
				$_url = \TYPO3\CMS\Core\Utility\GeneralUtility::locationHeaderUrl(\TYPO3\CMS\Core\Utility\GeneralUtility::linkThisScript(array ('id' => $this->id, 'CMD' => 'addStructure')));

				$_message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
					'TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
					sprintf($GLOBALS['LANG']->getLL('flash.structureNotOkayMsg'), $_url),
					$GLOBALS['LANG']->getLL('flash.structureNotOkay', TRUE),
					\TYPO3\CMS\Core\Messaging\FlashMessage::ERROR,
					FALSE
				);

			}

			tx_dlf_helper::addMessage($_message);

			// Check for existing metadata configuration.
			$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'uid',
				'tx_dlf_metadata',
				'pid='.intval($this->id).tx_dlf_helper::whereClause('tx_dlf_metadata')
			);

			if ($GLOBALS['TYPO3_DB']->sql_num_rows($result)) {

				// Fine.
				$_message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
					'TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
					$GLOBALS['LANG']->getLL('flash.metadataOkayMsg'),
					$GLOBALS['LANG']->getLL('flash.metadataOkay', TRUE),
					\TYPO3\CMS\Core\Messaging\FlashMessage::OK,
					FALSE
				);

			} else {

				// Configuration missing.
				$_url = \TYPO3\CMS\Core\Utility\GeneralUtility::locationHeaderUrl(\TYPO3\CMS\Core\Utility\GeneralUtility::linkThisScript(array ('id' => $this->id, 'CMD' => 'addMetadata')));

				$_message = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
					'TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
					sprintf($GLOBALS['LANG']->getLL('flash.metadataNotOkayMsg'), $_url),
					$GLOBALS['LANG']->getLL('flash.metadataNotOkay', TRUE),
					\TYPO3\CMS\Core\Messaging\FlashMessage::ERROR,
					FALSE
				);

			}

			tx_dlf_helper::addMessage($_message);

			$this->markerArray['CONTENT'] .= tx_dlf_helper::renderFlashMessages();

		} else {

			// TODO: Ändern!
			$this->markerArray['CONTENT'] .= 'You are not allowed to access this page or have not selected a page, yet.';

		}

		$this->printContent();

	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dfgviewer/modules/setup/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dfgviewer/modules/setup/index.php']);
}

$SOBE = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tx_dfgviewer_modSetup');

$SOBE->main();
