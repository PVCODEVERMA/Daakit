<?php
ob_start();?>
<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">Channel Details</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item">						
        <a href="<?= base_url('channels/add');?>" class="btn btn-info btn-sm pull-left display-block mright5"> New Channel </a>
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
				<h3 class="card-title">Channel</h3>
			</div>
			<div class="card-body">
            <div class="table-responsive">
                <table class="table card-table table-bordered table-vcenter text-dark table-outline text-nowrap">
                    <thead>
                        <tr>
                            <th>Channel Image</th>
                            <th>Channel Name</th>
                            <th>Store Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($channels)) {
                            foreach ($channels as $channel) {
                        ?>
                                <tr>
                                    <td>
                                        <div class="avatar avatar-sm ">
                                            <?php
                                            switch (strtolower($channel->channel)) {
                                                case 'shopify':
                                                    echo '<img src="assets/channel_images/shopify.jpg" class="avatar-img avatar-sm rounded-circle" alt="">';
                                                    break;
                                            }
                                            ?>
                                        </div>
                                    </td>
                                    <td><?= (strtolower($channel->channel) == 'shopify_oneclick') ? 'Shopify' :  ucfirst($channel->channel); ?></td>
                                    <td><?= ucfirst($channel->channel_name); ?></td>
                                    <td>
                                        <a href="<?php echo base_url('channels/edit/');?><?= $channel->id; ?>" class="btn  btn-primary btn-sm"><i class="mdi mdi-lead-pencil"></i> Edit</a>
                                        <a href="<?php echo base_url('channels/delete/');?><?= $channel->id; ?>" onclick=" return confirm('Are you sure?');" class="btn btn-danger btn-sm"><i class="mdi mdi-delete-forever"></i> Delete</a>
                                    </td>
                                </tr>
                            <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="4" style="text-align:center">No Records found</td>
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