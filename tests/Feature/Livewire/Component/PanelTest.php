<?php

namespace Tests\Feature\Livewire\Component;

use App\Http\Livewire\Component\Panel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PanelTest extends TestCase
{
    use RefreshDatabase;

    const TEST_PANEL_NAME = 'TestPanelName';
    const TEST_PANEL_TITLE = 'Test Panel Title';

    public function testTheComponentCanRender()
    {
        Livewire::test(Panel::class)->assertStatus(200);
    }

    public function testInitialParameters()
    {
        $this->caseNoConfig();
        $this->caseRightPosition();
        $this->caseBackdrop();
    }

    public function testOpenPanel()
    {
        $caseDefault = $this->caseNoConfig();
        $caseDefault->emit('panel.open', self::TEST_PANEL_NAME)
            ->assertSet('open', true)
            ->assertSeeHtml('<div class="panel left   visible " aria-labelledby="'.self::TEST_PANEL_NAME.'Label">')
            // check that the backdrop is not in the HTML
            ->assertDontSeeHtml('<div class="backdrop"></div>');
        $caseRight = $this->caseRightPosition();
        $caseRight->emit('panel.open', self::TEST_PANEL_NAME)
            ->assertSet('open', true)
            ->assertSeeHtml('<div class="panel right   visible " aria-labelledby="'.self::TEST_PANEL_NAME.'Label">')
            // check that the backdrop is not in the HTML
            ->assertDontSeeHtml('<div class="backdrop"></div>');
        $caseBackdrop = $this->caseBackdrop();
        $caseBackdrop->emit('panel.open', self::TEST_PANEL_NAME)
            ->assertSet('open', true)
            ->assertSeeHtml('<div class="panel right   visible " aria-labelledby="'.self::TEST_PANEL_NAME.'Label">')
            // check that the backdrop is in the HTML
            ->assertSeeHtml('<div class="backdrop"></div>');
    }

    public function testClosePanel()
    {
        $caseDefault = $this->caseNoConfig();
        $caseDefault->emit('panel.open', self::TEST_PANEL_NAME)
            ->assertSet('open', true)
            ->assertSeeHtml('<div class="panel left   visible " aria-labelledby="'.self::TEST_PANEL_NAME.'Label">')
            // check that the backdrop is not in the HTML
            ->assertDontSeeHtml('<div class="backdrop"></div>')
            ->emit('panel.close', self::TEST_PANEL_NAME)
            ->assertSet('open', false)
            ->assertDontSeeHtml('<div class="panel left   visible " aria-labelledby="'.self::TEST_PANEL_NAME.'Label">')
            ->assertSeeHtml('<div class="panel left  " aria-labelledby="'.self::TEST_PANEL_NAME.'Label">')
            // check that the backdrop is not in the HTML
            ->assertDontSeeHtml('<div class="backdrop"></div>');
        $caseRight = $this->caseRightPosition();
        $caseRight->emit('panel.open', self::TEST_PANEL_NAME)
            ->assertSet('open', true)
            ->assertSeeHtml('<div class="panel right   visible " aria-labelledby="'.self::TEST_PANEL_NAME.'Label">')
            // check that the backdrop is not in the HTML
            ->assertDontSeeHtml('<div class="backdrop"></div>')
            ->emit('panel.close', self::TEST_PANEL_NAME)
            ->assertSet('open', false)
            ->assertDontSeeHtml('<div class="panel right   visible " aria-labelledby="'.self::TEST_PANEL_NAME.'Label">')
            ->assertSeeHtml('<div class="panel right  " aria-labelledby="'.self::TEST_PANEL_NAME.'Label">')
            // check that the backdrop is not in the HTML
            ->assertDontSeeHtml('<div class="backdrop"></div>');
        $caseBackdrop = $this->caseBackdrop();
        $caseBackdrop->emit('panel.open', self::TEST_PANEL_NAME)
            ->assertSet('open', true)
            ->assertSeeHtml('<div class="panel right   visible " aria-labelledby="'.self::TEST_PANEL_NAME.'Label">')
            // check that the backdrop is in the HTML
            ->assertSeeHtml('<div class="backdrop"></div>')
            ->emit('panel.close', self::TEST_PANEL_NAME)
            ->assertSet('open', false)
            ->assertDontSeeHtml('<div class="panel right   visible " aria-labelledby="'.self::TEST_PANEL_NAME.'Label">')
            ->assertSeeHtml('<div class="panel right  " aria-labelledby="'.self::TEST_PANEL_NAME.'Label">')
            // check that the backdrop is not in the HTML
            ->assertDontSeeHtml('<div class="backdrop"></div>');
    }

    public function testUpdateParameters()
    {
        $newContentParameters = ['new'=>'content','parameters'=>'for','the' =>'panel'];
        $caseDefault = $this->caseNoConfig();
        $caseDefault->emit('panel.update', self::TEST_PANEL_NAME, $newContentParameters)
            ->assertSet('contentParameters', $newContentParameters);
        $caseRight = $this->caseRightPosition();
        $caseRight->emit('panel.update', self::TEST_PANEL_NAME, $newContentParameters)
            ->assertSet('contentParameters', $newContentParameters);
        $caseBackdrop = $this->caseBackdrop();
        $caseBackdrop->emit('panel.update', self::TEST_PANEL_NAME, $newContentParameters)
            ->assertSet('contentParameters', $newContentParameters);
    }

    private function caseNoConfig()
    {
        return Livewire::test(Panel::class, ['panelName' => self::TEST_PANEL_NAME, 'panelTitle' => self::TEST_PANEL_TITLE, 'contentParameters' => []])
            ->assertSet('panelName', self::TEST_PANEL_NAME)
            ->assertSet('panelTitle', self::TEST_PANEL_TITLE)
            ->assertSet('contentParameters', [])
            ->assertSet('open', false)
            // Check the title and name are in the HTML
            ->assertSeeHtml('<h3 class="panel-title" id="'.self::TEST_PANEL_NAME.'Label">'.self::TEST_PANEL_TITLE.'</h3>')
            // check the main configuration classes are in the HTML
            ->assertSeeHtml('<div class="panel left  " aria-labelledby="'.self::TEST_PANEL_NAME.'Label">')
            // check that the backdrop is not in the HTML
            ->assertDontSeeHtml('<div class="backdrop"></div>');
    }
    private function caseRightPosition()
    {
        return Livewire::test(Panel::class, ['panelName' => self::TEST_PANEL_NAME, 'panelTitle' => self::TEST_PANEL_TITLE, 'contentParameters' => [], 'position' => 'right'])
            ->assertSet('panelName', self::TEST_PANEL_NAME)
            ->assertSet('panelTitle', self::TEST_PANEL_TITLE)
            ->assertSet('contentParameters', [])
            ->assertSet('open', false)
            // Check the title and name are in the HTML
            ->assertSeeHtml('<h3 class="panel-title" id="'.self::TEST_PANEL_NAME.'Label">'.self::TEST_PANEL_TITLE.'</h3>')
            // check the main configuration classes are in the HTML
            ->assertSeeHtml('<div class="panel right  " aria-labelledby="'.self::TEST_PANEL_NAME.'Label">')
            // check that the backdrop is not in the HTML
            ->assertDontSeeHtml('<div class="backdrop"></div>');
    }
    private function caseBackdrop()
    {
        return Livewire::test(Panel::class, ['panelName' => self::TEST_PANEL_NAME, 'panelTitle' => self::TEST_PANEL_TITLE, 'contentParameters' => [], 'position' => 'right', 'backdrop' => true])
            ->assertSet('panelName', self::TEST_PANEL_NAME)
            ->assertSet('panelTitle', self::TEST_PANEL_TITLE)
            ->assertSet('contentParameters', [])
            ->assertSet('open', false)
            // Check the title and name are in the HTML
            ->assertSeeHtml('<h3 class="panel-title" id="'.self::TEST_PANEL_NAME.'Label">'.self::TEST_PANEL_TITLE.'</h3>')
            // check the main configuration classes are in the HTML
            ->assertSeeHtml('<div class="panel right  " aria-labelledby="'.self::TEST_PANEL_NAME.'Label">')
            // check that the backdrop is not in the HTML
            ->assertDontSeeHtml('<div class="backdrop"></div>');
    }
}
