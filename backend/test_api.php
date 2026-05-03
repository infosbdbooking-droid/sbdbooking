<?php
$url = 'http://localhost:8000/api/v1/cab-orders';
$data = json_encode([
    'car_id' => 5,
    'trip_type' => 'one_way',
    'pickup_address' => 'Test',
    'pickup_lat' => 23.1,
    'pickup_lng' => 76.9,
    'drop_address' => 'Test 2',
    'drop_lat' => 23.3,
    'drop_lng' => 77.1,
    'distance_km' => 77.3,
    'pickup_date' => '2026-05-03',
    'pickup_time' => '10:00',
    'passengers' => 1,
    'bags' => 0,
    'customer_name' => 'Test User',
    'customer_mobile' => '9876543210'
]);

$options = [
    'http' => [
        'header'  => "Content-type: application/json\r\nAccept: application/json\r\n",
        'method'  => 'POST',
        'content' => $data,
        'ignore_errors' => true
    ]
];

$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
echo "Result:\n" . $result . "\n";
