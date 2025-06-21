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
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'Tidak ada file dikirim.'], 400);
        }

        $file = $request->file('file');

        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $header = $rows[0];
        $fields = array_map('strtolower', $header);

        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // skip header

            $data = [];
            foreach ($fields as $key => $field) {
                $data[$field] = $row[$key] ?? null;
            }

            Lead::create($data);
        }

        return response()->json(['message' => 'Upload berhasil']);
    }
}