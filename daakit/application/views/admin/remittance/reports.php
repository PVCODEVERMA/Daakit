<style>
/* Hover state: font color changes to white */
a:hover {
    color: #fff !important; /* Change the font color to white */
}
</style>
<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">Remittance</h4>
    <ol class="breadcrumb d-flex flex-wrap">
    <?php
    switch ($report_type) {
        case 'receipt_history':
    ?>
        <li class="breadcrumb-item btn-list">
        <?php if (in_array('remittance_receipt_export', $user_details->permissions)) { ?>
            <a href="<?= base_url('admin/remittance/exportReceiptHistory'); ?><?php
                if (!empty($filter)) {
                echo "?" . http_build_query($_GET);
                }
                ?>" class="btn btn-info btn-sm me-2"><i class="fa fa-download" aria-hidden="true"></i> Retrieve Export </a>
        <?php } ?>
        <?php if (in_array('remittance_receipt_upload', $user_details->permissions)) { ?>
            <a href="<?= base_url('admin/remittance/reports/receipt_upload'); ?>" class="btn btn-info btn-sm me-2"><i class="fa fa-upload"></i> Upload Bank Receipts</a>
        <?php } ?>
        </li>
        <?php
            break;
        case 'remittance':
        ?>
            <li class="breadcrumb-item btn-list">
                <a href="<?= base_url('admin/remittance/bulkExportCSVRemittance'); ?><?php
                if (!empty($filter)) {
                    echo "?" . http_build_query($_GET);
                }
                ?>" class="btn btn-info btn-sm me-2"><i class="fa fa-download" aria-hidden="true"></i> Retrieve Export </a>
                <button type="button" class="btn btn-info btn-sm me-2" data-bs-toggle="modal" data-bs-target=".upload_utr_modal"><i class="fa fa-arrow-up-bold-circle"></i> Upload UTR</button>
                <a href="javascript:void(0);" class="btn btn-info btn-sm me-2" data-bs-toggle="sidebar-right" data-bs-target=".sidebar-right"><i class="fa fa-search" aria-hidden="true"></i> Filter </a>
            </li>
        <?php
            break;

        case 'seller':
        ?>
            <li class="breadcrumb-item btn-list">
                <a href="<?= base_url('admin/remittance/exportReportSellerPayable'); ?><?php if(!empty($filter)) { 
                echo "?" . http_build_query($_POST);
                } ?>" class="btn btn-info btn-sm me-2"><i class="fa fa-download" aria-hidden="true"></i> Retrieve Export </a>
                <a href="javascript:void(0);" class="btn btn-info btn-sm me-2" data-bs-toggle="sidebar-right" data-bs-target=".sidebar-right"><i class="fa fa-search" aria-hidden="true"></i> Filter </a>
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
</div>
 <!-- filter section end-->
<div class="main-container container-fluid">
<!-- START ROW-1 -->
<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="card">
			<div class="card-header">
                <div class="dataTables_length" id="responsive-datatable_length">
                    <div class="btn-group btn-group-lg ms-auto">
                    <?php if (in_array('remittance_receipt_upload', $user_details->permissions)) { ?>
                        <button class="btn <?= ($report_type == 'receipt_upload') ? 'btn-sm btn-primary' : 'btn-sm btn-outline-primary'; ?>">
                            <li class="nav-item">
                                <a class="btn <?= ($report_type == 'receipt_upload') ? 'btn-sm btn-primary' : 'btn-sm btn-outline-primary'; ?>" style="border: none;" href="<?php echo base_url('admin/remittance/reports/receipt_upload')?>"><span class="fw-bold">Receipts Upload</span></a>
                            </li>
                        </button>
                        <?php } ?>
                        <?php if (in_array('remittance_receipt_history', $user_details->permissions)) { ?>
                            <button class="btn <?= ($report_type == 'receipt_history') ? 'btn-sm btn-primary' : 'btn-sm btn-outline-primary'; ?>">
                            <li class="nav-item">
                                <a class="btn <?= ($report_type == 'receipt_history') ? 'btn-sm btn-primary' : 'btn-sm btn-outline-primary'; ?>" style="border: none;" href="<?php echo base_url('admin/remittance/reports/receipt_history')?>"><span class="fw-bold">Receipts History</span></a>
                            </li>
                        </button>
                        <?php } ?>
                        <?php if (in_array('seller_payable', $user_details->permissions)) { ?>
                            <button class="btn <?= ($report_type == 'seller') ? 'btn-sm btn-primary' : 'btn-sm btn-outline-primary'; ?>">
                            <li class="nav-item">
                                <a class="btn <?= ($report_type == 'seller') ? 'btn-sm btn-primary' : 'btn-sm btn-outline-primary'; ?>" style="border: none;" href="<?php echo base_url('admin/remittance/reports/seller')?>"><span class="fw-bold">Seller Payable</span></a>
                            </li>
                        </button>
                        <?php } ?>
                        <?php if (in_array('remittance_history', $user_details->permissions)) { ?>
                            <button class="btn <?= ($report_type == 'remittance') ? 'btn-sm btn-primary' : 'btn-sm btn-outline-primary'; ?>">
                            <li class="nav-item">
                                <a class="btn <?= ($report_type == 'remittance') ? 'btn-sm btn-primary' : 'btn-sm btn-outline-primary'; ?>" style="border: none;" href="<?php echo base_url('admin/remittance/reports/remittance')?>"><span class="fw-bold">Remittance</span></a>
                            </li>
                        </button>
                        <?php } ?>
                        <button class="btn btn-sm btn-outline-primary">
                            <li class="nav-item">
                                <a class="btn btn-sm btn-outline-primary" style="border: none;" href="javascript:void(0)"><span class="fw-bold">Total COD Payment </span><span style="font-weight: 900;">( <?php echo $remittance_data->courier_expected;?> )</span></a>
                            </li>
                        </button>
                        <button class="btn btn-sm btn-outline-primary">
                            <li class="nav-item">
                                <a class="btn btn-sm btn-outline-primary" style="border: none;" href="javascript:void(0)"><span class="fw-bold">Total COD Received </span><span style="font-weight: 900;">( <?php echo $remittance_data->receipt_uploaded;?> )</span></a>
                            </li>
                        </button>
                        <button class="btn btn-sm btn-outline-primary">
                            <li class="nav-item">
                                <a class="btn btn-sm btn-outline-primary" style="border: none;" href="javascript:void(0)"><span class="fw-bold">Total COD Remaining </span><span style="font-weight: 900;">( <?php if($remittance_data->courier_expected > $remittance_data->receipt_uploaded) { echo ($remittance_data->courier_expected-$remittance_data->receipt_uploaded);} else  {echo'0';}?> )</span></a>
                            </li>
                        </button>
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
<!--Change Status Popup start here-->
<div class="modal fade upload_utr_modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content" id="fulfillment_info">
            <form method="post" action="<?= base_url('admin/remittance/importUTR'); ?>" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="mySmallModalLabel">Upload UTR Sheet</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12" style="margin-bottom:20px">
                            Download sample upload file : <a class="text-info" href="<?= base_url('assets/utr_sheet.csv'); ?>">Download</a>
                        </div>
                        <div class="col-sm-12">
                            <div class="m-b-10">
                                <div class="input-group mb-3">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="importFile">
                                        <label class="custom-file-label" for="inputGroupFile02">Choose file</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-sm btn-primary">Upload</button>
                        <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('#getexporttype').on('change', function() {
        if (this.value != '') {
            window.open(this.value, '_blank');
        }
    });
</script>
