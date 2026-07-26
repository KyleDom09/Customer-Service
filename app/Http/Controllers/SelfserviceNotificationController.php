<?php

namespace App\Http\Controllers;

use App\Models\SelfserviceNotification;
use Illuminate\Http\Request;

class SelfserviceNotificationController extends Controller
{
    public function index()
    {
        return SelfserviceNotification::latest()->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:255',
        ]);

        $notification = SelfserviceNotification::create([
            'message' => $validated['message'],
            'is_read' => false,
        ]);

        return response()->json($notification, 201);
    }

    public function markAllRead()
    {
        SelfserviceNotification::query()->delete();

        return response()->json(['message' => 'All notifications cleared.'], 200);
    }
}
