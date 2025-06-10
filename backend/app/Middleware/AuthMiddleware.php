<?php
namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User; // Make sure your User model is correctly namespaced and accessible

class AuthMiddleware
{
    private $allowedRoles;

    public function __construct(array $allowedRoles = [])
    {
        $this->allowedRoles = $allowedRoles;
    }

    // Helper function for consistent JSON responses
    private function jsonResponse(Response $response, $data, int $status = 200): Response
    {
        $json = json_encode($data);
        $response->getBody()->write($json);
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $authorizationHeader = $request->getHeaderLine('Authorization');
        $jwtSecret = $_ENV['JWT_SECRET'] ?? 'a_super_secret_fallback_key'; // Added fallback

        // Create a new response object to work with
        $response = new \Slim\Psr7\Response();

        if (!$authorizationHeader) {
            return $this->jsonResponse($response, ['message' => 'Authentication required'], 401);
        }

        list($type, $token) = explode(' ', $authorizationHeader, 2);

        if (strtolower($type) !== 'bearer') {
            return $this->jsonResponse($response, ['message' => 'Invalid token type'], 401);
        }

        try {
            // Ensure Key is imported and correct secret/algorithm are used
            $decoded = JWT::decode($token, new Key($jwtSecret, 'HS256'));

            // Fetch user from DB to ensure they still exist and role is current
            $user = User::find($decoded->user_id);

            if (!$user) {
                return $this->jsonResponse($response, ['message' => 'User not found'], 401);
            }

            // Check if the user's role is allowed for this route
            // Make sure the role names in DB match the ones passed to middleware exactly
            if (!empty($this->allowedRoles) && !in_array($user->role, $this->allowedRoles)) {
                return $this->jsonResponse($response, ['message' => 'Access forbidden for this role'], 403);
            }

            // Add user to the request attributes for controllers to use later
            $request = $request->withAttribute('user', $user);

        } catch (\Firebase\JWT\ExpiredException $e) {
            return $this->jsonResponse($response, ['message' => 'Token has expired'], 401);
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            return $this->jsonResponse($response, ['message' => 'Invalid token signature'], 401);
        } catch (\UnexpectedValueException $e) { // Catches errors like malformed JWT
            return $this->jsonResponse($response, ['message' => 'Invalid token format'], 401);
        } catch (\Exception $e) {
            // General catch-all for other JWT or processing errors
            error_log("AuthMiddleware Error: " . $e->getMessage()); // Log detailed error
            return $this->jsonResponse($response, ['message' => 'Unauthorized: ' . $e->getMessage()], 401);
        }

        // If everything passes, hand over to the next middleware or route handler
        return $handler->handle($request);
    }
}