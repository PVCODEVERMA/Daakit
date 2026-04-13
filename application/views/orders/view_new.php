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
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/css/custom-design.css?v=1.0">
   <div class="row">
                    <div class="col-md-12">
                        <div class="card no-shadow custtom-bg1">
                            <div class="card-header header-bg rounded-top">
                                <h5 class="mb-0 text-white card-titles">Orders</h5>
                            </div>
                            <div class="card-body orders-view mt-3">
                                <div class="row">
                                    <div class="col-lg-4 col-md-12">
                                        <div class="card mb-4">
                                            <div class="d-flex border-bottom">
                                                <div class="col-4 pr-1">
                                                    <h6 class="my-3 fw-600">Order</h6>
                                                </div>
                                                <div class="col-8">
                                                    <ul class="my-3 list-unstyled view-list-details fw-400">
                                                    <?php if (!empty($order->channel_id)) { ?>
                                                         <?php if (!empty($channel)) { ?>
                                                        <li class="pb-1">Channel Name: <img src="<?= $channel['channel_icon'] ?>" class="rounded-circle" width="20" alt=""><?php echo ucwords(' ' . $channel['channel_name']); ?> </li>
                                                        <?php } } ?>
                                                        <li class="py-1">Order No: <?php echo $order->order_id; ?> </li>
                                                        <li class="py-1">Date: <?php echo date('M d, Y H:i A', $order->order_date); ?> </li>
                                                        <li class="py-1">Payment Type: <?php echo ucwords($order->order_payment_type); ?></li>
                                                        <li class="py-1">Order Status: <?= ucwords( $order->fulfillment_status); ?></li>
                                                        <?php if (($order->order_payment_type == 'reverse') && !empty($ordercategories[0]->categories_name)) { ?>

                                                            <li class="py-1">Category:<?= ucwords($ordercategories[0]->categories_name); ?> </li>
                                                            <li class="py-1">QC Check Status:<?= !empty($order->qccheck == '1') ? 'Yes' : 'No'; ?></li>
                                                            <?php } ?>
                                                    </ul>
                                                </div>
                                            </div>

                                            <?php if((isset($order->seller_applied_weight)) && (!empty($order->seller_applied_weight))){ ?>

                                                <div class="d-flex border-bottom">
                                                <div class="col-4 pr-1">
                                                    <h6 class="my-3 fw-600"><?php echo isset($order->seller_applied_weight_status)?$order->seller_applied_weight_status:"Applied"?> Weight Details (As per freeze weight)</h6>
                                                </div>
                                                <div class="col-8">
                                                    <ul class="my-3 list-unstyled view-list-details fw-400">
                                                    <?php if((isset($order->seller_applied_weight)) && (!empty($order->seller_applied_weight))){ ?>
                                                        <li class="pb-1">Weight: <?= is_numeric($order->seller_applied_weight) ? round($order->seller_applied_weight / 1000, 2) : '0'; ?> Kg </li>
                                                        <?php } ?>
                                                        <?php if((isset($order->seller_applied_length)) && (!empty($order->seller_applied_length)) && (isset($order->seller_applied_breadth)) && (!empty($order->seller_applied_breadth)) && (isset($order->seller_applied_height)) && (!empty($order->seller_applied_height)) ){ ?>
                                                        <li class="py-1">Dimension: <?= $order->seller_applied_length . ' x ' . $order->seller_applied_breadth . ' x ' . $order->seller_applied_height; ?> </li>
                                                        <?php } ?>
                                                   
                                                        <li class="py-1">Volumetric Weight:   <?php     if (!empty($order->seller_applied_length && $order->seller_applied_height && $order->seller_applied_breadth && @$courier->volumetric_divisor)) {
                                    $vol_weight = (($order->seller_applied_length * $order->seller_applied_breadth * $order->seller_applied_height) / $courier->volumetric_divisor) * 1000;
                                    if ($vol_weight >= 1000) {
                                        $vol_weight = round($vol_weight / 1000, 2) . ' Kg';
                                    } else if ($vol_weight >= 0) {
                                        $vol_weight = $vol_weight . ' g';
                                    }
                                    echo $vol_weight;
                                } else {
                                    echo "NULL";
                                }
                                ?>
                                
                            </li>
                                                     
                                                    </ul>
                                                </div>
                                            </div>
                                            <?php } else { ?>

                                            <div class="d-flex border-bottom">
                                                <div class="col-4 pr-1">
                                                    <h6 class="my-3 fw-600">Weight Details</h6>
                                                </div>
                                                <div class="col-8">
                                                    <ul class="my-3 list-unstyled view-list-details fw-400">
                                                        <li class="pb-1">Weight: <?= is_numeric($order->package_weight) ? round($order->package_weight / 1000, 2) : '0'; ?> Kg </li>
                                                        <li class="py-1">Dimension:  <?php
                                if (empty($order->package_length && $order->package_height && $order->package_breadth)) {
                                    echo "NULL";
                                 } else { ?>
                                    <?= $order->package_length . ' x ' . $order->package_breadth . ' x ' . $order->package_height; ?>
                                <?php } ?> </li>
                                                        <li class="py-1">Volumetric Weight:  <?php if (!empty($order->package_length && $order->package_height && $order->package_breadth && @$courier->volumetric_divisor)) {
                                    $vol_weight = (($order->package_length * $order->package_breadth * $order->package_height) / $courier->volumetric_divisor) * 1000;
                                    if ($vol_weight >= 1000) {
                                        $vol_weight = round($vol_weight / 1000, 2) . ' Kg';
                                    } else if ($vol_weight >= 0) {
                                        $vol_weight = $vol_weight . ' g';
                                    }
                                ?>
                                    <?= $vol_weight; ?>
                                <?php } else { echo "NULL"; } ?> </li>

                                <?php } ?>
                                <?php if (!empty($shipping->charged_weight) || !empty($shipping->calculated_weight)) { ?>
                                                        <li class="py-1">Charged Weight Slab: <?= (!empty($shipping->charged_weight) && is_numeric($shipping->charged_weight)) ? round($shipping->charged_weight / 1000, 2) : (!empty($shipping->calculated_weight) && is_numeric($shipping->calculated_weight) ? round($shipping->calculated_weight / 1000, 2) : '0'); ?> Kg </li>
                                                        <?php } ?>

                                                        <?php if (!empty($order->whatsapp_status)) { ?>
                                                        <?php $status = ''; ?>
                                                        <?php if ($order->whatsapp_status == 'confirm') { ?>
                                                            <?php $status = 'confirm';
                                                            ?>
                                                        <?php } else if ($order->whatsapp_status == 'cancel') { ?>
                                                            <?php $status = 'cancelled';
                                                            ?>
                                                        <?php } ?>
                                                        <li class="py-1"> Whatsapp Status: <?php echo ucwords(' ' . $status); ?></li>
                                                     <?php }  ?>

                                                     <?php if (!empty($order->ivr_calling_status)) { ?>
                                                        <?php $status = ''; ?>
                                                        <?php if ($order->ivr_calling_status == 'confirm') { ?>
                                                            <?php $status = 'verified';
                                                            ?>
                                                        <?php } elseif ($order->ivr_calling_status == 'cancel') { ?>
                                                            <?php $status = 'cancelled';
                                                            ?>
                                                        <?php } ?>
                                                        <li class="py-1"> IVR Status: <?php echo ucwords(' ' . $status); ?></li>
                                                            <?php }  ?>

                                                            <?php if (!empty($shipping->is_insurance)) { ?> 
                                                    <li class="py-1"> Is Insurance Opted: <?= ($shipping->is_insurance) ? 'Yes' : 'No'; ?> </li>
                                                   <?php } ?>
                                                       
                                                    </ul>
                                                </div>
                                            </div>
                                          


                                            <div class="d-flex">
                                                <div class="col-4 pr-1">
                                                    <h6 class="my-3 fw-600">Courier Details</h6>
                                                </div>
                                                <div class="col-8">
                                                    <ul class="my-3 list-unstyled view-list-details fw-400">
                                                        <li class="pb-1">Courier: Bluedart Express</li>
                                                        <li class="py-1">AWB: 80087109115 </li>
                                                        <li class="py-1">Essential: Yes </li>
                                                        <li class="py-1">EDD: 2023/02/11 </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-8 col-md-12">
                                        <div class="row">
                                            <div class="col-12 col-sm-3 mb-4 mb-sm-0 px-2">
                                                <div class="card">
                                                    <div class="card-media">
                                                        <img class="card-img-top" src="assets/img/new-design/ndr-dash/orders/1.png" alt="">
                                                    </div>
                                                    <div class="card-body px-3">
                                                        <h6 class="card-title fw-600">Warehouse Details</h6>
                                                        <p class="card-text mb-3 fw-400">GWALIOR<br> 9260086400 <br>206, City center</p>
                                                        <p class="card-text fw-400">
                                                            GWALIOR, MADHYA PRADESH India, 474001 <br>
                                                        </p>

                                                        <p class="card-text mt-4 fw-500">GST No:</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-3 mb-4 mb-sm-0 px-2">
                                                <div class="card">
                                                    <div class="card-media">
                                                        <img class="card-img-top" src="assets/img/new-design/ndr-dash/orders/2.png" alt="">
                                                    </div>
                                                    <div class="card-body px-3">
                                                        <h6 class="card-title fw-600">RTO Details</h6>
                                                        <p class="card-text fw-400">
                                                            Pickup Address and RTO Address are same
                                                        </p>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-3 mb-4 mb-sm-0 px-2">
                                                <div class="card">
                                                    <div class="card-media">
                                                        <img class="card-img-top" src="assets/img/new-design/ndr-dash/orders/3.png" alt="">
                                                    </div>
                                                    <div class="card-body px-3">
                                                        <h6 class="card-title fw-600">Shipping Details</h6>
                                                        <p class="card-text fw-400">
                                                            <span class="d-block mb-2">Pradeepta Biswal </span>Pl no D/12 Jagannathpur,<br> Pago daplanet Duplex Khurda, Odisha 752101 India
                                                        </p>
                                                        <p class="card-text mt-4 fw-400">7978452467</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-3 mb-4 mb-sm-0 px-2">
                                                <div class="card">
                                                    <div class="card-media">
                                                        <img class="card-img-top" src="assets/img/new-design/ndr-dash/orders/4.png" alt="">
                                                    </div>
                                                    <div class="card-body px-3">
                                                        <h6 class="card-title fw-600">Billing Details</h6>
                                                        <p class="card-text fw-400">
                                                            <span class="d-flex mb-2">Pradeepta Biswal</span>Pl no D/12 Jagannathpur, Pago daplanet Duplex Khurda, Odisha 752101 India
                                                        </p>
                                                        <p class="card-text mt-4 fw-400">7978452467</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="my-4 filter-btn text-right">
                                            <button class="btn mx-1 btn-light"><i class="mdi mdi-printer"></i> Print Lable</button>
                                            <button class="btn mx-1 btn-light"><i class="mdi mdi-receipt"></i> Print Invoice</button>
                                            <button class="btn mx-1 btn-light"><i class="mdi mdi-whatsapp"></i> Whatsapp Activee</button>
                                            <button class="btn mx-1 btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Clone</button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a href="#" class="dropdown-item"><i class="mdi mdi-plus"></i> Clone As Forward</a>
                                                <a href="#" class="dropdown-item"><i class="mdi mdi-plus"></i> Clone As Reverse</a>
                                                <a href="#" class="dropdown-item"><i class="mdi mdi-plus"></i> Clone As Reverse QC</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="card no-shadow custome-bg">
                                            <div class="card-body tab-data-sheet">
                                                <div class="table-responsive">
                                                    <table class="table table-sm align-td-middle table-card">
                                                        <thead>
                                                            <tr>
                                                                <th>Product</th>
                                                                <th>Product SKU </th>
                                                                <th>Quantity</th>
                                                                <th>Item Price</th>
                                                                <th>Procuct Total</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <p class="my-4">2 in1 Cervical and Migraine Pillow</p>
                                                                </td>
                                                                <td></td>
                                                                <td>01</td>
                                                                <td>999</td>
                                                                <td></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
<div class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" id="fulfillment_info">

        </div>
    </div>
</div>

<div class="modal fade" id="engage" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="slideRightModalLabel">Engage History</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="timeline" id="engagehistory">


                </div>
            </div>
        </div>
    </div>
</div>

<div id="myModal2" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="myModalLabel">Review Address # <span id="order_id"></span></h3>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form action="orders/whatsapp_addressUpdate" method="POST">
                <div class="modal-body" id="address_update">

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

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