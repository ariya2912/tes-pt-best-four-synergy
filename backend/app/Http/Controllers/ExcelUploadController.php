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

        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // skip header

            Lead::create([
                'nama'    => $row[0] ?? '',
                'telepon' => $row[1] ?? '',
                'email'   => $row[2] ?? '',
            ]);
        }

        return response()->json(['message' => 'Upload berhasil']);
    }
}