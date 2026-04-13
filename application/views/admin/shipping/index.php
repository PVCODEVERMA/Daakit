<?php
$user_channels = array();
if (!empty($channels)) {
    foreach ($channels as $channel) {
        $user_channels[$channel->id] = $channel->channel_name;
    }
}
?>
<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">Shipments summary</h4>
    <ol class="breadcrumb d-flex flex-wrap">
        <li class="breadcrumb-item btn-list">
            <?php if (in_array('change_payment_type', $user_details->permissions)) { ?>
                <a href="javascript:void(0)" target="_blank" class="btn btn-info btn-sm me-2"  data-bs-target="#scrollmodal" data-bs-toggle="modal"><i class="fa fa-edit"></i> Edit Payment Type</a>
            <?php } ?>
            <?php if (in_array('shipment_change_status', $user_details->permissions)) { ?>
                <a href="<?= base_url('admin/shipmentstatus/all'); ?>" class="btn btn-info btn-sm me-2"><i class="fa fa-edit"></i> Edit Shipment Status </a>
            <?php } ?>
            <?php if (in_array('change_payment_type', $user_details->permissions)) { ?>
                <a href="javascript:void(0)" class="btn btn-info btn-sm me-2" data-bs-target="#ndr_model" data-bs-toggle="modal"><i class="fa fa-upload" aria-hidden="true"></i> Bulk NDR Import </a>            
            <?php } ?>
            <a href="<?= base_url('admin/shipping/exportCSV'); ?><?php if (!empty($filter)) {
                echo "?" . http_build_query($_POST);
            } ?>" class="btn btn-info btn-sm me-2"><i class="fa fa-download" aria-hidden="true"></i> Retrieve Export </a>
            <a href="javascript:void(0);" class="btn btn-info btn-sm me-2" data-bs-toggle="sidebar-right" data-bs-target=".sidebar-right"><i class="fa fa-search" aria-hidden="true"></i> Filter </a>
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
                            <form method="post" id="tab_filter" action="<?= base_url('admin/shipping/list') ?>">
                                <input type="hidden" name="perPage" id="perPage" value="<?= !empty($_POST['perPage']) ? $_POST['perPage'] : '' ?>" />
                                <input type="hidden" autocomplete="off" name="filter[ship_status]" id="ship_status" value="<?= !empty($filter['ship_status']) ? $filter['ship_status'] : '' ?>">
                                <input type="hidden" autocomplete="off" name="filter[rto_status]" id="rto_status" value="<?= !empty($filter['rto_status']) ? $filter['rto_status'] : '' ?>">
                                <input type="hidden" name="page" id="page" value="1">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4 class="mtop10">Shipments Filters</h4>
                                    </div>
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
                                    <div class="col-md-6">
                                        <div class="form-group no-mbot" app-field-wrapper="status[]">
                                            <label for="status[]" class="control-label">AWB NO(s)</label>
                                            <input type="text" autocomplete="off" name="filter[awb_no]" value="<?= !empty($filter['awb_no']) ? $filter['awb_no'] : '' ?>" class="form-control" placeholder="Separated by comma">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group no-mbot" app-field-wrapper="status[]">
                                            <label for="status[]" class="control-label">Order ID(s)</label>
                                            <input type="text" autocomplete="off" name="filter[order_ids]" value="<?= !empty($filter['order_ids']) ? $filter['order_ids'] : '' ?>" class="form-control" placeholder="Separated by comma">
                                            </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group no-mbot" app-field-wrapper="Carrier[]">
                                            <label for="Carrier[]" class="control-label">Carrier</label>
                                                <select name="filter[courier_id]" class="form-control">
                                                    <option value="">All</option>
                                                    <?php if (!empty($couriers)) foreach ($couriers as $courier) { ?>
                                                        <option value="<?= $courier->id; ?>" <?php if (!empty($filter['courier_id']) && $filter['courier_id'] == $courier->id) { ?> selected="" <?php } ?>><?= ucwords($courier->name); ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group no-mbot" app-field-wrapper="Warehouse[]">
                                            <label for="Warehouse[]" class="control-label">Warehouse</label>
                                                <select name="filter[warehouse_id]" class="form-control">
                                                    <option value="">All</option>
                                                    <?php if (!empty($warehouses)) foreach ($warehouses as $warehouse) { ?>
                                                        <option value="<?= $warehouse->id; ?>" <?php if (!empty($filter['warehouse_id']) && $filter['warehouse_id'] == $warehouse->id) { ?> selected="" <?php } ?>><?= ucwords($warehouse->name); ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                    </div>
                                    <div class="col-sm-6" style="margin-top:20px;">
                                        <button type="submit" class="btn btn-sm btn-success">Filter</button>
                                        <a href="<?= base_url('admin/shipping/list'); ?>" class="btn btn-primary btn-sm">Reset</a>                                
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
				<h3 class="card-title">Shipments <span style="font-size:13px">(<?= date('d-m-Y',strtotime($filter['start_date']));?> - <?= date('d-m-Y',strtotime($filter['end_date']));?>)</span>
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
                    <div class="col-sm-6 col-md-6 action_row_selected"  style="display: none;">
                        <div class="dataTables_length" id="responsive-datatable_length">
                            <div class="btn btn-sm btn-success ms-auto">
                                <div class="item-action dropdown">
                                    <a href="javascript:void(0)" data-bs-toggle="dropdown" class="icon" aria-expanded="false" style="color: #ffffff;">
                                    <b>Chosen </b>(<b class="multiple_select_count">0</b>)&nbsp;<i class="fa fa-level-down" aria-hidden="true"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <?php if (in_array('cancel_shipment', $user_details->permissions)) { ?>
                                            <a href="javascript:void(0)" class="dropdown-item admin-bulk-cancel-button">Cancel Shipment</a>
                                        <?php } ?>
                                        <?php if (in_array('shipment_escalate', $user_details->permissions)) { ?>
                                            <!-- <a href="javascript:void(0)" class="dropdown-item create-shipment-escalation-button" rel="shipping">Escalate</a> -->
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-8 col-md-8  action_row_default">
                            <a href="javascript:void(0)" onclick="filter_data_status('','')" class="btn btn-block btn-sm m-b-15 ml-2 mr-2  <?= (!isset($_POST['filter']['ship_status']) || $_POST['filter']['ship_status'] == '') ? 'btn-sm btn-primary' : 'btn-sm btn-outline-primary'; ?>">All</a>
                            <a href="javascript:void(0)" onclick="filter_data_status('booked','')" class="btn btn-block btn-sm m-b-15 ml-2 mr-2 <?= (isset($_POST['filter']['ship_status']) && $_POST['filter']['ship_status'] == 'booked') ? 'btn-sm btn-primary' : 'btn-sm btn-outline-primary'; ?>">Booked (<?= array_key_exists('booked', $count_by_status) ? $count_by_status['booked'] : '0' ?>) </a>
                            <a href="javascript:void(0)" onclick="filter_data_status('new','')" class="btn btn-block btn-sm m-b-15 ml-2 mr-2 <?= (isset($_POST['filter']['ship_status']) && $_POST['filter']['ship_status'] == 'new') ? 'btn-sm btn-primary' : 'btn-sm btn-outline-primary'; ?>">In process (<?= array_key_exists('new', $count_by_status) ? $count_by_status['new'] : '0' ?>)</a>
                            <a href="javascript:void(0)" onclick="filter_data_status('pending pickup','')" class="btn btn-block btn-sm m-b-15 ml-2 mr-2 <?= (isset($_POST['filter']['ship_status']) && $_POST['filter']['ship_status'] == 'pending pickup') ? 'btn-sm btn-primary' : 'btn-sm btn-outline-primary'; ?>">Pending Pickup (<?= array_key_exists('pending pickup', $count_by_status) ? $count_by_status['pending pickup'] : '0' ?>)</a>
                            <a href="javascript:void(0)" onclick="filter_data_status('in transit','')" class="btn btn-block btn-sm m-b-15 ml-2 mr-2 <?= (isset($_POST['filter']['ship_status']) && $_POST['filter']['ship_status'] == 'in transit') ? 'btn-sm btn-primary' : 'btn-sm btn-outline-primary'; ?>">In Transit (<?= array_key_exists('in transit', $count_by_status) ? $count_by_status['in transit'] : '0' ?>)</a>
                            <a href="javascript:void(0)" onclick="filter_data_status('out for delivery','')" class="btn btn-block btn-sm m-b-15 ml-2 mr-2 <?= (isset($_POST['filter']['ship_status']) && $_POST['filter']['ship_status'] == 'out for delivery') ? 'btn-sm btn-primary' : 'btn-sm btn-outline-primary'; ?>">OFD (<?= array_key_exists('out for delivery', $count_by_status) ? $count_by_status['out for delivery'] : '0' ?>)</a>
                            <a href="javascript:void(0)" onclick="filter_data_status('delivered','')" class="btn btn-block btn-sm m-b-15 ml-2 mr-2 <?= (isset($_POST['filter']['ship_status']) && $_POST['filter']['ship_status'] == 'delivered') ? 'btn-sm btn-primary' : 'btn-sm btn-outline-primary'; ?>">Delivered (<?= array_key_exists('delivered', $count_by_status) ? $count_by_status['delivered'] : '0' ?>)</a>
                            <div class="btn btn-sm btn-success ms-auto" style="margin-top: 1px;">
                                <div class="item-action dropdown">
                                    <a href="javascript:void(0)" data-bs-toggle="dropdown" class="icon" aria-expanded="false" style="color: #ffffff;">
                                        <?= (isset($_POST['filter']['ship_status']) && in_array($_POST['filter']['ship_status'], array('rto', 'lost', 'damaged', 'cancelled')) ? ucwords($_POST['filter']['ship_status']) : 'More'); ?> <?= !empty($_POST['filter']['rto_status']) ? ucwords($_POST['filter']['rto_status']) : ''; ?><i class="fa fa-level-down" aria-hidden="true"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="javascript:void(0)" onclick="filter_data_status('cancelled','')" class="dropdown-item">Cancelled</a>
                                        <a href="javascript:void(0)" onclick="filter_data_status('lost','')" class="dropdown-item">Lost</a>
                                        <a href="javascript:void(0)" onclick="filter_data_status('damaged','')" class="dropdown-item">Damaged</a>
                                        <a href="javascript:void(0)" onclick="filter_data_status('rto','')" class="dropdown-item">RTO All</a>
                                        <a href="javascript:void(0)" onclick="filter_data_status('rto','in transit')" class="dropdown-item">RTO In Transit</a>
                                        <a href="javascript:void(0)" onclick="filter_data_status('rto','delivered')" class="dropdown-item">RTO Delivered</a>
                                        <a href="javascript:void(0)" onclick="filter_data_status('rto','lost')" class="dropdown-item">RTO Lost</a>
                                        <a href="javascript:void(0)" onclick="filter_data_status('rto','damaged')" class="dropdown-item">RTO Damaged</a>
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
                                <th><span class="bold">Seller Details</span></th>
                                <th><span class="bold">CONSIGNEE ADDRESS</span></th>
                                <th><span class="bold">PAYMENT DETAILS</span></th>
                                <th><span class="bold">Shipment DETAILS</span></th>
                                <th><span class="bold">Status</span></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            if (!empty($orders)) {
                                foreach ($orders as $order) {
                                    $view_path = base_url('admin/orders/view');
                            ?>
                                    <tr class="<?php if (@$order->whatsapp_status == 'address_request'){ echo 'table-secondary'; } ?>">
                                        <td><input value="<?= $order->shipping_id; ?>" type="checkbox" class="multiple_checkboxes" name="shipping_ids"></td>
                                        <td>
                                            <a style="color: #4c66fb;" target="_blank" href="<?= $view_path; ?>/<?= $order->id; ?>"><?= $order->order_no; ?></a>
                                            <br><?= date('M d,Y h:i A', $order->order_date); ?>
                                            <br> <?= (!empty($order->channel_name)) ? ucwords($order->channel_name) : 'Custom'; ?>
                                        </td>
                                        <td>
                                            Seller ID : #<?= $order->userid; ?>
                                            <br>Seller Name : <?= $order->user_fname." ".$order->user_lname; ?>
                                            <br>Company Name : <?= $order->company_name; ?>
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
                                            <?= '<span style="ffont-weight: bold"> ₹ </span>'. $order->order_amount; ?>
                                            <br><?= strtoupper($order->order_payment_type); ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($order->rto_awb)) { ?>
                                                <?php if (!empty($order->awb_number)) { ?><i class="mdi mdi-arrow-right"></i><a target="blank" style="color: #4c66fb;" href="<?php echo base_url('admin/shipping/tracking')?>/<?= $order->awb_number ?>"><?= ucwords($order->awb_number); ?></a><?php } ?>
                                                <br /><i class="mdi mdi-arrow-left"></i><a target="blank" class="text-info" href="<?php echo base_url('awb/tracking')?>/r/<?= $order->rto_awb ?>"><?= ucwords($order->rto_awb); ?></a>
                                            <?php } else {
                                            ?>
                                                <?php if (!empty($order->awb_number)) { ?><a target="blank" style="color: #4c66fb;" href="<?php echo base_url('awb/tracking')?>/<?= $order->awb_number ?>"><?= ucwords($order->awb_number); ?></a><?php } ?>
                                            <?php }
                                            ?>
                                            <br><?= ucwords($order->courier_name); ?>
                                            <?php if (!empty($order->edd_time)) { ?>
                                                <br><small class="text-muted">
                                                    EDD: <?= date("d M Y, h:i A", $order->edd_time); ?>
                                                </small>
                                            <?php } ?>
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
                                        <!-- <td>
                                            <?php if (!empty($order->order_applied_tags)) { ?>

                                                <span style="cursor: pointer;" data-toggle="tooltip" data-html="true" title="<?= str_replace(',', ', ', ucwords($order->order_applied_tags)); ?>">
                                                    <i class="mdi mdi-tag-multiple add_remove_single_tags_button" data-toggle="modal" data-tag-action="orders/add" data-target=".single_add_remove_tags" data-id="<?= $order->id ?>"></i>
                                                </span>
                                            <?php } else { ?>

                                                <i style="cursor: pointer;" class="mdi mdi-plus-circle tags add_remove_single_tags_button" data-toggle="modal" data-tag-action="orders/add" data-target=".single_add_remove_tags" data-id="<?= $order->id ?>"></i>

                                            <?php } ?>
                                        </td> -->
                                        <td>
                                                <?php $order->ship_status = strtolower($order->ship_status); ?>

                                                <?php if ($order->ship_status == 'new') { ?>
                                                    <button type="button" class="btn btn-warning btn-sm">In-process</button>
                                                    <?php if (!empty($order->ship_message)) { ?><br>
                                                        <div style="color:#8b0001; max-width: 200px;"><?= $order->ship_message; ?></div><?php } ?>
                                                <?php } else if ($order->ship_status == 'booked') { ?>
                                                    <button type="button" class="btn btn-success btn-sm">Booked</button>
                                                <?php } elseif ($order->ship_status == 'pending pickup') { ?>
                                                    <button type="button" class="btn btn-sm btn-primary btn-sm">Waiting for Pickup</button>
                                                <?php } elseif ($order->ship_status == 'cancelled') { ?>
                                                    <button type="button" class="btn btn-danger btn-sm" <?php if (!empty($order->ship_message)) { ?> data-toggle="tooltip" data-html="true" title="<?= $order->ship_message; ?>" <?php } ?>>Cancelled</button>
                                                <?php } elseif (in_array($order->ship_status, array('lost', 'damaged'))) { ?>
                                                    <button type="button" class="btn btn-warning btn-sm"><?= ucwords($order->ship_status); ?></button>
                                                <?php } elseif ($order->ship_status == 'rto') { ?>
                                                    <button type="button" class="btn btn-warning btn-sm">RTO <?= !empty($order->rto_status) ? ucwords($order->rto_status) : '' ?> </button>
                                                <?php } elseif ($order->ship_status == 'exception') { ?>
                                                    <button <?php if (!empty($order->ship_message)) { ?> data-toggle="tooltip" data-html="true" title="<?= $order->ship_message; ?>" <?php } ?> type="button" class="btn btn-outline-info btn-sm">Exception</button>
                                                <?php } else { ?>
                                                    <button type="button" class="btn btn-sm btn-primary btn-sm"><?= ucwords($order->ship_status); ?></button>
                                                <?php } ?>

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
                <h6 class="modal-title">Change Payment Type</h6><button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"></button>
            </div>
            <?php if (in_array('change_payment_type', $user_details->permissions)) { ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card m-b-30">
                        <div class="card-body">
                            <div class="row m-t-30">
                                <div class="col-sm-12 m-b-20">
                                    <b>Change Payment Type Status (cod to prepaid, prepaid to cod)</b>
                                    <p>File downloaded after update</p>
                                </div>
                                
                                <div class="col-sm-12">
                                    <form method="post" action="<?= base_url('admin/shipping/changePaymentType'); ?>" enctype="multipart/form-data" id="formId">
                                        <div class="form-row">
                                            <div class="form-group col-sm-12">
                                                <p>Download sample file : <a class="text-info" href="<?php echo base_url();?>assets/change_payment_type.csv?<?php echo time();?>"><i class="fa fa-download" aria-hidden="true"></i></a></p>
                                                <input class="form-control" type="file" name="importFile" required>
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
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<!-- SCROLLING WITH COTENT MODAL END -->
 <!--- NDR POPUP FOR ADMIN START -->
 <div class="modal fade" id="ndr_model">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content modal-content-demo">
            <div class="modal-header">
                <h6 class="modal-title">Bulk Shipment NDR</h6><button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"></button>
            </div>
            <?php if (in_array('change_payment_type', $user_details->permissions)) { ?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card m-b-30">
                            <div class="card-body">
                                <div class="row m-t-30">
                                    <div class="col-sm-12">
                                        <?php echo form_open(base_url('admin/ndr/import'), array('enctype' => 'multipart/form-data')); ?>
                                            <div class="form-row">
                                                <div class="form-group col-sm-12">
                                                    <label for="exampleFormControlFile1"></label>
                                                    <select name="action" class="form-control ndr_action_change" required>
                                                        <option value="">Choose Action</option>
                                                        <option value="re-attempt">Re-Attempt</option>
                                                        <option value="change address">Change Address</option>
                                                        <option value="change phone">Change Phone Number</option>
                                                        <option value="rto">RTO</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-sm-12">
                                                    <label for="exampleFormControlFile1"></label>
                                                    <p>Download sample file : <a class="text-info" href="<?php echo base_url();?>assets/bulk_ndr_import.csv?<?php echo time();?>"><i class="fa fa-download" aria-hidden="true"></i></a></p>
                                                    <input type="file" required="" name="importFile" class="form-control">
                                                    <div class="modal-footer">
                                                        <button class="btn ripple btn-primary" type="submit">Upload File</button>
                                                        <button class="btn ripple btn-secondary" data-bs-dismiss="modal" type="button">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php echo form_close(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
 <!--- NDR POPUP FOR ADMIN END -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    <?php unset($_POST['perPage']); ?>

    function per_page_records(per_page = false) {
        document.getElementById('perPage').value=per_page;
        document.getElementById('tab_filter').submit();
    }
    function filter_data_status(forward = false,rto= false) {
        document.getElementById('ship_status').value=forward;
        document.getElementById('rto_status').value=rto;
        document.getElementById('tab_filter').submit();
    }
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
              [3, 'desc']
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
