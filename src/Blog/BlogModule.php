<?php
/**
 * Created by IntelliJ IDEA.
 * User: meg4r0m
 * Date: 04/06/18
 * Time: 20:53
 */
namespace App\Blog;

use App\Blog\Actions\CategoryCrudAction;
use App\Blog\Actions\CategoryShowAction;
use App\Blog\Actions\PostCrudAction;
use App\Blog\Actions\PostIndexAction;
use App\Blog\Actions\PostShowAction;
use App\Framework\Middleware\CombinedMiddleware;
use App\Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Container\ContainerInterface;

/**
 * Class BlogModule
 *
 * @package App\Blog
 */
class BlogModule extends Module
{

    /**
     *
     */
    const DEFINITIONS = __DIR__.'/config.php';

    /**
     *
     */
    const MIGRATIONS = __DIR__.'/db/migrations';

    /**
     *
     */
    const SEEDS = __DIR__.'/db/seeds';

    /**
     * BlogModule constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $blogPrefix = $container->get('blog.prefix');
        $container->get(RendererInterface::class)->addPath('blog', __DIR__.'/views');
        $router = $container->get(Router::class);
        $router->get(
            $blogPrefix,
            new CombinedMiddleware($container, [PostIndexAction::class]),
            'blog.index'
        );
        $router->get(
            $blogPrefix.'/{slug:[a-z\-0-9]+}-{id:[0-9]+}',
            new CombinedMiddleware($container, [PostShowAction::class]),
            'blog.show'
        );
        $router->get(
            $blogPrefix.'/categories/{slug:[a-z\-0-9]+}',
            new CombinedMiddleware($container, [CategoryShowAction::class]),
            'blog.category'
        );

        if ($container->has('admin.prefix')) {
            $prefix = $container->get('admin.prefix');
            $router->crud(
                $prefix.'/posts',
                new CombinedMiddleware($container, [PostCrudAction::class]),
                'blog.admin'
            );
            $router->crud(
                $prefix.'/categories',
                new CombinedMiddleware($container, [CategoryCrudAction::class]),
                'blog.category.admin'
            );
        }
    }//end __construct()
}//end class
