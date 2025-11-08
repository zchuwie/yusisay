<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Fetch and filter users with info for the Alpine table.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');
        $sortBy = $request->get('sort_by', 'user_infos.created_at');
        $sortDir = $request->get('sort_dir', 'desc');

        // Safe columns to prevent SQL injection
        $validColumns = [
            'users.id',
            'users.name',
            'users.email',
            'user_infos.created_at',
        ];

        $column = in_array($sortBy, $validColumns) ? $sortBy : 'user_infos.created_at';
        $direction = in_array(strtolower($sortDir), ['asc', 'desc']) ? $sortDir : 'desc';

        // Query users joined with user_infos
        $users = User::select(
                'users.id',
                'users.name',
                'users.email',
                DB::raw('COALESCE(user_infos.profile_picture, "") as profile_picture'),
                DB::raw('COALESCE(user_infos.created_at, users.created_at) as joined_at')
            )
            ->leftJoin('user_infos', 'users.id', '=', 'user_infos.user_id');

        // Apply search filter
        if ($search) {
            $users->where(function ($query) use ($search) {
                $query->where('users.name', 'like', "%{$search}%")
                      ->orWhere('users.email', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $users->orderBy($column, $direction);

        // Paginate and return
        $paginated = $users->paginate($perPage)->withQueryString();

        return response()->json($paginated);
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            ]);

            $user->update($validated);

            return response()->json([
                'message' => "User **{$user->name}** updated successfully.",
                'user' => $user
            ]);

        } catch (ValidationException $e) {
            throw $e;
        }
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        $name = $user->name;
        $user->delete();

        return response()->json(['message' => "User **{$name}** deleted successfully."]);
    }
}
