<?php

namespace App\Http\Controllers;

use App\Models\ReportTemplate;
use Illuminate\Http\Request;

use App\Models\Lead;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;

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

    public function exportExcel($id)
    {
        $template = ReportTemplate::findOrFail($id);
        $criteria = json_decode($template->criteria, true);
        $fields = json_decode($template->fields, true);

        $query = Lead::query();

        foreach ($criteria as $field => $value) {
            if ($value) {
                $query->where($field, 'like', "%$value%");
            }
        }

        $leads = $query->get($fields);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header
        foreach ($fields as $col => $field) {
            $sheet->setCellValueByColumnAndRow($col + 1, 1, ucfirst($field));
        }

        // Set data
        $rowNum = 2;
        foreach ($leads as $lead) {
            foreach ($fields as $col => $field) {
                $sheet->setCellValueByColumnAndRow($col + 1, $rowNum, $lead->$field);
            }
            $rowNum++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'report_' . $template->id . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        $writer->save('php://output');
        exit;
    }

    public function exportPdf($id)
    {
        $template = ReportTemplate::findOrFail($id);
        $criteria = json_decode($template->criteria, true);
        $fields = json_decode($template->fields, true);

        $query = Lead::query();

        foreach ($criteria as $field => $value) {
            if ($value) {
                $query->where($field, 'like', "%$value%");
            }
        }

        $leads = $query->get($fields);

        $html = '<h1>Report: ' . htmlspecialchars($template->name) . '</h1>';
        $html .= '<table border="1" cellpadding="5" cellspacing="0"><thead><tr>';

        foreach ($fields as $field) {
            $html .= '<th>' . ucfirst($field) . '</th>';
        }
        $html .= '</tr></thead><tbody>';

        foreach ($leads as $lead) {
            $html .= '<tr>';
            foreach ($fields as $field) {
                $html .= '<td>' . htmlspecialchars($lead->$field) . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return $dompdf->stream('report_' . $template->id . '.pdf', ['Attachment' => true]);
    }
}
