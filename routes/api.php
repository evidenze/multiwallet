<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

//Authentication Routes
Route::post('/register', 'RegisterController@register');
Route::post('/login', 'LoginController@login');

Route::group(['middleware' => ['auth:api']], function() {
    
    //User routes
    Route::get('/user', 'HomeController@getUserDetails');
    Route::get('/transactions', 'HomeController@getUserTransactions');
    
    //Wallet Routes
    Route::prefix('wallet')->group(function () {
        Route::post('/', 'WalletController@createNewWallet');
        Route::get('/{id}', 'WalletController@getSingleWallet');
        Route::delete('/{id}', 'WalletController@deleteWallet');
        Route::get('/{id}/transactions', 'WalletController@getWalletTransactions');
        Route::post('/fund', 'WalletController@fundWallet');
        Route::post('/transfer', 'WalletController@transferToWallet');
    });
});


//Statistics Routes
Route::get('/users', 'HomeController@getAllUsers');
Route::get('/user/{id}', 'HomeController@getSingleUser');
Route::get('/wallets', 'HomeController@getAllWallets');
Route::get('/all-stats', 'HomeController@getAllStats');
Route::get('/total-users', 'HomeController@getUsersCount');
Route::get('/total-wallets', 'HomeController@getWalletsCount');
Route::get('/total-wallets-balance', 'HomeController@getTotalWalletsBalance');
Route::get('/total-transactions-volume', 'HomeController@getTotalTransactionsVolume');
