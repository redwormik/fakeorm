<?php

namespace FakeORM;

use Nette\Database\Connection;
use Nette\Utils\Arrays;

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
        $table = Arrays::get($this->tables, $name, $name);
        return new Selection($table, $this->connection);
    }

}
