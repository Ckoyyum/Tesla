<?php

namespace App\Controllers;

use App\Models\Event;
use App\Models\Attendee;
use App\Models\Attendance;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class AttendanceController
{
    /* ---------- MARK Attendance for an Attendee ---------- */
    public function markAttendance(Request $request, Response $response, array $args)
    {
        $user = $request->getAttribute('user');
        $eventId = $args['event_id'];
        $attendeeId = $args['attendee_id'];

        // Check if the event exists and belongs to the organizer
        $event = Event::find($eventId);
        if (!$event) {
            return $this->json($response, ['message' => 'Event not found'], 404);
        }
        if ($user->role !== 'organizer' || $event->organizer_id !== $user->id) {
            return $this->json($response, ['message' => 'Unauthorized'], 403);
        }

        // Check if the attendee exists and is registered for the event
        $attendee = Attendee::where('id', $attendeeId)->where('event_id', $eventId)->first();
        if (!$attendee) {
            return $this->json($response, ['message' => 'Attendee not found or not registered for this event'], 404);
        }

        // Check if attendance is already marked
        $existingAttendance = Attendance::where('attendee_id', $attendeeId)
            ->where('event_id', $eventId)
            ->first();
        if ($existingAttendance) {
            return $this->json($response, ['message' => 'Attendance already marked for this attendee'], 422);
        }

        // Mark attendance
        $attendance = Attendance::create([
            'event_id' => $eventId,
            'attendee_id' => $attendeeId,
            // 'attended_at' => now(),
            'attended_at' => (new DateTime())->format('Y-m-d H:i:s'),
            
        ]);

        return $this->json($response, $attendance, 201);
    }

    /* ---------- LIST Attendance for an Event ---------- */
    public function getAttendance(Request $request, Response $response, array $args)
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

        // Retrieve attendance records with attendee details
        $attendance = Attendance::where('event_id', $eventId)
            ->with(['attendee' => function ($query) {
                $query->select('id', 'name', 'qr_code');
            }])
            ->get();

        return $this->json($response, $attendance);
    }

    /* ---------- SCAN QR Code and Mark Attendance ---------- */
    public function scanAttendance(Request $request, Response $response)
    {
        $user = $request->getAttribute('user');
        $data = $request->getParsedBody();

        // Validate input
        if (empty($data['qr_code']) || empty($data['event_id'])) {
            return $this->json($response, ['message' => 'QR code and event ID are required', 'status' => 'error'], 422);
        }

        // Check if the event exists and belongs to the organizer
        $event = Event::find($data['event_id']);
        if (!$event) {
            return $this->json($response, ['message' => 'Event not found', 'status' => 'error'], 404);
        }
        if ($user->role !== 'organizer' || $event->organizer_id !== $user->id) {
            return $this->json($response, ['message' => 'Unauthorized', 'status' => 'error'], 403);
        }

        
        // Find attendee by QR code and event
        $attendee = Attendee::where('qr_code', $data['qr_code'])
            ->where('event_id', $data['event_id'])
            ->first();
        if (!$attendee) {
            return $this->json($response, ['message' => 'Invalid QR code or attendee not found for this event', 'status' => 'error'], 404);
        }

        // Check if attendance is already marked
        $existingAttendance = Attendance::where('attendee_id', $attendee->id)
            ->where('event_id', $data['event_id'])
            ->first();
        if ($existingAttendance) {
            return $this->json($response, [
                'message' => 'Attendance already marked for ' . $attendee->name,
                'status' => 'success',
                'attendee' => $attendee
            ], 200);
        }

        // Mark attendance
        $attendance = Attendance::create([
            'event_id' => $data['event_id'],
            'attendee_id' => $attendee->id,
            // 'attended_at' => now(),
            'attended_at' => date('Y-m-d H:i:s'),

        ]);

        return $this->json($response, [
            'message' => 'Attendance marked for ' . $attendee->name,
            'status' => 'success',
            'attendee' => $attendee,
            'attendance' => $attendance
        ], 201);
    }

    /* ---------- Helper for JSON Response ---------- */
    private function json(Response $response, $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
}