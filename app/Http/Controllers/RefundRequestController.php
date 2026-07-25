<?php

namespace App\Http\Controllers;

use App\Models\RefundRequest;
use Illuminate\Http\Request;

class RefundRequestController extends Controller
{
    public function index()
    {
        return RefundRequest::latest()->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:5120', // max 5MB
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('refund-requests', 'public');
        }

        $refundRequest = RefundRequest::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'image_path' => $imagePath,
            'status' => 'pending',
        ]);

        return response()->json($refundRequest, 201);
    }

    // User-facing cancel — only allowed while still pending.
    public function destroy(RefundRequest $refundRequest)
    {
        if ($refundRequest->status !== 'pending') {
            return response()->json(['message' => 'Only pending requests can be cancelled.'], 403);
        }

        $refundRequest->delete();

        return response()->json(['message' => 'Refund request deleted.'], 200);
    }

    // Admin: list all refund requests (used by the admin page's own fetch,
    // functionally the same as index() but kept separate in case admin-only
    // fields/logic get added later).
    public function adminIndex()
    {
        return RefundRequest::latest()->get();
    }

    public function approve(RefundRequest $refundRequest)
    {
        $refundRequest->update(['status' => 'approved']);

        return response()->json($refundRequest, 200);
    }

    public function reject(RefundRequest $refundRequest)
    {
        $refundRequest->update(['status' => 'rejected']);

        return response()->json($refundRequest, 200);
    }

    // Admin: can delete a request regardless of its status.
    public function adminDestroy(RefundRequest $refundRequest)
    {
        $refundRequest->delete();

        return response()->json(['message' => 'Refund request deleted.'], 200);
    }
}