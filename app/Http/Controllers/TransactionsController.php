<?php

namespace App\Http\Controllers;

use App\Models\MaidHourlyPrice;
use App\Models\MaidSchedule;
use App\Models\Transactions;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Midtrans\Notification;
use Midtrans\Snap;


class TransactionsController extends Controller
{
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
