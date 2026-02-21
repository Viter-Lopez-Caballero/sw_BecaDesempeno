<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Template;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Services\TemplateService;
use App\Http\Requests\Catalogos\StoreTemplateRequest;

class TemplateController extends Controller
{
    protected TemplateService $templateService;
    protected FileService $fileService;

    public function __construct(TemplateService $templateService, FileService $fileService)
    {
        $this->templateService = $templateService;
        $this->fileService = $fileService;
    }

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

    public function store(StoreTemplateRequest $request)
    {
        $this->templateService->createTemplate($request->validated(), $request->file('file'));

        return redirect()->route('catalog.templates.index')->with('success', 'Plantilla subida correctamente.');
    }

    public function toggleActive(Template $template)
    {
        $isActive = $this->templateService->toggleActiveStatus($template);
        
        $message = $isActive ? 'Plantilla activada correctamente.' : 'Plantilla desactivada.';
        return redirect()->back()->with('success', $message);
    }

    public function destroy(Template $template)
    {
        $this->templateService->deleteTemplate($template);

        return redirect()->back()->with('success', 'Plantilla eliminada.');
    }

    public function stream(Template $template)
    {
        return $this->fileService->responseFile($template->file_path);
    }
    
    public function download(Template $template)
    {
        return $this->fileService->download($template->file_path, $template->file_name);
    }
}
