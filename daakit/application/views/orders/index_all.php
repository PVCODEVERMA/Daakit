<div class="row">
    <div class="col-md-12">
        <div class="card m-b-30">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-4">
                        <h4 class="m-b-0">
                            <i class="mdi mdi-checkbox-intermediate"></i> Orders
                        </h4>
                    </div>
                    <div class="col-md-8 text-right">
                        <a href="products/all" class="btn btn-outline-dark btn-sm"><i class="mdi mdi-link-variant"></i> Products SKU Mapping</a>
                        <a href="<?= base_url('orders/exportCSV'); ?><?php
                                                                        if (!empty($filter)) {
                                                                            echo "?" . http_build_query($_GET);
                                                                        }
                                                                        ?>" class="btn btn-outline-dark btn-sm"><i class="mdi mdi-arrow-down-bold-circle"></i> Export</a>

                        <button type="button" class="btn btn-sm btn-outline-dark refresh_orders_button"><i class="mdi mdi-refresh"></i> Refresh</button>



                        <button class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target=".import_orders_modal"> <i class="mdi mdi-arrow-up-bold-circle"></i> Import</button>

                        <a href="orders/create" class="btn btn-outline-dark btn-sm"> <i class="mdi mdi-plus"></i>Create Order</a>

                        <button type="button" class="btn btn-outline-dark show_hide_filter btn-sm" <?php if (!empty($_GET['filter'])) { ?> style="display:none;" <?php } ?>><i class="icon-placeholder mdi mdi-filter"></i> Filters
                        </button>
                        <button class="btn btn-sm btn-success" style="background:#12263f!important;border: 1px solid #12263F!important;" title="Process Bulk Orders" style="cursor: pointer;color: #A94442;" data-toggle="modal" data-target="#exampleModalprocessorders"><i class="mdi mdi-comment-question"></i></button>

                        <button type="button" class="btn btn-outline-dark show_hide_filter btn-sm" <?php if (empty($_GET['filter'])) { ?> style="display:none;" <?php } ?>><i class="icon-placeholder mdi mdi-close"></i> Close
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form method="get" action="<?= base_url('orders/all') ?>">
                    <div class="row m-b-10" id="filter_row" <?php if (empty($_GET['filter'])) { ?> style="display:none;" <?php } ?>>
                        <div class="col-sm-12">
                            <div class="row m-b-10">
                                <div class="col-sm-3">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <label for="email">From Date:</label>
                                            <input type="text" autocomplete="off" data-start-date="<?= !empty($filter['start_date']) ? $filter['start_date'] : '' ?>" data-end-date="<?= !empty($filter['end_date']) ? $filter['end_date'] : '' ?>" class="form-control date-range-picker col-sm-12">
                                            <input type="hidden" autocomplete="off" id="date-min" name="filter[start_date]" value="<?= !empty($filter['start_date']) ? $filter['start_date'] : '' ?>" placeholder="from">
                                            <input type="hidden" autocomplete="off" id="date-max" name="filter[end_date]" value="<?= !empty($filter['end_date']) ? $filter['end_date'] : '' ?>" placeholder="to">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <label for="email">Order ID(s):</label>
                                    <input type="text" autocomplete="off" name="filter[order_ids]" value="<?= !empty($filter['order_ids']) ? $filter['order_ids'] : '' ?>" class="form-control" placeholder="Seperted by comma">
                                </div>
                                <div class="form-group col-sm-3">
                                    <label for="email">Product Name:</label>
                                    <input type="text" autocomplete="off" name="filter[product_name]" value="<?= !empty($filter['product_name']) ? $filter['product_name'] : '' ?>" class="form-control" placeholder="Product name to search">
                                </div>
                                <div class="col-sm-3">
                                    <label for="email"><span data-toggle="tooltip" data-html="true" title="" data-original-title="Search by Product or Customer Details">Search Query:</span></label>
                                    <input type="text" autocomplete="off" name="filter[search_query]" value="<?= !empty($filter['search_query']) ? $filter['search_query'] : '' ?>" class="form-control" placeholder="Search Anything">
                                </div>
                                <div class="col-sm-3">
                                    <label for="email">Channel:</label>
                                    <select name="filter[channel_id]" class="form-control js-select2" style="width: 100% !important;">
                                        <option value="">Select Channel</option>
                                        <option value="custom" <?php if (!empty($filter['channel_id']) && $filter['channel_id'] == 'custom') { ?> selected <?php } ?>>Custom Orders</option>
                                        <?php
                                        foreach ($channels as $values) {
                                            $channel_id = '';
                                            if (!empty($filter['channel_id']))
                                                $channel_id = $filter['channel_id'];
                                        ?>
                                            <option <?php if ($channel_id == $values->id) { ?> selected <?php } ?> value="<?php echo $values->id; ?>"><?php echo ucwords($values->channel_name); ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-sm-3">
                                    <label for="email">Type:</label>
                                    <select name="filter[pay_method]" class="form-control">
                                        <?php
                                        $pay_method = '';
                                        if (!empty($filter['pay_method']))
                                            $pay_method = $filter['pay_method'];
                                        ?>
                                        <option <?php if ($pay_method == '') { ?> selected <?php } ?> value="">All</option>
                                        <option <?php if ($pay_method == 'cod') { ?> selected <?php } ?> value="cod">COD</option>
                                        <option <?php if ($pay_method == 'prepaid') { ?> selected <?php } ?> value="prepaid">Prepaid</option>
                                        <option <?php if ($pay_method == 'reverse') { ?> selected <?php } ?> value="reverse">Reverse</option>
                                    </select>
                                </div>

                                <div class="col-sm-3">
                                    <label for="email">Order Tag(s):</label>
                                    <input type="text" autocomplete="off" name="filter[tags]" value="<?= !empty($filter['tags']) ? $filter['tags'] : '' ?>" class="form-control" placeholder="Tag name here">
                                </div>

                                <div class="col-sm-3" style="margin-top:34px;">
                                    <button type="submit" class="btn btn-sm btn-success">Apply</button>
                                    <a href="<?= base_url('orders/all'); ?>" class="btn btn-sm btn-primary">Clear</a>
                                </div>


                            </div>
                        </div>



                    </div>
                </form>

                <div class="row border-top p-t-10 p-b-10 action_row_default">
                    <div class="col-sm-10">
                        <div class="btn-group " role="group">
                            <?php
                            $applied_filters = !empty($_GET) ? $_GET : array('filter' => array());
                            $status_filters = $applied_filters;
                            $status_filters['filter']['fulfillment'] = '';
                            $status_filters['filter']['segment_id'] = '';
                            $btn_class = 'btn-default';
                            ?>
                            <a href="<?= base_url('orders/all') . '?' . http_build_query($status_filters); ?>" class="btn  <?= (empty($_GET['filter']['segment_id']) && (!isset($_GET['filter']['fulfillment']) || $_GET['filter']['fulfillment'] == '')) ? 'btn-dark' : 'btn-outline-dark'; ?>">All Orders</a>
                            <?php
                            $status_filters['filter']['fulfillment'] = 'new';
                            ?>
                            <a href="<?= base_url('orders/all') . '?' . http_build_query($status_filters); ?>" class="btn <?= (isset($_GET['filter']['fulfillment']) && $_GET['filter']['fulfillment'] == 'new') ? 'btn-dark' : 'btn-outline-dark'; ?>">Not Shipped (<?= array_key_exists('new', $count_by_status) ? $count_by_status['new'] : '0' ?>)</a>


                            <?php
                            $status_filters['filter']['fulfillment'] = 'booked';
                            ?>
                            <a href="<?= base_url('orders/all') . '?' . http_build_query($status_filters); ?>" class="btn <?= (isset($_GET['filter']['fulfillment']) && $_GET['filter']['fulfillment'] == 'booked') ? 'btn-dark' : 'btn-outline-dark'; ?>">Booked (<?= array_key_exists('booked', $count_by_status) ? $count_by_status['booked'] : '0' ?>)</a>

                            <?php
                            $status_filters['filter']['fulfillment'] = 'cancelled';
                            ?>
                            <a href="<?= base_url('orders/all') . '?' . http_build_query($status_filters); ?>" class="btn <?= (isset($_GET['filter']['fulfillment']) && $_GET['filter']['fulfillment'] == 'cancelled') ? 'btn-dark' : 'btn-outline-dark'; ?>">Cancelled (<?= array_key_exists('cancelled', $count_by_status) ? $count_by_status['cancelled'] : '0' ?>)</a>
                            <?php
                            $segment_filters = $applied_filters;
                            $segment_filters['filter']['segment_id'] = '';
                            $segment_filters['filter']['fulfillment'] = '';
                            if (!empty($segments)) {
                                foreach ($segments as  $segment) {
                                    $segment = (object) $segment;
                                    $segment_filters['filter']['segment_id'] = $segment->id;

                            ?>
                                    <a href="<?= base_url('orders/all') . '?' . http_build_query($segment_filters); ?>" class="btn <?= (isset($_GET['filter']['segment_id']) && $_GET['filter']['segment_id'] == $segment->id) ? 'btn-dark' : 'btn-outline-dark'; ?>"><?= ucwords($segment->name); ?></a>
                            <?php }
                            }
                            ?>

                        </div>
                    </div>
                    <div class="col-sm-2 p-t-5 text-center">
                        <?php if ($current_segment) { ?>
                            <a href="#" class="text-primary add_segment_button" data-segment-id="<?= $current_segment; ?>" style="text-decoration: none;" data-toggle="modal" data-target=".add_edit_segment_modal"><b><i class="mdi mdi-pencil"></i> Edit Segment</b></a>
                        <?php } else { ?>
                            <a href="#" class="text-primary add_segment_button" data-segment-id="" style="text-decoration: none;" data-toggle="modal" data-target=".add_edit_segment_modal"><b><i class="mdi mdi-plus"></i> Add Segment</b></a>
                        <?php } ?>
                    </div>
                </div>
                <div class="row p-t-10 border-top  p-b-10 action_row_selected sticky-top border-bottom" style="display: none;">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text  border-dark"> <b class="multiple_select_count">0</b>&nbsp;selected</span>
                            </div>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-dark bulk-ship-button" data-toggle="modal" data-target=".bd-example-modal-sm"><i class="mdi mdi-package-variant-closed"></i> Bulk Ship</button>
                                <button type="button" class="btn btn-outline-dark bulk-cancel-order-button"><i class="mdi mdi-cancel text-danger"></i> Cancel</button>

                                <button class="btn btn-outline-dark dropdown-toggle" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="mdi mdi-tag-multiple"></i> Tags
                                </button>

                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                    <button class="dropdown-item add_remove_tags_button" data-toggle="modal" data-tag-action="orders/add" data-target=".add_remove_tags">Add Tags</button>
                                    <button class="dropdown-item add_remove_tags_button" data-toggle="modal" data-tag-action="orders/remove" data-target=".add_remove_tags">Remove Tags</button>
                                </div>

                            </div>

                        </div>

                    </div>
                </div>


                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th><input data-switch="true" id="select_all_checkboxes" type="checkbox"></th>
                                <th>Channel</th>
                                <th>Order</th>
                                <th>Date</th>
                                <th>Product</th>
                                <th>Payment</th>
                                <th>Method</th>
                                <th style="width: 10%;">Customer</th>
                                <th>Zip Code</th>
                                <th>Weight</th>
                                <th>Tags</th>
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
                                        <td><?php if ($order->fulfillment_status == 'new' && $valid_shipment) { ?><input value="<?= $order->id; ?>" type="checkbox" class="multiple_checkboxes" name="order_ids"><?php } ?></td>
                                        <td>
                                            <?= (!empty($order->channel_name)) ? ucwords($order->channel_name) : 'Custom'; ?>
                                        </td>
                                        <td><a style="color: #4c66fb;" target="_blank" href="orders/view/<?= $order->id; ?>"><?= $order->order_id; ?></a></td>
                                        <td><?= date('M d, Y', $order->order_date); ?></td>
                                        <td><?php $products = $order->products . ' ' . "(" . $order->products_sku . ")"; ?>
                                            <span data-toggle="tooltip" data-html="true" title="<?= $products; ?>">
                                                <?= mb_strimwidth($products, 0, 20, "..."); ?>
                                            </span>
                                        </td>
                                        <td><?= $order->order_amount; ?></td>
                                        <td><?= strtoupper($order->order_payment_type); ?></td>
                                        <td>
                                            <?php
                                            $customercompany_name = ucwords($order->shipping_company_name);
                                            $customername = ucwords($order->shipping_fname . ' ' . $order->shipping_lname);
                                            $customerphn = isset($order->shipping_phone) ? $order->shipping_phone : '';
                                            $customeradd1 = isset($order->shipping_address) ? $order->shipping_address : '';
                                            $customeradd2 = isset($order->shipping_address_2) ? $order->shipping_address_2 : '';
                                            $compltadd = $customeradd1 . ' ' . $customeradd2;
                                            $shippcity = $order->shipping_city;
                                            $shipstate = $order->shipping_state;
                                            ?>
                                            <span data-toggle="tooltip" data-html="true" title="<?= $customername . '<br>' . (!empty($customercompany_name) ? ucwords($customercompany_name) . '<br/>' : '') . $customerphn . '<br>' . $compltadd . '<br>' . $shippcity . '<br>' . $shipstate; ?>">
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
                                            <?php if (!empty($order->order_applied_tags)) { ?>
                                                <span data-toggle="tooltip" data-html="true" title="<?= str_replace(',', ', ', ucwords($order->order_applied_tags)); ?>">
                                                    <i class="mdi mdi-tag-multiple"></i>
                                                </span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if ($order->fulfillment_status == 'new') { ?>
                                                <?php if (!$valid_shipment) { ?>

                                                    <a href="orders/create/<?= $order->id; ?>" target="blank" data-toggle="tooltip" data-html="true" title="<?= $valid_shipment_error; ?>" class="btn btn-outline-warning btn-sm"><i class="mdi mdi-pencil"></i> Edit</a>
                                                <?php } else { ?>
                                                    <button type="button" class="btn btn-outline-primary shipnowbtn btn-sm" data-order-id='<?= $order->id; ?>' data-toggle="modal" data-target=".bd-example-modal-sm">Ship</button>
                                                <?php
                                                }
                                                ?>

                                            <?php
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
                                            <?php
                                            if ($ivr_enabled) {
                                            ?>

                                                <button class="btn btn-sm btn-outline-info dropdown-toggle" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="mdi mdi-phone"></i>
                                                </button>

                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                    <button class="dropdown-item make_ivr_call" data-toggle="modal" data-target=".make_ivr_call_modal" data-order-id="<?= $order->id; ?>">Call</button>
                                                    <button class="dropdown-item view_ivr_history" data-toggle="modal" data-target=".ivr_history_modal" data-order-id="<?= $order->id; ?>">View History</button>
                                                </div>

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
                                    <td colspan="12" class="text-center">No Records found</td>
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
<div class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" id="fulfillment_info">

        </div>
    </div>
</div>


<div class="modal fade import_orders_modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content" id="fulfillment_info">
            <form method="post" action="<?= base_url('orders/import'); ?>" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="mySmallModalLabel">Bulk Upload Orders</h5>
                </div>
                <div class="modal-body">
                    <div class="row">

                        

                        <div class="col-sm-12 p-b-10">
                            Download sample order upload file : <a class="text-info" href="<?= base_url('assets/orders_samples.csv?v=1.2'); ?>">Download</a>
                        </div>
                        <div class="col-sm-12 m-t-10">
                            <div class="m-b-10">
                                <div class="input-group mb-3">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="importFile">
                                        <label class="custom-file-label" for="inputGroupFile02">Choose file</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-right">
                            <button type="submit" class="btn btn-primary">Upload</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>

                    <div class="row border-top m-t-20 m-b-10">
                        <div class="col-sm-12 p-t-10 text-center">
                            <b>Bulk Order Update</b>
                        </div>
                        <div class="col-sm-12 p-t-10">
                            For bulk orders update export orders and import the file after updates.<br />
                        </div>

                    </div>
                    <div class="row">
                    <iframe width="490" style="margin: 5px;border-radius: 5px;" height="315" src="https://www.youtube.com/embed/f3Ic8Iin3zI" title="How to Bulk Orders Upload in deltagloabal?" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<div class="modal fade make_ivr_call_modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content" id="ivr_call_modal">

        </div>
    </div>
</div>


<div class="modal fade ivr_history_modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" id="ivr_history_data">

        </div>
    </div>
</div>

<div class="modal fade add_edit_segment_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="add_edit_segment_form">

        </div>
    </div>
</div>
<div class="modal fade" id="exampleModalprocessorders" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">How to Proccess Bulk orders in deltagloabal?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="row">
                    <iframe width="490" style="margin: 5px;border-radius: 5px;margin-left: 20px;" height="315" src="https://www.youtube.com/embed/9QVLsta945s" title="How to Proccess Bulk orders in deltagloabal?" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
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

    $('.add_segment_button').on('click', function(e) {
        e.preventDefault();
        var segment_id = $(this).attr('data-segment-id');
        $.ajax({
            url: 'orders/add_edit_segment/' + segment_id,
            type: "GET",
            cache: false,
            success: function(data) {
                $('#add_edit_segment_form').html(data);

            }
        });
    });

    $('.make_ivr_call').on('click', function(e) {
        e.preventDefault();
        var order_id = $(this).attr('data-order-id');
        $.ajax({
            url: 'apps/ivrcalls/call_order',
            type: "POST",
            data: {
                order_id: order_id,
            },
            cache: false,
            success: function(data) {
                $('#ivr_call_modal').html(data);

            }
        });
    });

    $('.view_ivr_history').on('click', function(e) {
        e.preventDefault();
        var order_id = $(this).attr('data-order-id');
        $.ajax({
            url: 'apps/ivrcalls/order_history',
            type: "POST",
            data: {
                order_id: order_id,
            },
            cache: false,
            success: function(data) {
                $('#ivr_history_data').html(data);

            }
        });
    });

    $('.refresh_orders_button').on('click', function(e) {
        e.preventDefault();
        $.ajax({
            url: 'orders/refresh',
            type: "GET",
            cache: false,
            success: function(data) {
                alert('Refresh request received');

            }
        });
    });
</script>