<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\MasterServices;
use App\Models\Services;
use Illuminate\Http\Request;

class MaidServicesController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 10); // Set a default per_page value
        $search = $request->input('search', ''); // Get the search keyword from the request

        $query = MasterServices::query();

        // Apply search filter if a search term is provided
        if (!empty($search)) {
            $query->where('service_name', 'like', '%' . $search . '%');
        }

        // Paginate the results
        $services = $query->paginate($perPage);

        return ApiResponse::success($services, status: 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_name' => 'required|string',
        ]);

        $service = new MasterServices([
            'service_name' => $request->input('service_name'),
        ]);

        $service->save();

        return ApiResponse::success($service, message: 'Service created successfully', status: 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'service_name' => 'required|string',
        ]);

        $service = MasterServices::findOrFail($id);

        $service->service_name = $request->input('service_name');
        $service->save();

        return ApiResponse::success(message: 'Service updated successfully', status: 200);
    }

    public function destroy($id)
    {
        $service = MasterServices::findOrFail($id);
        $service->delete();

        return ApiResponse::success(message: 'Service deleted successfully', status: 200);
    }
}
