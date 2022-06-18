<?php

namespace App\Http\Controllers;

use App\Models\Transactions;
use App\Models\User;
use App\Models\Wallets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{    
    /**
     * Get the authenticated user details.
     *
     * @return Response
     */
    public function getUserDetails()
    {
        $user = User::find(Auth::id());
        $user->wallets;
        $user->transactions;

        return response()->json([
            'status'  => true,
            'message' => 'User fetched successfully',
            'data'    => $user,
        ], 200);
    }
    
    /**
     * Get the authenticated user transactions.
     *
     * @return Response
     */
    public function getUserTransactions()
    {
        $transactions = Transactions::where('user_id', Auth::id())->get();
        $transactions->user;

        return response()->json([
            'status'  => true,
            'mesaage' => 'Transactions fetched successfully',
            'data'    => $transactions
        ], 200);
    }
    
    /**
     * Get all users in the system.
     *
     * @return Response
     */
    public function getAllUsers()
    {
        $users = User::latest()->get();

        return response()->json([
            'status'  => true,
            'message' => "Users fetched successfully",
            'data'    => $users
        ], 200);
    }
    
    /**
     * Get a single user with id
     *
     * @param  mixed $id
     * @return Response
     */
    public function getSingleUser($id)
    {
        $user = User::find($id);

        return response()->json([
            'status' => true,
            'message' => 'User fetched successfully',
            'data' => $user
        ], 200);
    }
    
    /**
     * Get all wallets in the system.
     *
     * @return Response
     */
    public function getAllWallets()
    {
        $wallets = Wallets::latest()->get();

        return response()->json([
            'status'  => true,
            'message' => 'Wallets fetched successfully',
            'data'    => $wallets
        ], 200);
    }
    
    /**
     * Return the total users in number.
     *
     * @return Response
     */
    public function getUsersCount()
    {
        $user = User::all()->count();

        return response()->json([
            'status'  => true,
            'message' => 'Total users fetched successfully',
            'data'    => $user
        ], 200);
    }
    
    /**
     * Return the total wallets in number.
     *
     * @return Response
     */
    public function getWalletsCount()
    {
        $wallets = Wallets::all()->count();

        return response()->json([
            'status'  => true,
            'message' => 'Total wallets fetched successfully',
            'data'    => $wallets
        ], 200);
    }
    
    /**
     * Return the total wallets balances in the system.
     *
     * @return Response
     */
    public function getTotalWalletsBalance()
    {
        $wallets_balance = Wallets::all()->sum('wallet_balance');

        return response()->json([
            'status'  => true,
            'message' => 'Total wallets fetched successfully',
            'data'    => $wallets_balance
        ], 200);
    }
    
    /**
     * Return all the transactions volume in the system
     *
     * @return Response
     */
    public function getTotalTransactionsVolume()
    {
        $transactions = Transactions::all()->sum('amount');

        return response()->json([
            'status'  => true,
            'message' => 'Transactions fetched successfully',
            'data'    => $transactions
        ], 200);
    }
    
    /**
     * Return all system statistics.
     *
     * @return Response
     */
    public function getAllStats()
    {
        $users           = User::all()->count();
        $wallets         = Wallets::all()->count();
        $wallets_balance = Wallets::all()->sum('wallet_balance');
        $transactions    = Transactions::all()->sum('amount');

        return response()->json([
            'status'                    => true,
            'message'                   => 'Statistics fetched successfully',
            'total_user'                => $users,
            'total_wallets'             => $wallets,
            'total_wallets_balance'     => $wallets_balance,
            'total_transactions_volume' => $transactions
        ], 200);
    }
}
