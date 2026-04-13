<html>

<head>
    <style>
        table {
            border: 1px solid;
            border-collapse: collapse;
            margin: auto;
        }

        thead {
            background: #f3d4ac;
            color: black;
            font-weight: bold;
        }

        td {
            border: 1px solid;
            vertical-align: top;
        }
    </style>
</head>

<body style="text-align:center; padding-top: 20px;">
    <small>Courier Company : <b><?= $courier->name; ?></b> &nbsp; Date: <b><?= date('M d, Y h:i a', $manifest->created); ?></b>, Number of Shipments: <b><?= count($shipments) ?></b>, Pickup Ref No: <b><?= $manifest->pickup_number; ?></b>, Warehouse Name: <b><?= ucwords($manifest->warehouse_name) ?></b><br /></small><br />
    <table style="">
        <thead>
            <tr>
                <td>Sr#</td>
                <td>Order #</td>
                <td>Payment Mode</td>
                <td>Customer Details</td>
                <td>Contents</td>
                <td>Wt(Kg)</td>
                <td>AWB #</td>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($shipments)) {
                $i = 1;
                foreach ($shipments as $shipment) {
            ?>
                    <tr>
                        <td style="text-align:center"><?= $i; ?></td>
                        <td style="text-align:center"><?= $shipment->order->order_no; ?></td>
                        <td style="text-align:center"><?= $shipment->order->order_payment_type; ?><br /> (<?= 'Rs.' . $shipment->order->order_amount; ?>)</td>
                        <td style="width: 300px !important;"><?= ucwords($shipment->order->shipping_fname . ' ' . $shipment->order->shipping_lname) ?><br />
                        </td>
                        <td style="max-width: 150px;">
                            <ul>
                                <?php
                                foreach ($shipment->products as $prd) {
                                    echo '<li>';
                                    echo $prd->product_name . ' (Qty: ' . $prd->product_qty . ')';
                                    echo '</li>';
                                } ?>
                            </ul>
                        </td>
                        <td style="text-align:center"><?= (isset($shipment->order->package_weight) && $shipment->order->package_weight > 0) ? round($shipment->order->package_weight / 1000, 2) : '0.5'; ?></td>
                        <td style="padding: 10px; text-align: center;">
                            <?= $shipment->shipment->awb_number; ?><br />
                            <?= ucwords($shipment->courier->display_name) ?>
                        </td>
                    </tr>
            <?php
                    $i++;
                }
            }
            ?>
        </tbody>
    </table>
</body>

</html>