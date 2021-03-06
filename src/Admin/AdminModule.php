<?php
/**
 * Created by IntelliJ IDEA.
 * User: meg4r0m
 * Date: 08/06/18
 * Time: 17:38
 */

namespace App\Admin;

use App\Framework\Middleware\CombinedMiddleware;
use App\Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Renderer\TwigRenderer;
use Framework\Router;
use Psr\Container\ContainerInterface;

/**
 * Class AdminModule
 * @package App\Admin
 */
class AdminModule extends Module
{

    /**
     *
     */
    const DEFINITIONS = __DIR__.'/config.php';

    /**
     * AdminModule constructor.
     * @param RendererInterface $renderer
     * @param Router $router
     * @param string $prefix
     * @param AdminTwigExtension $adminTwigExtension
     * @param ContainerInterface $container
     */
    public function __construct(
        RendererInterface $renderer,
        Router $router,
        string $prefix,
        AdminTwigExtension $adminTwigExtension,
        ContainerInterface $container
    ) {
        $renderer->addPath('admin', __DIR__.'/views');
        $router->get($prefix, new CombinedMiddleware($container, [DashboardAction::class]), 'admin');
        if ($renderer instanceof TwigRenderer) {
            $renderer->getTwig()->addExtension($adminTwigExtension);
        }
    }//end __construct()
}//end class
