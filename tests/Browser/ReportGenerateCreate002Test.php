<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesTestReports;
use Tests\DuskTestCase;

class ReportGenerateCreate002Test extends DuskTestCase
{
    use CreatesTestReports;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareReportDuskTest();
    }

    /**
     * PBI-38 TC.ReportGenerate.Create.002 (Negative)
     */
    public function test_tc_report_generate_create_002(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAdmin($browser);
            $this->visitReportsPage($browser);

            $browser->script("document.getElementById('input-month').value = '';");

            $browser->click('#generate-btn')
                ->waitForText('Select Month', 10)
                ->assertSee('Please select a month first.');
        });
    }
}
