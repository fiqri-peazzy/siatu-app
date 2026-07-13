<?php

namespace App\Livewire\Master\Mapel;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.app')]
#[Title('Data Mapel')]
class MapelIndex extends Component
{
    public function render()
    {
        return view('livewire.master.mapel.mapel-index');
    }
}
