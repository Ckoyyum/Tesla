<?php

namespace App\Controllers;

use App\Models\Event;
use App\Models\Attendee;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class AttendeesController
{
    /* ---------- LIST Attendees for an Event ---------- */
    public function getAttendees(Request $request, Response $response, array $args)
    {
        $user = $request->getAttribute('user');
        $eventId = $args['event_id'];

        // Check if the event exists and belongs to the organizer
        $event = Event::find($eventId);
        if (!$event) {
            return $this->json($response, ['message' => 'Event not found'], 404);
        }
        if ($user->role !== 'organizer' || $event->organizer_id !== $user->id) {
            return $this->json($response, ['message' => 'Unauthorized'], 403);
        }

        $attendees = Attendee::where('event_id', $eventId)->get();

        return $this->json($response, $attendees);
    }

    /* ---------- CREATE Attendee ---------- */
    public function createAttendee(Request $request, Response $response)
    {
        error_log("asdasdasd");

        $user = $request->getAttribute('user');
        $data = $request->getParsedBody();

        // Validate input
        if (empty($data['event_id']) || empty($data['name'])) {
            return $this->json($response, ['message' => 'Event ID and name are required'], 422);
        }

        // Check if the event exists and belongs to the organizer
        $event = Event::find($data['event_id']);
        if (!$event) {
            return $this->json($response, ['message' => 'Event not found'], 404);
        }
        if ($user->role !== 'organizer' || $event->organizer_id !== $user->id) {
            return $this->json($response, ['message' => 'Unauthorized'], 403);
        }

        // Get last used QR code number for this event
        $lastAttendee = Attendee::where('event_id', $data['event_id'])
            ->orderBy('id', 'desc')
            ->first();
        $lastNumber = $lastAttendee ? (int)substr($lastAttendee->qr_code, -4) : 0;
        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        $qrCode = $event->title . '-' . $newNumber;

        // Create new attendee
        $attendee = Attendee::create([
            'event_id' => $data['event_id'],
            'name' => $data['name'],
            'qr_code' => $qrCode,
        ]);

        return $this->json($response, $attendee, 201);
    }

    /* ---------- Helper for JSON Response ---------- */
    private function json(Response $response, $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
}