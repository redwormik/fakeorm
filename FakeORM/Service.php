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
        return $data === NULL ? $entity : $entity->setData($data);
    }


    /**
     * @param mixed $data
     * @param Entity entity to update
     * @return Entity
     */
    public function saveData($data = NULL, Entity $entity = NULL) {
        if ($entity === NULL)
            $entity = $this->create();
        if ($data !== NULL)
            $entity->setData($data);
        return $this->save($entity);
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
        $data = $entity->getData();
        if ($data instanceof ActiveRow)
            $data->update();
        else
            $this->createRepository()->find($primary)->update($data);
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
