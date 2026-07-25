<?php

namespace App\Http\Controllers;

use App\Models\BillingItem;
use Illuminate\Http\Request;

class BillingItemController extends Controller
{
    public function index()
    {
        return BillingItem::orderBy('created_at', 'desc')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'problem' => 'nullable|string',
            'steps' => 'nullable|array',
        ]);

        $item = BillingItem::create([
            'title' => $validated['title'],
            'icon' => 'fa-calendar-check',
            'problem' => $validated['problem'] ?? '',
            'steps' => $validated['steps'] ?? [],
            'is_new' => true,
        ]);

        return response()->json($item);
    }

    public function rate(Request $request, BillingItem $billingItem)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $billingItem->update(['rating' => $validated['rating']]);

        return response()->json($billingItem);
    }

    public function update(Request $request, BillingItem $billingItem)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'problem' => 'nullable|string',
        ]);

        $billingItem->update($validated);

        return response()->json($billingItem);
    }

    public function destroy(BillingItem $billingItem)
    {
        $billingItem->delete();

        return response()->json(['message' => 'Billing item deleted.'], 200);
    }
}