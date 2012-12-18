<?php

namespace FakeORM;

use Nette\Database\Table\ActiveRow as NActiveRow;

/**
 * @author wormik
 */
class ActiveRow extends NActiveRow {


    public function __construct(array $data, Selection $table) {
        parent::__construct($data, $table);
    }

}
