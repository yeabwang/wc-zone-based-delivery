jQuery(document).ready(function($) {
    // When the "Check Availability" button is clicked
    $('#check-availability-button').on('click', function(e) {
        e.preventDefault(); // Prevent the default form submission

        var postcode = $('#postcode').val().trim(); // Get the value of the input field

        if (postcode === '') {
            $('#availability-result').html('<p>Please enter a suburb, state, or postcode.</p>');
            return;
        }

        // Show a loading message
        $('#availability-result').html('<p>Checking availability...</p>');

        // AJAX request to check availability
        $.ajax({
            url: ZoneDelivery.ajax_url, // The AJAX URL
            method: 'POST',
            data: {
                action: 'wc_check_availability',
                input: postcode // Send the input to the server
            },
            success: function(response) {
                // Handle the response from the server
                if (response.success) {
                    // Display the custom message from the zone
                    $('#availability-result').html('<p>' + response.data.message + '</p>');
                } else {
                    // Display an error message if no matching zone is found
                    $('#availability-result').html('<p>' + response.data.message + '</p>');
                }
            },
            error: function() {
                // Display an error message if the AJAX request fails
                $('#availability-result').html('<p>An error occurred while checking availability. Please try again later.</p>');
            }
        });
    });
});
