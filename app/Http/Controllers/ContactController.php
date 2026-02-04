<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Institucion;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormMail;
use App\Mail\ContactConfirmationMail;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function getInstituciones()
    {
        try {
            $instituciones = Institucion::with('estado')
                ->orderBy('nombre', 'asc')
                ->get()
                ->map(function ($institucion) {
                    return [
                        'id' => $institucion->id,
                        'nombre' => $institucion->nombre,
                        'estado' => $institucion->estado->nombre ?? ''
                    ];
                });

            return response()->json($instituciones);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al cargar instituciones'], 500);
        }
    }

    public function sendContact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'institucion_id' => 'required|exists:instituciones,id',
            'message' => 'required|string|max:1000',
        ], [
            'name.required' => 'El nombre es obligatorio',
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'El correo electrónico debe ser válido',
            'institucion_id.required' => 'Debes seleccionar una institución',
            'institucion_id.exists' => 'La institución seleccionada no es válida',
            'message.required' => 'El mensaje es obligatorio',
            'message.max' => 'El mensaje no puede exceder 1000 caracteres',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Obtener la institución
            $institucion = Institucion::with('estado')->find($request->institucion_id);
            
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'institucion' => $institucion->nombre . ' (' . ($institucion->estado->nombre ?? '') . ')',
                'message' => $request->message,
            ];

            // Enviar correo al administrador
            Mail::to('tecnmpedpd@gmail.com')->send(new ContactFormMail($data));

            // Enviar correo de confirmación al usuario
            Mail::to($request->email)->send(new ContactConfirmationMail($data));

            return response()->json([
                'success' => true,
                'message' => 'Mensaje enviado correctamente. Nos pondremos en contacto contigo pronto.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el mensaje. Por favor, intenta de nuevo más tarde.'
            ], 500);
        }
    }
}
