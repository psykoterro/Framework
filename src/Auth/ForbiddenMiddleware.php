<?php
/**
 * Created by PhpStorm.
 * User: fdurano
 * Date: 20/06/18
 * Time: 16:25
 */

namespace App\Auth;

use App\Framework\Auth\ForbiddenException;
use App\Framework\Response\RedirectResponse;
use App\Framework\Session\FlashService;
use App\Framework\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ForbiddenMiddleware implements MiddlewareInterface
{

    /**
     * @var string
     */
    private $loginPath;

    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(string $loginPath, SessionInterface $session)
    {
        $this->loginPath = $loginPath;
        $this->session = $session;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return RedirectResponse|ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (ForbiddenException $exception) {
            return $this->redirectLogin($request);
        } catch (\TypeError $error) {
            if (strpos($error->getMessage(), \App\Framework\Auth\User::class) !== false) {
                return $this->redirectLogin($request);
            }
            throw $error;
        }
    }

    public function redirectLogin(ServerRequestInterface $request): ResponseInterface
    {
        $this->session->set('auth.redirect', $request->getUri()->getPath());
        (new FlashService($this->session))->error('Vous devez posséder un compte pour accéder à cette page');
        return new RedirectResponse($this->loginPath);
    }
}
