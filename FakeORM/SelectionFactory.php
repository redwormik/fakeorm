<?php

namespace FakeORM;

use Nette\Database\Connection;

/**
 * @author wormik
 */
class SelectionFactory extends \Nette\Object {

    /** @var Connection */
    protected $connection;

    /** @var array of domain name => table name */
    protected $tables;


    public function __construct(Connection $connection, array $tables = array( )) {
        $this->connection = $connection;
        $this->tables = $tables;
    }


    public function create($name) {
        $table = isset($this->tables[$name]) ? $this->tables[$name] : $name;
        return new Selection($table, $this->connection);
    }

}
