<?php

// Only require bootstrap.php ONCE and assign its return value to $app
$app = require __DIR__ . '/../bootstrap.php';

use App\Controllers\AuthController;
use App\Controllers\EventController;
use App\Controllers\VendorController;
use App\Controllers\VenueController;
use App\Middleware\AuthMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


// --- Routes ---
// Public Routes (no authentication needed)

// It's good practice to define OPTIONS routes for specific paths if you need them.
// The CORS middleware *also* handles OPTIONS, but sometimes you might need specific
// route-level handling or for clarity.
$app->options('/register', function (Request $request, Response $response): Response {
    return $response->withStatus(200); // Or 204 No Content
});
$app->post('/register', AuthController::class . ':register');

$app->options('/login', function (Request $request, Response $response): Response {
    return $response->withStatus(200); // Or 204 No Content
});
$app->post('/login', AuthController::class . ':login');


// --- Authenticated Routes ---
// These routes will pass through the AuthMiddleware.
// The roles array in AuthMiddleware(['vendor', 'organizer', 'venue_owner']) specifies
// which roles are allowed to access routes within this group.

$app->group('/api', function ($group) {

    // You might also need OPTIONS routes here if clients initiate preflights for these
    // For example:
    // $group->options('/events', function (Request $request, Response $response): Response {
    //     return $response->withStatus(200);
    // });


    // --- Generic Event CRUD (accessible by all roles defined in the middleware) ---
    // These routes will handle creating, retrieving all, updating, and deleting events
    // that are *not* specific to an organizer's personal events.
    $group->get('/events', EventController::class . ':index'); // Get all events (might be filtered later)
    $group->post('/events', EventController::class . ':createEvent');
 // Create a new event
    $group->get('/events/{id}', EventController::class . ':show'); // Get a single event by ID
    $group->put('/events/{id}', EventController::class . ':update'); // Update an event by ID
    $group->delete('/events/{id}', EventController::class . ':destroy'); // Delete an event by ID


    // --- Organizer Scoped Events ---
    // This group is specifically for an organizer to manage *their own* events.
    // It's still within the /api group, so it inherits the parent AuthMiddleware.
    // No need to add AuthMiddleware again here, as it's already applied to the '/api' group.
    $group->group('/organizer', function ($organizerGroup) {
        // Example: Get all events created by the authenticated organizer
        // This expects AuthMiddleware to attach user info to the request,
        // which then EventController::getMyEvents can use to filter.
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

    // Auth profile route example
    $group->get('/profile', AuthController::class . ':profile');

})->add(new AuthMiddleware(['vendor', 'organizer', 'venue_owner'])); // Apply AuthMiddleware to the entire /api group


// ALWAYS ADD A CATCH-ALL ROUTE FOR THE ROOT if you expect to hit it
// This helps verify if the base setup is working.
$app->get('/', function (Request $request, Response $response, array $args) {
    $response->getBody()->write("Welcome to the Event Management API!");
    return $response;
});

// Run the Slim application
$app->run();