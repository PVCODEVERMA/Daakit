<style>
    .label {
        /* width: 300px; */
        /* border: 1px solid #000; */
        /* padding: 10px; */
        margin: auto;
    }
    .logo {
        text-align: right;
    }
    .logo div {
        background-color: #000;
        color: #fff;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }
    .service-name {
        text-align: center;
        font-weight: bold;
        font-size: 9px;
    }
    .tracking {
        text-align: left;
        font-size: 9px;
        margin: 5px 0;
    }
    .tracking1 {
        text-align: end;
        font-size: 9px;
        margin: 5px 0;
        float:right;
    }
    .barcode {
        text-align: center;
        font-size: 9px;
    }
    .section {
        border-top: 1px solid #000;
        padding: 5px 0;
    }
    .section1 {
        border-bottom: 1px solid #000;
        padding: 5px 0;
    }
    .address {
        font-size: 9px;
        line-height: 1.5;
        margin-left: 5px;
    }
    .footer {
        font-size: 10px;
        text-align: justify;
    }
    table {
        font-size: 9px;
    }
    p {
        font-size: 9px;
        font-family: Times New Roman, serif !important;
    }
</style>
<div class="label">
    <?php  if (isset($channels_brand_logo) && !empty($channels_brand_logo->channel_brand_logo)  ) { ?>
    <div class="logo">
        <?php if (empty($channels_brand_logo->channel_brand_logo) ) {  ?>
            <img src="<?php echo base_url('assets/images/dakit-favicon.gif');?>" width="50" height="50">
        <?php } ?>
        <?php  if (isset($channels_brand_logo) && !empty($channels_brand_logo->channel_brand_logo)  ) { ?>
            <img src="<?php echo $channels_brand_logo->channel_brand_logo;?>" width="50" height="50">
        <?php } ?>
    </div>
    <?php } ?>
    <div class="section1">
        <div class="address">
            <b>To:</b><br>
            <b><?= ucwords($order_info->shipping_fname . ' ' . $order_info->shipping_lname); ?></b><br>
            <?php if (!empty($order_info->shipping_company_name)) { ?>
            <?= ucwords($order_info->shipping_company_name); ?><br>
            <?php } ?>
            <?= ucwords($order_info->shipping_address . ', ' . $order_info->shipping_address_2 . ', ' . $order_info->shipping_city . ', ' . $order_info->shipping_state . ', ' . $order_info->shipping_country . ' - ' . $order_info->shipping_zip) ?> <br>
            MOBILE NO: <b><?= $order_info->shipping_phone; ?></b>
        </div>
    </div>
    <p class="tracking">
        <table style="width: 100%;padding: 5px;padding-left: 15px; padding-right: 15px;border-bottom: 1px solid;">
            <tbody>
                <tr>
                    <td style="text-align:left; width: 60%">
                        <b><?= strtoupper($courier->display_name); ?></b>
                        <p><?php
                            $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
                            echo '<img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment->awb_number, $generator::TYPE_CODE_128)) . '" style= height="45px" width="180px">';
                            ?>
                        </p>
                        <p style="text-align:center;">Tracking ID # <?= $shipment->awb_number; ?></p>
                        <p>
                            Dimensions (cm): <?= ($order_info->package_length > 0) ? $order_info->package_length : '0'; ?> X <?= ($order_info->package_breadth > 0) ? $order_info->package_breadth : '0'; ?> X <?= ($order_info->package_height > 0) ? $order_info->package_height : '0'; ?>
                        </p>
                    </td>
                    <td style="width:40%; text-align: right;">
                        <?php if (strtolower($order_info->order_payment_type) == 'cod') { ?>
                            <b><?= strtoupper($order_info->order_payment_type); ?></b><br>
                            <b>&#8377;<?= round($order_info->order_amount, 2); ?></b>
                        <?php
                        } elseif (strtolower($order_info->order_payment_type) == 'reverse') {
                        ?>
                            <p>PICKUP</p>
                        <?php
                        } else {
                        ?>
                            <p>PAID</p>
                        <?php } ?>
                        <p>WEIGHT : <?= ($order_info->package_weight > 0) ? round($order_info->package_weight / 1000, 1) : '0.5'; ?> KG</p>
                        <p>Order ID : <?= $order_info->order_no; ?></p>
                    </td>
                </tr>
            </tbody>
        </table>
    
    </p>

    <div class="barcode">
        <?php
        // $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        // echo '<img src="data:image/png;base64,' . base64_encode($generator->getBarcode($shipment->awb_number, $generator::TYPE_CODE_128)) . '" style= height="45px" width="180px">';
        ?>
    </div>
    <div class="section">
    <?php if ($warehouse->hide_label_products != '1') { ?>
        <table style="width: 100%;padding: 5px;border-collapse: collapse; margin:5px">
            <thead>
                <tr>
                    <th style="text-align:center;border: 1px solid black;">Item</th>
                    <th style="text-align:center;border: 1px solid black;">SKU</th>
                    <th style="text-align:center;border: 1px solid black;">Qty.</th>
                    <th style="text-align:center;border: 1px solid black;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($products)) {
                    foreach ($products as $product) {
                ?>
                        <tr>
                            <td style="text-align:center;border: 1px solid black;">
                                <?= ucwords($product->product_name); ?>
                            </td>
                            <td style="text-align:center;border: 1px solid black;">
                                <?= !empty($product->product_sku) ? strtoupper(wordwrap($product->product_sku, 13, "<br>\n", true))  : 'N/A'; ?>
                            </td>
                            <td style="text-align:center;border: 1px solid black;">
                                <?= $product->product_qty; ?>
                            </td>
                            <td style="text-align:center;border: 1px solid black;">
                            <?php 
                        $prod_price=round($product->product_price * $product->product_qty, 2);
                        if($prod_price>0){ ?>
                            &#8377;<?php echo $prod_price; ?>
                        <?php } ?>
                            </td>
                        </tr>
                <?php
                    }
                }
                ?>

                <?php if (!empty($order_info->shipping_charges) && $order_info->shipping_charges > 0) { ?>
                    <tr>
                        <td style="text-align:center;border: 1px solid black;">

                        </td>
                        <td style="text-align:center;border: 1px solid black;">
                            Shipping Charges
                        </td>
                        <td style="text-align:center;border: 1px solid black;">

                        </td>
                        <td style="text-align:center;border: 1px solid black;">
                            &#8377;<?= round($order_info->shipping_charges, 2); ?>
                        </td>
                    </tr>
                <?php } ?>

                <?php if (!empty($order_info->cod_charges) && $order_info->cod_charges > 0) { ?>
                    <tr>
                        <td style="text-align:center;border: 1px solid black;">

                        </td>
                        <td style="text-align:center;border: 1px solid black;">
                            COD Charges
                        </td>
                        <td style="text-align:center;border: 1px solid black;">

                        </td>
                        <td style="text-align:center;border: 1px solid black;">
                            &#8377;<?= round($order_info->cod_charges, 2); ?>
                        </td>
                    </tr>
                <?php } ?>

                <?php if (!empty($order_info->tax_amount) && $order_info->tax_amount > 0) { ?>
                    <tr>
                        <td style="text-align:center;border: 1px solid black;">

                        </td>
                        <td style="text-align:center;border: 1px solid black;">
                            Tax Amount
                        </td>
                        <td style="text-align:center;border: 1px solid black;">

                        </td>
                        <td style="text-align:center;border: 1px solid black;">
                            &#8377;<?= round($order_info->tax_amount, 2); ?>
                        </td>
                    </tr>
                <?php } ?>

                <?php if (!empty($order_info->discount) && $order_info->discount > 0) { ?>
                    <tr>
                        <td style="text-align:center;border: 1px solid black;">

                        </td>
                        <td style="text-align:center;border: 1px solid black;">
                            Discount Applied
                        </td>
                        <td style="text-align:center;border: 1px solid black;">

                        </td>
                        <td style="text-align:center;border: 1px solid black;">
                            &#8377;<?= round($order_info->discount, 2); ?>
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <td style="text-align:center;border: 1px solid black;">

                    </td>
                    <td style="text-align:center;border: 1px solid black;">
                        Order Total
                    </td>
                    <td style="text-align:center;border: 1px solid black;">

                    </td>
                    <td style="text-align:center;border: 1px solid black;">
                        &#8377;<?= round($order_info->order_amount, 2); ?>
                    </td>
                </tr>

            </tbody>
        </table>
        <?php } ?>
    </div>
    <div class="section">
        <div class="address">
                <?php if ($warehouse->hide_label_address != '1') { ?>
                <p>
                    <?php if ($shipment->is_rto_different) { ?>
                        <b>Pickup Address:</b><br />
                    <?php } else { ?>
                        <b>Pickup and Return Address:</b><br />
                    <?php } ?>
                    <b><?= ucwords($warehouse->name);?></b><br>
                    <?= ucwords($warehouse->contact_name) . ' ' . $warehouse->address_1 . ' ' . $warehouse->address_2 . ' ' . $warehouse->city . ', ' . $warehouse->state . ', ' . $warehouse->country . ' - ' . $warehouse->zip; ?><br>
                    <?php if ($warehouse->hide_label_pickup_mobile != '1') { ?>
                        Mobile No.: <?= $warehouse->phone; ?>
                    <?php } ?>
                    <?php if (!empty($warehouse->gst_number)) { ?> &nbsp; GST No: <?= strtoupper($warehouse->gst_number) ?> <?php } ?>
                </p>
                <?php if ($shipment->is_rto_different) { ?>
                    <p>
                        <b>Return Address:</b><br />
                        <b><?= ucwords($rto_warehouse->name);?></b><br>
                        <?= ucwords($rto_warehouse->name) . ' ' . $rto_warehouse->address_1 . ' ' . $rto_warehouse->address_2 . ' ' . $rto_warehouse->city . ', ' . $rto_warehouse->state . ', ' . $rto_warehouse->country . ' - ' . $rto_warehouse->zip; ?><br>
                        <?php if ($rto_warehouse->hide_label_pickup_mobile != '1') { ?>
                            Mobile No.: <?= $rto_warehouse->phone; ?>
                        <?php } ?>
                    </p>
                <?php } ?>
            <?php } ?>
            <?php if (!empty($warehouse->support_phone) || !empty($warehouse->support_email)) {  ?>
                <p>
                    <b>For any query please contact:</b><br />
                    <?= !empty($warehouse->support_phone) ? '<b>Mobile:</b> ' . $warehouse->support_phone : '';  ?> <?= !empty($warehouse->support_email) ? ' <b>Email:</b> ' . $warehouse->support_email : '';  ?>
                </p>
            <?php } ?>        
        </div>
    </div>
    <div class="footer">
        This document is generated by a computer system and therefore does not require a signature.<br>
    </div>
    <div class="logo" style="margin-right:30px">
        <img src="<?php echo base_url('assets/images/dakit-favicon.gif');?>" width="50" height="50">
    </div>
    <div class="footer" style="margin-top:5px;text-align:right">
        Powered by : <b>Daakit</b> Technologies Pvt. ltd
    </div>
</div>
