<?php

namespace App\Controllers;

use App\Models\VendorService;
// use Slim\Http\Request;
// use Slim\Http\Response;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class VendorController
{
    private function json(Response $response, $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }

    public function createVendor(Request $request, Response $response)
    {
        // Logic to create a new vendor
    }

    public function getVendor(Request $request, Response $response, $args)
    {
        // Logic to retrieve a vendor by ID
    }

    public function updateVendor(Request $request, Response $response, $args)
    {
        // Logic to update an existing vendor
    }

    public function deleteVendor(Request $request, Response $response, $args)
    {
        // Logic to delete a vendor
    }

    public function getVendorServices(Request $request, Response $response)
    {
        // error_log("hitt");
        $user = $request->getAttribute('user');

        if ($user->role !== 'organizer') {
            return $this->json($response, ['message' => 'Unauthorized'], 403);
        }

        $services = VendorService::select('id','user_id', 'name', 'description', 'price')->get();

        return $this->json($response, $services, 200);
    }
}