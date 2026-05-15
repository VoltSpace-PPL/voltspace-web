<?php

namespace Database\Seeders;

use App\Models\EnergyAlertSetting;
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
        User::query()->updateOrCreate(
            ['email' => 'superadmin@voltspace.id'],
            [
                'name' => 'Super Admin',
                'role' => 'super_admin',
                'password' => Hash::make('super123'),
            ]
        );

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

        if (! EnergyAlertSetting::query()->exists()) {
            EnergyAlertSetting::query()->create([
                'high_usage_threshold_kwh' => 100,
                'peak_demand_limit_kw' => 10,
            ]);
        }
    }
}
