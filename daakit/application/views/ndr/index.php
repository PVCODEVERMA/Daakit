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
    <h4 class="page-title">NDR summary</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item btn-list">		
            <a href="<?= base_url('ndr/exportCSV'); ?><?php if (!empty($filter)) {
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
                            <form method="post" action="<?= base_url('ndr') ?>" id="form_filter">
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
                                    <div class="col-md-12">
                                        <div class="form-group" app-field-wrapper="from_date">
                                            <label for="from_date" class="control-label">Order ID(s)</label>
                                            <input type="text" autocomplete="off" name="filter[order_ids]" value="<?= !empty($filter['order_ids']) ? $filter['order_ids'] : '' ?>" class="form-control" placeholder="separated by comma">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group" app-field-wrapper="from_date">
                                            <label for="from_date" class="control-label">AWB NO(s)</label>
                                            <input type="text" autocomplete="off" name="filter[awb_no]" value="<?= !empty($filter['awb_no']) ? $filter['awb_no'] : '' ?>" class="form-control" placeholder="Enter AWB no(s) separated by comma">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group" app-field-wrapper="to_date">
                                            <label for="to_date" class="control-label">Courier</label>
                                            <select name="filter[courier_id]" class="form-control js-select2" style="width: 100% !important;">
                                                <option value="">All</option>
                                                <?php if (!empty($couriers)) foreach ($couriers as $courier) { ?>
                                                    <option value="<?= $courier->id; ?>" <?php if (!empty($filter['courier_id']) && $filter['courier_id'] == $courier->id) { ?> selected="" <?php } ?>><?= ucwords($courier->name); ?></option>
                                                <?php } ?>
                                            </select>                                        
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group" app-field-wrapper="from_date">
                                            <label for="from_date" class="control-label">Channel</label>
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
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group" app-field-wrapper="to_date">
                                            <label for="to_date" class="control-label">Type</label>
                                            <select name="filter[pay_method]" class="form-control js-select2" style="width: 100% !important;">
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
                                </div>
                                <div class="row">
                                    <div class="col-md-6" style="margin-top:29px;">
                                        <div class="form-group" app-field-wrapper="to_date">
                                            <input type="hidden" autocomplete="off" name="filter[status]" id="status" value="<?= !empty($filter['fulfillment']) ? $filter['fulfillment'] : '' ?>">
                                            <button type="submit" class="btn btn-sm btn-success">Filter</button>
                                            <a href="<?= base_url('ndr'); ?>" class="btn btn-sm btn-primary">Reset</a>
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
                <h3 class="card-title">NDR <span style="font-size:13px">(<?= date('d-m-Y',strtotime($filter['start_date']));?> - <?= date('d-m-Y',strtotime($filter['end_date']));?>)</span></h3>
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
                            $all_actions=0;
                            if(!empty($count_by_status->action_required))
                                $all_actions+=$count_by_status->action_required;
                            if(!empty($count_by_status->action_requested))
                                $all_actions+=$count_by_status->action_requested;
                            if(!empty($count_by_status->delivered))
                                $all_actions+=$count_by_status->delivered;
                            if(!empty($count_by_status->rto))
                                $all_actions+=$count_by_status->rto;
                            ?>
                            <div class="btn btn-sm ms-auto">
                                <a href="javascript:void(0)" onclick="filter_data_status('pending')" class="btn btn-block m-b-15 ml-2 mr-2 btn-sm  <?= (!isset($_POST['filter']['status']) || $_POST['filter']['status'] == 'pending') ? 'btn-sm btn-primary' : 'btn-sm btn-outline-primary'; ?>">Action Required (<?= (!empty($count_by_status->action_required)) ? $count_by_status->action_required : '0'; ?>)</a>
                                <a href="javascript:void(0)" onclick="filter_data_status('submitted')" class="btn btn-block m-b-15 ml-2 mr-2 btn-sm <?= (isset($_POST['filter']['status']) && $_POST['filter']['status'] == 'submitted') ? ' btn-sm btn-primary' : 'btn-sm btn-outline-primary'; ?>">Action Taken (<?= (!empty($count_by_status->action_requested)) ? $count_by_status->action_requested : '0'; ?>)</a>
                                <a href="javascript:void(0)" onclick="filter_data_status('delivered')" class="btn btn-block m-b-15 ml-2 mr-2 btn-sm <?= (isset($_POST['filter']['status']) && $_POST['filter']['status'] == 'delivered') ? 'btn-sm btn-primary' : 'btn-sm btn-outline-primary'; ?>">Delivered (<?= (!empty($count_by_status->delivered)) ? $count_by_status->delivered : '0'; ?>)</a>
                                <a href="javascript:void(0)" onclick="filter_data_status('rto')" class="btn btn-block m-b-15 ml-2 mr-2 btn-sm <?= (isset($_POST['filter']['status']) && $_POST['filter']['status'] == 'rto') ? 'btn-sm btn-primary' : 'btn-sm btn-outline-primary'; ?>">RTO (<?= (!empty($count_by_status->rto)) ? $count_by_status->rto : '0'; ?>)</a>
                                <a href="javascript:void(0)" onclick="filter_data_status('')" class="btn btn-block m-b-15 ml-2 mr-2 btn-sm  <?= (!isset($all_actions) || $_POST['filter']['status'] == '') ? 'btn-sm btn-primary' : 'btn-sm btn-outline-primary'; ?>">All (<?= (!empty($all_actions)) ? $all_actions : '0'; ?>)</a>
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
                                    <a href="javascript:void(0)" class="dropdown-item fill_bulk_ndr"  data-bs-toggle="modal">Bulk NDR</a>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered border-bottom dataTable no-footer">
                        <thead>
                            <tr>
                                <th><input data-switch="true" id="select_all_checkboxes" type="checkbox"></th>
                                <th>NDR Date</th>
                                <th>Order Info</th>
                                <th>Customer Info</th>
                                <th>Shipment Info</th>
                                <th>Exception Info</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($ndrs)) {
                                foreach ($ndrs as $ndr) {
                            ?>
                                    <tr>
                                        <td>
                                            <?php if ($ndr->ndr_action == 'ndr' && !in_array($ndr->ship_status, array('rto', 'delivered'))) { ?>
                                                <input value="<?= $ndr->id; ?>" type="checkbox" class="multiple_checkboxes" name="ndr_ids">
                                            <?php } ?>
                                        </td>
                                        <td><?= date('M d,Y h:i A',$ndr->created); ?></td>
                                        <td><a target="_blank" class="text-info" href="orders/view/<?= $ndr->order_id; ?>"><?= $ndr->order_number; ?></a>
                                        <br>
                                        <span data-toggle="tooltip" data-html="true" title="<?= $ndr->products; ?>"><?= mb_strimwidth($ndr->products, 0, 14, "..."); ?>
                                        </span>
                                        <br>
                                        <?= $ndr->order_amount; ?>(<?= ucwords($ndr->order_payment_type) ?>)</td>
                                        <td>
                                            <?php
                                            $customername = ucwords($ndr->shipping_fname . ' ' . $ndr->shipping_lname);
                                            $customerphn = isset($ndr->shipping_phone) ? $ndr->shipping_phone : '';
                                            $customeradd1 = isset($ndr->shipping_address) ? $ndr->shipping_address : '';
                                            $customeradd2 = isset($ndr->shipping_address_2) ? $ndr->shipping_address_2 : '';
                                            $compltadd = $customeradd1 . ' ' . $customeradd2;
                                            $shippcity = $ndr->shipping_city;
                                            $shipstate = $ndr->shipping_state;
                                            ?>
                                            <span data-toggle="tooltip" data-html="true" title="<?= $customername . '<br>' . $customerphn . '<br>' . $compltadd . '<br>' . $shippcity . '<br>' . $shipstate; ?>">
                                                <?= mb_strimwidth($customername, 0, 14, "..."); ?><br />
                                            </span>
                                            <?= $customerphn; ?>
                                        </td>
                                        <td>
                                            <?= strtoupper($ndr->courier_name); ?><br />
                                            <a target="blank" class="text-info" href="awb/tracking/<?= $ndr->awb_number ?>"><?= ucwords($ndr->awb_number); ?></a>
                                            <br>
                                            <?= strtoupper($ndr->ship_status) ?> <?php if ($ndr->ship_status == 'rto' && !empty($ndr->rto_status)) {
                                                                                        echo strtoupper($ndr->rto_status);
                                        } ?></td>
                                        <td style="max-width:200px;">
                                            <span class="text-success"><?= $ndr->ndr_attempt; ?> Attempt(s)</span><br />
                                            <?php if ($ndr->ndr_action == 'ndr') { ?>
                                                <?= $ndr->ndr_remarks; ?><br />
                                            <?php } ?>
                                        </td>
                                        <td style="max-width:200px;">
                                            <button type="button" data-bs-toggle="modal" data-bs-target="#lgscrollmodal" class="btn btn-outline-primary btn-sm show_ndr_history" data-ndr-id="<?= $ndr->id; ?>">History</button>
                                            <?php if ($ndr->ndr_action == 'ndr' && !in_array($ndr->ship_status, array('rto', 'delivered'))) { ?>
                                                <button type="button" data-bs-toggle="modal" data-bs-target="#ndr_submit_model" class="btn btn-outline-primary btn-sm submit-ndr-response" data-ndr-id="<?= $ndr->id; ?>">Take Action</button>
                                            <?php } elseif ($ndr->ndr_action != 'ndr') { ?>
                                                <br><span class="text-success"><?= strtoupper($ndr->ndr_action) ?></span><br /> <?= $ndr->ndr_remarks; ?>
                                            <?php } else { ?>
                                                <?php echo '-'; ?>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <tr style="display:none;" class="ndr_history_tr ndr_history_tr_<?= $ndr->id ?>">
                                        <td colspan="13" class="ndr_history_td_<?= $ndr->id ?>"></td>
                                    </tr>
                                <?php
                                }
                            } else {
                                ?>
                                <tr>
                                    <td colspan="12" class="text-center">No Records Found</td>
                                </tr>
                            <?php
                            }
                            ?>

                        </tbody>
                    </table>
                    <!--Remark Popup start here-->
                    <div class="modal fade" id="ndr_submit_model" tabindex="-1" role="dialog" aria-labelledby="slideRightModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document" style="width: 415px !important;">
                            <div class="modal-content" style="height:auto;">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="slideRightModalLabel">Submit NDR</h5>
                                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form method="post" action="<?= base_url('ndr/action'); ?>" id="ndr_submit_form">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <input type="hidden" id="ndr_id" value="<?= isset($ndr_id) ? $ndr_id : '' ?>" name="ndr_id">
                                                        <select name="action" class="form-control ndr_action_change">
                                                            <option value="">Choose Action</option>
                                                            <option value="change address">Change Address</option>
                                                            <option value="change phone">Change Phone Number</option>
                                                            <option value="re-attempt">Re-Attempt</option>
                                                            <option value="rto">RTO</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group ndr_action_change_fields ndr_action_change_field_reattempt" style="display: none;">
                                                        <label>Re-Attempt Date</label>
                                                        <select class="form-control" name="re_attempt_date_pre">
                                                            <option value="">Choose Date</option>
                                                            <option value="<?= strtotime('+1 day 23:59:59'); ?>"><?= date('d M (D)', strtotime('+1 day')) ?></option>
                                                            <option value="<?= strtotime('+2 day 23:59:59'); ?>"><?= date('d M (D)', strtotime('+2 day')) ?></option>
                                                            <option value="<?= strtotime('+3 day 23:59:59'); ?>"><?= date('d M (D)', strtotime('+3 day')) ?></option>
                                                            <option value="<?= strtotime('+4 day 23:59:59'); ?>"><?= date('d M (D)', strtotime('+4 day')) ?></option>
                                                            <option value="<?= strtotime('+5 day 23:59:59'); ?>"><?= date('d M (D)', strtotime('+5 day')) ?></option>
                                                        </select>
                                                    </div>
                                                    <input type="hidden" value="<?= strtotime('+1 day 23:59:59'); ?>" name="re_attempt_date">


                                                    <div class="form-group ndr_action_change_fields ndr_action_change_field_customer_details" style="display: none;">
                                                        <label>Customer Name</label>
                                                        <input class="form-control" type="text" value="<?= isset($customer_details_name) ? $customer_details_name : '' ?>" name="customer_details_name">
                                                    </div>
                                                    <div class="form-group ndr_action_change_fields ndr_action_change_field_customer_details" style="display: none;">
                                                        <label>Customer Address 1</label>
                                                        <input class="form-control" type="text" value="<?= isset($customer_details_address_1) ? $customer_details_address_1 : '' ?>" name="customer_details_address_1">
                                                    </div>
                                                    <div class="form-group ndr_action_change_fields ndr_action_change_field_customer_details" style="display: none;">
                                                        <label>Customer Address 2</label>
                                                        <input class="form-control" type="text" value="<?= isset($customer_details_address_2) ? $customer_details_address_2 : '' ?>" name="customer_details_address_2">
                                                    </div>
                                                    <div class="form-group ndr_action_change_fields ndr_action_change_field_customer_contact" style="display: none;">
                                                        <label>Phone Number</label>
                                                        <input class="form-control" type="text" value="<?= isset($customer_contact_phone) ? $customer_contact_phone : '' ?>" name="customer_contact_phone">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Remark <small>(optional - Will not be shared with courier partner)</small></label>
                                                        <textarea class="form-control" name="remarks" placeholder="Enter Remark"><?= isset($remarks) ? htmlspecialchars($remarks) : '' ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary">Submit</button>
                                                    <button type="button" data-bs-dismiss="modal"  class="btn btn-secondary" data-dismiss="modal">
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
<div class="modal fade bd-modal-lg" id="lgscrollmodal" tabindex="-1" role="dialog" aria-labelledby="slideRightModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog modal-dialog-centered modal-lg" >
        <div class="modal-content" style="height:auto;">
            <div class="modal-header">
                <h5 class="modal-title" id="slideRightModalLabel">NDR History</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                        <div class="modal-content" id="show_information"></div>
                            <div class="modal-footer">
                                <button type="button" data-bs-dismiss="modal"  class="btn btn-sm btn-primary" data-dismiss="modal">
                                    OK
                                </button>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>

<form id="tab_filter" action="<?php base_url();?>" method="POST">
    <input type="hidden" name="perPage" id="perPage" />
</form>
<!-- SCROLLING WITH COTENT MODAL END -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  <?php unset($_POST['perPage']); ?>

  function per_page_records(per_page = false) {
    document.getElementById('perPage').value=per_page;
    document.getElementById('tab_filter').submit();
  }
  function filter_data_status(status = false) {
    document.getElementById('status').value=status;
    document.getElementById('form_filter').submit();
  }
  
    $('.ndr_action_change').on('change', function() {
        var ndr_action = $(this).val();
        $(".ndr_action_change_fields").hide();
        switch (ndr_action) {
             case 're-attempt':
                 $(".ndr_action_change_field_reattempt").show();
                 break;
            case 'change address':
                $(".ndr_action_change_field_customer_details").show();
                break;
            case 'change phone':
                $(".ndr_action_change_field_customer_contact").show();
                break;
            default:
        }
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
</script>

