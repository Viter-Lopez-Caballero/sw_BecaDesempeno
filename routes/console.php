<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

Schedule::command('convocatoria:update-status')->daily();
Schedule::command('notifications:send-weekly')->weeklyOn(5, '9:00'); // Viernes a las 9:00 AM
