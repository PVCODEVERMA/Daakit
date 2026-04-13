<!-- START PAGE-HEADER -->
<!-- <div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">Credit Notes</h4>
    <ol class="breadcrumb d-flex flex-wrap">
        <li class="breadcrumb-item btn-list">
            <a href="<?= base_url('billing/cod_remittance_export'); ?><?php if (!empty($filter)) {
                    echo "?" . http_build_query($_GET);
                } ?>" class="btn btn-info btn-sm me-2"> Retrieve Export--- </a>
        </li>
    </ol>
</div> -->
<!-- END PAGE-HEADER -->
<div class="main-container container-fluid">
    <!-- START ROW-1 -->
    <div class="row">
        <div class="col-md-12">
            <div class="card overflow-hidden">
                <div class="card-body p-0">
                    <div class="d-lg-flex">
                        <div class="border-end border-bottom bd-lg-b-0 d-flex flex-column mn-wd-20p">
                            <ul class="nav nav-pills main-nav-column p-3">
                                <li class="nav-item"><a class="nav-link"  href="<?php echo base_url('billing/version/seller_price_calculator');?>"><i class="fa fa-calculator" aria-hidden="true"></i>Cost Estimator</a></li>
                                <li class="nav-item"><a class="nav-link"  href="<?php echo base_url('billing/version/seller_recharge_logs');?>"><i class="fa fa-inr" aria-hidden="true"></i>Transactions</a></li>
                                <li class="nav-item"><a class="nav-link"  href="<?php echo base_url('billing/version/seller_cod_remittance');?>"><i class="fa fa-money"></i>COD Settlement</a></li>
                                <li class="nav-item"><a class="nav-link"  href="<?php echo base_url('billing/version/seller_shipping_charges');?>"><i class="fa fa-truck" aria-hidden="true"></i>Shipping Cost</a></li>
                                <li class="nav-item"><a class="nav-link"  href="<?php echo base_url('billing/version/seller_invoice');?>"><i class="fa fa-file-pdf-o" aria-hidden="true"></i>Billing Statement</a></li>
                                <li class="nav-item"><a class="nav-link active"  href="<?php echo base_url('billing/version/seller_credit_notes');?>"><i class="fa fa-file-pdf-o" aria-hidden="true"></i>Credit Notes</a></li>
                            </ul>
                        </div>
                        <div class="flex-grow-1">
                            <div class="tab-content">
                                <div class="tab-pane active" id="general">
                                    <div class="p-4 border-bottom">
                                        <h5 class="mb-0">Credit Notes</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered" data-order-col="2" data-order-type="desc">                    
                                                <thead>
                                                    <tr>
                                                        <th><span class="bold">Credit Notes No.</span></th>
                                                        <th><span class="bold">Credit Notes Date</span></th>
                                                        <th><span class="bold">Credit Notes Period</span></th>
                                                        <th><span class="bold">Credit Notes Service Type</span></th>
                                                        <th><span class="bold">Credit Notes Amount(&#8377;)</span></th>
                                                        <th><span class="bold">Action</span></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                        <?php
                                                        if (!empty($invoices)) {
                                                            foreach ($invoices as $invoice) {
                                                        ?>
                                                                <tr>
                                                                    <td><?= (!empty($invoice->invoice_no)) ? $invoice->invoice_no : 'NPPL/CN/' . sprintf('%03d', $invoice->id); ?></td>
                                                                    <td><?= date('d-m-Y', $invoice->created); ?></td>
                                                                    <td><?= ucwords($invoice->month); ?></td>
                                                                    <td><?= ucfirst($invoice->service_type) ?></td>
                                                                    <td>&#8377;<?= $invoice->total_amount; ?></td>
                                                                    <td>
                                                                        <a target="_blank" href="<?php echo base_url('download/force');?>?type=invoice&file=<?= $invoice->csv_file ?>" class="btn btn-sm btn-outline-primary"><i class="fa fa-file-excel-o" aria-hidden="true"></i></a>
                                                                        <a target="_blank" href="<?php echo base_url('download/force');?>?type=invoice&file=<?= $invoice->pdf_file ?>" class="btn btn-sm btn-outline-danger"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>
                                                                    </td>
                                                                </tr>
                                                            <?php
                                                        }
                                                        }else {
                                                                ?>
                                                        <tr>
                                                            <td colspan="6" class="text-center">No entries found</td>
                                                        </tr>
                                                    <?php } ?>
                                                    </tbody>
                                            </table>
                                        </div>
                                        <div class="row">
                                        <div class="col-md-4">
                                            <div class="dataTables_info" id="example-multi_info" role="status" aria-live="polite">Showing <?= $offset + 1; ?> to <?= $offset + $limit; ?> of <?= $total_records; ?> entries</div>
                                        </div>
                                        </div>
                                        <div class="row">
                                        <div id="colvis"></div>
                                        <div id="" class="dt-page-jump"></div>
                                        <div class="dataTables_paginate paging_simple_numbers" id="DataTables_Table_0_paginate">
                                            <ul class="pagination mb-0" style="float: right;">
                                                <?php if (isset($pagination)) { ?>
                                                    <?php echo $pagination ?>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                        </div>                                    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END ROW-1 -->
</div>
