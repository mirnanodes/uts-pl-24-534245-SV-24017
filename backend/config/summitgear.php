<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Demo administrator
    |--------------------------------------------------------------------------
    |
    | Seeded account used to log in to the API and the Vue frontend.
    | Never commit real credentials: set ADMIN_PASSWORD in the .env file
    | (or GitHub Secrets for the CI deploy job).
    |
    */

    'admin' => [
        'name' => env('ADMIN_NAME', 'Admin SummitGear'),
        'email' => env('ADMIN_EMAIL', 'admin@summitgear.test'),
        'password' => env('ADMIN_PASSWORD', 'password'),
    ],

];
