<?php

namespace FakeORM;

use Nette\Database\Table\ActiveRow as NActiveRow;
use Nette\Database\Table\Selection as NSelection;

/**
 * @author wormik
 */
class ActiveRow extends NActiveRow {

    public function __construct(array $data, NSelection $table) {
    	if (!($table instanceof Selection || $table instanceof GroupedSelection))
    		throw new \Nette\InvalidArgumentException;
        parent::__construct($data, $table);
    }
    
}
