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
            border: 1px solid #eee;
        }

        .vl {
            border-left: 1px solid #9e9292;
            height: 227px;
        }

        .abc {
            background-color: #fff;
            border-radius: 8px;
            height: 1200px;
        }
    </style>
</head>

<body>


    <?php
$i = '1';

$count = count($shipments);
foreach ($shipments as $orders) {
    if ($format == 'thermal') {
        $page_break = '';
        if ($i == $count)
            $page_break = '';

        $html = '<div style="font-size:11px; width: 100%; height: 100%; padding: 15px; height: 6.20in; border: 2px solid; ' . $page_break . '">';
    } else {
        if ($i % 2 == 0)
            $float = 'float:right;';
        else
            $float = 'float:left;';

        $products = json_decode(json_encode($orders->products), 1);
        $product_full_name = !empty($products) ? implode(', ', array_column($products, 'product_name')) : '';
        
        $height = 'height:auto;';
        if(strlen($product_full_name) > 180) {
            $height = 'height: auto;';
        }

        $html = '<div style="font-size:11px;width: 100%; background-color: #FFFFFF;"><div style="font-size:11px;width: 48%; ' . $height . ' border: 1px solid;   margin-bottom: 20px;">';

    }
    echo $html;
    $order_info = $orders->order;
    $shipment = $orders->shipment;
    $products = $orders->products;
    $courier = $orders->courier;
    $warehouse = $orders->warehouse;
    $rto_warehouse = $orders->rto_warehouse;
    $company = $orders->company_details;
    $user = $orders->user;

    $channels_brand_logo=$order->channels_brand_logo;

    if ($shipment->label != '') {
        if ($format == 'thermal')
            echo "<img src='{$shipment->label}'>";
        else if (in_array($shipment->courier_id, array('37', '52', '53','62','66')))
            echo "<img src='{$shipment->label}' style='width: 95%;'>";
        else
            echo "<img src='{$shipment->label}' style='height:100%; width: 100%;'>";
    } else {
        switch ($courier->id) {
            case '5':
            case '12':
            case '76': //bluedart ros
                include VIEWPATH . 'shipping/label/bluedart.php';
                break;
            case '24':
            case '77': //bluedart ros IN
                include VIEWPATH . 'shipping/label/bluedart_24.php';
                break;
            case '8':
            case '9':
            case '29':
            case '30':
            case '31':
            case '59':
            case '69':
            case '70':
            case '79':
            case '80':
            case '81':
            case '88':
                include VIEWPATH . 'shipping/label/dtdc.php';
                break;
            case '10':
                include VIEWPATH . 'shipping/label/ecom.php';
                break;
            case '4':
            case '21':
            case '32':
            case '33':
            case '34':
            case '82':
            case '83':
                include VIEWPATH . 'shipping/label/shadowfax.php';
                break;
            case '15':
            case '25':
            case '27':
            case '28':
            case '60': //Ekart 10 KG
            case '61': //Ekart 3 KG
                include VIEWPATH . 'shipping/label/ekart.php';
                break;
            case '3':
            case '14':
            case '41':
            case '42':
            case '45':
            case '46':
            case '47':
            case '72':
            case '73':
            case '74':
            case '75':
                include VIEWPATH . 'shipping/label/xpressbees.php';
                break;
            case '36':
            case '55':
            case '56':
                include VIEWPATH . 'shipping/label/gati.php';
                break;
            case '44':
            case '50':
            case '51':
            case '63':
            case '64':
                include VIEWPATH . 'shipping/label/udaan.php';
                break;
            case '67':
                include VIEWPATH . 'shipping/label/smartr.php';
                break;
            case '94': //bluedart cargo
                include VIEWPATH . 'shipping/label/bluedart_cargo.php';
                break;
            case '71': //xpressbees cargo
                include VIEWPATH . 'shipping/label/xpressbees_cargo.php';
                break;
            default:
                include VIEWPATH . 'shipping/label/common_lable_invoice.php';
                break;
        }
    }
?>

    </div></div>
<?php
    $i++;


?>

    <?php
    $i = '1';

    
        $billingtitle = 'Bill & Ship';
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
    <?php echo count($orders->products) > 5 ? '<div style="page-break-after: always;">&nbsp;</div>' : '';?>
        <div class="abc" style="border-top:2px dotted;">
            <table width="100%" cellpadding="0">
                <tr>
                    <td width="100%" style="padding: 2px; ">
                        <h3>
                            <center style="font-size:10px;">TAX INVOICE</center>
                        </h3>
                    </td>
                </tr>
            </table>
            <table width="100%" cellpadding="0">
                <tr>
                    <td width="30%" style="padding:0px 10px; ">
                        <?php if (!empty($orders->company_details->cmp_logo)) { ?>
                            <img src="<?php echo (strpos($orders->company_details->cmp_logo, "amazonaws.com") !== false) ? ($orders->company_details->cmp_logo) : (base_url() . 'assets/seller_company_logo/' . $orders->company_details->cmp_logo); ?>" style="max-width:300px; max-height:80px;">
                        <?php } ?>
                    </td>
                    <td width="70%" style="text-align:right">
                        <h4 style="font-size:10px;">Invoice</h4>
                        <P style="font-size:10px;">Invoice Number - <?= $orders->order->order_id; ?></P>
                        <P style="font-size:10px;">Invoice Date - <?= date('M d, Y',  $orders->shipment_details->invoice_date) ?></P>
                    </td>
                </tr>
            </table>
            <hr>
            <table width="100%" cellpadding="10">
                <tr>
                    <td width="33%">
                        <?php if (!empty($orders->order->billing_fname) && !empty($orders->order->billing_zip)) { ?>
                            <h4 style="font-size:10px;">Bill To:</h4> <br>
                            <?php if (!empty($orders->order->billing_company_name)) {
                                echo '<p style="margin-bottom: 0px;line-height:1.7;font-size:10px;">' . ucwords($orders->order->billing_company_name) . '</p>';
                            } ?>
                            <p style="margin-bottom: 0px;font-size:10px;"><?php echo ucwords($orders->order->billing_fname . ' ' . $orders->order->billing_lname); ?></p>
                            <p style="margin-bottom: 0px;font-size:10px;"><?= ucwords($orders->order->billing_address . ' ' . $orders->order->billing_address_2) ?></p>
                            <p style="margin-bottom: 0px;font-size:10px;"><?= ucwords($orders->order->billing_city . '  ' . $orders->order->billing_state) ?></p>
                            <p style="margin-bottom: 0px;font-size:10px;"><?= $orders->order->billing_zip ?></p>
                            <p style="margin-bottom: 0px;font-size:10px;">GST No : <?= strtoupper($orders->order->billing_gst_number) ?></p>
                            <?php if (!empty($orders->shipment_details->destination_state_code)) { ?> 
                                <p style="margin-bottom: 0px;font-size:10px;"> State Code :
                                    <?= $orders->shipment_details->destination_state_code; ?></p>
                            <?php } ?>
                        <?php $billingtitle = 'Ship';
                        } ?>
                    </td>
                        

                    
                    <td width="33%"><h3 style="font-size:10px;"> <?= $billingtitle ?> To:</h3> <br>
                        <?php if (!empty($orders->order->shipping_company_name)) {
                            echo '<p style="margin-bottom: 0px;font-size:10px;line-height:1.7;">' . ucwords($orders->order->shipping_company_name) . '</p>';
                        } ?>
                        <p style="margin-bottom: 0px;font-size:10px;"><?php echo ucwords($orders->order->shipping_fname . ' ' . $orders->order->shipping_lname); ?></p>
                        <p style="margin-bottom: 0px;font-size:10px;"><?= ucwords($orders->order->shipping_address . ', ' . $orders->order->shipping_address_2) ?></p>
                        <p style="margin-bottom: 0px;font-size:10px;"><?= ucwords($orders->order->shipping_city . ', ' . $orders->order->shipping_state) ?></p>
                        <p style="margin-bottom: 0px;font-size:10px;"><?= $orders->order->shipping_zip ?></p>
                        <?php if (!empty($orders->shipment_details->destination_state_code) && $billingtitle == 'Bill & Ship') { ?>
                            <p style="margin-bottom: 0px;font-size:10px;"> State Code :
                                <?= $orders->shipment_details->destination_state_code; ?></p>
                        <?php } ?>
                    </td>

                    <td width="33%" style="float:right;text-align:right">
                        <h4 style="margin-left: 79px;font-size:10px;">Sold By:</h4>
                        <?php if (!empty($orders->company_details->company_name)) { ?>
                            <?php if ((isset($orders->warehouse->hide_company_name)) && ($orders->warehouse->hide_company_name!= 1)) { ?>
                                <p style="margin-bottom: 0px;font-size:10px;margin-left: 89px;">
                                    <?php echo ucwords($orders->company_details->company_name); ?>
                                </p><?php } ?>
                        <?php } ?>
                        <br>
                        <p style="margin-bottom: 0px;font-size:10px;margin-left: 12px;"><?php echo $orders->warehouse->name; ?></p>
                        
                        <p style="margin-bottom: 0px;font-size:10px;margin-left: 35px;">
                            <?= ucwords($orders->warehouse->address_1 . ', ' . $orders->warehouse->address_2 . ', ' . $orders->warehouse->city . ',' . $orders->warehouse->zip) ?></p>
                        <?php if (!empty($orders->warehouse->gst_number)) { ?> 
                            <p style="margin-bottom: 0px;font-size:10px;margin-left: 35px;"> GST :
                                <?= $orders->warehouse->gst_number ?></p>
                        <?php } ?>

                        <?php if (!empty($orders->shipment_details->origin_state_code)) { ?> 
                            <p style="margin-bottom: 0px;font-size:10px;margin-left: 35px;"> State Code :
                                <?= $orders->shipment_details->origin_state_code ?></p>
                        <?php } ?>
                    </td>
                </tr>
            </table>
            <table width="100%" cellpadding="10" style="margin-top:-10px;">
                <tr>
                    <td width="49%">
                        <span style="margin-top: -35px;font-size:11px;"><b>Payment Method:</b><span style="font-size:11px;"><?php echo ucwords($orders->order->order_payment_type); ?></span></span>
                        <br>
                        <span style="margin-top: -35px; font-size:11px;"><b>AWB No:</b><span style="font-size:11px;"><?php echo ucwords($orders->shipment_details->awb_number); ?></span></span>

                    </td>
                    <td width="2%">&nbsp;</td>

                    <td width="49%" style="float:right;text-align:right">
                        <span style="margin-left: 2px;margin-top: -35px; font-size:11px;"><b>Order Date:</b><span style="font-size:11px;"> <?= date('M d, Y',  $orders->order->order_date) ?></span></span>
                        <br>
                        <span style="margin-left: 2px;margin-top: -35px; font-size:11px;"><b>Shipped By:</b><span style="font-size:11px;"> <?php echo ucwords($orders->courier->display_name); ?></span></span>
                    </td>
                </tr>
            </table>
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
            <table class="items" width="100%" style="font-size: 12px;margin-top:-35px; border-collapse: collapse;">
                <thead>
                    <tr>
                        <td width="25%" style="text-align: left;background-color:#cec6c6;">
                            <strong>
                                <center style="padding:10px;">Product Name</center>
                            </strong>
                        </td>
                        <td width="9%" style="text-align: left;background-color:#cec6c6;">
                            <strong>
                                <center style="padding:10px;">HSN</center>
                            </strong>
                        </td>
                        <td width="12%" style="text-align: left;background-color:#cec6c6;">
                            <strong>
                                <center style="padding:10px;">Quantity</center>
                            </strong>
                        </td>
                        <td width="12%" style="text-align: left;background-color:#cec6c6;">
                            <strong>
                                <center style="padding:10px;">Unit Price</center>
                            </strong>
                        </td>
                        <td width="12%" style="text-align: left;background-color:#cec6c6;">
                            <strong>
                                <center style="padding:10px;">TAX Amount</center>
                            </strong>
                        </td>
                        <?php if (!empty($gstmatch)) { ?>
                            <td width="12%" style="text-align: left;background-color:#cec6c6;">
                                <strong>
                                    <center style="padding:10px;">CGST (Value | %)</center>
                                </strong>
                            </td>
                            <td width="12%" style="text-align: left;background-color:#cec6c6;">
                                <strong>
                                    <center style="padding:10px;">SGST (Value | %)</center>
                                </strong>
                            </td>
                        <?php } else { ?>
                            <td width="12%" style="text-align: left;background-color:#cec6c6;">
                                <strong>
                                    <center style="padding:10px;">IGST (Value | %)</center>
                                </strong>
                            </td>
                        <?php } ?>
                        <td width="12%" style="text-align: left;background-color:#cec6c6;">
                            <strong>
                                <center style="padding:10px;">TOTAL</center>
                                (Including GST)
                            </strong>
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $k = 0;
                    $taxproducts = $igst = $igstfinal = $total_product = $totalproductPrice = $productsprice = 0;
                    foreach ($orders->products as $details) {
                        $totalproductPrice = $details->product_qty * $details->product_price;
                        $p_igst = (!empty($details->igst) && !is_nan($details->igst)) ? $details->igst : 0;
                        $igstfinal = $p_igst + 100;
                        $productsprice = ($totalproductPrice * 100) / $igstfinal;
                        $igst = $totalproductPrice - $productsprice;
                        $productsprice = $productsprice / $details->product_qty;
                        $details->product_name = trim($details->product_name);
                    ?>
                        <tr>
                            <td style="padding: 5px 2px;">
                                <center><?php echo strlen($details->product_name) > 33 ? substr($details->product_name, 0, 30) . '...': $details->product_name; ?></center>
                            </td>
                            <td style="padding: 5px 2px;background-color:#eee;">
                                <center><?php echo $details->hsn_code; ?> </center>
                            </td>
                            <td style="padding: 5px 2px;background-color:#eee;">
                                <center><?php echo $details->product_qty; ?></center>
                            </td>
                            <td style="padding: 5px 2px;background-color:#eee;">
                                <center><?php echo round($productsprice, 2); ?></center>
                            </td>
                            <td style="padding: 5px 2px; background-color:#eee;">
                                <center><?php if (!empty($igst)) echo round($igst, 2); ?></center>
                            </td>
                            <?php if (!empty($gstmatch)) {
                                $newgst = $details->igst / 2;
                            ?>
                                <td style="padding: 5px 2px; background-color:#eee;">
                                    <center><?php echo round($newgst, 2); ?></center>
                                </td>
                                <td style="padding: 5px 2px;background-color:#eee;">
                                    <center><?php echo round($newgst, 2); ?></center>
                                </td>
                            <?php } else { ?>
                                <td style="padding: 5px 2px;background-color:#eee;">
                                    <center><?php echo round($details->igst, 2); ?></center>
                                </td>
                            <?php } ?>
                            <td style="padding:5px 2px;background-color:#eee;">
                                <center><?= round($totalproductPrice, 2); ?></center>
                            </td>
                        </tr>
                    <?php $k++;
                    } ?>
                </tbody>
            </table>
            <p>&nbsp;
            </p>
            <!-- <p>&nbsp;
            <p> -->
            <table width="100%" style=" font-size: 12px;margin-top: <?php echo $padding_top; ?>;">
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
                          <!--   <tr>
                                <td style="padding: 0px; line-height: 20px;">&nbsp;<br></td>
                            </tr> -->
                        </table>
                        <table width="70%" align="right" style="font-size: 32px;">

                            <thead>
                                <tr>
                                    <td width="20%" style="padding-left: 20px; text-align: left;background-color:#cec6c6;">
                                        <strong>
                                            Charges Applied
                                        </strong>
                                    </td>
                                    <td width="12%" style="text-align: left;background-color:#cec6c6;">
                                        <strong>
                                            <center style="padding:10px;"> Tax Amount</center>
                                        </strong>
                                    </td>
                                    <?php if (!empty($gstmatch)) { ?>

                                        <td width="12%" style="text-align: left;background-color:#cec6c6;">
                                            <strong>
                                                <center style="padding:10px;">CGST (Value | %)</center>
                                            </strong>
                                        </td>
                                        <td width="12%" style="text-align: left;background-color:#cec6c6;">
                                            <strong>
                                                <center style="padding:10px;">SGST (Value | %)</center>
                                            </strong>
                                        </td>
                                    <?php } else { ?>
                                        <td width="12%" style="text-align: left;background-color:#cec6c6;">
                                            <strong>
                                                <center style="padding:10px;">IGST (Value | %)</center>
                                            </strong>
                                        </td>
                                    <?php } ?>
                                    <td width="12%" style="text-align: left;background-color:#cec6c6;">
                                        <strong>
                                            <center style="padding:10px;">TOTAL</center>
                                            (Including GST)
                                        </strong>
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="padding-left: 20px;text-align: left;background-color:#cec6c6;"><strong>
                                            Shipping Charges
                                        </strong><br>
                                    </td>
                                    <?php if ($orders->order->shipping_charges > 0) { ?>
                                        <?php
                                            $newgst = 18;
                                            $tax_amount = $orders->order->shipping_charges * $newgst / 100;
                                            $price = $orders->order->shipping_charges - $tax_amount  ?>
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
                                    <td style="padding-left: 20px; text-align: left;background-color:#cec6c6;">
                                        <strong>
                                            COD Charges
                                        </strong><br>
                                    </td>
                                    <?php if ($orders->order->cod_charges > 0) { ?>
                                        <?php 
                                            $newgst = 18;
                                            $tax_amount = $orders->order->cod_charges * $newgst / 100;
                                            $price = $orders->order->cod_charges - $tax_amount  ?>
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
            </table>

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

            <?php if (!empty($orders->company_details->cmp_signatureimg)) { ?>
                <table class="items" width="30%" style="margin-left:30px;background-color:#eee;">
                    <tr>
                        <td> <img src="<?php echo (strpos($orders->company_details->cmp_signatureimg, "amazonaws.com") !== false) ? ($orders->company_details->cmp_signatureimg) : (base_url() . 'assets/seller_company_signatureimg/' . $orders->company_details->cmp_signatureimg); ?>" width="200" height="60" alt="" align="center" border="0"></td>
                    </tr>
                </table>
                <h4 style="font-size:14px;margin-left:30px;">Signature</h4>
            <?php } ?>
        </div>
    <?php $i++;
    } ?>

</body>

</html>