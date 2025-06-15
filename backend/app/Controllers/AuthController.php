<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\User;
use Firebase\JWT\JWT;
use Dotenv\Dotenv; // Add this if you haven't loaded Dotenv globally


class AuthController
{
    private $jwtSecret;

    public function __construct()
    {
        // Ensure Dotenv is loaded if not already done in bootstrap.php
        // This makes sure $_ENV is populated even if a route is accessed directly for testing.
        if (empty($_ENV['JWT_SECRET'])) {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../../'); // Adjust path as needed
            $dotenv->load();
        }
        $this->jwtSecret = $_ENV['JWT_SECRET'] ?? 'your_super_secret_jwt_key_please_change_me'; // Use a strong secret!
    }

    // Helper function for consistent JSON responses
    private function jsonResponse(Response $response, array $data, int $status = 200): Response
    {
        $json = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); // Nicer output for debugging
        $response->getBody()->write($json);
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }

    public function register(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody(); // Should work with addBodyParsingMiddleware()

        // Debugging line: Check what data is actually received by the backend
        error_log("Received data for registration: " . print_r($data, true));

        // --- Input Validation ---
        // 1. Check for missing fields
        $requiredFields = ['username', 'email', 'password', 'role'];
        $missingFields = [];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            return $this->jsonResponse(
                $response,
                ['message' => 'Missing or empty required fields: ' . implode(', ', $missingFields)],
                400
            );
        }

        // Assign sanitized data (optional but good practice)
        $username = trim($data['username']);
        $email = trim($data['email']);
        $password = $data['password']; // Password won't be trimmed
        $role = trim($data['role']);

        // 2. Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->jsonResponse($response, ['message' => 'Invalid email format'], 400);
        }     

        // 3. Validate role against allowed values
        $allowedRoles = ['vendor', 'organizer', 'venue_owner'];
        if (!in_array($role, $allowedRoles)) {
            return $this->jsonResponse($response, ['message' => 'Invalid role selected.'], 400);
        }

        // 4. Basic password strength (add more robust rules if needed)
        if (strlen($password) < 6) { // Example: minimum 6 characters
            return $this->jsonResponse($response, ['message' => 'Password must be at least 6 characters long.'], 400);
        }
        
        $users = User::all(); // Get all users
        error_log("All users: " . json_encode($users));


        // --- Check for existing user ---
        // Use separate queries for better error messaging
        if (User::where('username', $username)->first()) {
            return $this->jsonResponse($response, ['message' => 'Username already taken.'], 409); // 409 Conflict
        }
        if (User::where('email', $email)->first()) {
            return $this->jsonResponse($response, ['message' => 'Email address already registered.'], 409); // 409 Conflict
        }

            error_log("hit there");
        

        // --- Create new user ---
        try {
            $user = new User();
            $user->username = $username;
            $user->email = $email;
            $user->password = password_hash($password, PASSWORD_DEFAULT); // Hash the password
            $user->role = $role;
            $user->save(); // Save the user to the database
        
            error_log("hit here");

            // Generate JWT after successful registration
            $payload = [
                'iss' => 'your_app',           // Issuer
                'aud' => 'your_app_users',     // Audience
                'iat' => time(),               // Issued at
                'exp' => time() + (3600 * 24), // Expiration time (24 hours)
                'user_id' => $user->id,
                'role' => $user->role,
            ];

            $jwt = JWT::encode($payload, $this->jwtSecret, 'HS256');

            // Return success response with token and user details
            return $this->jsonResponse($response, [
                'message' => 'User registered successfully!',
                'token' => $jwt,
                'user' => [ // Return user details for frontend state
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'role' => $user->role,
                    // Do NOT return hashed password or sensitive info
                ]
            ], 201); // 201 Created status
        } catch (\Exception $e) {
            // Catch any database or other unexpected exceptions
            error_log("Registration Database Error: " . $e->getMessage() . " on line " . $e->getLine() . " in " . $e->getFile());
            return $this->jsonResponse($response, ['message' => 'Registration failed due to a server error. Please try again later.'], 500);
        }
    }

    // Login Method (no significant changes needed here, as it follows similar logic)
    public function login(Request $request, Response $response): Response
    {
        error_log("Login endpoint hit");

        $body = $request->getBody()->getContents();
        error_log($body);
        $data = json_decode($body, true); // true = associative array

        if (!isset($data['email'], $data['password']) || trim($data['email']) === '' || trim($data['password']) === '') {
            return $this->jsonResponse($response, ['message' => 'Email and password are required.'], 400);
        }
        // $data = $request->getParsedBody();

        // if (!isset($data['email'], $data['password']) || trim($data['email']) === '' || trim($data['password']) === '') {
        //     return $this->jsonResponse($response, ['message' => 'Email and password are required.'], 400);
        // }

        $email = trim($data['email']);
        $password = $data['password'];

        $user = User::where('email', $email)->first();

        if (!$user || !password_verify($password, $user->password)) {
            return $this->jsonResponse($response, ['message' => 'Invalid email or password.'], 401);
        }

        $payload = [
            'iss' => 'your_app',
            'aud' => 'your_app_users',
            'iat' => time(),
            'exp' => time() + (3600 * 24), // Token valid for 24 hours
            'user_id' => $user->id,
            'role' => $user->role,
        ];

        $jwt = JWT::encode($payload, $this->jwtSecret, 'HS256');

        return $this->jsonResponse($response, [
            'message' => 'Login successful',
            'token' => $jwt,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
            ]
        ], 200);
    }

    // Profile Method (unchanged)
    public function profile(Request $request, Response $response): Response
    {
        $user = $request->getAttribute('user'); // User object from AuthMiddleware

        if (!$user) {
            return $this->jsonResponse($response, ['message' => 'User not authenticated or found in request context'], 401);
        }

        return $this->jsonResponse($response, [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $user->role,
        ], 200);
    }

    public function getUsers(Request $request, Response $response)
    {
        // $users = User::all(); // Gets all fields for all users

        $users = User::all()->toArray();


        return $this->jsonResponse($response, $users, 200);
    }
}