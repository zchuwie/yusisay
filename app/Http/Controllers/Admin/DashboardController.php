<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;
use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $startOfLastWeek = $now->copy()->subDays(7)->startOfDay();
        $startOfTwoWeeksAgo = $now->copy()->subDays(14)->startOfDay();
        
        $totalUsers = User::count();
        $totalUsersLastWeek = User::where('created_at', '<=', $startOfLastWeek)->count();
        $totalUsersChange = $this->calculateChangePercentage($totalUsers, $totalUsersLastWeek);

        $newUsersThisWeek = User::where('created_at', '>=', $startOfLastWeek)->count();
        
        $activeReports = Report::where('status', 'pending')->count();
        
        $resolvedLastWeekCount = Report::whereIn('status', ['approved', 'dismissed'])
            ->where('reviewed_at', '>=', $startOfLastWeek)
            ->count();

        $resolvedPrevWeekCount = Report::whereIn('status', ['approved', 'dismissed'])
            ->whereBetween('reviewed_at', [$startOfTwoWeeksAgo, $startOfLastWeek])
            ->count();
        
        $resolvedReportsChange = $this->calculateChangePercentage($resolvedLastWeekCount, $resolvedPrevWeekCount);
        $totalResolvedReports = Report::whereIn('status', ['approved', 'dismissed'])->count();
        
        $growthData = $this->getWeeklyGrowthData();
        
        $recentActivities = $this->getRecentActivities();

        $dashboardData = [
            'totalUsers' => $totalUsers,
            'newUsersThisWeek' => $newUsersThisWeek,
            'activeReports' => $activeReports,
            'resolvedReports' => $totalResolvedReports,
            
            'totalUsersChange' => $totalUsersChange,
            'resolvedReportsChange' => $resolvedReportsChange,

            'recentActivities' => $recentActivities,
            'growthData' => $growthData,
        ];

        return view('admin.dashboard', compact('dashboardData'));
    }

    protected function calculateChangePercentage($newValue, $baseValue)
    {
        if ($baseValue == 0) {
            return $newValue > 0 ? 100 : 0;
        }

        $change = $newValue - $baseValue;
        return round(($change / $baseValue) * 100, 1);
    }


    protected function getWeeklyGrowthData()
    {
        $days = [];
        $users = [];
        $posts = [];
        $today = Carbon::today();

        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $nextDay = $date->copy()->addDay();
            
            $days[] = $date->format('M d');

            $users[] = User::whereBetween('created_at', [$date, $nextDay])->count();
            $posts[] = Post::whereBetween('created_at', [$date, $nextDay])->count();
        }

        return [
            'labels' => $days,
            'users' => $users,
            'posts' => $posts,
        ];
    }

    protected function getRecentActivities()
    {
        $newUsers = User::orderBy('created_at', 'desc')->take(5)
            ->get(['id', 'name', 'created_at'])
            ->map(function ($user) {
                return [
                    'description' => "User **{$user->name} (ID: {$user->id})** registered.",
                    'time' => $user->created_at->diffForHumans(),
                    'timestamp' => $user->created_at,
                ];
            });

        $newReports = Report::with('user:id,name', 'post:id')
            ->orderBy('created_at', 'desc')->take(5)
            ->get(['id', 'user_id', 'post_id', 'status', 'created_at'])
            ->map(function ($report) {
                $statusText = $report->status === 'pending' ? 'submitted' : 'reviewed';
                $reporter = $report->user ? "by {$report->user->name} (ID: {$report->user_id})" : '(Deleted User)';
                
                return [
                    'description' => "Report **#{$report->id}** on Post #{$report->post_id} {$statusText} {$reporter}.",
                    'time' => $report->created_at->diffForHumans(),
                    'timestamp' => $report->created_at,
                ];
            });
            
        $activities = $newUsers->merge($newReports)
            ->sortByDesc('timestamp')
            ->take(10) 
            ->values()
            ->toArray();

        return $activities;
    }
}