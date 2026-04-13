
<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">Pincodes summary</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item btn-list">		
            <?php if (in_array('import_pincodes', $user_details->permissions)) { ?>
                <button class="tn btn-info btn-sm pull-left display-block mright5" data-bs-target="#scrollmodal" data-bs-toggle="modal" fdprocessedid="c8ebt9" style="margin-right: 10px;"> <i class="fa fa-upload" aria-hidden="true"></i> Upload Pincode(s) </button>
            <?php } ?>
            <?php if (in_array('pincodes_export', $user_details->permissions)) { ?>
                <a href="<?= base_url('admin/pincodes/v/listexport'); ?>" class="btn btn-info btn-sm pull-left display-block mright5" style="margin-right: 10px;"> <i class="fa fa-download" aria-hidden="true"></i> Export </a>
            <?php } ?>
            <a href="javascript:void(0);" class="btn btn-info btn-sm pull-left display-block mright5" data-bs-toggle="sidebar-right" data-bs-target=".sidebar-right" style="margin-right: 10px;"> <i class="fa fa-search" aria-hidden="true"></i> Filter </a>
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
                            <form method="POST" action="<?= base_url('admin/pincodes/v/list') ?>">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group" app-field-wrapper="to_date">
                                            <label for="email">Courier Name:</label>
                                            <select name="filter[courier_id]" class="form-control">
                                            <option value="">All</option>
                                            <?php if (!empty($couriers)) foreach ($couriers as $courier) { ?>
                                            <option value="<?= $courier->id; ?>" <?php if (!empty($filter['courier_id']) && $filter['courier_id'] == $courier->id) { ?> selected="" <?php } ?>><?php echo ucwords($courier->name);echo isset(($courier->courier_alias)) ? " (".ucfirst($courier->courier_alias).")" : ""; ?></option>
                                            <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group" app-field-wrapper="from_date">
                                            <label for="email">Pin Code:</label>
                                            <input type="text" name="filter[pincode]" value="<?= !empty($filter['pincode']) ? $filter['pincode'] : '' ?>" class="form-control" placeholder="Pin Code to Search">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6" style="margin-top:29px;">
                                        <div class="form-group" app-field-wrapper="to_date">
                                            <button type="submit" class="btn btn-sm btn-success">Filter</button>
                                            <a href="<?= base_url('admin/pincodes/v/list'); ?>" class="btn btn-sm btn-primary">Reset</a>
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
                <h3 class="card-title">Pincodes</span></h3>
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

                    <div class="col-sm-2 col-md-2 action_row_selected"  style="display: none;">
                        <div class="dataTables_length" id="responsive-datatable_length">
                        <div class="btn btn-sm btn-success ms-auto">
                            <div class="item-action dropdown">
                                <a href="javascript:void(0)" data-bs-toggle="dropdown" class="icon" aria-expanded="false" style="color: #ffffff;">
                                <b>Chosen </b>(<b class="multiple_select_count">0</b>)&nbsp;<i class="fa fa-level-down" aria-hidden="true"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="javascript:void(0)" class="dropdown-item bulk-ship-button" data-bs-target="#lgscrollmodal" data-bs-toggle="modal">Get Bulk AWB</a>
                                    <div class="dropdown-divider"></div>
                                    <?php if (!empty($user_details->parent_id)  &&    in_array('cancel_shipments', $user_details->permissions)) { ?>
                                        <a href="javascript:void(0)" class="dropdown-item bulk-cancel-order-button">Cancel Order</a>
                                    <?php } else if (empty($user_details->parent_id)) { ?>
                                        <a href="javascript:void(0)" class="dropdown-item bulk-cancel-order-button">Cancel Order</a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered text-nowrap border-bottom" id="responsive-datatable">
                        <thead>
                            <tr>

                                <th>Pincode</th>
                                <th>Courier</th>
                                <th>City Name</th>
                                <th>State</th>
                                <th>COD Status</th>
                                <th>Prepaid Status</th>
                                <th>Pickup Status</th>
                                <th>Reverse Pickup</th>
                                <th>Area Code</th>
                                <th>SDD/NDD Code</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($pincodes)) {
                                foreach ($pincodes as $pincode) {
                            ?>
                                    <tr>
                                        <td><?= $pincode->pincode; ?></td>
                                        <td><?php echo ucwords($pincode->courier_name); echo isset(($pincode->courier_alias)) ? " (".ucfirst($pincode->courier_alias).")" : ""; ?></td>
                                        <td><?= ucwords($pincode->city); ?></td>
                                        <td><?= ucwords($pincode->state_code); ?></td>
                                        <td><?= ucwords($pincode->cod); ?></td>
                                        <td><?= ucwords($pincode->prepaid); ?></td>
                                        <td><?= ucwords($pincode->pickup); ?></td>
                                        <td><?= ucwords($pincode->is_reverse_pickup); ?></td>
                                        <td><?= strtoupper($pincode->area_code); ?></td>
                                        <td><?= strtoupper($pincode->sdd_code); ?></td>
                                        <td>
                                            <?php if (in_array('import_pincodes', $user_details->permissions)) { ?>
                                                <button data-pincode-id="<?= $pincode->id; ?>" class="btn btn-outline-info btn-sm change_pincode_settings">Edit</button>
                                            <?php } ?>
                                        </td>

                                    </tr>

                                <?php
                                }
                            } else { ?>
                                <tr>
                                    <td colspan="8" class="text-center">No Records Found</td>
                                </tr>
                            <?php
                            } ?>
                        </tbody>
                    </table>
                    </div>
                    <div class="row mt-4">
                        <div class="col-sm-12 col-md-6">
                            <div class="dataTables_info" id="example-multi_info" role="status" aria-live="polite">Showing <?= $offset + 1; ?> to <?= $offset + $limit; ?> of <?= $total_records; ?> entries</div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <div class="dataTables_paginate paging_simple_numbers" id="responsive-datatable_paginate">
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
<!-- END ROW-1 -->
</div>
<!-- SCROLLING WITH COTENT MODAL START -->
<div class="modal fade" id="scrollmodal">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content modal-content-demo">
            <div class="modal-header">
                <h6 class="modal-title">Upload Pincode(s) (Bulk)</h6><button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"></button>
            </div>
            <?php if (in_array('import_pincodes', $user_details->permissions)) { ?>
            <form action="<?= base_url('admin/pincodes/v/upload'); ?>" enctype="multipart/form-data" method="post" accept-charset="utf-8" data-gtm-form-interact-id="0">
                <div class="modal-body">
                        <div class="col-lg-12 col-sm-12 mb-4 mb-lg-0">
                        <div class="form-group">
                        <label>Courier</label>
                        <select required="" name="courier_id" class="form-control">
                            <option value="" selected="">Please Select</option>
                            <?php
                            if (!empty($couriers)) {
                                array_multisort(array_column($couriers, 'name'), SORT_ASC, $couriers);
                                foreach ($couriers as $courier) {
                            ?>
                                    <option <?php if (set_value('courier_id') == $courier->id) { ?> selected="" <?php } ?> value="<?= $courier->id ?>"><?php echo ucwords($courier->name); echo isset(($courier->courier_alias)) ? " (".ucfirst($courier->courier_alias).")" : "";?></option>
                            <?php }
                            }
                            ?>

                        </select>
                        <br>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" name="action" required value="update" checked class="custom-control-input" id="customCheckDisabled2">
                            <label class="custom-control-label" for="customCheckDisabled2">Update Records (Insert new recods or update existing)</label>
                        </div>
                       <br><br>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" name="action" required value="replace" class="custom-control-input" id="customCheckDisabled1">
                            <label class="custom-control-label" for="customCheckDisabled1">Replace Records (Delete all existing records and insert new)</label>
                        </div>
                        <br>
                        <br>
                        <p>Download sample file : <a class="text-info" href="<?php echo base_url();?>assets/pincodes_format.csv?<?php echo time();?>"><i class="fa fa-download" aria-hidden="true"></i></a></p>
                        <input class="form-control" type="file" name="importFile" required>
                    </div>               
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn ripple btn-primary" type="submit">Upload File</button>
                    <button class="btn ripple btn-secondary" data-bs-dismiss="modal" type="button">Close</button>
                </div>
            </form>
            <?php } ?>
        </div>
    </div>
</div>
<div class="modal fade edit_pincode_modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content" id="edit_pincode_modal_content">
        </div>
    </div>
</div>
<form id="tab_filter" action="<?php echo base_url('orders/all');?>" method="POST">
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
        document.getElementById('fulfillment').value=status;
        document.getElementById('tab_filter').submit();
    }
    $('.change_pincode_settings').on('click', function(e) {
        e.preventDefault();
        var pincode_id = $(this).attr('data-pincode-id');

        $.ajax({
            url: baseUrl+'admin/pincodes/edit_pincode',
            type: "POST",
            cache: false,
            data: {
                pincode_id: pincode_id,
            },
            success: function(data) {

                $('.edit_pincode_modal').modal('show');
                $("#edit_pincode_modal_content").html(data);
            }

        });


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

