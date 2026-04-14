<?php
$is_franchise = (!empty($user_details->is_franchise) && ($user_details->is_franchise == 'yes')) ? 1 : 0;
$international_permission = (!empty($user_details->international_permission) && ($user_details->international_permission == '1')) ? 1 : 0;
?>
<!-- <div class="row">
    <div class="col-lg-12">
        <div class="card m-b-30">
            <div class="card-header">
                <div class="row">
                    <div class="col-sm-6">
                        <h4>Billing
                            <?php
                            switch ($view_page) {
                                case 'price_calculator':
                                    echo ' - Price Calculator';
                                    break;
                                case 'cod_remittance':
                                    echo ' - COD Remittance';
                                    break;
                                case 'recharge_logs':
                                    echo ' - Recharge Logs';
                                    break;
                                case 'shipping_charges':
                                    echo ' - Shipping Charges';
                                    break;
                                case 'invoice':
                                    echo ' - Invoice';
                                    break;
                                case 'credit_notes':
                                    echo ' - Credit Notes';
                                    break;
                                case 'weight_reconciliation':
                                    echo ' - Weight Reconciliation';
                                    break;
                                case 'b2b_price_calculator':
                                    echo ' - B2B Price Calculator';
                                    break;
                                case 'int_price_calculator':
                                    echo ' - International Price Calculator';
                                    break;

                                default:
                            }
                            ?>
                        </h4>
                    </div>
                    <div class="col-sm-6 text-right">
                        <?php
                        switch ($view_page) {
                            case 'shipping_charges':
                        ?>
                                <a href="billing/shipping_charges_export<?php if (!empty($filter)) { echo "?" . http_build_query($_GET); } ?>" class="btn btn-outline-dark btn-sm"><i class="mdi mdi-arrow-down-bold-circle"></i> Export</a>
                            <?php
                                break;
                            case 'recharge_logs':
                            ?>
                                <a href="billing/recharge_logs_export<?php if (!empty($filter)) { echo "?" . http_build_query($_GET); } ?>" class="btn btn-outline-dark btn-sm"><i class="mdi mdi-arrow-down-bold-circle"></i> Export</a>
                            <?php
                                break;
                            case 'weight_reconciliation':
                            ?>
                                <a href="billing/weight_rec_export<?php if (!empty($filter)) { echo "?" . http_build_query($_GET); } ?>" class="btn btn-outline-dark btn-sm"><i class="mdi mdi-arrow-down-bold-circle"></i> Export</a>
                        <?php
                                break;
                        }
                        ?>
                    <button class="btn btn-sm btn-success" style="background:#12263f!important;border: 1px solid #12263F!important;" title="Process Bulk Orders" style="cursor: pointer;color: #A94442;" data-toggle="modal" data-target="#exampleModalprocessorders"><i class="mdi mdi-comment-question"></i></button>
                    </div>
                </div>

            </div>
            <div class="card-body">
                <ul class="nav nav-tabs m-b-15">
                    <li class="nav-item">
                        <a class="nav-link <?= ($view_page == 'price_calculator') ? 'active' : '' ?> " href="billing/v/price_calculator"><i class="mdi mdi-calculator"></i> Price Calculator---</a>
                    </li>
                   
                    <li class="nav-item">
                        <a class="nav-link <?= ($view_page == 'cod_remittance') ? 'active' : '' ?> " href="billing/v/cod_remittance"><i class="mdi mdi-cash"></i> COD Remittance</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($view_page == 'recharge_logs') ? 'active' : '' ?>" href="billing/v/recharge_logs"><i class="mdi mdi-wallet"></i> Wallet Transactions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($view_page == 'shipping_charges') ? 'active' : '' ?>" href="billing/v/shipping_charges"><i class="mdi mdi-truck-fast"></i> Shipping Charges</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($view_page == 'invoice') ? 'active' : '' ?>" href="billing/v/invoice"><i class="mdi mdi-file-pdf"></i> Invoice</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($view_page == 'credit_notes') ? 'active' : '' ?>" href="billing/v/credit_notes"><i class="mdi mdi-file-pdf"></i> Credit Notes</a>
                    </li>
                   <li class="nav-item">
                        <a class="nav-link <?= ($view_page == 'weight_reconciliation') ? 'active' : '' ?>" href="billing/v/weight_reconciliation"><i class="mdi mdi-weight"></i> Weight Reconciliation</a>
                    </li>
                </ul>
                <?= $inner_content; ?>
            </div>
        </div>
    </div>
</div> 

<div class="modal fade" id="exampleModalprocessorders" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">How to Raise COD Remittance Request on the seller Panel?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="row">
                <iframe width="490" style="margin: 5px;border-radius: 5px;margin-left: 20px;" height="315" src="https://www.youtube.com/embed/6YaYAgjZPEw" title="How to Proccess Bulk orders in deltagloabal?" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
        </div>
    </div>
</div>