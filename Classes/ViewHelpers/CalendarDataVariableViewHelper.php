<?php

namespace Slub\Dfgviewer\ViewHelpers;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Viewhelper to filter calendar data and inject a variable with the result.
 *
 * This is intended to reproduce what the custom `NewspaperCalendar` did
 * (before migrating to Kitodo.Presentation 4).
 */
class CalendarDataVariableViewHelper extends AbstractViewHelper
{
    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('name', 'string', 'Name of variable to create', true);
        $this->registerArgument('data', 'array', 'Input calendar data from Kitodo controller', true);
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return null
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $data = $arguments['data'];

        if (count($data) === 12) {
            // Rule 1: If it's a single year (no season split), do show all the months
            // The condition works because showEmptyMonths = 1 in plugin configuration
            $renderingContext->getVariableProvider()->add($arguments['name'], $data);
        } else {
            $firstUsedIdx = 0;
            $lastUsedIdx = 0;

            // $data contains months of two consecutive years
            // Find indices of first and last month that contain an issue
            $monthIdx = 0;
            foreach ($data as $month) {
                foreach ($month['week'] as $week) {
                    $weekHasIssues = false;

                    foreach ($week as $day) {
                        if (!empty($day['issues'])) {
                            if ($firstUsedIdx === 0) {
                                $firstUsedIdx = $monthIdx;
                            }

                            $lastUsedIdx = $monthIdx;

                            $weekHasIssues = true;
                            break;
                        }
                    }

                    if ($weekHasIssues) {
                        break;
                    }
                }

                $monthIdx++;
            }

            if ($firstUsedIdx === $lastUsedIdx) {
                // Rule 2: If only one month contains an issue, show only that one
                $firstEntryIdx = $lastEntryIdx = $firstUsedIdx;
            } else {
                // Rule 3: Show empty months of the same tertial as first and last months
                $firstEntryIdx = floor($firstUsedIdx / 4) * 4;
                $lastEntryIdx = ceil(($lastUsedIdx + 1) / 4) * 4 - 1;
            }

            $selectedData = [];
            $keys = array_keys($data);
            for ($i = $firstEntryIdx; $i <= $lastEntryIdx; $i++) {
                $key = $keys[$i];
                $selectedData[$key] = $data[$key];
            }

            $renderingContext->getVariableProvider()->add($arguments['name'], $selectedData);
        }
    }
}
