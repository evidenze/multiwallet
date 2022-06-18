<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransactions extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_amount', 'transaction_type', 'transaction_description',
    ];

    protected $table = 'wallet_transactions';

    /**
     * Get the user that owns the wallet.
     */
    public function wallet()
    {
        return $this->belongsTo(Wallets::class);
    }
}
