<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth; 

class ReportController extends Controller
{
    /**
     * Aggregates reports by post_id and returns the paginated data.
     * The API endpoint is /admin/api/reports
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if (!Schema::hasTable('reports') || !Schema::hasTable('posts')) {
            return response()->json([
                'error' => 'Database Error: Missing "reports" or "posts" table.',
                'details' => 'Please ensure both tables exist and are properly migrated.'
            ], 500);
        }

        try {
            $perPage = $request->get('per_page', 10);
            $search = $request->get('search');
            $sortBy = $request->get('sort_by', 'total_reports');
            $sortDir = $request->get('sort_dir', 'desc');

            $reportsCount = DB::table('reports')
                ->select('post_id', DB::raw('count(*) as total_reports'))
                ->where('status', 'pending')
                ->groupBy('post_id');

            $query = DB::table('posts')
                ->joinSub($reportsCount, 'report_counts', function ($join) {
                    $join->on('posts.id', '=', 'report_counts.post_id');
                })
                ->select(
                    'posts.id as post_id',
                    'posts.title', 
                    'posts.user_id', 
                    'report_counts.total_reports' 
                );

            if ($search) {
                $query->where('posts.title', 'like', '%' . $search . '%');
            }

            $allowedSortColumns = ['total_reports', 'posts.title', 'post_id'];
            $column = in_array($sortBy, $allowedSortColumns) ? $sortBy : 'total_reports';
            $direction = strtolower($sortDir) === 'desc' ? 'desc' : 'asc';

            if ($column === 'post_id') {
                $query->orderBy('posts.id', $direction);
            } elseif ($column === 'posts.title') {
                $query->orderBy('posts.title', $direction);
            } else {
                $query->orderBy('total_reports', $direction);
            }
            
            $paginator = $query->paginate($perPage);

            return response()->json($paginator);

        } catch (\Exception $e) {
            Log::error("Reports API failed with SQL error: " . $e->getMessage());
            
            return response()->json([
                'error' => 'Server Error: The database query failed.',
                'details' => 'A SQL error occurred. Check your database migrations for the "posts" table (id, title, user_id) and ensure data is present.',
                'sql_error_detail' => $e->getMessage() 
            ], 500);
        }
    }

    public function resolve(Request $request, $postId)
    {
        try {
            $user = Auth::user(); 
            $reviewerId = $user ? $user->id : null;
            
            if (!$reviewerId) {
                return response()->json(['error' => 'Authentication required to perform this action.'], 401);
            }
            
            $updated = DB::table('reports')
                        ->where('post_id', $postId)
                        ->where('status', 'pending')
                        ->update([
                            'status' => 'dismissed', 
                            'reviewed_at' => now(),
                            'reviewed_by' => $reviewerId, 
                        ]);

            if ($updated > 0) {
                return response()->json(['message' => "Successfully dismissed $updated pending report(s) for Post ID $postId."]);
            }

            return response()->json(['message' => "No pending reports found for Post ID $postId to resolve."], 200);

        } catch (\Exception $e) {
            Log::error("Failed to resolve reports for Post ID $postId: " . $e->getMessage());
            return response()->json([
                'error' => 'Failed to resolve reports.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}