<script>
    $(".create_partial_remittance").submit(function(event) {
        event.preventDefault();
        $.ajax({
            url: '<?php echo base_url('admin/pincodes/save_pincode');?>',
            type: "POST",
            data: $(this).serialize(),
            cache: false,
            success: function(data) {
                if (data.success)
                    location.reload();
                else if (data.error)
                    alert_float(data.error);
            }
        });
    });
</script>

<div class="modal-header">
    <h5 class="modal-title" id="mySmallModalLabel"><i class="mdi mdi-map-marker"></i> Manage Serviceability for Pincode: <?= $pincode->pincode; ?></h5>
</div>
<form method="post" class="create_partial_remittance">
    <input type="hidden" name="pincode_id" value="<?= $pincode->id; ?>">
    <div class="modal-body">

        <div class="form-group col-sm-6 ">
            <label for="inputPassword" class="col-form-label">COD Delivery</label>

            <select class="form-control" name="cod">
                <option value="N" selected>N</option>
                <option value="Y" <?php if ($pincode->cod == 'Y') { ?> selected <?php } ?>>Y</option>
            </select>
        </div>
        <div class="form-group col-sm-6 ">
            <label for="inputPassword" class="col-form-label">Prepaid Delivery</label>
            <select class="form-control" name="prepaid">
                <option value="N" selected>N</option>
                <option value="Y" <?php if ($pincode->prepaid == 'Y') { ?> selected <?php } ?>>Y</option>
            </select>
        </div>
        <div class="form-group col-sm-6 ">
            <label for="inputPassword" class="col-form-label">Pickup</label>
            <select class="form-control" name="pickup">
                <option value="N" selected>N</option>
                <option value="Y" <?php if ($pincode->pickup == 'Y') { ?> selected <?php } ?>>Y</option>
            </select>
        </div>
        <div class="form-group col-sm-6 ">
            <label for="inputPassword" class="col-form-label">Reverse Pickup</label>
            <select class="form-control" name="is_reverse_pickup">
                <option value="N" selected>N</option>
                <option value="Y" <?php if ($pincode->is_reverse_pickup == 'Y') { ?> selected <?php } ?>>Y</option>
            </select>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
    </div>
</form>