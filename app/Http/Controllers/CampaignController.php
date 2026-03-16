<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCampaignRequest;
use App\Models\Campaign;
use App\Services\CampaignService;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $campaigns = Campaign::with('contactList')
            ->withCount([
                'sends as pending_count' => fn($q) => $q->where('status', 'pending'),
                'sends as sent_count' => fn($q) => $q->where('status', 'sent'),
                'sends as failed_count' => fn($q) => $q->where('status', 'failed'),
            ])->paginate(10);

        return response()->json($campaigns);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCampaignRequest $request)
    {
        $campaign = Campaign::create($request->validated());

        return response()->json($campaign, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $campaign = Campaign::with('contactList')
            ->withCount([
                'sends as pending_count' => fn($q) => $q->where('status', 'pending'),
                'sends as sent_count' => fn($q) => $q->where('status', 'sent'),
                'sends as failed_count' => fn($q) => $q->where('status', 'failed'),
            ])->findOrFail($id);

        return response()->json($campaign);
    }

    public function dispatch($id)
    {
        $campaign = Campaign::findOrFail($id);
        
        $service = new CampaignService();
        $service->dispatch($campaign);

        return response()->json([
            'message' => 'Campaign dispatch started'
        ], 200);
    }
}
