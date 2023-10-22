<?php

namespace App\Services;

use App\Models\MaidHourlyPrice;
use App\Models\MaidSchedule;
use App\Models\Transactions;
use Midtrans\Snap;
use Throwable;

class PaymentService
{
    public function createPayment($maidId, $scheduledId)
    {
        try {
            new MidtransService();

            $hourlyPrice = MaidHourlyPrice::where('maid_id', $maidId)->value('price');

            if (!$hourlyPrice) {
                return 'Maid hourly price not found';
            }

            $schedule = MaidSchedule::where('id', $scheduledId)->first();

            if (!$schedule) {
                return 'Maid schedule not found';
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
            return $paymentLink;
        } catch (Throwable $e) {
            return null;
        }
    }
}
