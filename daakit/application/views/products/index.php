<style>
    .dimnsion {
        font-size: .9rem;
        font-weight: 400;
        line-height: 1.5;
        width: 25%;
        height: calc(1.5em + .75rem + 2px);
        padding: .375rem .75rem;
        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        color: #2e384d;
        border: 1px solid #dce4ec;
        border-radius: .25rem;
        background-color: #fff;
        background-clip: padding-box;

    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="card m-b-30">
            <div class="card-header">
                <div class="row">
                    <div class="col-sm-6">
                        <h4 class="m-b-0"><i class="mdi mdi-checkbox-intermediate"></i> Products SKU Mapping </h4>
                    </div>
                    <div class="col-sm-6 text-right">
                        <?php if (in_array('orders_export', $user_details->permissions)) { ?>
                            <a href="<?= base_url('products/exportCSV'); ?><?php if (!empty($filter)) {
                                                                                echo "?" . http_build_query($_GET);
                                                                            } ?>" class="btn btn-outline-dark btn-sm"><i class="mdi mdi-arrow-down-bold-circle"></i> Export</a>
                        <?php } ?>
                        <button class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target=".import_bulk_skumapping_modal"> <i class="mdi mdi-arrow-up-bold-circle"></i> Import</button>
                        <button type="button" class="btn btn-outline-dark show_hide_filter btn-sm" <?php if (!empty($_GET['filter'])) { ?> style="display:none;" <?php } ?>><i class="icon-placeholder mdi mdi-filter"></i> Filters</button>
                        <button type="button" class="btn btn-outline-dark show_hide_filter btn-sm" <?php if (empty($_GET['filter'])) { ?> style="display:none;" <?php } ?>><i class="icon-placeholder mdi mdi-close"></i> Close</button>
                    </div>
                </div>

            </div>
            <div class="card-body">
                <form method="get" action="<?= base_url('products/all') ?>">
                    <div class="row" id="filter_row" <?php if (empty($_GET['filter'])) { ?> style="display:none;" <?php } ?>>
                        <div class="col-sm-12">
                            <div class="card m-b-30">
                                <div class="card-body">
                                    <div class="form-row">
                                        <div class="col-sm-4">
                                            <label for="email"><span data-toggle="tooltip" data-html="true" title="" data-original-title="Search by Product name or SKU Details">Search by Product name or SKU Details:</span></label>
                                            <input type="text" autocomplete="off" name="filter[search_query]" value="<?= !empty($filter['search_query']) ? $filter['search_query'] : '' ?>" class="form-control" placeholder="Search by Product name or SKU">
                                        </div>
                                        <div class="form-group col-sm-4" style="margin-top:32px;">
                                            <button type="submit" class="btn btn-sm btn-primary">Apply</button>
                                            <a href="<?= base_url('products/all'); ?>" class="btn btn-sm btn-default">Clear</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>


                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th width="20%" align="center">Dimensions (LBH) in CM</th>
                                <th>Weight</th>
                                <th>GST %</th>

                                <th>HSN</th>

                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($orders)) {
                                foreach ($orders as $order) {

                                   //  pr($order);//exit();
                            ?>

                                    <tr>

                                        <form class="formid_update_products" method="post">

                                            <td><?php
                                                $products = $order->products;
                                                ?>
                                                <span data-toggle="tooltip" data-html="true" title="<?= $products; ?>">
                                                    <?= mb_strimwidth($products, 0, 14, "..."); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if (!empty($order->product_sku)) { ?>
                                                    <input type="text" name="product_sku" class="form-control" placeholder="SKU" readonly value="<?php echo $order->product_sku; ?>">
                                                <?php } else { ?>
                                                    <input type="text" name="product_sku" class="form-control" placeholder="SKU" value="<?php echo $order->prod_sku; ?>">
                                                <?php } ?>
                                                <input type="hidden" name="product_name" class="form-control" value="<?php if (!empty($order->products)) {
                                                                                                                            echo $order->products;
                                                                                                                        } ?>">
                                                <input type="hidden" name="product_id" class="form-control" value="<?php if (!empty($order->prod_id)) {
                                                                                                                        echo $order->prod_id;
                                                                                                                    } ?>">
                                            </td>
                                            <td>
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1" style="background-color: #edf2f9;">LBH</span>
                                                    </div>
                                                    <input type="text" name="length" maxlength="4" class="dimnsion numbers" placeholder="CM" value="<?php if (!empty($order->prod_length)) {
                                                                                                                                                        echo $order->prod_length;
                                                                                                                                                    } ?>">
                                                    <input type="text" name="breadth" class="dimnsion numbers" maxlength="4" placeholder="CM" value="<?php if (!empty($order->prod_breadth)) {
                                                                                                                                                            echo $order->prod_breadth;
                                                                                                                                                        } ?>">
                                                    <input type="text" name="height" class="dimnsion numbers" maxlength="4" placeholder="CM" value="<?php if (!empty($order->prod_height)) {
                                                                                                                                                        echo $order->prod_height;
                                                                                                                                                    } ?>">

                                                </div>



                                            </td>

                                            <td>
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text" style="background-color: #edf2f9;">Weight</div>
                                                    </div>
                                                    <input type="text" name="weight" class="dimnsion numbers" placeholder="gram" maxlength="6" value="<?php if (!empty($order->prod_weight)) {
                                                                                                                                                            echo $order->prod_weight;
                                                                                                                                                        } ?>">

                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text" style="background-color: #edf2f9;">GST</div>
                                                    </div>
                                                    <input type="text" name="igst" class="dimnsion numbers" maxlength="10" placeholder="" value="<?php if (!empty($order->prod_igst)) {
                                                                                                                                                        echo $order->prod_igst;
                                                                                                                                                    } ?>">
                                                </div>

                                            </td>
                                            <td>
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text" style="background-color: #edf2f9;">HSN</div>
                                                    </div>

                                                    <input type="text" maxlength="10" name="hsn" class="dimnsion numbers" placeholder="" style="width: 45% !important" value="<?php if (!empty($order->prod_hsn_code)) {
                                                                                                                                                                                    echo $order->prod_hsn_code;
                                                                                                                                                                                } ?>">
                                                </div>
                                            </td>
                                            <td align="center">
                                                <button type="submit" class="btn btn-primary">Update</button>


                                            </td>
                                        </form>
                                    </tr>

                                <?php
                                }
                            } else {
                                ?>
                                <tr>
                                    <td colspan="11" class="text-center">No Records found</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
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
                        <ul class="pagination" style="float: right;margin-right: 40px;">
                            <?php if (isset($pagination)) { ?>
                                <?php echo $pagination ?>
                            <?php } ?>
                        </ul>

                    </div>
                </div>
            </div>
        </div>
    </div>
  
<div class="modal fade import_bulk_skumapping_modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content" id="fulfillment_info">
            <form method="post" action="<?= base_url('products/import'); ?>" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="mySmallModalLabel">Bulk Upload Product SKU Mapping</h5>
                </div>
                <div class="modal-body">
                    <div class="row">



                        <div class="col-sm-12 p-b-10">
                            Download sample Product SKU Mapping upload file : <a class="text-info" href="<?= base_url('assets/bulk_product_sku_mapping_sample.csv'); ?>">Download</a>
                        </div>
                        <div class="col-sm-12 m-t-10">
                            <div class="m-b-10">
                                <div class="input-group mb-3">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="importFile">
                                        <label class="custom-file-label" for="inputGroupFile02">Choose file</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                      <!--  <div class="col-sm-12 m-t-10">
                            <div class="m-b-10">
                                <div class="form-group input-group mb-3">
                                    <div class="custom-control custom-checkbox custom-control-inline">
                                        <?= form_checkbox('check_duplicates', '1', set_value('check_duplicates', false), 'class="custom-control-input" id="customCheckDup"'); ?>
                                        <label class="custom-control-label" for="customCheckDup">Check Duplicate Order IDs (Only for new orders) </label>

                                    </div>
                                </div>
                            </div>
                        </div>-->
                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-right">
                            <button type="submit" class="btn btn-primary">Upload</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>

                  <!--  <div class="row border-top m-t-20 m-b-10">
                        <div class="col-sm-12 p-t-10 text-center">
                            <b>Bulk Order Update</b>
                        </div>
                        <div class="col-sm-12 p-t-10">
                            For bulk orders update export orders and import the file after updates.<br />
                        </div>

                    </div>
                    <div class="row">
                        <iframe width="490" style="margin: 5px;border-radius: 5px;" height="315" src="https://www.youtube.com/embed/f3Ic8Iin3zI" title="How to Bulk Orders Upload in deltagloabal?" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                </div>-->
            </form>

        </div>
    </div>
</div>
</div>
<script>
    <?php unset($_GET['perPage']); ?>

    function per_page_records(per_page = false) {
        var page_url = '<?= base_url('products/all') . '?' . http_build_query($_GET) . '&perPage=' ?>' + per_page;
        window.location.href = page_url;
    }
    $(".formid_update_products").submit(function(event) {
        event.preventDefault();
        $.ajax({
            url: "products/productUpdate",
            type: "POST",
            data: $(this).serialize(),
            cache: false,
            success: function(data) {
                    if(data.success){
                        alert(data.success);
                        // location.reload();
                    }
                    else{
                        alert(data.error);
                    }

            }
        });
    });

    $(document).ready(function() {
        $('.numbers').keyup(function() {
            this.value = this.value.replace(/[^0-9\.]/g, '');
        });
    });
</script>
