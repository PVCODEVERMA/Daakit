<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">Shipping cost estimator</h4>
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
                                    <li class="nav-item"><a class="nav-link active"  href="<?php echo base_url('admin/billing/v/price_calculator');?>"><i class="fa fa-calculator" aria-hidden="true"></i>Cost Estimator</a></li>
                                <?php if (in_array('billing_consolidated_wallet', $user_details->permissions)) { ?>
                                    <li class="nav-item"><a class="nav-link"  href="<?php echo base_url('admin/billing/v/consolidated_wallet');?>"><i class="fa fa-file-pdf-o" aria-hidden="true"></i>Wallet Consolidated</a></li>
                                <?php } ?>
                            </ul>
                        </div>
                        <div class="flex-grow-1">
                            <div class="tab-content">
                                <div class="tab-pane active" id="general">
                                    <div class="p-4 border-bottom">
                                        <h5 class="mb-0">Cost Estimator</h5>
                                    </div>
                                    <form id="admin_pricing_calculator_form" method="post" action="#">
                                        <div class="row">
                                            <div class="col-md-12">
                                            <div class="panel_s">
                                                <div class="panel-body">
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label>Seller</label>
                                                            <select name="seller_id" class="form-control form-control-sm" style="width: 100% !important;" required>
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
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label>Source Pincode</label>
                                                            <input type="text" name="origin" class="form-control" required="" placeholder="">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label>Destination Pincode</label>
                                                            <input type="text" name="destination" class="form-control" required="" placeholder="">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label>Weight Kg</label>
                                                            <input type="text" name="weight" class="form-control" required="" value="0.5" placeholder="">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label>Value in INR &#8377;</label>
                                                            <input type="text" name="cod_amount" class="form-control" placeholder="example 1500">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label>L(cm)</label>
                                                            <input type="text" name="length" class="form-control" required="" value="10" placeholder="cm">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label>H(cm)</label>
                                                            <input type="text" name="height" class="form-control" required="" value="10" placeholder="cm">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label>B(cm)</label>
                                                            <input type="text" name="breadth" class="form-control" required="" value="10" placeholder="cm">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <div class="form-group">
                                                            <label>Is COD ?</label>
                                                            <select required="" name="cod" class="form-control">
                                                                <option value="no">No</option>
                                                                <option value="yes">Yes</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-1 text-right"  style="margin-top:28px">
                                                            <button type="submit" name="submit" class="btn btn-primary"><i class="mdi mdi-calculator"></i> Calculate</button>
                                                    </div>
                                                    <div class="table-responsive">
                                                        <table class="table card-table table-bordered table-vcenter text-dark table-outline text-nowrap mtop20" id="calculated_price" style="display:none;">
                                                            <thead>
                                                                <tr>
                                                                    <th>S.No</th>
                                                                    <th>Carrier</th>
                                                                    <th>Courier Rate</th>
                                                                    <th>COD Rate</th>
                                                                    <th>Total Rate</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <td colspan="5" class="text-right">*GST Additional</td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>     
                                                <br>
                                                <div class="col-sm-12 p-all-15 m-t-25">
                                                <p><b>Terms & Conditions:</b></p>
                                                <ul>
                                                    <li>1) Above Shared Commercials are Exclusive GST.</li>
                                                    <li>2) Above pricing subject to change based on courier company updation or change in any commercials.</li>
                                                    <li>3) Freight Weight is Picked - Volumetric or Dead weight whichever is higher will be charged.</li>
                                                    <li>4) Return charges as same as Forward for currier's where special RTO pricing is not shared.</li>
                                                    <li>5) Fixed COD charge or COD % of the order value whichever is higher.</li>
                                                    <li>6) Other charges like address correction charges if applicable shall be charged extra.</li>
                                                    <li>7) Prohibited item not to be ship, if any penalty will charge to seller.</li>
                                                    <li>8) No Claim would be entertained for Glassware, Fragile products,</li>
                                                    <li>9) Concealed damages and improper packaging.</li>
                                                    <li>10) Any weight dispute due to incorrect weight declaration cannot be claimed.</li>
                                                </ul>
                                            </div>           
                                        </div>
                                    <form>
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
<form id="tab_filter" action="<?php base_url();?>" method="POST">
    <input type="hidden" name="perPage" id="perPage" />
    <input type="hidden" autocomplete="off" name="filter[ship_status]" id="ship_status" value="<?= !empty($filter['ship_status']) ? $filter['ship_status'] : '' ?>">
    <input type="hidden" autocomplete="off" name="filter[rto_status]" id="rto_status" value="<?= !empty($filter['rto_status']) ? $filter['rto_status'] : '' ?>">
</form>
<!-- SCROLLING WITH COTENT MODAL END -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
   
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
  $("#admin_pricing_calculator_form").submit(function (event) {
        event.preventDefault();
        $.ajax({
            url: baseUrl+"admin/billing/calculate_pricing",
            type: "POST",
            data: $(this).serialize(),
            cache: false,
            success: function (data) {
                if (data.success) {
                    var price_list = data.success;
                    var content = "";
                    for (var i = 0; i < price_list.length; i++) {
                        content += "<tr>";
                        content += "<td>" + (i + 1) + "</td>";
                        content += "<td>" + price_list[i].name + "</td>";
                        content += "<td>" + price_list[i].courier_charges + "</td>";
                        content += "<td>" + price_list[i].cod_charges + "</td>";
                        content += "<td>" + price_list[i].total_price + "</td>";
                        content += "</tr>";
                    }
                    $("#calculated_price").show();
                    $("#calculated_price tbody").html(content);
                } else if (data.error) alert_float(data.error);
            },
        });
    });
</script> 
