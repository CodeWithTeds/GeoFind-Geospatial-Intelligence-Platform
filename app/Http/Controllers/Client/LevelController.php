<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\Game\LevelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LevelController extends Controller
{
    

    public function __construct(protected LevelService $levelService)
    {
    }

    public function index(): View
    {
        $levels = $this->levelService->getLevelsForUser();
        $progress = $this->levelService->getUserProgress();

        return view('client.levels', compact('levels', 'progress'));
    }

    public function play(Request $request)
    {
        $level = $request->query('level');
        
        if ($level) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            
            // Prevent accessing future levels
            if ($level > $user->completed_levels + 1) {
                return redirect()->route('levels');
            }

            // Prevent replaying completed levels
            if ($level <= $user->completed_levels) {
                return redirect()->route('levels');
            }
        }

        return view('play');
    }
}
