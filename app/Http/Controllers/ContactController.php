<?php

namespace App\Http\Controllers;

use App\Services\ContactMailService;
use App\Services\RecaptchaVerifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function __invoke(): View
    {
        return view('site.contact', [
            'privacyHref' => config('seo.privacy_url', '/oldal/adatkezelesi-tajekoztato'),
        ]);
    }

    public function store(Request $request, ContactMailService $mailer, RecaptchaVerifier $recaptcha): RedirectResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'message' => ['required', 'string', 'max:5000'],
            'privacy_accepted' => ['accepted'],
        ];

        $messages = [
            'name.required' => 'A név megadása kötelező.',
            'email.required' => 'Az e-mail cím megadása kötelező.',
            'message.required' => 'Az üzenet megadása kötelező.',
            'privacy_accepted.accepted' => 'Az adatkezelési tájékoztató elfogadása kötelező.',
            'g-recaptcha-response.required' => 'Kérjük, erősítse meg, hogy nem robot.',
        ];

        if ($recaptcha->isEnabled()) {
            $rules['g-recaptcha-response'] = ['required', 'string'];
        }

        $data = $request->validate($rules, $messages);

        if ($recaptcha->isEnabled() && ! $recaptcha->verify(
            $request->input('g-recaptcha-response'),
            $request->ip(),
        )) {
            return back()
                ->withInput()
                ->withErrors(['g-recaptcha-response' => 'A robotellenőrzés sikertelen. Kérjük, próbálja újra.']);
        }

        $sent = $mailer->send([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'message' => $data['message'],
        ]);

        if (! $sent) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Az üzenet küldése jelenleg nem sikerült. Kérjük, próbálja újra később, vagy írjon nekünk közvetlenül e-mailben.']);
        }

        return back()->with('status', 'Üzenetét megkaptuk, hamarosan válaszolunk.');
    }
}
