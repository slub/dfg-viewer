<?php

namespace Slub\Dfgviewer\Tests\Unit\Middleware;

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

use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use SimpleXMLElement;
use Slub\Dfgviewer\Middleware\SruMiddleware;

/**
 * Test case for {@see SruMiddleware} response parsing.
 *
 * The network-bound {@see SruMiddleware::process()} is not exercised here;
 * instead the pure record-parsing methods are tested against the fixture
 * responses in Tests/Fixtures/sru/. Both fixes under test are locked in:
 *   1. parsing must work for a standard default-namespace SRW envelope and
 *      for the literal `srw:` prefix (namespace-robust parsing).
 *   2. the snippet `<span>` must keep both the plain context text and the
 *      nested `<span class="highlight">` segments (see getSpanSegments()).
 */
class SruMiddlewareTest extends TestCase
{

    /**
     * @var SruMiddleware
     */
    protected SruMiddleware $subject;

    /**
     * The fixture path base.
     *
     * @var string
     */
    protected string $fixtureBase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new SruMiddleware();
        $this->fixtureBase = __DIR__ . '/../../Fixtures/sru/';
    }

    /**
     * Parse a fixture file the same way SruMiddleware::process() does after
     * the network fetch: register the `srw` prefix and return the parsed
     * records.
     */
    protected function parseFixture(string $filename): array
    {
        $sruXML = simplexml_load_file($this->fixtureBase . $filename);
        self::assertNotFalse($sruXML, 'Fixture could not be parsed');
        self::assertInstanceOf(SimpleXMLElement::class, $sruXML);

        // Mirror the registration SruMiddleware::process() performs.
        $sruXML->registerXPathNamespace('srw', 'http://www.loc.gov/zing/srw/');

        $sruResponse = $sruXML->xpath('/srw:searchRetrieveResponse');
        self::assertNotFalse($sruResponse, 'Response root must match');
        $sruRecords = $sruXML->xpath('/srw:searchRetrieveResponse/srw:records/srw:record');
        self::assertNotEmpty($sruRecords, 'Records must be found');

        return $this->invokeGetSruRecords($sruRecords);
    }

    protected function invokeGetSruRecords(array $sruRecords): array
    {
        $method = new ReflectionMethod(SruMiddleware::class, 'getSruRecords');
        return $method->invoke($this->subject, $sruRecords);
    }

    protected function invokeGetSpanSegments(string $spanXml): array
    {
        // The root element of the snippet is the outer <span> itself.
        $span = simplexml_load_string($spanXml);
        self::assertInstanceOf(SimpleXMLElement::class, $span);
        $method = new ReflectionMethod(SruMiddleware::class, 'getSpanSegments');
        return $method->invoke($this->subject, $span);
    }

    /**
     * A standards-compliant server using a default namespace for the SRW
     * envelope must be parsed (previously yielded no results silently).
     */
    public function testParsesDefaultNamespaceResponse(): void
    {
        $results = $this->parseFixture('sru-response-default-namespace.xml');
        self::assertCount(3, $results, 'Both records, all their hits must be parsed');
    }

    /**
     * A server emitting the literal `srw:` prefix must be parsed identically.
     */
    public function testParsesLiteralSrwPrefixResponse(): void
    {
        $defaultNs = $this->parseFixture('sru-response-default-namespace.xml');
        $literal = $this->parseFixture('sru-response-srw-prefix.xml');
        self::assertEquals($defaultNs, $literal, 'Both namespaces must yield the same result');
    }

    /**
     * The parsed structure must carry the page id, parent METS link,
     * dimensions, preview and the snippet segments with highlight flags.
     */
    public function testParsedRecordStructure(): void
    {
        $results = $this->parseFixture('sru-response-default-namespace.xml');

        // First record, first hit (page 4).
        $first = $results[0];
        self::assertSame(4, $first['page']);
        self::assertSame('https://host.example/mets.xml', $first['link']);
        self::assertSame('0,2481,3508', (string) $first['origImage']);
        self::assertSame('https://host.example/preview-4a.jpg', $first['previewImage']);
        self::assertSame(
            [
                ['text' => 'some context ', 'highlight' => false],
                ['text' => 'term', 'highlight' => true],
                ['text' => ' more context', 'highlight' => false],
            ],
            $first['previewText'],
            'Snippet must keep context and the highlighted term in order'
        );

        // Second record, plain-text-only snippet (page 9).
        $plain = $results[2];
        self::assertSame(9, $plain['page']);
        self::assertSame(
            [['text' => 'only plain text, no highlight', 'highlight' => false]],
            $plain['previewText']
        );
    }

    /**
     * Multiple hits on one page must all be present, each with its own snippet.
     */
    public function testMultipleHitsPerPage(): void
    {
        $results = $this->parseFixture('sru-response-default-namespace.xml');
        // Page 4 has two hits -> two result rows for that page.
        self::assertSame(4, $results[0]['page']);
        self::assertSame(4, $results[1]['page']);
        self::assertSame(
            [
                ['text' => 'second ', 'highlight' => false],
                ['text' => 'hit', 'highlight' => true],
                ['text' => ' here', 'highlight' => false],
            ],
            $results[1]['previewText']
        );
    }

    /**
     * getSpanSegments must keep context and highlight segments together, in
     * document order (mixed content).
     */
    public function testSpanSegmentsMixedContent(): void
    {
        $segments = $this->invokeGetSpanSegments(
            '<span>pre <span class="highlight">term</span> post</span>'
        );
        self::assertSame(
            [
                ['text' => 'pre ', 'highlight' => false],
                ['text' => 'term', 'highlight' => true],
                ['text' => ' post', 'highlight' => false],
            ],
            $segments
        );
    }

    /**
     * A span that is entirely plain text (no children) must be kept as a
     * single non-highlight segment.
     */
    public function testSpanSegmentsPlainTextOnly(): void
    {
        $segments = $this->invokeGetSpanSegments('<span>just plain text</span>');
        self::assertSame([['text' => 'just plain text', 'highlight' => false]], $segments);
    }

    /**
     * A span carrying the highlight class at the top level (no children) must
     * be a single highlighted segment.
     */
    public function testSpanSegmentsTopLevelHighlight(): void
    {
        $segments = $this->invokeGetSpanSegments('<span class="highlight">term</span>');
        self::assertSame([['text' => 'term', 'highlight' => true]], $segments);
    }
}
