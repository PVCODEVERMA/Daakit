<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">Add new order request</h4>
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
                    <h3 class="card-title">Shipping Details</h3>
                </div>
                <div class="card-body">
                    <div class="panel panel-success">
                        <div class="panel-body">
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label>First Name</label>
                                    <input type="text" name="shipping_name" id="shipping_name" autocomplete="nope" required="" class="form-control" placeholder="First Name" value="<?= set_value('shipping_name', !empty($order->shipping_fname) ? $order->shipping_fname : '') ?>" />
                                </div>
                                <div class="form-group col-sm-6">
                                    <label>Last name</label>
                                    <input type="text" name="shipping_lname" autocomplete="nope" id="shipping_lname" class="form-control" placeholder="Last Name" value="<?= set_value('shipping_lname', !empty($order->shipping_lname) ? $order->shipping_lname : '') ?>" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <label>Company Name</label>
                                    <input type="text" name="shipping_company_name" id="shipping_company_name" autocomplete="nope" class="form-control" placeholder="Company Name" value="<?= set_value('shipping_company_name', !empty($order->shipping_company_name) ? $order->shipping_company_name : '') ?>" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <label>Address</label>
                                    <textarea class="form-control" id="shipping_address_1" autocomplete="nope" required="" name="shipping_address_1" placeholder="Shipping Address"><?= set_value('shipping_address_1', !empty($order->shipping_address) ? $order->shipping_address : '') ?></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <label>Address 2 (If any)</label>
                                    <textarea class="form-control" autocomplete="nope" name="shipping_address_2" id="shipping_address_2" placeholder="Address 2"><?= set_value('shipping_address_2', !empty($order->shipping_address_2) ? $order->shipping_address_2 : '') ?></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <label>Pin Code</label>
                                    <input type="text" name="shipping_pincode" id="shipping_pincode" autocomplete="nope" required="" class="form-control" placeholder="Pin Code" value="<?= set_value('shipping_pincode', !empty($order->shipping_zip) ? $order->shipping_zip : '') ?>" />
                                    <span class="errormsg" id="errormsg" style="color: #8b0001;font-weight: bold;"></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label>City</label>
                                    <input type="text" name="shipping_city" autocomplete="nope" id="shipping_getcity" required="" class="form-control" placeholder="City" value="<?= set_value('shipping_city', !empty($order->shipping_city) ? $order->shipping_city : '') ?>" />
                                </div>
                                <div class="form-group col-sm-6">
                                    <label>State</label>
                                    <input type="text" name="shipping_state" autocomplete="nope" id="shipping_getstate" required="" class="form-control" placeholder="State" value="<?= set_value('shipping_state', !empty($order->shipping_state) ? $order->shipping_state : '') ?>" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label>Phone</label>
                                    <input type="text" name="shipping_phone" id="shipping_phone" autocomplete="nope" required="" class="form-control" placeholder="Phone" value="<?= set_value('shipping_phone', !empty($order->shipping_phone) ? $order->shipping_phone : '') ?>" />
                                </div>
                                <div class="form-group col-sm-6">
                                    <label>Email</label>
                                    <input type="text" name="shipping_email" id="shipping_email" autocomplete="nope" class="form-control" placeholder="Email" value="<?= set_value('shipping_email', !empty($order->shipping_email) ? $order->shipping_email : '') ?>" />
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div style="margin-top:20px">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Billing Details</h3>
                </div>
                <div class="card-body">
                    <input type="checkbox" id="check_billing_address" checked="" data-gtm-form-interact-field-id="0">
                     Is Billing Information are the same ?
                    <div class="panel-body">
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>First Name</label>
                                <input type="text" name="billing_name" id="billing_shipping_name" autocomplete="nope" class="form-control" placeholder="First Name" value="<?= set_value('billing_name', !empty($order->billing_fname) ? $order->billing_fname : '') ?>" />
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Last name</label>
                                <input type="text" name="billing_lname" autocomplete="nope" id="billing_shipping_lname" class="form-control" placeholder="Last Name" value="<?= set_value('billing_lname', !empty($order->billing_lname) ? $order->billing_lname : '') ?>" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>Company Name</label>
                                <input type="text" name="billing_company_name" id="billing_shipping_company_name" autocomplete="nope" class="form-control" placeholder="Company Name" value="<?= set_value('billing_company_name', !empty($order->billing_company_name) ? $order->billing_company_name : '') ?>" />
                            </div>
                            <div class="form-group col-sm-6">
                                <label>GST</label>
                                <input type="text" name="billing_gst_number" maxlength="15" id="billings_gst_number" autocomplete="nope" class="form-control" placeholder="GST Number" value="<?= set_value('billing_gst_number', !empty($order->billing_gst_number) ? $order->billing_gst_number : '') ?>" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-12">
                                <label>Address</label>
                                <textarea class="form-control" autocomplete="nope" name="billing_address_1" id="billing_shipping_address_1" placeholder="Shipping Address"><?= set_value('billing_address_1', !empty($order->billing_address) ? $order->billing_address : '') ?></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-12">
                                <label>Address 2 (If any)</label>
                                <textarea class="form-control" autocomplete="nope" name="billing_address_2" id="billing_shipping_address_2" placeholder="Address 2"><?= set_value('billing_address_2', !empty($order->billing_address_2) ? $order->billing_address_2 : '') ?></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-12">
                                <label>Pin Code</label>
                                <input type="text" name="billing_pincode" id="billing_shipping_pincode" autocomplete="nope" class="form-control" placeholder="Pin Code" value="<?= set_value('billing_pincode', !empty($order->billing_zip) ? $order->billing_zip : '') ?>" />
                                <span class="errormsg" id="errormsg" style="color: #8b0001;font-weight: bold;"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label>City</label>
                                <input type="text" name="billing_city" autocomplete="nope" id="billing_shipping_getcity" class="form-control" placeholder="City" value="<?= set_value('billing_city', !empty($order->billing_city) ? $order->billing_city : '') ?>" />
                            </div>
                            <div class="form-group col-sm-6">
                                <label>State</label>
                                <input type="text" name="billing_state" autocomplete="nope" id="billing_shipping_getstate" class="form-control" placeholder="State" value="<?= set_value('billing_state', !empty($order->billing_state) ? $order->billing_state : '') ?>" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-12">
                                <label>Phone</label>
                                <input type="text" name="billing_phone" id="billing_shipping_phone" autocomplete="nope" class="form-control" placeholder="Phone" value="<?= set_value('billing_phone', !empty($order->billing_phone) ? $order->billing_phone : '') ?>" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Order Details</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label>Order No.</label>
                            <input type="text" required="" class="form-control" name="order_id" placeholder="Order Id" value="<?= set_value('order_id', !empty($order->order_no) ? $order->order_no . (($clone) ? '-Copy' : '') : date('Ymdhis').random_int(1,9)); ?>" />
                        </div>
                        <div class="form-group col-sm-6">
                            <label>Order Payment Type</label>
                            <select id="paymenttype" required="" name="payment_method" class="form-control">
                                <?php
                                $change = '';
                                $pay_selected = strtolower(set_value('payment_method', !empty($order->order_payment_type) ? $order->order_payment_type : ''));
                                ?>
                                <?php if (!empty($user_details->parent_id)  &&    in_array('change_payment_mode', $user_details->permissions) && $pay_selected != '') {
                                    $change = '';
                                } else if (!empty($user_details->parent_id)  &&    !in_array('change_payment_mode', $user_details->permissions) && $pay_selected != '') {
                                    $change = 'disabled';
                                } else if (empty($user_details->parent_id)) {
                                    $change = '';
                                } ?>
                                <option <?php if ($pay_selected == 'cod') { ?> selected="" <?php } else { echo $change; } ?> value="COD">Cash on Delivery</option>
                                <option <?php if ($pay_selected == 'prepaid') { ?> selected="" <?php } else { echo $change; }  ?> value="prepaid">Prepaid</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-3">
                            <label>Weight*</label>
                            <input type="text" autocomplete="nope" onkeyup="alertFunction()" name="weight" id="weight" class="form-control" placeholder="in grams" value="<?= set_value('weight', !empty($order->package_weight) ? $order->package_weight : '') ?>">
                        </div>
                        <div class="form-group col-sm-3">
                            <div class="row">
                                <label class="col-sm-12">Dimensions*</label>
                                <div class="col-sm-4">
                                    <input type="text" onkeypress="return /[0-9]/i.test(event.key)" id="length" name="length" autocomplete="nope" class="form-control calculate_vol_weight" placeholder="CM" value="<?= set_value('length', !empty($order->package_length) ? $order->package_length : '') ?>">
                                </div> 
                                <div class="col-sm-4">
                                    <input type="text" onkeypress="return /[0-9]/i.test(event.key)" id="breadth" name="breadth" autocomplete="nope" class="form-control calculate_vol_weight" placeholder="CM" value="<?= set_value('breadth', !empty($order->package_breadth) ? $order->package_breadth : '') ?>">
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" onkeypress="return /[0-9]/i.test(event.key)" id="height" name="height" autocomplete="nope" class="form-control calculate_vol_weight" placeholder="CM" value="<?= set_value('height', !empty($order->package_height) ? $order->package_height : '') ?>">
                                </div>                        
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label>Volumetric</label>
                            <input type="hidden" name="volumetric_weight" id="volumetric_weight" value="<?= !empty($order->package_volumatic_weight) ? $order->package_volumatic_weight :'' ?>">
                            <input type="text" autocomplete="nope" name="vol_weight" id="vol_weight" class="form-control" placeholder="Volumetric" value="<?= !empty($vol_weight)? $vol_weight: '0'; ?>" readonly>
                            <span style="margin-top: 7px; margin-left: 6px;margin-right: 304px;" id="weight_in"> <?= $unit ?></span>
                            <div id="errmsgbox" style="color: #c59605"><?php
                                $value = 50001;
                                if(!empty($order)){
                                if (!empty($order->package_weight >= $value)) {      
                                    echo '<b>Note:</b> Enter weight is greater then 50 kg please cross verify before order created';
                                } else if (!empty($order->package_volumatic_weight >= $value)){  
                                    echo '<b>Note:</b> Enter weight is greater then 50 kg please cross verify before order created';
                                }else{
                                    echo ''; 
                                }
                            }
                            ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Item Details</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table invoice-detail-table create-invoice com-create-sales-table product_det_table" style="margin-top:0px">
                            <tbody id="field_wrapper" class="product_table_tbody">
                                <?php
                                $k = 0;
                                if (!empty($product)) {

                                    foreach ($product as $details) {
                                        $details = (object) $details;
                                ?>
                                        <tr id="customerfield<?php echo $k; ?>">
                                            <td>
                                                <input type="text" autocomplete="nope" id="perproduct_ids<?php echo $k; ?>" name="products[<?= $k; ?>][product_name]" required="" class="form-control" placeholder="Item Name" value="<?= set_value('product_name', !empty($details->product_name) ? $details->product_name : '') ?>">
                                            </td>
                                            <td>
                                                <input type="text" autocomplete="nope" id="productsku<?php echo $k; ?>" name="products[<?= $k; ?>][product_sku]" class="form-control" placeholder="Item SKU" value="<?= set_value('product_sku', !empty($details->product_sku) ? $details->product_sku : '') ?>">
                                                <input type="hidden" id="product_id<?php echo $k; ?>" name="products[<?= $k; ?>][product_id]" class="form-control" value="<?= set_value('product_id', !empty($details->product_id) ? $details->product_id : '') ?>">
                                            </td>
                                            <td>
                                                <input type="text" autocomplete="nope" name="products[<?= $k; ?>][product_qty]" id="basic_unit<?php echo $k; ?>" class="form-control" required="" placeholder="Item Quantity" value="<?= set_value('product_qty', !empty($details->product_qty) ? $details->product_qty : '1') ?>">
                                            </td>
                                            <td>
                                                <input type="text" autocomplete="nope" id="productrate<?php echo $k; ?>" name="products[<?= $k; ?>][product_price]" required="" class="form-control" placeholder="Item Amount" value="<?= set_value('product_price', !empty($details->product_price) ? $details->product_price : '') ?>">

                                            </td>
                                            <?php if ($k == 0) { ?>
                                                <td>
                                                    <a class="btn btn-primary btn-sm" id="addmorefields" href="javascript:void(0);" title="Product"><i class="fa fa-plus"></i></a>
                                                </td>
                                            <?php } else { ?>
                                                <td>
                                                    <a href="javascript:void(0);" class="btn btn-danger btn-sm" id="remove_button" href="javascript:void(0);" onclick="removediv('<?php echo $k; ?>');" title="Remove Field"><i class="fa fa-minus"></i></a>
                                                </td>
                                            <?php } ?>
                                        </tr>
                                    <?php
                                        $k++;
                                    }
                                } else {
                                    ?>
                                    <tr id="customerfield0">
                                        <td>
                                            <input type="text" autocomplete="nope" id="perproduct_ids0" name="products[<?= $k; ?>][product_name]" required="" class="form-control" placeholder="Item Name" value="<?= set_value("products[{$k}][product_name]", '') ?>">
                                        </td>
                                        <td>
                                            <input type="text" autocomplete="nope" id="productsku0" name="products[<?= $k; ?>][product_sku]" class="form-control" placeholder="Item SKU" value="<?= set_value("products[{$k}][product_sku]", '') ?>">
                                            <input type="hidden" id="product_id0" name="products[<?= $k; ?>][product_id]" class="form-control" value="<?= set_value("products[{$k}][product_id]", '') ?>">
                                        </td>
                                        <td>
                                            <input type="text" autocomplete="nope" name="products[<?= $k; ?>][product_qty]" id="basic_unit0" class="form-control" placeholder="Item Quantity" required="" value="<?= set_value("products[{$k}][product_qty]", '') ?>">
                                        </td>
                                        <td>
                                            <input type="text" autocomplete="nope" id="productrate0" name="products[<?= $k; ?>][product_price]" required="" class="form-control" placeholder="Item Amount" value="<?= set_value("products[{$k}][product_price]", '') ?>">
                                        </td>
                                        <td>
                                            <a class="btn btn-primary btn-sm" id="addmorefields" href="javascript:void(0);" title="Product"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                        </td>
                                    </tr>
                                <?php
                                    $k++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Order Charges</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-sm-3">
                            <label>Shipping Charges</label>
                            <input type="text" autocomplete="nope" name="shipping_charges" required="" class="form-control" placeholder="Shipping Charges" value="<?= set_value('shipping_charges', !empty($order->shipping_charges) ? $order->shipping_charges : '0') ?>" />
                        </div>
                        <div class="form-group col-sm-3">
                            <label>COD Charges</label>
                            <input type="text" autocomplete="nope" name="cod_charges" required="" class="form-control" placeholder="COD Charges" value="<?= set_value('cod_charges', !empty($order->cod_charges) ? $order->cod_charges : '0') ?>" />
                        </div>
                        <div class="form-group col-sm-3">
                            <label>Tax Amount</label>
                            <input type="text" autocomplete="nope" name="tax_amount" required="" class="form-control" placeholder="Tax Amount" value="<?= set_value('tax_amount', !empty($order->tax_amount) ? $order->tax_amount : '0') ?>" />
                        </div>
                        <div class="form-group col-sm-3">
                            <label>Discount</label>
                            <input type="text" autocomplete="nope" name="discount" required="" class="form-control" placeholder="Discount Applied" value="<?= set_value('discount', !empty($order->discount) ? $order->discount : '0') ?>" />
                        </div>
                    </div>
                    <span style="margin-top: 7px; margin-left: 6px;margin-right: 304px;" ></span>
                    <input type="hidden" id="lat" name="latitude" value="<?= set_value('latitude', !empty($order->latitude) ? $order->latitude : ''); ?>">
                    <input type="hidden" id="lng" name="longitude" value="<?= set_value('longitude', !empty($order->longitude) ? $order->longitude : ''); ?>">
                    <input type="hidden" id="hyperlocal_address" name="hyperlocal_address" value="<?= set_value('hyperlocal_address', !empty($order->hyperlocal_address) ? html_entity_decode($order->hyperlocal_address) : ''); ?>">
                    <input type="hidden" id="postal_code" name="postal_code" value="">
                    <div class="clearfix"></div>
                    <div class="btn-bottom-toolbar text-right">
                        <button type="submit" data-form="#project_form" class="btn btn-info" autocomplete="off" data-loading-text="Please wait...">Create Order</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- END ROW-1 -->

</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript">
    function alertFunction() {
        var total_weight = $("#weight").val();

        $('#errmsgbox').html('');

        if (total_weight >= 50001) {
            $("#errmsgbox").html("<b>Note:</b> Enter weight is greater then 50 kg please cross verify before order created");
        }

        var l = $("#length").val();
        var b = $("#breadth").val();
        var h = $("#height").val();

        len = l.replace(/\s/g, '');
        bre = b.replace(/\s/g, '');
        hei = h.replace(/\s/g, '');

            var sum = len * bre * hei;
            var totalsum = sum / 5000;
            var weight = (totalsum * 1000);

            if (weight >= 50001){
                  $("#errmsgbox").html("<b>Note:</b> Enter weight is greater then 50 kg please cross verify before order created");
            }
         
    }

    $('.calculate_vol_weight').keyup(function() {
        $('#vol_weight').val('');
        var l = $("#length").val();
        var b = $("#breadth").val();
        var h = $("#height").val();
        var total_weight = $("#weight").val();

        len = l.replace(/\s/g, '');
        bre = b.replace(/\s/g, '');
        hei = h.replace(/\s/g, '');
     
            var sum = len * bre * hei;
           
            var totalsum = sum / 5000;
            var weight = (totalsum * 1000);

            $('#errmsgbox').html('');

            if (weight >= 50001)
                $("#errmsgbox").html("<b>Note:</b> Enter weight is greater then 50 kg please cross verify before order created");

            if (total_weight >= 50001)
                $("#errmsgbox").html("<b>Note:</b> Enter weight is greater then 50 kg please cross verify before order created");

            var bs = totalsum.toString().split(".")[0]; ///before  
            var as = totalsum.toString().split(".")[1]; ///after
           
            $('#volumetric_weight').val(Math.round(weight));
            
            if (bs > 0) {
                $('#vol_weight').val(totalsum.toFixed(2));
                $("#weight_in").html("Kg");
            } else { 
                $('#vol_weight').val(Math.round(weight));
                $("#weight_in").html("Grams");
            }

    });


    $("#shipping_pincode").change(function() {
        var pincode = $('#shipping_pincode').val();
        if (pincode == "") {
            $('#shipping_getcity').val('');
            $('#shipping_getstate').val('');
            $('#billing_shipping_getcity').val('');
            $('#billing_shipping_getstate').val('');
        } else {
            $.ajax({
                type: 'POST',
                url: baseUrl+"orders/getcitystate", //file which read zip code excel file
                data: {
                    'pincode': pincode
                },
                success: function(data) {
                    if (data == '') {
                        $('#shipping_getcity').val('');
                        $('#shipping_getstate').val('');
                        $('#billing_shipping_getcity').val('');
                        $('#billing_shipping_getstate').val('');
                        return false;
                    } else {
                        var getData = $.parseJSON(data);
                        $('#shipping_getcity').val(getData.city);
                        $('#shipping_getstate').val(getData.state);
                        $('#billing_shipping_getcity').val(getData.city);
                        $('#billing_shipping_getstate').val(getData.state);
                    }
                },
            });
        }
    });

    $("#billing_shipping_pincode").change(function() {
        var pincode = $('#billing_shipping_pincode').val();
        if (pincode == "") {
            $('#billing_shipping_getcity').val('');
            $('#billing_shipping_getstate').val('');
        } else {
            $.ajax({
                type: 'POST',
                url: baseUrl+"orders/getcitystate", //file which read zip code excel file
                data: {
                    'pincode': pincode
                },
                success: function(data) {
                    if (data == '') {
                        $('#billing_shipping_getcity').val('');
                        $('#billing_shipping_getstate').val('');
                        return false;
                    } else {
                        var getData = $.parseJSON(data);
                        $('#billing_shipping_getcity').val(getData.city);
                        $('#billing_shipping_getstate').val(getData.state);
                    }
                },
            });
        }
    })
</script>

<script type="text/javascript">
    $(document).ready(function() {
        var addButton = $('#addmorefields');
        var wrapper = $('#field_wrapper');
        var x = <?= $k; ?>;
        $(addButton).click(function() {
            var fieldHTML = '<tr id="customerfield' + x + '">\
<td>\
<input type="text" autocomplete="nope" id="perproduct_ids' + x + '"  name="products[' + x + '][product_name]" required="" class="form-control" placeholder="Item Name">\
</td>\
<td>\
<input type="text" autocomplete="nope" id="productsku' + x + '" name="products[' + x + '][product_sku]" class="form-control" placeholder="Item SKU">\
<input type="hidden" id="products_id' + x + '" name="products[' + x + '][products_id]" class="form-control">\
</td>\
<td>\
<input type="text" autocomplete="nope" name="products[' + x + '][product_qty]" id="basic_unit' + x + '" class="form-control"  required="" placeholder="Item Quantity" value="1">\
</td>\
<td>\
<input type="text" autocomplete="nope" id="productrate' + x + '" name="products[' + x + '][product_price]" required="" class="form-control" placeholder="Item Amount">\
</td>\
<td>\
<a href="javascript:void(0);" class="btn btn-danger btn-sm" id="remove_button" href="javascript:void(0);" onclick="removediv(' + x + ');"  title="Remove Field" ><i class="fa fa-minus" aria-hidden="true"></i></a></td>\
</tr>';
            x++;
            $(wrapper).append(fieldHTML);
            $('.js-example-data-array').select2();
        });
    });

    function removediv(id) {
        var element = document.getElementById("customerfield" + id);
        element.parentNode.removeChild(element);
    }
    $("[id^='shipping_']").on('input',function(){
        var billingCheckbox = $("#check_billing_address").is(":checked");
        $("[id^='billing_']").each(function() {
            var tmpID = this.id.split('billing_')[1];
            $(this).val(billingCheckbox ? $("#" + tmpID).val() : "");
        });
    })
    $("#check_billing_address").on("click", function() {
        var biil = $(this).is(":checked");
        $("[id^='billing_']").each(function() {
            var tmpID = this.id.split('billing_')[1];
            $(this).val(biil ? $("#" + tmpID).val() : "");
        });
    });
</script>