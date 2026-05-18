<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsSection;
use App\Models\MediaLibrary;
use App\Models\SeoSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CmsController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Cms/Index', [
            'sections' => CmsSection::all(),
            'seo' => SeoSetting::all(),
            'media_count' => MediaLibrary::count(),
        ]);
    }

    public function updateSection(Request $request, CmsSection $section)
    {
        $request->validate([
            'content' => 'required|array',
            'is_active' => 'boolean',
        ]);

        $section->update($request->only('content', 'is_active'));

        return back()->with('success', 'Section updated successfully.');
    }

    public function updateSeo(Request $request, SeoSetting $seo)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'keywords' => 'nullable|string',
            'og_data' => 'nullable|array',
        ]);

        $seo->update($request->all());

        return back()->with('success', 'SEO settings updated.');
    }
}
