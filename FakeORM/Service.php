<?php

namespace FakeORM;

/**
 * @author wormik
 */
class Service extends \Nette\Object {

    /** @var string */
    protected $name;

    /** @var EntityFactory */
    protected $entityFactory;

    /** @var RepositoryFactory */
    protected $repositoryFactory;


    /**
     * @param string $name
     * @param EntityFactory $entityFactory
     * @param RepositoryFactory $repositoryFactory
     */
    public function __construct($name, EntityFactory $entityFactory, RepositoryFactory $repositoryFactory) {
        $this->name = $name;
        $this->entityFactory = $entityFactory;
        $this->repositoryFactory = $repositoryFactory;
    }


    /**
     * @param mixed $data
     * @return Entity
     */
    public function create($data = NULL) {
        $entity = $this->entityFactory->create($this->name);
        if ($data === NULL)
            return $entity;
        foreach ($data as $key => $value)
            $entity->$key = $value;
        return $entity;
    }


    /**
     * @return Repository
     */
    public function createRepository() {
        return $this->repositoryFactory->create($this->name);
    }


    public function save(Entity $entity) {
        $primary = $entity->getPrimary();
        if ($primary === NULL)
            return $this->createRepository()->insert($entity);
        $this->createRepository()->find($primary)->update($entity->getData());
        return $entity;
    }


    public function get($id) {
        return $this->createRepository()->find($id)->fetch();
    }


    public function delete(Entity $entity) {
        $primary = $entity->getPrimary();
        return $this->createRepository()->find($primary)->delete();
    }

}
