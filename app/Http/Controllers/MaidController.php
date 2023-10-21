<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;

class MaidController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 10);
        $search = $request->input('search', ''); // Get the search keyword from the request

        $maids = User::where('role_id', 'maid')
            ->with(['services', 'experiences'])
            ->paginate($perPage);

        return ApiResponse::success($maids, status: 200);
    }
}
