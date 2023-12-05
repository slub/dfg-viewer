<?php
namespace Slub\Dfgviewer\ViewHelpers;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 Alexander Bigga <typo3@slub-dresden.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

use Kitodo\Dlf\Common\MetsDocument;
use Kitodo\Dlf\Domain\Repository\DocumentRepository;

/**
 * ViewHelper to get page info
 *
 * # Example: Basic example
 * <code>
 * <si:pageInfo page="123">
 *	<span>123</span>
 * </code>
 * <output>
 * Will output the page record
 * </output>
 *
 * @package TYPO3
 */
class XpathViewHelper extends AbstractViewHelper
{
    /**
     * Initialize arguments.
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('xpath', 'string', 'Xpath Expression', true);
        $this->registerArgument('htmlspecialchars', 'boolean', 'Use htmlspecialchars() on the found result.', false, true);
        $this->registerArgument('returnArray', 'boolean', 'Return results in an array instead of string.', false, false);
    }

    /**
     * documentRepository
     *
     * @var DocumentRepository
     */
    protected static $documentRepository = null;

    /**
     * Render the supplied DateTime object as a formatted date.
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     */
    public static function renderStatic(
      array $arguments,
      \Closure $renderChildrenClosure,
      RenderingContextInterface $renderingContext
    ) {
        $xpath = $arguments['xpath'];
        $htmlspecialchars = $arguments['htmlspecialchars'];
        $returnArray = $arguments['returnArray'];

        $parameters = [];

        $parametersSet = GeneralUtility::_GPmerged('set');
        $parametersDlf = GeneralUtility::_GPmerged('tx_dlf');
        if (isset($parametersSet['mets']) && GeneralUtility::isValidUrl($parametersSet['mets'])) {
            $parameters['location'] = $parametersSet['mets'];
        } else if (isset($parametersDlf['id'])) {
            if (MathUtility::canBeInterpretedAsInteger($parametersDlf['id'])) {
                $parameters['id'] = $parametersDlf['id'];
            } else if (GeneralUtility::isValidUrl($parametersDlf['id'])) {
                $parameters['location'] = $parametersDlf['id'];
            }
        } else if (isset($parametersDlf['recordId'])) {
            $parameters['recordId'] = $parametersDlf['recordId'];
        }

        $document = self::getDocumentRepository()->findOneByParameters($parameters);

        if ($document === null || $document->getCurrentDocument() === null || !($document->getCurrentDocument() instanceof MetsDocument)) {
            return;
        }
        $currentDocument = $document->getCurrentDocument();
        $currentDocument->mets->registerXPathNamespace('mets', 'http://www.loc.gov/METS/');
        $currentDocument->mets->registerXPathNamespace('mods', 'http://www.loc.gov/mods/v3');
        $currentDocument->mets->registerXPathNamespace('dv', 'http://dfg-viewer.de/');
        $currentDocument->mets->registerXPathNamespace('slub', 'http://slub-dresden.de/');

        $result = $currentDocument->mets->xpath($xpath);

        if ($returnArray) {
            $output = [];
        } else {
            $output = '';
        }

        if (is_array($result)) {
            foreach ($result as $row) {
                if ($returnArray) {
                    $output[] = $htmlspecialchars ? htmlspecialchars(trim($row)) : trim($row);
                } else {
                    $output .= $htmlspecialchars ? htmlspecialchars(trim($row)) : trim($row) . ' ';
                }
            }
        }

        if ($returnArray) {
            return $output;
        } else {
            return trim($output);
        }
    }


    /**
     * Initialize the documentRepository
     *
     * return documentRepository
     */
    private static function getDocumentRepository()
    {
        if (null === static::$documentRepository) {
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            static::$documentRepository = $objectManager->get(
                DocumentRepository::class
            );
        }

        return static::$documentRepository;
    }

}
