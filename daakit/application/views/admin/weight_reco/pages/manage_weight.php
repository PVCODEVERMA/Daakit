<div class="flex-grow-1">
    <div class="tab-content">
        <div class="tab-pane active" id="general">
            <div class="p-4 border-bottom">
                <h5 class="mb-0">Manage Weight</span></h5>
            </div>
            <div class="card-body" style="margin-top: -22px;">
                <div class="table-responsive">
                    <table class="table table-bordered" data-order-col="2" data-order-type="desc">                    
                        <thead>
                            <tr>
                                <th><span class="bold">Courier</span></th>
                                <th><span class="bold">Seller</span></th>
                                <th><span class="bold">Date</span></th>
                                <th><span class="bold">Seller Wgt.</span></th>
                                <th><span class="bold">Courier Wgt.</span></th>
                                <th><span class="bold">Add. Details</span></th>
                                <th><span class="bold">Status</span></th>
                                <th><span class="bold">Action</span></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($records)) {
                                foreach ($records as $record) { ?>
                                    <tr>

                                        <td><?= ucwords($record->courier_name); ?><br /> <a target="_blank" class="text-primary" href="<?php echo base_url('shipping/tracking');?>/<?= $record->awb_number ?>"><b><?= $record->awb_number; ?></b></a></td>
                                        <td><a class="text-primary" href="<?php echo base_url('admin/users/viewuser');?>/<?= $record->user_id; ?>" target="_blank"> <?= ucwords($record->company_name); ?> (<?= ucwords($record->fname . ' ' . $record->lname); ?>) </a> </td>
                                        <td style="display: contents;">
                                            <b>Upload Date:</b> <?= date('Y-m-d', $record->upload_date); ?><br />
                                            <b>Applied Date:</b> <?= ($record->apply_weight_date > 0) ? date('Y-m-d', $record->apply_weight_date) : 'N/A' ?>
                                        </td>
                                        <td style="min-width:100px;">

                                            <b>Dead Wgt.</b> <?= $record->seller_dead_weight; ?> g<br />
                                            <b>Vol. Wgt.</b>: <?= $record->seller_volumetric_weight; ?> g<br />
                                            <b>Booked Slab. </b> <?= $record->seller_booking_weight; ?> g
                                        </td>
                                        <td style="min-width:150px;"><b>Billed Wgt</b> <?= $record->courier_billed_weight; ?> g<br />
                                            <b>Vol. Wgt</b> <?= $record->courier_vol_weight; ?> g<br />
                                            <b>LxBxH:</b> <?= (!empty($record->courier_length)) ? $record->courier_length : '0'; ?>x<?= (!empty($record->courier_breadth)) ? $record->courier_breadth : '0'; ?>x<?= (!empty($record->courier_height)) ? $record->courier_height : '0'; ?><br />
                                            <b>Applied Slab</b>: <?= $record->weight_new_slab; ?> g
                                        </td>
                                        <td style="min-width:150px;">
                                            <b>Extra Wgt.</b>: <?= $record->upload_weight_difference; ?> g<br />

                                            <b>Extra Wgt. Charges</b>: Rs.<?= $record->weight_difference_charges; ?><br />
                                            <?php if ($record->ship_status == 'rto') { ?><b>RTO Extra Wgt. Charges</b>: Rs.<?= $record->weight_difference_charges; ?><br /><?php } ?>
                                            <b>Wgt. Applied</b>: <?= ($record->weight_applied == '1') ? 'Yes' : 'No'; ?><br />
                                            <b>Charged to Wallet</b>: <?= ($record->applied_to_wallet == '1') ? 'Yes' : 'No'; ?>
                                        </td>
                                        <td><a href="javascript:void(0)"  class="btn btn-sm btn-outline-primary"><?= ucwords($record->seller_action_status); ?></a></td>
                                        <td>
                                            <?php
                                            $url =
                                                array(
                                                    'filter' => array(
                                                        'start_date' => '2019-10-01',
                                                        'end_date' => date('Y-m-d'),
                                                        'awb_no' => $record->awb_number,
                                                    ),
                                                );
                                            ?>
                                            <div class="btn btn-sm btn-success ms-auto" style="margin-top: 1px;">
                                                <div class="item-action dropdown">
                                                    <a href="javascript:void(0)" data-bs-toggle="dropdown" class="icon show"  style="color: #ffffff;">
                                                    View More <i class="fa fa-level-down" aria-hidden="true"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-right" data-popper-placement="bottom-start" style="position: absolute; inset: 0px auto auto 0px; margin: 0px; transform: translate3d(0px, 14.4px, 0px);">
                                                        <a href="javascript:void(0)" onclick="filter_data_status('shipment')"  class="dropdown-item">View Shipment</a>
                                                        <a href="javascript:void(0)" onclick="filter_data_status('charges')"   class="dropdown-item">Shipping Charges</a>
                                                        <?php if (!empty($record->dispute_id)) {
                                                            $url = array(
                                                                'filter' => array(
                                                                    'start_date' => '2019-10-01',
                                                                    'end_date' => date('Y-m-d'),
                                                                    'escalation_id' => $record->dispute_id
                                                                )
                                                            );
                                                        ?>
                                                            <a href="<?php echo base_url('admin/escalations/v/weight');?>?<?= http_build_query($url) ?>" target="_blank" class="dropdown-item">View Escalation</a>
                                                            <a href="javascript:void(0)" data-escalation-id="<?= $record->dispute_id; ?>"  class="dropdown-item show_escalation_remarks_button">View Escalation Remarks</a>
                                                        <?php } ?>  
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="8"> No records found
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                    </table>
                    <div class="col-sm-12 col-md-12"></div>
                    <div class="col-sm-12 col-md-12">
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
<form method="post" id="tab_filter" >
    <input type="hidden" name="perPage" id="perPage" value="<?= !empty($_POST['perPage']) ? $_POST['perPage'] : '' ?>" />
    <input type="hidden" autocomplete="off" name="filter[start_date]"  value="2019-10-01">
    <input type="hidden" autocomplete="off" name="filter[end_date]"  value="<?= date('Y-m-d'); ?>">
    <input type="hidden" autocomplete="off" name="filter[awb_no]"  value="<?= $record->awb_number; ?>">
    <input type="hidden" name="page" id="page" value="1">
</form>
<!--Remark Popup start here-->
<div class="modal fade bulk_action_popup" tabindex="-1" role="dialog" aria-labelledby="slideRightModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="slideRightModalLabel">Bulk Action</h5>
            </div>
            <div class="modal-body">
                <form method="post" action="<?= base_url('admin/weight_reco/bulk_action_import'); ?>" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Select Action</label>
                                <select name="action" required class="form-control">
                                    <option value="">Choose one</option>
                                    <?php if (in_array('weight_apply', $user_details->permissions)) { ?>
                                        <option value="apply_weight">Apply Weight (Hold Remittance)</option>
                                        <option value="charge_to_wallet">Apply & Charge to Wallet</option>
                                    <?php } ?>
                                    <?php if (in_array('weight_revert_charges', $user_details->permissions)) { ?>
                                        <option value="revert_extra_charges">Remove Extra Charges</option>
                                    <?php } ?>

                                    <?php if (in_array('weight_close_dispute', $user_details->permissions)) { ?>
                                        <option value="close_dispute_seller_favour">Close Dispute in Seller Favour</option>
                                        <option value="close_dispute_courier_favour">Close Dispute in Courier Favour</option>
                                        <option value="close_dispute_new_weight">Close Case with Modified Weight Details</option>
                                    <?php } ?>
                                    <?php if (in_array('weight_credit_notes', $user_details->permissions)) { ?>
                                        <option value="issue_credit_note">Issue Credit Note</option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="form-group files">
                                <label>Attachments </label>
                                <input type="file" name="importFile" id="filetoupload" class="form-control">
                                <small>Maximum file size : 5 MB</small>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    Close
                                </button>
                            </div>
                        </div>
                        <div class="col-sm-12 p-b-10 border-top p-t-20"><br>
                            Download sample upload file for action type :<br />
                            <b>Close Case with Modified Weight Details</b>: <a class="text-info" href="<?php echo base_url('assets/custom_weight_action.csv');?>"><i class="fa fa-download" aria-hidden="true"></i> Download</a><br />
                            <b>All Remaining</b>: <a class="text-info" href="<?php echo base_url('assets/weight_action.csv');?>"><i class="fa fa-download" aria-hidden="true"></i> Download</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade escalation_remarks_modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="slideRightModalLabel">Remarks History</h5>
            </div>
            <div class="modal-body row_scroll" id="escalation_remarks_modal_data">

            </div>
        </div>
    </div>
</div>
<script>
    function filter_data_status(filterType) {
        const form = document.getElementById('tab_filter');
        if (filterType === 'shipment') {
            form.action = "<?= base_url('admin/shipping/list') ?>";
        } else if (filterType === 'charges') {
            form.action = "<?= base_url('admin/billing/v/shipping_charges') ?>";
        }
        form.target = "_blank";
        form.submit();
    }
</script>

<script>
    <?php unset($_GET['perPage']); ?>

    function per_page_records(per_page = false) {
        var page_url = '<?= base_url('admin/weight_reco/v/manage') . '?' . http_build_query($_GET) . '&perPage=' ?>' + per_page;
        window.location.href = page_url;
    }
    $("#bulk_action_import_form").submit(function(event) {
        event.preventDefault();
        var form_data = new FormData(this);
        $.ajax({
            url: 'admin/weight_reco/bulk_action_import',
            data: form_data,
            type: "POST",
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                if (data.success) {
                    alert('Import Successfull');
                } else if (data.error)
                    alert(data.error);
            }
        });
    });

    $('.show_escalation_remarks_button').on('click', function(event) {
        event.preventDefault();
        var escalation_id = $(this).attr('data-escalation-id');
        $.ajax({
            url: 'admin/escalations/escalation_history/' + escalation_id,
            type: "GET",
            datatype: "JSON",
            cache: false,
            success: function(data) {
                $('#escalation_remarks_modal_data').html(data);
                $('.escalation_remarks_modal').modal('show');
            }
        });
    });
</script>