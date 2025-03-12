<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventSearchRequest;
use App\Http\Requests\EventJoinRequest;
use App\Models\User;
use App\Repository\EventRepository;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    private EventRepository $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * Display a listing of events with optional search parameters
     */
    public function index(EventSearchRequest $request): JsonResponse
    {
        $filters = $request->only(['search', 'status', 'date_from', 'date_to']);
        $events = $this->eventRepository->search($filters);

        return response()->json([
            'success' => true,
            'data' => $events,
        ]);
    }

    /**
     * Display a specific event
     *
     */
    public function show(int $id): JsonResponse
    {
        $event = $this->eventRepository->findById($id);

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $event->load(['participants', 'waitListUsers']),
        ]);
    }

    /**
     * Join an event as a participant
     */
    public function join(EventJoinRequest $request): JsonResponse
    {
        $event = $this->eventRepository->findById($request->event_id);

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found',
            ], 404);
        }

        /** @var User $user */
        $user = auth()->user();

        if ($event->isParticipant($user)) {
            return response()->json([
                'success' => false,
                'message' => 'User is already a participant',
            ], 400);
        }

        $this->eventRepository->addParticipant($event, $user);

        return response()->json([
            'success' => true,
            'message' => 'Successfully joined the event',
            'data' => $event->load('participants'),
        ]);
    }

    /**
     * Join an event's wait list
     */
    public function wait(EventJoinRequest $request): JsonResponse
    {
        $event = $this->eventRepository->findById($request->event_id);

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found',
            ], 404);
        }

        /** @var User $user */
        $user = auth()->user();

        if ($event->isOnWaitList($user)) {
            return response()->json([
                'success' => false,
                'message' => 'User is already on the wait list',
            ], 400);
        }

        if ($event->isParticipant($user)) {
            return response()->json([
                'success' => false,
                'message' => 'User is already a participant',
            ], 400);
        }

        $this->eventRepository->addToWaitList($event, $user);

        return response()->json([
            'success' => true,
            'message' => 'Successfully added to wait list',
            'data' => $event->load('waitListUsers'),
        ]);
    }
}
