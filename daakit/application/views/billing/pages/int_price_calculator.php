<style>
    .table_highlight .table-bordered td,
    .table_highlight .table-bordered th {
        border: 1px solid #8a8b8d !important;
        vertical-align: middle;
    }

    .table_highlight .table.table-bordered td {
        padding: 0px;
    }

   .table_highlight .table.table-bordered td p {
        margin-bottom: 0;
        line-height: 28px;
        padding: 2px 0px;
    }

   .table_highlight .table.table-bordered td p span {
        display: block;
        border-top: 1px solid #ddd;
    }
</style>

<div class="row">
    <div class="col-sm-6">
        <form id="int_pricing_calculator_form" method="post" action="javascript:;">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="m-b-0" style="text-align:center;">
                        International Shipping Rates Calculator
                    </h5>
                </div>
            </div>
            <div class="row m-t-20">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Pick-up Country</label>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><i class="mdi mdi-map-marker"></i></div>
                            </div>
                            <select required="" name="origin" class="form-control">
                                <option value="">Select Origin</option>
                                <option value="India">India (IN)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Delivery Country</label>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><i class="mdi mdi-map-marker"></i></div>
                            </div>
                            <select required="" name="destination" class="form-control">
                                <option value="">Select Destination</option>
                                <?php foreach ($countries as $country) { ?>
                                <option value="<?= $country->name; ?>"><?= $country->name . ' (' . $country->iso . ')'; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Weight</label>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text">Kg</div>
                            </div>
                            <input type="text" name="weight" class="form-control" required="" value="0.5" placeholder="">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label>L(cm)</label>
                        <input type="text" name="length" class="form-control" required="" value="10" placeholder="cm">
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label>H(cm)</label>
                        <input type="text" name="height" class="form-control" required="" value="10" placeholder="cm">
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label>B(cm)</label>
                        <input type="text" name="breadth" class="form-control" required="" value="10" placeholder="cm">
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Value in INR</label>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text">&#8377;</div>
                            </div>
                            <input type="text" name="cod_amount" class="form-control" placeholder="e.g 1000">
                        </div>
                    </div>
                </div>
                <div class="row m-t-20">
                    <div class="col-sm-12 text-center">
                        <button type="submit" style="margin-top: 8px;margin-left: 15px;" name="submit" class="btn btn-primary"><i class="mdi mdi-calculator"></i> Calculate</button>
                    </div>
                </div>
            </div>
            <div class="table-responsive m-t-30">
                <table class="table table-bordered table-sm text-left table-hover" id="calculated_price" style="display:none;">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Carrier</th>
                            <th>Courier Charges</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-right">*GST Additional</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </form>
    </div>
    <div class="col-sm-12 p-all-15 border-top m-t-15">
        <p><b>Terms & Conditions:</b></p>
        <ul>
            <li>Above Shared Commercials are Exclusive GST.</li>
            <li>Carriage of shipment is subject to destination country restrictions.</li>
            <li>Custom duty shall be additional as per destination country and it needs to be payable immediately.</li>
            <li>deltagloabal reserves the right to inspect shipment prior to carriage.</li>
            <li>Applicable charges will be based on the volumetric or actual weight, whichever is higher.</li>
            <li>Volumetric weight calculation (CMS): (L * B * H) / 5000</li>
            <li>For Fedex commercial shipment INR 950 + GST will be extra</li>
            <li>Address correction charges for Fedex 1550 + GST or 10 Rs per kg, whichever is higher</li>
            <li>For TNT commercial shipment INR 1200 + GST will be extra</li>
            <li>Address correction charges for TNT 1550 + GST or 10rs per kg, whichever is higher</li>
            <li>For DHL commercial shipment INR 2350 + GST will be extra</li>
            <li>Address correction charges for DHL 950 + GST.</li>
            <li>For Aramex commercial shipment INR 2500 + GST will be extra</li>
            <li>Address correction charges for Aramex 50 + GST.</li>
            <li>Maximum liability for lost/shortage is USD 100 only or Invoice value, whichever is lower.</li>
            <li>Any commercial shipment connect our Dedicated Sales Team.</li>
            <li>Due to Covid-19 outbreak Global countries are affected, please expect delay in all inbound and outbound shipments. All pickups, clearance and Deliveries are affected.</li>
            <li>delta Post will make every reasonable effort to deliver the shipment but delta Post is not liable for any damages or loss caused by delay.</li>
        </ul>
    </div>
</div>