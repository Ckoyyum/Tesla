<?php

namespace App\Controllers;

use App\Models\Event;
use App\Models\Venue;
use App\Models\VendorService;
use App\Models\EventBooking;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class BookingController
{
    public function getMyBookings(Request $request, Response $response)
    {
        $user = $request->getAttribute('user');

        if (!in_array($user->role, ['venue_owner', 'vendor'])) {
            return $this->json($response, ['message' => 'Unauthorized'], 403);
        }

        $bookings = EventBooking::where(function ($query) use ($user) {
            if ($user->role === 'venue_owner') {
                $query->where('entity_type', 'venue')
                    ->whereIn('entity_id', Venue::where('user_id', $user->id)->pluck('id'));
            } elseif ($user->role === 'vendor') {
                $query->where('entity_type', 'vendor_service')
                    ->whereIn('entity_id', VendorService::where('user_id', $user->id)->pluck('id'));
            }
        })->with(['event'])->get();

        return $this->json($response, $bookings);
    }

    public function updateBookingStatus(Request $request, Response $response, array $args)
    {
        $user = $request->getAttribute('user');
        $booking = EventBooking::find($args['id']);

        if (!$booking) {
            return $this->json($response, ['message' => 'Booking not found'], 404);
        }

        if (!in_array($user->role, ['venue_owner', 'vendor'])) {
            return $this->json($response, ['message' => 'Unauthorized'], 403);
        }

        $isVenueOwner = $user->role === 'venue_owner' && $booking->entity_type === 'venue' && Venue::where('id', $booking->entity_id)->where('user_id', $user->id)->exists();
        $isVendor = $user->role === 'vendor' && $booking->entity_type === 'vendor_service' && VendorService::where('id', $booking->entity_id)->where('user_id', $user->id)->exists();

        if (!$isVenueOwner && !$isVendor) {
            return $this->json($response, ['message' => 'Forbidden'], 403);
        }

        $data = $request->getParsedBody();
        if (!isset($data['status']) || !in_array($data['status'], ['approved', 'rejected'])) {
            return $this->json($response, ['message' => 'Invalid status'], 422);
        }

        $booking->status = $data['status'];
        $booking->save();

        $event = Event::find($booking->event_id);
        $event->updateStatus();

        return $this->json($response, $booking);
    }

    private function json(Response $response, $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
}