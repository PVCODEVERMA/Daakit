<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">Order details</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Orders List</a></li>
        <li class="breadcrumb-item active" aria-current="page">Order Details</li>
    </ol>
</div>
<!-- END PAGE-HEADER -->

<!-- START MAIN-CONTAINER -->
<div class="main-container container-fluid">

    <!-- START ROW-1 -->
    <div class="row">
        <div class="col-md-12">
            <div class="card m-b-30">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Shipment ID</th>
                                    <th>Shipment Date</th>
                                    <th>Courier Name</th>
                                    <th>AWB No</th>
                                    <th>Shipment status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($shiphistory)) {
                                    foreach ($shiphistory as $shiphistory) {
                                ?>
                                        <tr>
                                            <td><?php echo $shiphistory->shipping_id ?></td>
                                            <td><?= date('d-m-Y H:i', $shiphistory->shipping_created); ?></td>
                                            <td><?= $shiphistory->courier_name; ?></td>
                                            <td><?= strtoupper($shiphistory->awb_number); ?></td>
                                            <td>
                                                <?php if ($shiphistory->ship_status == 'new') { ?>
                                                    <button <?php if (!empty($shiphistory->ship_message)) { ?> data-toggle="tooltip" data-html="true" title="<?= $shiphistory->ship_message; ?>" <?php } ?> type="button" class="btn btn-outline-warning btn-sm">Processing</button>
                                                <?php } else if ($shiphistory->ship_status == 'booked') { ?>
                                                    <button type="button" class="btn btn-outline-success btn-sm">Booked</button>
                                                <?php } elseif ($shiphistory->ship_status == 'pending pickup') { ?>
                                                    <button type="button" class="btn btn-outline-info btn-sm">Waiting for Pickup</button>
                                                <?php } elseif (in_array($shiphistory->ship_status, array('lost', 'damaged'))) {
                                                ?>
                                                    <button type="button" class="btn btn-outline-warning btn-sm"><?= ucwords($shiphistory->ship_status); ?></button>
                                                <?php } elseif ($shiphistory->ship_status == 'cancelled') {
                                                ?>
                                                    <button type="button" class="btn btn-outline-danger btn-sm">Cancelled</button>
                                                <?php } elseif ($shiphistory->ship_status == 'exception') { ?>
                                                    <button <?php if (!empty($shiphistory->ship_message)) { ?> data-toggle="tooltip" data-html="true" title="<?= $shiphistory->ship_message; ?>" <?php } ?> type="button" class="btn btn-outline-warning btn-sm">Exception</button>
                                                <?php } else { ?>
                                                    <button type="button" class="btn btn-outline-info btn-sm"><?= $shiphistory->ship_status; ?></button>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="11" class="text-center">No Records found</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 m-b-40">
            <div class="card">
                <div class="card-header">
                    <div class="row" style="width: 100%;">
                        <div class="col-md-6">
                            <h4 class="m-b-0">
                               Orders
                            </h4>
                        </div>
                        <div class="col-md-6 text-right">
                            <?php
                            if ($order->fulfillment_status == "new") {
                            ?>
                                <button class="btn btn-danger btn-sm" data-order-id='<?= $order->id; ?>'> <i class="mdi mdi-cancel"></i> Cancel</button>
                            <?php } elseif ($order->fulfillment_status == "cancelled") {
                            ?>
                                <button class="btn btn-danger btn-sm">Cancelled</button>
                            <?php
                            } else {
                            ?>
                                <button class="btn btn-success btn-sm"><i class="mdi mdi-package-variant-closed"></i> <?= ucwords($order->fulfillment_status); ?></button>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <form method="post" action="<?php echo base_url('orders/orderdetailsedit') ?>">
                    <div class="card-body">
                        <div class="row">
                        <div class="col-md-3">
                                <h4 class="font-primary">Seller Details</h4>
                                <div class="">
                                    <strong>Seller Name:</strong>
                                    <?php if (in_array('view_seller_detail', $user_details->permissions)) { ?>
                                    <span><?php echo ucwords($sellerdetails->fname .' '.$sellerdetails->lname); ?></span>
                                    <?php } else {
                                    echo "******"; } ?>
                                </div>
                                <div class="">
                                    <strong>Company Name:</strong>
                                    <?php if (in_array('view_seller_detail', $user_details->permissions)) { ?>
                                    <a target="_blank;" href="<?php echo base_url('admin/users/viewuser/'); ?><?php echo $sellerdetails->id; ?>" style="color: #004080;font-weight:bold;">
                                        <span><?php echo ucwords($sellerdetails->company_name); ?></span>
                                    </a>
                                    <?php }else{ echo "******"; } ?>
                                    <div class="">
                                    <strong>Seller ID:</strong>
                                    <span><?php echo $sellerdetails->id; ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <h5>Warehouse Details</h5>
                                <?php
                                
                                    if (!empty($shipping)) { 
                                        if (in_array('shipment_warehouse_detail', $user_details->permissions)) { ?>
                                    <address class="m-t-10">
                                        <?php echo $warehouse->name; ?><br>
                                        <?php echo $warehouse->phone; ?><br>
                                        <?php echo $warehouse->address_1; ?><br>
                                        <?php echo $warehouse->address_2; ?><br>
                                        <?php echo $warehouse->city; ?>, <?php echo $warehouse->state; ?><br>
                                        <?php echo $warehouse->country; ?>, <?php echo $warehouse->zip; ?><br>
                                        <strong>GST No:</strong> <?php echo $warehouse->gst_number; ?>
                                    </address>
                                <?php 
                                } else {
                                echo "******"; }
                                } else { ?>
                                    Not Shipped
                                <?php }
                                ?>
                            </div>
                            <div class="col-md-3">
                                <h4 class="font-primary">Order</h4>
                                <div class="">
                                    <strong>Order No:</strong>
                                    <span><?php echo $order->order_id; ?></span>
                                </div>
                                <div class="">
                                    <strong>Date:</strong>
                                    <span><?php echo date('M d, Y H:i A', $order->order_date); ?></span>
                                </div>
                                <div class="">
                                    <strong>Payment Type:</strong>
                                    <span><?php echo ucwords($order->order_payment_type); ?></span>
                                </div>
                                <div class="">
                                    <strong>Order Weight:</strong>
                                    <span><?= is_numeric($order->package_weight) ? round($order->package_weight / 1000, 2) : '0'; ?> Kg</span>
                                </div>
                                <?php if (!empty($shipping) && !empty($shipping->calculated_weight)) { ?>
                                <div class="">
                                    <strong>Calculated Order Weight:</strong>
                                    <span><?= is_numeric($shipping->calculated_weight) ? round($shipping->calculated_weight / 1000, 2) : '0'; ?> Kg</span>
                                </div>
                                <?php } ?>
                                <div class="">
                                    <strong>Dimension:</strong>
                                    <?php
                                    if (empty($order->package_length && $order->package_height && $order->package_breadth)) {
                                    ?>
                                        <span>Null</span>
                                    <?php
                                    } else {
                                    ?>
                                        <span>
                                            <?= $order->package_length . ' x ' . $order->package_height . ' x ' . $order->package_breadth; ?>
                                        </span>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <div class="">
                                    <?php if((isset($order->seller_applied_weight)) && (!empty($order->seller_applied_weight))){ ?>
                                        <hr style="    margin-top: 6px;margin-bottom: 6px;border: 0;border-top: 1px solid rgb(147 147 147 / 10%);">
                                    <div style="margin-top:4px;"><h5 style="font-size: 16px;"><?php echo isset($order->seller_applied_weight_status)?$order->seller_applied_weight_status:"Applied"?> Weight Details</h5>                    
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
                        <hr style="margin-top: 1rem;margin-bottom: 1rem;border: 0;border-top: 1px solid rgb(0 0 0 / 17%);">
                        
                    
                </div>
                <?php } ?>
                                
                                </div>    
                                <?php if (!empty($shipping)) { ?>
                                    <hr />
                                    <div>
                                        <?php if (!empty($courier)) { ?>
                                            <strong>Courier:</strong>
                                            <span><?= ucwords($courier->name); ?></span><br />
                                        <?php } ?>
                                        <strong>AWB:</strong>
                                        <span><a class="text-info" href="<?php echo base_url('awb/tracking');?>/<?= $shipping->awb_number; ?>" target="blank"><?= $shipping->awb_number; ?></a></span>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="col-md-3">
                                <h5>
                                    Customer Details
                                </h5>
                                <address class="m-t-10" id="oldinfo">
                                <?php if (in_array('view_customer_detail', $user_details->permissions)) { ?>
                                    <?php echo ucwords($order->shipping_fname . ' ' . $order->shipping_lname); ?><br>
                                    <?php echo $order->shipping_address; ?>,<?php echo $order->shipping_address_2; ?><br>
                                    <?php echo $order->shipping_city; ?>, <?php echo $order->shipping_state; ?> <?php echo $order->shipping_zip; ?><br>
                                    <?php echo $order->shipping_country; ?><br>
                                    <?php echo $order->shipping_phone; ?><br>
                                    <?php }else{ echo "******"; } ?>
                                </address>
                                <div style="display:none;" id="updatedname">
                                    <div class="card-body">
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>First Name</label>
                                                <input type="text" name="firstname" class="form-control" value="<?php echo $order->shipping_fname; ?>" placeholder="Enter First Name">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Last Name</label>
                                                <input type="text" name="lastname" value="<?php echo $order->shipping_lname; ?>" class="form-control" placeholder="Enter Last Name">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>First Address</label>
                                                <input type="text" class="form-control" name="firstadd" value="<?php echo $order->shipping_address; ?>" placeholder="Enter First Address">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Second Address</label>
                                                <input type="text" name="secondadd" value="<?php echo $order->shipping_address_2; ?>" class="form-control" placeholder="Enter Second Address">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>City</label>
                                                <input type="text" class="form-control" name="customercity" value="<?php echo $order->shipping_city; ?>" placeholder="Enter Customer City">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>State</label>
                                                <input type="text" name="customerstate" value="<?php echo $order->shipping_state; ?>" class="form-control" placeholder="Enter Customer State">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>Country</label>
                                                <input type="text" class="form-control" name="customercountry" value="<?php echo $order->shipping_country; ?>" placeholder="Enter Customer Country">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Zip Code</label>
                                                <input type="text" name="customerzipcode" value="<?php echo $order->shipping_zip; ?>" class="form-control" placeholder="Enter Zip Code.">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>Phone</label>
                                                <input type="hidden" name="orderid" value="<?php echo $order->order_id; ?>">
                                                <input type="text" name="customercell" value="<?php echo $order->shipping_phone; ?>" class="form-control" placeholder="Enter Customer Phone">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card m-b-30">
                <div class="card-body">
                    <div class="table-responsive">
                        <div class="table-responsive ">
                            <table class="table m-t-50">
                                <thead>
                                    <tr>
                                        <th class="text-center">Product</th>
                                        <th class="text-right">Product SKU</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-right">Product Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $k = 0;
                                    foreach ($products as $details) {
                                    ?>
                                        <tr>
                                            <td class="text-center" id="oldproductname">
                                                <p class="text-black m-0"><?php
                                                if (in_array('view_product_detail', $user_details->permissions)) {
                                                    echo $details->product_name;
                                                }else{ echo "******"; } ?></p>
                                            </td>
                                            <td class="text-right"><?php 
                                                if (in_array('view_product_detail', $user_details->permissions)) {
                                                echo $details->product_sku;
                                            } else {
                                                echo "******";
                                            } ?></td>
                                            <td class="text-center" id="oldproductqty"><?php echo $details->product_qty; ?>
                                            </td>
                                            <td class="text-right"><?php echo $details->product_price; ?></td>
                                        </tr>
                                    <?php
                                        $k++;
                                    }
                                    ?>
                                    <tr class="bg-light">
                                        <td colspan="2" class="text-right">Shipping Charge</td>
                                        <td colspan="2" class="text-right"><?php echo $order->shipping_charges; ?> ₹.</td>
                                    </tr>

                                    <tr class="bg-light">
                                        <td colspan="2" class="text-right">COD Charges</td>
                                        <td colspan="2" class="text-right"><?php echo $order->cod_charges; ?> Rs.</td>
                                    </tr>

                                    <tr class="bg-light">
                                        <td colspan="2" class="text-right">Discount Charge</td>
                                        <td colspan="2" class="text-right"><?php echo $order->discount; ?> ₹.</td>
                                    </tr>
                                    <tr class="bg-light">
                                        <td colspan="2" class="text-right">Grand Total</td>
                                        <td colspan="2" class="text-right"><?php echo $order->order_amount; ?> ₹.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END ROW-1 -->
</div>
<!-- END MAIN-CONTAINER -->
<div class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content" id="fulfillment_info">

        </div>
    </div>
</div>