<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class MidtransController extends Controller
{
    public function callback(Request $request)
    {
        // Allow GET request to check endpoint health/reachability
        if ($request->isMethod('get')) {
            return response()->json([
                'status' => 'success',
                'message' => 'Midtrans callback endpoint is active'
            ], 200);
        }

        $serverKey = config('midtrans.server_key');
        $hashedKey = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashedKey !== $request->signature_key) {
            return response()->json([
                'message' => 'Invalid signature key'
            ], 403);
        }

        $orderId = $request->order_id;

        // Handle Midtrans Dashboard "Test notification URL" ping
        if (str_starts_with($orderId, 'payment_notif_test') || str_contains($orderId, 'test-')) {
            return response()->json([
                'status' => 'success',
                'message' => 'Test notification received successfully'
            ], 200);
        }

        $transactionStatus = $request->transaction_status;
        $transaction = Transaction::where('code', $orderId)->first();

        if (!$transaction) {
            return response()->json([
                'message' => 'Transaction not found'
            ], 404);
        }

        switch ($transactionStatus) {
            case 'capture':
                if ($request->payment_type == 'credit_card') {
                    if ($request->fraud_status == 'challenge') {
                        $transaction->update(['payment_status' => 'pending']);
                    } else {
                        $transaction->update(['payment_status' => 'paid']);
                        foreach ($transaction->passengers as $passenger) {
                            if ($passenger->seat) {
                                $passenger->seat->update(['is_available' => false]);
                            }
                        }
                    }
                }
                break;
            case 'settlement':
                $transaction->update(['payment_status' => 'paid']);
                foreach ($transaction->passengers as $passenger) {
                    if ($passenger->seat) {
                        $passenger->seat->update(['is_available' => false]);
                    }
                }
                break;
            case 'pending':
                $transaction->update(['payment_status' => 'pending']);
                break;
            case 'deny':
            case 'expire':
            case 'cancel':
            default:
                $transaction->update(['payment_status' => 'failed']);
                break;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Callback received successfully'
        ], 200);
    }
}
