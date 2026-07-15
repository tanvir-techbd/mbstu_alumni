<?php

namespace App\Exports;

use App\Models\FeedbackTicket;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FeedbackTicketsExport implements FromCollection, WithHeadings
{
    public function collection(): Collection
    {
        return FeedbackTicket::query()
            ->with('user')
            ->latest()
            ->get()
            ->map(fn (FeedbackTicket $ticket) => [
                'ID' => $ticket->id,
                'Submitted By' => $ticket->user?->name ?? 'Deleted user',
                'Type' => $ticket->type->label(),
                'Subject' => $ticket->subject,
                'Status' => $ticket->status->label(),
                'Submitted At' => $ticket->created_at->format('Y-m-d H:i'),
                'Closed At' => $ticket->closed_at?->format('Y-m-d H:i') ?? '',
            ]);
    }

    public function headings(): array
    {
        return ['ID', 'Submitted By', 'Type', 'Subject', 'Status', 'Submitted At', 'Closed At'];
    }
}
