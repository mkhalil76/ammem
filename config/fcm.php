<?php

return [
    'driver' => env('FCM_PROTOCOL', 'http'),
    'log_enabled' => true,

//    FCM_SERVER_KEY=AAAAfaGSYeU:APA91bGBQ3skM0Q3OtjbPNknp06YbN1t9Azyb8cmqjb8j0A2qESJTQDThZK5Is6Us2MSOEypZPQ6Y5FrEazkVC0gIqOiR2dXU5172FH8vPnR301-YBk9FGWOCcLQfZAdb8folKnQoB6H
//    FCM_SENDER_ID=539581637093
    'http' => [
        'server_key' => 'AAAAfaeU:APA91bGBQ3skM0Q3OtjbPNknp06YbN1t9Azyb8cmqjb8j0A2qESJTQDThZK5Is6Us2MSOEypZPQ6Y5FrEazkVC0gIqOiR2dXU5172FH8vPnR301-YBk9FGWOCcLQfZAdb8folKnQoB6H',
        'sender_id' => '539581637093',
        'server_send_url' => 'https://fcm.googleapis.com/fcm/send',
        'server_group_url' => 'https://android.googleapis.com/gcm/notification',
        'timeout' => 30.0, // in second
    ],
];
