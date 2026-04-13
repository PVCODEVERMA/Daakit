<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">Shipping Cost</h4>
    <ol class="breadcrumb d-flex flex-wrap">
        <li class="breadcrumb-item btn-list">
            <a href="<?= base_url('admin/billing/shippingChargesExport'); ?><?php if (!empty($filter)) {
                    echo "?" . http_build_query($_POST);
                } ?>" class="btn btn-info btn-sm me-2"> Retrieve Export </a>
            <a href="javascript:void(0);" class="btn btn-info btn-sm me-2" data-bs-toggle="sidebar-right" data-bs-target=".sidebar-right"> Filter </a>
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
                            <form method="post" action="<?= base_url('admin/billing/v/shipping_charges'); ?>">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4 class="mtop10">Transaction Filters</h4>
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
                                    <div class="col-md-12">
                                        <div class="form-group no-mbot" app-field-wrapper="Channel[]">
                                            <label for="Channel[]" class="control-label">AWB No(s)</label>
                                                <input type="text" autocomplete="off" name="filter[awb_no]" value="<?= !empty($filter['awb_no']) ? $filter['awb_no'] : '' ?>" class="form-control form-control-sm" placeholder="AWB Separated by comma">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group no-mbot" app-field-wrapper="Channel[]">
                                            <label for="Channel[]" class="control-label">Seller</label>
                                            <select name="filter[seller_id]" class="form-control getUserlist form-control-sm" style="width: 100% !important;">
                                                <option value="">Select Seller</option>
                                                <?php
                                                if (!empty($users)) {
                                                    foreach ($users as $values) {
                                                        $sellerid = '';
                                                        if (!empty($filter['seller_id']))
                                                            $sellerid = $filter['seller_id'];
                                                ?>
                                                        <option <?php if ($sellerid == $values->id) { ?> selected <?php } ?> value="<?php echo $values->id; ?>"><?php echo $values->id . ' - ' . ucwords($values->user_fname . ' ' . $values->user_lname); ?> (<?php echo ucwords($values->company_name) ?>)</option>
                                                <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                            </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6" style="margin-top:20px;">
                                        <button type="submit" class="btn btn-sm btn-success">Filter</button>
                                        <a href="<?= base_url('admin/billing/v/shipping_charges'); ?>" class="btn btn-primary btn-sm">Reset</a>                                
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
        <div class="col-md-12">
            <div class="card overflow-hidden">
                <div class="card-body p-0">
                    <div class="d-lg-flex">
                        <div class="border-end border-bottom bd-lg-b-0 d-flex flex-column mn-wd-20p">
                            <ul class="nav nav-pills main-nav-column p-3">
                                <?php if (in_array('billing_recharge_logs', $user_details->permissions)) { ?>
                                    <li class="nav-item"><a class="nav-link"  href="<?php echo base_url('admin/billing');?>"><i class="fa fa-inr" aria-hidden="true"></i>Transaction History</a></li>
                                <?php } ?>    
                                <?php if (in_array('billing_shipping_charges', $user_details->permissions)) { ?>
                                    <li class="nav-item"><a class="nav-link active"  href="<?php echo base_url('admin/billing/v/shipping_charges');?>"><i class="fa fa-truck" aria-hidden="true"></i>Shipping Cost</a></li>
                                <?php } ?>    
                                <?php if (in_array('billing_invoices', $user_details->permissions)) { ?>
                                    <li class="nav-item"><a class="nav-link"  href="<?php echo base_url('admin/billing/v/invoice');?>"><i class="fa fa-file-pdf-o" aria-hidden="true"></i>Billing Statement</a></li>
                                    <li class="nav-item"><a class="nav-link"  href="<?php echo base_url('admin/billing/v/credit_notes');?>"><i class="fa fa-file-pdf-o" aria-hidden="true"></i>Credit Memos</a></li>
                                <?php } ?>   
                                <?php if (in_array('billing_wallet_adjustment', $user_details->permissions)) { ?>
                                    <li class="nav-item"><a class="nav-link"  href="<?php echo base_url('admin/billing/v/wallet_adjustments');?>"><i class="fa fa-file-pdf-o" aria-hidden="true"></i>Wallet Adjustment</a></li>
                                <?php } ?>   
                                    <li class="nav-item"><a class="nav-link"  href="<?php echo base_url('admin/billing/v/price_calculator');?>"><i class="fa fa-calculator" aria-hidden="true"></i>Cost Estimator</a></li>
                                <?php if (in_array('billing_consolidated_wallet', $user_details->permissions)) { ?>
                                    <li class="nav-item"><a class="nav-link"  href="<?php echo base_url('admin/billing/v/consolidated_wallet');?>"><i class="fa fa-file-pdf-o" aria-hidden="true"></i>Wallet Consolidated</a></li>
                                <?php } ?>
                            </ul>
                        </div>
                        <div class="flex-grow-1">
                            <div class="tab-content">
                                <div class="tab-pane active" id="general">
                                    <div class="p-4 border-bottom">
                                        <h5 class="mb-0">Charges</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered" data-order-col="2" data-order-type="desc">                    
                                                <thead>
                                                    <tr>
                                                    <th><span class="bold">Shipment&nbsp;Details</span></th>
                                                        <th><span class="bold">Weight&nbsp;Details(kg)</span></th>
                                                        <th><span class="bold">Charges(&#8377;)</span></th>
                                                        <th><span class="bold">Total Charges</span></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                                    if (!empty($history)) {
                                                        $i = 1;
                                                        foreach ($history as $his) {
                                                            ?>
                                                            <tr>
                                                                <td>
                                                                    <?= (!empty($his->shipping_created)) ? date('M d, Y', $his->shipping_created) : ''; ?>
                                                                    <br><?= ucwords($his->company_name); ?>
                                                                    <br><?= ucwords($his->courier_name); ?>
                                                                    <br><?= strtoupper($his->ship_status); ?>
                                                                    <br><a href="javascript:void(0)" class="text-info" onclick="get_awb_records('<?= $his->awb_number; ?>')"><?= $his->awb_number; ?></a>
                                                                </td>
                                                                <td>
                                                                    Entered : <?= !empty($his->package_weight) ? round($his->package_weight / 1000, 1) : '0.5'; ?>
                                                                    <br>Applied : <?= ($his->charged_weight > $his->package_weight) ? round($his->charged_weight / 1000, 1) : '-'; ?>
                                                                </td>

                                                                <td>
                                                                    Freight : <?= (strtoupper($his->ship_status)=='CANCELLED') ?  "0" : $his->courier_fees; ?>
                                                                    , COD : <?= (strtoupper($his->ship_status)=='CANCELLED') ?  "0" : $his->cod_fees; ?>
                                                                    , Extra Wgt : <?= ($his->extra_weight_charges > 0) ? round($his->extra_weight_charges, 2) : '-'; ?>
                                                                    , RTO : <?= ($his->rto_charges > 0) ? round($his->rto_charges, 2) : '-'; ?>
                                                                    , COD Reversed : <?= ($his->cod_reverse_amount > 0) ? '-' . round($his->cod_reverse_amount, 2) : '-'; ?>
                                                                    , RTO Extra Wgt : <?= ($his->rto_extra_weight_charges > 0) ? round($his->rto_extra_weight_charges, 2) : '-'; ?>
                                                                    <!-- , Shipment Insurance : <?= ($his->insurance_price > 0) ? round($his->insurance_price, 2) : '-'; ?> -->
                                                                </td>
                                                                <td><?php
                                                                    $total = (strtoupper($his->ship_status)=='CANCELLED') ?  "0" : (($his->courier_fees > 0) ? round($his->courier_fees, 2) : '0') + (($his->cod_fees > 0) ? round($his->cod_fees, 2) : '0') +  (($his->insurance_price > 0) ? round($his->insurance_price, 2) : '0') + (($his->extra_weight_charges > 0) ? round($his->extra_weight_charges, 2) : '0') + (($his->rto_charges > 0) ? round($his->rto_charges, 2) : '0') + (($his->rto_extra_weight_charges > 0) ? round($his->rto_extra_weight_charges, 2) : '0') - (($his->cod_reverse_amount > 0) ? round($his->cod_reverse_amount, 2) : '0');
                                                                    echo round($total, 2);
                                                                    ?></td>
                                                            </tr>
                                                            <?php
                                                            $i++;
                                                        }
                                                    } else {
                                                        ?>
                                                        <tr>
                                                            <td colspan="14" class="text-center">No Records Found</td>
                                                        </tr>
                                                    <?php } ?>
                                                    </tbody>
                                            </table>
                                        </div>
                                        <div class="row">
                                        <div class="col-md-4">
                                            <div class="dataTables_info" id="example-multi_info" role="status" aria-live="polite">Showing <?= $offset + 1; ?> to <?= $offset + $limit; ?> of <?= $total_records; ?> entries</div>
                                        </div>
                                        </div>
                                        <div class="row">
                                        <div id="colvis"></div>
                                        <div id="" class="dt-page-jump"></div>
                                        <div class="dataTables_paginate paging_simple_numbers" id="DataTables_Table_0_paginate">
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
            </div>
        </div>
    </div>
    <!-- END ROW-1 -->
</div>
<form id="tab_filter" action="<?php echo base_url('admin/shipping/list');?>"  method="POST"></form>
<!-- SCROLLING WITH COTENT MODAL END -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function get_awb_records(awb_no = false) {
        // Create a new form element
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = document.getElementById('tab_filter').action; // Use the original form's action
        form.target = '_blank'; // Open in a new tab
        document.body.appendChild(form);
        // Create a hidden input for the AWB number
        const awbInput = document.createElement('input');
        awbInput.type = 'hidden';
        awbInput.name = 'filter[awb_no]'; // Ensure this matches your form's input name
        awbInput.value = awb_no;
        // Append the AWB input to the new form
        form.appendChild(awbInput);
        form.submit();
    }
</script>

