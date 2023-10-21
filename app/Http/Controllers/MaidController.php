<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\MaidExperience;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;
use Throwable;

class MaidController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 10);
        $search = $request->input('search', ''); // Get the search keyword from the request

        $roleMaid = 3;

        $maids = User::where('role_id', $roleMaid)
            ->with(['services'])
            ->paginate($perPage);

        return ApiResponse::success($maids, status: 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required',
            'about' => 'required',
            'experiences' => 'required|array|min:1',
            'services_ids' => 'required|array|min:1',
        ]);

        // Create a new "Maid" user
        $maid = new User();
        $maid->name = $request->input('name');
        $maid->about = $request->input('about');
        $maid->email = $request->input('email');
        $maid->password = bcrypt('maidline2023'); // Set the default password
        $maid->role_id = 3; // Assuming "Maid" role has the ID 3
        $maid->save();

        // Add experiences
        $experiencesData = $request->input('experiences');
        $experiences = [];

        foreach ($experiencesData as $experienceData) {
            $experience = new MaidExperience();
            $experience->description = $experienceData['name'];
            $experience->maid_id = $maid->id;
            $experience->save();
            $experiences[] = $experience;
        }

        // Add Services
        $serviceIds = $request->input('services_ids');
        $maid->services()->sync($serviceIds);

        return ApiResponse::success(message: 'Maid added successfully', status: 200);
    }
}
