<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesTestReports;
use Tests\DuskTestCase;

class ReportPreviewRead002Test extends DuskTestCase
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
     * PBI-39 TC.ReportPreview.Read.002 (Negative)
     */
    public function test_tc_report_preview_read_002(): void
    {
        $reportId = $this->reportId;

        $this->browse(function (Browser $browser) use ($reportId) {
            $this->loginStudent($browser);

            $result = $this->callReportApiAsBrowser(
                $browser,
                'GET',
                "/api/laporan-energi/{$reportId}/preview"
            );

            $this->assertSame(403, $result['status']);
            $this->assertStringContainsString('Akses ditolak', $result['message'] ?? '');
        });
    }
}
