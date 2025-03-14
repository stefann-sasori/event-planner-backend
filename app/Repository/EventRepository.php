<?php

namespace App\Repository;

use App\Enum\EventStatusEnum;
use App\Models\Event;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EventRepository
{
    public function findById(int $id): ?Event
    {
        return Event::find($id);
    }

    public function findByIdOrFail(int $id): ?Event
    {
        return Event::findOrFail($id);
    }

    public function save(Event $event): bool
    {
        return $event->save();
    }

    public function addParticipant(Event $event, User $user): void
    {
        $event->participants()->attach($user);
    }

    public function removeParticipant(Event $event, User $user): void
    {
        $event->participants()->detach($user);
    }

    public function addToWaitList(Event $event, User $user): void
    {
        $event->waitListUsers()->attach($user);
    }

    public function removeFromWaitList(Event $event, User $user): void
    {
        $event->waitListUsers()->detach($user);
    }

    public function search(array $filters = [], int $perPage = 100): LengthAwarePaginator
    {
        $query = Event::query()
        ->where('status', '=', EventStatusEnum::LIVE->value);

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('description', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('location', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['date_from'])) {
            $query->where('starts_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('ends_at', '<=', $filters['date_to']);
        }

        $query->orderBy('starts_at');

        return $query->latest()->paginate($perPage);
    }

    public function hasOverlappingEventsForUser(User $user, Event $newEvent, string $relationship = 'participants'): bool
    {
        return Event::whereHas($relationship, function ($query) use ($user) {
            $query->where('users.id', $user->id);
        })
            ->where('ends_at', '>=', $newEvent->starts_at)
            ->where('starts_at', '<=', $newEvent->ends_at)
            ->exists();
    }
}
