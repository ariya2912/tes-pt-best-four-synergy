<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportHelperController extends Controller
{
    public function getAvailableFields()
    {
        // Get columns from leads table
        $columns = DB::getSchemaBuilder()->getColumnListing('leads');

        // Remove id, timestamps if not needed
        $fields = array_filter($columns, function ($col) {
            return !in_array($col, ['id', 'created_at', 'updated_at']);
        });

        return response()->json(array_values($fields));
    }
}
