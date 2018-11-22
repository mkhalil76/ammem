<?php

return [
    'driver' => env('FCM_PROTOCOL', 'http'),
    'log_enabled' => true,

//    FCM_SERVER_KEY=AAAAfaGSYeU:APA91bGBQ3skM0Q3OtjbPNknp06YbN1t9Azyb8cmqjb8j0A2qESJTQDThZK5Is6Us2MSOEypZPQ6Y5FrEazkVC0gIqOiR2dXU5172FH8vPnR301-YBk9FGWOCcLQfZAdb8folKnQoB6H
//    FCM_SENDER_ID=539581637093
    'http' => [
        'server_key' => 'AAAATuXS3GM:APA91bEg2oHa4sAjzyfRz-MuDLmzK6GPqwevFPZ1KnsHAfzIwj3lnHpeZDqOvOPDB_enyHcEel5BLZA7o1CpbpcX18i4oY505MYQ5E18_d3LhbZRXfq9b67x3Uxh_KVNQuKYEdVH2pcN',
        'sender_id' => '338863250531',
        'server_send_url' => 'https://fcm.googleapis.com/fcm/send',
        'server_group_url' => 'https://android.googleapis.com/gcm/notification',
        'timeout' => 30.0, // in second
    ],
];
