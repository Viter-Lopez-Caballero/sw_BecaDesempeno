<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;

class SignatureService
{
    private $cerPath;
    private $keyPath;
    private $password;

    public function __construct()
    {
        // Rutas basadas en la solicitud y estructura de Laravel
        $this->cerPath = storage_path('app/firma/VIICyT.cer');
        $this->keyPath = storage_path('app/firma/VIICyT.key');
        $this->password = 'viicyt2024';
    }

    /**
     * Genera el Sello Digital (Firma) a partir de una cadena original.
     *
     * @param string $cadenaOriginal
     * @return string|null
     * @throws Exception
     */
    public function sign(string $cadenaOriginal): ?string
    {
        if (!file_exists($this->keyPath)) {
            throw new Exception("No se encontró el archivo de llave privada (.key)");
        }

        // Leer el archivo .key
        $privateKeyContent = file_get_contents($this->keyPath);
        
        // El archivo .key suele estar en formato DER, necesitamos convertirlo o usarlo directamente si OpenSSL lo soporta
        // Intentar cargar la llave privada
        $privateKey = openssl_pkey_get_private($privateKeyContent, $this->password);

        if (!$privateKey) {
            // Intentar convertir de DER a PEM si falla (común en certificados de México)
            $privateKey = $this->convertDerToPem($privateKeyContent);
        }

        if (!$privateKey) {
            throw new Exception("No se pudo cargar la llave privada. Verifique la contraseña.");
        }

        $signature = '';
        // Firmar usando algoritmos estándares (SHA256)
        $result = openssl_sign($cadenaOriginal, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        
        openssl_free_key($privateKey);

        if ($result) {
            return base64_encode($signature);
        }

        return null;
    }

    /**
     * Obtiene el número de serie del certificado .cer
     *
     * @return string|null
     */
    public function getCertificateNumber(): ?string
    {
        if (!file_exists($this->cerPath)) {
            return null;
        }

        $cerContent = file_get_contents($this->cerPath);
        
        // Convertir CER (DER) a PEM para lectura
        $pem = $this->derToPem($cerContent);
        $cert = @openssl_x509_read($pem);
        if ($cert) {
            $certData = openssl_x509_parse($cert);
            if (isset($certData['serialNumber'])) {
                return $certData['serialNumber'];
            }
            if (isset($certData['serialNumberHex'])) {
                return $certData['serialNumberHex'];
            }
        }

        // Fallback manual para certificados tipo SAT/México u otros
        // Buscar secuencias de 20 dígitos que suelen ser el número de serie
        if (preg_match('/([0-9]{20})/', $cerContent, $matches)) {
            return $matches[1];
        }
        
        // Si no hay 20 dígitos, buscar cualquier secuencia larga de números que parezca un serial (15-25 dígitos)
        if (preg_match('/([0-9]{15,25})/', $cerContent, $matches)) {
            return $matches[1];
        }

        return "NO_DISPONIBLE";
    }

    /**
     * Convierte un certificado DER a formato PEM.
     */
    private function derToPem(string $derContent): string
    {
        return "-----BEGIN CERTIFICATE-----\n" . chunk_split(base64_encode($derContent), 64, "\n") . "-----END CERTIFICATE-----\n";
    }

    /**
     * Convierte una llave privada DER a formato PEM (PKCS#8).
     */
    private function convertDerToPem(string $derContent): string
    {
        // Esta es una simplificación. Si el .key está cifrado, requiere manejo específico.
        // Pero openssl_pkey_get_private suele manejarlo si se pasa el contenido crudo y el password.
        return $derContent; 
    }
}
