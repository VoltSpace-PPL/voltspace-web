<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesTestReports;
use Tests\DuskTestCase;

class ReportGenerateCreate001Test extends DuskTestCase
{
    use CreatesTestReports;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cleanupReportStorage();
        $this->prepareReportDuskTest();
    }

    /**
     * PBI-38 TC.ReportGenerate.Create.001 (Positive)
     */
    public function test_tc_report_generate_create_001(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAdmin($browser);
            $this->visitReportsPage($browser);

            $browser->script("document.getElementById('input-month').value = '2026-06';");

            $browser->click('#generate-btn')
                ->waitForText('Report Generated', 20)
                ->assertSee('has been generated successfully.');

            $this->dismissVsAlert($browser);

            $this->waitForReportsList($browser)
                ->assertSee('Generated Reports')
                ->assertSee('Laporan Energi Bulanan');
        });
    }
}
