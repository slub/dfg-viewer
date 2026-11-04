<?php

namespace Slub\Dfgviewer\Tests\Unit\Middleware;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slub\Dfgviewer\Middleware\SetLegacyParametersMiddleware;

class SetLegacyParametersMiddlewareTest extends TestCase
{
    #[Test]
    public function ignoresScalarSetParameter(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn(['set' => 'invalid']);
        $request->expects(self::never())->method('withQueryParams');

        $response = $this->createMock(ResponseInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects(self::once())
            ->method('handle')
            ->with(self::identicalTo($request))
            ->willReturn($response);

        self::assertSame($response, (new SetLegacyParametersMiddleware())->process($request, $handler));
    }

    #[Test]
    public function mapsLegacyParametersWhenPluginParametersAreScalar(): void
    {
        $queryParams = [
            'set' => ['mets' => 'https://example.test/mets.xml', 'image' => '4'],
            'tx_dlf' => 'invalid',
        ];
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn($queryParams);
        $updatedRequest = $this->createMock(ServerRequestInterface::class);
        $request->expects(self::once())
            ->method('withQueryParams')
            ->with(
                [
                    'set' => $queryParams['set'],
                    'tx_dlf' => ['id' => 'https://example.test/mets.xml', 'page' => '4'],
                ]
            )
            ->willReturn($updatedRequest);

        $response = $this->createMock(ResponseInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects(self::once())
            ->method('handle')
            ->with(self::identicalTo($updatedRequest))
            ->willReturn($response);

        self::assertSame($response, (new SetLegacyParametersMiddleware())->process($request, $handler));
    }
}
