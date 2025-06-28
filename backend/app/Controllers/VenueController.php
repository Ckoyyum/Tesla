<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Venue;
use App\Models\Booking;

class VenueController
{
    private function json(Response $response, $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
    
    // Get all bookings for venues owned by the current user
    public function getOwnerBookings(Request $request, Response $response)
    {
        $user = $request->getAttribute('user');
        
        try {
            // Get all venues owned by this user
            $venueIds = Venue::where('user_id', $user->id)->pluck('id');
            
            // Get all bookings for these venues with organizer information
            $bookings = Booking::whereIn('venue_id', $venueIds)
                ->with(['organizer', 'venue'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Format the response to match what the frontend expects
            $formattedBookings = $bookings->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'title' => $booking->title,
                    'organizer_name' => $booking->organizer->username ?? 'Unknown',
                    'start_date' => $booking->start_date,
                    'end_date' => $booking->end_date,
                    'status' => $booking->status,
                    'venue_name' => $booking->venue->name ?? 'Unknown'
                ];
            });
            
            $response->getBody()->write(json_encode($formattedBookings));
            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['message' => 'Failed to fetch bookings']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
    
    // Approve a booking
    public function approveBooking(Request $request, Response $response, $args)
    {
        $user = $request->getAttribute('user');
        $bookingId = $args['id'];
        
        try {
            $booking = Booking::find($bookingId);
            
            if (!$booking) {
                $response->getBody()->write(json_encode(['message' => 'Booking not found']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
            
            // Check if the venue belongs to the current user
            $venue = Venue::find($booking->venue_id);
            if (!$venue || $venue->user_id !== $user->id) {
                $response->getBody()->write(json_encode(['message' => 'Unauthorized']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
            }
            
            $booking->status = 'approved';
            $booking->save();
            
            $response->getBody()->write(json_encode(['message' => 'Booking approved successfully']));
            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['message' => 'Failed to approve booking']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
    
    // Reject a booking
    public function rejectBooking(Request $request, Response $response, $args)
    {
        $user = $request->getAttribute('user');
        $bookingId = $args['id'];
        
        try {
            $booking = Booking::find($bookingId);
            
            if (!$booking) {
                $response->getBody()->write(json_encode(['message' => 'Booking not found']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
            
            // Check if the venue belongs to the current user
            $venue = Venue::find($booking->venue_id);
            if (!$venue || $venue->user_id !== $user->id) {
                $response->getBody()->write(json_encode(['message' => 'Unauthorized']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
            }
            
            $booking->status = 'rejected';
            $booking->save();
            
            $response->getBody()->write(json_encode(['message' => 'Booking rejected successfully']));
            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['message' => 'Failed to reject booking']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
    
    // Add a new venue with image upload
    public function addVenue(Request $request, Response $response, array $args): Response
{
    try {
        $parsedBody = $request->getParsedBody();
        $uploadedFiles = $request->getUploadedFiles();

        error_log("Parsed body: " . json_encode($parsedBody));
        error_log("Uploaded files: " . json_encode(array_keys($uploadedFiles)));

        $user = $request->getAttribute('user');  // comes from AuthMiddleware
        error_log("User: " . json_encode($user));

        if (!$user || !isset($user->id)) {
            return $this->json($response, ['message' => 'Unauthorized'], 401);
        }

        $venue = new Venue();
        $venue->user_id = $user->id;
        $venue->name = $parsedBody['name'] ?? null;
        $venue->address = $parsedBody['address'] ?? null;
        $venue->capacity = $parsedBody['capacity'] ?? null;
        $venue->description = $parsedBody['description'] ?? null;
        $venue->price = $parsedBody['price'] ?? null;

        if (isset($uploadedFiles['image'])) {
            $image = $uploadedFiles['image'];
            if ($image->getError() === UPLOAD_ERR_OK) {
                $directory = __DIR__ . '/../../public/uploads/venues/';
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }
                $filename = uniqid() . "_" . $image->getClientFilename();
                $image->moveTo($directory . $filename);
                $venue->image = "/uploads/venues/" . $filename;
            }
        }

        $venue->save();

        return $this->json($response, ['message' => 'Venue added successfully'], 201);

    } catch (\Exception $e) {
        error_log("Venue creation error: " . $e->getMessage());
        return $this->json($response, ['message' => 'Unable to add venue'], 500);
    }
}

    
    // Get all venues owned by the current user
    public function getOwnerVenues(Request $request, Response $response)
    {
        $user = $request->getAttribute('user');
        
        try {
            $venues = Venue::where('user_id', $user->id)->get();
            
            $response->getBody()->write(json_encode($venues));
            return $response->withHeader('Content-Type', 'application/json');
            
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['message' => 'Failed to fetch venues']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function getVenues(Request $request, Response $response)
    {
        // $user = $request->getAttribute('user');

        // $venues = Venue::select('id', 'name')->get();

        $venues = Venue::all(); // This returns all fields for each venue


        return $this->json($response, $venues, 200);
    }

    /* ---------- LIST for venue owner ---------- */
    public function getMyVenues(Request $request, Response $response)
    {
        $user = $request->getAttribute('user');

        if ($user->role !== 'venue_owner') {
            return $this->json($response, ['message' => 'Unauthorized'], 403);
        }

        $venues = Venue::where('user_id', $user->id)
            ->orderBy('name', 'asc')
            ->get();

        return $this->json($response, $venues);
    }

    /* ---------- CREATE ---------- */
    public function createVenue(Request $request, Response $response)
    {
        $user = $request->getAttribute('user');
        
        if ($user->role !== 'venue_owner') {
            return $this->json($response, ['message' => 'Unauthorized'], 403);
        }

        

        $data = $request->getParsedBody();
        $uploadedFiles = $request->getUploadedFiles();

        foreach (['name', 'address', 'capacity', 'price'] as $field) {
            if (empty($data[$field])) {
                return $this->json($response, ["message" => "$field is required"], 422);
            }
        }

        $imagePath = null;
        if (isset($uploadedFiles['image']) && $uploadedFiles['image']->getError() === UPLOAD_ERR_OK) {
            $image = $uploadedFiles['image'];
            $filename = uniqid() . '_' . $image->getClientFilename();
            $image->moveTo('uploads/venues/' . $filename);
            $imagePath = '/uploads/venues/' . $filename;
        }

        $venue = Venue::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'address' => $data['address'],
            'capacity' => $data['capacity'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'image' => $imagePath,
        ]);
        error_log('wweeeeeewwooooo');


        return $this->json($response, $venue, 201);
    }

    /* ---------- READ ---------- */
    public function getVenue(Request $request, Response $response, array $args)
    {
        $venue = Venue::find($args['id']);
        if (!$venue) {
            return $this->json($response, ['message' => 'Not found'], 404);
        }
        return $this->json($response, $venue);
    }

    /* ---------- UPDATE ---------- */
    public function updateVenue(Request $request, Response $response, array $args)
    {
        error_log('hegewwew');

        $user = $request->getAttribute('user');
        $venue = Venue::find($args['id']);

        if (!$venue) {
            return $this->json($response, ['message' => 'Not found'], 404);
        }
        if ($venue->user_id !== $user->id) {
            return $this->json($response, ['message' => 'Forbidden'], 403);
        }

        $data = $request->getParsedBody();
        $uploadedFiles = $request->getUploadedFiles();
        error_log('hegewwew');

        $imagePath = $venue->image;
        if (isset($uploadedFiles['image']) && $uploadedFiles['image']->getError() === UPLOAD_ERR_OK) {
            // Delete old image if it exists
            if ($imagePath && file_exists('uploads/venues/' . basename($imagePath))) {
                unlink('uploads/venues/' . basename($imagePath));
            }
            $image = $uploadedFiles['image'];
            $filename = uniqid() . '_' . $image->getClientFilename();
            $image->moveTo('uploads/venues/' . $filename);
            $imagePath = '/uploads/venues/' . $filename;
        }

        $venue->fill([
            'name' => $data['name'] ?? $venue->name,
            'address' => $data['address'] ?? $venue->address,
            'capacity' => $data['capacity'] ?? $venue->capacity,
            'description' => $data['description'] ?? $venue->description,
            'price' => $data['price'] ?? $venue->price,
            'image' => $imagePath,
        ]);
        $venue->save();

        return $this->json($response, $venue);
    }

    /* ---------- DELETE ---------- */
    public function deleteVenue(Request $request, Response $response, array $args)
    {
        $user = $request->getAttribute('user');
        $venue = Venue::find($args['id']);

        if (!$venue) {
            return $this->json($response, ['message' => 'Not found'], 404);
        }
        if ($venue->user_id !== $user->id) {
            return $this->json($response, ['message' => 'Forbidden'], 403);
        }

        if ($venue->image && file_exists('uploads/venues/' . basename($venue->image))) {
            unlink('uploads/venues/' . basename($venue->image));
        }

        $venue->delete();
        return $this->json($response, ['message' => 'Deleted'], 200);
    }

    
}