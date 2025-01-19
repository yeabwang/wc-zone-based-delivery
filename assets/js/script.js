jQuery(document).ready(function($) {

    $('.check-availability-button').on('click', function (e) {
        e.preventDefault(); // Prevent the default form submission
        var form = $(this).closest('.check-availability-form');
        var postcodeInput = form.find('.postcode');
        var resultDiv = form.find('.availability-result');
        resultDiv.show();

        if (form[0].checkValidity()) {

            var postcode = postcodeInput.val().trim();

            resultDiv.html('<p>Checking availability...</p>');
            var nonce = $(this).data('nonce');

            $.ajax({
                url: ZoneDelivery.ajax_url, // The AJAX URL
                method: 'POST',
                data: {
                    action: 'wc_check_availability',
                    input: postcode, // Send the input to the server
                    nonce: nonce // Pass nonce for security
                },
                success: function (response) {
                    if (response.success) {
                        $(resultDiv).html('<p>' + response.data.message + '</p>');
                    } else {
                        $(resultDiv).html('<p>' + response.data.message + '</p>');
                    }
                },
                error: function () {
                    $(resultDiv).html('<p>An error occurred while checking availability. Please try again later.</p>');
                }
            });
        }
        else{
            form[0].reportValidity();

            resultDiv.html('<p>Please enter a suburb, state, or postcode.</p>');
        }
    });

    // When the state is changed, fetch the postcodes
    /*$('#state').on('change', function() {
        var state = $(this).val();
        if (state) {
            // Add nonce to the request for security
            var nonce = $('#state').data('nonce');

            // Show loading message while fetching postcodes
            $('#postcodes-container').html('<p>Loading postcodes...</p>');

            $.ajax({
                url: ZoneDelivery.ajax_url,
                method: 'POST',
                data: {
                    action: 'wc_get_postcodes',
                    state: state,
                    nonce: nonce // Pass nonce for security
                },
                success: function(response) {
                    if (response.success) {
                        var postcodes = response.data.postcodes;
                        var container = $('#postcodes-container');
                        container.empty(); // Clear existing postcodes
                        if (postcodes.length > 0) {
                            postcodes.forEach(function(postcode) {
                                container.append('<label><input type="checkbox" name="wc_zone_postcodes[]" value="' + postcode + '"> ' + postcode + '</label><br>');
                            });
                        } else {
                            container.html('<p>No postcodes found for this state.</p>');
                        }
                    } else {
                        $('#postcodes-container').html('<p>' + response.data.message + '</p>');
                    }
                },
                error: function() {
                    $('#postcodes-container').html('<p>Failed to fetch postcodes. Please try again later.</p>');
                }
            });
        }
    });*/
});
