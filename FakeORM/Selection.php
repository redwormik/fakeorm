<?php

namespace FakeORM;

use Nette\Database\Table\Selection as NSelection;

/**
 * @author wormik
 */
final class Selection extends NSelection {

    public $onExecute = array( );

    
    public function insert($data) {
        $row = parent::insert($data);
        if ($row instanceof ActiveRow && $this->prevAccessed === NULL && $this->accessed === NULL) {
            $this->accessed = $this->prevAccessed = array_fill_keys(array_keys($row->toArray()), TRUE);
        }
        return $row;
    }

    
    protected function execute() {
        if ($this->rows !== NULL)
            return;
        parent::execute();
        $this->onExecute();
    }


    protected function createRow(array $row) {
        return new ActiveRow($row, $this);
    }


    protected function createSelectionInstance($table = NULL) {
        return new Selection($table ? : $this->name, $this->connection);
    }


    protected function createGroupedSelectionInstance($table, $column) {
        return new GroupedSelection($this, $table, $column);
    }

}
