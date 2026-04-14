<div class="row m-b-10">
    <div class="col-sm-12 bg-info text-white p-t-10 m-b-10">
        <p><b>Weight discrepancies are auto accepted after 7 days.</b></p>
    </div>
</div>
<form method="get" action="<?= base_url('billing/v/weight_reconciliation') ?>">
    <div class="row" id="filter_row">

        <div class="col-sm-2">
            <label for="email">From Date:</label>
            <input type="text" autocomplete="off" data-start-date="<?= !empty($filter['start_date']) ? $filter['start_date'] : '' ?>" data-end-date="<?= !empty($filter['end_date']) ? $filter['end_date'] : '' ?>" class="form-control form-control-sm date-range-picker col-sm-12">
            <input type="hidden" autocomplete="off" id="date-min" name="filter[start_date]" value="<?= !empty($filter['start_date']) ? $filter['start_date'] : '' ?>" placeholder="from">
            <input type="hidden" autocomplete="off" id="date-max" name="filter[end_date]" value="<?= !empty($filter['end_date']) ? $filter['end_date'] : '' ?>" placeholder="to">
        </div>
        <div class="col-sm-2">
            <label class="font-secondary">AWB With Comma</label>
            <input type="text" autocomplete="off" name="filter[awb_no]" value="<?= !empty($filter['awb_no']) ? $filter['awb_no'] : '' ?>" class="form-control form-control-sm" placeholder="AWB Separated by comma">
        </div>
        <div class="form-group col-sm-2" style="margin-top:2px;">
            <label for="email">Product Name:</label>
            <input type="text" autocomplete="off" name="filter[product_name]" value="<?= !empty($filter['product_name']) ? $filter['product_name'] : '' ?>" class="form-control form-control-sm" placeholder="Product name to search">
        </div>
        <div class="form-group col-sm-3" style="margin-top:2px;">
            <label for="email">Courier Name:</label>
            <select name="filter[courier_id]" class="form-control js-select2" style="width: 100% !important;">
                <option value="">All</option>
                <?php if (!empty($couriers)) foreach ($couriers as $courier) { ?>
                    <option value="<?= $courier->id; ?>" <?php if (!empty($filter['courier_id']) && $filter['courier_id'] == $courier->id) { ?> selected="" <?php } ?>><?= ucwords($courier->name); ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-sm-3" style="margin-top:29px;">
            <button type="submit" class="btn btn-sm btn-outline-success">Apply</button>
            <a href="<?= base_url('billing/v/weight_reconciliation'); ?>" class="btn btn-sm btn-outline-primary">Clear</a>
        </div>

    </div>
</form>
<div class="row p-t-10 border-top m-t-10 p-b-10 action_row_selected sticky-top border-bottom" style="display: none;">
    <div class="col-sm-12">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text  border-dark"> <b class="multiple_select_count">0</b>&nbsp;selected</span>
            </div>
            <div class="input-group-append">
                <button class="btn btn-outline-dark accept_weight_bulk_button"> <i class="mdi mdi-message"></i> Accept Weight</button>
                <button class="btn btn-outline-dark create-weight-escalation-button"> <i class="mdi mdi-message"></i> Raise Dispute</button>


            </div>

        </div>
    </div>
</div>




<div class="table-responsive" style="margin-top: 20px;">
    <table class="table table-hover table-sm">
        <thead>
            <tr>
                <th><input data-switch="true" id="select_all_checkboxes" type="checkbox"></th>
                <th>Weight Applied Date</th>
                <th>Courier</th>
                <th>AWB Number</th>
                <th>Entered Weight</th>
                <th>Applied Weight</th>
                <th>Weight Charges</th>
                <th>Product</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($records)) {
                foreach ($records as $record) {
            ?>
                    <tr>
                        <td><input value="<?= $record->shipment_id; ?>" type="checkbox" class="multiple_checkboxes" name="shipping_ids[]"></td>
                        <td><?= date('Y-m-d', $record->weight_applied_date); ?></td>
                        <td><?= ucwords($record->courier_name); ?>
                        </td>
                        <td>
                            <?= $record->courier_name; ?><br>
                            <?= $record->awb_number; ?>
                        </td>
                        <td style="min-width:100px;"><b>Entered Weight</b> <?= (!empty($record->package_weight)) ? $record->package_weight : '0'; ?>g<br />
                            <?php if (!empty($record->calculated_weight)) {  ?><b>Charged Weight </b> <?= $record->calculated_weight; ?><br /> <?php } ?>
                            <b>LxBxH</b>: <?= (!empty($record->package_length)) ? $record->package_length : '10'; ?>x<?= (!empty($record->package_breadth)) ? $record->package_breadth : '10'; ?>x<?= (!empty($record->package_height)) ? $record->package_height : '10'; ?>
                        </td>
                        <td> <?= (!empty($record->charged_weight)) ? $record->charged_weight : '0'; ?>g
                        </td>
                        <td>
                            <?php
                            if ($record->pending_weight_charges > 0) {
                            ?>
                                <b>Forward</b> : <?= round($record->pending_weight_charges, 2); ?><br />
                                <?php if ($record->ship_status == 'rto') { ?>
                                    <b>RTO</b> : <?= round($record->pending_weight_charges, 2); ?><br />
                                <?php } ?>
                            <?php
                            } else {
                            ?>
                                <b>Forward</b> : <?= round($record->extra_weight_charges, 2); ?><br />
                                <?php if ($record->ship_status == 'rto') { ?>
                                    <b>RTO</b> : <?= round($record->rto_extra_weight_charges, 2); ?><br />
                                <?php } ?>
                            <?php } ?>
                        </td>
                        <td>
                            <span data-toggle="tooltip" data-html="true" title="<?= ucwords($record->product_name) ?>">
                                <?= ucwords(mb_strimwidth($record->product_name, 0, 30, "...")); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($record->weight_dispute_raised == '1') { ?>
                                <b>Dispute Raised</b>
                            <?php } elseif ($record->weight_dispute_accepted == '1') {
                            ?>
                                <b>Accepted</b>
                            <?php
                            } else if ($record->weight_applied_date > (time() - $dispute_time_limit)) { ?>
                                <button class="btn btn-sm btn-success accept_weight_button" data-toggle="modal" data-shipment-id="<?= $record->shipment_id; ?>">Accept</button>
                                <button class="btn btn-sm btn-info escalation-button" data-toggle="modal" data-target="#escilation-modal" data-shipment-id="<?= $record->shipment_id; ?>">Raise Dispute</button>

                            <?php } else { ?>
                                <b>Auto Accepted</b>
                            <?php } ?>
                        </td>

                    </tr>
                <?php
                }
            } else {
                ?>
                <tr>
                    <td class="text-center" colspan="11">No Records Found</td>
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

<!--Remark Popup start here-->
<div class="modal fade " id="escilation-modal" tabindex="-1" role="dialog" aria-labelledby="slideRightModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="slideRightModalLabel">Create Weight Dispute</h5>
            </div>
            <div class="modal-body">
                <form method="post" id="escalate_pickup">
                    <div class="row">
                        <div class="col-sm-12 p-b-10">
                            Request you to upload <b>photographs</b> of your shipment so we can escalate to the courier company.
                        </div>
                        <div class="col-lg-12">

                            <div class="form-group">
                                <input type="hidden" id="esc_shipment_id" value="" name="shipment_id">
                                <label>Remarks</label>
                                <textarea class="form-control" name="remarks" required placeholder="Enter Remark"></textarea>
                            </div>
                            <div class="form-group files">
                                <label>Attachments (If any) </label>
                                <input type="file" name="importFile[]" id="filetoupload" class="form-control" multiple="">
                                <small>Maximum file size : 5 MB</small>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $('.escalation-button').on('click', function(e) {
        var shipment_id = $(this).attr('data-shipment-id');
        $("#esc_shipment_id").val(shipment_id);
    });

    $('.accept_weight_button').on('click', function(e) {
        e.preventDefault();

        if (!confirm("Are you sure?"))
            return;

        var shipment_ids = [];

        var shipment_id = $(this).attr('data-shipment-id');

        shipment_ids.push(shipment_id);


        accept_weight_seller(shipment_ids);

    });

    function accept_weight_seller(shipment_ids = false) {
        if (!shipment_ids)
            return false;

        $.ajax({
            url: 'billing/accept_weight',
            type: "POST",
            data: {
                shipping_ids: shipment_ids,
            },
            cache: false,
            success: function(data) {
                if (data.success) {
                    location.reload();
                } else if (data.error)
                    alert(data.error);
            }
        });
    }

    $("#escalate_pickup").submit(function(event) {
        event.preventDefault();
        var form_data = new FormData(this);
        $.ajax({
            url: 'weight/raise_dispute',
            type: "POST",
            data: form_data,
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                if (data.success) {
                    location.reload();
                } else if (data.error)
                    alert(data.error);
            }
        });
    });

    $('.create-weight-escalation-button').on('click', function(e) {
        e.preventDefault();
        var shipment_ids = [];
        $.each($("input[class='multiple_checkboxes']:checked"), function() {
            shipment_ids.push($(this).val());
        });

        if (shipment_ids.length < 1) {
            alert('Please select records');
            return;
        }
        $("#esc_shipment_id").val(shipment_ids);
        $('#escilation-modal').modal('show');
    });

    $('.accept_weight_bulk_button').on('click', function(e) {
        e.preventDefault();

        if (!confirm("Are you sure?"))
            return;

        var shipment_ids = [];
        $.each($("input[class='multiple_checkboxes']:checked"), function() {
            shipment_ids.push($(this).val());
        });

        if (shipment_ids.length < 1) {
            alert('Please select records');
            return;
        }

        accept_weight_seller(shipment_ids);

    });
</script>