<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $perPage = intval($request->get('per_page', 10));
        $page = intval($request->get('page', 1));
        $search = $request->get('search', null);
        $sortBy = $request->get('sort_by', 'total_reports');
        $sortDir = $request->get('sort_dir', 'desc');
        $showResolved = filter_var($request->query('resolved', false), FILTER_VALIDATE_BOOLEAN);

        // Handle resolved reports
        if ($showResolved) {
            // Group by post_id and get the latest report for each post
            $query = Report::select(
                    'post_id',
                    DB::raw('MAX(id) as latest_report_id'),
                    DB::raw('COUNT(*) as total_reports'),
                    DB::raw('MAX(reviewed_at) as reviewed_at'),
                    DB::raw('MAX(status) as status')
                )
                ->whereIn('status', [Report::STATUS_APPROVED, Report::STATUS_DISMISSED])
                ->groupBy('post_id');

            // Add search filter
            if ($search) {
                $query->join('posts', 'posts.id', '=', 'reports.post_id')
                     ->where(function($q) use ($search) {
                         $q->where('posts.title', 'like', '%' . $search . '%')
                           ->orWhere('posts.content', 'like', '%' . $search . '%');
                     });
            }

            // Add sorting
            if ($sortBy === 'total_reports') {
                $query->orderBy('total_reports', $sortDir);
            } elseif ($sortBy === 'title') {
                if (!$search) {
                    $query->join('posts', 'posts.id', '=', 'reports.post_id');
                }
                $query->orderBy('posts.title', $sortDir);
            } elseif ($sortBy === 'post_id') {
                $query->orderBy('post_id', $sortDir);
            } else {
                $query->orderBy('reviewed_at', 'desc');
            }

            // Paginate
            $paginator = $query->paginate($perPage, ['*'], 'page', $page);

            // Transform the results
            $items = $paginator->getCollection()->map(function($row) {
                $post = Post::find($row->post_id);
                if (!$post) return null;

                $title = $post->title ?? Str::limit(strip_tags($post->content), 60);
                $censoredPreview = $post->censored_content ?? Str::limit(strip_tags($post->content), 100);

                $author = $post->is_anonymous 
                    ? 'Anonymous' 
                    : optional(User::find($post->user_id))->name ?? 'User';

                return [
                    'post_id' => $post->id,
                    'title' => $title,
                    'censored_content' => $censoredPreview,
                    'post_content' => $post->content,
                    'is_hidden' => (bool) $post->is_hidden,
                    'post_author' => $author,
                    'status' => $row->status,
                    'reviewed_at' => $row->reviewed_at,
                    'total_reports' => (int) $row->total_reports,
                ];
            })->filter()->values();

            $paginator->setCollection($items);

            return response()->json($paginator);
        }

        // Handle pending reports - GROUP BY post_id
        $base = Report::query()
            ->select(
                'post_id', 
                DB::raw('COUNT(*) as total_reports'), 
                DB::raw('MAX(created_at) as latest_report_at'), 
                DB::raw('MIN(created_at) as oldest_report_at')
            )
            ->where('status', Report::STATUS_PENDING)
            ->groupBy('post_id');

        // Add search filter
        if ($search) {
            $base->join('posts', 'posts.id', '=', 'reports.post_id')
                 ->where(function($q) use ($search) {
                     $q->where('posts.title', 'like', '%' . $search . '%')
                       ->orWhere('posts.content', 'like', '%' . $search . '%');
                 });
        }

        // Add sorting
        if (in_array($sortBy, ['total_reports', 'latest_report_at', 'oldest_report_at'])) {
            $base->orderBy($sortBy, $sortDir);
        } elseif ($sortBy === 'post_id') {
            $base->orderBy('post_id', $sortDir);
        } elseif ($sortBy === 'title') {
            if (!$search) {
                $base->join('posts', 'posts.id', '=', 'reports.post_id');
            }
            $base->orderBy('posts.title', $sortDir);
        } else {
            $base->orderBy('total_reports', 'desc');
        }

        // Paginate
        $paginator = $base->paginate($perPage, ['*'], 'page', $page);

        // Transform the results
        $items = $paginator->getCollection()->map(function($row) {
            $post = Post::find($row->post_id);
            if (!$post) return null;

            $title = $post->title ?? Str::limit(strip_tags($post->content), 60);
            $censoredPreview = $post->censored_content ?? Str::limit(strip_tags($post->content), 100);

            if ($post->is_anonymous) {
                $author = 'Anonymous';
            } else {
                $user = User::find($post->user_id);
                $author = $user ? ($user->name ?? $user->username ?? 'User') : 'User';
            }

            // Get all reports for this post
            $reportModels = Report::where('post_id', $row->post_id)
                ->where('status', Report::STATUS_PENDING)
                ->orderBy('created_at', 'desc')
                ->with('user')
                ->get();

            $reporters = $reportModels->map(function($r) {
                $u = $r->user;
                return [
                    'id' => $r->user_id,
                    'username' => $u ? ($u->name ?? $u->username ?? 'User') : 'User',
                    'created_at' => $r->created_at->toDateTimeString(),
                    'reason' => $r->reason ?? 'No reason provided',
                ];
            });

            return [
                'post_id' => $row->post_id,
                'title' => $title,
                'censored_content' => $censoredPreview,
                'post_content' => $post->content,
                'is_anonymous' => (bool) $post->is_anonymous,
                'is_hidden' => (bool) ($post->is_hidden ?? 0),
                'post_author' => $author,
                'total_reports' => (int) $row->total_reports,
                'latest_report_at' => $row->latest_report_at,
                'oldest_report_at' => $row->oldest_report_at,
                'reporters' => $reporters,
                'status' => 'pending',
            ];
        })->filter()->values();

        $paginator->setCollection($items);

        return response()->json($paginator);
    }

    public function approve($postId, Request $request)
    {
        $post = Post::find($postId);
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $reports = Report::where('post_id', $postId)
            ->where('status', Report::STATUS_PENDING)
            ->get();

        if ($reports->isEmpty()) {
            return response()->json(['message' => 'No pending reports for this post'], 400);
        }

        // Update the reports
        Report::where('post_id', $postId)
            ->where('status', Report::STATUS_PENDING)
            ->update([
                'status' => Report::STATUS_APPROVED,
                'reviewed_at' => Carbon::now(),
                'reviewed_by' => Auth::id(),
                'updated_at' => Carbon::now(),
            ]);

        // Hide the post
        $post->is_hidden = true;
        $post->save();

        return response()->json(['message' => 'Reports approved and post hidden.'], 200);
    }

    public function decline($postId, Request $request)
    {
        $post = Post::find($postId);
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $reports = Report::where('post_id', $postId)
            ->where('status', Report::STATUS_PENDING)
            ->get();

        if ($reports->isEmpty()) {
            return response()->json(['message' => 'No pending reports for this post'], 400);
        }

        // Update the reports
        Report::where('post_id', $postId)
            ->where('status', Report::STATUS_PENDING)
            ->update([
                'status' => Report::STATUS_DISMISSED,
                'reviewed_at' => Carbon::now(),
                'reviewed_by' => Auth::id(),
                'updated_at' => Carbon::now(),
            ]);

        // Ensure post is visible
        $post->is_hidden = false;
        $post->save();

        return response()->json(['message' => 'Reports dismissed and post visible.'], 200);
    }
}