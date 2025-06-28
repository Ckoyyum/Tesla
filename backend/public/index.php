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
$app->post('/register', AuthController::class . ':register');
$app->post('/login', AuthController::class . ':login');
// $app->post('/sign-in', AuthController::class . ':login');

// $app->get('/sign-in', function ($request, $response) {
//     $response->getBody()->write("This is the sign-in page.");
//     return $response;
// });

$app->group('/api/surveys', function ($group) {
    // Submit a survey for an event
    $group->post('/{id:[0-9]+}', [SurveyController::class, 'submitSurvey']);

    // Get average ratings for organizer's events
    // $group->get('/ratings', [SurveyController::class, 'getAverageRatings']);
        // ->add(function (Request $request, Response $response, callable $next) {
        //     $user = $request->getAttribute('user');
        //     if (!$user) {
        //         $response->getBody()->write(json_encode(['message' => 'Unauthenticated']));
        //         return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        //     }
        //     return $next($request, $response);
        // });
});



$app->group('/api/events', function ($group) {
    // Get event details for survey
    $group->get('/{id:[0-9]+}', [SurveyController::class, 'getEvent']);
});

// --- Authenticated Routes Group (/api) ---
$app->group('/api', function ($group) {


    // organizer rating 
    $group->get('/surveys/ratings', [SurveyController::class, 'getAverageRatings']);

    // users
    $group->get('/getusers', AuthController::class . ':getUsers');


    // --- Event Routes ---
    $group->get('/events', EventController::class . ':index');
    $group->post('/events', EventController::class . ':createEvent');
    $group->get('/getevents', [EventController::class, 'getEvents']);
    $group->get('/events/{id}', EventController::class . ':show');
    $group->put('/events/{id}', EventController::class . ':update');
    $group->delete('/events/{id}', EventController::class . ':destroy');

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
        $organizerGroup->get('/vendors/{id}', VendorController::class . ':show');
        $organizerGroup->post('/vendors', VendorController::class . ':store');
        $organizerGroup->put('/vendors/{id}', VendorController::class . ':update');
        $organizerGroup->delete('/vendors/{id}', VendorController::class . ':destroy');
    });

    // --- Attendee Routes ---
    // $group->get('/attendees/event/{event_id}', AttendeesController::class . ':getAttendees');
    // $group->post('/attendees', AttendeesController::class . ':createAttendee');

    // // --- Attendance Routes ---
    // $group->post('/attendance/scan', AttendanceController::class . ':scanAttendance');
    // $group->get('/attendance/event/{event_id}', AttendanceController::class . ':getAttendance');

    // --- Vendor Routes ---
    // $group->get('/vendors', VendorController::class . ':index');
    // $group->get('/vendor-services', VendorController::class . ':getVendorServices');
    // $group->get('/vendors/{id}', VendorController::class . ':show');
    // $group->post('/vendors', VendorController::class . ':store');
    // $group->put('/vendors/{id}', VendorController::class . ':update');
    // $group->delete('/vendors/{id}', VendorController::class . ':destroy');

    // --- Venue Routes ---
    // $group->get('/venues', VenueController::class . ':index');
    // $group->get('/venues', VenueController::class . ':getVenues');
    // $group->get('/venues/{id}', VenueController::class . ':show');
    // $group->post('/venues', VenueController::class . ':store');
    // $group->put('/venues/{id}', VenueController::class . ':update');
    // $group->delete('/venues/{id}', VenueController::class . ':destroy');
    

         // List all venues for the authenticated venue owner
    $group->get('/venues', [VenueController::class, 'getMyVenues']);

    // Create a new venue
    $group->post('/venues', [VenueController::class, 'createVenue']);

    // Get a specific venue
    $group->get('/venues/{id:[0-9]+}', [VenueController::class, 'getVenue']);

    // Update a venue
    $group->post('/venues/{id:[0-9]+}', [VenueController::class, 'updateVenue']);

    // Delete a venue
    $group->delete('/venues/{id:[0-9]+}', [VenueController::class, 'deleteVenue']);


    // List all services for the authenticated vendor
    $group->get('/vendor-services', [VendorController::class, 'getMyServices']);

    // // Create a new service
    $group->post('/vendor-services', [VendorController::class, 'createService']);

    // // Get a specific service
    $group->get('/vendor-services/{id:[0-9]+}', [VendorController::class, 'getService']);

    // // Update a service
    $group->post('/vendor-services/{id:[0-9]+}', [VendorController::class, 'updateService']);

    // // Delete a service
    $group->delete('/vendor-services/{id:[0-9]+}', [VendorController::class, 'deleteService']);

    // --- Authenticated User Profile ---
    $group->get('/profile', AuthController::class . ':profile');

})->add(new AuthMiddleware(['vendor', 'organizer', 'venue_owner']));



// --- Venue Owner Specific Group (/owner) ---
$app->group('/owner', function () use ($app) {

    // Booking management
    $app->get('/bookings', VenueController::class . ':getOwnerBookings');
    $app->post('/bookings/{id}/approve', VenueController::class . ':approveBooking');
    $app->post('/bookings/{id}/reject', VenueController::class . ':rejectBooking');

    // Venue management for owner
    // $app->get('/venues', VenueController::class . ':getOwnerVenues');
    // $app->post('/venues', VenueController::class . ':addVenue');

})->add(new AuthMiddleware(['venue_owner']));

// --- Catch-all Root Route ---
$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write("Welcome to the Event Management API!");
    return $response;
});



// Run the Slim application
$app->run();
