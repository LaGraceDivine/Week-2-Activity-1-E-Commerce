<?php
header('Content-Type: application/json');


if (!isset($_GET['country']) || empty($_GET['country'])) {
    echo json_encode(['error' => 'No country provided']);
    exit();
}

$countryCode = urlencode($_GET['country']);

//GeoDB Cities API (limit to 10 biggest cities in that country)
$url = "http://geodb-free-service.wirefreethought.com/v1/geo/cities?limit=10&countryIds=$countryCode&sort=-population";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

if ($response === false) {
    echo json_encode(['error' => 'Unable to fetch cities']);
    exit();
}

curl_close($ch);

$data = json_decode($response, true);

//Extracting city names
if (isset($data['data']) && is_array($data['data'])) {
    $cities = array_map(function ($city) {
        return $city['name'];
    }, $data['data']);

    echo json_encode(['cities' => $cities]);
} else {
    echo json_encode(['cities' => []]);
}
