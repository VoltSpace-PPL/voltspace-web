<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesTestReports;
use Tests\DuskTestCase;

class ReportHideCancel002Test extends DuskTestCase
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
     * PBI-40 TC.ReportHide.Cancel.002 (Negative)
     */
    public function test_tc_report_hide_cancel_002(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAdmin($browser);
            $this->visitReportsPage($browser);
            $this->waitForReportsList($browser)
                ->assertSee('Laporan Energi Bulanan');

            $browser->script("document.querySelector('button[title=\"Hide report\"]').click();");

            $browser->waitForText('Hide Report', 10);
            $this->clickVsAlertButton($browser, 'Cancel');

            $browser->pause(1000)
                ->assertSee('Generated Reports')
                ->assertSee('Laporan Energi Bulanan')
                ->assertDontSee('Laporan berhasil disembunyikan.');
        });
    }
}
