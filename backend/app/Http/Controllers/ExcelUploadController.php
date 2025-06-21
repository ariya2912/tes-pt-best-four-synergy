<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Lead;

class ExcelUploadController extends Controller
{
    public function upload(Request $request)
    {
        try {
            if (!$request->hasFile('file')) {
                return response()->json(['error' => 'Tidak ada file dikirim.'], 400);
            }

            $file = $request->file('file');

            // Validate file type
            $allowedExtensions = ['xls', 'xlsx', 'csv'];
            $extension = $file->getClientOriginalExtension();
            if (!in_array($extension, $allowedExtensions)) {
                return response()->json(['error' => 'File harus berformat xls, xlsx, atau csv'], 400);
            }

            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            if (count($rows) === 0) {
                return response()->json(['error' => 'File Excel kosong'], 400);
            }

            $header = $rows[0];
            $fields = array_map('strtolower', $header);

            $dbColumns = \Schema::getColumnListing('leads');

            foreach ($rows as $index => $row) {
                if ($index === 0) continue; // skip header

                $data = [];
                foreach ($fields as $key => $field) {
                    if (!in_array($field, $dbColumns)) {
                        // Skip fields not in database
                        continue;
                    }
                    $value = $row[$key] ?? null;
                    if (($field === 'tanggal' || $field === 'tanggal_kredit' || $field === 'tglfollowup') && $value) {
                        // Convert Excel date to Y-m-d format
                        if (is_numeric($value)) {
                            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                            $value = $date->format('Y-m-d');
                        } else {
                            $value = date('Y-m-d', strtotime($value));
                        }
                    }
                    $data[$field] = $value;
                }

                // Skip row if required field 'nama' is missing or empty
                if (empty($data['nama'])) {
                    \Log::warning("Skipping row $index due to missing 'nama' field");
                    continue;
                }

                try {
                    Lead::create($data);
                } catch (\Exception $e) {
                    \Log::error("Failed to insert row $index: " . $e->getMessage());
                    // Continue processing other rows
                }
            }

            return response()->json(['message' => 'Upload berhasil']);
        } catch (\Exception $e) {
            \Log::error('Excel upload error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json(['error' => 'Upload gagal: ' . $e->getMessage()], 500);
        }
    }
}