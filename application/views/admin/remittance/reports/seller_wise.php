    <form action="<?= current_url(); ?>" method="post">

        <div class="row m-t-10">
            <div class="col-sm-6">
                <input type="hidden" name="createdremittance" id="allseller" value="1">
                <button type="submit" class="btn btn-sm btn-outline-success"><i class="mdi mdi-plus"></i>Create Remittance</button>
            </div>
        </div>
        <br>
        <div class="row  m-t-10">
            <div class="col-sm-12 table-responsive">
                <table class="table table-bordered no-wrap">
                    <thead>
                        <tr>
                            <th><input data-switch="true" id="select_all_orders" type="checkbox"></th>
                            <th>Seller Id</th>
                            <th>Company Name</th>
                            <th>Wallet Balance</th>
                            <th>On Hold</th>
                            <th>Total Remittance Pending</th>
                            <th>Remittance Cycle</th>
                            <th>Remittance Amount</th>
                            <th>Received from courier</th>
                            <th>Expected</th>
                            <th>Early Paid</th>
                            <th>Expected Due</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($records)) {
                            foreach ($records as $record) {
                        ?>
                                <tr>
                                    <td><input value="<?= $record->user_id ?>" type="checkbox" class="order_id_checkbox" name="user_ids[]"></td>
                                    <td><?= $record->user_id; ?></td>
                                    <td><?= ucwords(!empty($record->user_company) ? $record->user_company : $record->user_fname . ' ' . $record->user_lname); ?></td>
                                    <td><?= round($record->wallet_balance, 2); ?></td>
                                    <td><?= round($record->remittance_on_hold_amount, 2); ?></td>
                                    <td><?= round($record->total_cod_due, 2); ?></td>
                                    <td>T+<?= $record->remittance_cycle; ?></td>
                                    <td>
                                        <span class="float-left">
                                            <?= round($record->remittance_cycle_total, 2); ?>
                                        </span>
                                        <span class="float-right">
                                            <?php if ($record->remittance_cycle_total > 0) { ?>
                                            <?php } ?>
                                        </span>
                                    </td>

                                    <td>
                                        <span class="float-left">
                                            <?= round($record->receipt_uploaded, 2); ?>
                                        </span>
                                        <span class="float-right">
                                            <?php if ($record->receipt_uploaded > 0) { ?>
                                            <?php } ?>
                                        </span>
                                    </td>
                                    <td><?= round($record->seller_expected, 2); ?></td>
                                    <td><?= round($record->early_paid, 2); ?></td>
                                    <td>
                                        <span class="float-left">
                                            <?= $due = round($record->seller_expected - $record->early_paid, 2); ?>

                                        </span>
                                        <span class="float-right">
                                            <?php if ($due > 0) { ?>
                                            <?php } ?>
                                        </span>
                                    </td>

                                </tr>
                        <?php
                            }
                        }
                        else{
                            ?>
                                <tr>
                                    <td colspan="12" style="color:red; text-align:center">No record found.</td>
                                </tr>
                            <?php
                        }
                        ?>

                    </tbody>
                </table>
            </div>

        </div>
    </form>
<div class="modal fade view_awb_list" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" id="seller_awb_list_modal">

        </div>
    </div>
</div>