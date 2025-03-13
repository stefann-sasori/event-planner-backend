<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventSearchRequest;
use App\Http\Requests\EventJoinRequest;
use App\Repository\EventRepository;
use App\Service\EventService;
use Illuminate\Http\JsonResponse;
use Throwable;

class EventController extends Controller
{

    public function __construct(private readonly EventRepository $eventRepository, private readonly EventService $eventService)
    {
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
        $event = $this->eventRepository->findByIdOrFail($id);

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
        $event = $this->eventRepository->findByIdOrFail($request->event_id);

        try {
            $this->eventService->join($event, auth()->user());
        }catch (Throwable $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }

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
        $event = $this->eventRepository->findByIdOrFail($request->event_id);

        try {
            $this->eventService->wait($event, auth()->user());
        }catch (Throwable $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }


        return response()->json([
            'success' => true,
            'message' => 'Successfully added to wait list',
            'data' => $event->load('waitListUsers'),
        ]);
    }
}
