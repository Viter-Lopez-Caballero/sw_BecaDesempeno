<?php

namespace App\Http\Controllers\Evaluador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

class ReconocimientoController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $rows = $request->input('rows', 10);
        $search = $request->input('search');

        // Fetch active recognitions for the current user
        $reconocimientos = DB::table('reconocimientos')
            ->join('convocatorias', 'reconocimientos.convocatoria_id', '=', 'convocatorias.id')
            ->where('reconocimientos.user_id', $user->id)
            ->where('reconocimientos.activo', true)
            ->select(
                'reconocimientos.id',
                'reconocimientos.enviado_at',
                'convocatorias.nombre as convocatoria_nombre',
                'convocatorias.created_at as convocatoria_fecha'
            )
            ->when($search, function ($query, $search) {
                $query->where('convocatorias.nombre', 'like', "%{$search}%");
            })
            ->orderBy('reconocimientos.enviado_at', 'desc')
            ->paginate($rows)
            ->withQueryString();

        // Transform data
        $reconocimientos->getCollection()->transform(function ($item) {
            return [
                'id' => $item->id,
                'convocatoria' => $item->convocatoria_nombre,
                'fecha' => \Carbon\Carbon::parse($item->enviado_at)->isoFormat('D [de] MMMM [de] YYYY'),
                'anio' => \Carbon\Carbon::parse($item->convocatoria_fecha)->year,
            ];
        });

        return Inertia::render('Evaluador/Reconocimientos/Index', [
            'reconocimientos' => $reconocimientos,
            'filters' => $request->all(['search', 'rows']),
        ]);
    }

    public function download($id)
    {
        $user = Auth::user();
        
        $reconocimiento = DB::table('reconocimientos')
            ->join('convocatorias', 'reconocimientos.convocatoria_id', '=', 'convocatorias.id')
            ->where('reconocimientos.id', $id)
            ->where('reconocimientos.user_id', $user->id)
            ->where('reconocimientos.activo', true)
            ->select('reconocimientos.*', 'convocatorias.nombre as convocatoria_nombre')
            ->first();

        if (!$reconocimiento) {
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

        // 2. Reason / Convocatoria Text (Centered below name)
        $pdf->SetFont('Arial', '', 14);
        $text = "Por su valiosa participación como evaluador en la convocatoría:";
        $pdf->SetXY(0, 110);
        $pdf->Cell(297, 10, iconv('UTF-8', 'ISO-8859-1', $text), 0, 1, 'C');

        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetXY(0, 120);
        $pdf->Cell(297, 10, iconv('UTF-8', 'ISO-8859-1', $reconocimiento->convocatoria_nombre), 0, 1, 'C');

        // 3. Date (Bottom Right or Centered)
        $pdf->SetFont('Arial', 'I', 12);
        $dateText = "Emitido el " . \Carbon\Carbon::parse($reconocimiento->enviado_at)->isoFormat('D [de] MMMM [de] YYYY');
        $pdf->SetXY(0, 160);
        $pdf->Cell(297, 10, iconv('UTF-8', 'ISO-8859-1', $dateText), 0, 1, 'C');

        // Output PDF
        return response($pdf->Output('S'), 200)
            ->header('Content-Type', 'application/pdf');
    }
}
