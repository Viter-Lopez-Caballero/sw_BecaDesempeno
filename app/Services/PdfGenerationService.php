<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Template;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;
use Carbon\Carbon;
use Exception;
use App\Models\Recognition;

class PdfGenerationService
{
    protected $signatureService;

    public function __construct(SignatureService $signatureService)
    {
        $this->signatureService = $signatureService;
    }
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
        $pdf->SetAutoPageBreak(false);

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

        // Coordinates based on the new "Hoja_mem_2026" template
        // Date (Top Right)
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetXY(130, 45);
        $dateText = "Chilpancingo de los Bravo, Guerrero, a " . Carbon::now()->isoFormat('D [de] MMMM [de] YYYY');
        $pdf->Cell(60, 10, iconv('UTF-8', 'ISO-8859-1', $dateText), 0, 1, 'R');

        // Name
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetXY(25, 75); 
        $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', $user->name), 0, 1, 'L');

        // Announcement (Context)
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->SetXY(25, 82);
        $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', 'Participante en: ' . $application->announcement->name), 0, 1, 'L');

        // Signature Block (Bottom)
        $cadenaOriginal = "||{$application->id}|{$user->id}|" . Carbon::now()->toIso8601String() . "||";
        $this->addSignatureBlock($pdf, $cadenaOriginal, 25, 240);

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
        $pdf->SetAutoPageBreak(false);

        // Add a page
        $pdf->AddPage('P'); // Portrait
        $pdf->setSourceFile($templatePath);
        
        // Import page 1
        $tplId = $pdf->importPage(1);
        
        // Use the imported page and place it at point 0,0 with a width of 210mm (A4 Portrait)
        $pdf->useTemplate($tplId, 0, 0, 210);

        // Coordinates based on "Reconocimiento TecNM IT Fed 2026" (Portrait)
        // 1. Evaluator Name (Centered)
        $pdf->SetFont('Arial', 'B', 18); // Reducir tamaño de letra
        $pdf->SetTextColor(50, 50, 50); // Color gris obscuro, no completamente negro
        $pdf->SetXY(0, 114); // Bajar a la posición de 'Nombre Apellido'
        $pdf->Cell(210, 10, iconv('UTF-8', 'ISO-8859-1', mb_strtoupper($user->name)), 0, 1, 'C');

        // 2. Announcement (Centered)
        $pdf->SetFont('Arial', '', 11);
        $pdf->SetTextColor(80, 80, 80);
        $pdf->SetXY(20, 125); // Debajo del nombre, arriba del lorem ipsum
        $text = "Por su destacada participación como evaluador en la convocatoria:\n" . $recognition->convocatoria_nombre;
        $pdf->MultiCell(170, 6, iconv('UTF-8', 'ISO-8859-1', $text), 0, 'C');

        // 3. Date
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(50, 50, 50);
        $dateText = "Chilpancingo de los Bravo, Guerrero, a " . Carbon::parse($recognition->sent_at)->isoFormat('D [de] MMMM [de] YYYY');
        $pdf->SetXY(0, 203); // Justo sobre "Ciudad, Estado, mes de 2026"
        $pdf->Cell(210, 10, iconv('UTF-8', 'ISO-8859-1', $dateText), 0, 1, 'C');

        // Signature Block (Bottom Left) -> Mover a la equina inferior izquierda para no chocar con logos
        $cadenaOriginal = "||REC-{$recognition->id}|{$user->id}|" . Carbon::parse($recognition->sent_at)->toIso8601String() . "||";
        $pdf->SetTextColor(0, 0, 0); // Restaurar negro para la firma
        $this->addSignatureBlock($pdf, $cadenaOriginal, 15, 255, false);

        // Output PDF
        return response($pdf->Output('S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="Reconocimiento.pdf"');
    }

    /**
     * Genera un certificado de reconocimiento para Docentes por su participación.
     *
     * @param Recognition $recognition
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     * @throws Exception Si falla la carga de la plantilla.
     */
    public function generateTeacherRecognitionPdf(Recognition $recognition, $user)
    {
        // 1. Fetch Template
        $template = Template::active()->type('recognition')->first();

        // Si no hay plantilla, fallar o usar generador simple
        if (!$template || !Storage::disk('public')->exists($template->file_path)) {
            return $this->generateSimplePdf($recognition, $user, 'docente');
        }

        $templatePath = Storage::disk('public')->path($template->file_path);

        // 2. Init FPDI
        $pdf = new Fpdi('P', 'mm', 'A4'); // Portrait A4 is typical for certificates
        $pdf->SetAutoPageBreak(false);
        $pdf->AddPage();

        // 3. Set source file
        try {
            $pdf->setSourceFile($templatePath);
        } catch (\Exception $e) {
            throw new Exception("Error al procesar la plantilla PDF: " . $e->getMessage());
        }

        // Import page 1
        $tplId = $pdf->importPage(1);

        // Use the imported page and place it at point 0,0 with a width of 210mm (A4 Portrait)
        $pdf->useTemplate($tplId, 0, 0, 210);

        // Coordinates based on "Reconocimiento TecNM IT Fed 2026"
        // 1. Teacher Name (Centered)
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(50, 50, 50);
        $pdf->SetXY(0, 114); 
        $pdf->Cell(210, 10, iconv('UTF-8', 'ISO-8859-1', mb_strtoupper($user->name)), 0, 1, 'C');

        // 2. Announcement (Centered)
        $pdf->SetFont('Arial', '', 11);
        $pdf->SetTextColor(80, 80, 80);
        $pdf->SetXY(20, 125);
        // Use announcement name
        $announcementName = $recognition->announcement ? $recognition->announcement->name : 'Convocatoria General';
        $text = "Por su destacada participación en la convocatoria:\n" . $announcementName;
        $pdf->MultiCell(170, 6, iconv('UTF-8', 'ISO-8859-1', $text), 0, 'C');

        // 3. Date (Bottom Left)
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(50, 50, 50);
        $dateText = "Chilpancingo de los Bravo, Guerrero, a " . Carbon::parse($recognition->sent_at)->isoFormat('D [de] MMMM [de] YYYY');
        $pdf->SetXY(0, 203);
        $pdf->Cell(210, 10, iconv('UTF-8', 'ISO-8859-1', $dateText), 0, 1, 'C');

        // Signature Block (Bottom Left) -> Mover a la equina inferior izquierda para no chocar con logos
        $cadenaOriginal = "||DOC-{$recognition->id}|{$user->id}|" . Carbon::parse($recognition->sent_at)->toIso8601String() . "||";
        $pdf->SetTextColor(0, 0, 0); // Restaurar negro para la firma
        $this->addSignatureBlock($pdf, $cadenaOriginal, 15, 255, false);

        // Output PDF
        return response($pdf->Output('S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="Reconocimiento_Docente.pdf"');
    }

    /**
     * Generate a simple PDF without template
     */
    private function generateSimplePdf($recognition, $user, $type = 'evaluator')
    {
        // Require FPDF
        require_once(base_path('vendor/setasign/fpdf/fpdf.php'));
        
        $pdf = new \FPDF('L', 'mm', 'A4');
        $pdf->SetAutoPageBreak(false);
        
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

    /**
     * Agrega el bloque de firma electrónica al PDF.
     */
    private function addSignatureBlock($pdf, $cadenaOriginal, $x, $y, $landscape = false)
    {
        try {
            $sello = $this->signatureService->sign($cadenaOriginal);
            $numSerie = $this->signatureService->getCertificateNumber();
            
            $containerWidth = $landscape ? 220 : 160;
            
            $pdf->SetFont('Arial', 'B', 7);
            $pdf->SetXY($x, $y);
            $pdf->Cell(0, 4, iconv('UTF-8', 'ISO-8859-1', 'SELLO DIGITAL VIICYT'), 0, 1, 'L');
            
            $pdf->SetFont('Courier', '', 6);
            $pdf->SetXY($x, $y + 4);
            $pdf->MultiCell($containerWidth, 3, $sello, 0, 'L');
            
            $pdf->SetFont('Arial', 'B', 7);
            $pdf->SetXY($x, $y + 12);
            $pdf->Cell(50, 4, iconv('UTF-8', 'ISO-8859-1', 'NO. SERIE: ') . $numSerie, 0, 0, 'L');
            
            $pdf->SetXY($x + 60, $y + 12);
            $pdf->Cell(50, 4, iconv('UTF-8', 'ISO-8859-1', 'CADENA ORIGINAL: '), 0, 0, 'L');
            
            $pdf->SetFont('Courier', '', 5);
            $pdf->SetXY($x + 85, $y + 12.5);
            $pdf->Cell(0, 3, $cadenaOriginal, 0, 1, 'L');

        } catch (Exception $e) {
            Log::error("Error al firmar documento: " . $e->getMessage());
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->SetXY($x, $y);
            $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', '[Error en Sello Digital]'), 0, 1, 'L');
        }
    }
}
