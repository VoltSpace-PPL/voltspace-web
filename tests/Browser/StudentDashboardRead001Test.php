<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class StudentDashboardRead001Test extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        \Schema::disableForeignKeyConstraints();
        User::where('email', 'student@voltspace.id')->delete();
        \Schema::enableForeignKeyConstraints();

        User::factory()->create([
            'email' => 'student@voltspace.id',
            'role' => 'mahasiswa',
            'password' => bcrypt('student123'),
        ]);
    }

    protected function loginStudent(Browser $browser): void
    {
        $browser->visit('/login')
            ->waitFor('input[name="email"]', 10)
            ->type('email', 'student@voltspace.id')
            ->type('password', 'student123')
            ->press('Sign In')
            ->waitForLocation('/student/dashboard', 15)
            ->pause(500);
    }

    /**
     * PBI #33 TC.StudentDashboard.Read.001 (Positive)
     */
    public function test_tc_student_dashboard_read_001()
    {
        $this->browse(function (Browser $browser) {
            $this->loginStudent($browser);

            $browser->visit('/student/dashboard')
                ->waitForText('Student Dashboard')
                ->pause(1000)
                ->assertSee('Total Requests')
                ->assertSee('Pending')
                ->assertSee('Approved')
                ->assertSee('Rejected')
                ->assertSee('Welcome to VoltSpace Room Booking System')
                ->assertSee('Quick Actions');
        });
    }
}
