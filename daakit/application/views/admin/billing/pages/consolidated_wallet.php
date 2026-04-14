<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">Consolidated Wallet Report</h4>
    <ol class="breadcrumb d-flex flex-wrap">
    </ol>
</div>
<!-- END PAGE-HEADER -->
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
                                    <li class="nav-item"><a class="nav-link"  href="<?php echo base_url('admin/billing/v/invoice');?>"><i class="fa fa-file-pdf-o" aria-hidden="true"></i>Billing Statement</a></li>
                                    <li class="nav-item"><a class="nav-link"  href="<?php echo base_url('admin/billing/v/credit_notes');?>"><i class="fa fa-file-pdf-o" aria-hidden="true"></i>Credit Memos</a></li>
                                <?php } ?>   
                                <?php if (in_array('billing_wallet_adjustment', $user_details->permissions)) { ?>
                                    <li class="nav-item"><a class="nav-link"  href="<?php echo base_url('admin/billing/v/wallet_adjustments');?>"><i class="fa fa-file-pdf-o" aria-hidden="true"></i>Wallet Adjustment</a></li>
                                <?php } ?>   
                                    <li class="nav-item"><a class="nav-link"  href="<?php echo base_url('admin/billing/v/price_calculator');?>"><i class="fa fa-calculator" aria-hidden="true"></i>Cost Estimator</a></li>
                                <?php if (in_array('billing_consolidated_wallet', $user_details->permissions)) { ?>
                                    <li class="nav-item"><a class="nav-link active"  href="<?php echo base_url('admin/billing/v/consolidated_wallet');?>"><i class="fa fa-file-pdf-o" aria-hidden="true"></i>Wallet Consolidated</a></li>
                                <?php } ?>
                            </ul>
                        </div>
                        <div class="flex-grow-1">
                            <div class="tab-content">
                                <div class="tab-pane active" id="general">
                                    <div class="p-4 border-bottom">
                                        <h5 class="mb-0">Consolidated Wallet</h5>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                        <div class="panel_s">
                                            <div class="panel-body">
                                            <form method="post" action="<?= current_url(); ?>">
                                                <div class="row" id="filter_row">
                                                <div class="col-md-3">
                                                    <div class="form-group" app-field-wrapper="from_date">
                                                        <label for="from_date" class="control-label">From Date</label>
                                                        <input type="date"  name="start_date" value="<?= !empty($filter['start_date']) ? $filter['start_date'] : '' ?>" class="form-control fc-datepicker"  autocomplete="off">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group" app-field-wrapper="to_date">
                                                        <label for="to_date" class="control-label">To Date</label>
                                                        <input type="date" id="to_date" name="end_date" class="form-control" value="<?= !empty($filter['end_date']) ? $filter['end_date'] : '' ?>" autocomplete="off">
                                                    </div>
                                                </div>
                                                    <div class="form-group col-sm-3">
                                                        <label for="email">Seller:</label>
                                                        <select name="seller_id" class="form-control getUserlist form-control-sm" style="width: 100% !important;">
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
                                                    <div class="form-group col-sm-3" style="margin-top:2px;">
                                                        <label for="email">AWB Number:</label>
                                                        <input type="text" name="awb_no" value="<?= !empty($filter['awb_no']) ? $filter['awb_no'] : '' ?>" class="form-control awb-number" placeholder="AWB Separated by comma" disabled>
                                                    </div>
                                                    <div class="form-group col-sm-2" style="margin-top:2px;">
                                                        <label for="email">TXN Type:</label>
                                                        <?php
                                                        $esc_status = array(
                                                            'shipment' => 'Shipment',
                                                            'recharge' => 'Recharge',
                                                            'neft' => 'NEFT',
                                                            'cod' => 'COD',
                                                            'withdraw' => 'Withdraw',
                                                            'promotion' => 'Promotion',
                                                            'addon' => 'Addon',
                                                            'others' => 'Others'
                                                        );
                                                        $js = "class='form-control js-select2' multiple style='width: 100% !important;' ";
                                                        echo form_dropdown('txn_type[]', $esc_status, !empty($filter['txn_type']) ? $filter['txn_type'] : '', $js);
                                                        ?>
                                                    </div>
                                                    <div class="form-group col-sm-3" style="margin-top:2px;">
                                                        <label for="email">TXN Sub Type:</label>
                                                        <?php
                                                        $esc_status = array(
                                                            'cod' => 'COD',
                                                            'damaged_cn' => 'Damaged CN',
                                                            'lost_cn' => 'Lost CN',
                                                            'extra_weight' => 'Extra Weight',
                                                            'rto_extra_weight' => 'RTO Extra Weight',
                                                            'freight' => 'Freight',
                                                            'rto_freight' => 'RTO Freight',
                                                            'refund' => 'Refund',
                                                            'insurance' => 'Insurance',
                                                            'ivr_number' => 'Ivr Number',
                                                            'ivr_call' => 'Ivr Call'
                                                        );
                                                        $js = "class='form-control js-select2' multiple style='width: 100% !important;' ";
                                                        echo form_dropdown('txn_ref_type[]', $esc_status, !empty($filter['txn_ref_type']) ? $filter['txn_ref_type'] : '', $js);
                                                        ?>
                                                    </div>
                                                    <div class="form-group col-sm-3">
                                                        <label for="email">Report Type:</label>
                                                        <select name="report_type" onchange="disableFilter(this.value);" class="form-control report-type" style="width: 100% !important;">
                                                            <option value="consolidated">Consolidated</option>
                                                            <option value="detailed">Detailed</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-2" style="margin-top:32px;">
                                                        <button type="submit" class="btn btn-sm btn-outline-success">Export</button>
                                                        <a href="<?= base_url('admin/billing/v/consolidated_wallet'); ?>" class="btn btn-sm btn-outline-primary">Clear</a>
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
        </div>
    </div>
    <!-- END ROW-1 -->
</div>
<script>
    function disableFilter(val) {
        if (val == 'detailed') {

            $('.order-type').prop('disabled', false);
            $('.awb-number').prop('disabled', false);
        } else if (val == 'consolidated') {
            $('.order-type').prop('disabled', true);
            $('.awb-number').prop('disabled', true);
        }
    }
</script>