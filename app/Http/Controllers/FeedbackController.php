<?php

namespace App\Http\Controllers;

use App\Enums\FeedbackType;
use App\Enums\RoleName;
use App\Exports\FeedbackTicketsExport;
use App\Http\Requests\Feedback\StoreFeedbackReplyRequest;
use App\Http\Requests\Feedback\StoreFeedbackTicketRequest;
use App\Models\FeedbackTicket;
use App\Services\FeedbackService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FeedbackController extends Controller
{
    public function __construct(private readonly FeedbackService $feedback)
    {
    }

    public function index(Request $request): View
    {
        $query = FeedbackTicket::query()->with('user');

        if (! $request->user()->hasRole(RoleName::SuperAdmin->value)) {
            $query->where('user_id', $request->user()->id);
        }

        $tickets = $query
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search');
                $q->where('subject', 'like', "%{$search}%");
            })
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->string('type')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('feedback.index', [
            'tickets' => $tickets,
            'filters' => $request->only(['search', 'type', 'status']),
            'types' => FeedbackType::cases(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', FeedbackTicket::class);

        return view('feedback.create', ['types' => FeedbackType::cases()]);
    }

    public function store(StoreFeedbackTicketRequest $request): RedirectResponse
    {
        $ticket = $this->feedback->createTicket($request->validated(), $request->user());

        return redirect()->route('feedback.show', $ticket)->with('success', 'Ticket submitted.');
    }

    public function show(Request $request, FeedbackTicket $ticket): View
    {
        $this->authorize('view', $ticket);

        return view('feedback.show', [
            'ticket' => $ticket->load('replies.user'),
        ]);
    }

    public function reply(StoreFeedbackReplyRequest $request, FeedbackTicket $ticket): RedirectResponse
    {
        $this->feedback->reply($ticket, $request->validated(), $request->user());

        return redirect()->route('feedback.show', $ticket)->with('success', 'Reply posted.');
    }

    public function close(FeedbackTicket $ticket): RedirectResponse
    {
        $this->authorize('close', $ticket);

        $this->feedback->close($ticket);

        return redirect()->route('feedback.show', $ticket)->with('success', 'Ticket closed.');
    }

    public function export(): BinaryFileResponse
    {
        $this->authorize('export', FeedbackTicket::class);

        return Excel::download(new FeedbackTicketsExport, 'feedback-tickets.xlsx');
    }
}
