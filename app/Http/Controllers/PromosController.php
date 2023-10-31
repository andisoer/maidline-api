<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\Promos;
use Illuminate\Http\Request;

class PromosController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 10);

        $promos = Promos::paginate($perPage); // Pagination with 10 items per page
        return ApiResponse::success(data: $promos, status: 200);
    }

    public function store(Request $request)
    {

        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'discount_percentage' => 'required|integer',
            'valid_from' => 'required|date_format:Y-m-d',
            'valid_to' => 'required|date_format:Y-m-d',
        ]);

        $promo = Promos::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'discount_percentage' => $request->input('discount_percentage'),
            'valid_from' => $request->input('valid_from'),
            'valid_to' => $request->input('valid_to'),
        ]);

        $promo->save();

        return ApiResponse::success(message: 'Promo added successfully', status: 200);
    }
}
