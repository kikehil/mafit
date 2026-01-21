<?php

namespace App\Services;

use App\Models\Maf;
use App\Models\MafImportBatch;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\RichText;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;

class MafImportService
{
    /**
     * Encabezados exactos esperados en la plantilla (fila 1, hoja 1)
     */
    private const EXACT_HEADERS = [
        'plaza',
        'cr',
        'tienda',
        'placa',
        'activo',
        'mescompra',
        'anocompra',
        'valor_neto',
        'remanente',
        'descripcion',
        'marca',
        'modelo',
        'serie',
    ];

    /**
     * Placeholder para compatibilidad (no se usa con la plantilla estricta)
     */
    private const COLUMN_MAP = [];

    /**
     * Resuelve el mapeo de encabezados del Excel a campos DB
     */
    public function resolveHeaderMap(array $headers): array
    {
        $map = [];
        $normalizedHeaders = [];

        // Normalizar todos los encabezados
        foreach ($headers as $index => $header) {
            $normalizedHeaders[$index] = $this->normalizeHeader($header);
        }

        // Buscar coincidencias
        foreach (self::COLUMN_MAP as $dbField => $variants) {
            foreach ($normalizedHeaders as $index => $normalized) {
                foreach ($variants as $variant) {
                    if ($normalized === $variant) {
                        $map[$dbField] = $index;
                        break 2;
                    }
                }
            }
        }

        return $map;
    }

    /**
     * Normaliza un encabezado para comparación
     */
    private function normalizeHeader(string $header): string
    {
        // Validar UTF-8
        if (!mb_check_encoding($header, 'UTF-8')) {
            $header = mb_convert_encoding($header, 'UTF-8', 'UTF-8');
        }

        // Normalizar Unicode NFKC
        if (class_exists('Normalizer') && function_exists('normalizer_is_normalized')) {
            if (normalizer_is_normalized($header, \Normalizer::FORM_KC) === false) {
                $header = normalizer_normalize($header, \Normalizer::FORM_KC);
            }
        }

        // Eliminar BOM y NBSP
        $header = str_replace(["\xEF\xBB\xBF", "\xC2\xA0"], ' ', $header);

        // Eliminar caracteres de formato y control
        $header = preg_replace('/\p{Cf}+/u', '', $header);
        $header = preg_replace('/\p{Cc}+/u', '', $header);

        // Reemplazar underscores y guiones por espacio
        $header = str_replace(['_', '-'], ' ', $header);

        // Colapsar espacios
        $header = preg_replace('/\s+/', ' ', $header);
        $header = trim($header);

        // Quitar todo excepto letras, números y espacio
        $header = preg_replace('/[^\pL\pN ]+/u', '', $header);

        // Uppercase
        $header = mb_strtoupper($header, 'UTF-8');

        // Quitar acentos
        $header = $this->removeAccents($header);

        // Colapsar espacios nuevamente
        $header = preg_replace('/\s+/', ' ', $header);
        $header = trim($header);

        return $header;
    }

    /**
     * Elimina acentos de un string
     */
    private function removeAccents(string $text): string
    {
        if (class_exists('Normalizer') && function_exists('normalizer_normalize')) {
            $text = normalizer_normalize($text, \Normalizer::FORM_D);
            $text = preg_replace('/\p{Mn}/u', '', $text);
        } else {
            // Fallback sin extensión intl
            $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        }
        return $text;
    }

    /**
     * Limpia texto del Excel eliminando caracteres invisibles y normalizando
     */
    public function cleanExcelText(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        // Validar UTF-8
        if (!mb_check_encoding($value, 'UTF-8')) {
            $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        }

        // Normalizar Unicode NFKC
        if (class_exists('Normalizer') && function_exists('normalizer_is_normalized')) {
            if (normalizer_is_normalized($value, \Normalizer::FORM_KC) === false) {
                $value = normalizer_normalize($value, \Normalizer::FORM_KC);
            }
        }

        // Eliminar BOM (U+FEFF)
        $value = str_replace("\xEF\xBB\xBF", '', $value);

        // Eliminar NBSP (U+00A0)
        $value = str_replace("\xC2\xA0", ' ', $value);

        // Eliminar caracteres de formato \p{Cf} (incluye U+202D, U+202E, LRM/RLM, etc.)
        $value = preg_replace('/\p{Cf}/u', '', $value);

        // Eliminar controles \p{Cc}
        $value = preg_replace('/\p{Cc}/u', '', $value);

        // Trim y colapsar espacios
        $value = trim($value);
        $value = preg_replace('/\s+/', ' ', $value);

        return $value;
    }

    /**
     * Limpia un identificador (placa, activo, serie)
     */
    public function cleanIdentifier(?string $value): string
    {
        $value = $this->cleanExcelText($value);
        $value = mb_strtoupper($value, 'UTF-8');
        $value = preg_replace('/\s+/', '', $value); // Eliminar espacios internos
        $value = preg_replace('/[^A-Z0-9\-]/', '', $value); // Solo A-Z, 0-9, guión

        return $value;
    }

    /**
     * Convierte un valor a decimal seguro
     */
    public function toDecimal($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Convertir a string
        $value = (string) $value;

        // Eliminar símbolos de moneda y espacios (incluye NBSP/BOM y Cf/Cc)
        $value = str_replace(["\xEF\xBB\xBF", "\xC2\xA0"], '', $value);
        $value = preg_replace('/\p{Cf}+|\p{Cc}+|[\$\s]/u', '', $value);

        // Reemplazar comas por puntos (separador de miles)
        // Si hay múltiples comas o puntos, asumir formato europeo
        $hasComma = strpos($value, ',') !== false;
        $hasDot = strpos($value, '.') !== false;

        if ($hasComma && $hasDot) {
            // Formato mixto: asumir que la última coma/punto es decimal
            $lastComma = strrpos($value, ',');
            $lastDot = strrpos($value, '.');
            if ($lastComma > $lastDot) {
                // Coma es decimal, punto es miles
                $value = str_replace('.', '', $value);
                $value = str_replace(',', '.', $value);
            } else {
                // Punto es decimal, coma es miles
                $value = str_replace(',', '', $value);
            }
        } elseif ($hasComma) {
            // Solo coma: puede ser decimal o miles
            // Si hay más de una coma, son miles
            if (substr_count($value, ',') > 1) {
                $value = str_replace(',', '', $value);
            } else {
                $value = str_replace(',', '.', $value);
            }
        }

        // Convertir a float y luego a string con 2 decimales
        $floatValue = filter_var($value, FILTER_VALIDATE_FLOAT);
        
        if ($floatValue === false) {
            return null;
        }

        return number_format($floatValue, 2, '.', '');
    }

    /**
     * Detecta la hoja y fila de encabezados con mejor coincidencia.
     * Busca en todas las hojas y primeras 30 filas.
     */
    private function detectSheetAndHeaders(\PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet): array
    {
        $bestScore = -1;
        $best = null;
        $keyHeaders = ['distrito', 'cr', 'cr desc', 'codigo barras', 'no activo', 'serie'];

        foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
            $highestRow = min(30, $sheet->getHighestRow());
            $highestColumn = $sheet->getHighestColumn();

            for ($row = 1; $row <= $highestRow; $row++) {
                $headersRaw = [];
                $headersNorm = [];
                for ($col = 'A'; $col <= $highestColumn; $col++) {
                    $cellValue = $sheet->getCell($col . $row)->getValue();
                    $clean = $this->cleanExcelText($cellValue ?? '');
                    $headersRaw[] = $cellValue;
                    $headersNorm[] = $clean;
                }

                // Mapeo y score
                $map = $this->resolveHeaderMap($headersNorm);
                $score = count($map);

                // Bonus por key headers presentes
                $keyPresent = 0;
                foreach ($keyHeaders as $kh) {
                    if (in_array($kh, $headersNorm, true)) {
                        $keyPresent++;
                    }
                }
                $score += $keyPresent * 2; // dar peso extra a claves

                if ($score > $bestScore) {
                    $bestScore = $score;
                    $best = [
                        'sheet' => $sheet,
                        'sheet_title' => $sheet->getTitle(),
                        'header_row' => $row,
                        'headers_raw' => $headersRaw,
                        'headers_norm' => $headersNorm,
                        'header_map' => $map,
                        'key_present' => $keyPresent,
                    ];
                }
            }
        }

        return $best ?? [];
    }

    /**
     * Obtiene string de una celda (formateado), manejando RichText y valores nulos.
     */
    private function getCellString($cell): string
    {
        if ($cell === null) {
            return '';
        }
        $value = '';
        if ($cell instanceof RichText) {
            $value = $cell->getPlainText();
        } else {
            // getFormattedValue para encabezados
            if (method_exists($cell, 'getFormattedValue')) {
                $value = $cell->getFormattedValue();
            } else {
                $value = $cell->getValue();
            }
        }
        if ($value === null) {
            $value = '';
        }
        // limpieza básica de BOM/NBSP
        $value = str_replace(["\xEF\xBB\xBF", "\xC2\xA0"], ' ', (string) $value);
        return (string) $value;
    }

    /**
     * Construye un mapa header_normalized => colIndex usando contains.
     */
    private function buildHeaderMap(array $headersNorm): array
    {
        $map = [];
        // Diccionario de headers normalizados a index
        $dict = [];
        foreach ($headersNorm as $idx => $h) {
            if ($h !== '') {
                $dict[$idx] = $h;
            }
        }

        foreach (self::HEADER_ALIASES as $field => $aliases) {
            foreach ($dict as $idx => $hNorm) {
                foreach ($aliases as $alias) {
                    if (mb_stripos($hNorm, $alias) !== false) {
                        $map[$field] = $idx;
                        break 2;
                    }
                }
            }
        }

        return $map;
    }

    /**
     * Parsea el Excel usando la plantilla estricta:
     * - Hoja 1 (índice 0)
     * - Fila 1 con encabezados exactos iguales a self::EXACT_HEADERS
     */
    public function parseExcel(string $path): \Generator
    {
        try {
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($path);

            $sheet = $spreadsheet->getSheet(0);
            $sheetTitle = $sheet->getTitle();

            // Leer encabezados exactos de la fila 1
            $highestColumn = Coordinate::columnIndexFromString($sheet->getHighestColumn(1));
            $headersRaw = [];
            $headersNormalized = [];
            for ($colIdx = 1; $colIdx <= $highestColumn; $colIdx++) {
                $cell = $sheet->getCellByColumnAndRow($colIdx, 1);
                $rawValue = trim((string) $this->getCellString($cell));
                $headersRaw[] = $rawValue;
                // Normalizar el encabezado para comparación
                $normalized = $this->normalizeHeader($rawValue);
                // También crear versión simple en minúsculas sin espacios/guiones
                $simple = mb_strtolower(preg_replace('/[\s_\-]+/', '', $normalized));
                $headersNormalized[] = [
                    'raw' => $rawValue,
                    'normalized' => $normalized,
                    'simple' => $simple,
                ];
            }

            // Validar encabezados exactos (normalizando antes de comparar)
            $headerMap = [];
            $missing = [];
            
            // Primero buscar los requeridos
            foreach (self::EXACT_HEADERS as $field) {
                $found = null;
                $fieldSimple = mb_strtolower(preg_replace('/[\s_\-]+/', '', $field));
                
                foreach ($headersNormalized as $idx => $hInfo) {
                    // Comparar versión simple (sin espacios, guiones, mayúsculas)
                    if ($hInfo['simple'] === $fieldSimple) {
                        $found = $idx;
                        break;
                    }
                    // También comparar normalizado completo
                    if (mb_strtolower($hInfo['normalized']) === mb_strtolower($field)) {
                        $found = $idx;
                        break;
                    }
                }
                
                if ($found === null) {
                    $missing[] = $field;
                } else {
                    $headerMap[$field] = $found;
                }
            }
            
            if (!empty($missing)) {
                throw new \Exception('Columnas faltantes: ' . implode(', ', $missing));
            }

            yield [
                'meta' => [
                    'header_map' => $headerMap,
                    'missing_columns' => [],
                    'sheet_title' => $sheetTitle,
                    'header_row' => 1,
                    'headers_raw' => $headersRaw,
                    'headers_norm' => $headersRaw,
                    'score' => count($headerMap),
                ],
            ];

            $highestRow = $sheet->getHighestRow();
            for ($row = 2; $row <= $highestRow; $row++) {
                $rowData = [];
                foreach (self::EXACT_HEADERS as $field) {
                    $colIndex = $headerMap[$field];
                    $cell = $sheet->getCellByColumnAndRow($colIndex + 1, $row);
                    $cellValue = $this->getCellString($cell);
                    $rowData[$field] = $cellValue;
                }

                yield [
                    'row_num' => $row,
                    'data' => $rowData,
                ];
            }
        } catch (ReaderException $e) {
            throw new \Exception('Error al leer el archivo Excel: ' . $e->getMessage());
        }
    }

    /**
     * Prepara una fila para inserción respetando el orden fijo de columnas.
     * Devuelve null si la fila está completamente vacía en los campos relevantes.
     */
    private function prepareRowForInsert(int $batchId, int $rowNum, array $rowRaw): ?array
    {
        $plaza = $this->nullIfEmpty($this->cleanExcelText($rowRaw['plaza'] ?? ''));
        $cr = $this->nullIfEmpty($this->cleanExcelText($rowRaw['cr'] ?? ''));
        $tienda = $this->nullIfEmpty($this->cleanExcelText($rowRaw['tienda'] ?? ''));

        $placa = $this->nullIfEmpty($this->cleanIdentifier($rowRaw['placa'] ?? ''));
        $activo = $this->nullIfEmpty($this->cleanIdentifier($rowRaw['activo'] ?? ''));
        $serie = $this->nullIfEmpty($this->cleanIdentifier($rowRaw['serie'] ?? ''));

        $mescompra = $this->toInt($rowRaw['mescompra'] ?? null);
        $anocompra = $this->toInt($rowRaw['anocompra'] ?? null);

        $valorNeto = $this->toDecimal($rowRaw['valor_neto'] ?? null);
        $remanente = $this->toDecimal($rowRaw['remanente'] ?? null);

        $descripcion = $this->nullIfEmpty($this->cleanExcelText($rowRaw['descripcion'] ?? ''));
        $marca = $this->nullIfEmpty($this->cleanExcelText($rowRaw['marca'] ?? ''));
        $modelo = $this->nullIfEmpty($this->cleanExcelText($rowRaw['modelo'] ?? ''));

        $allEmpty = $plaza === null
            && $cr === null
            && $tienda === null
            && $placa === null
            && $activo === null
            && $serie === null
            && $descripcion === null
            && $marca === null
            && $modelo === null
            && $valorNeto === null
            && $remanente === null;

        if ($allEmpty) {
            return null;
        }

        $now = now();

        return [
            'batch_id' => $batchId,
            'row_num' => $rowNum,
            'plaza' => $plaza,
            'cr' => $cr,
            'tienda' => $tienda,
            'placa' => $placa,
            'activo' => $activo,
            'serie' => $serie,
            'mescompra' => $mescompra,
            'anocompra' => $anocompra,
            'valor_neto' => $valorNeto,
            'remanente' => $remanente,
            'descripcion' => $descripcion,
            'marca' => $marca,
            'modelo' => $modelo,
            'imported_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    /**
     * Convierte a entero seguro
     */
    private function toInt($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_int($value)) {
            return $value;
        }

        $value = preg_replace('/[^\d\-]+/', '', (string) $value);
        if ($value === '' || !is_numeric($value)) {
            return null;
        }

        return (int) $value;
    }

    /**
     * Devuelve null si la cadena está vacía
     */
    private function nullIfEmpty(?string $value): ?string
    {
        return ($value === null || $value === '') ? null : $value;
    }

    /**
     * Trunca las notas a 2000 caracteres
     */
    private function truncateNotes(?string $notes): ?string
    {
        if ($notes === null) {
            return null;
        }

        return mb_strlen($notes) > 2000 ? mb_substr($notes, 0, 2000) . '...' : $notes;
    }

    /**
     * Marca el batch como fallido con nota truncada
     */
    private function markBatchFailed(MafImportBatch $batch, string $message): void
    {
        $batch->update([
            'status' => 'failed',
            'finished_at' => now(),
            'notes' => $this->truncateNotes($message),
        ]);
    }

    /**
     * Importa un batch desde un archivo Excel
     */
    public function importBatch(MafImportBatch $batch, string $path): void
    {
        try {
            $batch->update([
                'status' => 'processing',
                'started_at' => now(),
            ]);

            // Limpiar tabla maf ANTES de la transacción (ALTER TABLE hace commit implícito)
            DB::table('maf')->delete();
            DB::statement('ALTER TABLE maf AUTO_INCREMENT = 1');

            DB::transaction(function () use ($batch, $path) {
                $totalRows = 0;
                $insertedRows = 0;
                $rowsToInsert = [];
                $sheetTitle = null;
                $headersRaw = [];

                foreach ($this->parseExcel($path) as $item) {
                    // Meta inicial
                    if (isset($item['meta'])) {
                        $sheetTitle = $item['meta']['sheet_title'] ?? null;
                        $headersRaw = $item['meta']['headers_raw'] ?? [];
                        continue;
                    }

                    $totalRows++;
                    $rowData = $this->prepareRowForInsert($batch->id, $item['row_num'], $item['data']);
                    if ($rowData === null) {
                        continue; // fila completamente vacía
                    }

                    $rowsToInsert[] = $rowData;

                    // Insertar en lotes de 500
                    if (count($rowsToInsert) >= 500) {
                        DB::table('maf')->insert($rowsToInsert);
                        $insertedRows += count($rowsToInsert);
                        $rowsToInsert = [];
                    }
                }

                // Insertar filas restantes
                if (!empty($rowsToInsert)) {
                    DB::table('maf')->insert($rowsToInsert);
                    $insertedRows += count($rowsToInsert);
                }

                // Notas (máx 2000 caracteres)
                $notesParts = [];
                if ($sheetTitle) {
                    $notesParts[] = 'Hoja: ' . $sheetTitle;
                }
                if (!empty($headersRaw)) {
                    $notesParts[] = 'Encabezados: ' . implode(', ', $headersRaw);
                }
                $notesStr = $this->truncateNotes(implode(' | ', array_filter($notesParts)));

                $batch->update([
                    'status' => 'done',
                    'finished_at' => now(),
                    'total_rows' => $totalRows,
                    'inserted_rows' => $insertedRows,
                    'notes' => $notesStr,
                ]);
            });
        } catch (\Exception $e) {
            $this->markBatchFailed($batch, 'Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Genera reporte de conflictos y duplicados para un batch
     */
    public function reportForBatch(int $batchId): array
    {
        $conflicts = [
            'placa' => [],
            'activo' => [],
            'serie' => [],
        ];

        $duplicates = [
            'placa' => [],
            'activo' => [],
            'serie' => [],
        ];

        $identifiers = ['placa', 'activo', 'serie'];

        foreach ($identifiers as $identifier) {
            // Query para encontrar conflictos y duplicados
            $results = DB::table('maf')
                ->select(
                    $identifier . ' as value',
                    DB::raw('COUNT(*) as rows_count'),
                    DB::raw('COUNT(DISTINCT cr) as tiendas_distintas'),
                    DB::raw('COUNT(DISTINCT plaza) as plazas_distintas')
                )
                ->where('batch_id', $batchId)
                ->whereNotNull($identifier)
                ->where($identifier, '!=', '')
                ->groupBy($identifier)
                ->havingRaw('COUNT(*) > 1')
                ->get();

            foreach ($results as $result) {
                $value = $result->value;
                $rowsCount = $result->rows_count;
                $tiendasDistintas = $result->tiendas_distintas;
                $plazasDistintas = $result->plazas_distintas;

                // Obtener ocurrencias
                $occurrences = DB::table('maf')
                    ->select('row_num', 'plaza', 'cr', 'tienda', 'descripcion', 'marca', 'modelo')
                    ->where('batch_id', $batchId)
                    ->where($identifier, $value)
                    ->orderBy('row_num')
                    ->get()
                    ->toArray();

                $item = [
                    'value' => $value,
                    'rows_count' => $rowsCount,
                    'tiendas_distintas' => $tiendasDistintas,
                    'plazas_distintas' => $plazasDistintas,
                    'occurrences' => $occurrences,
                ];

                if ($tiendasDistintas > 1) {
                    // CONFLICTO GRAVE: mismo identificador en 2+ tiendas distintas
                    $conflicts[$identifier][] = $item;
                } else {
                    // DUPLICADO SIMPLE: mismo identificador repetido en la misma tienda
                    $duplicates[$identifier][] = $item;
                }
            }
        }

        return [
            'conflicts' => $conflicts,
            'duplicates' => $duplicates,
        ];
    }
}

