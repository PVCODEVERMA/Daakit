<html>

<head>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sofia">
    <style>
        body {
            padding: 259px;
            margin-top: -230px;
            font-family: "Poppins", sans-serif;
            font-size: 4px;
            background-color: #eee;
        }

        p {
            margin: 0pt;
            line-height: 1.9;
        }

        table.items {
            border: 0.1mm solid #e7e7e7;
        }

        td {
            vertical-align: top;
        }

        .items td {
            border-left: 0.1mm solid #e7e7e7;
            border-right: 0.1mm solid #e7e7e7;
        }

        table thead td {
            text-align: center;
            border: 0.1mm solid #e7e7e7;
        }

        .items td.blanktotal {
            background-color: #EEEEEE;
            border: 0.1mm solid #e7e7e7;
            background-color: #FFFFFF;
            border: 0mm none #e7e7e7;
            border-top: 0.1mm solid #e7e7e7;
            border-right: 0.1mm solid #e7e7e7;
        }

        .items td.totals {
            text-align: right;
            border: 0.1mm solid #e7e7e7;
        }

        .items td.cost {
            text-align: "."center;
        }

        .abc {
            border: 1px solid #000;        }

        .vl {
            border-left: 1px solid #9e9292;
            height: 227px;
        }

        .abc {
            border-radius: 5px;
            height: 1200px;
        }


        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;1,400&display=swap');



        .show h2 {
            font-family: 'Poppins', sans-serif;
            text-transform: capitalize;
            font-size: 18px;
        }

        .show h3 {
            border-bottom: 2px solid rgb(185, 185, 185);
            font-size: 30px;
            padding-bottom: 10px;
        }

        .cstm-switch {
            margin-left: 14px;
            margin-bottom: 10px;
        }

        .logo-nim img {
            width: 100px;
        }

        .invoice p {
            font-weight: 600;
        }

        .invoice input {
            margin-top: -11px !important;
            margin: 0;
        }

        .input-content .first-p {

            font-family: 'Poppins', sans-serif;
            text-transform: capitalize;
            font-size: 18px;
        }



        .input-content .second-p {
            font-size: 12px;
            font-weight: 600;
        }

        .banner-size p {
            margin-left: 3px;
            /* margin-top: 30px; */
            margin-bottom: 0;
            font-family: 'Poppins', sans-serif;
            text-transform: capitalize;
            font-size: 12px;
            font-weight: 600;
        }

        .banner-size {
            margin-top: 30px;
        }

        .banner-size span {

            font-family: 'Poppins', sans-serif;
            text-transform: capitalize;
            font-size: 18px;

        }

        .signature p {
            font-family: 'Poppins', sans-serif;
            text-transform: capitalize;
            font-size: 18px;
        }

        .custom-file-input {
            position: relative;
            z-index: 2;
            width: 100%;
            height: calc(1.5em + 0.75rem + 2px);
            margin: 0;
            opacity: 0;
        }

        .fileinput {
            width: 100%;
        }

        .signature {
            margin-top: 37px;
        }

        .customize-field .second-p {
            font-size: 12px;
            font-weight: 600;
        }

        .customize-field .field {
            font-family: 'Poppins', sans-serif;
            text-transform: capitalize;
            font-size: 18px;
        }

        .signature .three {
            font-size: 12px;
            margin-top: 1px;
            font-weight: 600;
        }

        .tool {
            display: flex;
            align-items: baseline;
        }

        .had {
            border: 1px solid #e3e3e3 !important;
            border-bottom: none !important;

        }

        .had1 {
            border-right: none !important;
        }

        #part1 {
            border-right: none !important;
        }




        .card-back {
            background-color: transparent;
            box-shadow: none;
        }

        .card-back:hover {
            box-shadow: none;
        }

        .part {
            border: 1px solid #e3e3e3 !important;
            border-top: none;
        }

        .buttons {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .buttons img {
            width: 35px;
        }



        /* responcive start */


        @media (max-width: 768px) {
            .had1 {
                border-right: 1px solid #e3e3e3 !important;
            }

            #part1 {
                border-right: 1px solid #e3e3e3 !important;
            }
        }
    </style>
</head>
        <?php
    $i = '1';

    foreach ($shipments as $orders) {
        $billingtitle = 'Shipping';
        $gstmatch = 0;
        $padding_top = "-20px";
        if (strtolower($orders->shipment_details->origin_state_code) == strtolower($orders->shipment_details->destination_state_code)) {
            $gstmatch = 1;
        }
        if (!empty($orders->order->shipping_charges)) {
            $padding_top = "-40px";
        }
        if (!empty($orders->order->cod_charges)) {
            $padding_top = "-40px";
        }
        if (!empty($orders->order->discount)) {
            $padding_top = "-40px";
        }
        if (!empty($orders->order->shipping_charges) && !empty($orders->order->cod_charges) && !empty($orders->order->discount)) {
            $padding_top = "-80px";
        }
    ?>
        <div class="abc">
            <table width="100%" cellpadding="10">
                <tr>
                    <td width="100%" style="padding: 20px; ">
                        <h3>
                            <center style="font-size:15px;">TAX INVOICE</center>
                        </h3>
                    </td>
                </tr>
            </table>
            <?php ;?>
            <table width="100%" cellpadding="10">
                <tr>
                    <td width="70%">
                        <!-- <h4 style="font-size:16px;">Invoice</h4> -->
                        <!-- <P style="font-size:14px;">Invoice Number - <?php if (!empty($orders->invoice_settings->invoice_prefix)) {
                                                                        echo strtoupper($orders->invoice_settings->invoice_prefix);
                                                                    }
                                                                    echo $orders->order->order_no; ?></P>
                        <P style="font-size:14px;">Invoice Date - <?= date('M d, Y',  $orders->shipment_details->created) ?></P> -->
                    </td>
                    <!-- <td width="30%" style="padding:0px 20px; text-align:right">
                        <?php if (!empty($orders->invoice_settings->invoice_banner) && (isset($orders->channels_brand_logo) && empty($orders->channels_brand_logo->channel_brand_logo)  )) {  ?>
                            <img src="<?php echo $orders->invoice_settings->invoice_banner; ?>" style="max-width:300px; max-height:80px;">
                        <?php } ?>
                        <?php  if (isset($orders->channels_brand_logo) && !empty($orders->channels_brand_logo->channel_brand_logo)  ) {  ?>
                            <img src="<?php echo $orders->channels_brand_logo->channel_brand_logo; ?>" style="max-width:300px; max-height:80px;">
                        <?php } ?> -->

                    </td>

                </tr>
            </table>
            <table width="100%" cellpadding="10">
                <tr>
                    <td width="33.33%">
                        <?php if (!empty($orders->order->billing_fname) && !empty($orders->order->billing_zip)) { ?>
                            <h4 style="font-size:16px;padding:5px;">Billing Address:</h4> <br>
                            <?php if (!empty($orders->order->billing_company_name)) {
                                echo '<p style="margin-top: -19px;line-height:2.7;font-size:14px;padding:5px;">' . ucwords($orders->order->billing_company_name) . '</p><br>';
                            } ?>
                            <p style="margin-top: -19px;line-height:2.7;font-size:14px;padding:5px;"><?php echo ucwords($orders->order->billing_fname . ' ' . $orders->order->billing_lname); ?></p><br>
                            <p style="margin-top: -19px;line-height:2.7;font-size:14px;padding:5px;"><?= ucwords($orders->order->billing_address . ' ' . $orders->order->billing_address_2) ?></p><br>
                            <p style="margin-top: -19px;line-height:2.7;font-size:14px;padding:5px;"><?= ucwords($orders->order->billing_city . '  ' . $orders->order->billing_state) ?></p><br>
                            <p style="margin-top: -19px;line-height:2.7;font-size:14px;padding:5px;"><?= $orders->order->billing_zip ?></p><br />
                            <p style="margin-top: -19px;line-height:2.7;font-size:14px;padding:5px;">GST No : <?= strtoupper($orders->order->billing_gst_number) ?></p>
                            <?php if (!empty($orders->shipment_details->destination_state_code)) { ?> <br>
                                <p style="margin-top: -19px;line-height:2.7;font-size:14px;padding:5px;"> State Code :
                                    <?= $orders->shipment_details->destination_state_code; ?></p>
                            <?php } ?><br><br>
                        <?php $billingtitle = 'Shipping';
                        } ?>
                        <h3 style="font-size:16px;padding:5px;"> <?= $billingtitle ?> Address :</h3> <br>
                        <?php if (!empty($orders->order->shipping_company_name)) {
                            echo '<p style="margin-top: -19px;line-height:2.7;font-size:14px;padding:5px;">' . ucwords($orders->order->shipping_company_name) . '</p><br>';
                        } ?>
                        <p style="margin-top: -19px;line-height:2.7;font-size:14px;padding:5px;"><?php echo ucwords($orders->order->shipping_fname . ' ' . $orders->order->shipping_lname); ?></p><br>
                        <p style="margin-top: -19px;line-height:2.7;font-size:14px;padding:5px;"><?= ucwords($orders->order->shipping_address . ', ' . $orders->order->shipping_address_2) ?></p><br>
                        <p style="margin-top: -19px;line-height:2.7;font-size:14px;padding:5px;"><?= ucwords($orders->order->shipping_city . ', ' . $orders->order->shipping_state) ?></p><br>
                        <p style="margin-top: -19px;line-height:2.7;font-size:14px;padding:5px;"><?= $orders->order->shipping_zip ?></p>
                        <?php if (!empty($orders->shipment_details->destination_state_code) && $billingtitle == 'Shipping') { ?> <br>
                            <p style="margin-top: -19px;line-height:2.7;font-size:14px;padding:5px;"> State Code :
                                <?= $orders->shipment_details->destination_state_code; ?></p>
                        <?php } ?>

                    </td>
                    <td width="33.33%" style="float:right;text-align:left">
                        <h4 style="margin-left: 79px;font-size:16px;padding:5px;">Sold By:</h4> <br>
                        <?php if (!empty($orders->company_details->company_name)) { ?>

                            <?php if (isset($orders->invoice_settings) && empty($orders->invoice_settings->hide_compony)) { ?>

                                <p style="margin-top: -19px;line-height:1.7;font-size:14px;margin-left: 89px;padding:5px;">
                                    <?php echo ucwords($orders->company_details->company_name); ?>
                                </p><?php } ?>
                        <?php } ?>
                        <br>
                        <p style="margin-top: -19px;line-height:1.7;font-size:14px;margin-left: 12px;padding:5px;"><?php echo $orders->warehouse->name; ?></p>
                        <br>
                        <p style="margin-top: -19px;line-height:1.7;font-size:14px;margin-left: 35px;padding:5px;">
                            <?= ucwords($orders->warehouse->address_1 . ', ' . $orders->warehouse->address_2 . ', ' . $orders->warehouse->city . ',' . $orders->warehouse->zip) ?></p>
                        <?php if (!empty($orders->warehouse->gst_number)) { ?> <br>
                            <p style="margin-top: -19px;line-height:1.7;font-size:14px;margin-left: 35px;padding:5px;"> GST :
                                <?= $orders->warehouse->gst_number ?></p>
                        <?php } ?>

                        <?php if (!empty($orders->shipment_details->origin_state_code)) { ?> <br>
                            <p style="margin-top: -19px;line-height:1.7;font-size:14px;margin-left: 35px;padding:5px;"> State Code :
                                <?= $orders->shipment_details->origin_state_code ?></p>
                        <?php } ?> <br><br><br><br><br><br><br><br><br>
                        <table width="90%" cellpadding="10">
                            <tr>
                                <td width="100%" style="float:right;text-align:right;" align="left">
                                    <?php $custom_name = $custom_value = array();
                                    if (isset($orders->invoice_settings) && !empty($orders->invoice_settings->custom_name)) {
                                        $custom_name = explode(",", $orders->invoice_settings->custom_name);
                                    }
                                    if (isset($orders->invoice_settings) && !empty($orders->invoice_settings->custom_value)) {
                                        $custom_value = explode(",", $orders->invoice_settings->custom_value);
                                    }

                                    if (!empty($custom_value) && !empty($custom_name)) {
                                        echo "</br>"; ?>

                                        <table class="items" width="100%" style="font-size: 12px;margin-top:-35px; border-collapse: collapse;" align="left">
                                            <thead>
                                                <!--  <tr>
                        <td width="35%" style="border: 1px solid #000; padding: 10px; text-align: left;">
                            <strong>
                                <center style="padding:10px;">Column Name</center>
                            </strong>
                        </td>
                        <td width="35%" style="border: 1px solid #000; padding: 10px; text-align: left;">
                            <strong>
                                <center style="padding:10px;">Column Value </center>
                            </strong>
                        </td>
                    </tr>-->
                                            <!-- </thead>
                                            <tbody>
                                                <?php foreach ($custom_name as $ck => $cn) { ?>
                                                    <tr>
                                                        <td style="border: 1px solid #000; padding: 10px;">
                                                            <?php echo ucfirst($cn); ?>
                                                        </td>
                                                        <td style="border: 1px solid #000; padding: 10px;">
                                                            <?php echo ucfirst($custom_value[$ck]); ?>
                                                        </td>
                                                    <?php } ?>

                                            </tbody> -->
                                        </table>
                                    <?php } ?>

                            </tr>
                        </table>
                    </td>
                    <td width="33.33%">
                        <h3 style="font-size:16px;padding:5px;"> INVOICE DETAILS: </h3> <br>
                        <p style="margin-top: -19px;line-height:2.7;font-size:14px;padding:5px;">INVOICE NO. : <?php if (!empty($orders->invoice_settings->invoice_prefix)) {
                                                                        echo strtoupper($orders->invoice_settings->invoice_prefix);
                                                                    }
                                                                    echo $orders->order->order_no; ?></p><br>
                        <p style="margin-top: -19px;line-height:2.7;font-size:14px;padding:5px;">INVOICE DATE : <?= date('M d, Y',  $orders->shipment_details->created) ?></p><br>
                        <p style="margin-top: -19px;line-height:2.7;font-size:14px;padding:5px;">ORDER NO. : <?= $orders->order->order_no; ?></p>
                        <p style="margin-top: -19px;line-height:2.7;font-size:14px;padding:5px;">ORDER DATE : <?= date('M d, Y',  $orders->order->order_date) ?></p>
                        <p style="margin-top: -19px;line-height:2.7;font-size:14px;padding:5px;">FULLFILLED BY : <?php echo ucwords($orders->courier->display_name); ?></p>
                        <p style="margin-top: -19px;line-height:2.7;font-size:14px;padding:5px;">AWB NO. : <?php echo ucwords($orders->shipment_details->awb_number); ?></p>
                        <p style="margin-top: -19px;line-height:2.7;font-size:14px;padding:5px;">PAYMENT METHOD : <?php echo ucwords($orders->order->order_payment_type); ?></p>
                    </td>
                </tr>
            </table>

            <p></p>                                                    
            <table width="100%" cellpadding="10">
                <tr>
                    <td width="49%" style="padding: 20px; ">
                    <td width="2%">&nbsp;</td>
                    </td>
                    <td width="2%">&nbsp;</td>
                    <td width="49%" style="float:right;">
                    <td width="2%">&nbsp;</td>
                    </td>
                </tr>
            </table>
            <p>
            <table class="items" width="100%" style="font-size: 12px;margin-top:-35px; border-collapse: collapse;margin:10px">
                <thead>
                    <tr>
                        <td width="25%" style="border: 1px solid #000; padding: 10px; text-align: left;">
                            <strong>
                                <center style="padding:10px;">Product</center>
                            </strong>
                        </td>

                        <td width="14%" style="border: 1px solid #000; padding: 10px; text-align: left;">
                            <strong>
                                <center style="padding:10px;">SKU</center>
                            </strong>
                        </td>
                        <!-- <td width="9%" style="border: 1px solid #000; padding: 10px; text-align: left;">
                            <strong>
                                <center style="padding:10px;">HSN</center>
                            </strong>
                        </td> -->
                        <td width="12%" style="border: 1px solid #000; padding: 10px; text-align: left;">
                            <strong>
                                <center style="padding:10px;">Qty.</center>
                            </strong>
                        </td>
                        <td width="12%" style="border: 1px solid #000; padding: 10px; text-align: left;">
                            <strong>
                                <center style="padding:10px;">Amount</center>
                            </strong>
                        </td>
                        <!-- <td width="12%" style="border: 1px solid #000; padding: 10px; text-align: left;">
                            <strong>
                                <center style="padding:10px;">TAX Amount</center>
                            </strong>
                        </td>
                        <?php if (!empty($gstmatch)) { ?>
                            <td width="12%" style="border: 1px solid #000; padding: 10px; text-align: left;">
                                <strong>
                                    <center style="padding:10px;">CGST (Value | %)</center>
                                </strong>
                            </td>
                            <td width="12%" style="border: 1px solid #000; padding: 10px; text-align: left;">
                                <strong>
                                    <center style="padding:10px;">SGST (Value | %)</center>
                                </strong>
                            </td>
                        <?php } else { ?>
                            <td width="12%" style="border: 1px solid #000; padding: 10px; text-align: left;">
                                <strong>
                                    <center style="padding:10px;">IGST (Value | %)</center>
                                </strong>
                            </td>
                        <?php } ?> -->
                        <td width="12%" style="border: 1px solid #000; padding: 10px; text-align: left;">
                            <strong>
                                <center style="padding:10px;">TOTAL</center>
                            </strong>
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $k = 0;
                    $taxproducts = $igst = $igstfinal = $total_product_amt = $totalproductPrice = $productsprice = 0;
                    foreach ($orders->products as $details) {
                        $totalproductPrice = $details->product_qty * $details->product_price;
                        $p_igst = (!empty($details->igst) && !is_nan($details->igst)) ? $details->igst : 0;
                        $igstfinal = $p_igst + 100;
                        $productsprice = ($totalproductPrice * 100) / $igstfinal;
                        $igst = $totalproductPrice - $productsprice;
                        $productsprice = $productsprice / $details->product_qty;
                        $total_product_amt+=$totalproductPrice;
                    ?>
                        <tr>
                            <td style="border: 1px solid #000; padding: 10px;">
                                <center><?php echo $details->product_name; ?></center>
                            </td>
                            <td style="border: 1px solid #000; padding: 10px;">
                                <center><?php echo $details->product_sku; ?></center>
                            </td>
                            <!-- <td style="border: 1px solid #000; padding: 10px;">
                                <center><?php echo $details->hsn_code; ?> </center>
                            </td> -->
                            <td style="border: 1px solid #000; padding: 10px;">
                                <center><?php echo $details->product_qty; ?></center>
                            </td>
                            <td style="border: 1px solid #000; padding: 10px;">
                                <center><?php echo round($productsprice, 2); ?></center>
                            </td>
                            <!-- <td style="border: 1px solid #000; padding: 10px;">
                                <center><?php if (!empty($igst)) echo round($igst, 2); ?></center>
                            </td>
                            <?php if (!empty($gstmatch)) {
                                $newgst = $details->igst / 2;
                            ?>
                                <td style="border: 1px solid #000; padding: 10px;">
                                    <center><?php echo round($newgst, 2); ?></center>
                                </td>
                                <td style="border: 1px solid #000; padding: 10px;">
                                    <center><?php echo round($newgst, 2); ?></center>
                                </td>
                            <?php } else { ?>
                                <td style="border: 1px solid #000; padding: 10px;">
                                    <center><?php echo round((float)$details->igst, 2); ?></center>
                                </td>
                            <?php } ?> -->
                            <td style="border: 1px solid #000; padding: 10px;">
                                <center><?= round($totalproductPrice, 2); ?></center>
                            </td>
                        </tr>
                    <?php $k++;
                    } ?>
                    <tr>
                        <td style="border: 1px solid #000; padding: 10px;text-align:right" colspan="4">
                            <b>Net Total</b>
                        </td>
                        <td style="border: 1px solid #000; padding: 10px;">
                            <center><?php echo $total_product_amt; ?></center>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p>&nbsp;
            <p>
            <p>&nbsp;
            <p>
            <!-- <table width="100%" style=" font-size: 12px;margin-top: <?php echo $padding_top; ?>;">
                <tr>
                    <td>
                        <table width="30%" align="left" style=" font-size: 12px;">
                            <?php if ($orders->order->shipping_charges > 0) { ?>
                                <tr>
                                    <td style="padding: 0px; line-height: 20px;">&nbsp;<br></td>
                                </tr>
                            <?php } ?>
                            <?php if ($orders->order->tax_amount > 0) { ?>
                                <tr>
                                    <td style="padding: 0px; line-height: 20px;">&nbsp;<br></td>
                                </tr>
                            <?php } ?>
                            <?php if ($orders->order->cod_charges > 0) { ?>
                                <tr>
                                    <td style="padding: 0px; line-height: 20px;">&nbsp;<br></td>
                                </tr>
                            <?php } ?>
                            <?php if ($orders->order->discount > 0) { ?>
                                <tr>
                                    <td style="padding: 0px; line-height: 20px;">&nbsp;<br></td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <td style="padding: 0px; line-height: 20px;">&nbsp;<br></td>
                            </tr>
                        </table>
                        <table width="70%" align="right" style="font-size: 32px;">

                            <thead>
                                <tr>
                                    <td width="20%" style="padding-left: 20px; border: 1px solid #000; padding: 10px; text-align: left;">
                                        <strong>
                                            Charges Applied
                                        </strong>
                                    </td>
                                    <td width="12%" style="border: 1px solid #000; padding: 10px; text-align: left;">
                                        <strong>
                                            <center style="padding:10px;"> Tax Amount</center>
                                        </strong>
                                    </td>
                                    <?php if (!empty($gstmatch)) { ?>

                                        <td width="12%" style="border: 1px solid #000; padding: 10px; text-align: left;">
                                            <strong>
                                                <center style="padding:10px;">CGST (Value | %)</center>
                                            </strong>
                                        </td>
                                        <td width="12%" style="border: 1px solid #000; padding: 10px; text-align: left;">
                                            <strong>
                                                <center style="padding:10px;">SGST (Value | %)</center>
                                            </strong>
                                        </td>
                                    <?php } else { ?>
                                        <td width="12%" style="border: 1px solid #000; padding: 10px; text-align: left;">
                                            <strong>
                                                <center style="padding:10px;">IGST (Value | %)</center>
                                            </strong>
                                        </td>
                                    <?php } ?>
                                    <td width="12%" style="border: 1px solid #000; padding: 10px; text-align: left;">
                                        <strong>
                                            <center style="padding:10px;">TOTAL</center>
                                            (Including GST)
                                        </strong>
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="padding-left: 20px;border: 1px solid #000; padding: 10px; text-align: left;"><strong>
                                            Shipping Charges
                                        </strong><br>
                                    </td>
                                    <?php if ($orders->order->shipping_charges > 0) { ?>
                                        <?php
                                        $newgst = 1.18;
                                        $price = $orders->order->shipping_charges / $newgst;
                                        //$price = $orders->order->shipping_charges - $tax_amount  ?>
                                        <td align="right" style="padding:10px 7px; line-height: 40px;background-color:#eee;">
                                            <center> &nbsp;&nbsp; <?= round($price, 2); ?></center><br>
                                        </td>
                                        <?php if (!empty($gstmatch)) {
                                            $newgst = 9; ?>
                                            <td style="padding: 10px 7px; line-height: 40px;background-color:#eee;">
                                                <center><?php echo round($newgst, 2); ?></center>
                                            </td>
                                            <td style="padding: 10px 7px; line-height: 40px;background-color:#eee;">
                                                <center><?php echo round($newgst, 2); ?></center>
                                            </td>
                                        <?php } else {
                                            $newgst = 18; ?>
                                            <td style="padding: 10px 7px; line-height: 40px;background-color:#eee;">
                                                <center><?php echo round($newgst, 2); ?></center>
                                            </td>
                                        <?php } ?>
                                        <td align="right" style="padding:10px 7px; line-height: 40px;background-color:#eee;">
                                            <center> &nbsp;&nbsp; <?= round($orders->order->shipping_charges, 2); ?></center><br>
                                        </td>
                                    <?php } else {  ?>
                                        <td align="right" style="padding:10px 7px; line-height: 40px;background-color:#eee;">
                                            <center><?php echo '-'; ?></center><br>
                                        </td>

                                        <?php if (!empty($gstmatch)) { ?>
                                            <td style="padding: 10px 7px; line-height: 40px;background-color:#eee;">
                                                <center><?php echo '-'; ?></center>
                                            </td>
                                            <td style="padding: 10px 7px; line-height: 40px;background-color:#eee;">
                                                <center><?php echo '-'; ?></center>
                                            </td>
                                        <?php } else { ?>
                                            <td style="padding: 10px 7px; line-height: 40px;background-color:#eee;">
                                                <center><?php echo '-'; ?></center>
                                            </td>
                                        <?php } ?>
                                        <td style="padding: 10px 7px; line-height: 40px;background-color:#eee;">
                                            <center><?php echo '-'; ?></center></br>
                                        </td>
                                    <?php   } ?>
                                </tr>
                                <tr>
                                    <td style="padding-left: 20px; border: 1px solid #000; padding: 10px; text-align: left;">
                                        <strong>
                                            COD Charges
                                        </strong><br>
                                    </td>
                                    <?php if ($orders->order->cod_charges > 0) { ?>
                                        <?php
                                        $newgst = 1.18;
                                       
                                        $price = $orders->order->cod_charges / $newgst;
                                        // $price = $orders->order->cod_charges - $tax_amount  ?>
                                        <td align="right" style="padding:10px 7px; line-height: 40px;background-color:#eee;">
                                            <center> &nbsp;&nbsp; <?= round($price, 2); ?></center><br>
                                        </td>
                                        <?php if (!empty($gstmatch)) {
                                            $newgst = 9; ?>
                                            <td style="padding: 10px 7px; line-height: 40px;background-color:#eee;">
                                                <center><?php echo round($newgst, 2); ?></center>
                                            </td>
                                            <td style="padding: 10px 7px; line-height: 40px;background-color:#eee;">
                                                <center><?php echo round($newgst, 2); ?></center>
                                            </td>
                                        <?php } else {
                                            $newgst = 18; ?>
                                            <td style="padding: 10px 7px; line-height: 40px;background-color:#eee;">
                                                <center><?php echo round($newgst, 2); ?></center>
                                            </td>
                                        <?php } ?>
                                        <td align="right" style="padding:10px 7px; line-height: 40px;background-color:#eee;">
                                            <center>&nbsp;&nbsp; <?= round($orders->order->cod_charges, 2); ?></center><br>
                                        </td>

                                    <?php } else {
                                    ?>
                                        <td align="right" style="padding:10px 7px; line-height: 40px;background-color:#eee;">
                                            <center><?php echo '-'; ?></center><br>
                                        </td>

                                        <?php if (!empty($gstmatch)) { ?>
                                            <td style="padding: 10px 7px; line-height: 40px;background-color:#eee;">
                                                <center><?php echo '-'; ?></center>
                                            </td>
                                            <td style="padding: 10px 7px; line-height: 40px;background-color:#eee;">
                                                <center><?php echo '-'; ?></center>
                                            </td>
                                        <?php } else { ?>
                                            <td style="padding: 10px 7px; line-height: 40px;background-color:#eee;">
                                                <center><?php echo '-'; ?></center>
                                            </td>
                                        <?php } ?>
                                        <td style="padding: 10px 7px; line-height: 40px;background-color:#eee;">
                                            <center><?php echo '-'; ?></center></br>
                                        </td>
                                    <?php   } ?>
                                </tr>
                                <?php if ($orders->order->discount > 0) { ?>
                                    <?php if (!empty($gstmatch)) { ?>
                                        <tr>
                                            <td colspan="4" style="padding: 10px 7px; line-height: 80px;border:1px solid #000">
                                                <strong>
                                                    <center style="padding:10px;">Discount</center>
                                                </strong><br>
                                            </td>
                                            <td align="right" style="padding: 10px 7px; line-height: 80px;border:1px solid #000">
                                                <center>Rs. <?= round($orders->order->discount, 2); ?></center><br>
                                            </td>
                                        </tr>
                                    <?php } else { ?>
                                        <tr>
                                            <td colspan="3" style="padding: 10px 7px; line-height: 50px;border:1px solid #000">
                                                <strong>
                                                    <center style="padding:10px;">Discount</center>
                                                </strong><br>
                                            </td>
                                            <td align="right" style="padding: 10px 7px; line-height: 50px;border:1px solid #000">
                                                <center>Rs. <?= round($orders->order->discount, 2); ?></center><br>
                                            </td>
                                        </tr>
                                    <?php  } ?>
                                <?php  } ?>
                                <?php if (!empty($gstmatch)) { ?>
                                    <tr>
                                        <td colspan="4" style="padding: 10px 7px; line-height: 50px;border:1px solid #000">
                                            <strong>
                                                <center style="padding:10px;">Total Amount</center>
                                            </strong><br>
                                        </td>
                                        <td align="right" style="padding: 10px 7px; line-height: 50px;border:1px solid #000">
                                            <center>Rs. <?= round($orders->order->order_amount, 2); ?></center><br>
                                        </td>
                                    </tr>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="3" style="padding: 10px 7px; line-height: 50px;border:1px solid #000">
                                            <strong>
                                                <center style="padding:10px;">Total Amount</center>
                                            </strong><br>
                                        </td>
                                        <td align="right" style="padding: 10px 7px; line-height: 50px;border:1px solid #000">
                                            <center>Rs. <?= round($orders->order->order_amount, 2); ?></center><br>
                                        </td>
                                    </tr>
                                <?php  } ?>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table> -->

            <!-- <table width="100%" style=" font-size: 12px;margin-top: <?php echo $padding_top; ?>;">
            <tr>
                <td>
                    <table width="60%" align="left" style=" font-size: 12px;">
                        <?php if ($orders->order->shipping_charges > 0) { ?>
                        <tr>
                            <td style="padding: 0px; line-height: 20px;">&nbsp;<br></td>
                        </tr>
                        <?php } ?>
                        <?php if ($orders->order->tax_amount > 0) { ?>
                        <tr>
                            <td style="padding: 0px; line-height: 20px;">&nbsp;<br></td>
                        </tr>
                        <?php } ?>
                        <?php if ($orders->order->cod_charges > 0) { ?>
                        <tr>
                            <td style="padding: 0px; line-height: 20px;">&nbsp;<br></td>
                        </tr>
                        <?php } ?>
                        <?php if ($orders->order->discount > 0) { ?>
                        <tr>
                            <td style="padding: 0px; line-height: 20px;">&nbsp;<br></td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td style="padding: 0px; line-height: 20px;">&nbsp;<br></td>
                        </tr>
                    </table>
                    <table width="40%" align="right" style="font-size: 12px;">
                        <?php if ($orders->order->shipping_charges > 0) { ?>
                        <tr>
                            <td><strong style="margin-left:171px;">Shipping Charges</strong><br></td>
                            <td align="right">
                                <center style="margin-left:-61px;"> &nbsp;&nbsp; <?= round($orders->order->shipping_charges, 2); ?></center><br>
                            </td>
                        </tr>
                        <?php } ?>
                        <?php if ($orders->order->tax_amount > 0) { ?>
                        <tr>
                            <td><strong style="margin-left:171px;">Tax Amount</strong><br></td>
                            <td align="right">
                                <center style="margin-left:-61px;">&nbsp;&nbsp; <?= round($orders->order->tax_amount, 2); ?></center><br>
                            </td>
                        </tr>
                        <?php } ?>
                        <?php if ($orders->order->cod_charges > 0) { ?>
                        <tr>
                            <td><strong style="margin-left:171px;">COD Charges</strong><br></td>
                            <td align="right">
                                <center style="margin-left:-61px;">&nbsp;&nbsp; <?= round($orders->order->cod_charges, 2); ?></center><br>
                            </td>
                        </tr>
                        <?php } ?>
                        <?php if ($orders->order->discount > 0) { ?>
                        <tr>
                            <td><strong style="margin-left:171px;">Discount</strong><br></td>
                            <td align="right">
                                <center style="margin-left:-61px;"> &nbsp;&nbsp; <?= round($orders->order->discount, 2); ?></center><br>
                            </td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td><strong style="margin-left:171px;">Total Amount</strong><br></td>
                            <td align="center">
                                <center style="margin-left:-61px;">Rs &nbsp;&nbsp; <?= round($orders->order->order_amount, 2); ?></center><br>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table> -->
            <?php if (!empty($orders->invoice_settings->invoice_signature)) { ?>
                <br><br><br><br><br>
                <table width="100%" cellpadding="10">
                <tr>
                    <td width="70%">
                    </td>
                    <!-- <td width="30%" style="margin-top:10px;padding:0px 20px; text-align:right">
                            <img src="<?php echo $orders->invoice_settings->invoice_signature; ?>" style="max-width:300px; max-height:80px;">
                    </td> -->
                </tr>
            </table>
            <h4 style="font-size:14px;margin-right:40px;text-align:right">Authorized Signature</h4>
            <?php } ?>
            <br><br><br><br><br>
            <h4 style="font-size:14px;margin-right:120px;text-align:right"><img src="<?php echo base_url('assets/images/dakit-favicon.gif');?>" width="50" height="50"></h4>
            <h4 style="font-size:14px;margin-right:40px;text-align:right">Powered by : <b>Daakit</b> Technologies Pvt. ltd
            </h4>
        </div>
    <?php  $i++;
    } ?>
</body>

</html>