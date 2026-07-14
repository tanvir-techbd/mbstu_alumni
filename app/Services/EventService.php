<?php

namespace App\Services;

use App\Enums\EventStatus;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class EventService
{
    public function create(array $data, User $creator, ?UploadedFile $banner = null): Event
    {
        $event = new Event($data);
        $event->created_by = $creator->id;
        $event->forceFill(['status' => EventStatus::Draft]);

        if ($banner) {
            $event->banner_path = $banner->store('event-banners', 'public');
        }

        $event->save();

        return $event;
    }

    public function update(Event $event, array $data, ?UploadedFile $banner = null): Event
    {
        $event->fill($data);

        if ($banner) {
            if ($event->banner_path) {
                Storage::disk('public')->delete($event->banner_path);
            }
            $event->banner_path = $banner->store('event-banners', 'public');
        }

        $event->save();

        return $event;
    }

    public function publish(Event $event): void
    {
        $event->forceFill(['status' => EventStatus::Published])->save();
    }

    public function archive(Event $event): void
    {
        $event->forceFill(['status' => EventStatus::Archived])->save();
    }

    public function delete(Event $event): void
    {
        if ($event->banner_path) {
            Storage::disk('public')->delete($event->banner_path);
        }

        $event->delete();
    }

    public function register(Event $event, User $user): void
    {
        if (! $event->isRegistrationOpen()) {
            throw ValidationException::withMessages([
                'registration' => 'Registration for this event is closed.',
            ]);
        }

        if ($event->isRegisteredBy($user)) {
            throw ValidationException::withMessages([
                'registration' => 'You are already registered for this event.',
            ]);
        }

        $event->registrations()->create(['user_id' => $user->id]);
    }

    public function cancelRegistration(Event $event, User $user): void
    {
        $event->registrations()->where('user_id', $user->id)->delete();
    }

    public function markAttendance(Event $event, User $user, bool $attended): void
    {
        $event->registrations()->where('user_id', $user->id)->update(['attended' => $attended]);
    }
}
