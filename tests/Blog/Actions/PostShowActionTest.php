<?php
/**
 * Created by IntelliJ IDEA.
 * User: meg4r0m
 * Date: 08/06/18
 * Time: 11:59
 */

namespace Test\App\Blog\Actions;

use App\Blog\Actions\PostShowAction;
use App\Blog\Entity\Post;
use App\Blog\Table\PostTable;
use App\Framework\Database\NoRecordException;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use GuzzleHttp\Psr7\ServerRequest;
use PDO;
use PHPUnit\Framework\TestCase;

/**
 * Class BlogActionTest
 *
 * @package Test\App\Blog\Actions
 */
class PostShowActionTest extends TestCase
{

    /**
     *
     * @var PostShowActionTest
     */
    private $action;

    /**
     *
     * @var RendererInterface
     */
    private $renderer;

    /**
     *
     * @var Router
     */
    private $router;

    /**
     *
     * @var PostTable
     */
    private $postTable;


    /**
     *
     */
    public function setUp()
    {
        $this->renderer  = $this->prophesize(RendererInterface::class);
        $this->postTable = $this->prophesize(PostTable::class);
        // PDO
        $this->router = $this->prophesize(Router::class);
        $this->action = new PostShowAction(
            $this->renderer->reveal(),
            $this->router->reveal(),
            $this->postTable->reveal()
        );
    }//end setUp()


    /**
     *
     * @param  integer $id
     * @param  string  $slug
     * @return Post
     */
    public function makePost(int $id, string $slug): Post
    {
        // Article
        $post       = new Post();
        $post->id   = $id;
        $post->slug = $slug;
        return $post;
    }//end makePost()


    /**
     *
     * @throws NoRecordException
     */
    public function testShowRedirect()
    {
        $post    = $this->makePost(9, 'azeaze-azeaze');
        $request = (new ServerRequest('GET', '/'))->withAttribute('id', $post->id)->withAttribute('slug', 'demo');

        $this->router->generateUri(
            'blog.show',
            [
                'id'   => $post->id,
                'slug' => $post->slug,
            ]
        )->willReturn('/demo2');
        $this->postTable->findWithCategory($post->id)->willReturn($post);

        $response = call_user_func_array($this->action, [$request]);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals(['/demo2'], $response->getHeader('location'));
    }//end testShowRedirect()


    /**
     *
     * @throws NoRecordException
     */
    public function testShowRender()
    {
        $post    = $this->makePost(9, 'azeaze-azeaze');
        $request = (new ServerRequest('GET', '/'))->withAttribute('id', $post->id)->withAttribute('slug', $post->slug);
        $this->postTable->findWithCategory($post->id)->willReturn($post);
        $this->renderer->render('@blog/show', ['post' => $post])->willReturn('');

        $response = call_user_func_array($this->action, [$request]);
        $this->assertEquals(true, true);
    }//end testShowRender()
}//end class
