
<div class="tab-pane active" id="general">
    <div class="p-4 border-bottom">
        <h5 class="mb-0">Weight Reconciliation CSV File</span></h5>
    </div>
    <div class="col-sm-6">
        <?php if (in_array('weight_upload', $user_details->permissions)) { ?>
            <form method="post" action="<?= current_url(); ?>" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group col-sm-6">
                        <label for="exampleFormControlFile1">&nbsp;</label>
                        <input type="file" required="" name="importFile" class="form-control">
                        <p style="margin-top: 10px; font-size: 14px;">Download sample file : <a class="text-info" href="<?php echo base_url('assets/weight_file.csv');?>"><i class="fa fa-download" aria-hidden="true"></i></a></p>
                    </div>
                    <div class="form-group col-sm-6">
                        <button type="submit" style="margin-top: 28px;" class="btn btn-primary">Upload</button>
                    </div>
                </div>
            </form>
        <?php } ?>
    </div>
</div>
