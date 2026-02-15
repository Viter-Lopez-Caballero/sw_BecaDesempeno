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
        // JOIN: evaluations -> applications -> announcements
        $recognitions = \Illuminate\Support\Facades\DB::table('evaluations')
            ->join('users', 'evaluations.evaluator_id', '=', 'users.id')
            ->join('applications', 'evaluations.application_id', '=', 'applications.id')
            ->join('announcements', 'applications.announcement_id', '=', 'announcements.id')
            ->leftJoin('recognitions', function ($join) {
                $join->on('recognitions.user_id', '=', 'users.id')
                     ->on('recognitions.announcement_id', '=', 'announcements.id');
            })
            ->select(
                'users.id as evaluator_id',
                'users.name as evaluator_name',
                'announcements.id as announcement_id',
                'announcements.name as announcement_name',
                'announcements.created_at as announcement_date',
                \Illuminate\Support\Facades\DB::raw('COUNT(DISTINCT evaluations.id) as applications_reviewed'),
                'recognitions.id as recognition_id',
                \Illuminate\Support\Facades\DB::raw('COALESCE(recognitions.active, 0) as active'),
                'recognitions.sent_at'
            )
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('users.name', 'like', "%{$search}%")
                      ->orWhere('announcements.name', 'like', "%{$search}%");
                });
            })
            ->groupBy(
                'users.id',
                'users.name',
                'announcements.id',
                'announcements.name',
                'announcements.created_at',
                'recognitions.id',
                'recognitions.active',
                'recognitions.sent_at'
            )
            ->orderBy('announcements.created_at', 'desc') // Recent first
            ->orderBy('users.name', 'asc')
            ->paginate($rows)
            ->withQueryString();

        return Inertia::render('Admin/Recognitions/Index', [
            'recognitions' => $recognitions,
            'filters' => $request->all(['search', 'rows']),
        ]);
    }

    /**
     * Toggle recognition status.
     */
    public function toggle(Request $request)
    {
        // $id is recognition_id if exists, or need user_id + announcement_id
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'announcement_id' => 'required|exists:announcements,id',
        ]);

        $userId = $request->user_id;
        $announcementId = $request->announcement_id;

        // Verify evaluator reviewed at least 1 application of this announcement
        $applicationsReviewed = \Illuminate\Support\Facades\DB::table('evaluations')
            ->join('applications', 'evaluations.application_id', '=', 'applications.id')
            ->where('evaluations.evaluator_id', $userId)
            ->where('applications.announcement_id', $announcementId)
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
