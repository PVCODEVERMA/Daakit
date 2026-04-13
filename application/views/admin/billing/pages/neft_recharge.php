<?php if (in_array('billing_neft_recharge', $user_details->permissions)) { ?>
    <form method="get" action="<?= base_url('admin/billing/v/neft_recharge') ?>">
        <div class="row" id="filter_row">
            <div class="col-sm-3">
                <input type="text" autocomplete="off" data-start-date="<?= !empty($filter['start_date']) ? $filter['start_date'] : '' ?>" data-end-date="<?= !empty($filter['end_date']) ? $filter['end_date'] : '' ?>" class="form-control date-range-picker col-sm-12 form-control-sm">
                <input type="hidden" autocomplete="off" id="date-min" name="filter[start_date]" value="<?= !empty($filter['start_date']) ? $filter['start_date'] : '' ?>" placeholder="from">
                <input type="hidden" autocomplete="off" id="date-max" name="filter[end_date]" value="<?= !empty($filter['end_date']) ? $filter['end_date'] : '' ?>" placeholder="to">

            </div>
            <div class="form-group col-sm-3">
                <select name="filter[seller_id]" class="form-control getUserlist form-control-sm" style="width: 100% !important;">
                    <option value="">Select Seller</option>
                    <?php
                    if (!empty($users)) {
                        foreach ($users as $values) {
                            $sellerid = '';
                            if (!empty($filter['seller_id']))
                                $sellerid = $filter['seller_id'];
                    ?>
                            <option <?php if ($sellerid == $values->id) { ?> selected <?php } ?> value="<?php echo $values->id; ?>"><?php echo $values->id . ' - ' . ucwords($values->user_fname . ' ' . $values->user_lname); ?> (<?php echo ucwords($values->company_name) ?>)</option>
                    <?php
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="form-group col-sm-2" style="margin-top:2px;">
                <input type="text" name="filter[utr_no]" value="<?= !empty($filter['utr_no']) ? $filter['utr_no'] : '' ?>" class="form-control" placeholder="UTR Number">
            </div>

            <div class="col-sm-3">
                <button type="submit" class="btn btn-sm btn-outline-success">Apply</button>
                <a href="<?= base_url('admin/billing/v/neft_recharge'); ?>" class="btn btn-sm btn-outline-primary">Clear</a>
            </div>


        </div>
    </form>

    <div class="table-responsive" style="margin-top: 20px;">
        <table class="table table-hover table-sm">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Seller ID</th>
                    <th>Seller Name</th>
                    <th>Amount</th>
                    <th>UTR Number</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($neftpament)) {
                    $i = 1;
                    foreach ($neftpament as $neft) {
                ?>
                        <tr>
                            <td><?= (!empty($neft->created)) ? date('M d, Y', $neft->created) : ''; ?></td>
                            <td><?= $neft->userid; ?></td>
                            <td><?= ucwords($neft->company_name . ' (' . $neft->user_fname . ' ' . $neft->user_lname . ')'); ?></td>
                            <td><?= $neft->amount ?></td>
                            <td><?= ucwords($neft->utr_number); ?></td>
                        </tr>
                    <?php
                        $i++;
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

<?php } ?>