<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesTestReports;
use Tests\DuskTestCase;

class ReportDownloadDownload002Test extends DuskTestCase
{
    use CreatesTestReports;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cleanupReportStorage();
        $this->prepareReportDuskTest(withStudent: true);
        $this->generateReportForAdmin();
    }

    /**
     * PBI-41 TC.ReportDownload.Download.002 (Negative)
     */
    public function test_tc_report_download_download_002(): void
    {
        $reportId = $this->reportId;

        $this->browse(function (Browser $browser) use ($reportId) {
            $this->loginStudent($browser);

            $result = $this->callReportApiAsBrowser(
                $browser,
                'GET',
                "/api/laporan-energi/{$reportId}/download"
            );

            $this->assertSame(403, $result['status']);
            $this->assertStringContainsString('Akses ditolak', $result['message'] ?? '');
        });
    }
}
