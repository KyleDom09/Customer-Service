<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    public function index()
    {
        $allAgents = Agent::all();

        $agents = $allAgents->map(function ($agent) {
            return [
                'id' => $agent->id,
                'name' => $agent->name,
                'role' => $agent->role,
                'status' => ucfirst($agent->active_status),
                'assigned' => $agent->total_assigned,
                'resolved' => $agent->total_resolved,
                'response' => $agent->avg_response_minutes . 'm',
                'csat' => $agent->csat_score,
                'img' => $agent->avatar ?: 'https://i.pravatar.cc/150?img=' . (($agent->id % 70) + 1),
            ];
        })->toArray();

        // Summary card computations
        $totalActiveAgents = $allAgents->where('active_status', 'online')->count();

        $teamAvgEfficiency = $allAgents->count() > 0
            ? round($allAgents->avg(fn ($a) => $a->total_assigned > 0 ? ($a->total_resolved / $a->total_assigned) * 100 : 0))
            : 0;

        $topPerformer = $allAgents->sortByDesc('csat_score')->first();

        return view('agents', [
            'agents' => $agents,
            'totalActiveAgents' => $totalActiveAgents,
            'teamAvgEfficiency' => $teamAvgEfficiency,
            'topPerformer' => $topPerformer,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'active_status' => 'required|in:online,offline,away',
            'total_assigned' => 'required|integer|min:0',
            'total_resolved' => 'required|integer|min:0',
            'avg_response_minutes' => 'required|integer|min:0',
            'csat_score' => 'required|numeric|min:0|max:5',
        ]);

        $validated['team'] = 'Customer Service';

        Agent::create($validated);

        return redirect('/customer-service/agents')->with('form', 'add-agent');
    }

    public function update(Request $request, Agent $agent)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'active_status' => 'required|in:online,offline,away',
            'total_assigned' => 'required|integer|min:0',
            'total_resolved' => 'required|integer|min:0',
            'avg_response_minutes' => 'required|integer|min:0',
            'csat_score' => 'required|numeric|min:0|max:5',
        ]);

        $agent->update($validated);

        return redirect('/customer-service/agents');
    }

    public function destroy(Agent $agent)
    {
        $agent->delete();

        return redirect('/customer-service/agents');
    }
}