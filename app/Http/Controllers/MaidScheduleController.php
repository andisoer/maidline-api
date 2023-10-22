<?php

namespace App\Http\Controllers;

use App\Models\MaidSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaidScheduleController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'maid_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'duration_value' => 'required|integer', // Numerical value of duration
            'duration_unit' => 'required|in:hours,days', // Unit of duration
            'session' => 'required|integer',
        ]);

        $userId = Auth::user()->id;

        // Calculate the end date based on the duration
        $startDate = $request->input('start_date');
        $durationValue = $request->input('duration_value');
        $durationUnit = $request->input('duration_unit');

        if ($durationUnit === 'days') {
            $endDate = strtotime($startDate . ' +' . $durationValue . ' days');
        }

        if ($durationUnit === 'hours') {
            $endDate = strtotime($startDate . ' +0 days');
        }
        // Create a new schedule record
        $schedule = new MaidSchedule();
        $schedule->user_id = $userId;
        $schedule->maid_id = $request->input('maid_id');
        $schedule->start_date = $startDate;
        $schedule->end_date = date('Y-m-d H:i:s', $endDate); // Convert back to a MySQL datetime format
        $schedule->duration_value = $durationValue;
        $schedule->duration_unit = $durationUnit;
        $schedule->session = $request->input('session');
        $schedule->save();

        return response()->json(['message' => 'Schedule created successfully']);
    }
}
