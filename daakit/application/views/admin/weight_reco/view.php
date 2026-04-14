<?php if (in_array('billing_recharge_logs', $user_details->permissions)) { ?>
<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">Weight Reconciliation</h4>
    <ol class="breadcrumb d-flex flex-wrap">
    <?php if ($page_type == 'manage') { ?>
        <li class="breadcrumb-item btn-list">
            <button class="btn btn-info btn-sm me-2" data-bs-toggle="modal" data-bs-target=".bulk_action_popup"><i class="fa fa-upload" aria-hidden="true"></i> Bulk Action</button>
            <a href="<?php echo base_url('admin/weight_reco/exportCSV');?>?<?= http_build_query($_GET) ?>" target="_blank" class="btn btn-info btn-sm me-2"><i class="fa fa-download" aria-hidden="true"></i> Retrieve Export </a>
            <a href="javascript:void(0);" class="btn btn-info btn-sm me-2" data-bs-toggle="sidebar-right" data-bs-target=".sidebar-right"> Filter </a>
        </li>
    <?php } ?> 
    </ol>
</div>
<!-- END PAGE-HEADER -->
 <!-- filter section start -->
 <?php if ($page_type == 'manage') { ?>
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
                            <form method="get" action="<?= base_url('admin/weight_reco/v/manage'); ?>">
                                <div class="row m-t-15 m-b-10">
                                    <div class="col-sm-6">
                                        <label for="email">From Date:</label>
                                        <input type="date" autocomplete="off" name="filter[start_date]" value="<?= !empty($filter['start_date']) ? $filter['start_date'] : '' ?>" data-start-date="<?= !empty($filter['start_date']) ? $filter['start_date'] : '' ?>" data-end-date="<?= !empty($filter['end_date']) ? $filter['end_date'] : '' ?>" class="form-control date-range-picker col-sm-12 ">
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="email">To Date:</label>
                                        <input type="date" name="filter[end_date]" autocomplete="off" value="<?= !empty($filter['end_date']) ? $filter['end_date'] : '' ?>" data-start-date="<?= !empty($filter['start_date']) ? $filter['start_date'] : '' ?>" data-end-date="<?= !empty($filter['end_date']) ? $filter['end_date'] : '' ?>" class="form-control date-range-picker col-sm-12 ">
                                    </div>
                                    <div class="form-group col-sm-12" style="margin-top:10px;">
                                        <label for="email">Seller Name:</label>
                                        <select name="filter[seller_id]" class="form-control" style="width: 100% !important;">
                                            <option value="">Select Seller</option>
                                            <?php
                                            if (!empty($users)) {
                                                foreach ($users as $values) {
                                                    $sellerid = '';
                                                    if (!empty($filter['seller_id']))
                                                        $sellerid = $filter['seller_id'];
                                            ?>
                                                    <option <?php if ($sellerid == $values->id) { ?> selected <?php } ?> value="<?php echo $values->id; ?>"><?php echo ucwords($values->user_fname . ' ' . $values->user_lname); ?> (<?php echo ucwords($values->company_name) ?>)</option>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-12" style="margin-top:2px;">
                                        <label for="email">AWB Number(s):</label>
                                        <input type="text" autocomplete="off" name="filter[awb_no]" value="<?= !empty($filter['awb_no']) ? $filter['awb_no'] : '' ?>" class="form-control" placeholder="AWB Separated by comma">
                                    </div>

                                    <div class="form-group col-sm-12" style="margin-top:10px;">
                                        <label for="email">Courier Name:</label>
                                        <select name="filter[courier_id]" class="form-control">
                                            <option value="">All</option>
                                            <?php if (!empty($couriers)) foreach ($couriers as $courier) { ?>
                                                <option value="<?= $courier->id; ?>" <?php if (!empty($filter['courier_id']) && $filter['courier_id'] == $courier->id) { ?> selected="" <?php } ?>><?= ucwords($courier->name); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="form-group col-sm-12" style="margin-top:2px;">
                                        <label for="email">Status:</label>
                                        <?php
                                        $esc_status = array(
                                            '' => 'All',
                                            'no dispute' => 'No Dispute',
                                            'open' => 'Open',
                                            'accepted' => 'Accepted',
                                            'auto accepted' => 'Auto Accepted',
                                            'dispute' => 'Dispute Open',
                                            'dispute closed' => 'Dispute Closed',
                                        );
                                        $js = "class='form-control' style='width: 100% !important;' ";
                                        echo form_dropdown('filter[status]', $esc_status, !empty($filter['status']) ? $filter['status'] : '', $js);
                                        ?>
                                    </div>

                                    <div class="col-sm-12" style="margin-top:2px;padding-top: 30px;">
                                        <button type="submit" class="btn btn-sm btn-outline-success">Apply</button>
                                        <a href="<?= base_url('admin/weight_reco/v/manage'); ?>" class="btn btn-sm btn-outline-primary">Clear</a>
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
<?php } ?>    
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
                            <?php if (in_array('weight_upload', $user_details->permissions)) { ?>
                                <li class="nav-item"><a class="nav-link  <?= ($page_type == 'weight_upload') ? 'active' : '' ?>"  href="<?php echo base_url('admin/weight_reco/v/weight_upload');?>"><i class="fa fa-balance-scale" aria-hidden="true"></i> Upload (Weight)</a></li>
                            <?php } ?>    
                            <?php if (in_array('manage_weight', $user_details->permissions)) { ?>
                                <li class="nav-item"><a class="nav-link <?= ($page_type == 'manage') ? 'active' : '' ?>"  href="<?php echo base_url('admin/weight_reco/v/manage');?>"><i class="fa fa-balance-scale" aria-hidden="true"></i> Manage (Weight)</a></li>
                            <?php } ?>    
                            </ul>
                        </div>
                        <div class="flex-grow-1">
                            <div class="tab-content">
                                <?= $inner_content; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END ROW-1 -->
</div>
<?php } ?>
