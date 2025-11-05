<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display the Admin Dashboard with summary data.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        // Mock data for dashboard cards
        $dashboardData = [
            'totalUsers' => 1258,
            'pendingModeration' => 12,
            'newReportsToday' => 3,
            'activeUsersNow' => 58,
            'recentActivities' => [
                ['time' => '1m ago', 'description' => 'User John Doe updated profile.'],
                ['time' => '5m ago', 'description' => 'New content created by user Jane Smith.'],
                ['time' => '15m ago', 'description' => 'System integration check successful.'],
                ['time' => '1h ago', 'description' => 'Report #987 has been resolved.'],
            ],
            'quickReports' => [
                ['title' => 'User Growth', 'value' => '+18%', 'trend' => 'up', 'color' => 'green'],
                ['title' => 'Content Submissions', 'value' => '456', 'trend' => 'down', 'color' => 'red'],
                ['title' => 'Average Session', 'value' => '4:32 min', 'trend' => 'up', 'color' => 'green'],
            ]
        ];

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
}