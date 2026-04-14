<?php
$gstmatch = 0;
$pading = '5';
$padding_top = "-20px";
if (strtolower($orders->shipment_details->origin_state_code) == strtolower($orders->shipment_details->destination_state_code)) {
    $gstmatch = 1;
    $pading = '6';
}
?>

<div class="row">
    <div class="col-md-12 m-b-40">
        <div class="card">
            <div class="card-header" style="background-color: #edf2f9;">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="m-b-0">
                            <i class="mdi mdi-checkbox-intermediate"></i> Invoice
                        </h4>
                    </div>
                    <div class="col-md-6 text-right">

                        <button class="generate-invoice-button" rel="<?= $shipping_id ?>" id="printDiv1"> <i class="mdi
                     mdi-file-pdf"></i>
                        </button>
                        <!-- <button class="" id="printDiv"> <i class="mdi
                                mdi-printer"></i>
                            </button> -->
                    </div>
                </div>
            </div>
            <div class="container" id="printableArea">
                <div class="row">
                    <div class="col-md-12 m-b-40">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php 
                                        if (!empty($orders->company_details->cmp_logo)) { ?>
                                            <img width="140" src="<?php echo (strpos($orders->company_details->cmp_logo, "amazonaws.com") !== false) ? ($orders->company_details->cmp_logo) : (base_url() . 'assets/seller_company_logo/' . $orders->company_details->cmp_logo); ?>">
                                        <?php } else { ?>
                                            <img src="assets/img/logos/nytimes.jpg" width="140" alt=""> <?php } ?>

                                    </div>
                                    <div class="col-md-6 text-right my-auto">
                                        <h1 class="font-primary">Invoice</h1>
                                        <div class="">Invoice Number: <?= $orders->order->order_id; ?></div>
                                        <div class="">Date: <?= date('M d, Y',  $orders->shipment_details->invoice_date) ?></div>

                                    </div>
                                </div>
                                <hr>

                                <div class="row">
                                    <div class="col-md-6">
                                        <h4 class="font-primary">Shipping Address:</h4>
                                        <?php echo ucwords($orders->order->shipping_fname . ' ' . $orders->order->shipping_lname); ?><br>
                                        <?php echo $orders->order->shipping_address; ?>,<?php echo $orders->order->shipping_address_2; ?><br>
                                        <?php echo $orders->order->shipping_city; ?>, <?php echo $orders->order->shipping_state; ?> <?php echo $orders->order->shipping_zip; ?><br>
                                        <?php echo $orders->order->shipping_phone;
                                        ?>

                                        <?php if (!empty($orders->shipment_details->destination_state_code)) { ?> <br>
                                            State Code :
                                            <?= $orders->shipment_details->destination_state_code; ?>
                                        <?php } ?>


                                    </div>
                                    <div class="col-md-6 text-right my-auto">
                                        <h4 class="font-primary">Sold By:</h4>
                                        <?php if (!empty($orders->company_details->company_name)) { ?>
                                            <div class=""><?php echo ucwords($orders->company_details->company_name); ?></div>
                                        <?php } ?>
                                        <div class=""> <?= ucwords($orders->warehouse->address_1 . ', ' . $orders->warehouse->address_2 . '<br> ' . $orders->warehouse->city . ',' . $orders->warehouse->zip) ?></div>
                                        <?php if (!empty($orders->warehouse->gst_number)) { ?>
                                            <div class=""> GST :
                                                <?= $orders->warehouse->gst_number ?></div>

                                        <?php } ?>
                                        <?php if (!empty($orders->shipment_details->origin_state_code)) { ?> <br>
                                            <div class=""> State Code : <?= $orders->shipment_details->origin_state_code ?></div>

                                        <?php } ?>



                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h7 class="font-primary"><b>Payment Method:</b></h7> <?php echo ucwords($orders->order->order_payment_type); ?>
                                        <br>
                                        <h7 class="font-primary"><b>AWB No:</b></h7> <?php echo ucwords($orders->shipment_details->awb_number); ?>
                                    </div>
                                    <div class="col-md-6 text-right my-auto">

                                        <h7 class="font-primary"><b>Order Date:</b></h7> <?= date('M d, Y',  $orders->order->order_date) ?>
                                        <br>
                                        <h7 class="font-primary"><b>Shipped By:</b></h7> <?php echo ucwords($orders->courier->display_name); ?>


                                    </div>
                                </div>

                                <div class="table-responsive ">
                                    <table class="table m-t-50">
                                        <thead>
                                            <tr>
                                                <th class="">Product Name</th>
                                                <th class="text-center">HSN</th>
                                                <th class="text-center">Quantity</th>
                                                <th class="text-center">Unit Price</th>
                                                <th class="text-right">TAX Amount</th>
                                                <?php if (!empty($gstmatch)) { ?>
                                                    <th class="text-right">CGST (Value | %))</th>
                                                    <th class="text-right">SGST (Value | %)</th>

                                                <?php } else { ?>
                                                    <th class="text-right">IGST (Value | %)</th>
                                                <?php } ?>
                                                <th class="text-right">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $k = 0;
                                            $taxproducts = $igst = $total_product = $totalproductPrice = $productsprice = 0;
                                            
                                            foreach ($products as $details) {
                                              
                                                $totalproductPrice = $details->product_qty * $details->product_price;
                                                $igst = ($details->igst / 100) * $totalproductPrice;
                                                $productsprice = ($totalproductPrice - $igst) / $details->product_qty;
                                            ?>
                                                <tr>
                                                    <td><?php echo $details->product_name; ?></td>
                                                    <td class="text-center"><?php echo $details->hsn_code; ?></td>
                                                    <td class="text-center"><?php echo $details->product_qty; ?></td>
                                                    <td class="text-center"><?php echo round($productsprice, 2); ?></td>

                                                    <td class="text-center">
                                                        <?php if (!empty($igst)) echo round($igst, 2); ?>
                                                    </td>
                                                    <?php if (!empty($gstmatch)) {
                                                        $newgst = $details->igst / 2;
                                                    ?>
                                                        <td class="text-center">
                                                            <?php echo round($newgst, 2); ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php echo round($newgst, 2); ?></td>

                                                    <?php } else {

                                                    ?>
                                                        <td class="text-center">
                                                            <?php echo round($details->igst, 2); ?>
                                                        </td>

                                                    <?php } ?>

                                                    <td class="text-center">
                                                        <?= round($totalproductPrice, 2); ?>
                                                    </td>

                                                </tr>
                                            <?php
                                                $k++;
                                            }
                                            ?>

                                            <?php if ($orders->order->shipping_charges > 0) {

                                            ?>
                                                <tr class="bg-light">
                                                    <td colspan="<?= $pading ?>" class="text-right">Shipping Charges</td>

                                                    <td colspan="3" class="text-right"><?php echo round($orders->order->shipping_charges, 2); ?></td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($orders->order->cod_charges > 0) {
                                            ?>
                                                <tr class="bg-light">
                                                    <td colspan="<?= $pading ?>" class="text-right">COD Charges</td>
                                                    <td colspan="3" class="text-right"><?php echo round($orders->order->cod_charges, 2); ?></td>
                                                </tr>
                                            <?php } ?>
                                            <?php if ($orders->order->discount > 0) {
                                            ?>
                                                <tr class="bg-light">
                                                    <td colspan="<?= $pading ?>" class="text-right">Discount</td>
                                                    <td colspan="3" class="text-right"><?php echo round($orders->order->discount, 2); ?></td>
                                                </tr>
                                            <?php } ?>

                                            <tr class="bg-light">
                                                <td colspan="<?= $pading ?>" class="text-right">Grand Total</td>
                                                <td colspan="3" class="text-right">
                                                    Rs <?php echo round($orders->order->order_amount, 2); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <?php if (!empty($orders->company_details->cmp_signatureimg)) { ?>
                                        <table class="items" width="30%" style="padding: -26px;background-color:#eee;width: 20%;height:30;">
                                            <tr>
                                                <td> <img src="<?php echo (strpos($orders->company_details->cmp_signatureimg, "amazonaws.com") !== false) ? ($orders->company_details->cmp_signatureimg) : (base_url() . 'assets/seller_company_signatureimg/' . $orders->company_details->cmp_signatureimg); ?>" width="200" height="60" alt="" align="center" border="0"></td>
                                            </tr>
                                        </table>
                                        <p>
                                        <h4 style="font-size:14px;margin-left:66px;">Signature</h4>



                                    <?php } ?>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>