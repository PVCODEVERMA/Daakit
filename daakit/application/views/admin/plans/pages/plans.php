<div class="row">
    <div class="col-sm-12">
        <div class="table-responsive">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Plan Name</th>
                            <th>Plan Type</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($plans)) {
                            foreach ($plans as $plan) {
                        ?>
                        <tr>
                            <td><b><?= ucwords($plan->plan_name); ?></b></td>
                            <td><b><?= ucwords(str_replace('_', ' ', $plan->plan_type)); ?></b></td>
                            <td class="align-middle">
                                <div class="btn btn-sm btn-success ms-auto" style="margin-top: 1px;">
                                    <div class="item-action dropdown">
                                        <a href="javascript:void(0)" data-bs-toggle="dropdown" class="icon" aria-expanded="false" style="color: #ffffff;">
                                            View More <i class="fa fa-level-down" aria-hidden="true"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a href="<?= base_url('admin/plans/v/view_pricing/');?><?= $plan->id; ?>" class="dropdown-item">View Pricing</a>
                                            <?php if (in_array('plans_create', $user_details->permissions)) { ?>
                                            <a href="<?= base_url('admin/plans/v/add_pricing/');?><?= $plan->id; ?>" class="dropdown-item">Edit Pricing</a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php } } else { ?>
                        <tr>
                            <td colspan="8" class="text-center">No Records Found</td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url();?>assets/build/assets/plugins/vendors/jquery.min.js"></script>
<script>
$('#exportBtn').click(function() {
    var plan_type = $("#plan_type").val();
    var filter = '?filter[plan_type]='+plan_type;
    window.open("<?= base_url('admin/plans/v/export_plans'); ?>" + filter,"_blank");
});                    
</script>