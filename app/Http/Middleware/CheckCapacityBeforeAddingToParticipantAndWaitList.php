<?php

namespace App\Http\Middleware;

use App\Repository\EventRepository;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Response;

class CheckCapacityBeforeAddingToParticipantAndWaitList
{

    public function __construct(private readonly EventRepository $eventRepository)
    {
    }

    private const NOVA_ATTACH_ROUTE_PATTERN = '#^nova-api/events/\d+/attach/users#';

    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next ): Response
    {
        if($this->isJoinRoute($request)){
            $authorize = $this->authorizeFromEventApiEndpoint($request);
        }elseif($this->isNovaAttachToEventRoute($request)){
            $authorize = $this->authorizeFromNovaRequest($request);
        }else{
            // If it is not an endpoint that involves attaching users to the event just continue.
            $authorize = true;
        }

        if(!$authorize){
            throw new BadRequestException('Invalid request: Capacity is full');
        }

        return $next($request);
    }

    private function authorizeFromEventApiEndpoint(Request $request): bool
    {
        $eventId = $request->route('event_id');
        $event = $this->eventRepository->findById($eventId);

        return $event->canAddNewParticipant();
    }

    private function authorizeFromNovaRequest(Request $request): bool
    {
        $eventId = $request->route('resourceId');
        $event = $this->eventRepository->findById($eventId);
        $relationship = $request->getPayload()->get('viaRelationship');

        if($relationship === 'participants'){
            return $event->canAddNewParticipant();
        }elseif ($relationship === 'waitListUsers'){
            return $event->canAddInWaitList();
        }

        return true;
    }

    private function isJoinRoute(Request $request): bool
    {
        return $request->route()->getName() === 'event_join';
    }

    private function isNovaAttachToEventRoute(Request $request): bool
    {
        return preg_match(self::NOVA_ATTACH_ROUTE_PATTERN, $request->path());
    }
}
