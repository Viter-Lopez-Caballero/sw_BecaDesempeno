<?php

namespace App\Services;

use App\Models\Renapo;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurpService
{
    private string $apiToken;
    private string $apiUrl;

    public function __construct()
    {
        $this->apiToken = 'ead4c15a-98c1-4910-99c0-e122f070a1cf';
        $this->apiUrl = 'https://apimarket.mx/api/renapo/grupo/valida-curp';
    }

    /**
     * Busca datos por CURP, primero en caché local (renapohistory) y luego en API
     */
    public function buscarPorCurp(string $curp): ?array
    {
        // Limpiar y validar formato de CURP
        $curp = strtoupper(trim($curp));
        
        if (!$this->validarFormatoCurp($curp)) {
            return null;
        }

        // Buscar primero en la base de datos local (caché)
        $renapo = Renapo::where('curp', $curp)->first();
        
        if ($renapo) {
            return [
                'curp' => $renapo->curp,
                'nombres' => $renapo->nombres,
                'apellidoPaterno' => $renapo->apellidoPaterno,
                'apellidoMaterno' => $renapo->apellidoMaterno,
                'source' => 'cache'
            ];
        }

        // Si no está en caché, consultar la API
        return $this->consultarApi($curp);
    }

    /**
     * Consulta la API de APIMarket y guarda en caché
     */
    private function consultarApi(string $curp): ?array
    {
        try {
            // Construir URL completa con el CURP como query parameter
            $url = $this->apiUrl . '?curp=' . $curp;
            
            \Log::info('🌐 Llamando a APIMarket RENAPO', [
                'url' => $url,
                'method' => 'POST',
                'curp' => $curp
            ]);
            
            $response = Http::timeout(30)
                ->withoutVerifying() // Desactiva verificación SSL para desarrollo
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->apiToken,
                ])
                ->post($url);

            if ($response->successful()) {
                $data = $response->json();
                
                \Log::info('✅ Respuesta exitosa de APIMarket', [
                    'status' => $response->status(),
                    'data' => $data
                ]);
                
                // Verificar que la respuesta sea exitosa según APIMarket
                if (isset($data['success']) && $data['success'] === true && isset($data['data'])) {
                    $resultado = [
                        'curp' => $data['data']['curp'] ?? $curp,
                        'nombres' => $data['data']['nombres'] ?? '',
                        'apellidoPaterno' => $data['data']['apellidoPaterno'] ?? '',
                        'apellidoMaterno' => $data['data']['apellidoMaterno'] ?? '',
                        'source' => 'api'
                    ];

                    // Guardar en la base de datos para futuras consultas
                    Renapo::create($resultado);

                    return $resultado;
                }
            }

            Log::warning('CURP no encontrado en APIMarket', [
                'curp' => $curp,
                'status' => $response->status(),
                'response' => $response->json()
            ]);
            return null;

        } catch (\Exception $e) {
            Log::error('Error consultando API de CURP', [
                'curp' => $curp,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Valida el formato básico de una CURP
     */
    private function validarFormatoCurp(string $curp): bool
    {
        // CURP debe tener 18 caracteres alfanuméricos
        return preg_match('/^[A-Z]{4}[0-9]{6}[HM][A-Z]{5}[0-9A-Z][0-9]$/', $curp) === 1;
    }
}
