<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesTestReports;
use Tests\DuskTestCase;

class ReportHideHide001Test extends DuskTestCase
{
    use CreatesTestReports;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cleanupReportStorage();
        $this->prepareReportDuskTest();
        $this->generateReportForAdmin();
    }

    /**
     * PBI-40 TC.ReportHide.Hide.001 (Positive)
     */
    public function test_tc_report_hide_hide_001(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAdmin($browser);
            $this->visitReportsPage($browser);
            $this->waitForReportsList($browser)
                ->assertSee('Laporan Energi Bulanan');

            $browser->script("document.querySelector('button[title=\"Hide report\"]').click();");

            $browser->waitForText('Hide Report', 10);
            $this->clickVsAlertButton($browser, 'Yes');

            $browser->waitForText('Success', 15)
                ->assertSee('Laporan berhasil disembunyikan.')
                ->pause(1000);

            $this->dismissVsAlert($browser);

            $this->waitForReportsList($browser)
                ->assertSee('See Hidden Reports')
                ->assertDontSee('Laporan Energi Bulanan');
        });
    }
}
