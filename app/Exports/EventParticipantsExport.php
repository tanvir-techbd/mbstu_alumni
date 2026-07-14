<?php

namespace App\Exports;

use App\Models\Event;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EventParticipantsExport implements FromCollection, WithHeadings
{
    public function __construct(private readonly Event $event)
    {
    }

    public function collection(): Collection
    {
        return $this->event->registrations()
            ->with('user')
            ->get()
            ->map(fn ($registration) => [
                'Name' => $registration->user->name,
                'Email' => $registration->user->email,
                'Phone' => $registration->user->phone,
                'Registered At' => $registration->created_at->format('Y-m-d H:i'),
                'Attended' => $registration->attended ? 'Yes' : 'No',
            ]);
    }

    public function headings(): array
    {
        return ['Name', 'Email', 'Phone', 'Registered At', 'Attended'];
    }
}
