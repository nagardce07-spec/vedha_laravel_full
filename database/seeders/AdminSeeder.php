<?php
namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    // Run with: php artisan db:seed --class=AdminSeeder
    public function run(): void
    {
        Admin::firstOrCreate(
            ['email' => 'admin@vedha.com'],
            ['name' => 'company', 'password' => Hash::make('password')]
        );
    }
}
