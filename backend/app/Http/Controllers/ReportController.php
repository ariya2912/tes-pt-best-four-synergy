<?php

namespace App\Http\Controllers;

use App\Models\ReportTemplate;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return ReportTemplate::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string',
            'criteria' => 'required|array',
            'fields'   => 'required|array',
        ]);

        $template = ReportTemplate::create([
            'name'     => $request->name,
            'criteria' => json_encode($request->criteria),
            'fields'   => json_encode($request->fields),
        ]);

        return response()->json($template, 201);
    }

    public function destroy($id)
    {
        ReportTemplate::destroy($id);
        return response()->json(['message' => 'Report deleted']);
    }
}
