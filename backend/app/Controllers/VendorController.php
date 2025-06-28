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

    // public function createVendor(Request $request, Response $response)
    // {
    //     // Logic to create a new vendor
    // }

    // public function getVendor(Request $request, Response $response, $args)
    // {
    //     // Logic to retrieve a vendor by ID
    // }

    // public function updateVendor(Request $request, Response $response, $args)
    // {
    //     // Logic to update an existing vendor
    // }

    // public function deleteVendor(Request $request, Response $response, $args)
    // {
    //     // Logic to delete a vendor
    // }

    public function getVendorServices(Request $request, Response $response)
    {
        // error_log("hitt");
        $user = $request->getAttribute('user');

        // if ($user->role !== 'vendor') {
        //     return $this->json($response, ['message' => 'Unauthorized'], 403);
        // }

        $services = VendorService::select('id','user_id', 'name', 'description', 'price')->get();

        return $this->json($response, $services, 200);
    }

    /* ---------- LIST for vendor ---------- */
    public function getMyServices(Request $request, Response $response)
    {
        $user = $request->getAttribute('user');

        if ($user->role !== 'vendor') {
            return $this->json($response, ['message' => 'Unauthorized'], 403);
        }

        $services = VendorService::where('user_id', $user->id)
            ->orderBy('name', 'asc')
            ->get();

        return $this->json($response, $services);
    }

    /* ---------- CREATE ---------- */
    public function createService(Request $request, Response $response)
    {
        $user = $request->getAttribute('user');
        if ($user->role !== 'vendor') {
            return $this->json($response, ['message' => 'Unauthorized'], 403);
        }

        $data = $request->getParsedBody();

        foreach (['name', 'price'] as $field) {
            if (empty($data[$field])) {
                return $this->json($response, ["message" => "$field is required"], 422);
            }
        }

        $service = VendorService::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
        ]);

        return $this->json($response, $service, 201);
    }

    /* ---------- READ ---------- */
    public function getService(Request $request, Response $response, array $args)
    {
        $service = VendorService::find($args['id']);
        if (!$service) {
            return $this->json($response, ['message' => 'Not found'], 404);
        }
        return $this->json($response, $service);
    }

    /* ---------- UPDATE ---------- */
    public function updateService(Request $request, Response $response, array $args)
    {
        $user = $request->getAttribute('user');
        $service = VendorService::find($args['id']);

        if (!$service) {
            return $this->json($response, ['message' => 'Not found'], 404);
        }
        if ($service->user_id !== $user->id) {
            return $this->json($response, ['message' => 'Forbidden'], 403);
        }

        $data = $request->getParsedBody();

        $service->fill([
            'name' => $data['name'] ?? $service->name,
            'description' => $data['description'] ?? $service->description,
            'price' => $data['price'] ?? $service->price,
        ]);
        $service->save();

        return $this->json($response, $service);
    }

    /* ---------- DELETE ---------- */
    public function deleteService(Request $request, Response $response, array $args)
    {
        $user = $request->getAttribute('user');
        $service = VendorService::find($args['id']);

        if (!$service) {
            return $this->json($response, ['message' => 'Not found'], 404);
        }
        if ($service->user_id !== $user->id) {
            return $this->json($response, ['message' => 'Forbidden'], 403);
        }

        $service->delete();
        return $this->json($response, ['message' => 'Deleted'], 200);
    }

}