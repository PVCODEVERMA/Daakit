
<b>Closing Dues: &#8377;<?= (!empty($last_txn) ? $last_txn->closing_due : '0') ?></b>
<div class="table-responsive" style="margin-top: 20px;">
    <table class="table table-hover table-sm">
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Ref No.</th>
                <th>Amount</th>
                <th>Closing Due</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($history)) {

                foreach ($history as $his) {
                    ?>
                    <tr>
                        <td><?= (!empty($his->created)) ? date('Y-m-d', $his->created) : ''; ?></td>
                        <td><?php
                            switch ($his->transaction_type) {
                                case 'cod':
                                    echo 'COD Remittance Deductions';
                                    break;
                                case 'credits':
                                    echo 'Advance Credits';
                                    break;
                                case 'invoice':
                                    echo 'Invoice';
                                    break;
                            }
                            ?></td>
                        <td><?= ($his->reference_number != '0') ? $his->reference_number : '-'; ?></td>
                        <td>&#8377;<?php
                            if ($his->credit_debit == 'debit') {
                                echo '-';
                            } echo round($his->amount, 2);
                            ?></td>
                        <td>&#8377; <?= round($his->closing_due, 2) ?></td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr>
                    <td colspan="5" class="text-center">No Records Found</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<div class="row">

    <div class="col-sm-12 col-md-6">
        <div class="dataTables_info" id="example-multi_info" role="status" aria-live="polite">Showing <?= $offset + 1; ?> to <?= $offset + $limit; ?> of <?= $total_records; ?> entries</div>
    </div>
    <div class="col-sm-12 col-md-6">
        <ul class="pagination" style="float: right;">
            <?php if (isset($pagination)) { ?>
                <?php echo $pagination ?>
            <?php } ?>
        </ul>

    </div>
</div>
