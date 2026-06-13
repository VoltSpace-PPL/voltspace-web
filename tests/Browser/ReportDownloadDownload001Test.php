<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesTestReports;
use Tests\DuskTestCase;

class ReportDownloadDownload001Test extends DuskTestCase
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
     * PBI-41 TC.ReportDownload.Download.001 (Positive)
     */
    public function test_tc_report_download_download_001(): void
    {
        $reportId = $this->reportId;

        $this->browse(function (Browser $browser) use ($reportId) {
            $this->loginAdmin($browser);
            $this->visitReportsPage($browser);
            $this->waitForReportsList($browser);

            $browser->script("document.querySelector('button[onclick^=\"downloadReport\"]').click();");

            $browser->pause(2000)
                ->assertDontSee('Download Failed');

            $result = $this->callReportApiAsBrowser(
                $browser,
                'GET',
                "/api/laporan-energi/{$reportId}/download"
            );

            $this->assertSame(200, $result['status']);
        });
    }
}
