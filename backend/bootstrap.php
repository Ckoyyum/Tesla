<?php
require __DIR__ . '/vendor/autoload.php'; // Ensure autoloader is at the very top

use Dotenv\Dotenv;
use Illuminate\Database\Capsule\Manager as Capsule;
use Slim\Factory\AppFactory; // Make sure this is present
use Slim\Exception\HttpBadRequestException; // For custom error handling
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

// Load environment variables (usually needs to be before anything relies on them)
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Eloquent ORM Setup
$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => $_ENV['DB_HOST'],
    'port'      => $_ENV['DB_PORT'],
    'database'  => $_ENV['DB_DATABASE'],
    'username'  => $_ENV['DB_USERNAME'],
    'password'  => $_ENV['DB_PASSWORD'],
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix'    => '',
]);

// error_log("DB HOST: " . ($_ENV['DB_HOST'] ?? 'NOT SET'));
// error_log("DB DATABASE: " . ($_ENV['DB_DATABASE'] ?? 'NOT SET'));
// error_log("DB USERNAME: " . ($_ENV['DB_USERNAME'] ?? 'NOT SET'));
// error_log("DB PASSWORD: " . ($_ENV['DB_PASSWORD'] ?? 'NOT SET'));
$capsule->setAsGlobal();
$capsule->bootEloquent();

// Slim App Setup - THIS MUST BE BEFORE YOU USE $app
$app = AppFactory::create(); // <--- This line is crucial and needs to be here!

// Add the Body Parsing Middleware explicitly (from previous solution)
$app->addBodyParsingMiddleware();

// IMPORTANT: For `php -S localhost:8000 -t public`, this should be empty.
$app->setBasePath(''); // This line will now work because $app is defined

// -------------------------------------------------------------
// IMPORTANT: CORS Middleware should generally be the FIRST middleware added.
// It needs to handle the OPTIONS preflight request before any routing or
// other middleware can interfere or produce errors.
// -------------------------------------------------------------
$app->add(new class implements MiddlewareInterface {
    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $response = $handler->handle($request);

        // Allow from your Vue.js development server
        $origin = 'http://localhost:8080';

        // Set common CORS headers for all responses
        $response = $response
            ->withHeader('Access-Control-Allow-Origin', $origin)
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Credentials', 'true'); // Required if you use cookies/sessions

        // Handle preflight OPTIONS requests
        // If it's an OPTIONS request, simply return the response with CORS headers
        // as no actual resource is requested.
        if ($request->getMethod() === 'OPTIONS') {
            return $response->withStatus(200); // Or 204 No Content, 200 is fine too.
        }

        return $response;
    }
});

// Add Error Middleware (important for development)
// This should be added *after* the CORS middleware if you want CORS headers
// to be present even during errors.
$errorMiddleware = $app->addErrorMiddleware(
    true,  // Display error details
    true,  // Log errors
    true   // Log error details
);

// Custom Error Handler to return JSON errors for API requests
$errorMiddleware->setDefaultErrorHandler(function (
    Request $request,
    Throwable $exception,
    bool $displayErrorDetails,
    bool $logErrors,
    bool $logErrorDetails
) use ($app): Response {
    $payload = [
        'error' => 'An error occurred.',
        'message' => $exception->getMessage(), // Primary error message
    ];
    $statusCode = 500; // Default status code

    // Handle specific HTTP exceptions
    if ($exception instanceof HttpBadRequestException) {
        $statusCode = 400;
        $payload['error'] = 'Bad Request';
    }
    // You can add more specific error handling here for other types of exceptions
    // e.g., if ($exception instanceof HttpNotFoundException) { $statusCode = 404; $payload['error'] = 'Not Found'; }

    // If displayErrorDetails is true, include the full stack trace and details
    if ($displayErrorDetails) {
        $payload['file'] = $exception->getFile();
        $payload['line'] = $exception->getLine();
        $payload['trace'] = $exception->getTraceAsString();
    }

    $response = $app->getResponseFactory()->createResponse($statusCode);
    $response->getBody()->write(json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/db-check', function (Request $request, Response $response) {
    try {
        $pdo = \Illuminate\Database\Capsule\Manager::connection()->getPdo();
        $message = "✅ Connected to database: " . $pdo->query('select database()')->fetchColumn();
    } catch (\Exception $e) {
        $message = "❌ DB Connection error: " . $e->getMessage();
    }

    $response->getBody()->write($message);
    return $response;
});

return $app;