<?php

namespace App\Http\Controllers;

use App\Enums\CampaignStatus;
use App\Http\Requests\ConfigureCampaignRequest;
use App\Http\Requests\StoreCampaignRequest;
use App\Http\Requests\UpdateCampaignRequest;
use App\Models\LoyaltyProgram;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $merchant = $request->user()->merchant;
        $filter   = $request->input('filter', 'active');

        if (! in_array($filter, ['draft', 'active', 'paused', 'archived', 'all'])) {
            $filter = 'active';
        }

        if (! $merchant) {
            $query = LoyaltyProgram::whereNull('id');
        } elseif ($filter === 'archived') {
            $query = LoyaltyProgram::onlyTrashed()->where('merchant_id', $merchant->id);
        } elseif ($filter === 'all') {
            $query = LoyaltyProgram::withTrashed()->where('merchant_id', $merchant->id);
        } else {
            $query = LoyaltyProgram::where('merchant_id', $merchant->id)
                                   ->where('status', $filter);
        }

        if ($search = $request->input('search_name')) {
            $query->where('name', 'like', '%'.$search.'%');
        }

        $campaigns = $query->orderBy('updated_at', 'desc')->paginate(10)->withQueryString();

        return view('campaigns.index', compact('campaigns', 'filter'));
    }

    public function create()
    {
        return view('campaigns.create');
    }

    public function store(StoreCampaignRequest $request)
    {
        $request->user()->merchant->loyaltyPrograms()->create($request->validated());

        return redirect()->route('campaigns.index')->with('success', 'Campaign created successfully.');
    }

    public function show(Request $request, LoyaltyProgram $campaign)
    {
        abort_unless($campaign->merchant_id === $request->user()->merchant?->id, 403);
        $campaign->loadMissing('merchant');

        return view('campaigns.show', compact('campaign'));
    }

    public function configure(ConfigureCampaignRequest $request, LoyaltyProgram $campaign)
    {
        abort_unless($campaign->merchant_id === $request->user()->merchant?->id, 403);
        abort_if($campaign->trashed(), 403);

        $campaign->update(['settings' => $request->validated()]);

        return redirect()->route('campaigns.show', $campaign)
                         ->with('success', 'Campaign configuration saved.');
    }

    public function update(UpdateCampaignRequest $request, LoyaltyProgram $campaign)
    {
        abort_unless($campaign->merchant_id === $request->user()->merchant?->id, 403);
        abort_if($campaign->trashed(), 403);

        $campaign->update($request->validated());

        return redirect()->route('campaigns.show', $campaign)->with('success', 'Campaign updated successfully.');
    }

    public function pause(Request $request, LoyaltyProgram $campaign)
    {
        abort_unless($campaign->merchant_id === $request->user()->merchant?->id, 403);
        abort_if($campaign->trashed(), 403);

        $campaign->update(['status' => CampaignStatus::Paused]);

        return redirect()->route('campaigns.show', $campaign)->with('success', 'Campaign paused.');
    }

    public function archive(Request $request, LoyaltyProgram $campaign)
    {
        abort_unless($campaign->merchant_id === $request->user()->merchant?->id, 403);
        abort_if($campaign->trashed(), 409);

        $campaign->delete();

        return redirect()->route('campaigns.index')->with('success', 'Campaign archived.');
    }
}
