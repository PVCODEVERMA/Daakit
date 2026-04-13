<style>
.lines {
    position: relative;
    font-size: 20px;
    font-family: sans-serif;
    margin-bottom: 20px;
    border-top: 3px solid rebeccapurple;
    margin-top: 20px;
}
.lines:before{
    content: attr(data-text);
    background-color: #fff;
    position: absolute;
    text-align: center;
    left: 55%;
    width: 150px;
    margin-left: -110px;
    padding: 5px;
    top: -20px;
}
.lines2 {
    position: relative;
    font-size: 20px;
    font-family: sans-serif;
    margin-bottom: 20px;
    border-top: 3px solid rebeccapurple;
    margin-top: 20px;
}
.lines2:before{
    content: attr(data-text);
    background-color: #fff;
    position: absolute;
    text-align: center;
    left: 47%;
    width: 250px;
    margin-left: -110px;
    padding: 5px;
    top: -20px;
}
</style>
<form method="post" action="<?= current_url(); ?>" autocomplete="off" enctype="multipart/form-data">
    <div class="row">
        <div class="col-lg-6">
            <div class="card m-b-30">
                <div class="card-header">
                    <h5 class="m-b-0">
                        Customer Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-sm-6 input-group">
                            <label style="width: 90%;">Order ID</label>
                            <div class="input-group-append" style="height: 36px;">
                                <span class="input-group-text bg-gray-400">R-</span>
                            </div>
                            <?php
                            $order_id = !empty($order->order_id) ? $order->order_id . ($clone ? '-Copy' : '') : time();
                            $order_id = (strpos($order_id, 'R-') === 0) ? substr($order_id, 2) : $order_id;
                            ?>
                            <input type="text" autocomplete="nope" required="" class="form-control" name="order_id" placeholder="Order Id" value="<?= set_value('order_id', $order_id); ?>">
                        </div>
                        <div class="form-group col-sm-6">
                            <label>Order Type</label>
                            <input type="text" disabled class="form-control" name="paymentmode" value="Reverse"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label>First Name</label>
                            <input type="text" autocomplete="nope" name="shipping_name" required="" class="form-control" placeholder="First Name" value="<?= set_value('shipping_name', !empty($order->shipping_fname) ? $order->shipping_fname : '') ?>" />
                        </div>
                        <div class="form-group col-sm-6">
                            <label>Last Name</label>
                            <input type="text" autocomplete="nope" name="shipping_lname" class="form-control" placeholder="Last Name" value="<?= set_value('shipping_lname', !empty($order->shipping_lname) ? $order->shipping_lname : '') ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <label>Company Name</label>
                            <input type="text" autocomplete="nope" name="shipping_company_name" class="form-control" placeholder="Company Name" value="<?= set_value('shipping_company_name', !empty($order->shipping_company_name) ? $order->shipping_company_name : '') ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label>Address</label>
                            <textarea autocomplete="nope" class="form-control" required="" name="shipping_address_1" placeholder="Shipping Address"><?= set_value('shipping_address_1', !empty($order->shipping_address) ? $order->shipping_address : '') ?></textarea>
                        </div>
                        <div class="form-group col-sm-6">
                            <label>Address 2 (Optional)</label>
                            <textarea autocomplete="nope" class="form-control" name="shipping_address_2" placeholder="Address 2"><?= set_value('shipping_address_2', !empty($order->shipping_address_2) ? $order->shipping_address_2 : '') ?></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <label>Pin Code</label>
                            <input type="text" autocomplete="nope" name="shipping_pincode" id="shipping_pincode" required="" class="form-control" placeholder="Pin Code" value="<?= set_value('shipping_pincode', !empty($order->shipping_zip) ? $order->shipping_zip : '') ?>" />
                            <span class="errormsg" id="errormsg" style="color: #8b0001;font-weight: bold;"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label>City</label>
                            <input type="text" autocomplete="nope" name="shipping_city" id="shipping_getcity" required="" class="form-control" placeholder="City" value="<?= set_value('shipping_city', !empty($order->shipping_city) ? $order->shipping_city : '') ?>" />
                        </div>
                        <div class="form-group col-sm-6">
                            <label>State</label>
                            <input type="text" autocomplete="nope" name="shipping_state" id="shipping_getstate" required="" class="form-control" placeholder="State" value="<?= set_value('shipping_state', !empty($order->shipping_state) ? $order->shipping_state : '') ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-12">
                            <label>Phone</label>
                            <input type="text" autocomplete="nope" name="shipping_phone" required="" class="form-control" placeholder="Phone" value="<?= set_value('shipping_phone', !empty($order->shipping_phone) ? $order->shipping_phone : '') ?>" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card m-b-30">
                <div class="card-header">
                    <h5 class="m-b-0">
                        Product Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <input type="hidden" name="product_qty" required="" value="1"/>
                        <div class="form-group col-sm-6">
                            <label>Product Name</label>
                            <input type="text" autocomplete="nope" id="product_name" name="product_name" required="" class="form-control" placeholder="Product Name" value="<?= set_value('product_name', !empty($product->product_name) ? $product->product_name : '') ?>">
                        </div>
                        <div class="form-group col-sm-6">
                            <label>Amount</label>
                            <input type="text" autocomplete="nope" id="product_price" name="product_price" class="form-control" required="" placeholder="Product Amount" value="<?= set_value('product_price', !empty($product->product_price) ? $product->product_price : '') ?>">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card m-b-30">
                <div class="card-header"></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <table class="table-responsive">
                                <tbody>
                                    <tr>
                                        <th style="margin-top: 7px;display: block;">Weight*</th>
                                        <td>
                                            <div class="input-group">
                                                <input type="text" autocomplete="nope" name="weight" class="form-control" placeholder="in grams" value="<?= set_value('weight', !empty($order->package_weight) ? $order->package_weight : '') ?>">
                                            </div>
                                            <p>eg: 500, 300</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="margin-top: 7px;display: block;">Dimensions*</th>
                                        <td>
                                            <div class="input-group">
                                                <input type="text" onkeypress="return /[0-9]/i.test(event.key)" id="length"  autocomplete="nope" name="length" class="form-control calculate_vol_weight" placeholder="CM" value="<?= set_value('length', !empty($order->package_length) ? $order->package_length : '') ?>">
                                                <input type="text"  onkeypress="return /[0-9]/i.test(event.key)" id="height"  autocomplete="nope" name="height" class="form-control calculate_vol_weight" placeholder="CM" value="<?= set_value('height', !empty($order->package_height) ? $order->package_height : '') ?>">
                                                <input type="text" onkeypress="return /[0-9]/i.test(event.key)" id="breadth" autocomplete="nope" name="breadth" class="form-control calculate_vol_weight" placeholder="CM" value="<?= set_value('breadth', !empty($order->package_breadth) ? $order->package_breadth : '') ?>">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th style="margin-top: 17px;display: block;">Volumetric Weight</th>
                                        <td>
                                            <div class="input-group" style="margin-top: 10px;">
                                                <input type="text" autocomplete="nope" name="vol_weight" id="vol_weight" class="form-control" placeholder="Volumetric Weight" value="<?= set_value('vol_weight', !empty($order->package_volumatic_weight) ? ($order->package_volumatic_weight) : '') ?>" readonly>
                                                <span style="margin-top: 7px; margin-left: 6px;margin-right: 304px;" id="weight_in"> <?php if( !empty($order->package_volumatic_weight) && fmod($order->package_volumatic_weight, 1) !== 0.00) { echo 'Kg'; } else { echo 'Grams'; } ?></span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30">
                <div class="card-header">
                    <label class="cstm-switch" style="margin-bottom: 25px;font-weight: bold;">
                        <input type="checkbox" onclick="toggleText()" name="qccheck" value='<?= set_value('qccheck', !empty($order->qccheck) ? $order->qccheck : '0');?>' <?= !empty($order->qccheck) ? 'checked' : ''; ?> class="cstm-switch-input" id="qccheck">
                        <span class="cstm-switch-indicator"></span>
                        <span class="cstm-switch-description">QC Check Information</span>
                    </label>
                    <div class="row" id="qc" style="display:<?= !empty($order->qccheck) ? 'flex' : 'none'; ?>">
                        <div class="col-sm-6">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card m-b-30">
                                        <div class="card-header">
                                            <h5 class="m-b-0">
                                                <strong>QC Questions</strong>
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="form-group col-sm-12">
                                                    <label>Product Category</label>
                                                    <select name="order_category_id" class="form-control js-select2" style="width: 100% !important;">
                                                        <option value="">Select</option>
                                                        <?php if(!empty($ordercategories)) { foreach ($ordercategories as $order_category) {
                                                            $order_category_id = strtolower(set_value('order_category_id', !empty($qc_product->order_category_id) ? $qc_product->order_category_id : ''));
                                                        ?>
                                                        <option <?php if ($order_category_id == $order_category->id) { ?> selected <?php } ?> value="<?= $order_category->id; ?>"><?= ucwords($order_category->categories_name); ?></option>
                                                        <?php } } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-sm-4">
                                                    <div class="m-b-10">
                                                        <p class="font-secondary" style="font-weight: bold;">Used Product</p>
                                                        <div class="custom-control custom-radio custom-control-inline">
                                                            <input type="radio" value ='1' <?php if(isset($qc_product->product_usage) && $qc_product->product_usage == '1'){ echo 'checked'; } else {} ?> id="customRadioInline1" name="product_usage" class="custom-control-input">
                                                            <label class="custom-control-label" for="customRadioInline1">Yes</label>
                                                        </div>
                                                        <div class="custom-control custom-radio custom-control-inline">
                                                            <input type="radio" value ='0' <?php if(isset($qc_product->product_usage) && $qc_product->product_usage == '0'){ echo 'checked'; } else {} ?> id="customRadioInline2" name="product_usage" class="custom-control-input">
                                                            <label class="custom-control-label" for="customRadioInline2">No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group col-sm-4">
                                                    <div class="m-b-10">
                                                        <p class="font-secondary" style="font-weight: bold;">Damaged Product</p>
                                                        <div class="custom-control custom-radio custom-control-inline">
                                                            <input type="radio" value ='1' <?php if(isset($qc_product->product_damage) && $qc_product->product_damage == '1'){ echo 'checked'; } else {} ?> id="customRadioInline3" name="product_damage" class="custom-control-input">
                                                            <label class="custom-control-label" for="customRadioInline3">Yes</label>
                                                        </div>
                                                        <div class="custom-control custom-radio custom-control-inline">
                                                            <input type="radio" value ='0' <?php if(isset($qc_product->product_damage) && $qc_product->product_damage == '0'){ echo 'checked'; } else {} ?> id="customRadioInline4" name="product_damage" class="custom-control-input">
                                                            <label class="custom-control-label" for="customRadioInline4">No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-sm-4">
                                                    <div class="m-b-10">
                                                        <p class="font-secondary" style="font-weight: bold;">Match Brand Name</p>
                                                        <div class="custom-control custom-radio custom-control-inline">
                                                            <input type="radio" value='1' <?php if(isset($qc_product->brandname) && $qc_product->brandname == '1'){ echo 'checked'; } else {} ?> id="customRadioInline5" name="brandname" class="custom-control-input">
                                                            <label class="custom-control-label" for="customRadioInline5">Yes</label>
                                                        </div>
                                                        <div class="custom-control custom-radio custom-control-inline">
                                                            <input type="radio" value='0' <?php if(isset($qc_product->brandname) && $qc_product->brandname == '0'){ echo 'checked'; } else {} ?> id="customRadioInline6" name="brandname" class="custom-control-input">
                                                            <label class="custom-control-label" for="customRadioInline6">No</label>
                                                        </div>
                                                    </div>
                                                    <div class="m-b-10" id="brandnamesection" style="display:<?php if(isset($qc_product->brandname) && $qc_product->brandname == '1'){ echo 'block'; } else { echo 'none';} ?>">
                                                        <input type="text" autocomplete="nope" value="<?= set_value('brandnametype', !empty($qc_product->brand_name_text) ? $qc_product->brand_name_text : '') ?>" name="brandnametype" id="brandnametype" placeholder="Brand Name" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="form-group col-sm-4">
                                                    <div class="m-b-10">
                                                        <p class="font-secondary" style="font-weight: bold;">Match Product Size</p>
                                                        <div class="custom-control custom-radio custom-control-inline">
                                                            <input type="radio" value ='1' <?php if(isset($qc_product->productsize) && $qc_product->productsize == '1'){ echo 'checked'; } else {} ?> id="customRadioInline7" name="productsize" class="custom-control-input">
                                                            <label class="custom-control-label" for="customRadioInline7">Yes</label>
                                                        </div>
                                                        <div class="custom-control custom-radio custom-control-inline">
                                                            <input type="radio" value ='0' <?php if(isset($qc_product->productsize) && $qc_product->productsize == '0'){ echo 'checked'; } else {} ?> id="customRadioInline8" name="productsize" class="custom-control-input">
                                                            <label class="custom-control-label" for="customRadioInline8">No</label>
                                                        </div>
                                                    </div>
                                                    <div class="m-b-10" id="productsizesection" style="display:<?php if(isset($qc_product->productsize) && $qc_product->productsize == '1'){ echo 'block'; } else { echo 'none';} ?>">
                                                        <input type="text" autocomplete="nope" value="<?= set_value('productsizetype', !empty($qc_product->product_size_text) ? $qc_product->product_size_text : '') ?>" name="productsizetype" id="productsizetype" placeholder="Product Size" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="form-group col-sm-4">
                                                    <div class="m-b-10">
                                                        <p class="font-secondary" style="font-weight: bold;">Match Product Color</p>
                                                        <div class="custom-control custom-radio custom-control-inline">
                                                            <input type="radio" value ='1' <?php if(isset($qc_product->productcolor) && $qc_product->productcolor == '1'){ echo 'checked'; } else {} ?> id="customRadioInline9" name="productcolor" class="custom-control-input">
                                                            <label class="custom-control-label" for="customRadioInline9">Yes</label>
                                                        </div>
                                                        <div class="custom-control custom-radio custom-control-inline">
                                                            <input type="radio" value ='0' <?php if(isset($qc_product->productcolor) && $qc_product->productcolor == '0'){ echo 'checked'; } else {} ?> id="customRadioInline10" name="productcolor" class="custom-control-input">
                                                            <label class="custom-control-label" for="customRadioInline10">No</label>
                                                        </div>
                                                    </div>
                                                    <div class="m-b-10" id="productcolorsection" style="display:<?php if(isset($qc_product->productcolor) && $qc_product->productcolor == '1'){ echo 'block'; } else { echo 'none';} ?>">
                                                        <input type="text" autocomplete="nope" value="<?= set_value('productcolourtype', !empty($qc_product->product_color_text) ? $qc_product->product_color_text : '') ?>" name="productcolourtype" id="productcolourtype" placeholder="Product Colour" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card m-b-30">
                                        <div class="card-header">
                                            <h5 class="m-b-0">
                                                <strong>Product Reference Images</strong>
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="input-group mb-3">
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input" name="product_img_1" id="firstimgupload">
                                                            <input type="hidden" name="uploadedimage" value="<?= set_value('product_img_1', !empty($qc_product->product_img_1) ? $qc_product->product_img_1 : ''); ?>" id="uploadedfirstimg">
                                                            <label class="custom-file-label"><?= !empty($qc_product->product_img_1) ? str_replace('https://nimubs-assets.s3.amazonaws.com/assets/order_product/', '', $qc_product->product_img_1) : 'Image 1'; ?></label>
                                                        </div>
                                                    </div>
                                                    <div class="input-group mb-3">
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input" name="product_img_2" id="secondimgupload">
                                                            <input type="hidden" name="uploadedimage_2" value="<?= set_value('product_img_2', !empty($qc_product->product_img_2) ? $qc_product->product_img_2 : ''); ?>" id="uploadedsecondimg">
                                                            <label class="custom-file-label"><?= !empty($qc_product->product_img_2) ? str_replace('https://nimubs-assets.s3.amazonaws.com/assets/order_product/', '', $qc_product->product_img_2) : 'Image 2'; ?></label>
                                                        </div>
                                                    </div>
                                                    <div class="input-group mb-3">
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input" name="product_img_3" id="thirdimgupload">
                                                            <input type="hidden" name="uploadedimage_3" value="<?= set_value('product_img_3', !empty($qc_product->product_img_3) ? $qc_product->product_img_3 : ''); ?>" id="uploadedthirdimg">
                                                            <label class="custom-file-label"><?= !empty($qc_product->product_img_3) ? str_replace('https://nimubs-assets.s3.amazonaws.com/assets/order_product/', '', $qc_product->product_img_3) : 'Image 3'; ?></label>
                                                        </div>
                                                    </div>
                                                    <div class="input-group mb-3">
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input" name="product_img_4" id="fourthimgupload">
                                                            <input type="hidden" name="uploadedimage_4" value="<?= set_value('product_img_4', !empty($qc_product->product_img_4) ? $qc_product->product_img_4 : ''); ?>" id="uploadedfourthimg">
                                                            <label class="custom-file-label"><?= !empty($qc_product->product_img_4) ? str_replace('https://nimubs-assets.s3.amazonaws.com/assets/order_product/', '', $qc_product->product_img_4) : 'Image 4'; ?></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div>
                                                        <div class="avatar avatar-xl" id="firstimage">
                                                        <img class="avatar-img rounded" src="<?= set_value('product_img_1', !empty($qc_product->product_img_1) ? $qc_product->product_img_1 : 'assets/img/no-img.png'); ?>" id="uploadedfirstimg_preview">
                                                        </div>
                                                        <div class="avatar avatar-xl" id="secondimage">
                                                        <img class="avatar-img rounded" src="<?= set_value('product_img_2', !empty($qc_product->product_img_2) ? $qc_product->product_img_2 : 'assets/img/no-img.png'); ?>" id="uploadedsecondimg_preview">
                                                        </div>
                                                    </div>
                                                    <div style="margin-top: 20px;">
                                                        <div class="avatar avatar-xl" id="thirdimage">
                                                        <img class="avatar-img rounded" src="<?= set_value('product_img_3', !empty($qc_product->product_img_3) ? $qc_product->product_img_3 : 'assets/img/no-img.png'); ?>" id="uploadedthirdimg_preview">
                                                        </div>
                                                        <div class="avatar avatar-xl" id="fourthimage">
                                                        <img class="avatar-img rounded" src="<?= set_value('product_img_4', !empty($qc_product->product_img_4) ? $qc_product->product_img_4 : 'assets/img/no-img.png'); ?>" id="uploadedfourthimg_preview">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="m-b-30">
                <div class="form-group text-center">
                    <input type="submit" name="submit" id="btnSubmit" class="btn btn-primary" value="Save" />&nbsp;
                    <a href="<?= !empty($order->id) ? 'orders/view/' . $order->id : 'orders/all'; ?>" name="cancel" class="btn btn-danger">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">

$('.calculate_vol_weight').keyup(function(){
    $('#vol_weight').val('');
    var l=$("#length").val();
    var b=$("#breadth").val();
    var h=$("#height").val();
   
    len = l.replace(/\s/g, '');
    bre = b.replace(/\s/g, '');
    hei = h.replace(/\s/g, '');
    if(len!='' || bre!='' || hei!='')
    {
        if(len=='') {  len=1; }
        if(bre=='') {  bre=1; }
        if(hei=='') {  hei=1; }
    
        var sum=len*bre*hei;
        var totalsum=sum/5000;
        var bs=totalsum.toString().split(".")[0]; ///before
        var as=totalsum.toString().split(".")[1]; ///after
        if(bs> 0) 
        {  
           $('#vol_weight').val(totalsum.toFixed(2));
           $("#weight_in").html("Kg");
        }
        else
        {
            var weight=(totalsum*1000);
            $('#vol_weight').val(Math.round(weight));
            $("#weight_in").html("Grams");
        }
    }

  });


$("#btnSubmit").on("click", function() {
    if ($("#qccheck").val() == '1') {
        if($('#order_category_id').val() == '') {
            alert("Please select order category in QC."); return false;
        }
        if($('input:radio:checked').length != 5) {
            alert("Please fill all the QC Questions."); return false;
        }
        if($('input:radio[name=brandname]:checked').val() == 1 && $('#brandnametype').val() == '') {
            alert("Please fill the input for brand name."); return false;
        }
        if($('input:radio[name=productsize]:checked').val() == 1 && $('#productsizetype').val() == '') {
            alert("Please fill the input for product size."); return false;
        }
        if($('input:radio[name=productcolor]:checked').val() == 1 && $('#productcolourtype').val() == '') {
            alert("Please fill the input for product color."); return false;
        }
        if($('#uploadedfirstimg').val() == '' && $('#uploadedsecondimg').val() == '' && $('#uploadedthirdimg').val() == '' && $('#uploadedfourthimg').val() == '') {
            alert("Please select at least one image."); return false;
        }
    }
});

$("#shipping_pincode").change(function() {
    var pincode = $('#shipping_pincode').val();
    if (pincode == "") {
        $('#shipping_getcity').val('');
        $('#shipping_getstate').val('');
    } else {
        $.ajax({
            type: 'POST',
            url: "orders/getcitystate", //file which read zip code excel file
            data: {
                'pincode': pincode
            },
            success: function(data) {
                if (data == '') {
                    $('#shipping_getcity').val('');
                    $('#shipping_getstate').val('');
                    return false;
                } else {
                    var getData = $.parseJSON(data);
                    $('#shipping_getcity').val(getData.city);
                    $('#shipping_getstate').val(getData.state);
                }
            },
        });
    }
});

function toggleText(){
    var x = document.getElementById("qc");
    var value = document.getElementById("qccheck");
    var checked = $('#check_id').is(":checked");
    if (x.style.display === "none" || value == '1') {
        x.style.display = "flex";
        $("#qccheck").val('1');
    } else {
        $("#qccheck").val('0');
        x.style.display = "none";
    }
}

$("#customRadioInline5").on("click", function() {
    $("#brandnamesection").show();
});

$("#customRadioInline6").on("click", function() {
    $("#brandnamesection").hide();
});

$("#customRadioInline7").on("click", function() {
    $("#productsizesection").show();
});

$("#customRadioInline8").on("click", function() {
    $("#productsizesection").hide();
});

$("#customRadioInline9").on("click", function() {
    $("#productcolorsection").show();
});

$("#customRadioInline10").on("click", function() {
    $("#productcolorsection").hide();
});

$("#firstimgupload").on("change", function() {
    var file_data = $(this).prop('files')[0];
    var form_data = new FormData();
    form_data.append('product_img_1', file_data);
    $.ajax({
        url: 'orders/upload_first_img',
        type: "POST",
        data: form_data,
        cache: false,
        contentType: false,
        processData: false,
        success: function(data) {
            if (data.error)
                alert(data.error);
            if (data.success) {
                $("#uploadedfirstimg_preview").attr('src',data.success);
                $("#uploadedfirstimg").val(data.success);
            }
        }
    });
});

$("#secondimgupload").on("change", function() {
    var file_data_1 = $(this).prop('files')[0];
    var form_data = new FormData();
    form_data.append('product_img_2', file_data_1);
    $.ajax({
        url: 'orders/upload_second_file',
        type: "POST",
        data: form_data,
        cache: false,
        contentType: false,
        processData: false,
        success: function(data) {
            if (data.error)
                alert(data.error);
            if (data.success) {
                $("#uploadedsecondimg_preview").attr('src',data.success);
                $("#uploadedsecondimg").val(data.success);
            }
        }
    });
});

$("#thirdimgupload").on("change", function() {
    var file_data_2 = $(this).prop('files')[0];
    var form_data = new FormData();
    form_data.append('product_img_3', file_data_2);
    $.ajax({
        url: 'orders/upload_third_file',
        type: "POST",
        data: form_data,
        cache: false,
        contentType: false,
        processData: false,
        success: function(data) {
            if (data.error)
                alert(data.error);
            if (data.success) {
                $("#uploadedthirdimg_preview").attr('src',data.success);
                $("#uploadedthirdimg").val(data.success);
            }
        }
    });
});

$("#fourthimgupload").on("change", function() {
    var file_data_4 = $(this).prop('files')[0];
    var form_data = new FormData();
    form_data.append('product_img_4', file_data_4);
    $.ajax({
        url: 'orders/upload_fourth_file',
        type: "POST",
        data: form_data,
        cache: false,
        contentType: false,
        processData: false,
        success: function(data) {
            if (data.error)
                alert(data.error);
            if (data.success) {
                $("#uploadedfourthimg_preview").attr('src',data.success);
                $("#uploadedfourthimg").val(data.success);
            }
        }
    });
});
</script>