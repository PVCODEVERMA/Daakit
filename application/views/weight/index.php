<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">Weight (Reconciliation)</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item btn-list">		
            <a href="<?= base_url('weight/exportCSV'); ?><?php if (!empty($filter)) { echo "?" . http_build_query($_GET); } ?>" class="btn btn-info btn-sm pull-left display-block mright5" style="margin-right: 10px;"> Export </a>
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
                            <form method="get" action="<?= base_url('weight') ?>" id="form_filter">
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
                                        <div class="form-group col-sm-12" style="margin-top:2px;">
                                            <label for="email">AWB NO(s):</label>
                                            <input type="text" autocomplete="off" name="filter[awb_no]" value="<?= !empty($filter['awb_no']) ? $filter['awb_no'] : '' ?>" class="form-control" placeholder="Enter AWB no(s) separated by comma">
                                        </div>
                                        <div class="form-group col-sm-12" style="margin-top:2px;">
                                            <label for="email">Product Name:</label>
                                            <input type="text" autocomplete="off" name="filter[product_name]" value="<?= !empty($filter['product_name']) ? $filter['product_name'] : '' ?>" class="form-control form-control-sm" placeholder="Product name to search">
                                        </div>


                                        <div class="col-sm-12">
                                            <label for="email">Courier:</label>
                                            <select name="filter[courier_id]" class="form-control js-select2" style="width: 100% !important;">
                                                <option value="">All</option>
                                                <?php if (!empty($couriers)) foreach ($couriers as $courier) { ?>
                                                    <option value="<?= $courier->id; ?>" <?php if (!empty($filter['courier_id']) && $filter['courier_id'] == $courier->id) { ?> selected="" <?php } ?>><?= ucwords($courier->name); ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="col-sm-12" style="margin-top:27px;text-align: end;">
                                            <button type="submit" class="btn btn-sm btn-success">Apply</button>
                                            <a href="<?= base_url('weight'); ?>" class="btn btn-sm btn-primary">Clear</a>
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
                <h3 class="card-title">Weight (Reconciliation)  <span style="font-size:13px">(<?= date('d-m-Y',strtotime($filter['start_date']));?> - <?= date('d-m-Y',strtotime($filter['end_date']));?>)</span></h3>
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
                            <?php
                                $applied_filters = !empty($_GET) ? $_GET : array('filter' => array());
                                $status_filters = $applied_filters;
                                $status_filters['filter']['status'] = '';
                                $btn_class = 'btn-default';
                            ?>
                            <div class="btn btn-sm ms-auto">
                                <?php
                                $status_filters['filter']['status'] = '';
                                ?>
                                <a href="<?= base_url('weight/all') . '?' . http_build_query($status_filters); ?>" class="btn btn-block m-b-15 ml-2 mr-2 btn-sm <?= (!isset($_GET['filter']['status']) || $_GET['filter']['status'] == '') ? 'btn-sm btn-primary' : 'btn-sm btn-outline-primary';; ?>">All</a>
                                <?php
                                $status_filters['filter']['status'] = 'open';
                                ?>
                                <a href="<?= base_url('weight/all') . '?' . http_build_query($status_filters); ?>" class="btn btn-block m-b-15 ml-2 mr-2 btn-sm  <?= (isset($_GET['filter']['status']) && $_GET['filter']['status'] == 'open') ? 'btn-sm btn-primary' : 'btn-sm btn-outline-primary';; ?>">Action Required</a>
                                <?php
                                $status_filters['filter']['status'] = 'accepted';
                                ?>
                                <a href="<?= base_url('weight/all') . '?' . http_build_query($status_filters); ?>" class="btn btn-block m-b-15 ml-2 mr-2 btn-sm <?= (isset($_GET['filter']['status']) && $_GET['filter']['status'] == 'accepted') ? 'btn-sm btn-primary' : 'btn-sm btn-outline-primary';; ?>">Accepted</a>
                                <?php
                                $status_filters['filter']['status'] = 'dispute';
                                ?>
                                <a href="<?= base_url('weight/all') . '?' . http_build_query($status_filters); ?>" class="btn btn-block m-b-15 ml-2 mr-2 btn-sm <?= (isset($_GET['filter']['status']) && $_GET['filter']['status'] == 'dispute') ? 'btn-sm btn-primary' : 'btn-sm btn-outline-primary';; ?>">Open Disputes</a>
                                <?php
                                $status_filters['filter']['status'] = 'dispute closed';
                                ?>                                
                                <a href="<?= base_url('weight/all') . '?' . http_build_query($status_filters); ?>" class="btn btn-block m-b-15 ml-2 mr-2 btn-sm <?= (isset($_GET['filter']['status']) && $_GET['filter']['status'] == 'dispute closed') ? 'btn-sm btn-primary' : 'btn-sm btn-outline-primary';; ?>">Closed Disputes</a>
                            </div>
                        </div>
                    </div>
<!-- 
                    <div class="col-sm-2 col-md-2 action_row_selected"  style="display: none;">
                        <div class="dataTables_length" id="responsive-datatable_length">
                        <div class="btn btn-sm btn-success ms-auto">
                            <div class="item-action dropdown">
                                <a href="javascript:void(0)" data-bs-toggle="dropdown" class="icon" aria-expanded="false" style="color: #ffffff;">
                                <b>Chosen </b>(<b class="multiple_select_count">0</b>)&nbsp;<i class="fa fa-level-down" aria-hidden="true"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="javascript:void(0)" class="dropdown-item fill_bulk_ndr"  data-bs-toggle="modal">Bulk NDR</a>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div> -->
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered border-bottom dataTable no-footer">
                        <thead>
                            <tr>
                                <th>Shipment Details</th>
                                <th>Entered Weight</th>
                                <th>Applied Weight</th>
                                <th>Weight Charges</th>
                                <th>Product</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($records)) {
                                foreach ($records as $record) {
                                   //pr($record); die;
                            ?>
                                    <tr>
                                        <td>
                                            <b>Courier : </b><?= $record->courier_name; ?><br>
                                            <b>AWB No. : </b><a target="_blank" class="text-primary" href="<?php echo base_url('shipping/tracking/');?><?= $record->awb_number ?>"><?= $record->awb_number; ?></a>
                                            <br/><b>Weight Applied Date : </b><?= date('d-m-Y', $record->apply_weight_date); ?>
                                            <br/><b>Order Id : </b><a  target="_blank" class="text-primary" href="<?php echo base_url('orders/view/');?><?php echo $record->o_id;?>" > <?= $record->o_id; ?> </a>
                                        </td>
                                        <td style="min-width:150px;"><b>Dead Weight</b> <?= (!empty($record->seller_dead_weight)) ? $record->seller_dead_weight : '0'; ?>g<br />
                                            <b>LxBxH</b>: <?= (!empty($record->seller_package_length)) ? $record->seller_package_length : '0'; ?>x<?= (!empty($record->seller_package_breadth)) ? $record->seller_package_breadth : '0'; ?>x<?= (!empty($record->seller_package_height)) ? $record->seller_package_height : '0'; ?><br />
                                            <?php if (!empty($record->seller_booking_weight)) {  ?><b>Charged Slab </b> <?= $record->seller_booking_weight; ?>g<br /> <?php } 
                                               
                                                    $len=(!empty($record->seller_package_length)) ? $record->seller_package_length : '0'; 
                                                    $bre=(!empty($record->seller_package_breadth)) ? $record->seller_package_breadth : '0';
                                                    $hei=(!empty($record->seller_package_height)) ? $record->seller_package_height : '0'; 
                                                    $sum = $len * $bre * $hei;
           
                                                    $totalsum = $sum / 5000;
                                                   $weight = ($totalsum * 1000);?> 

                                                <b>Volumetric Weight  </b> <?php echo round($weight)."g"; ?>

                                        </td>
                                        <td>
                                            <b>Applied Slab</b> : <?= $record->weight_new_slab; ?>g
                                        </td>
                                        <td>
                                            <b>Forward</b> : Rs.<?= round($record->weight_difference_charges, 2); ?><br />
                                            <?php if ($record->ship_status == 'rto' && $plan_type != 'per_dispatch') { ?>
                                                <b>RTO</b> : Rs.<?= round($record->weight_difference_charges, 2); ?><br />
                                            <?php } ?>
                                            <b>Charged to wallet </b> : <?= ($record->applied_to_wallet) ? 'Yes' : 'No' ?>

                                        </td>
                                        <td>
                                            <span data-bs-toggle="tooltip" data-bs-html="true" data-bs-original-title="<?= ucwords($record->product_name) ?>">
                                                <?= ucwords(mb_strimwidth($record->product_name, 0, 30, "...")); ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                            switch (strtolower($record->seller_action_status)) {
                                                case 'dispute':
                                                    echo "<b><a class='btn btn-block m-b-15 ml-2 mr-2 btn-sm btn-sm btn-outline-primary' href='javascript:void(0)'>Dispute Raised</a></b>";
                                                    break;
                                                case 'dispute closed':
                                                    echo "<b><a class='btn btn-block m-b-15 ml-2 mr-2 btn-sm btn-sm btn-outline-primary' href='javascript:void(0)'>Dispute Closed</a></b>";
                                                    break;
                                                case 'accepted':
                                                    echo "<b>Accepted</b>";
                                                    break;
                                                case 'auto accepted':
                                                    echo "<b>Auto Accepted</b>";
                                                    break;
                                                case 'open':

                                            ?>
                                                    <button class="btn btn-sm btn-success accept_weight_button" data-bs-toggle="modal" data-shipment-id="<?= $record->id; ?>">Accept</button>
                                                    <button class="btn btn-sm btn-info escalation-button" data-bs-toggle="modal" data-bs-target="#escilation-modal" data-shipment-id="<?= $record->id; ?>">Raise Dispute</button>
                                                    <br /><br />
                                                    <div class="m-t-10"><b class="btn btn-block btn-sm btn-outline-danger"><?= time_left_for_weight_dispute(date('Y-m-d', $record->apply_weight_date + $dispute_time_limit)); ?> remaining</b></div>
                                            <?php
                                                    break;
                                                default:
                                                    echo "<b>" . ucwords($record->seller_action_status) . "</b>";
                                            }
                                            ?>

                                        </td>

                                    </tr>
                                <?php
                                }
                            } else {
                                ?>
                                <tr>
                                    <td class="text-center" colspan="11">No Records Found</td>
                                </tr>
                            <?php } ?>


                        </tbody>
                    </table>
                </div>
                <div class="row">
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
<!-- END ROW-1 -->
</div>
<!--Remark Popup start here-->
<div class="modal fade " id="escilation-modal" tabindex="-1" role="dialog" aria-labelledby="slideRightModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="slideRightModalLabel">Create Weight Dispute</h5>
            </div>
            <div class="modal-body">
                <form method="post" id="escalate_pickup">
                    <div class="row">
                        <div class="col-sm-12 p-b-10">
                            Please upload <b>photographs</b> of your shipment so we can escalate the issue to the courier company
                        </div>
                        <div class="col-lg-12">

                            <div class="form-group">
                                <input type="hidden" id="esc_shipment_id" value="" name="shipment_id">
                                <label>Remarks</label>
                                <textarea class="form-control" name="remarks" required placeholder="Enter Remark"></textarea>
                            </div>
                            <div class="form-group files">
                                <label>Attachments (If any) </label>
                                <input type="file" name="importFile[]" id="filetoupload" class="form-control" multiple="">
                                <small>Maximum file size : 5 MB</small>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <button type="button" class="btn btn-secondary" data-bs-    dismiss="modal">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="exampleModalprocessorders" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">How to Raise a Weight Dispute on Daakit Seller Panel?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="row">
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    <?php unset($_GET['perPage']); ?>

    function per_page_records(per_page = false) {
        var page_url = '<?= base_url('weight/all') . '?' . http_build_query($_GET) . '&perPage=' ?>' + per_page;
        window.location.href = page_url;
    }

    $('.escalation-button').on('click', function(e) {
        var shipment_id = $(this).attr('data-shipment-id');
        $("#esc_shipment_id").val(shipment_id);
    });


    $('.accept_weight_button').on('click', function(e) {
        e.preventDefault();

        if (!confirm("Are you sure?"))
            return;

        var shipment_ids = [];
        var shipment_id = $(this).attr('data-shipment-id');
        shipment_ids.push(shipment_id);
        accept_weight_seller(shipment_ids);
    });

    function accept_weight_seller(shipment_ids = false) {
        if (!shipment_ids)
            return false;

        $.ajax({
            url: 'weight/accept_weight',
            type: "POST",
            data: {
                ids: shipment_ids,
            },
            cache: false,
            success: function(data) {
                if (data.success) {
                    alert(data.success);
                    location.reload();
                } else if (data.error)
                    alert(data.error);
            }
        });
    }

    $("#escalate_pickup").submit(function(event) {
        event.preventDefault();
        var form_data = new FormData(this);
        $.ajax({
            url: 'weight/raise_dispute',
            type: "POST",
            data: form_data,
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                if (data.success) {
                    location.reload();
                } else if (data.error)
                    alert(data.error);
            }
        });
    });

    $('.create-weight-escalation-button').on('click', function(e) {
        e.preventDefault();
        var shipment_ids = [];
        $.each($("input[class='multiple_checkboxes']:checked"), function() {
            shipment_ids.push($(this).val());
        });

        if (shipment_ids.length < 1) {
            alert('Please select records');
            return;
        }
        $("#esc_shipment_id").val(shipment_ids);
        $('#escilation-modal').modal('show');
    });

    $('.accept_weight_bulk_button').on('click', function(e) {
        e.preventDefault();

        if (!confirm("Are you sure?"))
            return;

        var shipment_ids = [];
        $.each($("input[class='multiple_checkboxes']:checked"), function() {
            shipment_ids.push($(this).val());
        });

        if (shipment_ids.length < 1) {
            alert('Please select records');
            return;
        }

        accept_weight_seller(shipment_ids);

    });
</script>
