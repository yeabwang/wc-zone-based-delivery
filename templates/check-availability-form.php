<form class="check-availability-form">
    <h3 class="mb-3">Check Delivery Availability</h3>

    <div class="mb-3">
        <label for="postcode" class="form-label">Enter Suburb, State, or Postcode:</label>
        <input
                type="text"
                class="form-control postcode"
                id="postcode"
                name="postcode"
                required
                placeholder="Enter a postcode, suburb, or state"
        />
    </div>

    <button type="button" class="btn btn-primary check-availability-button">Check Availability</button>

    <div
            class="availability-result mt-3 alert alert-info"
            style="display: none;">
    </div>
</form>
