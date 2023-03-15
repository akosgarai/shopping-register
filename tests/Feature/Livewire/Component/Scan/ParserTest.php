<?php

namespace Tests\Feature\Livewire\Component\Scan;

use App\Http\Livewire\Component\Scan\Parser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class ParserTest extends TestCase
{
    /** @test */
    public function testTheComponentCanRender()
    {
        Livewire::test(Parser::class)->assertStatus(200);
    }
}
