<?php

namespace Tests\Feature\Livewire\Component;

use App\Http\Livewire\Component\ImageManipulator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class ImageManipulatorTest extends TestCase
{
    public function testTheComponentCanRender()
    {
        Livewire::test(ImageManipulator::class)->assertStatus(200);
    }

    public function testWithoutImage()
    {
        Livewire::test(ImageManipulator::class)
            ->assertSet('image', null)
            ->assertSeeHtml('<div id="tui-image-editor"></div>')
        ;
    }

    public function testWithImage()
    {
        $image = 'test.jpg';
        Livewire::test(ImageManipulator::class, ['image' => $image])
            ->assertSet('image', $image)
            ->assertSeeHtml('<div id="tui-image-editor"></div>')
        ;
    }
}
