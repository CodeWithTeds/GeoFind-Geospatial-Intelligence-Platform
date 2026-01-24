<?php

namespace App\Livewire\Admin;

use App\Services\QuestionService;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.admin')]
class Dashboard extends Component
{
    public function render(QuestionService $service)
    {
        $stats = $service->getDashboardStats();
        
        return view('livewire.admin.dashboard', [
            'stats' => $stats
        ]);
    }

    public function placeholder()
    {
        return view('livewire.admin.dashboard-placeholder');
    }
}
