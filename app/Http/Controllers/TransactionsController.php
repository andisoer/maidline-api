<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\MaidHourlyPrice;
use App\Models\MaidSchedule;
use App\Models\Transactions;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Midtrans\Notification;
use Midtrans\Snap;

class TransactionsController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 10);

        $userId = Auth::user()->id;

        $query = Transactions::where('user_id', $userId)->with(['maid']);

        if ($request->has('service_id')) {
            $serviceId = $request->input('service_id');
            $query->whereHas('maid',  function ($query) use ($serviceId) {
                $query->whereHas('services', function ($q) use ($serviceId) {
                    $q->where('service_id', $serviceId);
                });
            });
        }

        $transaction = $query->paginate($perPage);

        return ApiResponse::success($transaction, status: 200);
    }

    public function show($transactionId)
    {
        $transaction = Transactions::with(['user', 'maid.services', 'schedule'])->find(($transactionId));

        if (!$transaction) {
            return ApiResponse::error(message: 'Transaction not found', status: 404);
        }

        return ApiResponse::success($transaction, status: 200);
    }

    // Callback handler for Midtrans
    public function handleCallback(Request $request)
    {
        new MidtransService();

        // Verify the callback signature
        $notification = json_decode($request->getContent());

        // Verify the callback
        if ($notification) {
            // Payment is valid, update your database or perform other actions
            $orderId = $notification->order_id;
            $paymentStatus = $notification->transaction_status;

            // Example: Update your payment record in the database
            $payment = Transactions::where('order_id', $orderId)->first();
            if ($payment) {
                $payment->status = $paymentStatus;
                $payment->save();
            }

            // You can also perform additional actions based on the payment status
            if ($paymentStatus === 'capture') {
                // Payment has been successfully captured
            } elseif ($paymentStatus === 'settlement') {
                // Payment has been settled
            } elseif ($paymentStatus === 'deny') {
                // Payment has been denied
            }

            return response(['message' => 'Payment callback received and processed'], 200);
        } else {
            // Invalid payment, handle the error as needed
            return response(['message' => 'Invalid payment callback'], 400);
        }
    }
}
