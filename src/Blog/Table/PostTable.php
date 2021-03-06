<?php
/**
 * Created by IntelliJ IDEA.
 * User: meg4r0m
 * Date: 08/06/18
 * Time: 12:31
 */

namespace  App\Blog\Table;

use App\Blog\Entity\Post;
use App\Framework\Database\Query;
use App\Framework\Database\Table;

/**
 * Class PostTable
 * @package App\Blog\Table
 */
class PostTable extends Table
{

    /**
     * @var string
     */
    protected $entity = Post::class;

    /**
     * @var string
     */
    protected $table = 'posts';

    /**
     * @return Query
     */
    public function findAll(): Query
    {
        $category = new CategoryTable($this->pdo);
        return $this->makeQuery()
            ->join($category->getTable() . ' as c', 'c.id = p.categoryId')
            ->select('p.*, c.name as category_name, c.slug as category_slug')
            ->order('p.createdAt DESC');
    }

    /**
     * @return Query
     */
    public function findPublic(): Query
    {
        return $this->findAll()
            ->where('p.published = 1')
            ->where('p.createdAt < NOW()');
    }

    /**
     * @param int $id
     * @return Query
     */
    public function findPublicForCategory(int $id): Query
    {
        return $this->findPublic()->where("p.categoryId = $id");
    }

    /**
     * @param int $postId
     * @return Post
     */
    public function findWithCategory(int $postId): Post
    {
        return $this->findPublic()->where("p.id = $postId")->fetch();
    }
}
