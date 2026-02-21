<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Template;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;
use Carbon\Carbon;
use Exception;

class PdfGenerationService
{
    /**
     * Genera una carta de aceptación en formato PDF inyectando texto en una plantilla.
     *
     * @param Application $application El modelo de la aplicación aprobada con relaciones cargadas.
     * @return \Illuminate\Http\Response
     * @throws Exception Si no hay plantilla activa.
     */
    public function generateAcceptanceLetter(Application $application)
    {
        $user = $application->user;

        // Path to the template
        $template = Template::active()->type('acceptance')->first();
        $templatePath = $template ? Storage::disk('public')->path($template->file_path) : null;

        if (!$templatePath || !file_exists($templatePath)) {
            throw new Exception("No hay una plantilla de carta de aceptación activa.");
        }

        // Initialize FPDI
        $pdf = new Fpdi();

        // Add a page
        $pdf->AddPage();
        $pdf->setSourceFile($templatePath);
        
        // Import page 1
        $tplId = $pdf->importPage(1);
        
        // Use the imported page
        $pdf->useTemplate($tplId, 0, 0, 210); // A4 Width

        // --- Overlay Text Logic ---
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 0, 0);

        // Coordinates (approximate, adjust based on actual template)
        // Name
        $pdf->SetXY(20, 90); 
        $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', $user->name), 0, 1, 'L');

        // Announcement
        $pdf->SetXY(20, 100);
        $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', $application->announcement->name), 0, 1, 'L');

        // Date
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetXY(20, 50);
        $dateText = Carbon::now()->isoFormat('D [de] MMMM [de] YYYY');
        $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', $dateText), 0, 1, 'R');

        // Output PDF
        return response($pdf->Output('S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="Carta_Aceptacion.pdf"');
    }

    /**
     * Genera un reconocimiento en PDF con o sin plantilla.
     *
     * @param object $recognition
     * @param object $user
     * @return \Illuminate\Http\Response
     */
    public function generateRecognitionPdf($recognition, $user)
    {
        // Path to the template
        $template = Template::active()->type('recognition')->first();
        
        $templatePath = $template ? Storage::disk('public')->path($template->file_path) : null;

        if (!$templatePath || !file_exists($templatePath)) {
            // If template doesn't exist, generate a simple PDF without template
            return $this->generateSimplePdf($recognition, $user);
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
        $dateText = "Emitido el " . Carbon::parse($recognition->sent_at)->isoFormat('D [de] MMMM [de] YYYY');
        $pdf->SetXY(0, 160);
        $pdf->Cell(297, 10, iconv('UTF-8', 'ISO-8859-1', $dateText), 0, 1, 'C');

        // Output PDF
        return response($pdf->Output('S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="Reconocimiento.pdf"');
    }

    /**
     * Generate a simple PDF without template
     */
    private function generateSimplePdf($recognition, $user)
    {
        // Require FPDF
        require_once(base_path('vendor/setasign/fpdf/fpdf.php'));
        
        $pdf = new \FPDF('L', 'mm', 'A4');
        
        // Add a page
        $pdf->AddPage();
        
        // Background with border
        $pdf->SetDrawColor(27, 57, 106);
        $pdf->SetLineWidth(1.5);
        $pdf->Rect(10, 10, 277, 190);
        
        $pdf->SetLineWidth(0.3);
        $pdf->Rect(15, 15, 267, 180);
        
        // Title
        $pdf->SetFont('Arial', 'B', 28);
        $pdf->SetTextColor(27, 57, 106);
        $pdf->SetXY(20, 40);
        $pdf->Cell(257, 15, iconv('UTF-8', 'ISO-8859-1', 'RECONOCIMIENTO'), 0, 1, 'C');
        
        // Subtitle
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->SetXY(20, 58);
        $pdf->Cell(257, 8, iconv('UTF-8', 'ISO-8859-1', 'Se otorga el presente reconocimiento a:'), 0, 1, 'C');
        
        // Evaluator name
        $pdf->SetFont('Arial', 'B', 22);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetXY(20, 75);
        $pdf->Cell(257, 12, iconv('UTF-8', 'ISO-8859-1', $user->name), 0, 1, 'C');
        
        // Line under name
        $pdf->SetLineWidth(0.5);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->Line(80, 93, 217, 93);
        
        // Recognition text
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetTextColor(60, 60, 60);
        $pdf->SetXY(20, 100);
        $pdf->Cell(257, 8, iconv('UTF-8', 'ISO-8859-1', 'Por su valiosa participaci\363n como evaluador en la convocatoria:'), 0, 1, 'C');
        
        // Announcement name
        $pdf->SetFont('Arial', 'B', 15);
        $pdf->SetTextColor(27, 57, 106);
        $pdf->SetXY(20, 112);
        $pdf->MultiCell(257, 8, iconv('UTF-8', 'ISO-8859-1', $recognition->convocatoria_nombre), 0, 'C');
        
        // Additional text
        $pdf->SetFont('Arial', '', 11);
        $pdf->SetTextColor(80, 80, 80);
        $pdf->SetXY(20, 135);
        $pdf->MultiCell(257, 6, iconv('UTF-8', 'ISO-8859-1', 'Agradecemos su compromiso y dedicaci\363n en el proceso de evaluaci\363n, contribuyendo al desarrollo acad\351mico y profesional de nuestros docentes.'), 0, 'C');
        
        // Date
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->SetTextColor(100, 100, 100);
        $dateText = "Emitido el " . Carbon::parse($recognition->sent_at)->isoFormat('D [de] MMMM [de] YYYY');
        $pdf->SetXY(20, 160);
        $pdf->Cell(257, 8, iconv('UTF-8', 'ISO-8859-1', $dateText), 0, 1, 'C');
        
        // Footer
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetTextColor(120, 120, 120);
        $pdf->SetXY(20, 180);
        $pdf->Cell(257, 5, iconv('UTF-8', 'ISO-8859-1', 'Tecnol\363gico Nacional de M\351xico'), 0, 1, 'C');
        
        // Output PDF
        $filename = 'Reconocimiento_' . str_replace(' ', '_', $user->name) . '.pdf';
        return response($pdf->Output('I', $filename), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
    }
}
