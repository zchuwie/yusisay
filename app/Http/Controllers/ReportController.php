<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'reason' => 'nullable|string|max:500'
        ]);

        $existingReport = Report::where('post_id', $request->post_id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingReport) { 
            $existingReport->update([
                'reason' => $request->reason,
                'updated_at' => now()
            ]);

            return back()->with('success', 'Report updated successfully!');
        }
 
        Report::create([
            'post_id' => $request->post_id,
            'user_id' => Auth::id(),
            'reason' => $request->reason,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Report submitted successfully!');
    }
}