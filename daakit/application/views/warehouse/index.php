<?php
ob_start();?>
<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">Warehouse Details</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item">						
        <a href="<?= base_url('warehouse/add');?>" class="btn btn-info btn-sm pull-left display-block mright5"> New Warehouse </a>
		</li>
    </ol>
</div>
<!-- END PAGE-HEADER -->
<div class="main-container container-fluid">
<!-- START ROW-1 -->
<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">Warehouse</h3>
			</div>
			<div class="card-body">
            <div class="table-responsive">
                <table class="table card-table table-bordered table-vcenter text-dark table-outline text-nowrap" data-order-col="2" data-order-type="desc">                    
                    <thead>
                        <tr>
                            <th><span class="bold">Warehouse Name</span></th>
                            <th><span class="bold">Contact Person</span></th>
                            <th><span class="bold">Warehouse Details</span></th>
                            <th><span class="bold">Action</span></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                            if (!empty($warehouses)) {
                                foreach ($warehouses as $warehouse) {
                            ?>
                                    <tr>
                                        <td><?= ucfirst($warehouse->name); ?></td>
                                        <td><?= ucfirst($warehouse->contact_name); ?><br />
                                            <i class="mdi mdi-cellphone"></i> <?= $warehouse->phone; ?>
                                        </td>
                                        <td>
                                            <?= !empty($warehouse->address_2)? $warehouse->address_1 . '<br/> ' . $warehouse->address_2:$warehouse->address_1 ?><br />
                                            <?= $warehouse->city . ', ' . $warehouse->state ?><br />
                                            <?= $warehouse->country . ' - ' . $warehouse->zip ?><br />
                                            GST No: <?= $warehouse->gst_number ?>
                                        </td>
                                        <td>
                                            <a href="warehouse/add/<?= $warehouse->id; ?>" class="btn btn-info btn-sm"><i class="mdi mdi-lead-pencil"></i> Edit</a>
                                            <?php if ($warehouse->active == '1') { ?>
                                                <a href="warehouse/toggle_status/<?= $warehouse->id; ?>" class="btn btn-success btn-sm"><i class="mdi mdi-check"></i>Active</a>
                                            <?php } else { ?>
                                                <a href="warehouse/toggle_status/<?= $warehouse->id; ?>" class="btn btn-warning btn-sm"><i class="mdi mdi-close"></i> Inactive</a>
                                            <?php } ?>
                                            <?php if ($warehouse->is_default == '1') { ?>
                                                <a href="warehouse/toggle_default/<?= $warehouse->id; ?>" class="btn btn-success btn-sm"><i class="mdi mdi-check"></i> Primary Warehouse</a>
                                            <?php } else { ?>
                                                <a href="warehouse/toggle_default/<?= $warehouse->id; ?>" class="btn btn-warning btn-sm"><i class="mdi mdi-check"></i> Make Primary Warehouse</a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php
                                }
                            } else {
                                ?>
                                <tr>
                                    <td colspan="4" style="text-align:center">No entries found.</td>
                                </tr>
                            <?php } ?>

                    </tbody>
                </table>
            </div>
        </div>
		</div>
	</div>
</div>
<!-- END ROW-1 -->
</div>
<script>
  <?php unset($_GET['perPage']); ?>

function per_page_records(per_page = false) {
    var page_url = '<?= base_url('warehouse') . '?' . http_build_query($_GET) . '&perPage=' ?>' + per_page;
    window.location.href = page_url;
}
</script>