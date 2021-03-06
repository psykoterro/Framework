<?php
/**
 * Created by IntelliJ IDEA.
 * User: meg4r0m
 * Date: 18/06/18
 * Time: 11:07
 */

namespace App\Framework\Database;

/**
 * Représente les résultats d'une requête
 *
 * Class QueryResult
 *
 * @package App\Framework\Database
 */
class QueryResult implements \ArrayAccess, \Iterator
{

    /**
     *
     * @var array Les enregistrements
     */
    private $records;

    /**
     *
     * @var null|string Entité à utiliser pour hydrater nos objets
     */
    private $entity;

    /**
     *
     * @var int Index servant à l'itération
     */
    private $index = 0;

    /**
     *
     * @var array Sauvegarde les enregistrements déjà hydratés
     */
    private $hydratedRecords = [];


    /**
     * QueryResult constructor.
     *
     * @param array       $records
     * @param null|string $entity
     */
    public function __construct(array $records, ?string $entity = null)
    {
        $this->records = $records;
        $this->entity  = $entity;
    }//end __construct()

    /**
     * Récupère un éléments à l'index définit
     *
     * @param  integer $index
     * @return mixed|null|string
     */
    public function get(int $index)
    {
        if ($this->entity) {
            if (!isset($this->hydratedRecords[$index])) {
                $this->hydratedRecords[$index] = Hydrator::hydrate($this->records[$index], $this->entity);
            }
            return $this->hydratedRecords[$index];
        }
        return $this->entity;
    }//end get()

    /**
     * Return the current element
     *
     * @link   http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since  5.0.0
     */
    public function current()
    {
        return $this->get($this->index);
    }//end current()

    /**
     * Move forward to next element
     *
     * @link   http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since  5.0.0
     */
    public function next(): void
    {
        $this->index++;
    }//end next()

    /**
     * Return the key of the current element
     *
     * @link   http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since  5.0.0
     */
    public function key()
    {
        return $this->index;
    }//end key()

    /**
     * Checks if current position is valid
     *
     * @link   http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since  5.0.0
     */
    public function valid(): bool
    {
        return isset($this->records[$this->index]);
    }//end valid()

    /**
     * Rewind the Iterator to the first element
     *
     * @link   http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since  5.0.0
     */
    public function rewind(): void
    {
        $this->index = 0;
    }//end rewind()

    /**
     * Whether a offset exists
     *
     * @link   http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param  mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since  5.0.0
     */
    public function offsetExists($offset): bool
    {
        return isset($this->records[$offset]);
    }//end offsetExists()

    /**
     * Offset to retrieve
     *
     * @link   http://php.net/manual/en/arrayaccess.offsetget.php
     * @param  mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since  5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }//end offsetGet()

    /**
     * Offset to set
     *
     * @link   http://php.net/manual/en/arrayaccess.offsetset.php
     * @param  mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param  mixed $value  <p>
     *  The value to set.
     *  </p>
     * @return void
     * @since  5.0.0
     * @throws \Exception
     */
    public function offsetSet($offset, $value): void
    {
        throw new \Exception("Can't alter records");
    }//end offsetSet()

    /**
     * Offset to unset
     *
     * @link   http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param  mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since  5.0.0
     * @throws \Exception
     */
    public function offsetUnset($offset): void
    {
        throw new \Exception("Can't alter records");
    }//end offsetUnset()
}//end class
