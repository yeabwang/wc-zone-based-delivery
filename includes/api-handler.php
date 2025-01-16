<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Fetch states and postcodes from the external API
 *
 * @return array
 */
function wc_zone_based_delivery_get_states_from_api() {
    // The external API URL to fetch states and postcodes
    $url = "https://data.handyapi.com/au-postcodes/2481";

    // Set the necessary headers for the request
    $options = [
        "http" => [
            "method"  => "GET",
            "header"  => "Referer: your_domain\r\n"
        ]
    ];

    // Create the HTTP context and send the request
    $context  = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);

    // Decode the response
    $data = @json_decode($response, true);

    // If the API response is successful, return the Locations data
    if ($data && isset($data['Status']) && $data['Status'] === 'SUCCESS') {
        return $data['Locations'];
    }

    // If the API request fails, return an empty array
    return [];
}
