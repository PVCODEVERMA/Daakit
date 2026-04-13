<div class="row m-b-20">
    <div class="col-sm-12 text-right">
        <?php if (in_array('pincodes_export', $user_details->permissions)) { ?>
            <a href="<?= base_url('admin/pincodes/v/listexport'); ?><?php if (!empty($filter)) { echo "?" . http_build_query($_GET); } ?>" class="btn btn-outline-dark btn-sm"><i class="mdi mdi-arrow-down-bold-circle"></i> Export</a>
        <?php } ?>
        <button type="button" class="btn btn-outline-dark show_hide_filter btn-sm" <?php if (!empty($filter)) { ?> style="display:none;" <?php } ?>><i class="icon-placeholder mdi mdi-filter"></i> Filters</button>
        <button type="button" class="btn btn-outline-dark show_hide_filter btn-sm" <?php if (empty($filter)) { ?> style="display:none;" <?php } ?>><i class="icon-placeholder mdi mdi-close"></i> Close</button>
    </div>
</div>
<form method="get" action="<?= base_url('admin/pincodes/v/list') ?>">
    <div class="row" id="filter_row" <?php if (empty($filter)) { ?> style="display:none;" <?php } ?>>
        <div class="col-sm-12">
            <div class="card m-b-30">
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-sm-4" style="margin-top:2px;">
                            <label for="email">Pin Code:</label>
                            <input type="text" name="filter[pincode]" value="<?= !empty($filter['pincode']) ? $filter['pincode'] : '' ?>" class="form-control" placeholder="Pin Code to Search">
                        </div>
                        <div class="form-group col-sm-4" style="margin-top:2px;">
                            <label for="email">Courier Name:</label>
                            <select name="filter[courier_id]" class="form-control">
                                <option value="">All</option>
                                <?php if (!empty($couriers)) foreach ($couriers as $courier) { ?>
                                    <option value="<?= $courier->id; ?>" <?php if (!empty($filter['courier_id']) && $filter['courier_id'] == $courier->id) { ?> selected="" <?php } ?>><?php echo ucwords($courier->name);echo isset(($courier->courier_alias)) ? " (".ucfirst($courier->courier_alias).")" : ""; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group col-sm-2" style="margin-top:32px;">
                            <button type="submit" class="btn btn-sm btn-primary">Apply</button>
                            <a href="<?= base_url('admin/pincodes/v/list'); ?>" class="btn btn-sm btn-default">Clear</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="table-responsive">
    <div class="table-responsive">
        <table class="table table-hover table-sm">
            <thead>
                <tr>

                    <th>Pincode</th>
                    <th>Courier</th>
                    <th>City Name</th>
                    <th>State</th>
                    <th>COD Status</th>
                    <th>Prepaid Status</th>
                    <th>Pickup Status</th>
                    <th>Reverse Pickup</th>
                    <th>Area Code</th>
                    <th>Action</th>

                </tr>
            </thead>
            <tbody>
                <?php if (!empty($pincodes)) {
                    foreach ($pincodes as $pincode) {
                ?>
                        <tr>
                            <td><?= $pincode->pincode; ?></td>
                            <td><?php echo ucwords($pincode->courier_name); echo isset(($pincode->courier_alias)) ? " (".ucfirst($pincode->courier_alias).")" : ""; ?></td>
                            <td><?= ucwords($pincode->city); ?></td>
                            <td><?= ucwords($pincode->state_code); ?></td>
                            <td><?= ucwords($pincode->cod); ?></td>
                            <td><?= ucwords($pincode->prepaid); ?></td>
                            <td><?= ucwords($pincode->pickup); ?></td>
                            <td><?= ucwords($pincode->is_reverse_pickup); ?></td>
                            <td><?= strtoupper($pincode->area_code); ?></td>
                            <td>
                                <?php if (in_array('import_pincodes', $user_details->permissions)) { ?>
                                    <button data-pincode-id="<?= $pincode->id; ?>" class="btn btn-outline-info btn-sm change_pincode_settings">Edit</button>
                                <?php } ?>
                            </td>

                        </tr>

                    <?php
                    }
                } else { ?>
                    <tr>
                        <td colspan="8" class="text-center">No Records Found</td>
                    </tr>
                <?php
                } ?>
            </tbody>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-md-1">
        <?php
        $per_page_options = array(
            '10' => '10',
            '20' => '20',
            '50' => '50',
            '100' => '100',
            '200' => '200',
            '500' => '500',
        );

        $js = "class='form-control' onchange='per_page_records(this.value)'";
        echo form_dropdown('per_page', $per_page_options, $limit, $js);
        ?>
    </div>

    <div class="col-sm-12 col-md-4">
        <div class="dataTables_info" id="example-multi_info" role="status" aria-live="polite">Showing <?= $offset + 1; ?> to <?= $offset + $limit; ?> of <?= $total_records; ?> entries</div>
    </div>
    <div class="col-sm-12 col-md-7">
        <ul class="pagination" style="float: right;
						margin-right: 0px;">
            <?php if (isset($pagination)) { ?>
                <?php echo $pagination ?>
            <?php } ?>
        </ul>

    </div>
</div>


<div class="modal fade edit_pincode_modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content" id="edit_pincode_modal_content">

        </div>
    </div>
</div>

<script type="text/javascript">
    <?php unset($_GET['perPage']); ?>

    function per_page_records(per_page = false) {
        var page_url = '<?= base_url('admin/pincodes/v/list') . '?' . http_build_query($_GET) . '&perPage=' ?>' + per_page;
        window.location.href = page_url;

    }

    $('.change_pincode_settings').on('click', function(e) {
        e.preventDefault();
        var pincode_id = $(this).attr('data-pincode-id');

        $.ajax({
            url: 'admin/pincodes/edit_pincode',
            type: "POST",
            cache: false,
            data: {
                pincode_id: pincode_id,
            },
            success: function(data) {

                $('.edit_pincode_modal').modal('show');
                $("#edit_pincode_modal_content").html(data);
            }

        });


    });
</script>