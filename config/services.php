<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'facebook' => [
        'client_id'     => config('bookdose.facebook_client_id'),
        'client_secret' => config('bookdose.facebook_client_secret'),
        'redirect'      => config('bookdose.facebook_redirect'),
    ],
    'google' => [
	    'client_id'     => config('bookdose.google_client_id'),
	    'client_secret' => config('bookdose.google_client_secret'),
	    'redirect'      => config('bookdose.google_redirect')
	],
    'tkpark' => [
	    'client_id'     => config('bookdose.tkpark_client_id'),
	    'client_secret' => config('bookdose.tkpark_client_secret'),
	    'redirect'      => config('bookdose.tkpark_redirect')
	],

];
