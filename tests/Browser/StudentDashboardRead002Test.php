<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class StudentDashboardRead002Test extends DuskTestCase
{
    /**
     * PBI #33 TC.StudentDashboard.Read.002 (Negative)
     */
    public function test_tc_student_dashboard_read_002()
    {
        $this->browse(function (Browser $browser) {
            $browser->logout()
                ->visit('/student/dashboard')
                ->pause(1000)
                ->assertPathIs('/login')
                ->assertDontSee('Student Dashboard');
        });
    }
}
