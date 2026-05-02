<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Must match demo text on frontend login (resources/views/auth/login.blade.php)
        User::query()->updateOrCreate(
            ['email' => 'admin@voltspace.id'],
            [
                'name' => 'Admin User',
                'role' => 'admin',
                'password' => Hash::make('admin123'),
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'student@voltspace.id'],
            [
                'name' => 'Student User',
                'role' => 'mahasiswa',
                'password' => Hash::make('student123'),
            ]
        );
    }
}
