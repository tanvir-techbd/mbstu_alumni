<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\DonationCampaign;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DonationReportController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewReports', DonationCampaign::class);

        $donations = Donation::query()
            ->with(['user', 'campaign'])
            ->when($request->filled('campaign_id'), fn ($q) => $q->where('donation_campaign_id', $request->integer('campaign_id')))
            ->when($request->filled('payment_method'), fn ($q) => $q->where('payment_method', $request->string('payment_method')))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('donated_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('donated_at', '<=', $request->date('to')))
            ->latest('donated_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.donations.reports.index', [
            'donations' => $donations,
            'campaigns' => DonationCampaign::orderBy('title')->get(),
            'filters' => $request->only(['campaign_id', 'payment_method', 'from', 'to']),
            'totalRaised' => Donation::sum('amount'),
            'totalDonors' => Donation::distinct('user_id')->count('user_id'),
        ]);
    }
}
