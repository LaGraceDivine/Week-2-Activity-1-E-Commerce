<?php
header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['country']) || empty($_GET['country'])) {
    echo json_encode(['error' => 'No country provided', 'cities' => []]);
    exit();
}

$countryCode = trim($_GET['country']);

//GeoDB Cities API (limit to 10 biggest cities in that country)
$url = "http://geodb-free-service.wirefreethought.com/v1/geo/cities?limit=10&countryIds=" . urlencode($countryCode) . "&sort=-population";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);

curl_close($ch);

if ($response === false || !empty($curlError)) {
    error_log("Cities API error: " . $curlError);
    echo json_encode(['error' => 'Unable to fetch cities: ' . $curlError, 'cities' => []]);
    exit();
}

if ($httpCode !== 200) {
    error_log("Cities API HTTP error: " . $httpCode);
    echo json_encode(['error' => 'API returned error code: ' . $httpCode, 'cities' => []]);
    exit();
}

$data = json_decode($response, true);

//Extracting city names
if (isset($data['data']) && is_array($data['data']) && count($data['data']) > 0) {
    $cities = array_map(function ($city) {
        return isset($city['name']) ? $city['name'] : '';
    }, $data['data']);
    
    // Filter out empty city names
    $cities = array_filter($cities, function($city) {
        return !empty($city);
    });
    
    $cities = array_values($cities); // Re-index array
    
    if (count($cities) > 0) {
        echo json_encode(['cities' => $cities]);
    } else {
        echo json_encode(['error' => 'No valid cities found', 'cities' => []]);
    }
} else {
    // Try alternative API or return empty
    echo json_encode(['error' => 'No cities data available for this country', 'cities' => []]);
}
