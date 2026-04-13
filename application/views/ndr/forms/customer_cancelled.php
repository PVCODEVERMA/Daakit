<p>Do you still want the product?</p>
<div class="row action_buttons">
    <div class="col-sm-12 text-center">
        <form method="post" action="<?= current_url(); ?>">
            <input type="hidden" required name="action" value="re-attempt">
            <input type="hidden" name="re_attempt_date" value="<?= strtotime("+1 day 23:59:59") ?>">
            <input type="hidden" name="remarks" value="Customer want the product">
            <button type="submit" class="btn btn-success btn-block btn-sm">Yes, I want the product</button>
        </form>

        <form method="post" action="<?= current_url(); ?>" style="margin-top:15px;">
            <input type="hidden" required name="action" value="rto">
            <input type="hidden" required name="remarks" value="Cancel the order">
            <button type="submit" class="btn btn-danger btn-block btn-sm">No, Cancel the order</button>
        </form>
    </div>
</div>