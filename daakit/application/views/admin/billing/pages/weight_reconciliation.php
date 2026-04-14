<?php if (empty($shipments)) { ?>
    <div class="row m-t-30">
        <div class="col-sm-6">
            <form method="post" action="<?= current_url(); ?>" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group col-sm-6">
                        <label for="exampleFormControlFile1">Weight Reconciliation CSV File</label>
                        <input type="file" required="" name="importFile"  class="form-control-file" >
                        <small id="passwordHelpBlock" class="form-text">
                            <a href="assets/weight_file.csv" class="text-info">Download Sample File</a>
                        </small>
                    </div>
                    <div class="form-group col-sm-6">
                        <button type="submit" style="margin-top: 20px;" class="btn btn-primary">Upload</button>    
                    </div>
                </div>
            </form>
        </div>
    </div>

<?php } ?>
<?php if (!empty($shipments)) { ?>
    <form method="post" action="<?= base_url() . 'admin/billing/apply_weight'; ?>">
        <div class="row m-t-10">
            <div class="col-sm-6">
                - Select the shipments for weight Reconciliation
            </div>
            <div class="col-sm-6 text-right">
                <a href="<?= current_url(); ?>" class="btn btn-sm btn-outline-danger">Cancel</a>
                <button type="submit" class="btn btn-sm btn-outline-success">Apply Weight</button>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="table-responsive" style="margin-top: 20px;">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th><input data-switch="true" id="select_all_orders" checked="" type="checkbox"></th>
                                <th>Order ID</th>
                                <th>Product</th>
                                <th>Seller</th>
                                <th>AWB</th>
                                <th>Entered Weight(kg)</th>
                                <th>Charged Weight(kg)</th>
                                <th>Weight Diff.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($shipments as $shipment) {
                                if ($shipment->package_weight < $shipment->uploaded_weight) {
                                    ?>
                                    <tr>
                                        <td><input value="<?= $shipment->shipping_id; ?>" type="checkbox" checked="" class="order_id_checkbox" name="shipping_ids[]">
                                            <input type="hidden" value="<?= $shipment->uploaded_weight; ?>" name="uploaded_weight[<?= $shipment->shipping_id; ?>]"></td>
                                        <td><?= $shipment->order_id; ?></td>
                                        <td><?= $shipment->products; ?></td>
                                        <td><?= $shipment->company_name; ?></td>
                                        <td><a href="<?= base_url() ?>admin/shipping/all?filter[awb_no]=<?= $shipment->awb_number; ?>" target="_blank" class="text-info"><?= $shipment->awb_number; ?></a></td>
                                        <td><?= ($shipment->package_weight > 0) ? round($shipment->package_weight / 1000, 2) : '0'; ?></td>
                                        <td><?= ($shipment->uploaded_weight > 0) ? round($shipment->uploaded_weight / 1000, 2) : '0'; ?></td>
                                        <td><?= round(($shipment->uploaded_weight - $shipment->package_weight) / 1000, 2); ?></td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
<?php } ?>