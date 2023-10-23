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

        $roleMaid = 3;

        $query = User::where('role_id', $roleMaid)->with(['services']);

        // Filter by gender if the parameter is provided
        if ($request->has('gender')) {
            $gender = $request->input('gender');
            $query->where('gender', $gender);
        }

        // Filter by service name from the pivot table
        if ($request->has('service_name')) {
            $serviceName = $request->input('service_name');
            $query->whereHas('services', function ($q) use ($serviceName) {
                $q->where('service_name', 'like', '%' . $serviceName . '%');
            });
        }

        // Filter by schedule clashes
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $query->whereDoesntHave('schedules', function ($q) use ($startDate, $endDate) {
                $q->where(function ($q) use ($startDate, $endDate) {
                    $q->where('start_date', '<', $endDate)
                        ->where('end_date', '>', $startDate);
                });
            });
        }

        // Paginate the result
        $perPage = $request->input('per_page', 10); // You can specify the default per page value
        $maids = $query->paginate($perPage);

        return ApiResponse::success($maids, status: 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust image validation rules as needed
            'about' => 'required',
            'experiences' => 'required',
            'services_ids' => 'required',
        ]);

        $imagePath = $request->file('image')->store('user_images', 'public');

        // Create a new "Maid" user
        $maid = new User();
        $maid->name = $request->input('name');
        $maid->about = $request->input('about');
        $maid->profile_picture = $imagePath;
        $maid->email = $request->input('email');
        $maid->password = bcrypt('maidline2023'); // Set the default password
        $maid->role_id = 3; // Assuming "Maid" role has the ID 3
        $maid->save();

        // Add experiences
        $experiencesData = json_decode($request->input('experiences'));

        $experiences = [];

        foreach ($experiencesData as $experienceData) {
            $experience = new MaidExperience();
            $experience->description = $experienceData->name;
            $experience->maid_id = $maid->id;
            $experience->save();
            $experiences[] = $experience;
        }

        // Add Services
        $serviceIds = json_decode($request->input('services_ids'));
        $maid->services()->sync($serviceIds);

        return ApiResponse::success(message: 'Maid added successfully', status: 200);
    }
}
