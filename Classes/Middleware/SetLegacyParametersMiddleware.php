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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Plugin 'DFG-Viewer: Set Legacy Parameters Middleware for the 'dfgviewer' extension.
 *
 * @package TYPO3
 * @subpackage tx_dfgviewer
 * @access public
 */
class SetLegacyParametersMiddleware implements MiddlewareInterface
{
    /**
     * The process method of the middleware.
     *
     * @access public
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface JSON response of search suggestions
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $parameters = $request->getQueryParams();
        $setParameters = ($parameters['set'] ?? []);
        if (!empty($setParameters)) {
            if (isset($setParameters['mets'])) {
                $parameters['tx_dlf']['id'] = $setParameters['mets'];
            }
            if (isset($setParameters['image'])) {
                $parameters['tx_dlf']['page'] = $setParameters['image'];
            }
            if (isset($setParameters['double'])) {
                $parameters['tx_dlf']['double'] = $setParameters['double'];
            }
            $request = $request->withQueryParams($parameters);
        }

        return $handler->handle($request);
    }
}
