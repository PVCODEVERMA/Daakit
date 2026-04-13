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
        <form id="b2b_pricing_calculator_form" method="post" action="javascript:;">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="m-b-0" style="text-align:center;">
                        B2B Shipping Rates Calculator
                    </h5>
                </div>
            </div>
            <div class="row m-t-20">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Pick-up Pincode</label>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><i class="mdi mdi-map-marker"></i></div>
                            </div>
                            <input type="text" name="origin" class="form-control" required="" placeholder="">
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Delivery Pincode</label>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><i class="mdi mdi-map-marker"></i></div>
                            </div>
                            <input type="text" name="destination" class="form-control" required="" placeholder="">
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
                            <input type="text" name="weight" class="form-control" required="" value="5" placeholder="">
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
        </form>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="table-responsive m-t-30">
            <table class="table table-bordered table-sm text-left table-hover" id="calculated_price" style="display:none;">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Carrier</th>
                        <th>Courier Charges</th>
                        <th>Courier Charges (Bifurcation)</th>
                        <th>Transportation Id</th>
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
    </div>
</div>

<div class="col-sm-12 p-all-15 border-top m-t-15">
    <p><b>Terms & Conditions:</b></p>
    <ul>
        <li>Above Shared Commercials are Exclusive GST.</li>
        <li>Above pricing subject to change based on courier company updation or change in any commercials.</li>
        <li>Freight Weight is Picked - Volumetric or Dead weight whichever is higher will be charged.</li>
        <li>Other charges like address correction charges if applicable shall be charged extra.</li>
        <li>Prohibited item not to be ship, if any penalty will charge to seller.</li>
        <li>No Claim would be entertained for Glassware, Fragile products,</li>
        <li>Concealed damages and improper packaging.</li>
        <li>Any weight dispute due to incorrect weight declaration cannot be claimed.</li>
        <li>Chargeable weight would be volumetric or actual weight, whichever is higher
            <br />
            <strong>Xpressbees:</strong> (LxBxH/27000)*7
            <br />
            <strong>Bluedart:</strong> LxBxH/5000
            <br />
        </li>
    </ul>
</div>