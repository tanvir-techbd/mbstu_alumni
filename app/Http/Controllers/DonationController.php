<?php

namespace App\Http\Controllers;

use App\Http\Requests\Donation\StoreDonationRequest;
use App\Models\Donation;
use App\Models\DonationCampaign;
use App\Services\DonationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DonationController extends Controller
{
    public function __construct(private readonly DonationService $donations)
    {
    }

    public function index(): View
    {
        $campaigns = DonationCampaign::query()
            ->active()
            ->withSum('donations', 'amount')
            ->latest()
            ->paginate(9);

        return view('donations.index', ['campaigns' => $campaigns]);
    }

    public function show(DonationCampaign $campaign): View
    {
        return view('donations.show', ['campaign' => $campaign]);
    }

    public function store(StoreDonationRequest $request, DonationCampaign $campaign): RedirectResponse
    {
        abort_if($campaign->status->value === 'closed', 404);

        $donation = $this->donations->donate($campaign, $request->user(), $request->validated());

        return redirect()->route('donations.history')->with('success', 'Thank you for your donation! Receipt '.$donation->receipt_number.' is ready to download.');
    }

    public function history(Request $request): View
    {
        $donations = $request->user()->donations()->with('campaign')->latest('donated_at')->paginate(15);

        return view('donations.history', ['donations' => $donations]);
    }

    public function receipt(Donation $donation): Response
    {
        $this->authorize('view', $donation);

        $pdf = Pdf::loadView('donations.receipt-pdf', ['donation' => $donation->load(['campaign', 'user'])]);

        return $pdf->download('receipt-'.$donation->receipt_number.'.pdf');
    }
}
