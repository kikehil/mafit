<?php

namespace Tests\Feature;

use App\Models\MafImportBatch;
use App\Models\User;
use App\Services\MafImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class MafImportServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_clean_identifier_removes_invisible_chars(): void
    {
        $service = new MafImportService();
        $input = "ABC\u{202D}123";

        $this->assertSame('ABC123', $service->cleanIdentifier($input));
    }

    public function test_to_decimal_parses_currency_string(): void
    {
        $service = new MafImportService();

        $this->assertSame('62652.46', $service->toDecimal('$62,652.46'));
    }

    public function test_import_fails_when_placa_header_is_missing(): void
    {
        $user = User::factory()->create();
        $batch = MafImportBatch::create([
            'period' => '2026-01',
            'filename' => 'test.xlsx',
            'uploaded_by' => $user->id,
            'status' => 'pending',
        ]);

        $path = $this->makeSpreadsheetWithoutPlaca();
        $service = app(MafImportService::class);

        try {
            $service->importBatch($batch, $path);
        } catch (\Exception $e) {
            // Se espera fallo, no es necesario propagar en la prueba
        }

        $batch->refresh();

        $this->assertSame('failed', $batch->status);
        $this->assertStringContainsString('placa', strtolower($batch->notes ?? ''));
    }

    private function makeSpreadsheetWithoutPlaca(): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados exactos, omitiendo "placa" para provocar el fallo
        $headers = [
            'plaza',
            'cr',
            'tienda',
            'activo',
            'mescompra',
            'anocompra',
            'costo',
            'valor_neto',
            'remanente',
            'descripcion',
            'marca',
            'modelo',
            'serie',
        ];

        foreach ($headers as $idx => $header) {
            $sheet->setCellValueByColumnAndRow($idx + 1, 1, $header);
        }

        // Datos de ejemplo
        $sheet->setCellValue('A2', 'PLZ1');
        $sheet->setCellValue('B2', 'CR1');

        $temp = tempnam(sys_get_temp_dir(), 'maf_test_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($temp);

        return $temp;
    }
}











