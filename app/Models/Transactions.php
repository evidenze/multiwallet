<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_amount', 'transaction_type', 'transaction_description',
    ];

    protected $table = 'transactions';

    /**
     * Get the user that owns the wallet.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
