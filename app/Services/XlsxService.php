<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class XlsxService
{
    public const MIME = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

    private const MAGIC_ZIP = "PK\x03\x04";

    private const MAGIC_XLS = "\xD0\xCF\x11\xE0";

    public static function download(string $defaultFilename, callable $fill, ?string $downloadFilename = null): StreamedResponse
    {
        $name = self::sanitizeFilename($downloadFilename ?? $defaultFilename);

        return response()->streamDownload(function () use ($fill): void {
            if (ob_get_level() > 0) {
                ob_clean();
            }

            $spreadsheet = new Spreadsheet;
            $fill($spreadsheet);
            (new Xlsx($spreadsheet))->save('php://output');
        }, $name, self::responseHeaders());
    }

    public static function downloadFromPath(string $path, string $defaultFilename, ?string $downloadFilename = null): BinaryFileResponse
    {
        $name = self::sanitizeFilename($downloadFilename ?? $defaultFilename);

        return response()->download($path, $name, self::responseHeaders());
    }

    public static function filenameFromRequest(Request $request, string $defaultFilename): string
    {
        $custom = $request->query('filename');
        if (! is_string($custom) || trim($custom) === '') {
            return self::sanitizeFilename($defaultFilename);
        }

        return self::sanitizeFilename(trim($custom));
    }

    public static function sanitizeFilename(string $filename): string
    {
        $filename = trim($filename);
        $filename = preg_replace('/[\\\\\\/:*?"<>|\\x00-\\x1F]/u', '_', $filename) ?? 'download';
        $filename = trim($filename, ".\u{FEFF} ");

        if ($filename === '') {
            $filename = 'download';
        }

        $lower = strtolower($filename);
        if (! str_ends_with($lower, '.xlsx') && ! str_ends_with($lower, '.xls')) {
            $filename .= '.xlsx';
        }

        return $filename;
    }

    /**
     * @return array<string, string>
     */
    public static function responseHeaders(): array
    {
        return [
            'Content-Type' => self::MIME,
            'Content-Transfer-Encoding' => 'binary',
            'Cache-Control' => 'must-revalidate, no-cache, no-store, private',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Content-Type-Options' => 'nosniff',
        ];
    }

    public static function isSpreadsheetFile(string $path): bool
    {
        if (! is_readable($path)) {
            return false;
        }

        $handle = @fopen($path, 'rb');
        if ($handle === false) {
            return false;
        }

        $header = fread($handle, 4);
        fclose($handle);

        if ($header === false || strlen($header) < 4) {
            return false;
        }

        return $header === self::MAGIC_ZIP || $header === self::MAGIC_XLS;
    }

    public static function validateUploadedSpreadsheet(UploadedFile $file, \Closure $fail): void
    {
        $path = $file->getRealPath();
        if ($path === false || ! self::isSpreadsheetFile($path)) {
            $fail('File harus berupa spreadsheet Excel (.xlsx atau .xls) yang valid.');
        }
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    public static function readRows(string $path): array
    {
        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);
        $sheet = $reader->load($path)->getActiveSheet();

        return $sheet->toArray();
    }
}
