<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index');
    }

    public function updateTheme(Request $request)
    {
        $validated = $request->validate([
            'dark_mode' => ['nullable', 'boolean'],
        ]);

        session([
            'dark_mode' => (bool) ($validated['dark_mode'] ?? false),
        ]);

        return back()->with('success', 'Settings updated.');
    }
}
