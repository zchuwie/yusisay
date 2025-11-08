<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Report;
use App\Models\Post;
use App\Models\CensoredWord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Display the Admin Dashboard with summary data.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $dashboardData = $this->getDashboardData();
        return view('admin.dashboard', compact('dashboardData'));
    }

    /**
     * Display the User Management page.
     *
     * @return \Illuminate\View\View
     */
    public function user()
    {
        return view('admin.user');
    }

    /**
     * Display the Reporting & Analytics page.
     *
     * @return \Illuminate\View\View
     */
    public function report()
    {
        return view('admin.report');
    }

    /**
     * Display the Censored Words page.
     *
     * @return \Illuminate\View\View
     */
    public function censoredWords()
    {
        return view('admin.censored-words');
    }

    /**
     * API endpoint to get growth data for different time periods.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGrowthData(Request $request)
    {
        try {
            $period = $request->input('period', 'week');
            $now = Carbon::now();
            
            switch ($period) {
                case 'month':
                    $startDate = $now->copy()->subDays(30);
                    $data = $this->getMonthlyGrowthData($startDate, $now);
                    break;
                case 'year':
                    $startDate = $now->copy()->subMonths(12);
                    $data = $this->getYearlyGrowthData($startDate, $now);
                    break;
                default: // week
                    $startDate = $now->copy()->subDays(7);
                    $data = $this->getWeeklyGrowthData($startDate, $now);
                    break;
            }
            
            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Growth data error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch growth data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get real-time dashboard data from database.
     *
     * @return array
     */
    private function getDashboardData()
    {
        $now = Carbon::now();
        $weekAgo = $now->copy()->subDays(7);
        $twoWeeksAgo = $now->copy()->subDays(14);

        // Total Users
        $totalUsers = User::count();
        $totalUsersLastWeek = User::where('created_at', '<', $weekAgo)->count();
        $totalUsersChange = $totalUsersLastWeek > 0 
            ? round((($totalUsers - $totalUsersLastWeek) / $totalUsersLastWeek) * 100, 1) 
            : ($totalUsers > 0 ? 100 : 0);

        // New Users This Week
        $newUsersThisWeek = User::where('created_at', '>=', $weekAgo)->count();
        $newUsersPreviousWeek = User::whereBetween('created_at', [$twoWeeksAgo, $weekAgo])->count();
        $newUsersChange = $newUsersPreviousWeek > 0 
            ? round((($newUsersThisWeek - $newUsersPreviousWeek) / $newUsersPreviousWeek) * 100, 1) 
            : ($newUsersThisWeek > 0 ? 100 : 0);

        // Active Reports (Pending)
        $activeReports = Report::where('status', 'pending')->count();
        $activeReportsLastWeek = Report::where('status', 'pending')
            ->where('created_at', '<', $weekAgo)
            ->count();
        $activeReportsChange = $activeReportsLastWeek > 0 
            ? round((($activeReports - $activeReportsLastWeek) / $activeReportsLastWeek) * 100, 1) 
            : ($activeReports > 0 ? 100 : 0);

        // Resolved Reports (Not Pending)
        $resolvedReports = Report::where('status', '!=', 'pending')->count();
        $resolvedReportsLastWeek = Report::where('status', '!=', 'pending')
            ->where('updated_at', '<', $weekAgo)
            ->count();
        $resolvedReportsChange = $resolvedReportsLastWeek > 0 
            ? round((($resolvedReports - $resolvedReportsLastWeek) / $resolvedReportsLastWeek) * 100, 1) 
            : ($resolvedReports > 0 ? 100 : 0);

        // Growth Data for Chart (Last 7 Days)
        $growthData = $this->getWeeklyGrowthData($weekAgo, $now);

        // Recent Censored Words (Last 5)
        $recentCensoredWords = CensoredWord::orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($word) {
                return [
                    'description' => 'New censored word added: <strong>' . e($word->word) . '</strong>',
                    'time' => $word->created_at->diffForHumans(),
                ];
            });

        return [
            'totalUsers' => $totalUsers,
            'totalUsersChange' => $totalUsersChange,
            'newUsersThisWeek' => $newUsersThisWeek,
            'newUsersChange' => $newUsersChange,
            'activeReports' => $activeReports,
            'activeReportsChange' => $activeReportsChange,
            'resolvedReports' => $resolvedReports,
            'resolvedReportsChange' => $resolvedReportsChange,
            'growthData' => $growthData,
            'recentActivities' => $recentCensoredWords,
        ];
    }

    /**
     * Get weekly growth data for chart (users and posts per day).
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    private function getWeeklyGrowthData($startDate, $endDate)
    {
        $labels = [];
        $users = [];
        $posts = [];

        // Generate data for each day in the past week
        for ($i = 6; $i >= 0; $i--) {
            $date = $endDate->copy()->subDays($i);
            $labels[] = $date->format('M d');

            // Count users created on this day
            $userCount = User::whereDate('created_at', $date->toDateString())->count();
            $users[] = $userCount;

            // Count posts created on this day
            $postCount = Post::whereDate('created_at', $date->toDateString())->count();
            $posts[] = $postCount;
        }

        return [
            'labels' => $labels,
            'users' => $users,
            'posts' => $posts,
        ];
    }

    /**
     * Get monthly growth data for chart (users and posts per day for last 30 days).
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    private function getMonthlyGrowthData($startDate, $endDate)
    {
        $labels = [];
        $users = [];
        $posts = [];

        // Generate data for every 3 days in the past month (to keep chart readable)
        for ($i = 30; $i >= 0; $i -= 3) {
            $date = $endDate->copy()->subDays($i);
            $labels[] = $date->format('M d');

            // Count users created on this day
            $userCount = User::whereDate('created_at', $date->toDateString())->count();
            $users[] = $userCount;

            // Count posts created on this day
            $postCount = Post::whereDate('created_at', $date->toDateString())->count();
            $posts[] = $postCount;
        }

        return [
            'labels' => $labels,
            'users' => $users,
            'posts' => $posts,
        ];
    }

    /**
     * Get yearly growth data for chart (users and posts per month for last 12 months).
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    private function getYearlyGrowthData($startDate, $endDate)
    {
        $labels = [];
        $users = [];
        $posts = [];

        // Generate data for each month in the past year
        for ($i = 11; $i >= 0; $i--) {
            $date = $endDate->copy()->subMonths($i);
            $labels[] = $date->format('M Y');

            // Count users created in this month
            $userCount = User::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $users[] = $userCount;

            // Count posts created in this month
            $postCount = Post::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $posts[] = $postCount;
        }

        return [
            'labels' => $labels,
            'users' => $users,
            'posts' => $posts,
        ];
    }
}