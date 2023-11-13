<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Address;
use App\Helpers\ApiResponse;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 10);

        $userId = auth()->user()->id;

        $query = Address::where('user_id', auth()->user()->id);

        $address = $query->paginate($perPage);

        return ApiResponse::success($address, status: 200);
    }

    public function store(Request $request)
    {
        // Validate input
        $validatedData = $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'province_state_postal_code' => 'required|string',
            'street_name_house_number' => 'required|string',
            'address_detail' => 'nullable|string',
            'tag' => 'required|in:home,office',
            'is_main' => 'required|integer|in:0,1',
        ]);

        // If the submitted address is marked as main, update other addresses for the same user
        if ($validatedData['is_main'] == 1) {
            // Set is_main to 0 for other addresses of the same user
            Address::where('user_id', auth()->user()->id)->update(['is_main' => 0]);
        }
        
        // Create the address
        $address = Address::create([
            'user_id' => auth()->user()->id, // Assuming you're using authentication
            'name' => $validatedData['name'],
            'phone' => $validatedData['phone'],
            'province_state_postal_code' => $validatedData['province_state_postal_code'],
            'street_name_house_number' => $validatedData['street_name_house_number'],
            'tag' => $validatedData['tag'],
            'is_main' => $validatedData['is_main'],
        ]);

        if ($request->has('address_detail')) {
            $address->address_detail = $validatedData['address_detail'];
            $address->save();
        }

        return ApiResponse::success(data: $address, message: 'Address created successfully', status: 200);
    }

    public function update(Request $request, $addressId)
    {
        // Validate input
        $validatedData = $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'province_state_postal_code' => 'required|string',
            'street_name_house_number' => 'required|string',
            'address_detail' => 'nullable|string',
            'tag' => 'required|in:home,office',
            'is_main' => 'required|integer|in:0,1',
        ]);

        // Find the address by ID
        $address = Address::findOrFail($addressId);

        // Check if the user is authorized to update this address (optional)
        // You might want to implement your own authorization logic based on your requirements.

        // If the submitted data includes 'is_main' and it is set to true,
        // update other addresses for the same user to set 'is_main' to false
        Address::where('user_id', auth()->user()->id)->update(['is_main' => 0]);

        $address->update($validatedData);       

        if ($request->has('address_detail')) {
            $address->address_detail = $validatedData['address_detail'];
            $address->save();
        }

        return ApiResponse::success(data: $address, message: 'Address created successfully', status: 200);
    }
}
