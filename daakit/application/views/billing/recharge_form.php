<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">Wallet recharge</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item">						
		</li>
    </ol>
</div>
<!-- END PAGE-HEADER -->
<div class="main-container container-fluid">

<!-- START ROW-1 -->
<form method="post" action="<?= current_url(); ?>">
    <div class="row">
        <div class="col-lg-6 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Enter recharge amount</h3>
                </div>
                <div class="card-body">
                    <div class="col-sm-12 text-left">
                        <strong style="color:red"><i class="fa fa-bell" aria-hidden="true"></i> Note</strong>
                        <p>* Min value:₹200 & Max value: ₹50,00,000</p>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row" id="data_amount">
                            <label>Amount</label>
                            <div class="col-sm-6">
                                <input type="text" autocomplete="off" name="filter[order_ids]" class="form-control" required="" id="recharge_wallet_amount" placeholder="Enter Amount" value="200">
                            </div>
                            <div class="col-sm-6 text-center" style=" float: right;margin-top: -65px;width: 17%;margin-right: 189px;">
                                <label>Instant&nbsp;Selection</label>
                                <select class="form-control"  id="recharge_amount" style="width: 100% !important;">
                                    <option value="200">200</option>
                                    <option value="500">500</option>
                                    <option value="1000">1000</option>
                                    <option value="1500">1500</option>
                                    <option value="2000">2000</option>
                                    <option value="5000">5000</option>
                                    <option value="10000">10000</option>
                                    <option value="15000">15000</option>
                                </select>

                            </div>
                        </div>
                        <div class="row">
                            <label>Apply Coupon</label>
                            <div class="col-sm-6">
                                <input type="hidden" autocomplete="off" name="filter[coupon_code_apply]" class="form-control" id="coupon_code_apply" value="">
                                <input type="text" autocomplete="off" name="filter[coupon_code]" class="form-control" id="coupon_code" placeholder="Enter Code" value="">
                                <b> <span id="message"></span></b>
                                <p></p>
                                <b>
                                    <p id="message_description"></p>
                                </b>
                            </div>
                            <div class="col-sm-4">
                                <button type="button" id="apply" class="btn btn-success btn-sm coupon_apply_button">Apply</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                            </div>
                        </div>
                        <br>
                        <div class="row">
                        <label class="card-title">Payment Gateway</label>
                            <div class="col-sm-4 text-center">
                                <label>
                                    <input type="radio" checked id="recharge_option" name="recharge_option" onchange="change_recharge_option()" class="easebuzz" value="easebuzz">
                                    <img src="<?php echo base_url('assets/images/easebuzz-logo.png');?>" class="m-b-15 mr-2 vvvvvvv" style="width:100px; border-color:#0c9;">
                                    <p>
                                        <small>UPI / Net Banking <br> Credit &amp; Debit Cards</small>
                                    </p>
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 text-right">
                                <button type="button" class="btn btn-primary recharge_wallet_button">Recharge</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- END ROW-1 -->
</div>
<script src="https://ebz-static.s3.ap-south-1.amazonaws.com/easecheckout/v2.0.0/easebuzz-checkout-v2.min.js"></script> 
