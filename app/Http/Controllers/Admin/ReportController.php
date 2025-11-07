<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CensoredWord;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth; 
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Aggregates reports by post_id and returns the paginated data.
     */
    public function index(Request $request): JsonResponse
    {
        // Check for required tables early
        if (!Schema::hasTable('reports') || !Schema::hasTable('posts')) {
            return response()->json([
                'error' => 'Database Error: Missing "reports" or "posts" table.',
                'details' => 'Please ensure both tables exist and are properly migrated.'
            ], 500);
        }

        try {
            // 1. Fetch the list of banned words from the database
            $bannedWords = CensoredWord::pluck('word')->toArray();

            $perPage = $request->get('per_page', 10);
            $search = $request->get('search');
            $sortBy = $request->get('sort_by', 'total_reports');
            $sortDir = $request->get('sort_dir', 'desc');

            // Subquery to count pending reports per post
            $reportsCount = DB::table('reports')
                ->select('post_id', DB::raw('count(*) as total_reports'))
                ->where('status', 'pending')
                ->groupBy('post_id');

            // Main query to join posts with report counts
            $query = DB::table('posts')
                ->joinSub($reportsCount, 'report_counts', function ($join) {
                    $join->on('posts.id', '=', 'report_counts.post_id');
                })
                ->select(
                    'posts.id as post_id',
                    'posts.user_id', 
                    'posts.content', 
                    'report_counts.total_reports' 
                );

            if ($search) {
                // Search only applies to the 'content' column
                $query->where('posts.content', 'like', '%' . $search . '%');
            }

            // Apply sorting
            $allowedSortColumns = ['total_reports', 'posts.content', 'post_id'];
            $column = in_array($sortBy, $allowedSortColumns) ? $sortBy : 'total_reports';
            $direction = strtolower($sortDir) === 'desc' ? 'desc' : 'asc';

            if ($column === 'post_id') {
                $query->orderBy('posts.id', $direction);
            } elseif ($column === 'posts.content') { 
                $query->orderBy('posts.content', $direction);
            } else {
                $query->orderBy('total_reports', $direction);
            }
            
            $paginator = $query->paginate($perPage);

            // 2. Apply Censoring and formatting
            $censoredReports = $paginator->getCollection()->map(function ($report) use ($bannedWords) {
                
                if (isset($report->content)) {
                    $report->censored_content = $this->censorWords($report->content, $bannedWords);
                } else {
                    $report->censored_content = 'N/A';
                }
                
                // Clean up output
                $reportArray = (array) $report;
                unset($reportArray['content']);
                return (object) $reportArray; 
            });
            
            $paginator->setCollection($censoredReports);

            return response()->json($paginator);

        } catch (\Exception $e) {
            Log::error("Reports API failed with SQL error: " . $e->getMessage());
            
            return response()->json([
                'error' => 'Server Error: The database query failed.',
                'details' => 'A SQL error occurred. Please verify all tables (posts, reports, censored_words) exist and have the required columns.',
                'sql_error_detail' => $e->getMessage() 
            ], 500);
        }
    }

    /**
     * Censors a string by replacing banned words with asterisks (****).
     */
    private function censorWords(string $text, array $bannedWords): string
    {
        if (empty($bannedWords)) {
            return $text;
        }
        
        $replacement = '****';
        return str_ireplace($bannedWords, $replacement, $text);
    }

    /**
     * Resolves (dismisses) all pending reports for a specific post.
     * This function uses $postId and does NOT reference an undefined $post variable.
     */
    public function resolve(Request $request, $postId): JsonResponse
    {
        // NOTE: Authentication is handled by the 'auth' middleware on the route group.
        try {
            $user = Auth::user(); 
            $reviewerId = $user ? $user->id : null;
            
            if (!$reviewerId) {
                // Should only happen if middleware fails, but good defensive programming
                return response()->json(['error' => 'Authentication required to perform this action.'], 401);
            }

            if (!is_numeric($postId)) {
                return response()->json(['error' => 'Invalid Post ID provided.'], 400);
            }
            
            $updated = DB::table('reports')
                ->where('post_id', (int)$postId)
                ->where('status', 'pending')
                ->update([
                    'status' => 'dismissed', 
                    'reviewed_at' => now(),
                    'reviewed_by' => $reviewerId, 
                ]);

            if ($updated > 0) {
                // This line correctly uses $updated and $postId
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