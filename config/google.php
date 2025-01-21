<?php
// config/google.php
// return [
//     'client_id' => env('GOOGLE_CLIENT_ID'),
//     'client_secret' => env('GOOGLE_CLIENT_SECRET'),
//     'redirect' => env('GOOGLE_REDIRECT_URI'),
// ];

return [
    'credentials_json' => env('GOOGLE_APPLICATION_CREDENTIALS'),
    'scopes' => [
        'https://www.googleapis.com/auth/drive',
    ],
];