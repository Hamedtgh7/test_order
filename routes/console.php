<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule as FacadesSchedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


FacadesSchedule::command('app:delete-old-orders')->daily();