<form method="post" action="<?= current_url(); ?>">
    <p>Please choose a delivery date for your order:</p>
    <div class="form-group">
        <label>Deliver on</label>
        <input type="hidden" required name="action" value="re-attempt">
        <select name="re_attempt_date" required="" class="form-control">
            <option value="">Choose Day</option>
            <option value="<?= strtotime('+1 day 23:59:59'); ?>"><?= date('d M (D)', strtotime('+1 day')) ?></option>
            <option value="<?= strtotime('+2 day 23:59:59'); ?>"><?= date('d M (D)', strtotime('+2 day')) ?></option>
            <option value="<?= strtotime('+3 day 23:59:59'); ?>"><?= date('d M (D)', strtotime('+3 day')) ?></option>
            <option value="<?= strtotime('+4 day 23:59:59'); ?>"><?= date('d M (D)', strtotime('+4 day')) ?></option>
            <option value="<?= strtotime('+5 day 23:59:59'); ?>"><?= date('d M (D)', strtotime('+5 day')) ?></option>
        </select>
    </div>
    <div class="row action_buttons">
        <div class="col-sm-12 text-center">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </div>
</form>