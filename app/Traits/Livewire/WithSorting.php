<?php

namespace App\Traits\Livewire;

trait WithSorting
{
    // list ordering
    public $orderColumn = 'id';
    public $orderDirection = self::ORDER_ASC;

    // This method is called when the orderable column is changed.
    public function orderColumnChangeTo($column)
    {
        // If the column is not orderable, do nothing.
        if (!in_array($column, static::ORDERABLE_COLUMNS)) {
            return;
        }
        if ($this->orderColumn == $column) {
            $this->orderDirection = $this->orderDirection == self::ORDER_ASC ? self::ORDER_DESC : self::ORDER_ASC;
            return;
        }
        $this->orderColumn = $column;
        $this->orderDirection = self::ORDER_ASC;
    }
}
