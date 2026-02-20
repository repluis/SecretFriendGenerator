<?php

use App\Modules\Fundraising\Infrastructure\Jobs\ProcessDailyFundraisingJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Fundraising: process charges and penalties daily at midnight
Schedule::job(new ProcessDailyFundraisingJob)->dailyAt('00:00');
