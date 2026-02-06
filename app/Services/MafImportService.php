<?php

namespace App\Services;

use App\Models\Maf;
use App\Models\MafImportBatch;
use App\Models\MafCategoriaMap;
use Rap2hpoutre\FastExcel\FastExcel; // Usaremos FastExcel si está instalado, o implementación nativa
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MafImportService
{
    public function import($filePath)
    {
        $batch = MafImportBatch::create([
            'filename' => basename($filePath),
            'status' => 'processing',
            'started_at' => now(),
            'total_rows' => 0,
            'processed_rows' => 0,
            'error_rows' => 0,
        ]);

        try {
            // Verificar si el archivo existe
            if (!file_exists($filePath)) {
                throw new \Exception("El archivo no existe: $filePath");
            }

            // Detectar tipo de archivo
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
            
            $rows = [];
            
            if (in_array(strtolower($extension), ['xlsx', 'xls'])) {
                // Cargar Excel usando PhpSpreadsheet
                $spreadsheet = IOFactory::load($filePath);
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray();
                
                // Remover encabezado
                $header = array_shift($rows);
                
                // Mapear encabezados a índices
                $headerMap = $this->mapHeaders($header);
            } else {
                throw new \Exception("Formato no soportado. Use .xlsx o .xls");
            }

            $batch->total_rows = count($rows);
            $batch->save();

            $chunkSize = 500;
            $chunks = array_chunk($rows, $chunkSize);
            $processed = 0;
            $errors = 0;

            foreach ($chunks as $chunk) {
                DB::beginTransaction();
                try {
                    foreach ($chunk as $row) {
                        try {
                            $data = $this->mapRow($row, $headerMap);
                            
                            // Validaciones básicas
                            if (empty($data['placa']) && empty($data['serie'])) {
                                continue;
                            }
                            
                            // Buscar categoría automáticamente
                            $categoria = null;
                            if (!empty($data['descripcion'])) {
                                $categoria = MafCategoriaMap::buscarCategoria($data['descripcion']);
                            }

                            Maf::create([
                                'batch_id' => $batch->id,
                                'placa' => $data['placa'] ?? null,
                                'serie' => $data['serie'] ?? null,
                                'descripcion' => $data['descripcion'] ?? null,
                                'marca' => $data['marca'] ?? null,
                                'modelo' => $data['modelo'] ?? null,
                                'activo' => $data['activo'] ?? null,
                                'cr' => $data['cr'] ?? null,
                                'tienda' => $data['tienda'] ?? null,
                                'plaza' => $data['plaza'] ?? null,
                                'status' => 'valid',
                                'categoria' => $categoria,
                            ]);
                            
                            $processed++;
                        } catch (\Exception $e) {
                            $errors++;
                            Log::warning("Error importando fila: " . $e->getMessage());
                        }
                    }
                    DB::commit();
                    
                    // Actualizar progreso
                    $batch->processed_rows = $processed;
                    $batch->error_rows = $errors;
                    $batch->save();
                    
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            }

            $batch->status = 'done';
            $batch->finished_at = now();
            $batch->save();

            // Después de importar, actualizar el catálogo de tiendas
            \Illuminate\Support\Facades\Artisan::call('db:seed', [
                '--class' => 'TiendaSeeder',
                '--force' => true
            ]);

            return $batch;

        } catch (\Exception $e) {
            $batch->status = 'failed';
            $batch->error_message = $e->getMessage();
            $batch->finished_at = now();
            $batch->save();
            
            throw $e;
        }
    }

    private function mapHeaders($headerRow)
    {
        $map = [];
        foreach ($headerRow as $index => $value) {
            if (!$value) continue;
            
            $slug = \Illuminate\Support\Str::slug($value, '_');
            
            // Mapeo flexible de nombres de columnas
            if (str_contains($slug, 'placa')) $map['placa'] = $index;
            elseif (str_contains($slug, 'serie')) $map['serie'] = $index;
            elseif (str_contains($slug, 'descripcion') || str_contains($slug, 'desc_activo')) $map['descripcion'] = $index;
            elseif (str_contains($slug, 'marca')) $map['marca'] = $index;
            elseif (str_contains($slug, 'modelo')) $map['modelo'] = $index;
            elseif (str_contains($slug, 'activo') || str_contains($slug, 'no_activo')) $map['activo'] = $index;
            elseif (str_contains($slug, 'cr') || str_contains($slug, 'c_costo')) $map['cr'] = $index;
            elseif (str_contains($slug, 'tienda') || str_contains($slug, 'desc_c_costo')) $map['tienda'] = $index;
            elseif (str_contains($slug, 'plaza') || str_contains($slug, 'localidad')) $map['plaza'] = $index;
        }
        return $map;
    }

    private function mapRow($row, $map)
    {
        $data = [];
        foreach ($map as $field => $index) {
            $data[$field] = isset($row[$index]) ? trim($row[$index]) : null;
        }
        return $data;
    }
}
