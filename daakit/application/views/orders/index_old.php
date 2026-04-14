<style>
    .container
    {
        width: 100% !important;
        max-width: none !important;
    }
</style>
<section class="admin-content">
    <div class="bg-dark m-b-30" style="margin-bottom: 0px !important;">
        <div class="container">
            <div class="row p-b-60 p-t-60" style="padding-bottom: 60px !important;padding-top: 0px !important;">
                <div class="col-md-6 m-auto text-white p-b-30">
                    <h1> Orders</h1>
                </div>
                <div class="col-md-6 m-auto text-white p-b-30">
                    <div class="text-md-right">
                        <a href="<?= base_url('orders/exportCSV'); ?><?php
                        if (!empty($filter)) {
                            echo "?" . http_build_query($_GET);
                        }
                        ?>" class="btn btn-secondary btn-sm"><i class="mdi mdi-arrow-down-bold-circle"></i> Export</a>
                        <button type="button" class="btn btn-secondary bulk-ship-button btn-sm "  data-toggle="modal"
                                data-target=".bd-example-modal-sm"> <i class="mdi mdi-package-variant-closed"></i> &nbsp; Ship</button>
                        <button class="btn btn-secondary btn-sm" data-toggle="modal"
                                data-target=".import_orders_modal"> <i class="mdi mdi-arrow-up-bold-circle"></i> &nbsp; Import</button>
                        <a href="orders/create" class="btn btn-secondary btn-sm"> <i class="mdi mdi-plus"></i>Create Order</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container pull-up">
        <div class="container pull-up">
            <div class="row">
                <div class="col-md-12">
                    <div class="card m-b-30">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-sm-10">
                                    <h6 class="m-b-0">
                                        <i class="mdi mdi-checkbox-intermediate"></i> Orders
                                    </h6>
                                </div>
                                <div class="col-sm-2 text-right">
                                    <button type="button" class="btn btn-default show_hide_filter btn-sm" <?php if (!empty($filter)) { ?> style="display:none;" <?php } ?>><i class="icon-placeholder mdi mdi-filter"></i> Filters</button>
                                    <button type="button" class="btn btn-default show_hide_filter btn-sm" <?php if (empty($filter)) { ?> style="display:none;" <?php } ?>><i class="icon-placeholder mdi mdi-close"></i></button>
                                </div>
                            </div>

                        </div>
                        <div class="card-body">
                            <form method="get" action="<?= base_url('orders/all') ?>">
                                <div class="row m-b-10" id="filter_row" <?php if (empty($filter)) { ?> style="display:none;" <?php } ?>>
                                    <div class="col-sm-12">
                                        <div class="row m-b-10">
                                            <div class="col-sm-2" style="margin-top:2px;">
                                                <label for="email">Order ID(s):</label>
                                                <input type="text" autocomplete="off" name="filter[order_ids]" value="<?= !empty($filter['order_ids']) ? $filter['order_ids'] : '' ?>" class="form-control" placeholder="Seperted by comma">
                                            </div>
                                            <div class="col-sm-2" style="margin-top:2px;">
                                                <label for="email">Product Name:</label>
                                                <input type="text" autocomplete="off"  name="filter[product_name]" value="<?= !empty($filter['product_name']) ? $filter['product_name'] : '' ?>" class="form-control" placeholder="Product name to search">
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <label for="email">From Date:</label>
                                                        <input type="text" autocomplete="off" data-start-date="<?= !empty($filter['start_date']) ? $filter['start_date'] : '' ?>" data-end-date="<?= !empty($filter['end_date']) ? $filter['end_date'] : '' ?>"  class="form-control date-range-picker col-sm-12">
                                                        <input type="hidden" autocomplete="off" id="date-min"  name="filter[start_date]" value="<?= !empty($filter['start_date']) ? $filter['start_date'] : '' ?>" placeholder="from">
                                                        <input type="hidden" autocomplete="off" id="date-max"  name="filter[end_date]" value="<?= !empty($filter['end_date']) ? $filter['end_date'] : '' ?>" placeholder="to">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <label for="email">Method:</label>
                                                <select name="filter[pay_method]" class="form-control">
                                                    <?php
                                                    $pay_method = '';
                                                    if (!empty($filter['pay_method']))
                                                        $pay_method = $filter['pay_method'];
                                                    ?>
                                                    <option <?php if ($pay_method == '') { ?> selected <?php } ?>  value="">All</option>
                                                    <option <?php if ($pay_method == 'cod') { ?> selected <?php } ?> value="cod">COD</option>
                                                    <option <?php if ($pay_method == 'prepaid') { ?> selected <?php } ?> value="prepaid">Prepaid</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-2" style="margin-top:34px;">
                                                <button type="submit" class="btn btn-sm btn-success">Apply</button>

                                                <a href="<?= base_url('orders/all'); ?>" class="btn btn-sm btn-primary">Clear</a>
                                            </div>


                                        </div>
                                    </div>



                                </div>
                            </form>
                            <div class="row border-top p-t-10">
                                <div class="col-sm-2">
                                    <?php
                                    $applied_filters = !empty($_GET) ? $_GET : array('filter' => array());
                                    $status_filters = $applied_filters;
                                    $status_filters['filter']['fulfillment'] = '';
                                    $btn_class = 'btn-default';
                                    ?>
                                    <a href="<?= base_url('orders/all') . '?' . http_build_query($status_filters); ?>" class="btn btn-block m-b-15 ml-2 mr-2 btn-sm  <?= (!isset($_GET['filter']['fulfillment']) || $_GET['filter']['fulfillment'] == '') ? 'btn-info' : 'btn-default'; ?>">All</a>
                                </div>
                                <div class="col-sm-2">
                                    <?php
                                    $status_filters['filter']['fulfillment'] = 'new';
                                    ?>
                                    <a href="<?= base_url('orders/all') . '?' . http_build_query($status_filters); ?>" class="btn btn-block m-b-15 ml-2 mr-2 btn-sm <?= (isset($_GET['filter']['fulfillment']) && $_GET['filter']['fulfillment'] == 'new') ? 'btn-info' : 'btn-default'; ?>">Not Shipped (<?= array_key_exists('new', $count_by_status) ? $count_by_status['new'] : '0' ?>)</a>
                                </div>
                                <div class="col-sm-2">
                                    <?php
                                    $status_filters['filter']['fulfillment'] = 'booked';
                                    ?>
                                    <a href="<?= base_url('orders/all') . '?' . http_build_query($status_filters); ?>" class="btn btn-block m-b-15 ml-2 mr-2 btn-sm <?= (isset($_GET['filter']['fulfillment']) && $_GET['filter']['fulfillment'] == 'booked') ? 'btn-info' : 'btn-default'; ?>">Booked (<?= array_key_exists('booked', $count_by_status) ? $count_by_status['booked'] : '0' ?>)</a>
                                </div>
                                <div class="col-sm-2">
                                    <?php
                                    $status_filters['filter']['fulfillment'] = 'cancelled';
                                    ?>
                                    <a href="<?= base_url('orders/all') . '?' . http_build_query($status_filters); ?>" class="btn btn-block m-b-15 ml-2 mr-2 btn-sm <?= (isset($_GET['filter']['fulfillment']) && $_GET['filter']['fulfillment'] == 'cancelled') ? 'btn-info' : 'btn-default'; ?>">Cancelled (<?= array_key_exists('cancelled', $count_by_status) ? $count_by_status['cancelled'] : '0' ?>)</a>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th><input data-switch="true" id="select_all_orders" type="checkbox"></th>
                                            <th>Channel</th>
                                            <th>Order</th>
                                            <th>Date</th>
                                            <th>Product</th>
                                            <th>Payment</th>
                                            <th>Method</th>
                                            <th style="width: 10%;">Customer</th>
                                            <th>Zip Code</th>
                                            <th>Weight</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (!empty($orders)) {
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

                                            foreach ($orders as $order) {
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
                                                <tr>
                                                    <td><?php if ($order->fulfillment_status == 'new' && $valid_shipment) { ?><input value="<?= $order->id; ?>" type="checkbox" class="order_id_checkbox" name="order_ids"><?php } ?></td>
                                                    <td>
                                                        <?= ucwords($order->channel_name); ?>
                                                    </td>
                                                    <td><a style="color: #4c66fb;" target="_blank" href="orders/view/<?= $order->id; ?>"><?= $order->order_id; ?></a></td>
                                                    <td><?= date('M d, Y', $order->order_date); ?></td>
                                                    <td><?php $products = $order->products; ?>
                                                        <span data-toggle="tooltip" data-html="true" title="<?= $products; ?>">
                                                            <?= mb_strimwidth($products, 0, 14, "..."); ?>
                                                        </span>                                                               
                                                    </td>
                                                    <td><?= $order->order_amount; ?></td>
                                                    <td><?= strtoupper($order->order_payment_type); ?></td>
                                                    <td>
                                                        <?php
                                                        $customername = ucwords($order->shipping_fname . ' ' . $order->shipping_lname);
                                                        $customerphn = isset($order->shipping_phone) ? $order->shipping_phone : '';
                                                        $customeradd1 = isset($order->shipping_address) ? $order->shipping_address : '';
                                                        $customeradd2 = isset($order->shipping_address_2) ? $order->shipping_address_2 : '';
                                                        $compltadd = $customeradd1 . ' ' . $customeradd2;
                                                        $shippcity = $order->shipping_city;
                                                        $shipstate = $order->shipping_state;
                                                        ?>
                                                        <span data-toggle="tooltip" data-html="true" 
                                                              title="<?= $customername . '<br>' . $customerphn . '<br>' . $compltadd . '<br>' . $shippcity . '<br>' . $shipstate; ?>">
                                                                  <?= mb_strimwidth($customername, 0, 14, "..."); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?= ucwords($order->shipping_zip); ?>
                                                    </td>
                                                    <td>
                                                        <?= is_numeric($order->package_weight) ? round($order->package_weight / 1000, 2) : '0'; ?> Kg
                                                    </td>
                                                    <td>
                                                        <?php if ($order->fulfillment_status == 'new') { ?>
                                                            <?php if (!$valid_shipment) { ?>

                                                                <a href="orders/create/<?= $order->id; ?>" target="blank" data-toggle="tooltip" data-html="true"  title="<?= $valid_shipment_error; ?>" class="btn btn-outline-warning btn-sm"><i class="mdi mdi-pencil"></i> Edit</a> 
                                                            <?php } else { ?>
                                                                <button type="button" class="btn btn-outline-primary shipnowbtn btn-sm" data-order-id ='<?= $order->id; ?>' data-toggle="modal"
                                                                        data-target=".bd-example-modal-sm">Ship</button>
                                                                        <?php
                                                                    }
                                                                } elseif ($order->fulfillment_status == 'booked') {
                                                                    ?>
                                                            <button type="button" class="btn btn-outline-success btn-sm">Booked</button>
                                                        <?php } elseif ($order->fulfillment_status == 'cancelled') { ?>
                                                            <button type="button" class="btn btn-outline-danger btn-sm">Cancelled</button>
                                                        <?php } else {
                                                            ?>
                                                            <button type="button" class="btn btn-outline-warning btn-sm"><?= ucwords($order->fulfillment_status) ?></button>
                                                            <?php
                                                        }
                                                        ?>
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

                            <div class="row">
                                <div class="col-md-1">
                                    <?php
                                    $per_page_options = array(
                                        '10' => '10',
                                        '20' => '20',
                                        '50' => '50',
                                        '100' => '100',
                                        '200' => '200',
                                        '500' => '500',
                                    );

                                    $js = "class='form-control' onchange='per_page_records(this.value)'";
                                    echo form_dropdown('per_page', $per_page_options, $limit, $js);
                                    ?>
                                </div>

                                <div class="col-sm-12 col-md-4">
                                    <div class="dataTables_info" id="example-multi_info" role="status" aria-live="polite">Showing <?= $offset + 1; ?> to <?= $offset + $limit; ?> of <?= $total_records; ?> entries</div>                                    
                                </div>

                                <div class="col-sm-12 col-md-6">
                                    <ul class="pagination" style="float: right;margin-right: -50px;">
                                        <?php if (isset($pagination)) { ?>
                                            <?php echo $pagination ?>
                                        <?php } ?>
                                    </ul>

                                </div>
                            </div>





                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<div class="modal fade bd-example-modal-sm"  tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content" id="fulfillment_info">

        </div>
    </div>
</div>


<div class="modal fade import_orders_modal"  tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content" id="fulfillment_info">
            <form method="post" action="<?= base_url('orders/import'); ?>" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="mySmallModalLabel">Bulk Upload Orders</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 border-bottom p-b-10">
                            Download sample order upload file : <a class="text-info" href="<?= base_url('assets/orders_sample.csv'); ?>">Download</a>
                        </div>
                        <div class="col-sm-12 m-t-10">
                            <div class="m-b-10">
                                <div class="input-group mb-3">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="importFile" >
                                        <label class="custom-file-label" for="inputGroupFile02">Choose file</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Upload</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
            </form>
        </div>
    </div>
</div>
</div>

<script>
<?php unset($_GET['perPage']); ?>
    function per_page_records(per_page = false) {
        var page_url = '<?= base_url('orders/all') . '?' . http_build_query($_GET) . '&perPage=' ?>' + per_page;
        window.location.href = page_url;
    }
</script>