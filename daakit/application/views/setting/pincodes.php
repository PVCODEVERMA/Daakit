<div class="row">
    <div class="col-lg-12">
        <div class="card m-b-30">
            <div class="card-header border-bottom">
                <div class="row">
                    <div class="col-sm-6">
                        <h4 class="m-b-0">
                            <i class="mdi mdi-map-marker"></i> Download Pincodes List
                        </h4>
                    </div>
                    <div class="col-md-6 text-right">

                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row m-t-30">
                    <div class="col-sm-12">
                        <form method="post" action="<?= current_url(); ?>">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Pickup Pincode (For Zone Mapping)</label>
                                        <input type="text" required class="form-control" name="pickup_pincode" placeholder="">
                                    </div>
                                    <div class="form-group text-right">
                                        <button type="submit" class="btn btn-info"><i class="mdi mdi-download"></i> Download</button>
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