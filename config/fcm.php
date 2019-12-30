<?php

return [
    'driver' => env('FCM_PROTOCOL', 'http'),
    'log_enabled' => true,

//    FCM_SERVER_KEY=AAAAfaGSYeU:APA91bGBQ3skM0Q3OtjbPNknp06YbN1t9Azyb8cmqjb8j0A2qESJTQDThZK5Is6Us2MSOEypZPQ6Y5FrEazkVC0gIqOiR2dXU5172FH8vPnR301-YBk9FGWOCcLQfZAdb8folKnQoB6H
//    FCM_SENDER_ID=539581637093

    'http' => [
        'server_key' => 'AAAAqMQLNdY:APA91bFY9-4NWyfokyVbxkZc1B3_8d4NFLIQoQPJoVdrDiT0OCjzg56fXhqTAkZToT4gbs9jWwolTOh5mR9bnvxUC3KhR8CyR_GSZ2ZLM8ZVa-cse42q976QiXexRWMC9NnIMD09g1vR',
        'sender_id' => '724843574742',
        'server_send_url' => 'https://fcm.googleapis.com/fcm/send',
        'server_group_url' => 'https://android.googleapis.com/gcm/notification',
        'timeout' => 30.0, // in second
    ],
];
