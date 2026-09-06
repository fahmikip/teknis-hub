<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('backup:database')
    ->dailyAt(config('backup.schedule.database', '02:00'))
    ->onFailure(function () {
        Log::error('Backup database terjadwal gagal.');
    });

Schedule::command('backup:files')
    ->dailyAt(config('backup.schedule.files', '03:00'))
    ->onFailure(function () {
        Log::error('Backup files terjadwal gagal.');
    });

Schedule::command('model:prune')->daily();
