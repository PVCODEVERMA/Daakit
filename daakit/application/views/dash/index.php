<div class="row">
    <div class="col-lg-12">
        <div class="card m-b-30">
            <div class="card-header border-bottom">
                <div class="row">
                    <div class="col-sm-6">
                        <h4 class="m-b-0">
                            <i class="mdi mdi-view-dashboard"></i> Welcome, <?php echo $user_details->fname . ' ' . $user_details->lname; ?>!
                        </h4>
                    </div>
                    <div class="col-md-6 text-right">
                    </div>
                </div>
            </div>
            <div class="card-body bg-secondary">

                <div class="row">
                    <div class="col-sm-6">
                        <ul class="list-group">
                            <li class="list-group-item d-flex">
                                <i class="mdi mdi-arrow-left"></i> &nbsp; Please choose menu from the left
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include APPPATH . 'views/banner.php'; ?>