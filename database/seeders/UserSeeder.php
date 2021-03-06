<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallets;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->has(Wallets::factory()->count(10))->count(10)->create();
    }
}
