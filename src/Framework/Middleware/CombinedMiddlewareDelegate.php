<?php
namespace App\Framework\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class CombinedMiddlewareDelegate
 * @package App\Framework\Middleware
 */
class CombinedMiddlewareDelegate implements RequestHandlerInterface
{

    /**
     *
     * @var string[]
     */
    private $middlewares = [];

    /**
     *
     * @var integer
     */
    private $index = 0;

    /**
     *
     * @var ContainerInterface
     */
    private $container;

    /**
     *
     * @var RequestHandlerInterface
     */
    private $delegate;

    /**
     * CombinedMiddlewareDelegate constructor.
     * @param ContainerInterface $container
     * @param array $middlewares
     * @param RequestHandlerInterface $delegate
     */
    public function __construct(ContainerInterface $container, array $middlewares, RequestHandlerInterface $delegate)
    {
        $this->middlewares = $middlewares;
        $this->container   = $container;
        $this->delegate    = $delegate;
    }//end __construct()

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->getMiddleware();
        if (null === $middleware) {
            return $this->delegate->handle($request);
        }

        if (\is_callable($middleware)) {
            $response = \call_user_func_array($middleware, [$request, [$this, 'handle']]);
            if (\is_string($response)) {
                return new Response(200, [], $response);
            }
            return $response;
        }

        if ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $this);
        }
    }//end handle()

    /**
     *
     * @return object
     */
    private function getMiddleware()
    {
        if (array_key_exists($this->index, $this->middlewares)) {
            if (\is_string($this->middlewares[$this->index])) {
                $middleware = $this->container->get($this->middlewares[$this->index]);
            } else {
                $middleware = $this->middlewares[$this->index];
            }
            $this->index++;
            return $middleware;
        }
        return null;
    }//end getMiddleware()
}//end class
