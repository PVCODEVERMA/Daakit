<div class="row">
    <div class="col-sm-6">
        <form method="post" class="rule_testing_form">
            <div class="form-group text-center">
                <b>Enter details for rule testing</b>
            </div>

            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-sm-6">
                        <label for="inputPassword" class="col-form-label">Order ID</label>
                        <input type="text" class="form-control" required="" name="order_id" value="">
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="inputPassword" class=" text-right col-form-label">Pickup Warehouse</label>
                        <div class="">
                            <select class="form-control js-select2" required name="warehouse_id">
                                <option value="">Select Warehouse</option>
                                <?php
                                foreach ($warehouses as $warehouse) {
                                ?>
                                    <option value="<?= $warehouse->id; ?>"><?= ucwords($warehouse->name); ?></option>
                                <?php
                                }
                                ?>

                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group bg-gray-400 p-all-15 text-black" id="matched_rule_name" style="display:none;">

                </div>
                <div class="form-group text-right">
                    <button type="submit" class="btn btn-primary">Make Test</button>
                </div>
            </div>
        </form>
    </div>

</div>

<script>
    $(".rule_testing_form").submit(function(event) {
        event.preventDefault();
        $('#matched_rule_name').hide();
        $.ajax({
            url: 'allocation/make_test',
            type: "POST",
            data: $(this).serialize(),
            cache: false,
            success: function(data) {
                if (data.success) {
                    $('#matched_rule_name').html('Matched Rule: ' + data.success);
                    $('#matched_rule_name').show();
                }
                if (data.error) {
                    $(".admin-content").alertNotify({
                        message: data.error,
                        type: 'danger',
                        dismiss: true
                    })
                }

            }
        });
    });
</script>