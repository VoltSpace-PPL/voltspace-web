<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\CreatesTestReports;
use Tests\DuskTestCase;

class ReportPreviewRead001Test extends DuskTestCase
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
     * PBI-39 TC.ReportPreview.Read.001 (Positive)
     */
    public function test_tc_report_preview_read_001(): void
    {
        $this->browse(function (Browser $browser) {
            $this->loginAdmin($browser);
            $this->visitReportsPage($browser);
            $this->waitForReportsList($browser);

            $browser->script("document.querySelector('button[onclick^=\"previewReport\"]').click();");

            $browser->waitUntil('!document.getElementById("preview-modal").classList.contains("hidden")', 15)
                ->waitUntil('!document.getElementById("preview-data").classList.contains("hidden")', 20)
                ->pause(500);

            $modalText = $browser->script('return document.getElementById("preview-modal").innerText;')[0];

            $this->assertStringContainsString('TOTAL CONSUMPTION', $modalText);
            $this->assertStringContainsString('Room Details', $modalText);
            $this->assertStringContainsString('Lab Energi Test', $modalText);
            $this->assertStringContainsString('42.5', $modalText);
        });
    }
}
