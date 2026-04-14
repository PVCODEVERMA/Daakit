<style>
/* Hover state: font color changes to white */
a:hover {
    color: #fff !important; /* Change the font color to white */
}
</style>
<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">Receipt #<?= $receipt->id; ?></h4>
    <ol class="breadcrumb d-flex flex-wrap">
        <li class="breadcrumb-item btn-list">
			<a href="<?php echo base_url('admin/remittance/reports/receipt_history');?>" class="btn btn-info btn-sm me-2" data-bs-toggle="sidebar-right" data-bs-target=".sidebar-right"><i class="fa fa-backward" aria-hidden="true"></i> Back </a>
        </li>
    </ol>
</div>
<!-- END PAGE-HEADER -->
<div class="main-container container-fluid">
<!-- START ROW-1 -->
<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="card">
            <div class="card-body">
                <?= $inner_content; ?>
            </div>
    </div>
</div>
<!-- END ROW-1 -->
</div>
