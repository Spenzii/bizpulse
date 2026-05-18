<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CompanySettingsController extends Controller
{
    public function index()
    {
        return Inertia::render('Settings/Index', [
            'company' => Auth::user()->company,
        ]);
    }

    public function update(Request $request)
    {
        $company = Auth::user()->company;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:50',
            'logo' => 'nullable|image|max:2048', // 2MB Max
        ]);

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($company->logo_path) {
                Storage::disk('public')->delete($company->logo_path);
            }

            $path = $request->file('logo')->store('logos', 'public');
            $validated['logo_path'] = $path;
        }

        $company->update($validated);

        return back()->with('success', 'Company settings updated successfully.');
    }
}
