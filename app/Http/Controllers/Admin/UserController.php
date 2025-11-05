<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $search = $request->get('search');
            $sortBy = $request->get('sort_by', 'created_at');
            $sortDir = $request->get('sort_dir', 'desc');
            
            $query = User::query();

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%');
                });
            }

            $allowedSortColumns = ['id', 'name', 'email', 'created_at'];
            $column = in_array($sortBy, $allowedSortColumns) ? $sortBy : 'created_at';
            $direction = strtolower($sortDir) === 'desc' ? 'desc' : 'asc';

            $query->orderBy($column, $direction);

            $users = $query->paginate($perPage);

            return response()->json($users);
        } catch (\Exception $e) {
            Log::error("Admin User API failed: " . $e->getMessage());
            return response()->json([
                'error' => 'Server Error: Could not fetch users.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
