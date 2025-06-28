<?php

namespace App\Controllers;

use App\Models\Event;
use App\Models\SurveyResponse;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class SurveyController
{
    /* ---------- GET EVENT FOR SURVEY ---------- */
    public function getEvent(Request $request, Response $response, array $args)
    {
        error_log("came here");
        $event = Event::find($args['id']);
        if (!$event) {
            return $this->json($response, ['message' => 'Event not found'], 404);
        }
        return $this->json($response, ['title' => $event->title]);
    }

    /* ---------- SUBMIT SURVEY ---------- */
    public function submitSurvey(Request $request, Response $response, array $args)
    {
        $event = Event::find($args['id']);
        if (!$event) {
            return $this->json($response, ['message' => 'Event not found'], 404);
        }

        $data = $request->getParsedBody();

        foreach (['venue_rating', 'services_rating', 'management_rating'] as $field) {
            if (!isset($data[$field]) || $data[$field] < 1 || $data[$field] > 5) {
                return $this->json($response, ["message" => "$field must be between 1 and 5"], 422);
            }
        }

        $survey = SurveyResponse::create([
            'event_id' => $args['id'],
            'venue_rating' => $data['venue_rating'],
            'services_rating' => $data['services_rating'],
            'management_rating' => $data['management_rating'],
        ]);

        return $this->json($response, $survey, 201);
    }

    /* ---------- GET AVERAGE RATINGS FOR ORGANIZER'S EVENTS ---------- */
    public function getAverageRatings(Request $request, Response $response)
    {
        $user = $request->getAttribute('user');

        // if ($user->role !== 'organizer') {
        //     return $this->json($response, ['message' => 'Unauthorized'], 403);
        // }

        error_log($user);

        $averageRatings = SurveyResponse::join('events', 'survey_responses.event_id', '=', 'events.id')
            ->where('events.organizer_id', $user->id)
            ->selectRaw('
                AVG(venue_rating) as venue_rating,
                AVG(services_rating) as services_rating,
                AVG(management_rating) as management_rating
            ')
            ->groupBy('events.organizer_id')
            ->first();

        $ratings = [
            'venue_rating' => $averageRatings->venue_rating ?? 0,
            'services_rating' => $averageRatings->services_rating ?? 0,
            'management_rating' => $averageRatings->management_rating ?? 0
        ];

        return $this->json($response, $ratings);
    }

    /* ---------- Helper for JSON Response ---------- */
    private function json(Response $response, $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
}