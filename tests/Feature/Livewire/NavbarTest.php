<?php

namespace Tests\Feature\Livewire;

use App\Http\Livewire\Navbar;
use Livewire\Livewire;
use Tests\TestCase;

class NavbarTest extends TestCase
{
    public function testTheComponentCanRender()
    {
        Livewire::test(Navbar::class)->assertStatus(200);
    }

    public function testSearch()
    {
        Livewire::test(Navbar::class)
            ->set('search', 'test')
            ->call('search')
            ->assertEmitted('search', 'test');
    }
}
