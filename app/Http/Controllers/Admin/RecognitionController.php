<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Recognition;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RecognitionController extends Controller
{
    /**
     * Display a listing of evaluators with their announcements.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $rows = $request->input('rows', 10);

        // Query to get evaluators with their announcements (where they participated)
        $recognitions = \App\Models\Evaluation::withRecognitionDetails()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('users.name', 'like', "%{$search}%")
                        ->orWhere('announcements.name', 'like', "%{$search}%");
                });
            })
            ->paginate($rows)
            ->withQueryString();

        return Inertia::render('Admin/Recognitions/Index', [
            'recognitions' => $recognitions,
            'filters' => $request->all(['search', 'rows', 'sort_field', 'sort_direction']),
        ]);
    }

    /**
     * Toggle recognition status.
     */
    public function toggle(\App\Http\Requests\ToggleRecognitionRequest $request)
    {

        $userId = $request->user_id;
        $announcementId = $request->announcement_id;

        // Verify evaluator reviewed at least 1 application of this announcement
        $applicationsReviewed = \App\Models\Evaluation::where('evaluator_id', $userId)
            ->whereHas('application', function ($query) use ($announcementId) {
                $query->where('announcement_id', $announcementId);
            })
            ->where('status', '!=', 'pending')
            ->count();

        if ($applicationsReviewed === 0) {
            return back()->withErrors(['error' => 'El evaluador no ha revisado solicitudes de esta convocatoria.']);
        }

        // Find or Create Recognition
        $recognition = \App\Models\Recognition::firstOrNew([
            'user_id' => $userId,
            'announcement_id' => $announcementId,
        ]);

        $recognition->active = !$recognition->active;

        if ($recognition->active) {
            $recognition->sent_at = now();
        }

        $recognition->save();

        return back()->with('success', $recognition->active
            ? 'Reconocimiento activado correctamente.'
            : 'Reconocimiento desactivado correctamente.');
    }
}
