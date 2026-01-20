<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessMafImport;
use App\Models\MafImportBatch;
use App\Services\MafImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class MafImportController extends Controller
{
    public function create()
    {
        \Illuminate\Support\Facades\Gate::authorize('import-maf');

        return view('maf.import');
    }

    public function store(Request $request, MafImportService $service)
    {
        \Illuminate\Support\Facades\Gate::authorize('import-maf');

        $validated = $request->validate([
            'period' => ['required', 'regex:/^\d{4}-\d{2}$/'],
            'file' => ['required', 'file', 'mimes:xlsx', 'max:51200'], // 50MB
        ]);

        // Guardar archivo
        $file = $request->file('file');
        $filename = $file->getClientOriginalName();
        $path = $file->store('maf-imports', 'local');

        // Crear batch
        $batch = MafImportBatch::create([
            'period' => $validated['period'],
            'filename' => $filename,
            'uploaded_by' => auth()->id(),
            'status' => 'processing',
            'started_at' => now(),
        ]);

        // Procesar (síncrono por ahora, pero estructura lista para async)
        try {
            $fullPath = Storage::path($path);
            $service->importBatch($batch, $fullPath);
        } catch (\Exception $e) {
            return redirect()
                ->route('maf.batches.show', $batch)
                ->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }

        return redirect()
            ->route('maf.batches.show', $batch)
            ->with('success', 'Archivo procesado correctamente');
    }

    /**
     * Descargar plantilla Excel para importación MAF
     */
    public function downloadTemplate()
    {
        \Illuminate\Support\Facades\Gate::authorize('import-maf');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados de columnas (exactos como se muestran en la imagen)
        $headers = [
            'plaza',
            'cr',
            'tienda',
            'placa',
            'activo',
            'mescompra',
            'anocompra',
            'Valor_Neto',
            'remanente',
            'descripcion',
            'marca',
            'modelo',
            'serie',
        ];

        // Establecer encabezados en la fila 1
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $column++;
        }

        // Estilizar encabezados
        $headerRange = 'A1:' . $column . '1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'], // Azul
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Agregar fila de ejemplo
        $exampleRow = [
            '32YXH',                        // plaza
            '5001',                         // cr
            'TIENDA EJEMPLO',               // tienda
            '123456',                       // placa
            'ACT001',                       // activo
            '01',                           // mescompra
            '2024',                         // anocompra
            '800.00',                       // Valor_Neto
            '200.00',                       // remanente
            'MONITOR P/PUNTO DE VENTA',     // descripcion
            'HP',                           // marca
            'EliteDisplay E243',           // modelo
            'ABC123456',                    // serie
        ];

        $row = 2;
        $column = 'A';
        foreach ($exampleRow as $value) {
            $sheet->setCellValue($column . $row, $value);
            $column++;
        }

        // Estilizar fila de ejemplo
        $exampleRange = 'A2:' . chr(ord('A') + count($headers) - 1) . '2';
        $sheet->getStyle($exampleRange)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E7E6E6'], // Gris claro
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);

        // Agregar nota informativa
        $sheet->setCellValue('A4', 'NOTA: Esta es una fila de ejemplo. Puede eliminarla antes de importar sus datos.');
        $sheet->mergeCells('A4:' . chr(ord('A') + count($headers) - 1) . '4');
        $sheet->getStyle('A4')->applyFromArray([
            'font' => [
                'italic' => true,
                'color' => ['rgb' => '666666'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
        ]);

        // Ajustar ancho de columnas
        foreach (range('A', chr(ord('A') + count($headers) - 1)) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Establecer altura de fila de encabezado
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Crear respuesta de descarga
        $filename = 'Plantilla_MAF_' . date('Y-m-d') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}






