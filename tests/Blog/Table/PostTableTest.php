<?php
/**
 * Created by IntelliJ IDEA.
 * User: meg4r0m
 * Date: 08/06/18
 * Time: 16:11
 */

namespace Test\App\Blog\Table;

use App\Blog\Entity\Post;
use App\Blog\Table\PostTable;
use App\Framework\Database\NoRecordException;
use Tests\DatabaseTestCase;

/**
 * Class PostTableTest
 *
 * @package Test\App\Blog\Table
 */
class PostTableTest extends DatabaseTestCase
{

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
        parent::setUp();
        $pdo = $this->getPDO();
        $this->migrateDatabase($pdo);
        $this->postTable = new PostTable($pdo);
    }//end setUp()


    /**
     *
     */
    public function testFind()
    {
        $this->seedDatabase($this->postTable->getPdo());
        $post = $this->postTable->find(1);
        $this->assertInstanceOf(Post::class, $post);
    }//end testFind()


    /**
     *
     */
    public function testFindNotFoundRecord()
    {
        $this->expectException(NoRecordException::class);
        $this->postTable->find(1);
    }//end testFindNotFoundRecord()


    /**
     *
     */
    public function testUpdate()
    {
        $this->seedDatabase($this->postTable->getPdo());
        $this->postTable->update(1, ['name' => 'Salut', 'slug' => 'demo']);
        $post = $this->postTable->find(1);
        $this->assertEquals('Salut', $post->name);
        $this->assertEquals('demo', $post->slug);
    }//end testUpdate()


    /**
     *
     */
    public function testInsert()
    {
        $this->postTable->insert(['name' => 'Salut', 'slug' => 'demo']);
        $post = $this->postTable->find(1);
        $this->assertEquals('Salut', $post->name);
        $this->assertEquals('demo', $post->slug);
    }//end testInsert()


    /**
     *
     */
    public function testDelete()
    {
        $this->postTable->insert(['name' => 'Salut', 'slug' => 'demo']);
        $this->postTable->insert(['name' => 'Salut', 'slug' => 'demo']);
        $count = $this->postTable->getPdo()->query('SELECT COUNT(id) FROM posts')->fetchColumn();
        $this->assertEquals(2, (int) $count);
        $this->postTable->delete($this->postTable->getPdo()->lastInsertId());
        $count = $this->postTable->getPdo()->query('SELECT COUNT(id) FROM posts')->fetchColumn();
        $this->assertEquals(1, (int) $count);
    }//end testDelete()
}//end class
