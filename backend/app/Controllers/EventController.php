<?php

namespace App\Controllers;

use App\Models\Event;
use App\Models\Venue;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class EventController
{
    /* ---------- LIST for organizer ---------- */
    public function getMyEvents(Request $request, Response $response)
    {
        $user = $request->getAttribute('user');

        if ($user->role !== 'organizer') {
            return $this->json($response, ['message' => 'Unauthorized'], 403);
        }

        $events = Event::with('venue')
            ->where('organizer_id', $user->id)
            ->orderBy('start_date', 'desc')
            ->get();

        return $this->json($response, $events);
    }

    /* ---------- CREATE ---------- */
    public function createEvent(Request $request, Response $response)
    {
        $user = $request->getAttribute('user');
        if ($user->role !== 'organizer') {
            return $this->json($response, ['message' => 'Unauthorized'], 403);
        }

        $data = $request->getParsedBody();

        foreach (['title', 'description', 'start_date', 'end_date'] as $field) {
            if (empty($data[$field])) {
                return $this->json($response, ["message" => "$field is required"], 422);
            }
        }

        if (isset($data['venue_id'])) {
            $venue = Venue::find($data['venue_id']);
            if (!$venue) {
                return $this->json($response, ['message' => 'Venue not found'], 404);
            }
        }

        $event = Event::create([
            'organizer_id' => $user->id,
            'title'        => $data['title'],
            'description'  => $data['description'],
            'start_date'   => $data['start_date'],
            'end_date'     => $data['end_date'],
            'venue_id'     => $data['venue_id'] ?? null,
            'status'       => 'pending',
        ]);

        return $this->json($response, $event, 201);
    }

    /* ---------- READ ---------- */
    public function getEvent(Request $request, Response $response, array $args)
    {
        $event = Event::with(['venue', 'organizer'])->find($args['id']);
        if (!$event) {
            return $this->json($response, ['message' => 'Not found'], 404);
        }
        return $this->json($response, $event);
    }

    /* ---------- UPDATE ---------- */
    public function updateEvent(Request $request, Response $response, array $args)
    {
        $user = $request->getAttribute('user');
        $event = Event::find($args['id']);

        if (!$event) {
            return $this->json($response, ['message' => 'Not found'], 404);
        }
        if ($event->organizer_id !== $user->id) {
            return $this->json($response, ['message' => 'Forbidden'], 403);
        }

        $data = $request->getParsedBody();
        $event->fill($data);
        $event->save();

        return $this->json($response, $event);
    }

    /* ---------- DELETE ---------- */
    public function deleteEvent(Request $request, Response $response, array $args)
    {
        $user = $request->getAttribute('user');
        $event = Event::find($args['id']);

        if (!$event) {
            return $this->json($response, ['message' => 'Not found'], 404);
        }
        if ($event->organizer_id !== $user->id) {
            return $this->json($response, ['message' => 'Forbidden'], 403);
        }

        $event->delete();
        return $this->json($response, ['message' => 'Deleted'], 200);
    }

    /* ---------- Helper for JSON Response ---------- */
    private function json(Response $response, $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
}
