<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferRequest;
use App\Http\Requests\WalletFundingRequest;
use App\Http\Requests\WalletRequest;
use App\Models\Transactions;
use App\Models\Wallets;
use App\Models\WalletTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{    
    /**
     * Get authenticated user wallets.
     *
     * @return Response
     */
    public function getUserWallets()
    {
        $wallets = Wallets::where('user_id', Auth::id())->get();

        return response()->json([
            'status'  => true,
            'message' => 'Wallets fetched successfully',
            'data'    => $wallets
        ], 200);
    }
    
    /**
     * Create new wallet.
     *
     * @param  mixed $request
     * @return Response
     */
    public function createNewWallet(WalletRequest $request)
    {
        $wallet                         = new Wallets;
        $wallet->wallet_code            = rand(1111111111, 9999999999);
        $wallet->wallet_name            = $request->wallet_name;
        $wallet->wallet_type            = $request->wallet_type;
        $wallet->monthly_interest_rate  = $request->interest_rate;
        $wallet->wallet_minimum_balance = $request->wallet_minimum_balance;
        $wallet->user_id                = Auth::id();
        $wallet->save();

        return response()->json([
            'status'  => true,
            'message' => 'Wallet created successfully',
            'data'    => $wallet
        ], 201);
    }
    
    /**
     * Get single wallet details
     *
     * @param  mixed $id
     * @return Response
     */
    public function getSingleWallet($id)
    {
        $wallet = Wallets::find($id);
        $wallet->user;
        $wallet->transactions;

        return response()->json([
            'status'  => true,
            'message' => 'Wallet details fetched successfully',
            'data'    => $wallet,
        ], 200);
    }
    
    /**
     * Get single wallet transactions
     *
     * @param  mixed $id
     * @return Response
     */
    public function getWalletTransactions($id)
    {
        $transactions = WalletTransactions::where('wallet_id', $id)->latest()->get();

        return response()->json([
            'status'  => true,
            'message' => 'Transactions fetched successfully',
            'data'    => $transactions
        ], 200);
    }
    
    /**
     * Fund wallet
     *
     * @param  mixed $request
     * @return Response
     */
    public function fundWallet(WalletFundingRequest $request)
    {
        //Check if wallet code exist
        $wallet = Wallets::where('wallet_code', $request->wallet_code)->first();

        if (!isset($wallet)) {
            return response()->json([
                'status'  => false,
                'message' => 'Wallet not found or invalid wallet code'
            ], 404);
        }

        //Credit wallet balance
        $wallet->wallet_balance += $request->amount;
        $wallet->save();

        //Add wallet transaction
        $walletTransaction                          = new WalletTransactions;
        $walletTransaction->transaction_type        = 'credit';
        $walletTransaction->transaction_amount      = $request->amount;
        $walletTransaction->transaction_description = 'Wallet funding';
        $walletTransaction->wallet_id               = $wallet->id;
        $walletTransaction->user_id                 = Auth::id();
        $walletTransaction->save();
        
        return response()->json([
            'status'  => true,
            'message' => 'Wallet funded successfully'
        ], 200);
    }
    
    /**
     * Transfer to another wallet
     *
     * @param  mixed $request
     * @return Response
     */
    public function transferToWallet(TransferRequest $request)
    {
        //Begin database transaction
        DB::beginTransaction();

            //Check if sender wallet exist and lock wallet to prevent concurrent attack
            $sender = Wallets::where('wallet_code', $request->sender_wallet_code)->lockForUpdate()->first();

            if (!isset($sender)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid sender wallet code'
                ], 400);
            }

            //Check if receiver wallet exist and lock wallet to prevent concurrent attack
            $receiver = Wallets::where('wallet_code', $request->recipient_wallet_code)->lockForUpdate()->first();

            if (!isset($receiver)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid receiver wallet code'
                ], 400);
            }

            //Check if request amount is valid
            if ($request->amount < 0) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Amount must be greater than 0'
                ], 400);
            }

            //Check if sender wallet have a sufficient balance
            if ($sender->wallet_balance < $request->amount) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Insufficient wallet balance'
                ], 400);
            }

            //Deduct from sender wallet
            $sender->wallet_balance -= $request->amount;
            $sender->save();

            //Credit receiver wallet
            $receiver->wallet_balance += $request->amount;
            $receiver->save();

            //Add transaction histories
            $senderWalletTransaction                          = new WalletTransactions;
            $senderWalletTransaction->transaction_type        = 'debit';
            $senderWalletTransaction->transaction_amount      = $request->amount;
            $senderWalletTransaction->transaction_description = 'Funds transfer to another wallet';
            $senderWalletTransaction->wallet_id               = $sender->id;
            $senderWalletTransaction->user_id                 = Auth::id();
            $senderWalletTransaction->save();

            $receiverWalletTransaction                          = new WalletTransactions;
            $receiverWalletTransaction->transaction_type        = 'credit';
            $receiverWalletTransaction->transaction_amount      = $request->amount;
            $receiverWalletTransaction->transaction_description = 'Funds received from another wallet';
            $receiverWalletTransaction->wallet_id               = $receiver->id;
            $receiverWalletTransaction->user_id                 = $receiver->user_id;
            $receiverWalletTransaction->save();

            //Update senders transaction history
            $transaction                          = new Transactions;
            $transaction->transaction_type        = 'debit';
            $transaction->transaction_amount      = $request->amount;
            $transaction->transaction_description = 'Funds transfer';
            $transaction->wallet_id               = $sender->id;
            $transaction->user_id                 = Auth::id();
            $transaction->save();

        //Commit database transaction
        DB::commit();

        return response()->json([
            'status'  => true,
            'message' => 'Funds transferred successfully',
        ], 200);
    }
    
    /**
     * Delete wallet
     *
     * @param  mixed $id
     * @return Response
     */
    public function deleteWallet($id)
    {
        $wallet = Wallets::find($id);

        if (!isset($wallet)) {
            return response()->json([
                'status' => false,
                'message' => 'Wallet not found'
            ], 404);
        }

        $wallet->delete();

        return response()->json([
            'status' => true,
            'message' => 'Wallet deleted successfully'
        ], 200);
    }
}
