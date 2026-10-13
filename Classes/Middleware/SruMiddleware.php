<?php

namespace Slub\Dfgviewer\Middleware;

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

use DOMElement;
use DOMText;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SimpleXMLElement;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Plugin 'DFG-Viewer: SRU Client Middleware for the 'dfgviewer' extension.
 *
 * @package TYPO3
 * @subpackage tx_dfgviewer
 * @access public
 */
class SruMiddleware implements MiddlewareInterface
{

    /**
     * Namespace of the SRU (SRW) response envelope (see SRU 1.2,
     * https://www.loc.gov/standards/sru/).
     *
     * @var string
     */
    private const SRW_NAMESPACE = 'http://www.loc.gov/zing/srw/';

    /**
     * Namespace of the "dfg-viewer/page" record schema (DFG-Viewer
     * SRU/ALTO application profile).
     *
     * @var string
     */
    private const DFGVIEWER_NAMESPACE = 'http://dfg-viewer.de/';

    /**
     * The main method of the middleware.
     *
     * @access public
     *
     * @param ServerRequestInterface $request for processing
     * @param RequestHandlerInterface $handler for processing
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        // parameters are sent by POST --> use getParsedBody() instead of getQueryParams()
        $parameters = $request->getParsedBody();
        // Return if not this middleware
        if (!isset($parameters['middleware']) || ($parameters['middleware'] != 'dfgviewer/sru')) {
            return $handler->handle($request);
        }

        $sru = (string) $parameters['sru'];
        $query = (string) $parameters['q'];

        $url = $sru . '?operation=searchRetrieve&version=1.2&startRecord=1&maximumRecords=10&recordSchema=dfg-viewer/page&query=' . urlencode($query);

        $results = [];

        // make request to SRU service and return stream warnings to the client
        $errorMessage = '';
        set_error_handler(
            static function (int $severity, string $message) use (&$errorMessage): bool {
                if ((error_reporting() & $severity) !== 0) {
                    $errorMessage = $message;
                }
                return true;
            }
        );
        try {
            $sruXML = simplexml_load_file($url);
        } finally {
            restore_error_handler();
        }

        if ($errorMessage !== '') {
            $results = [
                'error' => true,
                'message' => $errorMessage,
            ];
        } else if ($sruXML === false) {
            $results = [
                'error' => true,
                'message' => 'The SRU server returned an unreadable response.',
            ];
        } else {
            // Register the `srw` prefix used in the XPath queries below. The
            // prefix is only matched against the namespace *URI*, so this works
            // regardless of the prefix (or default namespace) the server actually
            // emits.
            $sruXML->registerXPathNamespace('srw', self::SRW_NAMESPACE);
            // the result may be a valid <srw:searchRetrieveResponse> or some HTML code
            $sruResponse = $sruXML->xpath('/srw:searchRetrieveResponse');
            if ($sruResponse === false) {
                $results['error'] = '';
            } else {
                $sruRecords = $sruXML->xpath('/srw:searchRetrieveResponse/srw:records/srw:record');
                if (empty($sruRecords)) {
                    $results['error'] = '';
                } else {
                    $results = $this->getSruRecords($sruRecords);
                }
            }
        }

        /** @var Response $response */
        $response = GeneralUtility::makeInstance(Response::class);
        $response->getBody()->write(json_encode($results));
        return $response;
    }

    /**
     * Gets parsed SRU records.
     *
     * @access private
     *
     * @param array $sruRecords records returned by SROU API
     *
     * @return array
     */
    private function getSruRecords(array $sruRecords): array
    {
        $results = [];

        foreach ($sruRecords as $id => $record) {
            // XPath namespace bindings are not inherited by descendant nodes,
            // so register the prefix on the record node as well.
            $record->registerXPathNamespace('srw', self::SRW_NAMESPACE);
            $fullTextHit = $record->xpath('//srw:recordData');
            $page = $fullTextHit[$id]->children(self::DFGVIEWER_NAMESPACE)->page;
            $pageAttributes = [];
            foreach ($page->attributes() as $key => $val) {
                $pageAttributes[$key] = $val;
            }

            $hitFound = [];
            // there may be multiple hits on a page per search query
            foreach ($page->fulltexthit as $hit) {
                $hitAttributes = [];
                foreach ($hit->attributes() as $key => $val) {
                    $hitAttributes[$key] = $val;
                }

                $hitFound[] = [
                    'text' => $hit->span,
                    'attributes' => $hitAttributes
                ];
            }

            $pageId = (int) $pageAttributes['id'];

            // get METS file of search hit
            $parentUrl = (string) $page->parent->attributes()->url;

            // unset $highlightParams but make sure, it's an array()
            $highlightParams = [];

            // get highlight boxes for all results of a page
            foreach ($hitFound as $hit) {
                $highlightField = $hit['attributes']['x1'] . ',' . $hit['attributes']['y1'] . ',' . $hit['attributes']['x2'] . ',' . $hit['attributes']['y2'];
                if (!in_array($highlightField, $highlightParams)) {
                    $highlightParams[] = $highlightField;
                }
            }

            foreach ($hitFound as $hit) {
                $previewImage = (string) ($hit['attributes']['preview'] ?? '');
                $previewText = [];
                if (is_object($hit['text'])) {
                    $previewText = $this->getSpanSegments($hit['text']);
                }

                $origImageParams = '0,' . $pageAttributes['width'] . ',' . $pageAttributes['height'];

                $data = [
                    'link' => $parentUrl,
                    'page' => $pageId,
                    'previewImage' => $previewImage,
                    'previewText' => $previewText,
                    'origImage' => $origImageParams,
                    'highlight' => urlencode(serialize($highlightParams))
                ];

                $results[] = $data;
            }
        }

        return $results;
    }

    /**
     * Gets the text segments of a snippet `<span>` element.
     *
     * The DFG-Viewer snippet is a `<span>` whose content may mix plain text
     * with nested `<span class="highlight">` elements marking the matched
     * search term, e.g.
     * `<span>context <span class="highlight">term</span> context</span>`.
     * Both text nodes and element children are read in document order so that
     * neither the surrounding context nor the highlighted term is lost. A text
     * node is treated as highlighted when the outer span itself carries the
     * "highlight" class (e.g. `<span class="highlight">term</span>`).
     *
     * @access private
     *
     * @param SimpleXMLElement $span the snippet `<span>` element
     *
     * @return array[] the segments, each with "text" (string) and "highlight" (bool)
     */
    private function getSpanSegments(SimpleXMLElement $span): array
    {
        $segments = [];
        // A plain text node is marked as highlighted when the outer span itself
        // carries the "highlight" class (e.g. <span class="highlight">term</span>).
        $inheritHighlight = (string) $span->attributes()->class === 'highlight';
        $domNode = dom_import_simplexml($span);
        foreach ($domNode->childNodes as $child) {
            if ($child instanceof DOMText) {
                $segments[] = [
                    'text' => (string) $child->nodeValue,
                    'highlight' => $inheritHighlight
                ];
            } else if ($child instanceof DOMElement) {
                $segments[] = [
                    'text' => (string) $child->nodeValue,
                    'highlight' => (string) $child->getAttribute('class') === 'highlight'
                ];
            }
        }

        return $segments;
    }
}
