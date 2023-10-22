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
    public function createPayment(Request $request)
    {
        // Validate the request data
        $request->validate([
            'maid_id' => 'required|exists:users,id',
            'schedule_id' => 'required|exists:maid_schedules,id',
        ]);

        new MidtransService();

        $maidId = $request->input('maid_id');
        $hourlyPrice = MaidHourlyPrice::where('maid_id', $maidId)->value('price');

        if (!$hourlyPrice) {
            return response()->json(['message' => 'Maid hourly price not found'], 404);
        }

        $scheduledId = $request->input('schedule_id');

        $schedule = MaidSchedule::where('id', $scheduledId)->first();

        if (!$schedule) {
            return response()->json(['message' => 'Maid schedule not found'], 404);
        }

        $session = $schedule->session;
        $duration_value = $schedule->duration_value;
        $duration_unit = $schedule->duration_unit;

        if ($duration_unit == 'hours') {
            $duration_value =  1;
        }

        $amount = $hourlyPrice * $session * $duration_value;

        // Create a payment record in your database
        $payment = new Transactions();
        $payment->user_id = auth()->user()->id; // Assuming you're handling user authentication
        $payment->maid_id = $maidId;
        $payment->order_id = uniqid();
        $payment->schedule_id = $scheduledId;
        $payment->hourly_price = $hourlyPrice;
        $payment->amount = $amount;
        $payment->status = 'pending';

        // Create a Midtrans transaction request
        $params = [
            'transaction_details' => [
                'order_id' => $payment->order_id,
                'gross_amount' => $payment->amount,
            ],

        ];

        // Generate the payment link using Midtrans Snap
        $paymentLink = Snap::createTransaction($params)->redirect_url;

        $payment->payment_link = $paymentLink;
        $payment->save();


        // Return the payment link to the client
        return response()->json(['payment_link' => $paymentLink]);
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
