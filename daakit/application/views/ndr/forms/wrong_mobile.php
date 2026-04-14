<form method="post" action="<?= current_url(); ?>">
    <p>Please update your mobile number for order delivery</p>
    <div class="form-group">
        <label>Mobile Number</label>
        <input class="form-control" required type="text" value="" name="customer_contact_phone">
        <input type="hidden" required name="action" value="change phone">
    </div>
    <div class="row action_buttons">
        <div class="col-sm-12 text-center">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </div>
</form>