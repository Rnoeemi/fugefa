<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function __invoke(): View
    {
        return view('site.contact', [
            'privacyHref' => '/oldal/adatkezelesi-tajekoztato',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'privacy_accepted' => ['accepted'],
        ], [
            'name.required' => 'A név megadása kötelező.',
            'email.required' => 'Az e-mail cím megadása kötelező.',
            'message.required' => 'Az üzenet megadása kötelező.',
            'privacy_accepted.accepted' => 'Az adatkezelési tájékoztató elfogadása kötelező.',
        ]);

        // Később: notification / mail. Egyelőre session visszajelzés.
        return back()->with('status', 'Üzenetét megkaptuk, hamarosan válaszolunk.');
    }
}
