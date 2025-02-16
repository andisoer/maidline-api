<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\MaidSchedule;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

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

        try {
            DB::beginTransaction();

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

            $maidId =  $request->input('maid_id');

            // Create a new schedule record
            $schedule = new MaidSchedule();
            $schedule->user_id = $userId;
            $schedule->maid_id = $maidId;
            $schedule->start_date = $startDate;
            $schedule->end_date = date('Y-m-d H:i:s', $endDate); // Convert back to a MySQL datetime format
            $schedule->duration_value = $durationValue;
            $schedule->duration_unit = $durationUnit;
            $schedule->session = $request->input('session');
            $schedule->save();

            $paymentService = new PaymentService();

            $promoId = 0;
            if ($request->has('promo_id')) {
                $promoId = $request->input('promo_id');
            }

            $paymentLink = $paymentService->createPayment($maidId, $schedule->id, $promoId);
            if ($paymentLink == null) {
                return ApiResponse::error(message: 'Failed to create schedule', status: 401);

                DB::rollBack();
            }

            DB::commit();

            $data = [
                "schedule" => $schedule,
                "payment_link" => $paymentLink,
            ];

            return ApiResponse::success(data: $data, message: 'Schedule created successfully', status: 200);
        } catch (Throwable $e) {
            return ApiResponse::error(message: 'Failed to create schedule', status: 401);

            DB::rollBack();
        }
    }
}
