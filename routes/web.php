<?php

use App\Http\Controllers\AccommodationController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\IcalController;
use App\Http\Controllers\SiteBuilderMediaController;
use App\Http\Controllers\SitePageController;
use App\Livewire\SiteGrapesBuilder;
use Illuminate\Support\Facades\Route;

Route::get('/', [SitePageController::class, 'home'])->name('home');
Route::get('/oldal/{slug}', [SitePageController::class, 'show'])->name('site-pages.show');

Route::middleware(['auth'])->prefix('site-builder')->group(function (): void {
    Route::get('/page/{page}', SiteGrapesBuilder::class)->name('site-builder.page');
    Route::get('/header', SiteGrapesBuilder::class)->name('site-builder.header');
    Route::get('/footer', SiteGrapesBuilder::class)->name('site-builder.footer');
    Route::get('/media', [SiteBuilderMediaController::class, 'index'])->name('site-builder.media.index');
    Route::post('/media', [SiteBuilderMediaController::class, 'store'])->name('site-builder.media.store');
});

Route::middleware('module:accommodation')->group(function (): void {
    Route::get('/szallasok', [AccommodationController::class, 'index'])->name('accommodations.index');
    Route::get('/szallasok/{accommodation:slug}', [AccommodationController::class, 'show'])->name('accommodations.show');
    Route::get('/szallasok/{accommodation:slug}/availability', [AccommodationController::class, 'availability'])
        ->name('accommodations.availability');

    Route::get('/foglalas-panel/foglalas-koszonjuk/{booking}', [BookingController::class, 'thanks'])->name('booking.thanks');

    Route::get('/ical/{token}.ics', [IcalController::class, 'export'])->name('ical.export');
});

Route::middleware('module:appointment')->group(function (): void {
    // A foglaló UI a Filament appointment-booking panelen él (/idopontfoglalas).
    Route::get('/idopontfoglalas-koszonjuk/{appointment}', [AppointmentController::class, 'thanks'])
        ->name('appointments.thanks');
});

Route::get('/kapcsolat', ContactController::class)->name('contact');
Route::post('/kapcsolat', [ContactController::class, 'store'])->name('contact.store');
