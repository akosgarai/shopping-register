<?php

namespace Tests\Unit;

use App\Traits\Livewire\WithSorting;

use PHPUnit\Framework\TestCase;

class WithSortingTraitUser {
    use WithSorting;

    const ORDERABLE_COLUMNS = ['name', 'age', 'id'];
    const ORDER_ASC = 'asc';
    const ORDER_DESC = 'desc';
}

class WithSortingTest extends TestCase
{
    function testOrderColumnChangeTo()
    {
        $user1 = new WithSortingTraitUser();
        $this->assertEquals('id', $user1->orderColumn);
        $this->assertEquals(WithSortingTraitUser::ORDER_ASC, $user1->orderDirection);
        $user1->orderColumnChangeTo('name');
        $this->assertEquals('name', $user1->orderColumn);
        $this->assertEquals(WithSortingTraitUser::ORDER_ASC, $user1->orderDirection);
        $user1->orderColumnChangeTo('age');
        $this->assertEquals('age', $user1->orderColumn);
        $this->assertEquals(WithSortingTraitUser::ORDER_ASC, $user1->orderDirection);
        $user1->orderColumnChangeTo('age');
        $this->assertEquals('age', $user1->orderColumn);
        $this->assertEquals(WithSortingTraitUser::ORDER_DESC, $user1->orderDirection);
        $user1->orderColumnChangeTo('wrong-column');
        $this->assertEquals('age', $user1->orderColumn);
        $this->assertEquals(WithSortingTraitUser::ORDER_DESC, $user1->orderDirection);
    }
}
