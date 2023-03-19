<?php

namespace Tests\Feature\Livewire\Component;

use App\Http\Livewire\Component\Imageditor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class ImageditorTest extends TestCase
{
    public function testTheComponentCanRender()
    {
        Livewire::test(Imageditor::class)->assertStatus(200);
    }

    public function testInitState()
    {
        $this->caseInit();
    }

    public function testCropMode()
    {
        $case = $this->caseInit();
        $this->goCropMode($case);
    }

    public function testCropModeCancel()
    {
        $case = $this->caseInit();
        $case = $this->goCropMode($case);
        $case->call('broadcastCancel')
            ->assertSet('editMode', false)
            ->assertDispatchedBrowserEvent(Imageditor::EVENT_CANCEL, ['action' => Imageditor::MODE_CROP]);
    }

    public function testCropModeApply()
    {
        $case = $this->caseInit();
        $case = $this->goCropMode($case);
        $case->call('broadcastApply')
            ->assertSet('editMode', false)
            ->assertDispatchedBrowserEvent(Imageditor::EVENT_APPLY.Imageditor::MODE_CROP);
    }

    public function testBroadcastFilter()
    {
        $case = $this->caseInit();
        // Turn all filters on.
        foreach (array_keys($case->get('filters')) as $filter) {
            $case->call('broadcastFilter', $filter)
                ->assertSet('filters.'.$filter, true)
                ->assertDispatchedBrowserEvent(Imageditor::EVENT_FILTER, ['filter' => $filter, 'value' => true]);
        }
        // Turn all filters off.
        foreach (array_keys($case->get('filters')) as $filter) {
            $case->call('broadcastFilter', $filter)
                ->assertSet('filters.'.$filter, false)
                ->assertDispatchedBrowserEvent(Imageditor::EVENT_FILTER, ['filter' => $filter, 'value' => false]);
        }
    }

    public function testBroadcastEditComplete()
    {
        $case = $this->caseInit();
        $case->call('broadcastEditComplete')
            ->assertSet('editMode', false)
            ->assertDispatchedBrowserEvent(Imageditor::EVENT_EDIT_COMPLETE);
    }

    private function caseInit()
    {
        $case = Livewire::test(Imageditor::class)
            ->assertSet('editMode', false)
            // Crop button has to be visible.
            ->assertSeeHtml('<button class="btn btn-outline-secondary" type="button" wire:click="broadcastCrop">'. __('Crop') .'</button>')
            // Filter button has to be visible.
            ->assertSeeHtml('<button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">'. __('Filter') .'</button>')
            // Next button has to be visible.
            ->assertSeeHtml('<button class="btn btn-outline-secondary" type="button" wire:click="broadcastEditComplete">'. __('Next') .'</button>');
        // Filter options have to be in the dropdown.
        foreach (array_keys($case->get('filters')) as $filter) {
            $case->assertSeeHtml('<input class="form-check-input" type="checkbox" value="" id="filter'.$filter.'" wire:click="broadcastFilter(\''.$filter.'\')">');
        }
        return $case;
    }

    private function goCropMode($case)
    {
        $case->call('broadcastCrop')
            ->assertSet('editMode', Imageditor::MODE_CROP)
            ->assertDontSeeHtml('<button class="btn btn-outline-secondary" type="button" wire:click="broadcastCrop">'. __('Crop') .'</button>')
            ->assertDontSeeHtml('<button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">'. __('Filter') .'</button>')
            ->assertDontSeeHtml('<button class="btn btn-outline-secondary" type="button" wire:click="broadcastEditComplete">'. __('Next') .'</button>')
            ->assertSeeHtml('<button class="btn btn-outline-secondary" type="button" wire:click="broadcastApply">'. __('Apply') .'</button>')
            ->assertSeeHtml('<span class="input-group-text">Options</span>')
            ->assertSeeHtml('<button class="btn btn-outline-secondary" type="button" wire:click="broadcastCancel">'. __('Cancel') .'</button>')
            ->assertDispatchedBrowserEvent(Imageditor::EVENT_CROP);
        return $case;
    }
}
