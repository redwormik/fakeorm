<?php

namespace FakeORM;

use Nette\ArrayHash;
use Nette\Utils\Arrays;

/**
 * @author wormik
 */
class EntityFactory extends \Nette\Object {

    /** @var array */
    protected $classes;


    public function __construct(array $classes = array()) {
        if (!isset($classes[NULL]))
        	$classes[NULL] = 'FakeORM\Entity';
        $this->classes = $classes;
    }


    public function create($type = NULL, $data = NULL, Repository $repository = NULL) {
        $class = Arrays::get($this->classes, $type, $this->classes[NULL]);
        return new $class($this->normalizeData($data),$repository);
    }


    protected function normalizeData($data) {
        if ($data === NULL || is_array($data))
            return ArrayHash::from((array) $data);
        return $data;
    }

}
