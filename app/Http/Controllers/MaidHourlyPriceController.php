<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\MaidHourlyPrice;
use Illuminate\Http\Request;

class MaidHourlyPriceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'maid_id' => 'required|exists:users,id',
            'price' => 'required|numeric',
        ]);

        $maidHourlyPrice = new MaidHourlyPrice();
        $maidHourlyPrice->maid_id = $request->maid_id;
        $maidHourlyPrice->price = $request->price;
        $maidHourlyPrice->save();

        return ApiResponse::success(message: 'Maid hourly price stored successfully', status: 200);
    }
}
