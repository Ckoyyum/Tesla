<?php

// Bootstrap and app initialization
$app = require __DIR__ . '/../bootstrap.php';

use App\Controllers\AuthController;
use App\Controllers\EventController;
use App\Controllers\VendorController;
use App\Controllers\VenueController;
use App\Controllers\AttendeesController;
use App\Controllers\AttendanceController;
use App\Controllers\SurveyController;
use App\Controllers\BookingController;
use App\Middleware\AuthMiddleware;
use App\Middleware\CorsMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// --- Register Global CORS Middleware ---
$app->add(new CorsMiddleware());

// --- Handle all OPTIONS requests (for CORS preflight) ---
$app->options('/{routes:.+}', function (Request $request, Response $response): Response {
    return $response;
});

// --- Public Routes (No authentication needed) ---
$app->post('/api/register', AuthController::class . ':register');
$app->post('/api/login', AuthController::class . ':login');


$app->group('/api/surveys', function ($group) {
    // Submit a survey for an event
    $group->post('/{id:[0-9]+}', [SurveyController::class, 'submitSurvey']);

});



$app->group('/api/events', function ($group) {
    // Get event details for survey
    $group->get('/{id:[0-9]+}', [SurveyController::class, 'getEvent']);
});

// --- Authenticated Routes Group (/api) ---
$app->group('/api', function ($group) {



    $group->get('/getusers', AuthController::class . ':getUsers'); // users

    $group->group('/bookings', function ($group) {
        // List bookings for venue owner or vendor
        $group->get('', [BookingController::class, 'getMyBookings']);

        // Update booking status
        $group->post('/{id:[0-9]+}', [BookingController::class, 'updateBookingStatus']);

    });

    // Organizer-scoped events
    $group->group('/organizer', function ($organizerGroup) {
        $organizerGroup->get('/events', EventController::class . ':getMyEvents');
        $organizerGroup->post('/events', EventController::class . ':createEvent');
        $organizerGroup->put('/events/{id}', EventController::class . ':updateEvent');

        $organizerGroup->get('/venues', VenueController::class . ':getVenues');

    // // --- Attendance Routes ---
        $organizerGroup->get('/attendees/event/{event_id}', AttendeesController::class . ':getAttendees');
        $organizerGroup->post('/attendees', AttendeesController::class . ':createAttendee');

    // // --- Attendance Routes ---
        $organizerGroup->post('/attendance/scan', AttendanceController::class . ':scanAttendance');
        $organizerGroup->get('/attendance/event/{event_id}', AttendanceController::class . ':getAttendance');

        $organizerGroup->get('/vendor-services', VendorController::class . ':getVendorServices');

        $organizerGroup->get('/surveys/ratings', [SurveyController::class, 'getAverageRatings']);     // organizer rating 

    });

    

         // List all venues for the authenticated venue owner
    $group->get('/venues', [VenueController::class, 'getMyVenues']);
    $group->post('/venues', [VenueController::class, 'createVenue']); // Create a new venue
    $group->get('/venues/{id:[0-9]+}', [VenueController::class, 'getVenue']); // Get a specific venue
    $group->post('/venues/{id:[0-9]+}', [VenueController::class, 'updateVenue']); // Update a venue
    $group->delete('/venues/{id:[0-9]+}', [VenueController::class, 'deleteVenue']);     // Delete a venue

    $group->get('/vendor-services', [VendorController::class, 'getMyServices']); // List all services for the authenticated vendor
    $group->post('/vendor-services', [VendorController::class, 'createService']); // // Create a new service
    $group->get('/vendor-services/{id:[0-9]+}', [VendorController::class, 'getService']); // // Get a specific service
    $group->post('/vendor-services/{id:[0-9]+}', [VendorController::class, 'updateService']); // // Update a service
    $group->delete('/vendor-services/{id:[0-9]+}', [VendorController::class, 'deleteService']); // // Delete a service

    $group->get('/profile', AuthController::class . ':profile'); // --- Authenticated User Profile ---

})->add(new AuthMiddleware(['vendor', 'organizer', 'venue_owner']));

// --- Catch-all Root Route ---
$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write("Welcome to the Event Management API!");
    return $response;
});



// Run the Slim application
$app->run();
