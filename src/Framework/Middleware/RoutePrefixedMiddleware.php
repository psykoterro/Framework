<?php
/**
 * Created by PhpStorm.
 * User: fdurano
 * Date: 19/06/18
 * Time: 22:53
 */

namespace App\Framework\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class RoutePrefixedMiddleware
 * @package App\Framework\Middleware
 */
class RoutePrefixedMiddleware implements MiddlewareInterface
{

    /**
     *
     * @var ContainerInterface
     */
    private $container;

    /**
     *
     * @var string
     */
    private $prefix;

    /**
     *
     * @var string|MiddlewareInterface
     */
    private $middleware;

    /**
     * RoutePrefixedMiddleware constructor.
     * @param ContainerInterface $container
     * @param string $prefix
     * @param $middleware
     */
    public function __construct(ContainerInterface $container, string $prefix, $middleware)
    {
        $this->container  = $container;
        $this->prefix     = $prefix;
        $this->middleware = $middleware;
    }//end __construct()

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     *
     * @param  ServerRequestInterface  $request
     * @param  RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();
        if (strpos($path, $this->prefix) === 0) {
            if (is_string($this->middleware)) {
                return $this->container->get($this->middleware)->process($request, $handler);
            } else {
                return $this->middleware->process($request, $handler);
            }
        }
        return $handler->handle($request);
    }//end process()
}//end class
