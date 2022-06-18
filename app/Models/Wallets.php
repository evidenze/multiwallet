<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallets extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_name', 'wallet_code', 'wallet_type'
    ];

    protected $table = 'wallets';

    /**
     * Get the user that owns the wallet.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get transactions.
     */
    public function transactions()
    {
        return $this->hasMany(WalletTransactions::class, 'wallet_id');
    }
}
