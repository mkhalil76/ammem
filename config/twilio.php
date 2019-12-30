<?php

return [

    'twilio' => [

        'default' => 'twilio',

        'connections' => [

            'twilio' => [

                /*
                |--------------------------------------------------------------------------
                | SID
                |--------------------------------------------------------------------------
                |
                | Your Twilio Account SID #
                |
                */

                'sid' => env('TWILIO_ACCOUNT_SID') ?: 'ACd00893d9e5fdea5bed58f9648fd5b5fd',

                /*
                |--------------------------------------------------------------------------
                | Access Token
                |--------------------------------------------------------------------------
                |
                | Access token that can be found in your Twilio dashboard
                |
                */

                'token' => env('TWILIO_TOKEN') ?: 'd4986bd9a01b96a62ca0e021aa1c0d94',

                /*
                |--------------------------------------------------------------------------
                | From Number
                |--------------------------------------------------------------------------
                |
                | The Phone number registered with Twilio that your SMS & Calls will come from
                |
                */

                'from' => '(201) 594-6278',

                /*
                |--------------------------------------------------------------------------
                | Your Twilio VIDEO KEY
                |--------------------------------------------------------------------------
                |
                | TWILIO VIDEO API KEY 
                |
                */

                'key' => env('TWILIO_VIDEO_KEY') ?: 'SK9378be785be4516a34120b44e9259a43',

                /*
                |--------------------------------------------------------------------------
                | Your Twilio VIDEO SECRET
                |--------------------------------------------------------------------------
                |
                | TWILIO VIDEO API SECRET 
                |
                */

                'secret' => env('TWILIO_VIDEO_SECRET') ?: 'nDH7mhw0CtnbjGwI2XPkYQoKTkMFghtX',

                /*
                |--------------------------------------------------------------------------
                | Verify Twilio's SSL Certificates
                |--------------------------------------------------------------------------
                |
                | Allows the client to bypass verifying Twilio's SSL certificates.
                | It is STRONGLY advised to leave this set to true for production environments.
                |
                */

                'ssl_verify' => true,
            ],
        ],
    ],
];
