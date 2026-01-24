<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class InstructionOverlay extends Component
{
    public $isOpen = false;

    #[On('open-instruction-overlay')]
    public function open()
    {
        $this->isOpen = true;
    }

    public function close()
    {
        $this->isOpen = false;
    }

    public function render()
    {
        return view('livewire.instruction-overlay');
    }
}
