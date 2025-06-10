public function updateEventStatus($request, $response, $args)
{
    $user = $request->getAttribute('user');
    $eventId = $args['id'];
    $parsedBody = $request->getParsedBody();
    $status = $parsedBody['status']; // 'approved' or 'rejected'

    if (!in_array($status, ['approved', 'rejected'])) {
        return $response->withJson(['message' => 'Invalid status'], 400);
    }

    $event = \App\Models\Event::find($eventId);

    if (!$event) {
        return $response->withJson(['message' => 'Event not found'], 404);
    }

    // Ensure this venue belongs to the current owner
    $venue = \App\Models\Venue::find($event->venue_id);
    if (!$venue || $venue->user_id !== $user->id) {
        return $response->withJson(['message' => 'Unauthorized'], 403);
    }

    $event->status = $status;
    $event->save();

    return $response->withJson(['message' => "Event $status"], 200);
}
