<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Donation\StoreDonationCampaignRequest;
use App\Http\Requests\Donation\UpdateDonationCampaignRequest;
use App\Models\DonationCampaign;
use App\Services\DonationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class DonationCampaignController extends Controller
{
    public function __construct(private readonly DonationService $donations)
    {
    }

    public function index(): View
    {
        $campaigns = DonationCampaign::query()
            ->withSum('donations', 'amount')
            ->withCount('donations')
            ->latest()
            ->paginate(15);

        return view('admin.donations.campaigns.index', ['campaigns' => $campaigns]);
    }

    public function create(): View
    {
        $this->authorize('create', DonationCampaign::class);

        return view('admin.donations.campaigns.create');
    }

    public function store(StoreDonationCampaignRequest $request): RedirectResponse
    {
        $this->donations->createCampaign($request->validated(), $request->user());

        return redirect()->route('admin.donation-campaigns.index')->with('success', 'Campaign created.');
    }

    public function edit(DonationCampaign $donationCampaign): View
    {
        $this->authorize('update', $donationCampaign);

        return view('admin.donations.campaigns.edit', ['campaign' => $donationCampaign]);
    }

    public function update(UpdateDonationCampaignRequest $request, DonationCampaign $donationCampaign): RedirectResponse
    {
        $this->donations->updateCampaign($donationCampaign, $request->validated());

        return redirect()->route('admin.donation-campaigns.index')->with('success', 'Campaign updated.');
    }

    public function close(DonationCampaign $donationCampaign): RedirectResponse
    {
        $this->authorize('update', $donationCampaign);

        $this->donations->closeCampaign($donationCampaign);

        return back()->with('success', 'Campaign closed.');
    }

    public function reopen(DonationCampaign $donationCampaign): RedirectResponse
    {
        $this->authorize('update', $donationCampaign);

        $this->donations->reopenCampaign($donationCampaign);

        return back()->with('success', 'Campaign reopened.');
    }

    public function destroy(DonationCampaign $donationCampaign): RedirectResponse
    {
        $this->authorize('delete', $donationCampaign);

        $donationCampaign->delete();

        return redirect()->route('admin.donation-campaigns.index')->with('success', 'Campaign deleted.');
    }
}
