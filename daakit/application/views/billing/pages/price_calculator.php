<?php
$plan->plan_name = strtolower($plan->plan_name);
$zone_price_division = $this->config->item('zone_price_division');
?>

<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">Shipping cost estimator</h4>
    <ol class="breadcrumb d-flex flex-wrap">
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
                            <form method="post" action="<?= base_url('shipping/all') ?>">
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
                                        <a href="<?= base_url('shipping/all'); ?>" class="btn btn-primary btn-sm">Reset</a>                                
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
                                <li class="nav-item"><a class="nav-link active"  href="<?php echo base_url('billing/version/seller_price_calculator');?>"><i class="fa fa-calculator" aria-hidden="true"></i>Cost Estimator</a></li>
                                <li class="nav-item"><a class="nav-link"  href="<?php echo base_url('billing/version/seller_recharge_logs');?>"><i class="fa fa-inr" aria-hidden="true"></i>Transactions</a></li>
                                <li class="nav-item"><a class="nav-link"  href="<?php echo base_url('billing/version/seller_cod_remittance');?>"><i class="fa fa-money"></i>COD Settlement</a></li>
                                <li class="nav-item"><a class="nav-link"  href="<?php echo base_url('billing/version/seller_shipping_charges');?>"><i class="fa fa-truck" aria-hidden="true"></i>Shipping Cost</a></li>
                                <li class="nav-item"><a class="nav-link"  href="<?php echo base_url('billing/version/seller_invoice');?>"><i class="fa fa-file-pdf-o" aria-hidden="true"></i>Billing Statement</a></li>
                                <li class="nav-item"><a class="nav-link"  href="<?php echo base_url('billing/version/seller_credit_notes');?>"><i class="fa fa-file-pdf-o" aria-hidden="true"></i>Credit Memos</a></li>
                            </ul>
                        </div>
                        <div class="flex-grow-1">
                            <div class="tab-content">
                                <div class="tab-pane active" id="general">
                                    <div class="p-4 border-bottom">
                                        <h5 class="mb-0">Cost Estimator</h5>
                                        <div style="float: right;margin-top: -25px;">
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#lgscrollmodal"><i class="mdi mdi-calculator"></i> View Pricing Plans</button>
                                        </div>
                                    </div>
                                    <form id="pricing_calculator_form" method="post" action="<?= base_url('tools/calculate_pricing'); ?>">
                                        <div class="row">
                                            <div class="col-md-12">
                                            <div class="panel_s">
                                                <div class="panel-body">
                                                <div class="row">
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
                                                </div>
                                                <div class="row">
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
                                                    <div class="col-sm-1">
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
<div class="modal fade bs-modal-lg" id="lgscrollmodal" tabindex="-1" role="dialog" aria-labelledby="slideRightModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog modal-dialog-centered modal-lg" >
        <div class="modal-content" style="height:auto;">
            <div class="modal-header">
                <h5 class="modal-title" id="slideRightModalLabel">Pricing Plans</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-primary">
                            <div class="tab-menu-heading">
                                <div class="tabs-menu1">
                                    <!-- Tabs -->
                                    <ul class="nav panel-tabs">
                                        <li class="nav-item">
                                            <a href="#tab5" class="nav-link active" data-bs-toggle="tab" aria-selected="true" role="tab">Forward</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#tab6" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">RTO</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="panel-body tabs-menu-body pt-4">
                                <div class="tab-content table_price_style">
                                    <div class="tab-pane active show" id="tab5" role="tabpanel">
                                        <div class="table-responsive m-t-10">
                                            <table class="table table-bordered table-sm text-left table-hover">
                                                <thead>
                                                    <tr class="border">
                                                        <th width="200" class="border" style="text-align: center;">Courier</th>
                                                        <th style="text-align: center;">Z1<br>Metropolitan</th>
                                                        <th style="text-align: center;">Z2<br>Regional</th>
                                                        <th style="text-align: center;">Z3<br>Intercity</th>
                                                        <th style="text-align: center;">Z4<br>Pan-India</th>
                                                        <th style="text-align: center;">Z5<br>North-East and J&K.</th>
                                                        <th style="text-align: center;">COD Charges</th>
                                                        <th style="text-align: center;">COD %</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    foreach ($couriers as $courier) {
                                                        switch ($plan->plan_type) {
                                                            case 'standard':
                                                                $price = new \App\Lib\Pricing\PlanPrice($plan->id, $courier->id, 'fwd');
                                                                $weight = new \App\Lib\Pricing\PlanPrice($plan->id, $courier->id, 'weight');
                                                                break;

                                                            default:
                                                                $price = new \App\Lib\Pricing\PlanPrice($plan->id, $courier->id, 'fwd');
                                                                $weight = new \App\Lib\Pricing\PlanPrice($plan->id, $courier->id, 'weight');
                                                                break;
                                                        }
                                                    ?>
                                                        <tr class="border">
                                                            <td class="border" style="text-align:center;">
                                                                <?php $additional_weight =  ($courier->additional_weight / 1000); ?>
                                                                <p><?= ucwords($courier->name); ?> <span>Additional <?= '(' . $additional_weight . 'kg)'; ?></span></p>
                                                            </td>
                                                            <td style="text-align:center;">
                                                                <p>
                                                                    <?= round(($price->getZone1Price() / $zone_price_division),2) ?>
                                                                    <span>
                                                                        <?= round(($weight->getZone1Price() / $zone_price_division),2) ?>
                                                                    </span>
                                                                </p>
                                                            </td>
                                                            <td style="text-align:center;">
                                                                <p>
                                                                    <?= round(($price->getZone2Price() / $zone_price_division),2) ?>
                                                                    <span>
                                                                        <?= round(($weight->getZone2Price() / $zone_price_division),2) ?>
                                                                    </span>
                                                                </p>
                                                            </td>
                                                            <td style="text-align:center;">
                                                                <p>
                                                                    <?= round(($price->getZone3Price() / $zone_price_division),2) ?>
                                                                    <span>
                                                                        <?= round(($weight->getZone3Price() / $zone_price_division),2) ?>
                                                                    </span>
                                                                </p>
                                                            </td>
                                                            <td style="text-align:center;">
                                                                <p>
                                                                    <?= round(($price->getZone4Price() / $zone_price_division),2) ?>
                                                                    <span>
                                                                        <?= round(($weight->getZone4Price() / $zone_price_division),2) ?>
                                                                    </span>
                                                                </p>
                                                            </td>
                                                            <td style="text-align:center;">
                                                                <p>
                                                                    <?= round(($price->getZone5Price() / $zone_price_division),2) ?>
                                                                    <span>
                                                                        <?= round(($weight->getZone5Price() / $zone_price_division),2) ?>
                                                                    </span>
                                                                </p>
                                                            </td>
                                                            <td style="text-align:center;">
                                                                <p><?= round(($price->getMinCod() / $zone_price_division),2) ?></p>
                                                            </td>
                                                            <td style="text-align:center;">
                                                                <p><?= round($price->getCodPercent() / $zone_price_division, 1) ?>%</p>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tab6" role="tabpanel">
                                        <div class="table-responsive m-t-10">
                                            <table class="table table-bordered table-sm text-left table-hover ">
                                                <thead>
                                                    <tr>
                                                        <th width="200" style="text-align: center;">Courier</th>
                                                        <th style="text-align: center;">Z1<br>Metropolitan</th>
                                                        <th style="text-align: center;">Z2<br>Regional</th>
                                                        <th style="text-align: center;">Z3<br>Intercity</th>
                                                        <th style="text-align: center;">Z4<br>Pan-India</th>
                                                        <th style="text-align: center;">Z5<br>North-East and J&K.</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    foreach ($couriers as $courier) {
                                                        switch ($plan->plan_type) {
                                                            case 'standard':
                                                                $price = new \App\Lib\Pricing\PlanPrice($plan->id, $courier->id, 'rto');
                                                                break;

                                                            default:
                                                                $price = new \App\Lib\Pricing\PlanPrice($plan->id, $courier->id, 'rto');
                                                                break;
                                                        }
                                                    ?>
                                                        <tr>
                                                            <td style="text-align:center;"><?= ucwords($courier->name); ?></td>
                                                            <td style="text-align:center;"><?= round(($price->getZone1Price() / $zone_price_division),2) ?></td>
                                                            <td style="text-align:center;"><?= round(($price->getZone2Price() / $zone_price_division),2) ?></td>
                                                            <td style="text-align:center;"><?= round(($price->getZone3Price() / $zone_price_division),2) ?></td>
                                                            <td style="text-align:center;"><?= round(($price->getZone4Price() / $zone_price_division),2) ?></td>
                                                            <td style="text-align:center;"><?= round(($price->getZone5Price() / $zone_price_division),2) ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">*GST Additional</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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
</script> 
<style>
    .table_price_style .table-bordered td,
    .table_price_style .table-bordered th {
        border: 1px solid #8a8b8d !important;
        vertical-align: middle !important;
    }

    .table_price_style .table.table-bordered td {
        padding: 0px !important;
    }

    .table_price_style .table.table-bordered td p {
        margin-bottom: 0 !important;
        line-height: 28px !important;
        padding: 2px 0px !important;
    }

    .table_price_style .table.table-bordered td p span {
        display: block !important;
        border-top: 1px solid #ddd !important;
    }
    .table-responsive {
    overflow-x: clip !important;
}
.tab-menu-heading {
    padding: 5px;
    border: 1px solid var(--border);
    border-block-end: 0;
}
</style>