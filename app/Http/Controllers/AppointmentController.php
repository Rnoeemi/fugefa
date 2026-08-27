<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function thanks(Appointment $appointment): View
    {
        $appointment->load('worker');

        return view('site.appointments.thanks', [
            'appointment' => $appointment,
        ]);
    }
}
