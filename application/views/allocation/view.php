<!-- <div class="row">
    <div class="col-lg-12">
        <div class="card m-b-30">
            <div class="card-header">
                <div class="row">
                    <div class="col-sm-6">
                        <h4><i class="mdi mdi-checkbox-intermediate"></i> Order Allocation Engine---
                        </h4>
                    </div>
                    <div class="col-sm-6 text-right">
                        <?php if ($view_page == 'edit_rule') { ?>
                            <a href="allocation/v/rules" class="btn btn-info btn-sm"><i class="mdi mdi-plus-circle"></i> Add New Rule</a>
                        <?php } ?>
                    </div>
                </div>

            </div>
            <div class="card-body">
                <ul class="nav nav-tabs m-b-15">
                    <li class="nav-item">
                        <a class="nav-link <?= ($view_page == 'rules' || $view_page == 'edit_rule') ? 'active' : '' ?> " href="allocation/v/rules">Shipping Rules</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($view_page == 'test_rule') ? 'active' : '' ?> " href="allocation/v/test_rule">Rule Testing</a>
                    </li>

                </ul> -->
                <?= $inner_content; ?>
            <!-- </div>
        </div>
    </div>
</div> -->