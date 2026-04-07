<?php

namespace App\Http\Controllers\Api;

use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\TransactionStatusHistory;

class PaymentController extends Controller
{
    public function webhook(Request $request)
    {
        // Token Verifikasi dari Dashboard Xendit untuk keamanan (Opsional tapi sangat disarankan)
        $xenditToken = env('XENDIT_CALLBACK_TOKEN');
        if ($request->header('x-callback-token') !== $xenditToken) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        DB::beginTransaction();
        try {
            $external_id = $request->input('external_id');
            $status = $request->input('status'); // 'PAID', 'EXPIRED'

            $transaction = Transaction::where('transaction_code', $external_id)->first();

            if (!$transaction) {
                return response()->json(['message' => 'Transaction not found'], 404);
            }

            $payment = Payment::where('transaction_id', $transaction->id)->first();

            if ($status === 'PAID' && $payment->status === 'PENDING') {
                $payment->update(['status' => 'PAID']);

                $newStatus = 'Pembayaran lunas';
                $transaction->update(['status' => $newStatus]);

                TransactionStatusHistory::create([
                    'transaction_id' => $transaction->id,
                    'status' => $newStatus,
                    'changed_at' => now(),
                ]);
            } elseif ($status === 'EXPIRED') {
                $payment->update(['status' => 'EXPIRED']);

                $newStatus = 'Dibatalkan';
                $transaction->update([
                    'status' => $newStatus,
                    'is_final' => 'Sudah selesai'
                ]);

                TransactionStatusHistory::create([
                    'transaction_id' => $transaction->id,
                    'status' => $newStatus,
                    'changed_at' => now(),
                ]);

                // Opsional: Kembalikan stok produk jika expired
                // Logika pengembalian stok bisa ditempatkan di sini
            }

            DB::commit();
            return response()->json(['message' => 'Webhook received successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
