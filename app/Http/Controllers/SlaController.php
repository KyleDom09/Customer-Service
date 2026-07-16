<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SlaRule;
use App\Models\CalendarSetting;

class SlaController extends Controller
{
    public function index()
    {
        $rules = SlaRule::all();
        $calendar = CalendarSetting::first();
        return view('SLA', compact('rules', 'calendar'));
    }

    public function storeRule(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'response'   => 'required|string|max:255',
            'resolution' => 'required|string|max:255',
            'active'     => 'boolean',
        ]);

        $validated['active'] = $validated['active'] ?? true;

        $rule = SlaRule::create($validated);

        return response()->json($rule);
    }

    public function updateRule(Request $request, $id)
    {
        $validated = $request->validate([
            'active' => 'required|boolean',
        ]);

        $rule = SlaRule::findOrFail($id);
        $rule->update(['active' => $validated['active']]);

        return response()->json(['success' => true]);
    }

    public function destroyRule($id)
    {
        SlaRule::destroy($id);

        return response()->json(['success' => true]);
    }

    public function updateCalendar(Request $request)
    {
        $validated = $request->validate([
            'date'   => 'required|integer|min:1|max:31',
            'month'  => 'required|integer|min:0|max:11',
            'year'   => 'required|integer|min:2000|max:2100',
            'hour'   => 'required|string|max:10',
            'minute' => 'required|string|max:10',
            'ampm'   => 'required|string|in:AM,PM',
        ]);

        $calendar = CalendarSetting::first() ?? new CalendarSetting();
        $calendar->fill($validated)->save();

        return response()->json(['success' => true]);
    }
}