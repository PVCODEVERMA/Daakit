<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">Billing Statement</h4>
    <ol class="breadcrumb d-flex flex-wrap">
        <li class="breadcrumb-item btn-list">
            <a href="<?= base_url('admin/billing/exportInvoice'); ?><?php if (!empty($filter)) {
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
                            <form method="post" action="<?= base_url('admin/billing/v/invoice'); ?>">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4 class="mtop10">Transaction Filters</h4>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group no-mbot" app-field-wrapper="Channel[]">
                                            <label for="Channel[]" class="control-label">Seller</label>
                                            <select name="filter[seller_id]" class="form-control-sm" style="width: 100% !important;">
                                                <option value="">Select Seller</option>
                                                <?php
                                                if (!empty($users)) {
                                                    foreach ($users as $values) {
                                                        $sellerid = '';
                                                        // if (!empty($filter['seller_id']))
                                                        //     $sellerid = $filter['seller_id'];
                                                ?>
                                                        <option <?php if ($sellerid == $values->id) { ?> selected <?php } ?> value="<?php echo $values->id; ?>"><?php echo $values->id . ' - ' . ucwords($values->user_fname . ' ' . $values->user_lname); ?> (<?php echo ucwords($values->company_name) ?>)</option>
                                                <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                            </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group no-mbot" app-field-wrapper="Channel[]">
                                            <label for="Month[]" class="control-label">Month</label>
                                                <select name="filter[month]" class="form-control " style="width: 100% !important;">
                                                    <option value="">Select Month</option>
                                                    <?php
                                                    foreach ($months as $month) {
                                                        $selected_month = '';
                                                        if (!empty($filter['month']))
                                                            $selected_month = $filter['month'];
                                                    ?>
                                                        <option <?php if ($selected_month == $month->month) { ?> selected <?php } ?> value="<?php echo $month->month; ?>"><?php echo ucwords($month->month); ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group no-mbot" app-field-wrapper="Service[]">
                                            <label for="Service[]" class="control-label">Service Type</label>
                                                <select name="filter[service_type]" class="form-control " style="width: 100% !important;">
                                                    <option value="">Select Service Type</option>
                                                    <?php
                                                        $service_type ='';
                                                        if (!empty($filter['service_type']))
                                                            $service_type = $filter['service_type'];
                                                    ?>
                                                        <option <?php if ($service_type == 'shipment') { ?> selected <?php } ?> value="shipment">Shipment</option>
                                                        <!-- <option <?php if ($service_type == 'insurance') { ?> selected <?php } ?> value="insurance">Insurance</option> -->
                                                        <option <?php if ($service_type == 'addon') { ?> selected <?php } ?> value="addon">Addon Services</option>
                                                </select>
                                            </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group no-mbot" app-field-wrapper="invoice_no[]">
                                            <label for="invoice_no[]" class="control-label">Invoice No(s)</label>
                                                <input type="text"  name="filter[invoice_no]" value="<?= !empty($filter['invoice_no']) ? $filter['invoice_no'] : '' ?>" class="form-control" placeholder="Invoice No(s) separated by comma"  autocomplete="off">
                                            </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6" style="margin-top:20px;">
                                        <button type="submit" class="btn btn-sm btn-success">Filter</button>
                                        <a href="<?= base_url('admin/billing/v/invoice'); ?>" class="btn btn-primary btn-sm">Reset</a>                                
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
                                <li class="nav-item"><a class="nav-link"  href="<?php echo base_url('admin/billing/v/shipping_charges');?>"><i class="fa fa-truck" aria-hidden="true"></i>Shipping Cost</a></li>
                            <?php } ?>    
                            <?php if (in_array('billing_invoices', $user_details->permissions)) { ?>
                                <li class="nav-item"><a class="nav-link active"  href="<?php echo base_url('admin/billing/v/invoice');?>"><i class="fa fa-file-pdf-o" aria-hidden="true"></i>Billing Statement</a></li>
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
                                        <h5 class="mb-0">Invoice</h5>
                                        <h5 class="mb-0" style="float: right;margin-top: -24px;">
                                        <?php if (in_array('billing_generate_invoice', $user_details->permissions)) { ?>
                                            <?php echo form_open(current_url(), [
                                                    'onsubmit' => "return confirm('This will generate invoices for last month\'s shipments. Are you sure you want to proceed?')"
                                                ]); ?>
                                                    <button type="submit" name="generate" value="invoice" class="btn btn-info btn-sm me-2">
                                                        <i class="icon-placeholder mdi mdi-file-pdf"></i> Generate Invoice
                                                    </button>
                                                <?php echo form_close(); ?>
                                        <?php } ?>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th><span class="bold">Seller Name</span></th>
                                                        <th><span class="bold">Invoice No.</span></th>
                                                        <th><span class="bold">Invoice Date</span></th>
                                                        <th><span class="bold">Invoice Period</span></th>
                                                        <th><span class="bold">Service Type</span></th>
                                                        <th><span class="bold">Invoice Amount(&#8377;)</span></th>
                                                        <th><span class="bold">Action</span></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if (!empty($invoices)) {
                                                        foreach ($invoices as $invoice) {
                                                    ?>
                                                            <tr>
                                                                <td><?= $invoice->company_name; ?></td>
                                                                <td><?= (!empty($invoice->invoice_no)) ? $invoice->invoice_no : 'DKT/' . sprintf('%03d', $invoice->id); ?></td>
                                                                <td><?= date('d-m-Y', $invoice->created); ?></td>
                                                                <td><?= ucwords($invoice->month); ?></td>
                                                                <td><?= ucfirst($invoice->service_type) ?></td>
                                                                <td>&#8377;<?= $invoice->total_amount; ?></td>
                                                                <td>
                                                                    <a target="_blank" href="<?php echo base_url('download/force');?>?type=invoice&file=<?= $invoice->csv_file ?>" class="btn btn-sm btn-outline-primary"><i class="fa fa-file-excel-o" aria-hidden="true"></i></a>
                                                                    <a target="_blank" href="<?php echo base_url('download/force');?>?type=invoice&file=<?= $invoice->pdf_file ?>" class="btn btn-sm btn-outline-danger"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>
                                                                </td>
                                                            </tr>
                                                        <?php
                                                        }
                                                    } else {
                                                        ?>
                                                        <tr>
                                                            <td colspan="6" class="text-center">No entries found</td>
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
<form id="tab_filter" action="<?php base_url('billing/version/seller_recharge_logs');?>" method="POST">
    <input type="hidden" name="perPage" id="perPage" />
</form>

