<?php

namespace App\Service;

use App\Models\Event;
use App\Models\User;
use App\Notifications\EventJoined;
use App\Repository\EventRepository;
use Exception;

final readonly class EventService
{
    public function __construct(private EventRepository $eventRepository)
    {
    }

    /**
     * Allows a user to join an event if conditions are met.
     *
     * @throws Exception When the user cannot join the event
     */
    public function join(Event $event, User $user): void
    {
        $this->validateParticipation($event, $user, 'participants');

        $event->participants()->attach($user);
        $user->notify(new EventJoined($event));
    }

    /**
     * Adds a user to the event's wait list if conditions are met.
     *
     * @throws Exception When the user cannot be added to the wait list
     */
    public function wait(Event $event, User $user): void
    {
        $this->validateParticipation($event, $user, 'waitListUsers');

        $event->waitListUsers()->attach($user);
        $user->notify(new EventJoined($event, 'waitListUsers'));
    }

    /**
     * Validates whether a user can participate in an event (either as a participant or wait list).
     *
     * @throws Exception When validation fails
     */
    private function validateParticipation(Event $event, User $user, string $listType): void
    {
        $canAdd = $listType === 'participants'
            ? $event->canAddNewParticipant()
            : $event->canAddInWaitList();

        if (!$canAdd) {
            throw new Exception(sprintf(
                'Event capacity exceeded for %s',
                $listType === 'participants' ? 'participants' : 'wait list'
            ));
        }

        if($listType === 'participants' ? $event->isParticipant($user): $event->isOnWaitList($user)){
            throw new Exception('User has already joined');
        }

        if ($event->isPast()) {
            throw new Exception('Cannot join a past event');
        }

        if ($this->eventRepository->hasOverlappingEventsForUser($user, $event, $listType)) {
            throw new Exception('User has overlapping events');
        }
    }
}
