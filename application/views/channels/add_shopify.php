<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">Add Shopify Details</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item">						
		</li>
    </ol>
</div>
<!-- END PAGE-HEADER -->
<div class="main-container container-fluid">

<!-- START ROW-1 -->
<div class="row">
    <div class="col-lg-6">
        <div class="card m-b-30">
            <div class="card-header">
                <h5 class="m-b-0">
                    Shopify Add Information
                </h5>
            </div>
            <div class="card-body ">
                <form id="addchannel" method="post" action="<?= current_url(); ?>" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Channel Name <span style="color:red">*</span></label>
                        <input type="text" class="form-control" name="channel_name" placeholder="Channel Name" value="<?= set_value('channel_name', !empty($channel->channel_name) ? $channel->channel_name : ''); ?>" />
                    </div>
                    <div class="form-group">
                            <label>Store URL <span style="color:red">*</span></label>
                            <input type="text" class="form-control" name="host" value="<?= set_value('host', !empty($channel->api_field_1) ? $channel->api_field_1 : ''); ?>" placeholder="Enter Store URL">
                            <small id="passwordHelpBlock" class="form-text text-muted">
                                Store URL should be like <b>yourstore.myshopify.com</b>
                            </small>
                    </div>
                    <?php if (empty($channel) || $channel->channel != 'shopify_oneclick') { ?>
                        <div class="form-group">
                            <label>API Key <span style="color:red">*</span></label>
                            <input type="text" class="form-control" name="api_key" value="<?= set_value('api_key', !empty($channel->api_field_2) ? $channel->api_field_2 : ''); ?>" placeholder="Enter API Key">
                        </div>
                        
                        <div class="form-group">
                            <label>Admin API Access token <span style="color:red">*</span></label>
                            <input type="password" class="form-control" name="api_password" value="<?= set_value('api_password', !empty($channel->api_field_3) ? $channel->api_field_3 : ''); ?>" placeholder="Admin API access token">
                        </div>


                        <div class="form-group">
                            <label>API secret key <span style="color:red">*</span></label>
                            <input type="password" class="form-control" name="shared_secret" value="<?= set_value('shared_secret', !empty($channel->api_field_4) ? $channel->api_field_4 : ''); ?>" placeholder="Enter Shared Secret">
                        </div>

                    <?php } ?>
                    <div class="form-group col-6">
                        <div class="control ">
                            <label class="label" for="auto_fulfill">Fulfill Orders</label>
                            <br /><small><b> Select when to Auto Fulfill the Order in Shopify?</b> </small>
                            <select class="form-control" name="auto_fulfill" id="auto_fulfill">
                                <?php
                                $autofill = (isset($channel->auto_fulfill)) ? $channel->auto_fulfill : '1'; ?>
                                <option value="">Do Not Fulfill </option>
                                <option value="1" <?php if ($autofill == '1') {
                                                        echo "selected";
                                                    }  ?>> Order is Booked </option>
                                <option value="2" <?php if ($autofill == '2') {
                                                        echo "selected";
                                                    }  ?>> Order is Deliverd </option>
                                <option value="3" <?php if ($autofill == '3') {
                                                        echo "selected";
                                                    }  ?>> Order is In Transit</option>
                                <option value="4" <?php if ($autofill == '4') {
                                                        echo "selected";
                                                    }  ?>> Order Out for Delivery</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox custom-control-inline">
                            <?= form_checkbox('auto_update_status', '1', set_value('auto_update_status', (isset($channel->auto_update_status)) ? $channel->auto_update_status : '1'), 'class="custom-control-input" id="customCheckDisabled1"'); ?>
                            <label class="custom-control-label" for="customCheckDisabled1">Automatically update the shipment status in Shopify. <i class="fa fa-question-circle" aria-hidden="true" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Enabling this will auto update order status in Shopify when order staus is changed"></i></label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox custom-control-inline">
                            <?= form_checkbox('auto_cancel', '1', set_value('auto_cancel', (isset($channel->auto_cancel)) ? $channel->auto_cancel : '1'), 'class="custom-control-input" id="customCheckDisabled2"'); ?>
                            <label class="custom-control-label" for="customCheckDisabled2">Cancel orders <i class="fa fa-question-circle" aria-hidden="true" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Enabling this will auto cancel order in Shopify when order is cancelled in Daakit Panel"></i></label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox custom-control-inline">
                            <?= form_checkbox('auto_cod_paid', '1', set_value('auto_cod_paid', (isset($channel->auto_cod_paid)) ? $channel->auto_cod_paid : '1'), 'class="custom-control-input" id="customCheckDisabled3"'); ?>
                            <label class="custom-control-label" for="customCheckDisabled3">Mark as paid <i class="fa fa-question-circle" aria-hidden="true" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Mark COD orders as paid in Shopify once the orders are delivered to the customer."></i></label>
                        </div>
                    </div>
                    
                    <!-- <div class="form-group">
                    <div class="row border-top">
                        <div class="col-md-6">
                            <label style="margin-top:15px">Channel Logo <i class="fa fa-question-circle" aria-hidden="true" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Avoid blurry or pixelated images by uploading a file with a size of 100KB or less."></i></label> 
                            <div class="custom-file">
                                <input type="file" class="form-control" name="brand_logo" id="brand_logo"  accept="image/png, image/gif, image/jpeg"/  >
                            </div>
                        </div>
                        <div class="col-md-6 fetch_brand_logo" style="margin-top: 20px">
                            <img src="<?=!empty($channel->brand_logo) ?$channel->brand_logo : "";?>" style="width:150px;" >
                            <?php if(isset($channel) && !empty($channel->brand_logo)) {?>
                            <a class="btn btn-sm" onclick="delete_brand_logo(<?php echo $channel->id;?>)"  style="margin-top: 70px;    margin-left: -37px;;background: white;"><i class="mdi mdi-delete-forever"></i> </a>
                            <?php } ?>
                        </div>
                    </div>
                    </div> -->

                    <div class="form-group">
                        <button class="btn btn-primary" style="float: right;" type="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card m-b-30">
            <div class="card-header">
                <h5 class="m-b-0">
                    Guidelines for Integrate shopify Channel
                </h5>
            </div>
            <div class="card-body ">
                <ol >
                    <li>Log in to the Shopify Admin Panel via This URL <a href="https://www.shopify.com/in" target="_blank">(https://www.shopify.com/in/)</a></li>
                    <li>Click on login put your login credentials to process</li>
                    <li>Go to the “settings”.</li>
                    <li>Go to the “Apps and sales Channels”. </li>
                    <li>Click on the “Develop Apps” button.</li>
                    <li>Click on “Create an App”.</li>
                    <li>Enter the Name of the App and click on “Create App”.</li>
                    <li>Go to API Settings to get the API Key & Secrete Key, copy and put the API key & Secrete key on the form.</li>
                    <li>Click on “Configure Admin API Scope” then check all the permissions then click on save.</li>
                    <li>Click on “Configure Storefront API Integration” then check all the permissions then click on save.</li>
                    <li>Click on “Install App” button & Copy The “Admin API Access Token” then put the same token on the form.</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- END ROW-1 -->
</div>
