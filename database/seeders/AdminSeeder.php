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
        // updateOrCreate (not firstOrCreate) so that if an earlier deploy
        // left a bad/plaintext password in this row, every fresh deploy
        // re-hashes and corrects it instead of leaving the broken row as-is.
        Admin::updateOrCreate(
            ['email' => 'admin@vedha.com'],
            ['name' => 'company', 'password' => Hash::make('password')]
        );
    }
}
