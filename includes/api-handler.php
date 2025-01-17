<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function wc_zone_based_delivery_get_states_from_api() {
    $url = "https://data.handyapi.com/au-postcodes/2481";
    
    // Prepare the request headers
    $headers = [
        "Referer" => "https://sleeprepublstg.wpengine.com/"
    ];

    // Set up the request arguments
    $args = [
        'headers' => $headers,
        'timeout' => 15 // Set a timeout for the request
    ];

    // Attempt to fetch the data from the API using wp_remote_get
    $response = wp_remote_get($url, $args);

    // Check if the request was successful
    if (is_wp_error($response)) {
        return []; // Return empty array if there's an error with the request
    }

    // Get the body of the response
    $body = wp_remote_retrieve_body($response);

    // Decode the JSON response
    $data = json_decode($body, true);

    // Ensure the data structure is valid before accessing
    if (isset($data['Status']) && $data['Status'] === 'SUCCESS' && isset($data['Locations'])) {
        return $data['Locations'];
    }

    // Return an empty array if the response is not as expected
    return [];
}
