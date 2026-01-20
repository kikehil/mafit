<?php
require __DIR__.'/vendor/autoload.php';
use App\Services\MafImportService;
use PhpOffice\PhpSpreadsheet\IOFactory;

$file = 'C:/Users/galex/Downloads/MAF AL 23 DE DICIEMBRE-6pm.xlsx';
if (!file_exists($file)) { echo "No existe el archivo\n"; exit; }
$svc = new MafImportService();
$reader = IOFactory::createReader('Xlsx');
$reader->setReadDataOnly(true);
$sheet = $reader->load($file)->getActiveSheet();
$highestCol = $sheet->getHighestColumn();
$headers = [];
for ($col='A'; $col <= $highestCol; $col++) {
    $val = $sheet->getCell($col.'1')->getValue();
    $headers[] = $val;
}
echo "Encabezados crudos:\n"; print_r($headers);
$ref = new ReflectionClass(MafImportService::class);
$method = $ref->getMethod('resolveHeaderMap');
$method->setAccessible(true);
$map = $method->invoke($svc, $headers);
echo "Mapeo:\n"; print_r($map);
