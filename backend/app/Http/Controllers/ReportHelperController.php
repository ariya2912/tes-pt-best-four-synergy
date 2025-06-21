<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportHelperController extends Controller
{
    public function getAvailableFields()
    {
        // Define field metadata manually for demonstration
        $fields = [
            ['name' => 'nama', 'type' => 'string'],
            ['name' => 'telepon', 'type' => 'string'],
            ['name' => 'email', 'type' => 'string'],
            ['name' => 'tipe', 'type' => 'dropdown', 'options' => ['Sedan', 'SUV', 'Truck', 'Motorcycle']],
            ['name' => 'warna', 'type' => 'string'],
            ['name' => 'no_hp', 'type' => 'string'],
            ['name' => 'tanggal', 'type' => 'date'],
        ];

        return response()->json($fields);
    }
}
