<?php
/**
 * Created by PhpStorm.
 * User: fdurano
 * Date: 19/06/18
 * Time: 06:28
 */

namespace App\Framework\Auth;

use App\Framework\Auth;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LoggedinMiddleware implements MiddlewareInterface
{

    /**
     * @var Auth
     */
    private $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws ForbiddenException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = $this->auth->getUser();
        if (is_null($user)) {
            throw new ForbiddenException();
        }
        return $handler->handle($request->withAttribute('user', $user));
    }
}