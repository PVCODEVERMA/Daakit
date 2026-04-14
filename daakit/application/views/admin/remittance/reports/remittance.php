<div class="row">
    <div class="table-responsive">
        <table class="table table-bordered no-wrap">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Remittance ID</th>
                    <th>Company-Seller Id</th>
                    <th>COD Amount</th>
                    <th>Created</th>
                    <th>Paid</th>
                    <th>Freight Deductions</th>
                    <th>Remittance Amount</th>
                    <th>Convenience Fee</th>
                    <th>Payment Date</th>
                    <th>UTR No.</th>
                    <th>Created By</th>
                    <th>Download</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($remittances)) {
                    $i = 1;
                    foreach ($remittances as $remittance) {
                ?>
                        <tr>
                            <td><?= $i; ?></td>
                            <td><?= $remittance->id; ?></td>
                            <td><?= ucwords(!empty($remittance->user_company) ? $remittance->user_company : $remittance->user_fname . ' ' . $remittance->user_lname);  echo "</br>(".$remittance->user_id.")"; ?></td>
                            <td>&#8377;<?= round($remittance->amount, 2); ?></td>
                            <td><?= date('M d, Y', $remittance->created); ?></td>
                            <td><?= ($remittance->paid == '1') ? 'Yes' : 'No'; ?></td>
                            <td>&#8377;<?= !empty($remittance->freight_deductions) ? round($remittance->freight_deductions, 2) : '0'; ?></td>
                            <td>&#8377;<?= !empty($remittance->remittance_amount) ? round($remittance->remittance_amount, 2) : '0'; ?></td>
                            <td>&#8377;<?= !empty($remittance->convenience_fee) ? round($remittance->convenience_fee, 2) : '0'; ?></td>
                            <td><?= !empty($remittance->payment_date) ? date('M d, Y', $remittance->payment_date) : ''; ?></td>
                            <td><?= $remittance->utr_number; ?></td>
                            <td><?= ($remittance->seller_created == '1') ? 'Seller' : 'Daakit ' . (!empty($admin_users[$remittance->created_by]) ?  '('.$admin_users[$remittance->created_by]. ')' : '') ; ?></td>
                            <td><a href="<?php echo base_url("admin/remittance/exportRemittanceAWB/$remittance->id")?>" class="btn btn-sm btn-outline-info"><i class="fa fa-download"></i></a></td>

                        </tr>
                <?php
                        $i++;
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-sm-12 col-md-6">
        <div class="dataTables_info" id="example-multi_info" role="status" aria-live="polite">Showing <?= $offset + 1; ?> to <?= $offset + $limit; ?> of <?= $total_records; ?> entries</div>
    </div>
    <div class="col-sm-12 col-md-6">
        <ul class="pagination" style="float: right;
            margin-right: 0px;">
            <?php if (isset($pagination)) { ?>
                <?php echo $pagination ?>
            <?php } ?>
        </ul>
    </div>
</div>