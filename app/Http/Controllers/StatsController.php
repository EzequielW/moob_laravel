<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StatsController extends Controller
{
    public function messagesPerPlatform(Request $request)
    {
        $userId = $request->user()->id;
        $stats = Message::selectRaw('platform, count(*) as total')
            ->where('user_id', $userId)
            ->groupBy('platform');

        if($request->wantsJson()) {
            return response()->json([
                'labels' => $stats->pluck('platform'),
                'data' => $stats->pluck('total')
            ]);
        }

        return Inertia::render('Dashboard', [
            'stats' => [
                'labels' => $stats->pluck('platform'),
                'data' => $stats->pluck('total')
            ],
        ]);
    }
}
