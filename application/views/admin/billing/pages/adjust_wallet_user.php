<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">Wallet Adjustment</h4>
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
                                <li class="nav-item"><a class="nav-link active"  href="<?php echo base_url('admin/billing/v/wallet_adjustments');?>"><i class="fa fa-file-pdf-o" aria-hidden="true"></i>Wallet Adjustment</a></li>
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
                                        <h5 class="mb-0">Wallet Adjustments</h5>
                                    </div>
                                    <div class="card-body">
                                    <?php if (in_array('billing_wallet_adjustment', $user_details->permissions)) { ?>
                                        <div class="row m-t-30">
                                            <div class="col-sm-6">
                                                <form method="post" action="<?= current_url(); ?>">
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <label>Seller</label>
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
                                                            <div class="form-group">
                                                                <label>Adjustment For</label>
                                                                <select class="form-control" name="txn_for" onchange="check_ref(this.value)" required>
                                                                    <?php
                                                                    $txn_for = '';
                                                                    if (!empty(set_value('txn_for')))
                                                                        $txn_for = set_value('txn_for');
                                                                    ?>
                                                                    <option <?php if ($txn_for == '') { ?> selected="" <?php } ?> value="">Select</option>
                                                                    <option <?php if ($txn_for == 'cod') { ?> selected="" <?php } ?> value="cod">COD Adjustments</option>
                                                                    <option <?php if ($txn_for == 'recharge') { ?> selected="" <?php } ?> value="recharge">Recharge -Gateway</option>
                                                                    <option <?php if ($txn_for == 'neft') { ?> selected="" <?php } ?> value="neft">Recharge - NEFT</option>
                                                                    <?php if (in_array('billing_wallet_shipment', $user_details->permissions)) { ?>
                                                                        <option <?php if ($txn_for == 'shipment') { ?> selected="" <?php } ?> value="shipment">Shipment</option>
                                                                    <?php } ?>
                                                                    <option <?php if ($txn_for == 'lost') { ?> selected="" <?php } ?> value="lost">lost</option>
                                                                    <option <?php if ($txn_for == 'damaged') { ?> selected="" <?php } ?> value="damaged">Damaged</option>
                                                                    <option <?php if ($txn_for == 'promotion') { ?> selected="" <?php } ?> value="promotion">promotion</option>
                                                                    <option <?php if ($txn_for == 'wallet_to_wallet_transfer') { ?> selected="" <?php } ?> value="wallet_to_wallet_transfer">Wallet to wallet transfer</option>
                                                                    <option <?php if ($txn_for == 'tds_refund') { ?> selected="" <?php } ?> value="tds_refund">TDS refund</option>
                                                                    <option <?php if ($txn_for == 'customer_refund') { ?> selected="" <?php } ?> value="customer_refund">Customer Refund</option>

                                                                    <option <?php if ($txn_for == 'others') { ?> selected="" <?php } ?> value="others">Others</option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Adjustment Type</label>
                                                                <select class="form-control" name="txn_type" required>
                                                                    <?php
                                                                    $txn_type = '';
                                                                    if (!empty(set_value('txn_type')))
                                                                        $txn_type = set_value('txn_type');
                                                                    ?>
                                                                    <option <?php if ($txn_type == '') { ?> selected="" <?php } ?> value="">Select</option>
                                                                    <option <?php if ($txn_type == 'credit') { ?> selected="" <?php } ?> value="credit">Credit</option>
                                                                    <option <?php if ($txn_type == 'debit') { ?> selected="" <?php } ?> value="debit">Debit</option>

                                                                </select>
                                                            </div>
                                                            <div class="form-group" style="display:none;" id="txn_ref_id">
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Ref No</label>
                                                                <input type="text" name="ref_id" value="<?= set_value('ref_id') ?>" class="form-control" placeholder="">
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Amount</label>
                                                                <input type="text" required="" name="amount" value="<?= set_value('amount') ?>" class="form-control" placeholder="">

                                                            </div>
                                                            <div class="form-group">
                                                                <label>Notes</label>
                                                                <input type="text" required="" name="notes" value="<?= set_value('notes') ?>" class="form-control" placeholder="">
                                                            </div>
                                                            <div class="form-group" style="text-align:right">
                                                                <button type="submit" style="margin-top: 20px;" class="btn btn-primary">Submit</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    <?php } ?>                                
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
    function check_ref(txn_for) {
        //alert(txn_for);
        if (txn_for == 'shipment') {
            $("#txn_ref_id").css("display", "block");
            $("#recharge_type").prop("required",false);
           // $("#recharge_type_section").css("display", "none"); 
            $("#txn_ref_id").html('<label>Txn Ref</label><select class="form-control" name="txn_ref" required>' + '<option selected = "" value = "" > Select </option>' +
                '<option value = "freight" > Freight </option>' +
                '<option value = "cod" > Cod </option>' +
                '<option value = "rto_freight" > Rto Freight </option>' +
                '<option value = "extra_weight" > Extra Weight </option>' +
                '<option value = "rto_extra_weight" > Rto Extra Weight </option>' +
                '</select>');
        } else if (txn_for == 'recharge'){
          // $("#recharge_type_section").css("display", "block");
           $("#recharge_type").prop("required",true);
        } else {
            $("#txn_ref_id").css("display", "none");
            $("#recharge_type").prop("required",false);
            //$("#recharge_type_section").css("display", "none"); 
            $("#txn_ref_id").html('');
        }
    }
</script>  