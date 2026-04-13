<link rel="stylesheet" href="<?= $this->config->item('assets_base') ?>assets/css/custom-design.css?v=1.3">
<style>
    .card .box:hover {
    box-shadow: 0px 0px 12px rgb(126 142 177 / 70%) !important;
}
.card .addons-box:hover {
    box-shadow: 0px 0px 12px rgb(126 142 177 / 70%) !important;
}
</style>
<section class="admin-content">
            <div class="container-fluid p-t-30">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card no-shadow custtom-bg1">
                            <div class="card-header header-bg rounded-top">
                                <h5 class="mb-0 text-white card-titles">SETTINGS</h5>
                            </div>
                            <div class="card-body mt-3">
                                <div class="row">
                                    <div class="col-lg-3 col-md-4 col-sm-4">
                                        <div class="card addons-box h-100 custome-bg p-4">
                                            <h4 class="fw-600">Settings</h4>
                                            <p class="fw-400">Take your shipping experience a notch higher with us by regulating your panel settings as per your convenience and specific business requirements. From importing orders to managing labels and all other account
                                                settings, get everything at the tap of your finger for an uninterrupted experience.</p>
                                            <div class="addons-img-box text-center pt-4 mt-1 mt-sm-4">
                                                <img src="<?= $this->config->item('assets_base') ?>assets/img/settings/setting.png" alt="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-9 col-md-8 col-sm-8">
                                        <div class="addons-boxes mt-4 mt-sm-0">
                                            <div class="row">
                                            <?php if (in_array('settings', $user_details->permissions)) { ?>
                                                <div class="col-lg-4 col-md-6 col-sm-6 m-b-30">
                                                    <div class="card box">
                                                        <a href="channels">
                                                            <div class="card-body my-2">
                                                                <div class="pb-2">
                                                                    <button type="button" class="btn m-b-15">
                                                                        <img src="<?= $this->config->item('assets_base') ?>assets/img/settings/icons/1.png" class="icon">
                                                                    </button>
                                                                </div>
                                                                <div>
                                                                    <h5 class="fw-600 text-dark">Channel</h5>
                                                                    <p class="text-muted m-0 fw-600"><span>Import orders from your online store</span> </p>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-6 col-sm-6 m-b-30">
                                                    <div class="card box">
                                                        <a href="warehouse">
                                                            <div class="card-body my-2">
                                                                <div class="pb-2">
                                                                    <button type="button" class="btn m-b-15">
                                                                        <img src="<?= $this->config->item('assets_base') ?>assets/img/settings/icons/2.png" class="icon">
                                                                    </button>
                                                                </div>
                                                                <div>
                                                                    <h5 class="fw-600 text-dark">Warehouse</h5>
                                                                    <p class="text-muted m-0 fw-600">Manage your pickup locations</p>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-6 col-sm-6 m-b-30">
                                                    <div class="card box">
                                                        <a href="employees">
                                                            <div class="card-body my-2">
                                                                <div class="pb-2">
                                                                    <button type="button" class="btn m-b-15">
                                                                        <img src="<?= $this->config->item('assets_base') ?>assets/img/settings/icons/3.png" class="icon">
                                                                    </button>
                                                                </div>
                                                                <div>
                                                                    <h5 class="fw-600 text-dark">Employees</h5>
                                                                    <p class="text-muted m-0 fw-600">Allow access to team members</p>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-6 col-sm-6 m-b-30">
                                                    <div class="card box">
                                                        <a href="user_api">
                                                            <div class="card-body my-2">
                                                                <div class="pb-2">
                                                                    <button type="button" class="btn m-b-15">
                                                                        <img src="<?= $this->config->item('assets_base') ?>assets/img/settings/icons/4.png" class="icon">
                                                                    </button>
                                                                </div>
                                                                <div>
                                                                    <h5 class="fw-600 text-dark">API</h5>
                                                                    <p class="text-muted m-0 fw-600">Programmatically access deltagloabal data</p>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-6 col-sm-6 m-b-30">
                                                    <div class="card box">
                                                        <a href="webhook">
                                                            <div class="card-body my-2">
                                                                <div class="pb-2">
                                                                    <button type="button" class="btn m-b-15">
                                                                        <img src="<?= $this->config->item('assets_base') ?>assets/img/settings/icons/5.png" class="icon">
                                                                    </button>
                                                                </div>
                                                                <div>
                                                                    <h5 class="fw-600 text-dark">Webhooks</h5>
                                                                    <p class="text-muted m-0 fw-600">Receive shipments status notification on URL</p>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-6 col-sm-6 m-b-30">
                                                    <div class="card box">
                                                        <a href="profile">
                                                            <div class="card-body my-2">
                                                                <div class="pb-2">
                                                                    <button type="button" class="btn m-b-15">
                                                                        <img src="<?= $this->config->item('assets_base') ?>assets/img/settings/icons/6.png" class="icon">
                                                                    </button>
                                                                </div>
                                                                <div>
                                                                    <h5 class="fw-600 text-dark">Company Profile</h5>
                                                                    <p class="text-muted m-0 fw-600">Your company profile</p>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </div>
                                                </div>
                                                <?php } ?>
                                                <?php if (in_array('shipments', $user_details->permissions)) { ?>
                                                <div class="col-lg-4 col-md-6 col-sm-6 m-b-30">
                                                    <div class="card box">
                                                        <a href="setting/label">
                                                            <div class="card-body my-2">
                                                                <div class="pb-2">
                                                                    <button type="button" class="btn m-b-15">
                                                                        <img src="<?= $this->config->item('assets_base') ?>assets/img/settings/icons/7.png" class="icon">
                                                                    </button>
                                                                </div>
                                                                <div>
                                                                    <h5 class="fw-600 text-dark">Label Settings</h5>
                                                                    <p class="text-muted m-0 fw-600">Set your shipping label format</p>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </div>
                                                </div>
                                                <?php } ?>
                                                <div class="col-lg-4 col-md-6 col-sm-6 m-b-30">
                                                    <div class="card box">
                                                        <a href="setting">
                                                            <div class="card-body my-2">
                                                                <div class="pb-2">
                                                                    <button type="button" class="btn m-b-15">
                                                                        <img src="<?= $this->config->item('assets_base') ?>assets/img/settings/icons/8.png" class="icon">
                                                                    </button>
                                                                </div>
                                                                <div>
                                                                    <h5 class="fw-600 text-dark">Account Settings</h5>
                                                                    <p class="text-muted m-0 fw-600">Update your profile or password</p>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-6 col-sm-6 m-b-30">
                                                    <div class="card box">
                                                    <?php
                            $international_path = 'international_profile';
                            if ((isset($user_details->is_international_franchise)) && ($user_details->is_international_franchise == '1')) { $international_path = 'international_franchise'; }
                        ?>
                                                        <a href="<?= $international_path; ?>">
                                                            <div class="card-body my-2">
                                                                <div class="pb-2">
                                                                    <button type="button" class="btn m-b-15">
                                                                        <img src="<?= $this->config->item('assets_base') ?>assets/img/settings/icons/9.png" class="icon">
                                                                    </button>
                                                                </div>
                                                                <div>
                                                                    <h5 class="fw-600 text-dark">International KYC</h5>
                                                                    <p class="text-muted m-0 fw-600">Update you international KYC</p>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-6 col-sm-6 m-b-30">
                                                    <div class="card box">
                                                        <a href="<?php echo base_url('product/invoice_settings');?>">
                                                            <div class="card-body my-2">
                                                                <div class="pb-2">
                                                                    <button type="button" class="btn m-b-15">
                                                                        <img src="<?= $this->config->item('assets_base') ?>assets/img/settings/icons/10.png" class="icon">
                                                                    </button>
                                                                </div>
                                                                <div>
                                                                    <h5 class="fw-600 text-dark">Invoice Settings</h5>
                                                                    <p class="text-muted m-0 fw-600">Shipment invoice customization</p>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-6 col-sm-6 m-b-30">
                                                    <div class="card box">
                                                        <a href="product/all">
                                                            <div class="card-body my-2">
                                                                <div class="pb-2">
                                                                    <button type="button" class="btn m-b-15">
                                                                        <img src="<?= $this->config->item('assets_base') ?>assets/img/settings/icons/11.png" class="icon">
                                                                    </button>
                                                                </div>
                                                                <div>
                                                                    <h5 class="fw-600 text-dark">Product Weight Freeze</h5>
                                                                    <p class="text-muted m-0 fw-600">Set weight & dimension of your shipment</p>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-6 col-sm-6 m-b-30">
                                                    <div class="card box">
                                                        <a href="product/billings">
                                                            <div class="card-body my-2">
                                                                <div class="pb-2">
                                                                    <button type="button" class="btn m-b-15">
                                                                        <img src="<?= $this->config->item('assets_base') ?>assets/img/settings/icons/12.png" class="icon">
                                                                    </button>
                                                                </div>
                                                                <div>
                                                                    <h5 class="fw-600 text-dark">Product HSN/GST Mapping</h5>
                                                                    <p class="text-muted m-0 fw-600">Set GST & HSN against your product</p>
                                                                </div>
                                                            </div>
                                                        </a>
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
        </section>