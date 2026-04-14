<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">Manifest request</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item">		
            <a href="<?= base_url('shipping/all');?>" class="btn btn-info btn-sm pull-left display-block mright5" style="margin-right: 10px;"> Back </a>
            <a href="<?= base_url('pickups/exportCSV');?>" class="btn btn-info btn-sm pull-left display-block mright5" style="margin-right: 10px;"> Export </a>
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
                            <form method="post" action="<?= base_url('pickups') ?>">
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
                                    <div class="col-md-6">
                                        <div class="form-group" app-field-wrapper="from_date">
                                            <label for="email" class="control-label">Pickup ID:</label>
                                            <input type="text" name="filter[pickup_number]" value="<?= !empty($filter['pickup_number']) ? $filter['pickup_number'] : '' ?>" class="form-control" placeholder="Pickup Number">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group" app-field-wrapper="to_date">
                                            <label for="email" class="control-label">Courier Name:</label>
                                            <select name="filter[courier_id]" class="form-control">
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
                                        <div class="form-group" app-field-wrapper="to_date">
                                            <label for="email" class="control-label">Warehouse:</label>
                                            <select name="filter[warehouse_id]" class="form-control">
                                                <option value="">All</option>
                                                <?php if (!empty($warehouses)) foreach ($warehouses as $warehouse) { ?>
                                                    <option value="<?= $warehouse->id; ?>" <?php if (!empty($filter['warehouse_id']) && $filter['warehouse_id'] == $warehouse->id) { ?> selected="" <?php } ?>><?= ucwords($warehouse->name); ?></option>
                                                <?php } ?>
                                            </select>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group" app-field-wrapper="from_date">
                                            <label for="email" class="control-label">Pickup:</label>
                                            <select name="filter[pickup_done]" class="form-control">
                                                <?php
                                                $pickup_done = '';
                                                if (!empty($filter['pickup_done']))
                                                    $pickup_done = $filter['pickup_done'];
                                                ?>
                                                <option <?php if ($pickup_done == '') { ?> selected <?php } ?> value="">All</option>
                                                <option <?php if ($pickup_done == 'yes') { ?> selected <?php } ?> value="yes">Yes</option>
                                                <option <?php if ($pickup_done == 'no') { ?> selected <?php } ?> value="no">No</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6" style="margin-top:29px;">
                                        <div class="form-group" app-field-wrapper="to_date">
                                            <button type="submit" class="btn btn-sm btn-success">Filter</button>
                                            <a href="<?= base_url($filter_path) ?>" class="btn btn-sm btn-primary">Reset</a>
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
				<h3 class="card-title">Manifest <span style="font-size:13px">(<?= date('d-m-Y',strtotime($filter['start_date']));?> - <?= date('d-m-Y',strtotime($filter['end_date']));?>)</span></h3>
			</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-nowrap border-bottom" id="responsive-datatable">
                    <thead>
                        <tr>
                            <th>Manifest ID</th>
                            <th>Courier</th>
                            <th>Number of Orders/Status</th>
                            <th>Warehouse</th>
                            <th>Pickup No</th>
                            <th>Download</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($manifests)) {
                            $i = 1;
                            foreach ($manifests as $manifest) {
                        ?>
                                <tr>
                                    <td>
                                        <?= $manifest->id; ?> <br><small><b> <?php if($manifest->order_type=='ecom') { echo "B2C";} else if($manifest->order_type=='cargo') { echo "Cargo"; } else if($manifest->order_type=='international') { echo "International"; }   ?> </b></small> 
                                        <br><?= date('M d, Y H:i', $manifest->pickup_created); ?>
                                    </td>
                                    <td><?= $manifest->courier_name; ?></td>
                                    <td><?php
                                        $shipments = explode(',', (string)$manifest->shipment_ids);
                                        echo count($shipments);
                                        ?>
                                        <br><?= ($manifest->pickup_done == '1') ? 'Completed' : 'Pickup Scheduled'; ?>
                                    </td>
                                    <td><?= ucwords($manifest->warehouse_name); ?> </td>
                                    <td><?= $manifest->pickup_number; ?></td>
                                    <td>
                                        <a target="_blank" href="pickups/download/<?= $manifest->id; ?>" class="btn btn-sm btn-outline-info"><i class="fa fa-download"></i></a>
                                        <?php if (($manifest->pickup_created < strtotime("-10 hours") && $manifest->pickup_done == '0') && (!empty($manifest->escalation_time < strtotime("-10 hours")) && $manifest->pickup_done == '0')) { ?>
                                            <!-- <button class="btn btn-sm btn-info pickup-escalation-button" type="button" data-toggle="modal" data-target="#pickup-escilation-modal" data-pickup-id="<?= $manifest->id; ?>">Escalate</button> -->
                                        <?php }else if(!empty($manifest->esc_id)){ ?>
                                            <?php ?>
                                            <!-- <a class="btn btn-sm btn-info" href="escalations/view/<?= $manifest->esc_id; ?>" type="button" >View Escalation</a> -->
                                        <?php  } ?>

                                    </td>

                                </tr>
                            <?php
                                $i++;
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="8" class="text-center">No Records Found</td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END ROW-1 -->
</div>
<!-- SCROLLING WITH COTENT MODAL START -->
<div class="modal fade" id="scrollmodal">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content modal-content-demo">
            <div class="modal-header">
                <h6 class="modal-title">Upload Orders (Bulk)</h6><button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"></button>
            </div>
            <form action="<?php echo base_url();?>orders/import" enctype="multipart/form-data" method="post" accept-charset="utf-8" data-gtm-form-interact-id="0">
                <div class="modal-body">
                        <div class="col-lg-12 col-sm-12 mb-4 mb-lg-0">
                        <div class="form-group">
                        <p>Download sample file : <a class="text-info" href="<?php echo base_url();?>assets/downloaded_order_sample.csv?<?php echo time();?>"><i class="fa fa-download" aria-hidden="true"></i></a></p>
                        <br>
                        <input class="form-control" type="file" name="importFile" required>
                    </div>               
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn ripple btn-primary" type="submit">Upload File</button>
                    <button class="btn ripple btn-secondary" data-bs-dismiss="modal" type="button">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade bd-modal-lg"  role="dialog" id="lgscrollmodal">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" id="show_information">
        </div>
    </div>
</div>
<!-- SCROLLING WITH COTENT MODAL END -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
  <?php unset($_GET['perPage']); ?>

  function per_page_records(per_page = false) {
      var page_url = '<?= base_url('orders/all') . '?' . http_build_query($_GET) . '&perPage=' ?>' + per_page;
      window.location.href = page_url;
  }

  $('.add_segment_button').on('click', function(e) {
      e.preventDefault();
      var segment_id = $(this).attr('data-segment-id');
      $.ajax({
          url: 'orders/add_edit_segment/' + segment_id,
          type: "GET",
          cache: false,
          success: function(data) {
              $('#add_edit_segment_form').html(data);

          }
      });
  });

  $('.make_ivr_call').on('click', function(e) {
      e.preventDefault();
      var order_id = $(this).attr('data-order-id');
      $.ajax({
          url: 'apps/ivrcalls/call_order',
          type: "POST",
          data: {
              order_id: order_id,
          },
          cache: false,
          success: function(data) {
              $('#ivr_call_modal').html(data);

          }
      });
  });

  $('.view_ivr_history').on('click', function(e) {
      e.preventDefault();
      var order_id = $(this).attr('data-order-id');
      $.ajax({
          url: 'apps/ivrcalls/order_history',
          type: "POST",
          data: {
              order_id: order_id,
          },
          cache: false,
          success: function(data) {
              $('#ivr_history_data').html(data);

          }
      });
  });

  $('.refresh_orders_button').on('click', function(e) {
      e.preventDefault();
      $.ajax({
          url: 'orders/refresh',
          type: "GET",
          cache: false,
          success: function(data) {
              alert('Refresh request received');

          }
      });
  });
</script>
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
          "paging": true, // false to disable pagination (or any other option)
          "filter": true,
          "info": false,
      });

  });
</script> 
