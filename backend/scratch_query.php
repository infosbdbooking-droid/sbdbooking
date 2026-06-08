<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = Request::create('/api/v1/calculate-charges', 'POST', [
    'car_id' => 6,
    'distance_km' => 26.73,
    'is_ac' => 1,
    'trip_type' => 'round_trip',
    'hours' => 26,
    'days' => 3
]);

$response = app()->handle($request);
echo "=== CALCULATION RESPONSE ===\n";
echo $response->getContent() . "\n";
