<!-- START PAGE-HEADER -->
<!-- <div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">COD Settlement</h4>
    <ol class="breadcrumb d-flex flex-wrap">
        <li class="breadcrumb-item btn-list">
            <a href="<?= base_url('billing/cod_remittance_export'); ?><?php if (!empty($filter)) {
                    echo "?" . http_build_query($_GET);
                } ?>" class="btn btn-info btn-sm me-2"> Retrieve Export </a>
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
                                <li class="nav-item"><a class="nav-link active"  href="<?php echo base_url('billing/version/seller_cod_remittance');?>"><i class="fa fa-money"></i>COD Settlement</a></li>
                                <li class="nav-item"><a class="nav-link"  href="<?php echo base_url('billing/version/seller_shipping_charges');?>"><i class="fa fa-truck" aria-hidden="true"></i>Shipping Cost</a></li>
                                <li class="nav-item"><a class="nav-link"  href="<?php echo base_url('billing/version/seller_invoice');?>"><i class="fa fa-file-pdf-o" aria-hidden="true"></i>Billing Statement</a></li>
                                <li class="nav-item"><a class="nav-link"  href="<?php echo base_url('billing/version/seller_credit_notes');?>"><i class="fa fa-file-pdf-o" aria-hidden="true"></i>Credit Memos</a></li>
                            </ul>
                        </div>
                        <div class="flex-grow-1">
                            <div class="tab-content">
                                <div class="tab-pane active" id="general">
                                    <div class="p-4 border-bottom">
                                        <h5 class="mb-0">Settlement</h5>
                                    </div>
                                    <div class="card-body"  style="margin-top: -22px;">
                                    <div class="row">
                                            <div class="col-sm-6 col-md-6 action_row_default">
                                                <div class="dataTables_length" id="responsive-datatable_length">
                                                    <div class="btn-group btn-sm ms-auto">
                                                        <button  class="btn btn-sm btn-outline-primary" aria-label="Show unshipped orders">Remitted Till Date : <b>&#8377;</b><?= (!empty($remitted_amount)) ? round($remitted_amount) : '0' ?></button>
                                                        <button  class="btn btn-sm btn-outline-primary" aria-label="Show unshipped orders">Last Remittance : <b>&#8377;</b><?= (!empty($last_remittance)) ? round($last_remittance) : '0' ?></button>
                                                        <button  class="btn btn-sm btn-outline-primary" aria-label="Show unshipped orders">Next Remittance<small>(Expected)</small> : <b>&#8377;</b><?= (!empty($next_remittance->total_due)) ? round($next_remittance->total_due) : '0' ?></button>
                                                        <button  class="btn btn-sm btn-outline-primary" aria-label="Show unshipped orders">Total Remittance Due : <b>&#8377;</b><?= (!empty($total_remittance_due->total_due)) ? round($total_remittance_due->total_due) : '0' ?></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered" data-order-col="2" data-order-type="desc">                    
                                                <thead>
                                                    <tr>
                                                        <th>Settlement Details</th>
                                                        <th>Settlement AMOUNT</th>
                                                        <th>COD Amount</th>
                                                        <th>Status</th>
                                                        <th>Freight Deductions</th>
                                                        <th>Convenience Fee</th>
                                                        <th>Download</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                if (!empty($history)) {
                                                    $i = 1;
                                                    foreach ($history as $his) {
                                                ?>
                                                        <tr>
                                                            <td>#<?= $i; ?>
                                                            SID : <?= $his->id; ?><br>
                                                            REF NO. : <?= $his->utr_number; ?></br>
                                                            <?= (!empty($his->payment_date)) ? date('M d, Y', $his->payment_date) : ''; ?></td>
                                                            <td>&#8377;<?= (!empty($his->remittance_amount)) ? round($his->remittance_amount, 2) : 0; ?></td>
                                                            <td>&#8377;<?= round($his->amount, 2); ?></td>
                                                            <td><?= ($his->paid == '1') ? 'Paid' : 'Pending'; ?></td>
                                                            <td>&#8377;<?= (!empty($his->freight_deductions)) ? round($his->freight_deductions) : 0 ?></td>
                                                            <td>&#8377;<?= round($his->convenience_fee, 2); ?></td>
                                                            <td><a href="<?php echo base_url('remittance/exportAWB/');?><?= $his->id ?>" class="btn btn-sm btn-outline-info"><i class="fa fa-download"></i></a></td>
                                                        </tr>
                                                    <?php
                                                        $i++;
                                                    }
                                                } else {
                                                    ?>
                                                    <tr>
                                                        <td colspan="9" class="text-center">No Records Found</td>
                                                    </tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                            <div class="col-sm-12 col-md-12"></div>
                                            <div class="col-sm-12 col-md-12">
                                                <div class="dataTables_paginate paging_simple_numbers" id="responsive-datatable_paginate">
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
    </div>
    <!-- END ROW-1 -->
</div>
<form id="tab_filter" action="<?php base_url('billing/version/seller_recharge_logs');?>" method="POST">
    <input type="hidden" name="perPage" id="perPage" />
</form>
<!-- SCROLLING WITH COTENT MODAL END -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function per_page_records(per_page = false) {
        document.getElementById('perPage').value=per_page;
        document.getElementById('tab_filter').submit();
    }
  $(document).ready(function() {
      $('#responsive-datatable').DataTable({
          "aoColumnDef": [
              null,
              null,
              null,
              null,
              null,
              {
                  "sType": "numeric"
              },
              null,
              {
                  "sType": "string"
              },
              null,
              null,
              null,
              null
          ],
          aoColumnDefs: [{
              orderable: false,
              aTargets: [0]
          }],
          'aaSorting': [
              [3, 'desc']
          ],
          "paging": false, // false to disable pagination (or any other option)
          "filter": false,
          "info": false,
      });

  });
</script> 
