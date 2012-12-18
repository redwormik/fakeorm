<?php

namespace FakeORM;

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
        $class = isset($this->classes[$type]) ? $this->classes[$type] : '\FakeORM\Repository';
        return new $class($type, $selection, $this->entityFactory);
    }


    public function createReferenced($type = NULL, $key = NULL, $selection = NULL) {
        $type = isset($this->referenced[$type][$key]) ? $this->referenced[$type][$key] : $key;
        return $this->create($type, $selection);
    }


    public function createRelated($type = NULL, $key = NULL, $selection = NULL) {
        $type = isset($this->related[$type][$key]) ? $this->related[$type][$key] : $key;
        return $this->create($type, $selection);
    }

}
