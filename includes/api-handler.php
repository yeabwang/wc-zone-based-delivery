<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function wc_zone_based_delivery_get_states_from_api() {
    $url = "https://data.handyapi.com/au-postcodes/2481";
    $options = [
        "http" => [
            "method" => "GET",
            "header" => "Referer: your_domain\r\n"
        ]
    ];
    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);
    $data = @json_decode($response, true);

    if ($data && $data['Status'] === 'SUCCESS') {
        return $data['Locations'];
    }

    return [];
}
