<?php

namespace App\Services;

use App\Models\Template;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class TemplateService
{
    /**
     * Store a new template in storage and database.
     */
    public function createTemplate(array $data, \Illuminate\Http\UploadedFile $file): Template
    {
        return DB::transaction(function () use ($data, $file) {
            $path = $file->store('templates', 'public');
            $originalName = $file->getClientOriginalName();

            return Template::create([
                'name' => $data['name'],
                'type' => $data['type'],
                'file_path' => $path,
                'file_name' => $originalName,
                'is_active' => false, // Default inactive
            ]);
        });
    }

    /**
     * Toggle the active status of a template, ensuring only one is active per type.
     */
    public function toggleActiveStatus(Template $template): bool
    {
        return DB::transaction(function () use ($template) {
            if (!$template->is_active) {
                // Deactivate all others of the same type
                Template::where('type', $template->type)
                    ->where('id', '!=', $template->id)
                    ->update(['is_active' => false]);
                
                $template->is_active = true;
                $template->save();
                return true; // Now active
            } else {
                // Deactivating
                $template->is_active = false;
                $template->save();
                return false; // Now inactive
            }
        });
    }

    /**
     * Delete a template from storage and database.
     */
    public function deleteTemplate(Template $template): void
    {
        DB::transaction(function () use ($template) {
            if (Storage::disk('public')->exists($template->file_path)) {
                Storage::disk('public')->delete($template->file_path);
            }
            
            $template->delete();
        });
    }
}
