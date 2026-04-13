<?php if (in_array('billing_recharge_logs', $user_details->permissions)) { ?>
<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">Transactions</h4>
    <ol class="breadcrumb d-flex flex-wrap">
        <li class="breadcrumb-item btn-list">
                <a href="javascript:void(0);" 
                id="retrieveExportBtn" 
                class="btn btn-info btn-sm me-2">Retrieve Export</a>
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
                            <form method="post" action="<?= base_url('admin/billing/v/recharge_logs'); ?>">
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
                                            <label for="Channel[]" class="control-label">Channel</label>
                                            <select class="form-control form-control-sm" name="filter[txn_for]" id="filter_wallet_txn">
                                                <?php
                                                $txn_for = '';
                                                if (!empty($filter['txn_for']))
                                                    $txn_for = $filter['txn_for'];
                                                ?>
                                                <option <?php if ($txn_for == '') { ?> selected="" <?php } ?> value="">Show All</option>
                                                <option <?php if ($txn_for == 'cod') { ?> selected="" <?php } ?> value="cod">COD Adjustments</option>
                                                <option <?php if ($txn_for == 'recharge') { ?> selected="" <?php } ?> value="recharge">Recharge - Gateway</option>
                                                <option <?php if ($txn_for == 'recharge_paytm') { ?> selected="" <?php } ?> value="recharge_paytm">Recharge - PayTM</option>
                                                <option <?php if ($txn_for == 'recharge_razorpay') { ?> selected="" <?php } ?> value="recharge_razorpay">Recharge - Razorpay</option>
                                                <option <?php if ($txn_for == 'recharge_hdfc') { ?> selected="" <?php } ?> value="recharge_hdfc">Recharge - Hdfc</option>
                                                <option <?php if ($txn_for == 'neft') { ?> selected="" <?php } ?> value="neft">Recharge - NEFT</option>
                                                <option <?php if ($txn_for == 'shipment') { ?> selected="" <?php } ?> value="shipment">Shipments</option>
                                                <option <?php if ($txn_for == 'shipment_refund') { ?> selected="" <?php } ?> value="shipment_refund">Shipments - Refunds</option>
                                               
                                                 <option <?php if ($txn_for == 'whatsapp') { ?> selected="" <?php } ?> value="whatsapp">Whatsapp</option>
                                                <option <?php if ($txn_for == 'sms') { ?> selected="" <?php } ?> value="sms">SMS</option>
                                                <option <?php if ($txn_for == 'email') { ?> selected="" <?php } ?> value="email">Email</option>
                                                <option <?php if ($txn_for == 'all_communication') { ?> selected="" <?php } ?> value="all_communication">All Communications</option>
                                                <option <?php if ($txn_for == 'ivr') { ?> selected="" <?php } ?> value="ivr">IVR Calls</option>
                                                <option <?php if ($txn_for == 'promotion') { ?> selected="" <?php } ?> value="promotion">Promotion</option>
                                    
                                                <option <?php if ($txn_for == 'lost') { ?> selected="" <?php } ?> value="lost">lost</option>
                                                <option <?php if ($txn_for == 'damaged') { ?> selected="" <?php } ?> value="damaged">Damaged</option>
                                                <option <?php if ($txn_for == 'promotion') { ?> selected="" <?php } ?> value="promotion">promotion</option>
                                                <option <?php if ($txn_for == 'wallet_to_wallet_transfer') { ?> selected="" <?php } ?> value="wallet_to_wallet_transfer">Wallet to wallet transfer</option>
                                                <option <?php if ($txn_for == 'tds_refund') { ?> selected="" <?php } ?> value="tds_refund">TDS refund</option>
                                                <option <?php if ($txn_for == 'customer_refund') { ?> selected="" <?php } ?> value="Ccustomer_refund">Customer Refund</option>
                                                <option <?php if ($txn_for == 'others') { ?> selected="" <?php } ?> value="others">Others</option>
                                            </select>
                                            </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group no-mbot" app-field-wrapper="Channel[]">
                                            <label for="Channel[]" class="control-label">Seller</label>
                                            <select name="filter[seller_id]" class="form-control" style="width: 100% !important;">
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
                                        <a href="<?= base_url('admin/billing/v/recharge_logs'); ?>" class="btn btn-primary btn-sm">Reset</a>                                
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
                                <li class="nav-item"><a class="nav-link  active"  href="<?php echo base_url('admin/billing');?>"><i class="fa fa-inr" aria-hidden="true"></i>Transaction History</a></li>
                            <?php } ?>    
                            <?php if (in_array('billing_shipping_charges', $user_details->permissions)) { ?>
                                <li class="nav-item"><a class="nav-link"  href="<?php echo base_url('admin/billing/v/shipping_charges');?>"><i class="fa fa-truck" aria-hidden="true"></i>Shipping Cost</a></li>
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
                                        <h5 class="mb-0">Transaction history <span style="font-size:13px">(<?= date('d-m-Y',strtotime($filter['start_date']));?> - <?= date('d-m-Y',strtotime($filter['end_date']));?>)</span></h5>
                                    </div>
                                    <div class="card-body" style="margin-top: -22px;">
                                        <div class="table-responsive">
                                            <table class="table table-bordered" data-order-col="2" data-order-type="desc">                    
                                                <thead>
                                                    <tr>
                                                        <th><span class="bold">Transaction Details</span></th>
                                                        <th><span class="bold">Reference No.</span></th>
                                                        <th><span class="bold">Credit(&#8377;)</span></th>
                                                        <th><span class="bold">Debit(&#8377;)</span></th>
                                                        <th><span class="bold">Closing Balance(&#8377;)</span></th>
                                                        <th><span class="bold">Remark</span></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        if (!empty($history)) {
                                                            $i = 1;
                                                            foreach ($history as $his) {
                                                        ?>
                                                                <tr>
                                                                    <td>TID : <?= $his->id; ?><br>
                                                                    <?= (!empty($his->created)) ? date('M d, Y H:i', $his->created) : ''; ?><br>
                                                                    TTYPE : <?php
                                                                        switch ($his->txn_for) {
                                                                            case 'shipment':
                                                                                echo 'Shipping';
                                                                                break;
                                                                            case 'credits':
                                                                                echo 'Credit Note';
                                                                                break;
                                                                            case 'cod':
                                                                                echo 'COD Adjustments';
                                                                                break;
                                                                            case 'neft':
                                                                                echo 'Recharge - NEFT';
                                                                                break;
                                                                            case 'recharge':
                                                                                echo 'Recharge - Gateway';
                                                                                break;
                                                                            case 'promotion':
                                                                                echo 'Promotion';
                                                                                break;
                                                                            case 'ivr_number':
                                                                                echo 'IVR number';
                                                                                break;
                                                                            case 'ivr_call':
                                                                                echo 'IVR Call';
                                                                                break;
                                                                            case 'whatsapp':
                                                                                echo 'Whatsapp';
                                                                                break;
                                                                            case 'lost':
                                                                                echo 'lost';
                                                                                break;
                                                                            case 'damaged':
                                                                                echo 'damaged';
                                                                                break;
                                                                            case 'promotion':
                                                                                echo 'promotion';
                                                                                break;
                                                                            case 'wallet_to_wallet_transfer':
                                                                                echo 'Wallet to wallet transfer';
                                                                                break;
                                                                            case 'tds_refund':
                                                                                echo 'Tds refund';
                                                                                break; 
                                                                            case 'customer_refund':
                                                                                echo 'Customer refund';
                                                                                break; 
                                                                            case 'others':
                                                                                echo 'Others';
                                                                                break;
                                                                            case 'sms':
                                                                                echo 'SMS';
                                                                                break;                
                                                                            case 'email':
                                                                                echo 'EMail';
                                                                                break;                        
                                                                            default:
                                                                                echo '-';
                                                                        }
                                                                        ?>
                                                                        <br>SELLER : <?= $his->fname . ' ' . $his->lname; ?>
                                                                        <br>COMPANY : <a target="_blank;" href="<?php echo base_url('admin/users/viewuser/'); ?><?php echo $his->userid; ?>" style="color: #004080;font-weight:bold;"><?= $his->company_name; ?></a>
                                                                    </td>
                                                                        <td>
                                                                        <?php
                                                                        if ($his->txn_for == 'shipment') {
                                                                        ?>
                                                                            <a target="blank" class="text-info" href="admin/billing/v/shipping_charges?filter[awb_no]=<?= $his->awb_number; ?>"><?= $his->awb_number; ?></a>
                                                                        <?php
                                                                        }else if($his->txn_for == 'whatsapp' || $his->txn_for == 'email' || $his->txn_for == 'sms'){
                                                                            echo $his->ref_id;
                                                                        } 
                                                                        else {
                                                                            echo '-';
                                                                        }
                                                                        ?>
                                                                    </td>
                                                                    <td><?= ($his->type == 'credit') ? round($his->amount, 2) : '-' ?></td>
                                                                    <td><?= ($his->type == 'debit') ? round($his->amount, 2) : '-' ?></td>
                                                                    <td><?= round($his->balance_after, 2) ?></td>
                                                                    <td><?= ucwords($his->notes); ?></td>

                                                                </tr>
                                                            <?php
                                                                $i++;
                                                            }
                                                        } else {
                                                            ?>
                                                            <tr>
                                                                <td colspan="8" class="text-center">No Records Found</td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                            </table>
                                            <div class="col-sm-12 col-md-12"></div>
                                            <div class="col-sm-12 col-md-12">
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
            </div>
        </div>
    </div>
    <!-- END ROW-1 -->
</div>
<form id="tab_filter" action="<?php base_url('billing/version/seller_recharge_logs');?>" method="POST">
    <input type="hidden" name="perPage" id="perPage" />
</form>
<!-- SCROLLING WITH COTENT MODAL END -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function per_page_records(per_page = false) {
        document.getElementById('perPage').value=per_page;
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
</script> 

<script>
$(document).ready(function() {
    $('#retrieveExportBtn').on('click', function() {
        // Get the selected channel from the filter dropdown
        let selectedChannel = $('select[name="filter[txn_for]"]').val();

        // Build the query string from the form data
        let formData = $('form').serialize();
        

        // Define the alternate backend
        let specialChannels = ['sms', 'whatsapp', 'email', 'ivr_call', 'ivr_number', 'all_communication'];

        let url;
        if (specialChannels.includes(selectedChannel)) {
            // Call the xyz.com backend
            url = "<?= base_url('admin/billing/communications_recharge_logs_export'); ?>?"+formData;
        } else {
            // Call the original backend
            url = "<?= base_url('admin/billing/recharge_logsexportCSV'); ?>?"+formData;
        }

        // Redirect (or open in new tab if you prefer)
        window.location.href = url;
    });
});
</script>
<?php } ?>
