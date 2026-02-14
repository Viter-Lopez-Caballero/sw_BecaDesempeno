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
    public function getInstitutions()
    {
        try {
            $institutions = \App\Models\Institution::with('state')
                ->orderBy('name', 'asc')
                ->get()
                ->map(function ($institution) {
                    return [
                        'id' => $institution->id,
                        'name' => $institution->name,
                        'state' => $institution->state->name ?? ''
                    ];
                });

            return response()->json($institutions);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al cargar instituciones'], 500);
        }
    }

    public function sendContact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'institucion_id' => 'required|exists:institutions,id', // Table name updated? Yes.
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
            // Get Institution
            $institution = \App\Models\Institution::with('state')->find($request->institucion_id);
            
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'institution' => $institution->name . ' (' . ($institution->state->name ?? '') . ')',
                'message' => $request->message,
            ];

            // Send to Admin
            Mail::to('tecnmpedpd@gmail.com')->send(new ContactFormMail($data));

            // Send Confirmation
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
