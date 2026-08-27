<?php

namespace App\Filament\Reception\Resources\Bookings\Pages;

use App\Filament\Reception\Resources\Bookings\ReceptionBookingResource;
use Filament\Resources\Pages\ListRecords;

class ListReceptionBookings extends ListRecords
{
    protected static string $resource = ReceptionBookingResource::class;
}
