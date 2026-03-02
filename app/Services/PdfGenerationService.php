<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Template;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;
use Carbon\Carbon;
use Exception;
use App\Models\Recognition;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;

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

        // 1. Fetch Template (Priority: Frozen Template > Active Template)
        $template = $application->template;

        if (!$template) {
            $template = Template::active()->type('acceptance')->first();
            
            // If we found an active template but this application didn't have one frozen, freeze it now
            if ($template && isset($application->id)) {
                $application->template_id = $template->id;
                $application->save();
            }
        }

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
        $originalString = "||{$application->id}|{$user->id}|" . Carbon::now()->toIso8601String() . "||";
        $this->addSignatureBlock($pdf, $originalString, 25, 240);

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
        // Path to the template (Priority: Frozen Template > Active Template)
        $template = $recognition->template;

        if (!$template) {
            $template = Template::active()->type('recognition')->first();
            
            // If we found an active template but this recognition didn't have one frozen, freeze it now
            if ($template && isset($recognition->id)) {
                $recognition->template_id = $template->id;
                $recognition->save();
            }
        }
        
        $templatePath = $template ? Storage::disk('public')->path($template->file_path) : null;

        if (!$templatePath || !file_exists($templatePath)) {
            throw new Exception("El reconocimiento aún no se encuentra disponible.");
        }

        // Initialize FPDI
        $pdf = new Fpdi();
        $pdf->SetAutoPageBreak(false);

        // Add a page
        $pdf->AddPage('P'); // Portrait
        $pdf->setSourceFile($templatePath);
        
        // Format date and location strings
        $stateName = $user->institution?->state?->name ?? 'Guerrero';
        $currentMonth = Carbon::parse($recognition->sent_at)->isoFormat('MMMM');
        $currentYear = Carbon::parse($recognition->sent_at)->isoFormat('YYYY');
        $dateText = "Chilpancingo de los Bravo, {$stateName}, {$currentMonth} de {$currentYear}";

        // Import page 1
        $tplId = $pdf->importPage(1);

        // A4 Dimensions: 210 width x 297 height. Use both to force no white bottom margins.
        $pdf->useTemplate($tplId, 0, 0, 210, 297);

        // Coordinates based on the updated template
        // 1. Participant Name (Centered)
        $pdf->SetFont('Arial', 'B', 24); 
        $pdf->SetTextColor(80, 80, 80); 
        $pdf->SetXY(0, 134); // Added extra margin underneath the 'A'
        $pdf->Cell(210, 10, iconv('UTF-8', 'ISO-8859-1', mb_strtoupper($user->name)), 0, 1, 'C');

        // 2. Participating Activity (Centered)
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetTextColor(80, 80, 80);
        $pdf->SetXY(20, 153); // Adjusted accordingly
        $announcementName = $recognition->announcement ? $recognition->announcement->name : 'CONVOCATORIA GENERAL';
        $text = "Por su destacada participación como evaluador en la convocatoria:\n" . mb_strtoupper($announcementName);
        $pdf->MultiCell(170, 7, iconv('UTF-8', 'ISO-8859-1', $text), 0, 'C');

        // 3. Signer Information
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(80, 80, 80);
        $pdf->SetXY(0, 225); // Below the golden line
        $pdf->Cell(210, 6, iconv('UTF-8', 'ISO-8859-1', 'Vitervo López Caballero'), 0, 1, 'C');

        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->SetXY(0, 231); // Below the signer name
        $pdf->Cell(210, 6, iconv('UTF-8', 'ISO-8859-1', 'Profesor Investigador'), 0, 1, 'C');

        // 4. City, State, Month, Year
        $pdf->SetFont('Arial', 'B', 10);
        // Yellow-gold color matching the template
        $pdf->SetTextColor(194, 155, 34);
        $pdf->SetXY(0, 242); 
        $pdf->Cell(210, 6, iconv('UTF-8', 'ISO-8859-1', $dateText), 0, 1, 'C');

        // Capture Original String for Signature
        $originalString = "||REC-{$recognition->id}|{$user->id}|" . Carbon::parse($recognition->sent_at)->toIso8601String() . "||";

        // Second Page setup for legal QR and Seal
        $this->addLegalSignaturePage($pdf, $originalString, "Reconocimiento Evaluador", $user->id, $recognition->id);

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
        // 1. Fetch Template (Priority: Frozen Template > Active Template)
        $template = $recognition->template;
        
        if (!$template) {
            $template = Template::active()->type('recognition')->first();
            
            // If we found an active template but this recognition didn't have one frozen, freeze it now
            if ($template && isset($recognition->id)) {
                $recognition->template_id = $template->id;
                $recognition->save();
            }
        }

        // Si no hay plantilla (ni histórica ni activa), fallar
        if (!$template || !Storage::disk('public')->exists($template->file_path)) {
            throw new Exception("El reconocimiento aún no se encuentra disponible.");
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

        // A4 Dimensions: 210 width x 297 height.
        $pdf->useTemplate($tplId, 0, 0, 210, 297);

        // Format date and location strings
        $stateName = $user->institution?->state?->name ?? 'Guerrero';
        $currentMonth = Carbon::parse($recognition->sent_at)->isoFormat('MMMM');
        $currentYear = Carbon::parse($recognition->sent_at)->isoFormat('YYYY');
        $dateText = "Chilpancingo de los Bravo, {$stateName}, {$currentMonth} de {$currentYear}";

        // Coordinates based on updated template
        // 1. Teacher Name (Centered)
        $pdf->SetFont('Arial', 'B', 24);
        $pdf->SetTextColor(80, 80, 80);
        $pdf->SetXY(0, 134); // Added extra margin underneath the 'A'
        $pdf->Cell(210, 10, iconv('UTF-8', 'ISO-8859-1', mb_strtoupper($user->name)), 0, 1, 'C');

        // 2. Announcement (Centered)
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetTextColor(80, 80, 80);
        $pdf->SetXY(20, 153); // Adjusted accordingly
        $announcementName = $recognition->announcement ? $recognition->announcement->name : 'Convocatoria General';
        $text = "Por su destacada e invaluable participación como postulante en la convocatoria:\n" . mb_strtoupper($announcementName);
        $pdf->MultiCell(170, 7, iconv('UTF-8', 'ISO-8859-1', $text), 0, 'C');

        // 3. Signer Information
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(80, 80, 80);
        $pdf->SetXY(0, 225); // Below the golden line
        $pdf->Cell(210, 6, iconv('UTF-8', 'ISO-8859-1', 'Vitervo López Caballero'), 0, 1, 'C');

        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->SetXY(0, 231); // Below the signer name
        $pdf->Cell(210, 6, iconv('UTF-8', 'ISO-8859-1', 'Profesor Investigador'), 0, 1, 'C');

        // 4. Date (Bottom Center)
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetTextColor(194, 155, 34);
        $pdf->SetXY(0, 242);
        $pdf->Cell(210, 6, iconv('UTF-8', 'ISO-8859-1', $dateText), 0, 1, 'C');

        // Capture Original String for Signature
        $originalString = "||DOC-{$recognition->id}|{$user->id}|" . Carbon::parse($recognition->sent_at)->toIso8601String() . "||";

        // Append Legal Page
        $this->addLegalSignaturePage($pdf, $originalString, "Reconocimiento Postulante", $user->id, $recognition->id);

        // Output PDF
        return response($pdf->Output('S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="Reconocimiento_Docente.pdf"');
    }

    /**
     * Creates a secondary page containing the legal QR code and the digital signature seal.
     */
    private function addLegalSignaturePage($pdf, $originalString, $documentType, $userId, $recognitionId)
    {
        $pdf->AddPage('P'); // Add a standard blank A4 portrait page
        
        try {
            $recognition = \App\Models\Recognition::find($recognitionId);
            
            // Check if it already has an identifier, so we don't regenerate on re-download
            if ($recognition && $recognition->identifier) {
                $uniqueIdentifier = $recognition->identifier;
                $digitalSeal = $recognition->digital_seal;
            } else {
                // Generate Unique Identifier
                $year = date('Y');
                $uniqueSuffix = substr(str_shuffle(str_repeat("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ", 5)), 0, 6);
                $typeAcronym = $documentType === 'Reconocimiento Evaluador' ? 'EVAL' : 'DOC';
                $uniqueIdentifier = "REC-{$year}-{$typeAcronym}-{$recognitionId}-{$uniqueSuffix}";
                
                // Generate Seal using SignatureService
                $digitalSeal = $this->signatureService->sign($originalString);

                // Save to Database
                if ($recognition) {
                    $recognition->identifier = $uniqueIdentifier;
                    $recognition->digital_seal = $digitalSeal;
                    $recognition->save();
                }
            }

            $user = \App\Models\User::find($userId);

            // Generate QR Code image (Holding just the Lookup URL)
            $appUrl = config('app.url') ?? 'http://becaslaravel_ad.test:8080';
            $validationUrl = rtrim($appUrl, '/') . "/verify-recognition/" . urlencode($uniqueIdentifier);
            
            $qrData = $validationUrl;
            
            $qrCode = new QrCode(
                data: $qrData,
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: ErrorCorrectionLevel::Low,
                size: 250,
                margin: 2
            );

            $writer = new PngWriter();
            $qrResult = $writer->write($qrCode);

            // Save temporarily to disk to inject into FPDF
            $tempQR = tempnam(sys_get_temp_dir(), 'qr_') . '.png';
            $qrResult->saveToFile($tempQR);

            // Draw a black outline box behind the QR to resemble the prototype
            $pdf->SetDrawColor(0, 0, 0); // Black
            $pdf->SetLineWidth(0.5);
            $pdf->Rect(14.5, 14.5, 46, 46); // Slightly larger than the 45x45 image

            // Left side block: QR Code
            $pdf->Image($tempQR, 15, 15, 45, 45, 'PNG');
            unlink($tempQR); // Cleanup

            // Print the URL below the QR for visual inspection
            $pdf->SetFont('Arial', '', 6);
            $pdf->SetTextColor(50, 50, 50);
            $pdf->SetXY(15, 62);
            $pdf->Cell(45, 3, iconv('UTF-8', 'ISO-8859-1', $validationUrl), 0, 1, 'C');

            // Right side block: Texts
            $startX = 65;
            $startY = 15;

            // Sello Digital Header
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetTextColor(50, 50, 50);
            $pdf->SetXY($startX, $startY);
            $pdf->Cell(0, 4, iconv('UTF-8', 'ISO-8859-1', 'Sello Digital de Verificación:'), 0, 1, 'L');

            // Sello Digital Base64 string - Lighter smaller text
            $pdf->SetFont('Courier', '', 5);
            $pdf->SetTextColor(160, 160, 160);
            $pdf->SetXY($startX, $startY + 5);
            $pdf->MultiCell(130, 2.5, $digitalSeal, 0, 'J');
            
            // Identificador Value (REMOVED FROM PDF, ONLY IN QR)
            // Identificador Header (REMOVED)

            // Leyenda Legal - Adjusted positioning relative to seal
            $currentY = $pdf->GetY() + 8;
            $legalNotice = "La firma electrónica que sustituye a la firma autógrafa del firmante, garantiza la integridad de la constancia y producirá los mismos efectos que las leyes otorgan a los documentos con firma autógrafa.";
            
            $pdf->SetFont('Arial', '', 5);
            $pdf->SetTextColor(160, 160, 160); // Softer color for legal notice
            $pdf->SetXY($startX, $currentY);
            $pdf->MultiCell(130, 2.5, iconv('UTF-8', 'ISO-8859-1', $legalNotice), 0, 'J');

        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error generating QR or Signature: " . $e->getMessage());
            $pdf->SetFont('Arial', 'I', 10);
            $pdf->SetXY(20, 30);
            $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1', '[Error general al procesar la firma electrónica y QR]'), 0, 1, 'L');
        }
    }
}
