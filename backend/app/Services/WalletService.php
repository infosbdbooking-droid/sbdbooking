<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class WalletService
{
    /**
     * Get the user's wallet, or create one if it doesn't exist.
     */
    public function getOrCreateWallet(User $user): Wallet
    {
        return DB::transaction(function () use ($user) {
            $wallet = Wallet::where('user_id', $user->id)->first();
            if (!$wallet) {
                $wallet = Wallet::create([
                    'user_id' => $user->id,
                    'balance' => 0.00,
                    'currency' => 'INR'
                ]);
            }
            return $wallet;
        });
    }

    /**
     * Credit funds to a user's wallet.
     */
    public function credit(User $user, float $amount, string $transactionType, $reference = null, ?string $description = null): WalletTransaction
    {
        return DB::transaction(function () use ($user, $amount, $transactionType, $reference, $description) {
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
            if (!$wallet) {
                $wallet = Wallet::create([
                    'user_id' => $user->id,
                    'balance' => 0.00,
                    'currency' => 'INR'
                ]);
            }

            // Update balance
            $wallet->balance += $amount;
            $wallet->save();

            // Record transaction
            return WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'amount' => $amount,
                'type' => 'credit',
                'transaction_type' => $transactionType,
                'reference_id' => $reference ? $reference->id : null,
                'reference_type' => $reference ? get_class($reference) : null,
                'description' => $description
            ]);
        });
    }

    /**
     * Debit funds from a user's wallet.
     */
    public function debit(User $user, float $amount, string $transactionType, $reference = null, ?string $description = null): WalletTransaction
    {
        return DB::transaction(function () use ($user, $amount, $transactionType, $reference, $description) {
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
            if (!$wallet) {
                $wallet = Wallet::create([
                    'user_id' => $user->id,
                    'balance' => 0.00,
                    'currency' => 'INR'
                ]);
            }

            // Update balance
            $wallet->balance -= $amount;
            $wallet->save();

            // Record transaction
            return WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'amount' => $amount,
                'type' => 'debit',
                'transaction_type' => $transactionType,
                'reference_id' => $reference ? $reference->id : null,
                'reference_type' => $reference ? get_class($reference) : null,
                'description' => $description
            ]);
        });
    }
}
