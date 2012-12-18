<?php

namespace FakeORM;

use Nette\ArrayHash;

/**
 * @author wormik
 */
class EntityFactory extends \Nette\Object {

    /** @var array */
    protected $classes;


    public function __construct(array $classes = array()) {
        $this->classes = $classes;
    }


    public function create($type = NULL, $data = NULL, Repository $repository = NULL) {
        $class = isset($this->classes[$type]) ? $this->classes[$type] : '\FakeORM\Entity';
        return new $class($this->normalizeData($data),$repository);
    }


    protected function normalizeData($data) {
        if ($data === NULL || is_array($data))
            return ArrayHash::from((array) $data);
        return $data;
    }

}
