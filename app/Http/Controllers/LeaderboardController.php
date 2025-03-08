<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'all');
        $query = Activity::select('activities.user_id', 'users.name', DB::raw('SUM(activities.points) as total_points'))
            ->join('users', 'users.id', '=', 'activities.user_id')
            ->groupBy('activities.user_id', 'users.name')
            ->orderBy('total_points', 'desc');
    
        if ($filter == 'day') {
            $query->whereDate('performed_at', Carbon::today());
        } elseif ($filter == 'month') {
            $query->whereMonth('performed_at', Carbon::now()->month);
        } elseif ($filter == 'year') {
            $query->whereYear('performed_at', Carbon::now()->year);
        }
    
        if ($request->user_name) {
            $query->where('users.name', 'LIKE', '%' . $request->user_name . '%');
        }
    
        // Paginate 10 records per page
        $leaderboard = $query->paginate(10);
    
        // Add ranking calculation
        $rank = 1;
        $last_points = null;
        foreach ($leaderboard as $key => $entry) {
            if ($last_points !== null && $last_points != $entry->total_points) {
                $rank = $key + 1;
            }
            $entry->rank = $rank;
            $last_points = $entry->total_points;
        }
    
        return response()->json($leaderboard);
    }
    
}

