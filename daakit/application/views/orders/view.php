<?php
$details_required = array(
    'order_id' => 'Order ID',
    'shipping_fname' => 'Customer Name',
    'shipping_address' => 'Address',
    'shipping_city' => 'City',
    'shipping_state' => 'State',
    'shipping_zip' => 'Pin Code',
    'shipping_phone' => 'Phone Number',
    'order_amount' => 'Order Amount',
    'order_payment_type' => 'Payment Type'
);
$valid_shipment = true;
$valid_shipment_error = '';
foreach ($details_required as $d_key => $d_value) {
    if (empty($order->{$d_key})) {
        $valid_shipment = false;
        $valid_shipment_error .= $details_required[$d_key] . ', ';
    } else {
        switch ($d_key) {
            case 'shipping_phone':
                if (!isValidPhone($order->{$d_key})) {
                    $valid_shipment = false;
                    $valid_shipment_error .= $details_required[$d_key] . ', ';
                }
                break;
            case 'shipping_zip':
                if (!isValidZip($order->{$d_key})) {
                    $valid_shipment = false;
                    $valid_shipment_error .= $details_required[$d_key] . ', ';
                }
                break;
        }
    }
}
?>
<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">Order details</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url('orders/all');?>">Orders List</a></li>
        <li class="breadcrumb-item active" aria-current="page">Order Details</li>
    </ol>
</div>
<!-- END PAGE-HEADER -->

<!-- START MAIN-CONTAINER -->
<div class="main-container container-fluid">

    <!-- START ROW-1 -->
    <div class="row row-sm">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-lg-flex">
                        <div class="d-flex">
                            <div class="mb-4 ms-2">
                                <h3 class="mb-0">Order</h3>
                                <hr style="margin-top: 10px;margin-bottom: 10px;border: 0;border-top: 1px solid rgb(0 0 0 / 17%);">
                                <strong>Order No:</strong>
                                <span class="tx-muted"><?php echo $order->order_no; ?></span>
                                <br>
                                <strong>Date:</strong>
                                <span class="tx-muted"><?php echo date('M d, Y H:i A', (int)$order->order_date); ?></span>
                                <br>
                                <strong>Payment Type:</strong>
                                <span class="tx-muted"><?php echo ucwords($order->order_payment_type); ?></span>
                                <br>
                                <strong>Order Status:</strong>
                                <span class="tx-muted"><?= ucwords( $order->fulfillment_status); ?></span><br />

                            </div>
                        </div>
                        <div class="ms-auto">
                            <address class="tx-muted text-end">
                            <h3 class="mb-0">Order Weight</h3>
                            <?php if((isset($order->seller_applied_weight)) && (!empty($order->seller_applied_weight))){ ?>
                             <hr style="    margin-top: 6px;margin-bottom: 6px;border: 0;border-top: 1px solid rgb(147 147 147 / 10%);">
                            <div style="margin-top:4px;"><h5 style="font-size: 14px;"><?php echo isset($order->seller_applied_weight_status)?$order->seller_applied_weight_status:"Applied"?> Weight(As per freeze weight)</h5>
                              <div>
                                <?php if((isset($order->seller_applied_weight)) && (!empty($order->seller_applied_weight))){ ?>
                                <strong>Weight:</strong>
                               <span><span><?= is_numeric($order->seller_applied_weight) ? round($order->seller_applied_weight / 1000, 2) : '0'; ?> Kg</span></span><br />
                            <?php }?></div>
                              <div><?php if((isset($order->seller_applied_length)) && (!empty($order->seller_applied_length)) && (isset($order->seller_applied_breadth)) && (!empty($order->seller_applied_breadth)) && (isset($order->seller_applied_height)) && (!empty($order->seller_applied_height)) ){ ?>
                            <strong>Dimension:</strong>
                          <span><?= $order->seller_applied_length . ' x ' . $order->seller_applied_breadth . ' x ' . $order->seller_applied_height; ?></span>
                            <?php }?></div>

                            <div>  <strong>Volumetric Weight:</strong>
                                <?php
                                if (!empty($order->seller_applied_length && $order->seller_applied_height && $order->seller_applied_breadth && @$courier->volumetric_divisor)) {
                                    $vol_weight = (($order->seller_applied_length * $order->seller_applied_breadth * $order->seller_applied_height) / $courier->volumetric_divisor) * 1000;
                                    if ($vol_weight >= 1000) {
                                        $vol_weight = round($vol_weight / 1000, 2) . ' Kg';
                                    } else if ($vol_weight >= 0) {
                                        $vol_weight = $vol_weight . ' g';
                                    }
                                ?>
                                    <span><?= $vol_weight; ?></span>
                                <?php } else { ?>
                                    <span>Null</span>
                                <?php } ?></div>
                              
                             
                            
                        </div>
                        <?php } else {?>
                            <div class="main-order-weight-div">
                            <hr style="margin-top: 10px;margin-bottom: 10px;border: 0;border-top: 1px solid rgb(0 0 0 / 17%);">
                            <div class="">
                                 <strong>Weight:</strong>
                                 <span><?= is_numeric($order->package_weight) ? round($order->package_weight / 1000, 2) : '0'; ?> Kg</span>
                            </div>
                            <div class="">
                                 <strong>Dimension:</strong>
                                <?php
                                if (empty($order->package_length && $order->package_height && $order->package_breadth)) {
                                ?>
                                    <span>Null</span>
                                <?php } else { ?>
                                    <span><?= $order->package_length . ' x ' . $order->package_breadth . ' x ' . $order->package_height; ?></span>
                                <?php } ?>
                            </div>
                            <div>
                                <strong>Volumetric Weight:</strong>
                                <?php
                                if (!empty($order->package_length && $order->package_height && $order->package_breadth && @$courier->volumetric_divisor)) {
                                    $vol_weight = (($order->package_length * $order->package_breadth * $order->package_height) / $courier->volumetric_divisor) * 1000;
                                    if ($vol_weight >= 1000) {
                                        $vol_weight = round($vol_weight / 1000, 2) . ' Kg';
                                    } else if ($vol_weight >= 0) {
                                        $vol_weight = $vol_weight . ' g';
                                    }
                                ?>
                                    <span><?= $vol_weight; ?></span>
                                <?php } else { ?>
                                    <span>Null</span>
                                <?php } ?>
                            </div>
                        </div>
                          <?php } ?>

                           <?php if (!empty($shipping->charged_weight) || !empty($shipping->calculated_weight)) { ?>
                                <div>
                                    <strong>Charged Weight Slab:</strong>
                                    <span><?= (!empty($shipping->charged_weight) && is_numeric($shipping->charged_weight)) ? round($shipping->charged_weight / 1000, 2) : (!empty($shipping->calculated_weight) && is_numeric($shipping->calculated_weight) ? round($shipping->calculated_weight / 1000, 2) : '0'); ?> Kg</span>
                                </div>

                            <?php } ?>

                            </address>
                        </div>
                    </div>
                    <div class="bg-primary br-4">
                        <div class="row row-sm p-3">
                            <div class="col-lg-6">
                                <p class="h3 mb-3">Shipping Address :</p>
                                <?php echo ucwords($order->shipping_fname . ' ' . $order->shipping_lname); ?><br>
                                <?php if (!empty($order->shipping_company_name)) {
                                    echo ucwords($order->shipping_company_name) . '<br/>';
                                }; ?>
                                <?php echo $order->shipping_address; ?>,<?php echo $order->shipping_address_2; ?><br>
                                <?php echo $order->shipping_city; ?>, <?php echo $order->shipping_state; ?> <?php echo $order->shipping_zip; ?><br>
                                <?php echo $order->shipping_country; ?><br>
                                <?php echo $order->shipping_phone; ?><br>
                            </div>
                            <?php if (!empty($order->billing_fname) || !empty($order->billing_address)  || !empty($order->billing_city)  || !empty($order->billing_state)  || !empty($order->billing_phone)  || !empty($order->billing_zip)) { ?>
                                <div class="col-lg-6 text-end">
                                    <p class="h3 mb-3">Billing Address :</p>
                                    <?php echo ucwords($order->billing_fname . ' ' . $order->billing_lname); ?><br>
                                        <?php if (!empty($order->billing_company_name)) {
                                            echo ucwords($order->billing_company_name) . '<br/>';
                                        }; ?>
                                        <?php echo $order->billing_address; ?> <?php if (!empty($order->billing_address_2)) {
                                                                                    echo ', ' . $order->billing_address_2;
                                                                                } ?><br>
                                        <?php echo $order->billing_city; ?> <?php if (!empty($order->billing_state)) {
                                                                                echo ', ' . $order->billing_state;
                                                                            } ?> <?php echo $order->billing_zip; ?><br>
                                        <?php echo $order->billing_country; ?><br>
                                        <?php echo $order->billing_phone; ?><br>
                                        <?php if (!empty($order->billing_gst_number)) echo '<b>GST No: </b>' . $order->billing_gst_number; ?><br>
                                </div>
                            <?php  } ?>
                        </div>
                    </div>
                    <div class="table-responsive p-0 radius-4 mt-5">
                        <table class="table table-invoice mb-0 table-bordered">
                            <thead>
                                <tr>
                                    <th class="w-30">Product</th>
                                    <th class="w-20">SKU</th>
                                    <th class="text-center">QNTY</th>
                                    <th class="text-end">Item Price</th>
                                    <th class="text-end">Product Total</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                $k = 0;
                                foreach ($products as $details) {
                                ?>
                                    <tr>
                                        <td class="fw-semibold fs-13" id="oldproductname">
                                            <p class="text-black m-0"><?php echo $details->product_name; ?></p>
                                        </td>
                                        <td class="fs-13"><?php echo $details->product_sku; ?></td>
                                        <td class="text-center" id="oldproductqty">
                                            <?php echo $details->product_qty; ?>
                                        </td>
                                        <td class="text-center"><?php echo $details->product_price; ?></td>
                                        <td class="text-right"><?php echo $details->product_price*$details->product_qty; ?></td>
                                       
                                        
                                    </tr>
                                <?php
                                    $k++;
                                }
                                ?>
                                <?php if ($order->shipping_charges > 0) { ?>
                                    <tr class="bg-light">
                                        <td colspan="3" class="text-right">Shipping Charges</td>
                                        <td colspan="3" class="text-right"><?php echo $order->shipping_charges; ?> Rs.</td>
                                    </tr>
                                <?php } ?>
                                <?php if ($order->cod_charges > 0) { ?>
                                    <tr class="bg-light">
                                        <td colspan="3" class="text-right">COD Charges</td>
                                        <td colspan="3" class="text-right"><?php echo $order->cod_charges; ?> Rs.</td>
                                    </tr>
                                <?php } ?>
                                <?php if ($order->tax_amount > 0) { ?>
                                    <tr class="bg-light">
                                        <td colspan="3" class="text-right">Tax Amount</td>
                                        <td colspan="3" class="text-right"><?php echo $order->tax_amount; ?> Rs.</td>
                                    </tr>
                                <?php } ?>
                                <?php if ($order->discount > 0) { ?>
                                    <tr class="bg-light">
                                        <td colspan="3" class="text-right">Discount</td>
                                        <td colspan="3" class="text-right"><?php echo $order->discount; ?> Rs.</td>
                                    </tr>
                                <?php } ?>
                                <tr class="bg-light">
                                    <td colspan="3" class="text-right">Grand Total</td>
                                    <td colspan="3" class="text-right"><?php echo $order->order_amount; ?> Rs.</td>
                                </tr>   
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-end">
                        <div class="btn-list">
                                <a href="<?= base_url('orders/all');?>" class="btn btn-secondary btn-sm">Back</a>

                                <?php if (!empty($shipping)) { ?>
                                    <?php if ($shipping->ship_status != "new") { ?>
                                        <button class="btn btn-secondary btn-sm generate-singlelabel" data-shipping-id="<?= $shipping->id ?>"> <i class="mdi mdi-cloud-print"></i>Label</button>
                                        <button class="btn btn-info btn-sm generate-singleinvoice" data-shipping-id="<?= $shipping->id ?>" rel="shipping"> <i class="mdi mdi-receipt"></i>Invoice</button>
                                    <?php } ?>
                                    <?php if ($shipping->ship_status == "booked" || $shipping->ship_status == "pending pickup") { ?>
                                        <?php if (!empty($user_details->parent_id)  &&    in_array('cancel_shipments', $user_details->permissions)) { ?>
                                            <button type="button" class="btn btn-danger btn-sm" id="cancel-shipment" data-shipping-id="<?= $shipping->id ?>"><i class="mdi mdi-cancel text-danger"></i> Cancel Shipment</button>
                                        <?php } else if (empty($user_details->parent_id)) { ?>
                                            <button type="button" class="btn btn-danger btn-sm" id="cancel-shipment" data-shipping-id="<?= $shipping->id ?>"><i class="mdi mdi-cancel text-danger"></i> Cancel Shipment</button>
                                        <?php } ?>
                                    <?php } ?>
                                    <!--  <a href="orders/invoice/<?= $order->id; ?>" class="btn btn-outline-dark btn-sm"> <i class="mdi mdi-invoice"></i> View Invoice</a>  -->
                                <?php
                                }
                                if ($order->fulfillment_status == "new") {

                                    $edit_path = base_url("orders/create/" . $order->id);
                                    if ($order->order_payment_type == 'reverse' && !empty($order->qccheck == '1'))
                                        $edit_path = base_url("orders/reverse_qc_create/" . $order->id);
                                    else if ($order->order_payment_type == 'reverse')
                                        $edit_path = base_url("orders/reverse_create/" . $order->id);
                                ?>
                                    <?php if (!empty($shipping)) { ?>
                                        <?php if (!empty($shipping->ship_status == "cancelled")) { ?>
                                            <?php if (!$valid_shipment) { ?>
                                                <a href="<?= $edit_path; ?>" data-toggle="tooltip" data-html="true" title="<?= $valid_shipment_error; ?>" class="btn btn-outline-warning btn-sm"><i class="mdi mdi-pencil"></i> Edit</a>
                                            <?php } else { ?>
                                                <button class="btn btn-primary shipnowbtn btn-sm" data-order-id='<?= $order->id; ?>' data-toggle="modal" data-target=".bd-example-modal-sm"><i class="mdi mdi-package-variant-closed"></i> Get Awb</button>
                                                <a href="<?= $edit_path; ?>" class="btn btn-primary btn-sm"> <i class="mdi mdi-pencil"></i> Edit</a>
                                            <?php } ?>
                                            <?php if (!empty($user_details->parent_id)  &&    in_array('cancel_orders', $user_details->permissions)) { ?>
                                                <button class="btn btn-danger btn-sm cancel_order" data-order-id='<?= $order->id; ?>'> <i class="mdi mdi-cancel"></i> Cancel Order</button>
                                            <?php } else if (empty($user_details->parent_id)) { ?>
                                                <button class="btn btn-danger btn-sm cancel_order" data-order-id='<?= $order->id; ?>'> <i class="mdi mdi-cancel"></i> Cancel Order</button>
                                            <?php } ?>

                                            <!-- <button class="btn btn-outline-dark text-danger btn-sm cancel_order" data-order-id='<?= $order->id; ?>'> <i class="mdi mdi-cancel"></i> Cancel</button>-->
                                        <?php } ?>
                                    <?php } else { ?>
                                        <?php if (!$valid_shipment) { ?>
                                            <a href="<?= $edit_path; ?>" data-toggle="tooltip" data-html="true" title="<?= $valid_shipment_error; ?>" class="btn btn-primary btn-sm"><i class="mdi mdi-pencil"></i> Edit</a>
                                        <?php } else { ?>
                                            <button class="btn btn-danger shipnowbtn btn-sm" data-order-id='<?= $order->id; ?>' data-toggle="modal" data-target=".bd-example-modal-sm"><i class="mdi mdi-package-variant-closed"></i> Get Awb</button>
                                            <a href="<?= $edit_path; ?>" class="btn btn-danger btn-sm"> <i class="mdi mdi-pencil"></i> Edit</a>
                                        <?php } ?>
                                        <?php if (!empty($user_details->parent_id)  &&    in_array('cancel_orders', $user_details->permissions)) { ?>
                                            <button class="btn btn-outline-dark text-danger btn-sm cancel_order" data-order-id='<?= $order->id; ?>'> <i class="mdi mdi-cancel"></i> Cancel Order</button>
                                        <?php } else if (empty($user_details->parent_id)) { ?>
                                            <button class="btn btn-danger btn-sm cancel_order" data-order-id='<?= $order->id; ?>'> <i class="mdi mdi-cancel"></i> Cancel Order</button>
                                        <?php } ?>
                                    <?php  } ?>
                                <?php } elseif ($order->fulfillment_status == "cancelled") {
                                ?>
                                    <button style="display:none;" class="btn btn-danger btn-sm">Cancelled</button>
                                <?php
                                } else {
                                ?>
                                    <button style="display:none;" class="btn btn-success btn-sm"><i class="mdi mdi-package-variant-closed"></i> <?= ucwords($order->fulfillment_status); ?></button>
                                <?php
                                }

                                $clone_fwd_path = base_url("orders/create/" . $order->id);
                                $clone_reverse_path = "orders/reverse_create/" . $order->id;
                                $clone_reverse_qc_path = "orders/reverse_qc_create/" . $order->id;
                                ?>
                                <a href="<?= $clone_fwd_path; ?>/clone" class="btn btn-success btn-sm">Copy Order</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- col-end -->
    </div>
    <!-- END ROW-1 -->

</div>
<!-- END MAIN-CONTAINER -->
<script>
    function getengage(order_id) {
        $.ajax({
            url: 'orders/engagehistory',
            type: "POST",
            data: {
                order_id: order_id,
            },
            cache: false,
            success: function(data) {

                $('#engagehistory').html(data);

            }
        });

    }

    function addressupdate(order_id) {
        $("#engage").modal("hide");
        $.ajax({
            url: 'orders/seller_update_address',
            type: "POST",
            data: {
                order_id: order_id,
            },
            cache: false,
            success: function(data) {

                $('#order_id').html(order_id);
                $('#address_update').html(data);
                $("#myModal2").modal("show");

            }
        });

    }
</script>