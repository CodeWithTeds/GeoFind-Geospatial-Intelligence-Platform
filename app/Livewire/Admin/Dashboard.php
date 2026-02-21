<?php

namespace App\Livewire\Admin;

use App\Services\QuestionService;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Analytics\Facades\Analytics;
use Spatie\Analytics\Period;

#[Lazy]
#[Layout('layouts.admin')]
class Dashboard extends Component
{
    public $analyticsError = null;

    public function render(QuestionService $service)
    {
        $stats = $service->getDashboardStats();
        
        $analyticsData = null;
        try {
            // Fetch visitors and page views for the last 7 days
            $analyticsData = Analytics::fetchVisitorsAndPageViews(Period::days(7));
        } catch (\Exception $e) {
            // Log the error for debugging
            \Illuminate\Support\Facades\Log::error('Analytics error: ' . $e->getMessage());
            $this->analyticsError = $e->getMessage();
        }

        return view('livewire.admin.dashboard', [
            'stats' => $stats,
            'analyticsData' => $analyticsData
        ]);
    }

    public function placeholder()
    {
        return view('livewire.admin.dashboard-placeholder');
    }
}
