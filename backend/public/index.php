<?php

// Bootstrap and app initialization
$app = require __DIR__ . '/../bootstrap.php';

use App\Controllers\AuthController;
use App\Controllers\EventController;
use App\Controllers\VendorController;
use App\Controllers\VenueController;
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

// --- Authenticated Routes Group (/api) ---
$app->group('/api', function ($group) {

    // --- Event Routes ---
    $group->get('/events', EventController::class . ':index');
    $group->post('/events', EventController::class . ':createEvent');
    $group->get('/getevents', [EventController::class, 'getEvents']);
    $group->get('/events/{id}', EventController::class . ':show');
    $group->put('/events/{id}', EventController::class . ':update');
    $group->delete('/events/{id}', EventController::class . ':destroy');

    // Organizer-scoped events
    $group->group('/organizer', function ($organizerGroup) {
        $organizerGroup->get('/events', EventController::class . ':getMyEvents');
    });

    // --- Vendor Routes ---
    $group->get('/vendors', VendorController::class . ':index');
    $group->get('/vendors/{id}', VendorController::class . ':show');
    $group->post('/vendors', VendorController::class . ':store');
    $group->put('/vendors/{id}', VendorController::class . ':update');
    $group->delete('/vendors/{id}', VendorController::class . ':destroy');

    // --- Venue Routes ---
    $group->get('/venues', VenueController::class . ':index');
    $group->get('/venues/{id}', VenueController::class . ':show');
    $group->post('/venues', VenueController::class . ':store');
    $group->put('/venues/{id}', VenueController::class . ':update');
    $group->delete('/venues/{id}', VenueController::class . ':destroy');

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
    $app->get('/venues', VenueController::class . ':getOwnerVenues');
    $app->post('/venues', VenueController::class . ':addVenue');

})->add(new AuthMiddleware(['venue_owner']));

// --- Catch-all Root Route ---
$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write("Welcome to the Event Management API!");
    return $response;
});



// Run the Slim application
$app->run();
