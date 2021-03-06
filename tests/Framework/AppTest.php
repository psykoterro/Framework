<?php
namespace Tests\Framework;

use Exception;
use Framework\App;
use GuzzleHttp\Psr7\ServerRequest;
use Middlewares\Utils\RequestHandler;
use phpDocumentor\Reflection\Types\Callable_;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;

class AppTest extends TestCase
{

    /**
     * @var App
     */
    private $app;

    private $handler;

    private $fakeCallable;

    public function setUp()
    {
        $this->app = new App();
        $this->fakeCallable = function ($CallablesParams) {
            // Completely mock out fakeCallable.
        };
    }

    public function testApp()
    {
        $this->app->addModule(get_class($this));
        $this->assertEquals([get_class($this)], $this->app->getModules());
    }

    public function testAppWithArrayDefinition()
    {
        $app = new App(['a' => 2]);
        $this->assertEquals(2, $app->getContainer()->get('a'));
    }

    public function testPipe()
    {
        $middleware = $this->getMockBuilder(MiddlewareInterface::class)->getMock();
        $middleware2 = $this->getMockBuilder(MiddlewareInterface::class)->getMock();
        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
        $middleware->expects($this->once())->method('process')->willReturn($response);
        $middleware2->expects($this->never())->method('process')->willReturn($response);
        $this->assertEquals($response, $this->app->pipe($middleware)->run($request));
    }

    public function testPipeWithClosure()
    {
        $middleware = $this->getMockBuilder(MiddlewareInterface::class)->getMock();
        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
        $middleware->expects($this->once())->method('process')->willReturn($response);
        $this->handler = new RequestHandler($this->fakeCallable);
        $this->app
            ->pipe(function ($request, $next) {
                return $next($request, $this->handler);
            })
            ->pipe($middleware);
        $this->assertEquals($response, $this->app->run($request));
    }

    /**
     *
     */
    public function testPipeWithoutMiddleware()
    {
        $this->expectException(\Exception::class);
        $this->app->run($this->getMockBuilder(ServerRequestInterface::class)->getMock());
    }

    public function testPipeWithPrefix()
    {
        $middleware = $this->getMockBuilder(MiddlewareInterface::class)->getMock();
        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $middleware->expects($this->once())->method('process')->willReturn($response);

        $this->app->pipe('/demo', $middleware);
        $this->assertEquals($response, $this->app->run(new ServerRequest('GET', '/demo/hello')));
        $this->expectException(Exception::class);
        $this->assertEquals($response, $this->app->run(new ServerRequest('GET', '/hello')));
    }
}
