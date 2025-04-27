<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function edit()
    {
        $setting = Auth::user()->setting;
        
        // Create a default setting if it doesn't exist
        if (!$setting) {
            $setting = new Setting();
            $setting->user_id = Auth::id();
            $setting->company_name = Auth::user()->name . "'s Business";
            $setting->company_email = Auth::user()->email;
            $setting->default_tax_rate = 0;
            $setting->save();
        }
        
        return view('settings.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|email|max:255',
            'company_phone' => 'nullable|string|max:20',
            'company_address' => 'nullable|string',
            'tax_number' => 'nullable|string|max:50',
            'default_tax_rate' => 'required|numeric|min:0|max:100',
            'company_logo' => 'nullable|image|max:2048',
        ]);

        $setting = Auth::user()->setting;
        
        if (!$setting) {
            $setting = new Setting();
            $setting->user_id = Auth::id();
        }
        
        if ($request->hasFile('company_logo')) {
            // Delete old logo if exists
            if ($setting->company_logo) {
                Storage::disk('public')->delete($setting->company_logo);
            }
            $validated['company_logo'] = $request->file('company_logo')->store('logos', 'public');
        }
        
        $setting->fill($validated);
        $setting->save();
        
        return redirect()->route('settings.edit')
            ->with('success', 'Settings updated successfully.');
    }
}