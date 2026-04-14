<?php if (in_array('remittance_receipt_upload', $user_details->permissions)) { ?>
    <div class="row m-t-30">
        <div class="col-sm-6">
            <form method="post" action="<?= current_url(); ?>" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group col-sm-6">
                        <label>Carrier</label>
                        <select required="" name="courier_id" class="form-control">
                            <option value="" selected="">Please Select</option>
                            <?php
                            if (!empty($couriers))
                                foreach ($couriers as $courier) {
                            ?>
                                <option <?php if (set_value('courier_id') == $courier->id) { ?> selected="" <?php } ?> value="<?= $courier->id ?>"><?= ucwords($courier->name); ?></option>
                            <?php } ?>

                        </select>
                    </div>
                    <div class="form-group col-sm-6">
                        <label>Date</label>
                        <div class="input-group mb-2">
                            <input type="date" required="" name="date" autocomplete="off" class="form-control js-datepicker col-sm-12" data-date-format="yyyy-mm-dd" value="<?= set_value('date') ?>" placeholder="YYYY-MM-DD">
                        </div>
                    </div>
                    <div class="form-group col-sm-6">
                        <label>Amount</label>
                        <input type="text" required="" name="amount" value="<?= set_value('amount') ?>" class="form-control" placeholder="">

                    </div>
                    <div class="form-group col-sm-6">
                        <label>UTR Number</label>
                        <input type="text" required="" name="utr_number" value="<?= set_value('utr_number'); ?>" class="form-control" placeholder="">
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="exampleFormControlFile1">Receipt CSV File</label>
                        <input type="file" required="" name="importFile" class="form-control">
                        <small id="passwordHelpBlock" class="form-text">
                            <a href="<?php echo base_url('assets/receipt_upload.csv');?>" class="text-info">Download Sample File</a>
                        </small>
                    </div>
                    <div class="form-group col-sm-6">
                        <button type="submit" style="margin-top: 28px;text-align:right" class="btn btn-primary">Upload</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php } ?>