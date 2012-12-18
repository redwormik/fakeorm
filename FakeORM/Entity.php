<?php

namespace FakeORM;

use Nette\ArrayHash;

/**
 * @author wormik
 */
class Entity extends \Nette\Object {

    /** @var ActiveRow */
    protected $data;
    
    /** @var Repository */
    protected $repository;


    /**
     * @param ActiveRow|ArrayHash $data
     * @throws \Nette\InvalidArgumentException
     */
    public function __construct($data, Repository $repository = NULL) {
        if (!($data instanceof ActiveRow || $data instanceof ArrayHash))
            throw new \Nette\InvalidArgumentException;
        $this->data = $data;
        $this->repository = $repository;
    }


    public function __clone() {
        $this->data = clone $this->data;
    }


    /**
     * Returns ID
     * @return mixed or NULL
     */
    public function getPrimary() {
        $column = $this->repository->getPrimary();
        return $this->data->$column;
    }


    public function getData() {
        return $this->data;
    }


    /**
     * @param string
     * @param string
     * @return Entity or NULL if does not exist
     */
    public function ref($key, $throughColumn = NULL) {
        if (!method_exists($this->data, 'ref') || $this->repository === NULL)
            return NULL;
        $row = $this->data->ref($key, $throughColumn);
        if (!$row)
            return $row;
        $repository = $this->repository->getReferenced($key, $throughColumn, $row->getTable());
        return $repository[$row->getPrimary()];
    }


    /**
     * Returns referencing entities.
     * @param  string
     * @param  string
     * @return Repository or array if does not exist
     */
    public function related($key, $throughColumn = NULL) {
        if (!method_exists($this->data, 'related') || $this->repository === NULL)
            return array( );
        $table = $this->data->related($key, $throughColumn);
        return $this->repository->getRelated($key, $throughColumn, $table);
    }


    public function __set($name, $value) {
        if (parent::__isset($name))
            parent::__set($name, $value);
        else
            $this->row->$name = $value;
    }


    public function & __get($name) {
        if (parent::__isset($name))
            return parent::__get($name);
        $value = $this->data->$name;
        if ($value instanceof ActiveRow)
            return $this->repository->ref($name, NULL, $value);
        return $value;
    }


    public function __isset($name) {
        return parent::__isset($name) || isset($this->data->$name);
    }


    public function __unset($name) {
        if (parent::__isset($name))
            parent::__unset($name);
        else
            unset($this->data->$name);
    }

}
