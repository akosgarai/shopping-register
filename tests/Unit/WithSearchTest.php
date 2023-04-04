<?php

namespace Tests\Unit;

use App\Traits\Livewire\WithSearch;

use PHPUnit\Framework\TestCase;

class WithSearchTraitUser {
    use WithSearch;

    public function resetPage() {
        // do nothing
    }
}

class WithSearchTest extends TestCase
{
    function testSearch()
    {
        $user1 = new WithSearchTraitUser();
        $this->assertEquals('', $user1->search);
        $user1->search('test');
        $this->assertEquals('test', $user1->search);
    }
}
