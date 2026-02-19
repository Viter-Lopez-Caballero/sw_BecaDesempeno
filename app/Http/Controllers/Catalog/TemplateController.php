<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Template;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class TemplateController extends Controller
{
    public function index()
    {
        $templates = Template::latest()->get();
        return Inertia::render('SuperAdmin/Catalog/TemplateModule/Index', [
            'templates' => $templates
        ]);
    }

    public function create(Request $request)
    {
        return Inertia::render('SuperAdmin/Catalog/TemplateModule/Create', [
            'type' => $request->query('type', 'recognition')
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:recognition,acceptance',
            'file' => 'required|file|mimes:pdf|max:10240', // 10MB
        ]);

        $file = $request->file('file');
        $path = $file->store('templates', 'public');
        $originalName = $file->getClientOriginalName();

        Template::create([
            'name' => $request->name,
            'type' => $request->type,
            'file_path' => $path,
            'file_name' => $originalName,
            'is_active' => false, // Default inactive
        ]);

        return redirect()->route('catalog.templates.index')->with('success', 'Plantilla subida correctamente.');
    }

    public function toggleActive(Template $template)
    {
        // If we are activating this template
        if (!$template->is_active) {
            // Deactivate all others of the same type
            Template::where('type', $template->type)
                ->where('id', '!=', $template->id)
                ->update(['is_active' => false]);
            
            $template->is_active = true;
            $template->save();
            
            return redirect()->back()->with('success', 'Plantilla activada correctamente.');
        } else {
            // Deactivating
            $template->is_active = false;
            $template->save();
            return redirect()->back()->with('success', 'Plantilla desactivada.');
        }
    }

    public function destroy(Template $template)
    {
        if (Storage::disk('public')->exists($template->file_path)) {
            Storage::disk('public')->delete($template->file_path);
        }
        
        $template->delete();

        return redirect()->back()->with('success', 'Plantilla eliminada.');
    }

    public function stream(Template $template)
    {
        if (!Storage::disk('public')->exists($template->file_path)) {
            abort(404);
        }
        return response()->file(Storage::disk('public')->path($template->file_path));
    }
    
    public function download(Template $template)
    {
         if (!Storage::disk('public')->exists($template->file_path)) {
            return back()->with('error', 'El archivo no existe.');
        }
        return Storage::disk('public')->download($template->file_path, $template->file_name);
    }
}
