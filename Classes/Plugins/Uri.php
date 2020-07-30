<?php

namespace Slub\Dfgviewer\Plugins;

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

use Kitodo\Dlf\Common\AbstractPlugin;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 */

/**
 * Plugin 'DFG-Viewer: Persistent Identifier' for the 'dfgviewer' extension.
 *
 * @author  Sebastian Meyer <sebastian.meyer@slub-dresden.de>
 * @copyright  Copyright (c) 2012, Sebastian Meyer, SLUB Dresden
 * @package  TYPO3
 * @subpackage  tx_dfgviewer
 * @access  public
 */
class Uri extends AbstractPlugin
{
    public $extKey = 'dfgviewer';

    public $scriptRelPath = 'Classes/Plugins/Uri.php';

    /**
     * The main method of the PlugIn
     *
     * @access  public
     *
     * @param string $content : The PlugIn content
     * @param array $conf : The PlugIn configuration
     *
     * @return  string    The content that is displayed on the website
     */
    public function main($content, $conf)
    {
        $this->init($conf);

        // Load current document.
        $this->loadDocument();

        if ($this->doc === NULL) {
            // Quit without doing anything if required variables are not set.
            return $content;
        }

        // Set default values if not set.
        // page may be integer or string (physical page attribute)
        if ((int)$this->piVars['page'] > 0 || empty($this->piVars['page'])) {
            $this->piVars['page'] = MathUtility::forceIntegerInRange((int)$this->piVars['page'], 1, $this->doc->numPages, 1);
        } else {
            $this->piVars['page'] = array_search($this->piVars['page'], $this->doc->physicalStructure);
        }

        // Load template file.
        $this->getTemplate('###TEMPLATE###');

        $markerArray = array(
            '###URIBOOK###' => '',
            '###URIPAGE###' => ''
        );

        // Get persistent identifier of book.
        $uriBook = GeneralUtility::trimExplode(' ', $this->doc->physicalStructureInfo[$this->doc->physicalStructure[0]]['contentIds'], TRUE);

        if (empty($uriBook)) {
            $uriBook = $this->doc->getLogicalStructure($this->doc->toplevelId);
            $uriBook = GeneralUtility::trimExplode(' ', $uriBook['contentIds'], TRUE);
        }

        if (!empty($uriBook)) {

            $uris = array();

            foreach ($uriBook as $uri) {
                $piUriBook = htmlspecialchars($this->pi_getLL('uriBook', ''));

                if (strpos($uri, 'http:') === 0 || strpos($uri, 'https:') === 0) {
                    $uris[] = '<a class="persistence-document" href="' . htmlspecialchars($uri) . '">' . $piUriBook . '</a>';
                } elseif (strpos($uri, 'urn:') === 0) {
                    if (strpos($uri, '/fragment/') === FALSE) {
                        $uris[] = '<a class="persistence-document" href="https://nbn-resolving.de/' . $uri . '">' . $piUriBook . '</a>';
                    } else {
                        $uris[] = '<a class="persistence-document" href="https://nbn-resolving.org/' . $uri . '">' . $piUriBook . '</a>';
                    }
                }

            }

            if (!empty($uris)) {
                $markerArray['###URIBOOK###'] = implode(', ', $uris);
            }
        }

        // Get persistent identifier of page.
        $uriPage = GeneralUtility::trimExplode(' ', $this->doc->physicalStructureInfo[$this->doc->physicalStructure[$this->piVars['page']]]['contentIds'], TRUE);

        if (!empty($uriPage)) {

            $uris = array();

            foreach ($uriPage as $uri) {
                $piUriPage = htmlspecialchars($this->pi_getLL('uriPage', ''));

                if (strpos($uri, 'http:') === 0 || strpos($uri, 'https:') === 0) {
                    $uris[] = '<a class="persistence-page" href="' . htmlspecialchars($uri) . '">' . $piUriPage . '</a>';
                } elseif (strpos($uri, 'urn:') === 0) {

                    if (strpos($uri, '/fragment/') === FALSE) {
                        $uris[] = '<a class="persistence-page" href="https://nbn-resolving.de/' . $uri . '">' . $piUriPage . '</a>';
                    } else {
                        $uris[] = '<a class="persistence-page" href="https://nbn-resolving.org/' . $uri . '">' . $piUriPage . '</a>';
                    }

                }

            }

            if (!empty($uris)) {
                $markerArray['###URIPAGE###'] = implode(', ', $uris);
            }

        }

        return $this->templateService->substituteMarkerArray($this->template, $markerArray);
    }

}
