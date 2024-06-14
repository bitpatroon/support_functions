<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c)  S.M. Zonneveld
 *  www: https://www.bitpatroon.nl/
 *  e-mail: code@bitpatroon.nl
 *
 *  Created on 12-6-2024 14:27
 *
 *  All rights reserved
 *
 *  This script is part of a client or private project.
 *  You can redistribute it and/or modify it under the terms of
 *  the GNU General Public License as published by the Free
 *  Software Foundation; either version 3 of the License, or
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

namespace BPN\SupportFunctions\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Extbase\Object\Exception;

class NoCachePrefixMiddleware implements MiddlewareInterface
{
    /**
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     * @throws AspectNotFoundException
     * @throws DataLimitExceededException
     * @throws Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($uri = $request->getUri()) {
            $path = $uri->getPath();
            if (str_starts_with($path, '/nc')) {
                $path = substr($path, 3);

                $uri = $uri->withPath($path)->withQuery('no_cache=1');
                $requestClone = $request->withUri($uri);

                return $handler->handle($requestClone);
            }
        }

        return $handler->handle($request);
    }
}
