<?php

namespace FakeORM;

/**
 * Description of Repository
 *
 * @author wormik
 */
class Repository extends \Nette\Object implements \Iterator, \ArrayAccess, \Countable {

    /** @var string */
    protected $type;

    /** @var Selection|GroupedSelection */
    protected $selection;

    /** @var EntityFactory */
    protected $entityFactory;
    
    /** @var RepositoryFactory */
    protected $repositoryFactory;

    /** @var array of $id => Entity */
    protected $data = array( );

    /** @var array of $key,$throughColumn => Repository */
    protected $references = array( );


    public function __construct($type, $selection, EntityFactory $entityFactory, RepositoryFactory $repositoryFactory) {
        if (!($selection instanceof Selection || $selection instanceof GroupedSelection))
            throw new \Nette\InvalidArgumentException;
        $this->type = $type;
        $this->selection = $selection;
        $this->entityFactory = $entityFactory;
        $this->repositoryFactory = $repositoryFactory;
        $this->selection->onExecute[] = callback($this, 'selectionExecuted');
    }


    public function __clone() {
        $this->selection = clone $this->selection;
    }


    public function selectionExecuted() {
        $this->data = array( );
    }


    public function getPrimary() {
        return $this->selection->getPrimary();
    }

    /* SELECTION */


    /**
     * Returns row specified by primary key.
     * @param  mixed
     * @return Repository or NULL if there is no such row
     */
    public function get($key) {
        $clone = clone $this->selection;
        $clone->where($this->selection->getPrimary(), $key);
        return $clone->fetch();
    }


    /**
     * Adds select clause, more calls appends to the end.
     * @param  string for example "column, MD5(column) AS column_md5"
     * @return Repository provides a fluent interface
     */
    public function select($columns) {
        $this->selection->select($columns);
        return $this;
    }


    /**
     * Selects by primary key.
     * @param  mixed
     * @return Repository provides a fluent interface
     */
    public function find($key) {
        $this->selection->find($key);
        return $this;
    }


    /**
     * Adds where condition, more calls appends with AND.
     * @param  string condition possibly containing ?
     * @param  mixed
     * @param  mixed ...
     * @return Repository provides a fluent interface
     */
    public function where($condition, $parameters = array( )) {
        call_user_func_array(array( $this->selection, 'where' ), func_get_args());
        return $this;
    }


    /**
     * Adds order clause, more calls appends to the end.
     * @param  string for example 'column1, column2 DESC'
     * @return Repository provides a fluent interface
     */
    public function order($columns) {
        $this->selection->order($columns);
        return $this;
    }


    /**
     * Sets limit clause, more calls rewrite old values.
     * @param  int
     * @param  int
     * @return Repository provides a fluent interface
     */
    public function limit($limit, $offset = NULL) {
        $this->selection->limit($limit, $offset);
        return $this;
    }


    /**
     * Sets offset using page number, more calls rewrite old values.
     * @param  int
     * @param  int
     * @return Repository provides a fluent interface
     */
    public function page($page, $itemsPerPage) {
        $this->selection->page($page, $itemsPerPage);
        return $this;
    }


    /**
     * Sets group clause, more calls rewrite old values.
     * @param  string
     * @param  string
     * @return Repository provides a fluent interface
     */
    public function group($columns, $having = '') {
        $this->selection->group($columns, $having);
        return $this;
    }

    /* AGGREGATION */


    /**
     * Executes aggregation function.
     * @param  string
     * @return string
     */
    public function aggregation($function) {
        return $this->selection->aggregation($function);
    }


    /**
     * Returns minimum value from a column.
     * @param  string
     * @return int
     */
    public function min($column) {
        return $this->selection->min($column);
    }


    /**
     * Returns maximum value from a column.
     * @param  string
     * @return int
     */
    public function max($column) {
        return $this->selection->max($column);
    }


    /**
     * Counts number of rows.
     * @param  string
     * @return int
     */
    public function count($column = '') {
        return $this->selection->count($column);
    }


    /**
     * Returns sum of values in a column.
     * @param  string
     * @return int
     */
    public function sum($column) {
        return $this->selection->sum($column);
    }

    /* RETRIEVAL */


    /**
     * Returns next row of result.
     * @return Entity or FALSE if there is no row
     */
    public function fetch() {
        return $this->getEntity($this->selection->fetch());
    }


    /**
     * Returns all rows as associative array.
     * @param  string
     * @param  string column name used for an array value or an empty string for the whole row
     * @return array
     */
    public function fetchPairs($key, $value = '') {
        $return = array( );
        foreach ($this as $row) {
            $return[is_object($row->$key) ? (string) $row->$key : $row->$key] = ($value ? $row->$value : $row);
        }
        return $return;
    }

    /* MANIPULATION */


    public function insert(Entity $entity) {
        $row = $this->selection->insert($entity->getData());
        return $this->entityFactory->create($this->type, $row, $this);
    }


    public function update($data) {
        return $this->selection->update($data);
    }


    public function delete() {
        return $this->selection->delete();
    }

    /* INTERFACES */


    public function current() {
        return $this->getEntity($this->selection->current());
    }


    public function key() {
        return $this->selection->key();
    }


    public function next() {
        return $this->selection->next();
    }


    public function rewind() {
        return $this->selection->rewind();
    }


    public function valid() {
        return $this->selection->valid();
    }


    public function offsetExists($offset) {
        return $this->selection->offsetExists($offset);
    }


    public function offsetGet($offset) {
        return $this->getEntity($this->selection->offsetGet($offset));
    }


    /**
     * @param string $offset
     * @param Entity $value
     * @return NULL
     */
    public function offsetSet($offset, $value) {
        $this->data[$offset] = $value;
        return $this->selection->offsetSet($offset, $value->getData());
    }


    public function offsetUnset($offset) {
        unset($this->data[$offset]);
        return $this->selection->offsetUnset($offset);
    }


    /**
     * @param ActiveRow $row
     */
    protected function getEntity($row) {
        if (!$row)
            return $row;
        $id = $row->getSignature(TRUE);
        if (!isset($this->data[$id]))
            $this->data[$id] = $this->entityFactory->create($this->type, $row, $this);
        return $this->data[$id];
    }


    public function getReferenced($key, $throughColumn, ActiveRow $row) {
        $through = $throughColumn ? : $key;
        $table = $row->getTable();
        if (!isset($this->references[$key][$through]) || $this->references[$key][$through]->selection !== $table)
            $this->references[$key][$through] = $this->repositoryFactory->createReferenced($this->type, $key, $table);
        return $this->references[$key][$through][$row->getPrimary()];
    }


    public function getRelated($key, $throughColumn, GroupedSelection $table) {
        return $this->repositoryFactory->createRelated($this->type, $key, $table);
    }

}
