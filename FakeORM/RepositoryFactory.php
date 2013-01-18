<?php

namespace FakeORM;

use Nette\Utils\Arrays;

/**
 * @author wormik
 */
class RepositoryFactory extends \Nette\Object {

    /** @var array of domain name => class */
    protected $classes;

    /** @var array of domain name, key => domain name */
    protected $referenced;

    /** @var array of domain name, key => domain name */
    protected $related;

    /** @var EntityFactory */
    protected $entityFactory;

    /** @var SelectionFactory */
    protected $selectionFactory;


    public function __construct(EntityFactory $entityFactory, SelectionFactory $selectionFactory, array $classes = array(), array $ref = array(), array $related = array()) {
        $this->entityFactory = $entityFactory;
        $this->selectionFactory = $selectionFactory;
        if (!isset($classes[NULL]))
        	$classes[NULL] = 'FakeORM\Repository';
        $this->classes = $classes;
        $this->referenced = $ref;
        $this->related = $related;
    }


    /**
     * @param Selection|GroupedSelection|string $selection
     * @return Repository
     */
    public function create($type = NULL, $selection = NULL) {
        if ($selection === NULL)
            $selection = $this->selectionFactory->create($type);
        $class = Arrays::get($this->classes, $type, $this->classes[NULL]);
        return new $class($type, $selection, $this->entityFactory, $this);
    }


    public function createReferenced($type = NULL, $key = NULL, $selection = NULL) {
        $type = Arrays::get($this->referenced, array($type,$key), $key);
        return $this->create($type, $selection);
    }


    public function createRelated($type = NULL, $key = NULL, $selection = NULL) {
        $type = Arrays::get($this->related, array($type,$key), $key);
        return $this->create($type, $selection);
    }

}
