<style>
/* Hover state: font color changes to white */
a:hover {
    color: #fff !important; /* Change the font color to white */
}
</style>
<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <h4 class="page-title"><?php if($view_page=='add_plan') echo 'Add Pricing Plan'; else if($view_page=='view_pricing') echo 'View pricing for <b>'. ucwords($plan_details->plan_name).'</b>';  else if($view_page=='add_pricing') echo 'Add pricing for <b>'.ucwords($plan_details->plan_name).'</b>'; else if($view_page=='landing') echo 'Standard Landing Price'; else echo 'Pricing Plans'?></h4>
    <ol class="breadcrumb d-flex flex-wrap">
    <?php
    switch ($view_page) {
        case 'plans':
    ?>
        <li class="breadcrumb-item btn-list">
            <?php if (in_array('plans_export', $user_details->permissions)) { ?>
                <button type="submit" id="exportBtn" class="btn btn-info btn-sm me-2"><i class="fa fa-download"></i> Retrieve Export </button>
            <?php } ?>
            <?php if (in_array('plans_create', $user_details->permissions)) { ?>
                <a href="<?= base_url('admin/plans/v/add_plan');?>" class="btn btn-info btn-sm me-2">Create Plan</a>
            <?php } ?>
        </li>
        <?php
        break;
        case 'landing':
        ?>
            <li class="breadcrumb-item btn-list">
                <?php if (in_array('export_margin', $user_details->permissions)) { ?>
                    <a href="<?= base_url('admin/plans/v/export_margin')?>" class="btn btn-info btn-sm"><i class="mdi mdi-arrow-down-bold-circle"></i> Export Margin</a>
                <?php } ?>
                <?php if (in_array('export_landing', $user_details->permissions)) { ?>
                    <a href="<?= base_url('admin/plans/v/export_landing')?>" class="btn btn-info btn-sm"><i class="mdi mdi-arrow-down-bold-circle"></i> Export Landing</a>
                <?php } ?>  
            </li>
        <?php
            break;
        default:
    }
    ?>   
    </ol>
</div>
<!-- END PAGE-HEADER -->
 <!-- filter section start -->
 <!-- <div class="sidebar sidebar-right sidebar-animate">
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
                            <?php
                                if($report_type == 'receipt_history')
                                {
                                ?>
                                <form method="get" action="<?= base_url('admin/remittance/reports/receipt_history') ?>">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-row">
                                                <div class="col-md-12">
                                                    <div class="form-group" app-field-wrapper="from_date">
                                                        <label for="from_date" class="control-label">Courier</label>
                                                            <select   name="filter[courier_id]" class="form-control js-select2" style="width: 100% !important;">
                                                            <option value="" selected="">Please Select</option>
                                                            <?php
                                                            $courier_id = '';
                                                            if (!empty($filter['courier_id']))
                                                                $courier_id = $filter['courier_id'];
                                                            
                                                            if (!empty($couriers))
                                                                foreach ($couriers as $courier) {
                                                                    ?>
                                                                    <option <?php if ($courier_id == $courier->id) { ?> selected="" <?php } ?> value="<?= $courier->id ?>"><?= ucwords($courier->name); ?></option>
                                                                <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group" app-field-wrapper="to_date">
                                                        <label for="email">UTR No:</label>
                                                        <input type="text" name="filter[utr_no]" value="<?= !empty($filter['utr_no']) ? $filter['utr_no'] : '' ?>"class="form-control" placeholder="Search by UTR No.">
                                                    </div>
                                                </div>
                                                <div class="col-sm-12" style="margin-top:20px;text-align:right">
                                                    <button type="submit" class="btn btn-sm btn-success">Filter</button>
                                                    <a href="<?= base_url('admin/remittance/reports/receipt_history'); ?>" class="btn btn-primary btn-sm">Reset</a>                                
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <?php
                                }
                                else if($report_type == 'seller')
                                {
                                    ?>
                                    <form method="POST" action="<?php echo base_url('admin/remittance/reports/seller');?>">
                                        <div class="row m-b-10 p-3">
                                            <div class="form-group col-sm-12">
                                                <label for="email">Seller Name:</label>
                                                <select name="filter[seller_id][]"  class="form-control" style="width: 100% !important;">
                                                    <option value="">Select Seller</option>

                                                    <?php
                                                    if (!empty($users)) {
                                                        foreach ($users as $values) {
                                                            $sellerid = '';
                                                            if (!empty($filter['seller_id']))
                                                                $sellerid = $filter['seller_id'];
                                                    ?>
                                                            <option value="<?= $values->id; ?>" <?php if (!empty($filter['seller_id']) && in_array($values->id, $filter['seller_id'])) { ?> selected="" <?php } ?>><?php echo $values->id . ' - ' . ucwords($values->user_fname . ' ' . $values->user_lname); ?> (<?php echo ucwords($values->company_name) ?>)</option>
                                                    <?php
                                                        }
                                                    }
                                                    ?>

                                                </select>
                                                <input type="hidden" name="filter[allseller]" id="allseller" value="1">
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="row">
                                                    <div class="form-group col-sm-12">
                                                        <label class="font-secondary">Seller Id </label>
                                                        <input type="text" autocomplete="off" name="filter[seller_ids]" value="<?= !empty($filter['seller_ids']) ? $filter['seller_ids'] : '' ?>" class="form-control" placeholder="Enter Seller ID With Comma">

                                                    </div>
                                                    <div class="form-group col-sm-12">
                                                        <label for="font-secondary"> Ignore Seller Id</label>
                                                        <input type="text" autocomplete="off" name="filter[ignore_seller_id]" value="<?= !empty($filter['ignore_seller_id']) ? $filter['ignore_seller_id'] : '' ?>" class="form-control" placeholder="Enter Ignore Seller ID With Comma">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="row">
                                                    <div class="form-group col-sm-12" style="margin-top:2px;">
                                                        <label class="font-secondary">Remittance Cycle </label>
                                                        <select name="filter[remittance_cycles]" class="form-control js-select2">
                                                            <option value="">Select Remittance Cycle</option>
                                                            <?php
                                                            foreach ($remmitance_cycle as $remmitance_val) {
                                                                $remittance_cy = '';
                                                                if (!empty($filter['remittance_cycles']))
                                                                    $remittance_cy = $filter['remittance_cycles'];
                                                            ?>
                                                                <option value="<?= $remmitance_val->remittance_cycle; ?>" <?php if (!empty($remittance_cy) && $remmitance_val->remittance_cycle == $remittance_cy) { ?> selected="" <?php } ?>><?= 'T+' . $remmitance_val->remittance_cycle; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-12" style="margin-top:31px;">
                                                        <button type="submit" class="btn btn-sm btn-success">Apply</button>
                                                        <a href="<?= base_url('admin/remittance/reports/seller'); ?>" class="btn btn-sm btn-primary">Clear</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <?php
                                }
                                else if($report_type == 'remittance')
                                {
                                    ?>
                                    <form method="get" action="<?= base_url('admin/remittance/reports/remittance') ?>">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-row">
                                                <div class="form-group col-sm-12">
                                                        <label for="email">Company Name:</label>
                                                        <select name="filter[seller_id]" class="form-control  form-control-sm" style="width: 100% !important;">
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
                                                    <div class="form-group col-sm-12">
                                                        <label for="email">Remittance ID:</label>
                                                        <input type="text" name="filter[remittance_id]" value="<?= !empty($filter['remittance_id']) ? $filter['remittance_id'] : '' ?>" class="form-control" placeholder="Search remittance ID">
                                                    </div>
                                                    <div class="form-group col-sm-12">
                                                        <label for="email">UTR No:</label>
                                                        <input type="text" name="filter[utr_no]" value="<?= !empty($filter['utr_no']) ? $filter['utr_no'] : '' ?>" class="form-control" placeholder="Search by UTR No.">
                                                    </div>
                                                    <div class="form-group col-sm-12">
                                                        <label for="email">Paid Status</label>
                                                        <select name="filter[paid_status]" class="form-control" style="width: 100% !important;">
                                                            <?php
                                                            $paid_status = '';
                                                            if (!empty($filter['paid_status']))
                                                                $paid_status = $filter['paid_status'];
                                                            ?>
                                                            <option <?php if ($paid_status == '') { ?> selected="" <?php } ?> value="">All</option>
                                                            <option <?php if ($paid_status == 'no') { ?> selected="" <?php } ?> value="no">Unpaid</option>
                                                            <option <?php if ($paid_status == 'yes') { ?> selected="" <?php } ?> value="yes">Paid</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-sm-12">
                                                        <label for="filter[created_by_user]">Created By (User)</label>
                                                        <select name="filter[created_by_user]" class="form-control" style="width: 100% !important;">
                                                            <?php
                                                            $created_by_user = '';
                                                            if (!empty($filter['created_by_user']))
                                                                $created_by_user = $filter['created_by_user'];
                                                            ?>
                                                            <option <?php if ($created_by_user == '') { ?> selected="" <?php } ?> value="">All</option>
                                                            <?php
                                                            foreach ($admin_users as $admin_user_id => $admin_user) { ?>
                                                                <option <?php if ($created_by_user == $admin_user_id) { ?> selected="" <?php } ?> value="<?=$admin_user_id?>"><?= $admin_user ?></option>
                                                            <?php }
                                                            ?>
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-sm-12" style="margin-top:34px;">
                                                        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                                                        <a href="<?= current_url(); ?>" class="btn btn-sm btn-default">Reset</a>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </form>
                                    <?php
                                }
                                ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div> -->
 <!-- filter section end-->
<div class="main-container container-fluid">
<!-- START ROW-1 -->
<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="card">
			<div class="card-header">
                <div class="dataTables_length" id="responsive-datatable_length">
                    <div class="btn-group btn-group-lg ms-auto">
                    <?php if (in_array('plans_view_price', $user_details->permissions)) { ?>
                        <button class="btn <?= in_array($view_page, array('plans', 'add_plan', 'add_pricing', 'view_pricing', 'copy_plan', 'view_pricing_custom', 'add_pricing_custom', 'add_custom_rules')) ? 'btn-sm btn-primary' : 'btn-sm btn-outline-primary'; ?>">
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('admin/plans/v/plans');?>">Pricing Plans</a>
                            </li>
                        </button>
                        <?php } ?>
                        <?php if (in_array('plans_edit_landing', $user_details->permissions)) { ?>
                            <button class="btn <?= ($view_page == 'landing') ? 'btn-sm btn-primary' : 'btn-sm btn-outline-primary'; ?>">
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('admin/plans/v/landing');?>">Standard Landing Price</a>
                            </li>
                        </button>
                        <?php } ?>
                        <?php if (in_array('plans_create', $user_details->permissions)) { ?>
                            <button class="btn <?= ($view_page == 'import_pricing') ? 'btn-sm btn-primary' : 'btn-sm btn-outline-primary'; ?>">
                            <li class="nav-item">
                                <a class="nav-link " href="<?= base_url('admin/plans/v/import_pricing');?>">Import Pricing</a>
                            </li>
                        </button>
                        <?php } ?>
                    </div>
                </div>
			</div>
            <div class="card-body">
                <?= $inner_content; ?>
            </div>
    </div>
</div>
<!-- END ROW-1 -->
</div>

