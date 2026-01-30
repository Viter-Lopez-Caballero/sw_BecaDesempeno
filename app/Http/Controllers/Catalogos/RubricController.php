<?php

namespace App\Http\Controllers\Catalogos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalogos\StoreRubricRequest;
use App\Http\Requests\Catalogos\UpdateRubricRequest;
use App\Http\Resources\Catalogos\RubricResource;
use App\Models\Rubric;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class RubricController extends Controller
{
    public function index()
    {
        $rubrics = Rubric::withCount('questions')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return Inertia::render('Catalogos/Rubrics/Index', [
            'rubrics' => $rubrics,
        ]);
    }

    public function create()
    {
        return Inertia::render('Catalogos/Rubrics/Create', [
            'title' => 'Crear Rúbrica',
            'routeName' => 'catalogo.rubrics.',
        ]);
    }

    public function store(StoreRubricRequest $request)
    {
        // Check for active rubric if trying to activate this one
        if ($request->is_active) {
            if (Rubric::where('is_active', true)->exists()) {
                // Return error if one is already active
                return back()->withErrors(['is_active' => 'Ya existe una rúbrica activa. Desactívala primero.']);
            }
        }

        DB::transaction(function () use ($request) {
            $rubric = Rubric::create([
                'title' => $request->title,
                'is_active' => $request->is_active ?? false,
            ]);

            if ($request->questions) {
                foreach ($request->questions as $qData) {
                    $question = $rubric->questions()->create(['text' => $qData['text']]);
                    if (isset($qData['options'])) {
                        foreach ($qData['options'] as $oData) {
                            $question->options()->create([
                                'text' => $oData['text'],
                                'score' => $oData['score'],
                            ]);
                        }
                    }
                }
            }
        });

        return redirect()->route('catalogo.rubrics.index')->with('success', 'Rúbrica creada correctamente.');
    }

    public function edit(Rubric $rubric)
    {
        $rubric->load('questions.options');

        return Inertia::render('Catalogos/Rubrics/Edit', [
            'title' => 'Editar Rúbrica',
            'routeName' => 'catalogo.rubrics.',
            'rubric' => new RubricResource($rubric),
        ]);
    }

    public function update(UpdateRubricRequest $request, Rubric $rubric)
    {
        // Check for active rubric if trying to activate this one AND it wasn't already active
        if ($request->is_active && !$rubric->is_active) {
            if (Rubric::where('is_active', true)->where('id', '!=', $rubric->id)->exists()) {
                 return back()->withErrors(['is_active' => 'Ya existe una rúbrica activa. Desactívala primero.']);
            }
        }

        DB::transaction(function () use ($request, $rubric) {
            $rubric->update([
                'title' => $request->title,
                'is_active' => $request->is_active,
            ]);

            // Sync Questions (Simple approach: Delete all and recreate. 
            // Better approach: Update existing, Create new, Delete missing.
            // Let's go with the better approach for ID stability if needed, 
            // but for simplicity and robustness given nested structure, delete/recreate 
            // is safer for ensuring consistency unless ID stability is critical.)
            // User requested "actions... edit... delete... add question... add answer".
            // Since we receive the full structure, sync is best.

            // Get IDs of current questions to track deletions
            $currentQuestionIds = $rubric->questions()->pluck('id')->toArray();
            $incomingQuestionIds = [];

            if ($request->questions) {
                foreach ($request->questions as $qData) {
                    if (isset($qData['id'])) {
                        $incomingQuestionIds[] = $qData['id'];
                        $question = $rubric->questions()->find($qData['id']);
                        if ($question) {
                            $question->update(['text' => $qData['text']]);
                            
                            // Sync Options
                            $currentOptionIds = $question->options()->pluck('id')->toArray();
                            $incomingOptionIds = [];

                            if (isset($qData['options'])) {
                                foreach ($qData['options'] as $oData) {
                                    if (isset($oData['id'])) {
                                        $incomingOptionIds[] = $oData['id'];
                                        $question->options()->where('id', $oData['id'])->update([
                                            'text' => $oData['text'],
                                            'score' => $oData['score'],
                                        ]);
                                    } else {
                                        $newOption = $question->options()->create([
                                            'text' => $oData['text'],
                                            'score' => $oData['score'],
                                        ]);
                                        $incomingOptionIds[] = $newOption->id;
                                    }
                                }
                            }
                            // Delete removed options
                            $question->options()->whereNotIn('id', $incomingOptionIds)->delete();
                        }
                    } else {
                        // New Question
                        $question = $rubric->questions()->create(['text' => $qData['text']]);
                        $incomingQuestionIds[] = $question->id;

                        if (isset($qData['options'])) {
                            foreach ($qData['options'] as $oData) {
                                $question->options()->create([
                                    'text' => $oData['text'],
                                    'score' => $oData['score'],
                                ]);
                            }
                        }
                    }
                }
            }
            // Delete removed questions
            $rubric->questions()->whereNotIn('id', $incomingQuestionIds)->delete();
        });

        return redirect()->route('catalogo.rubrics.index')->with('success', 'Rúbrica actualizada correctamente.');
    }

    public function destroy(Rubric $rubric)
    {
        $rubric->delete();
        return redirect()->route('catalogo.rubrics.index')->with('success', 'Rúbrica eliminada correctamente.');
    }

    public function toggleActive(Rubric $rubric)
    {
        if (!$rubric->is_active) {
            if (Rubric::where('is_active', true)->exists()) {
                return back()->withErrors(['error' => 'Ya existe una rúbrica activa. Desactívala primero para activar esta.']);
            }
            $rubric->update(['is_active' => true]);
        } else {
            $rubric->update(['is_active' => false]);
        }

        return back();
    }
}
