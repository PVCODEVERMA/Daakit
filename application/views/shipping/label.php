<?php
$i = '1';
$count = count($shipments);
function remove_spc_charcater($str)
{
    if (empty($str))
        return false;
    return str_ireplace(array('\'', '"', '<', '>', ';', '$', '*', '#', '%', '!', '×', '’', '–', '·', '—', '‘', '“', '”'), ' ', $str);
}
foreach ($shipments as $order) {
    if ($format == 'thermal') {
        $page_break = 'page-break-after: always;';
        if ($i == $count)
            $page_break = '';
        $html = '<div style="width: 100%; height: 100%; padding: 15px; height: 6.20in; border: 2px solid; ' . $page_break . '">';
    } else {
        if ($i % 2 == 0)
            $float = 'float:right;';
        else
            $float = 'float:left;';
        $products = json_decode(json_encode($order->products), 1);
        $product_full_name = !empty($products) ? implode(', ', array_column($products, 'product_name')) : '';
        $height = 'height: 48%;';
        if (strlen($product_full_name) > 180) {
            $height = 'height: 98%;';
        }
        $html = '<div style="width: 48%; ' . $height . ' border: 1px solid; margin: 2px; margin-bottom: 20px; ' . $float . '">';
    }
    echo $html;
    $order_info = $order->order;
    $shipment = $order->shipment;
    $products = [];
    foreach ($order->products as $prod) {
        $prod_name = preg_replace('/\xc2\xa0/', ' ', remove_spc_charcater($prod->product_name));
        $prod_sku = preg_replace('/\xc2\xa0/', ' ', remove_spc_charcater($prod->product_sku));
        $prod->product_name = $prod_name;
        $prod->product_sku = $prod_sku;
        $products[] = $prod;
    }
    //pr($products,1);
    $courier = $order->courier;
    $warehouse = $order->warehouse;
    $rto_warehouse = $order->rto_warehouse;
    $company = $order->company;
    $user = $order->user;
    $channels_brand_logo = $order->channels_brand_logo;
    if ($shipment->label != '') {
        if ($format == 'thermal')
            echo "<img src='{$shipment->label}'>";
        else if (in_array($shipment->courier_id, array('37', '52', '53', '62', '66', '159')))
            echo "<img src='{$shipment->label}' style='width: 95%;'>";
        else
            echo "<img src='{$shipment->label}' style='height:100%; width: 100%;'>";
    } else {

        switch ($courier->id) {
            default:
                include VIEWPATH . 'shipping/label/common.php';
                break;
        }
    }
?>
    </div>
<?php
    $i++;
}
?>