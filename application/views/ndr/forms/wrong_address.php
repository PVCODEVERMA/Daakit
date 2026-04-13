<form method="post" action="<?= current_url(); ?>">
    <p>Please provide your complete address for order delivery</p>
    <div class="form-group ">
        <label>Your Name</label>
        <input type="hidden" required name="action" value="change address">
        <input class="form-control" type="text" value="" required name="customer_details_name">
    </div>
    <div class="form-group ">
        <label>Your Address</label>
        <input class="form-control" type="text" value="" required name="customer_details_address_1">
    </div>
    <div class="form-group">
        <label>Landmark (If any)</label>
        <input class="form-control" type="text" value="" name="customer_details_address_2">
    </div>
    <div class="row action_buttons">
        <div class="col-sm-12 text-center">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </div>
</form>