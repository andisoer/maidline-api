<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\MaidExperience;
use App\Models\User;
use Illuminate\Http\Request;

class MaidController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 10);

        $roleMaid = 3;

        $query = User::where('role_id', $roleMaid)->with(['services', 'hourlyPrice']);

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
        $maids = $query->paginate($perPage);

        return ApiResponse::success($maids, status: 200);
    }

    public function show($userId)
    {
        $maid = User::with(['services', 'experiences', 'hourlyPrice'])->find(($userId));

        if (!$maid) {
            return ApiResponse::error(message: 'Maid not found', status: 404);
        }

        return ApiResponse::success($maid, status: 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'gender' => 'required|in:male,female',
            'name' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust image validation rules as needed
            'about' => 'required',
            'experiences' => 'required',
            'services_ids' => 'required',
        ]);

        $imagePath = $request->file('image')->store('user_images', 'public');
        $fullUrl = asset('storage/'.$imagePath);

        // Create a new "Maid" user
        $maid = new User();
        $maid->name = $request->input('name');
        $maid->about = $request->input('about');
        $maid->gender = $request->input('gender');
        $maid->profile_picture = $fullUrl;
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
