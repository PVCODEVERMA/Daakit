<div class="row m-t-15">
    <div class="col-sm-12">
        <form action="<?= current_url(); ?>" method="post" class="needs-validation">
            <div class="row">
                <div class="col-sm-12 ">
                    <div class="col-sm-4 ">
                        <form method="post" action="<?= current_url(); ?>" enctype="multipart/form-data">
                            <div class="form-row">
                                <div class="form-group col-sm-10">
                                    <label>Plan Name</label>
                                    <input type="text" required="" name="name" value="<?= set_value('name', (!empty($plan_details->plan_name) ? $plan_details->plan_name : '')) ?>" class="form-control" placeholder="">
                                    <input type="hidden" required="" name="plan_type" value="standard" class="form-control" placeholder="">
                                </div>
                                <div class="form-group col-sm-2">
                                    <button type="submit" style="margin-top: 28px;" class="btn btn-primary">Add Plan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>