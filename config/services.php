<?php

return [

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*
    |------------------------------------------------------------------
    | Google OAuth (Login JUBEMA)
    |------------------------------------------------------------------
    | Kredensial diambil dari Google Cloud Console > APIs & Services >
    | Credentials > OAuth 2.0 Client ID (tipe: Web application).
    |
    | Authorized redirect URI yang harus didaftarkan:
    |   http://localhost:8000/auth/google/callback
    */
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', 'http://localhost:8000/auth/google/callback'),
    ],

];
