<?php

namespace App\Http\Controllers\Evaluator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

class RecognitionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $rows = $request->input('rows', 10);
        $search = $request->input('search');

        // Fetch active recognitions for the current user
        $recognitions = DB::table('recognitions')
            ->join('announcements', 'recognitions.announcement_id', '=', 'announcements.id')
            ->where('recognitions.user_id', $user->id)
            ->where('recognitions.active', true)
            ->select(
                'recognitions.id',
                'recognitions.sent_at',
                'announcements.name as announcement_name',
                'announcements.created_at as announcement_date'
            )
            ->when($search, function ($query, $search) {
                $query->where('announcements.name', 'like', "%{$search}%");
            })
            ->orderBy('recognitions.sent_at', 'desc')
            ->paginate($rows)
            ->withQueryString();

        // Transform data
        $recognitions->getCollection()->transform(function ($item) {
            return [
                'id' => $item->id,
                'announcement' => $item->announcement_name,
                'date' => \Carbon\Carbon::parse($item->sent_at)->isoFormat('D [de] MMMM [de] YYYY'),
                'year' => \Carbon\Carbon::parse($item->announcement_date)->year,
            ];
        });

        return Inertia::render('Evaluator/Recognitions/Index', [
            'recognitions' => $recognitions,
            'filters' => $request->all(['search', 'rows']),
        ]);
    }

    public function download($id)
    {
        $user = Auth::user();
        
        $recognition = DB::table('recognitions')
            ->join('announcements', 'recognitions.announcement_id', '=', 'announcements.id')
            ->where('recognitions.id', $id)
            ->where('recognitions.user_id', $user->id)
            ->where('recognitions.active', true)
            ->select('recognitions.*', 'announcements.name as convocatoria_nombre')
            ->first();

        if (!$recognition) {
            abort(404);
        }

        // Path to the template
        $templatePath = storage_path('app/templates/reconocimiento_template.pdf');

        if (!file_exists($templatePath)) {
            abort(500, 'La plantilla de reconocimiento no fue encontrada.');
        }

        // Initialize FPDI
        $pdf = new Fpdi();

        // Add a page
        $pdf->AddPage('L'); // Landscape
        $pdf->setSourceFile($templatePath);
        
        // Import page 1
        $tplId = $pdf->importPage(1);
        
        // Use the imported page and place it at point 0,0 with a width of 297mm (A4 Landscape)
        $pdf->useTemplate($tplId, 0, 0, 297);

        // --- Overlay Text Logic ---
        // Fonts
        $pdf->SetFont('Arial', 'B', 24);
        $pdf->SetTextColor(0, 0, 0); // Black

        // 1. Evaluator Name (Centered)
        // Adjust Y coordinate based on the template design
        $pdf->SetXY(0, 90); 
        $pdf->Cell(297, 10, iconv('UTF-8', 'ISO-8859-1', $user->name), 0, 1, 'C');

        // 2. Reason / Announcement Text (Centered below name)
        $pdf->SetFont('Arial', '', 14);
        $text = "Por su valiosa participación como evaluador en la convocatoría:";
        $pdf->SetXY(0, 110);
        $pdf->Cell(297, 10, iconv('UTF-8', 'ISO-8859-1', $text), 0, 1, 'C');

        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetXY(0, 120);
        // Use announcement name
        $pdf->Cell(297, 10, iconv('UTF-8', 'ISO-8859-1', $recognition->convocatoria_nombre), 0, 1, 'C');

        // 3. Date (Bottom Right or Centered)
        $pdf->SetFont('Arial', 'I', 12);
        $dateText = "Emitido el " . \Carbon\Carbon::parse($recognition->sent_at)->isoFormat('D [de] MMMM [de] YYYY');
        $pdf->SetXY(0, 160);
        $pdf->Cell(297, 10, iconv('UTF-8', 'ISO-8859-1', $dateText), 0, 1, 'C');

        // Output PDF
        return response($pdf->Output('S'), 200)
            ->header('Content-Type', 'application/pdf');
    }
}
