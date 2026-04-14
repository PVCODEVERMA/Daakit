<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">Add new warehouse</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item">						
		</li>
    </ol>
</div>
<!-- END PAGE-HEADER -->
<div class="main-container container-fluid">

<!-- START ROW-1 -->
<form method="post" action="<?= current_url(); ?>">
    <div class="row">
        <div class="col-lg-6 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Warehouse</h3>
                </div>
                <div class="card-body">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Warehouse Name</label>
                            <input type="text" class="form-control" <?= ($order_exists) ? 'disabled' : ''; ?> required="" name="name" value="<?= set_value('name', !empty($warehouse->name) ? $warehouse->name : ''); ?>" placeholder="Warehouse Name" />
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>Contact Name</label>
                                <input type="text" class="form-control" <?= ($order_exists) ? 'disabled' : ''; ?> required="" name="contact_name" value="<?= set_value('contact_name', !empty($warehouse->contact_name) ? $warehouse->contact_name : ''); ?>" placeholder="Enter Contact Name">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Contact No.</label>
                                <input type="text" class="form-control" <?= ($order_exists) ? 'disabled' : ''; ?> required="" name="phone" value="<?= set_value('phone', !empty($warehouse->phone) ? $warehouse->phone : ''); ?>" placeholder="Enter Contact No.">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Address Line 1</label>
                            <input type="text" class="form-control" <?= ($order_exists) ? 'disabled' : ''; ?> required="" name="address_1" value="<?= set_value('address_1', !empty($warehouse->address_1) ? $warehouse->address_1 : ''); ?>" placeholder="Address Line 1">
                        </div>
                        <div class="form-group">
                            <label>Address Line 2</label>
                            <input type="text" class="form-control" <?= ($order_exists) ? 'disabled' : ''; ?> name="address_2" value="<?= set_value('address_2', !empty($warehouse->address_2) ? $warehouse->address_2 : ''); ?>" placeholder="Address Line 2">
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-12">
                                <label>Pin Code</label>
                                <input type="text" autocomplete="off"  class="form-control" <?= ($order_exists) ? 'disabled' : ''; ?>  name="zip" id="zip" value="<?= set_value('zip', !empty($warehouse->zip) ? $warehouse->zip : ''); ?>" placeholder="Enter Pin Code"  />
                                <span class="errormsg" id="errormsg" style="color: #8b0001;font-weight: bold;"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>City</label>
                                <input type="text"  autocomplete="off" readonly class="form-control" <?= ($order_exists) ? 'disabled' : ''; ?> required="" name="city" id="city" value="<?= set_value('city', !empty($warehouse->city) ? $warehouse->city : ''); ?>" placeholder="Enter City" />
                            </div>
                            <div class="form-group col-sm-6">
                                <label>State</label>
                                <input type="text" class="form-control" readonly  autocomplete="off" <?= ($order_exists) ? 'disabled' : ''; ?> required="" name="state" id="state"  value="<?= set_value('city', !empty($warehouse->state) ? $warehouse->state : ''); ?>" placeholder="Enter State" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label>GST Number</label>
                            <input type="text" class="form-control" <?= ($order_exists) ? 'disabled' : ''; ?> name="gst_number" value="<?= set_value('gst_number', !empty($warehouse->gst_number) ? $warehouse->gst_number : ''); ?>" placeholder="GST Number">
                        </div>
                        <div class="clearfix"></div>
                        <div class="btn-bottom-toolbar text-right">
                            <button type="submit" data-form="#project_form" class="btn btn-info" autocomplete="off" data-loading-text="Please wait...">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Label</h3>
                </div>
                <div class="card-body">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Support Email (If any, Used on Label)</label>
                            <input type="email" class="form-control" name="support_email" value="<?= set_value('support_email', !empty($warehouse->support_email) ? $warehouse->support_email : ''); ?>" placeholder="">
                        </div>
                        <div class="form-group">
                            <label>Support Phone (If any, Used on Label)</label>
                            <input type="text" class="form-control" name="support_phone" value="<?= set_value('support_phone', !empty($warehouse->support_phone) ? $warehouse->support_phone : ''); ?>" placeholder="">
                        </div>
                        <div class="form-group">
                            <div class="mb-3 form-check">
                                <?= form_checkbox('hide_label_address', '1', set_value('hide_label_address', (isset($warehouse->hide_label_address) && $warehouse->hide_label_address == '1') ? true : false), 'class="form-check-input" id="customCheckDisabled1"'); ?>
                                <label for="customCheckDisabled1" class="mb-0 text-700 form-check-label">Hide warehouse address in label</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="mb-3 form-check">
                                <?= form_checkbox('hide_label_pickup_mobile', '1', set_value('hide_label_pickup_mobile', (isset($warehouse->hide_label_pickup_mobile) && $warehouse->hide_label_pickup_mobile == '1') ? true : false), 'class="form-check-input" id="customCheckDisabled3"'); ?>
                                <label for="customCheckDisabled2" class="mb-0 text-700 form-check-label">Hide warehouse mobile number in label</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="mb-3 form-check">
                                <?= form_checkbox('hide_label_products', '1', set_value('hide_label_products', (isset($warehouse->hide_label_products) && $warehouse->hide_label_products == '1') ? true : false), 'class="form-check-input" id="customCheckDisabled2"'); ?>
                                <label for="customCheckDisabled3" class="mb-0 text-700 form-check-label">Hide product details in label</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="hidden" id="lat" name="latitude" value="<?= set_value('latitude', !empty($warehouse->latitude) ? $warehouse->latitude : ''); ?>">
                            <input type="hidden" id="lng" name="longitude" value="<?= set_value('longitude', !empty($warehouse->longitude) ? $warehouse->longitude : ''); ?>">
                            <input type="hidden" id="postal_code" name="postal_code" value="">
                            <input type="hidden" id="hyperlocal_address" name="hyperlocal_address" value="<?= set_value('hyperlocal_address', !empty($warehouse->hyperlocal_address) ? html_entity_decode($warehouse->hyperlocal_address) : ''); ?>">
                            <div class="mb-3 form-check">
                                <?= form_checkbox('hide_company_name', '1', set_value('hide_company_name', (isset($warehouse->hide_company_name) && $warehouse->hide_company_name == '1') ? true : false), 'class="form-check-input" id="customCheckDisabled4"'); ?>
                                <label for="customCheckDisabled4" class="mb-0 text-700 form-check-label">Hide company name on invoice</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</form>
<!-- END ROW-1 -->

</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
        $("#zip").change(function() {
        var pincode = $('#zip').val();
        if (pincode == "") {
            $('#city').val('');
            $('#state').val('');
        } else {
            $.ajax({
                type: 'POST',
                url: baseUrl+"orders/getcitystate", //file which read zip code excel file
                data: {
                    'pincode': pincode
                },
                success: function(data) {
                    if (data == '') {
                        $('#city').val('');
                        $('#state').val('');
                        return false;
                    } else {
                        var getData = $.parseJSON(data);
                        $('#city').val(getData.city);
                        $('#state').val(getData.state);
                    }
                },
            });
        }
    });
</script>
