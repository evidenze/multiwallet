<?php

namespace Database\Seeders;

use App\Models\Wallets;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Wallets::factory()->count(20)->create();
    }
}
