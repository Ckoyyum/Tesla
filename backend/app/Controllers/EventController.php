<?php

namespace App\Controllers;

use App\Models\Event;
use App\Models\Venue;
use App\Models\VendorService;
use App\Models\EventBooking;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class EventController
{
    // /* ---------- LIST for organizer ---------- */
    // public function getMyEvents(Request $request, Response $response)
    // {
    //     $user = $request->getAttribute('user');

    //     if ($user->role !== 'organizer') {
    //         return $this->json($response, ['message' => 'Unauthorized'], 403);
    //     }

    //     $events = Event::with('venue')
    //         ->where('organizer_id', $user->id)
    //         ->orderBy('start_date', 'desc')
    //         ->get();

    //     return $this->json($response, $events);
    // }

    // /* ---------- CREATE ---------- */
    // public function createEvent(Request $request, Response $response)
    // {
    //     $user = $request->getAttribute('user');
    //     if ($user->role !== 'organizer') {
    //         return $this->json($response, ['message' => 'Unauthorized'], 403);
    //     }

    //     $data = $request->getParsedBody();

    //     foreach (['title', 'description', 'start_date', 'end_date'] as $field) {
    //         if (empty($data[$field])) {
    //             return $this->json($response, ["message" => "$field is required"], 422);
    //         }
    //     }

    //     if (isset($data['venue_id'])) {
    //         $venue = Venue::find($data['venue_id']);
    //         if (!$venue) {
    //             return $this->json($response, ['message' => 'Venue not found'], 404);
    //         }
    //     }

    //     $event = Event::create([
    //         'organizer_id' => $user->id,
    //         'title'        => $data['title'],
    //         'description'  => $data['description'],
    //         'start_date'   => $data['start_date'],
    //         'end_date'     => $data['end_date'],
    //         'venue_id'     => $data['venue_id'] ?? null,
    //         'status'       => 'pending',
    //     ]);

    //     return $this->json($response, $event, 201);
    // }

    public function createEvent2(Request $request, Response $response)
    {
        $user = $request->getAttribute('user');

        if ($user->role !== 'organizer') {
            return $this->json($response, ['message' => 'Unauthorized'], 403);
        }

        $data = (array)$request->getParsedBody();

        $event = new Event();
        $event->organizer_id = $user->id;
        $event->title = $data['title'];
        $event->description = $data['description'];
        $event->start_date = $data['start_date'];
        $event->end_date = $data['end_date'];
        $event->venue_id = $data['venue_id'];

        $event->save();

        return $this->json($response, $event, 201);
    }


    // /* ---------- READ ---------- */
    // public function getEvent(Request $request, Response $response, array $args)
    // {
    //     $event = Event::with(['venue', 'organizer'])->find($args['id']);
    //     if (!$event) {
    //         return $this->json($response, ['message' => 'Not found'], 404);
    //     }
    //     return $this->json($response, $event);
    // }

    /* ---------- UPDATE ---------- */
    // public function updateEvent(Request $request, Response $response, array $args)
    // {
    //     $user = $request->getAttribute('user');
    //     $event = Event::find($args['id']);

    //     if (!$event) {
    //         return $this->json($response, ['message' => 'Not found'], 404);
    //     }
    //     if ($event->organizer_id !== $user->id) {
    //         return $this->json($response, ['message' => 'Forbidden'], 403);
    //     }

    //     $data = $request->getParsedBody();
    //     $event->fill($data);
    //     $event->save();

    //     return $this->json($response, $event);
    // }

    public function updateEvent2(Request $request, Response $response, array $args)
    {
        $user = $request->getAttribute('user');

        if ($user->role !== 'organizer') {
            return $this->json($response, ['message' => 'Unauthorized'], 403);
        }

        $eventId = $args['id'];
        $event = Event::find($eventId);

        if (!$event) {
            return $this->json($response, ['message' => 'Event not found'], 404);
        }

        if ($event->organizer_id !== $user->id) {
            return $this->json($response, ['message' => 'Unauthorized: You can only edit your own events'], 403);
        }

        $data = (array)$request->getParsedBody();

        $event->title = $data['title'] ?? $event->title;
        $event->description = $data['description'] ?? $event->description;
        $event->start_date = $data['start_date'] ?? $event->start_date;
        $event->end_date = $data['end_date'] ?? $event->end_date;
        $event->venue_id = $data['venue_id'] ?? $event->venue_id;

        $event->save();

        return $this->json($response, $event, 200);
    }

    /* ---------- DELETE ---------- */
    // public function deleteEvent(Request $request, Response $response, array $args)
    // {
    //     $user = $request->getAttribute('user');
    //     $event = Event::find($args['id']);

    //     if (!$event) {
    //         return $this->json($response, ['message' => 'Not found'], 404);
    //     }
    //     if ($event->organizer_id !== $user->id) {
    //         return $this->json($response, ['message' => 'Forbidden'], 403);
    //     }

    //     $event->delete();
    //     return $this->json($response, ['message' => 'Deleted'], 200);
    // }

    /* ---------- Helper for JSON Response ---------- */
    // private function json(Response $response, $data, int $status = 200): Response
    // {
    //     $response->getBody()->write(json_encode($data));
    //     return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    // }

    // new getevents
    public function getEvents(Request $request, Response $response, array $args): Response
    {
        // $params = $request->getQueryParams();
        // $email = $params['email'] ?? '';

        // // Sample SQL to fetch events for the organizer
        // $sql = "SELECT title, start_date, end_date, color FROM events WHERE organizer_email = :email";

        // try {
        //     $stmt = $this->db->prepare($sql);
        //     $stmt->bindParam(':email', $email);
        //     $stmt->execute();

        //     $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //     // Optional: format the dates or apply transformations here

        //     $response->getBody()->write(json_encode($events));
        //     return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        // } catch (\PDOException $e) {
        //     $error = ['error' => $e->getMessage()];
        //     $response->getBody()->write(json_encode($error));
        //     return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        // }

        $user = $request->getAttribute('user');
        $email = $user->email;

        $sql = "SELECT title, start_date, end_date FROM events WHERE organizer_email = :email";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $events = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $response->getBody()->write(json_encode($events));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\PDOException $e) {
            return $response->withJson(['error' => $e->getMessage()], 500);
        }
    }

    /* ---------- LIST for organizer ---------- */
    public function getMyEvents(Request $request, Response $response)
    {
        $user = $request->getAttribute('user');

        if ($user->role !== 'organizer') {
            return $this->json($response, ['message' => 'Unauthorized'], 403);
        }

        $events = Event::with(['venue', 'vendorServices'])
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

        foreach (['title', 'description', 'start_date', 'end_date', 'venue_id'] as $field) {
            if (empty($data[$field])) {
                return $this->json($response, ["message" => "$field is required"], 422);
            }
        }

        if (!isset($data['vendor_service_ids']) || !is_array($data['vendor_service_ids']) || empty($data['vendor_service_ids'])) {
            return $this->json($response, ["message" => "At least one vendor service is required"], 422);
        }

        $venue = Venue::find($data['venue_id']);
        if (!$venue) {
            return $this->json($response, ['message' => 'Venue not found'], 404);
        }

        $vendorServices = VendorService::whereIn('id', $data['vendor_service_ids'])->get();
        if ($vendorServices->count() !== count($data['vendor_service_ids'])) {
            return $this->json($response, ['message' => 'One or more vendor services not found'], 404);
        }

        $event = Event::create([
            'organizer_id' => $user->id,
            'title' => $data['title'],
            'description' => $data['description'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'venue_id' => $data['venue_id'],
            'status' => 'pending',
        ]);

        // Attach vendor services
        $event->vendorServices()->sync($data['vendor_service_ids']);

        // Create booking records
        EventBooking::create([
            'event_id' => $event->id,
            'entity_type' => 'venue',
            'entity_id' => $data['venue_id'],
            'status' => 'pending',
        ]);

        foreach ($data['vendor_service_ids'] as $serviceId) {
            EventBooking::create([
                'event_id' => $event->id,
                'entity_type' => 'vendor_service',
                'entity_id' => $serviceId,
                'status' => 'pending',
            ]);
        }

        return $this->json($response, $event, 201);
    }

    /* ---------- READ ---------- */
    public function getEvent(Request $request, Response $response, array $args)
    {
        $event = Event::with(['venue', 'vendorServices', 'bookings'])->find($args['id']);
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

       

        // Collect vendor_service_ids[]
        // $vendorServiceIds = [];
        // foreach ($data as $key => $value) {
        //     if (str_starts_with($key, 'vendor_service_ids')) {
        //         $vendorServiceIds[] = $value;
        //     }
        // }
        // $data['vendor_service_ids'] = $vendorServiceIds;


        if (isset($data['venue_id'])) {
            $venue = Venue::find($data['venue_id']);
            if (!$venue) {
                return $this->json($response, ['message' => 'Venue not found'], 404);
            }
        }

        if (isset($data['vendor_service_ids'])) {
            if (!is_array($data['vendor_service_ids']) || empty($data['vendor_service_ids'])) {
                return $this->json($response, ["message" => "At least one vendor service is required"], 422);
            }
            $vendorServices = VendorService::whereIn('id', $data['vendor_service_ids'])->get();
            if ($vendorServices->count() !== count($data['vendor_service_ids'])) {
                return $this->json($response, ['message' => 'One or more vendor services not found'], 404);
            }
        }


        $event->fill([
            'title' => $data['title'] ?? $event->title,
            'description' => $data['description'] ?? $event->description,
            'start_date' => $data['start_date'] ?? $event->start_date,
            'end_date' => $data['end_date'] ?? $event->end_date,
            'venue_id' => $data['venue_id'] ?? $event->venue_id,
            'status' => 'pending', // Reset to pending if updated
        ]);
        $event->save();

        if (isset($data['vendor_service_ids'])) {
            $event->vendorServices()->sync($data['vendor_service_ids']);
            // Update booking records
            EventBooking::where('event_id', $event->id)->delete();
            EventBooking::create([
                'event_id' => $event->id,
                'entity_type' => 'venue',
                'entity_id' => $event->venue_id,
                'status' => 'pending',
            ]);
            foreach ($data['vendor_service_ids'] as $serviceId) {
                EventBooking::create([
                    'event_id' => $event->id,
                    'entity_type' => 'vendor_service',
                    'entity_id' => $serviceId,
                    'status' => 'pending',
                ]);
            }
        }

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
