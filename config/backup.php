<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Backup Storage Path
    |--------------------------------------------------------------------------
    |
    | Path where backups are stored. Relative to storage/app/.
    |
    */

    'path' => env('BACKUP_PATH', 'backups'),

    /*
    |--------------------------------------------------------------------------
    | Retention (days)
    |--------------------------------------------------------------------------
    |
    | Number of days to keep backups before automatic cleanup.
    |
    */

    'keep_days' => (int) env('BACKUP_KEEP_DAYS', 7),

    /*
    |--------------------------------------------------------------------------
    | Schedule
    |--------------------------------------------------------------------------
    |
    | When automatic backups run (defined in routes/console.php).
    |
    */

    'schedule' => [
        'database' => '02:00',
        'files' => '03:00',
    ],

];
