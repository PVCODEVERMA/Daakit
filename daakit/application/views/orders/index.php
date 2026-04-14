<?php
$filter_path = 'orders/all';
?>
<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">Order summary</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item btn-list">		
            <a href="<?= base_url('orders/create');?>" class="btn btn-info btn-sm pull-left display-block mright5" style="margin-right: 10px;"> Create Order </a>
            <a href="<?= base_url('shipping/all');?>" class="btn btn-info btn-sm pull-left display-block mright5" style="margin-right: 10px;"> All Shipments </a>
            <button class="tn btn-info btn-sm pull-left display-block mright5" data-bs-target="#scrollmodal" data-bs-toggle="modal" fdprocessedid="c8ebt9" style="margin-right: 10px;"> <i class="mdi mdi-arrow-up-bold-circle" ></i> Import</button>
            <a href="<?= base_url('orders/exportCSV');?><?php if (!empty($filter)) {
                                                                        echo "?" . http_build_query($_POST);
                                                                    } ?>" class="btn btn-info btn-sm pull-left display-block mright5" style="margin-right: 10px;"> Export </a>
            <a href="javascript:void(0);" class="btn btn-info btn-sm pull-left display-block mright5" data-bs-toggle="sidebar-right" data-bs-target=".sidebar-right" style="margin-right: 10px;"> Filter </a>
        </li>
    </ol>
</div>
<!-- END PAGE-HEADER -->
 <!-- filter section start -->

 <div class="sidebar sidebar-right sidebar-animate">
	<div class="p-4">
		<a href="#" class="float-end sidebar-close" data-bs-toggle="sidebar-right" data-bs-target=".sidebar-right"><i class="fa fa-times"></i></a>
	</div>
    <br>
	<div class="panel-body tabs-menu-body side-tab-body p-0 border-0 ">
		<div class="tab-content border-top">
			<div class="tab-pane active" id="tab1">
				<div class="chat">
					<div class="contacts_card">
						<div class="input-group p-3">
                            <form method="post" action="<?= base_url($filter_path) ?>"  id="tab_filter">
                                <input type="hidden" name="perPage" id="perPage" value="<?= !empty($_POST['perPage']) ? $_POST['perPage'] : '' ?>" />
                                <input type="hidden" name="page" id="page" value="1">
                                <input type="hidden" autocomplete="off" name="filter[fulfillment]" id="fulfillment" value="<?= !empty($filter['fulfillment']) ? $filter['fulfillment'] : '' ?>">
                                <input type="hidden" autocomplete="off" name="filter[segment_id]" id="segment_id" value="<?= !empty($filter['segment_id']) ? $filter['segment_id'] : '' ?>">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group" app-field-wrapper="from_date">
                                        <label for="from_date" class="control-label">From Date</label>
                                            <input type="date"  name="filter[start_date]" value="<?= !empty($filter['start_date']) ? $filter['start_date'] : '' ?>" class="form-control fc-datepicker"  autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group" app-field-wrapper="to_date">
                                        <label for="to_date" class="control-label">To Date</label>
                                            <input type="date" id="to_date" name="filter[end_date]" class="form-control" value="<?= !empty($filter['end_date']) ? $filter['end_date'] : '' ?>" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group" app-field-wrapper="from_date">
                                            <label for="from_date" class="control-label">Order ID(s)</label>
                                            <input type="text" autocomplete="off" name="filter[order_ids]" value="<?= !empty($filter['order_ids']) ? $filter['order_ids'] : '' ?>" class="form-control" placeholder="separated by comma">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group" app-field-wrapper="to_date">
                                            <label for="to_date" class="control-label">Product Name</label>
                                            <input type="text" autocomplete="off" name="filter[product_name]" value="<?= !empty($filter['product_name']) ? $filter['product_name'] : '' ?>" class="form-control" placeholder="Product name to search">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group" app-field-wrapper="from_date">
                                            <label for="from_date" class="control-label">Channel</label>
                                            <select class="form-control" data-width="100%" name="direction" id="direction">
                                                <option value="">--Select--</option>
                                                <option value="custom" <?php if (!empty($filter['channel_id']) && $filter['channel_id'] == 'custom') { ?> selected <?php } ?>>Custom Orders</option>
                                                <?php
                                                foreach ($channels as $values) {
                                                    $channel_id = '';
                                                    if (!empty($filter['channel_id']))
                                                        $channel_id = $filter['channel_id'];
                                                ?>
                                                    <option <?php if ($channel_id == $values->id) { ?> selected <?php } ?> value="<?php echo $values->id; ?>"><?php echo ucwords($values->channel_name); ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group" app-field-wrapper="to_date">
                                            <label for="to_date" class="control-label">Type</label>
                                            <select class="form-control" data-width="100%" name="direction" id="direction">
                                                <?php
                                                $pay_method = '';
                                                if (!empty($filter['pay_method']))
                                                    $pay_method = $filter['pay_method'];
                                                ?>
                                                <option <?php if ($pay_method == '') { ?> selected <?php } ?> value="">--All--</option>
                                                <option <?php if ($pay_method == 'cod') { ?> selected <?php } ?> value="cod">COD</option>
                                                <option <?php if ($pay_method == 'prepaid') { ?> selected <?php } ?> value="prepaid">Prepaid</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group" app-field-wrapper="to_date">
                                            <label for="to_date" class="control-label">Order Tag(s):</label>
                                            <input type="text" autocomplete="off" name="filter[tags]" value="<?= !empty($filter['tags']) ? $filter['tags'] : '' ?>" class="form-control" placeholder="Tag name here">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group" app-field-wrapper="ivr_status">
                                            <label for="ivr_status" class="control-label">IVR Status</label>
                                            <select class="form-control" data-width="100%" name="filter[ivr_status]" id="ivr_status">
                                                <option value="">--Select--</option>
                                                <option value="1" <?php if (isset($filter['ivr_status']) && (int)$filter['ivr_status'] === 1) { ?> selected <?php } ?>>Verified</option>
                                                <option value="0" <?php if (isset($filter['ivr_status']) && (int)$filter['ivr_status'] === 0) { ?> selected <?php } ?>>Non-Verified</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6" style="margin-top:29px;">
                                        <div class="form-group" app-field-wrapper="to_date">
                                            <button type="submit" class="btn btn-sm btn-success">Filter</button>
                                            <a href="<?= base_url($filter_path) ?>" class="btn btn-sm btn-primary">Reset</a>
                                        </div>
                                    </div>
                                </div>
                            </form>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
 <!-- filter section end-->
<div class="main-container container-fluid">
<!-- START ROW-1 -->
<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="card">
			<div class="card-header">
                <h3 class="card-title">Orders <span style="font-size:13px">(<?= date('d-m-Y',strtotime($filter['start_date']));?> - <?= date('d-m-Y',strtotime($filter['end_date']));?>)</span></h3>
			</div>
            <div class="card-body">
            <div id="responsive-datatable_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                <div class="row">
                    <div class="col-sm-1 col-md-1">
                        <div class="dataTables_length" id="responsive-datatable_length">
                        <label>Show
                            <?php
                                $per_page_options = array(
                                    '10' => '10',
                                    '20' => '20',
                                    '50' => '50',
                                    '100' => '100',
                                    '200' => '200',
                                    '500' => '500',
                                );

                                $js = "class='form-select form-select-sm select2' onchange='per_page_records(this.value)'";
                                echo form_dropdown('per_page', $per_page_options, $limit, $js);
                                ?>
                        </label>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-6  action_row_default" style="margin-top: -7px;">
                        <div class="dataTables_length" id="responsive-datatable_length">
                            <div class="btn btn-sm ms-auto">
                                    <a href="javascript:void(0)" onclick="filter_data_status('')" class="btn  <?= (empty($_POST['filter']['segment_id']) && (!isset($_POST['filter']['fulfillment']) || $_POST['filter']['fulfillment'] == '')) ? 'btn-sm btn-primary' : 'btn-sm btn-outline-primary'; ?>">All Orders</a>
                                    <a href="javascript:void(0)" onclick="filter_data_status('new')" class="btn <?= (isset($_POST['filter']['fulfillment']) && $_POST['filter']['fulfillment'] == 'new') ? 'btn-sm btn-primary' : 'btn-sm btn-outline-primary'; ?>">Unshipped (<?= array_key_exists('new', $count_by_status) ? $count_by_status['new'] : '0' ?>)</a>
                                    <a href="javascript:void(0)" onclick="filter_data_status('booked')" class="btn <?= (isset($_POST['filter']['fulfillment']) && $_POST['filter']['fulfillment'] == 'booked') ? 'btn-sm btn-primary' : 'btn-sm btn-outline-primary'; ?>">Booked (<?= array_key_exists('booked', $count_by_status) ? $count_by_status['booked'] : '0' ?>)</a>
                                    <a href="javascript:void(0)"  onclick="filter_data_status('cancelled')" class="btn <?= (isset($_POST['filter']['fulfillment']) && $_POST['filter']['fulfillment'] == 'cancelled') ? 'btn-sm btn-primary' : 'btn-sm btn-outline-primary'; ?>">Cancelled (<?= array_key_exists('cancelled', $count_by_status) ? $count_by_status['cancelled'] : '0' ?>)</a>
                                    <?php
                                    $segment_filters = $applied_filters;
                                    $segment_filters['filter']['segment_id'] = '';
                                    $segment_filters['filter']['fulfillment'] = '';
                                    if (!empty($segments)) {
                                        foreach ($segments as  $segment) {
                                            $segment = (object) $segment;
                                            $segment_filters['filter']['segment_id'] = $segment->id;
                                    ?>
                                            <a href="<?= base_url($filter_path) . '?' . http_build_query($segment_filters); ?>" class="btn <?= (isset($_GET['filter']['segment_id']) && $_GET['filter']['segment_id'] == $segment->id) ? 'btn-sm btn-primary ' : 'btn-sm btn-outline-primary'; ?>"><?= ucwords($segment->name); ?></a>
                                    <?php }
                                    } ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-2 col-md-2 action_row_selected"  style="display: none;">
                        <div class="dataTables_length" id="responsive-datatable_length">
                        <div class="btn btn-sm btn-success ms-auto">
                            <div class="item-action dropdown">
                                <a href="javascript:void(0)" data-bs-toggle="dropdown" class="icon" aria-expanded="false" style="color: #ffffff;">
                                <b>Chosen </b>(<b class="multiple_select_count">0</b>)&nbsp;<i class="fa fa-level-down" aria-hidden="true"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="javascript:void(0)" class="dropdown-item bulk-ship-button" data-bs-target="#lgscrollmodal" data-bs-toggle="modal">Get Bulk AWB</a>
                                    <div class="dropdown-divider"></div>
                                    <?php if (!empty($user_details->parent_id)  &&    in_array('cancel_shipments', $user_details->permissions)) { ?>
                                        <a href="javascript:void(0)" class="dropdown-item bulk-cancel-order-button">Cancel Order</a>
                                    <?php } else if (empty($user_details->parent_id)) { ?>
                                        <a href="javascript:void(0)" class="dropdown-item bulk-cancel-order-button">Cancel Order</a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered border-bottom" id="responsive-datatable">
                    <thead>
                        <tr>
                            <th><span class="bold"><input data-switch="true" id="select_all_checkboxes" type="checkbox"></span></th>
                            <th><span class="bold">Order ID</span></th>
                            <th><span class="bold">CONSIGNEE ADDRESS</span></th>
                            <th><span class="bold">PACKAGE DETAILS</span></th>
                            <th><span class="bold">PAYMENT DETAILS</span></th>
                            <th><span class="bold">Tags</span></th>
                            <th><span class="bold">IVR Status</span></th>
                            <th><span class="bold">Status</span></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                          if (!empty($orders)) {
                              $details_required = array(
                                  'order_no' => 'Order ID',
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


                                  $view_path = 'orders/view';
                          ?>
                                  <tr class="<?php if (@$order->whatsapp_status == 'address_request'){ echo 'table-secondary'; } ?>">
                                      <td><?php if ($order->fulfillment_status == 'new' && $valid_shipment) { ?><input value="<?= $order->id; ?>" type="checkbox" class="multiple_checkboxes" name="order_ids"><?php } ?></td>
                                      <td>
                                        <a style="color: #4c66fb;" target="_blank" href="<?= base_url($view_path); ?>/<?= $order->id; ?>"><?= $order->order_no; ?></a>
                                        <br><?= date('M d,Y h:i A', (int)$order->order_date); ?>
                                        <br> <?= (!empty($order->channel_name)) ? ucwords($order->channel_name) : 'Custom'; ?>
                                      </td>
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
                                            <?= $customername . '<br>' . (!empty($customercompany_name) ? ucwords($customercompany_name).' ( '.$customerphn . ' ) <br/>' : '')  . $compltadd . '<br> ( ' . $shippcity .' '. $shipstate .' '.  ucwords($order->shipping_zip) .' )';; ?>
                                          </span>
                                      </td>
                                      <td>
                                             Pkg Wt. <?= is_numeric($order->package_weight) ? round($order->package_weight / 1000, 2) : '0'; ?> Kg
                                            <br>Vol Wt. <?= ((int)$order->package_length * (int)$order->package_breadth * (int)$order->package_height/5000);?> gm (<?php echo $order->package_length;?> x <?php echo $order->package_breadth;?> x <?php echo $order->package_height;?> cm)
                                      </td>
                                      <td>
                                        <?= '<span style="ffont-weight: bold"> ₹ </span>'. $order->order_amount; ?>
                                        <br><?= strtoupper($order->order_payment_type); ?>
                                    </td>
                                      <!-- <td>
                                          <?php if (!empty($order->ivr_calling_status)) {
                                              if ($order->ivr_calling_status == 'confirm') { ?>
                                                  <span class="badge badge-success">Verified</span>
                                              <?php   } else { ?>
                                                  <span class="badge badge-danger">Cancelled</span>
                                          <?php  }
                                          }
                                          ?>
                                      </td> -->
                                      <!-- <td>
                                          <?php
                                          if (!empty($order->whatsapp_status)) {
                                              if ($order->whatsapp_status == 'confirm') { ?>
                                                  <span class="badge badge-success">Confirm</span>
                                              <?php   } elseif ($order->whatsapp_status == 'cancel') { ?>
                                                  <span class="badge badge-danger">Cancelled</span>
                                          <?php  } elseif ($order->whatsapp_status == 'address_request'){ echo '<span class="badge badge-primary">Address Change</span>'; } else {
                                                  echo '';
                                              }
                                          }
                                          ?>
                                      </td> -->
                                      <td>
                                          <?php if (!empty($order->applied_tags)) { ?>

                                              <span style="cursor: pointer;" data-toggle="tooltip" data-html="true" title="<?= str_replace(',', ', ', ucwords($order->applied_tags)); ?>">
                                                  <i class="fa fa-tags add_remove_single_tags_button" data-bs-toggle="modal" data-tag-action="orders/add" data-bs-target=".single_add_remove_tags" data-id="<?= $order->id ?>"></i>
                                              </span>
                                          <?php } else { ?>

                                              <i style="cursor: pointer;" class="fa fa-plus-square add_remove_single_tags_button" data-bs-toggle="modal" data-tag-action="orders/add" data-bs-target=".single_add_remove_tags" data-id="<?= $order->id ?>"></i>

                                          <?php } ?>
                                      </td>
                                      <td>
                                        <?php
                                        if (!empty($order->ivr_status)) {
                                            echo '<span class="badge text-dark badge-info">' . ucwords($order->ivr_status) . '</span>';
                                        } else {
                                            echo '';
                                        }
                                        ?>
                                    </td>
                                      <td>
                                          <?php if ($order->fulfillment_status == 'new') { ?>
                                              <?php if (!$valid_shipment) { ?>
                                                  <a href="<?php echo base_url('orders/create/');?><?= $order->id; ?>" target="blank" data-toggle="tooltip" data-html="true" title="<?= $valid_shipment_error; ?>" class="btn btn-success btn-sm"><i class="mdi mdi-pencil"></i> Edit</a>
                                              <?php } else { ?>
                                                  <button type="button" class="btn btn-outline-success shipnowbtn btn-sm" data-order-id='<?= $order->id; ?>' data-bs-target="#lgscrollmodal" data-bs-toggle="modal">Get AWB</button>
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
                                              <!-- <button class="btn btn-sm btn-outline-info dropdown-toggle" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                  <i class="mdi mdi-phone"></i>
                                              </button> -->

                                              <!-- <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                  <button class="dropdown-item make_ivr_call" data-toggle="modal" data-target=".make_ivr_call_modal" data-order-id="<?= $order->id; ?>">Call</button>
                                                  <button class="dropdown-item view_ivr_history" data-toggle="modal" data-target=".ivr_history_modal" data-order-id="<?= $order->id; ?>">View History</button>
                                              </div> -->
                                          <?php
                                          }
                                          ?>
                                      </td>
                                  </tr>
                              <?php }
                          } else { ?>
                              <tr>
                                  <td colspan="12" class="text-center">No entries found.</td>
                              </tr>
                          <?php } ?>
                    </tbody>
                    </table> 
                    </div>
                    <div class="row mt-4">
                        <div class="col-sm-12 col-md-6">
                            <div class="dataTables_info" id="example-multi_info" role="status" aria-live="polite">Showing <?= $offset + 1; ?> to <?= $offset + $limit; ?> of <?= $total_records; ?> entries</div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <div class="dataTables_paginate paging_simple_numbers" id="responsive-datatable_paginate">
                                <ul class="pagination mb-0" style="float: right;">
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
<!-- END ROW-1 -->
</div>
<!-- SCROLLING WITH COTENT MODAL START -->
<div class="modal fade" id="scrollmodal">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content modal-content-demo">
            <div class="modal-header">
                <h6 class="modal-title">Upload Orders (Bulk)</h6><button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"></button>
            </div>
            <form action="<?php echo base_url();?>orders/import" enctype="multipart/form-data" method="post" accept-charset="utf-8" data-gtm-form-interact-id="0">
                <div class="modal-body">
                        <div class="col-lg-12 col-sm-12 mb-4 mb-lg-0">
                        <div class="form-group">
                        <p>Download sample file : <a class="text-info" href="<?php echo base_url();?>assets/downloaded_order_sample.csv?<?php echo time();?>"><i class="fa fa-download" aria-hidden="true"></i></a></p>
                        <br>
                        <input class="form-control" type="file" name="importFile" required>
                    </div>               
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn ripple btn-primary" type="submit">Upload File</button>
                    <button class="btn ripple btn-secondary" data-bs-dismiss="modal" type="button">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade bd-modal-lg"  role="dialog" id="lgscrollmodal">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 2000px;">
        <div class="modal-content" id="show_information">
        </div>
    </div>
</div>
<!-- SCROLLING WITH COTENT MODAL END -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  <?php unset($_POST['perPage']); ?>

  function per_page_records(per_page = false) {
    document.getElementById('perPage').value=per_page;
    document.getElementById('tab_filter').submit();
  }
  function filter_data_status(status = false) {
    document.getElementById('fulfillment').value=status;
    document.getElementById('tab_filter').submit();
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
  $(document).ready(function() {
      $('#responsive-datatable').DataTable({
          "aoColumnDef": [
              null,
              null,
              null,
              null,
              null,
              {
                  "sType": "numeric"
              },
              null,
              {
                  "sType": "string"
              },
              null,
              null,
              null,
              null
          ],
          aoColumnDefs: [{
              orderable: false,
              aTargets: [0]
          }],
          'aaSorting': [
              [3, 'asc']
          ],
          "paging": false, // false to disable pagination (or any other option)
          "filter": false,
          "info": false,
      });

  });

  $(document).on('click', '.page-link', function (e) {
    e.preventDefault();
    // Get the page number from the link
    var pageNum = $(this).data('ci-pagination-page');
    if(pageNum===undefined)
        pageNum=1;

    // Set the page number in the hidden input field
    $('#page').val(pageNum);
    // Submit the form
    $('#tab_filter').submit();
});
</script>

