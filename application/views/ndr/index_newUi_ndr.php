<?php
$user_channels = array();
if (!empty($channels)) {
    foreach ($channels as $channel) {
        $user_channels[$channel->id] = $channel->channel_name;
    }
}
?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/css/custom-design.css?v=1.0">
<style>
.action_row_selected {
    top: 65px;
    background-color: transparent !important;
    z-index: 995;
}
.page-link:first-child
{
    border-top-left-radius: 0.25rem;
    border-bottom-left-radius: 0.25rem;
}
.page-link:focus {
    box-shadow: 0 0 0 0.2rem rgb(255 255 255 / 25%) !important;
}

</style>

            <div class="container-fluid ">
                <div class="row" style="margin-right: -29px;margin-left: -29px;">
                    <div class="col-md-12">
                        <div class="card no-shadow custtom-bg1">
                            <div class="card-header header-bg rounded-top">
                                <h5 class="mb-0 text-white card-titles">NDR</h5>
                            </div>
                            <div class="card-body mt-3">
                                <div class="ndr-dash-data mt-4">
                                    <div class="row">
                                        <div class="filter-btn-group text-right">
                                        <a href="<?= base_url('ndr/exportCSV'); ?><?php if (!empty($filter)) {
                                                                        echo "?" . http_build_query($_GET);
                                                                    } ?>" > <button type="button" class="btn btn-light "><i class="mdi mdi-download"></i> Export</button></a>
                                            <button type="button" data-toggle="modal" data-target=".import_ndr_modal" class="btn btn-light "><i class="mdi mdi-download mdi-flip-v"></i> CSV Update </button>
                                            <button type="button"  class="btn btn-light show_hide_filter " <?php if (!empty($_GET['filter'])) { ?> style="display:none;" <?php } ?> ><i class="mdi mdi-filter-outline"></i> Filters</button>
                                            <button type="button" class="btn btn-danger show_hide_filter " <?php if (empty($_GET['filter'])) { ?> style="display:none;" <?php } ?> ><i class="icon-placeholder mdi mdi-close"></i> Close</button>
                                        </div>
                                        <div class="col-md-12">
                                        <form method="get" action="<?= base_url('ndr') ?>">
                                            <div class="filter-form" id="filter_row"  <?php if (empty($_GET['filter'])) { ?> style="display:none;" <?php } ?>>
                                                <div class="form-row">
                                                    <div class="form-group col-sm-3">
                                                        <label class="fw-400">From Date</label>
                                                        <input type="text" autocomplete="off" data-start-date="<?= !empty($filter['start_date']) ? $filter['start_date'] : '' ?>" data-end-date="<?= !empty($filter['end_date']) ? $filter['end_date'] : '' ?>"  class="form-control rounded-pill date-range-picker" placeholder="">
                                                        <input type="hidden" autocomplete="off" id="date-min" name="filter[start_date]" value="<?= !empty($filter['start_date']) ? $filter['start_date'] : '' ?>" placeholder="from">
                                                    <input type="hidden" autocomplete="off" id="date-max" name="filter[end_date]" value="<?= !empty($filter['end_date']) ? $filter['end_date'] : '' ?>" placeholder="to">
                                                    </div>
                                                    <div class="form-group col-sm-3">
                                                        <label class="fw-400">Order ID(s)</label>
                                                        <input type="text" autocomplete="off" name="filter[order_ids]" value="<?= !empty($filter['order_ids']) ? $filter['order_ids'] : '' ?>" class="form-control rounded-pill" placeholder="Enter Order IDs separated by comma">
                                                    </div>
                                                    <div class="form-group col-sm-3">
                                                        <label class="fw-400">AWB No(s)</label>
                                                        <input type="text" autocomplete="off" name="filter[awb_no]" value="<?= !empty($filter['awb_no']) ? $filter['awb_no'] : '' ?>"  class="form-control rounded-pill" placeholder="Enter AWB No(s) separated by comma">
                                                    </div>
                                                    <div class="form-group col-sm-3">
                                                        <label class="fw-400">Method</label>
                                                        <select name="filter[pay_method]" class="form-control rounded-pill js-select2" aria-placeholder="Select">
                                                        <?php
                                                $pay_method = '';
                                                if (!empty($filter['pay_method']))
                                                    $pay_method = $filter['pay_method'];
                                                ?>
                                                <option <?php if ($pay_method == '') { ?> selected <?php } ?> value="">All</option>
                                                <option <?php if ($pay_method == 'cod') { ?> selected <?php } ?> value="cod">COD</option>
                                                <option <?php if ($pay_method == 'prepaid') { ?> selected <?php } ?> value="prepaid">Prepaid</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-row">
                                                    <div class="form-group col-sm-3">
                                                        <label class="fw-400">Courier</label>
                                                        <select name="filter[courier_id]"  class="form-control rounded-pill js-select2">
                                                        <option value="">All</option>
                                                <?php if (!empty($couriers)) foreach ($couriers as $courier) { ?>
                                                    <option value="<?= $courier->id; ?>" <?php if (!empty($filter['courier_id']) && $filter['courier_id'] == $courier->id) { ?> selected="" <?php } ?>><?= ucwords($courier->name); ?></option>
                                                <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-sm-3">
                                                        <label class="fw-400">Attempts</label>
                                                        <select name="filter[attempts]" class="form-control rounded-pill js-select2">
                                                        <?php
                                                $attempts = '';
                                                if (!empty($filter['attempts']))
                                                    $attempts = $filter['attempts'];
                                                ?>
                                                <option <?php if ($attempts == '') { ?> selected <?php } ?> value="">All</option>
                                                <option <?php if ($attempts == '1') { ?> selected <?php } ?> value="1">Attempt 1</option>
                                                <option <?php if ($attempts == '2') { ?> selected <?php } ?> value="2">Attempt 2</option>
                                                <option <?php if ($attempts == '3') { ?> selected <?php } ?> value="3">Attempt 3</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-sm-3">
                                                        <label class="fw-400">Channel</label>
                                                        <select  name="filter[channel_id]" class="form-control rounded-pill js-select2">
                                                        <option value="">Select Channel</option>
                                                <option value="custom" <?php if (!empty($filter['channel_id']) && $filter['channel_id'] == 'custom') { ?> selected <?php } ?>>Custom Orders</option>
                                                <?php
                                                foreach ($channels as $values) {
                                                    $channel_id = '';
                                                    if (!empty($filter['channel_id']))
                                                        $channel_id = $filter['channel_id'];
                                                ?>
                                                    <option <?php if ($channel_id == $values->id) { ?> selected <?php } ?> value="<?php echo $values->id; ?>"><?php echo ucwords($values->channel_name); ?></option>
                                                <?php
                                                }
                                                ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-sm-3">
                                                        <label class="fw-400">Tag(s)</label>
                                                        <input type="text" autocomplete="off" name="filter[tags]" value="<?= !empty($filter['tags']) ? $filter['tags'] : '' ?>" class="form-control rounded-pill" placeholder="Enter Tag name here ">
                                                    </div>
                                                </div>
                                                <div class="form-row">
                                                    <div class="form-group col-sm-12 text-center text-sm-right">
                                                        <button type="submit" class="btn btn-success rounded-pill px-2 px-sm-4" id=""> Apply</button>
                                                        <a href="<?= base_url('ndr'); ?>" class="btn btn-dark rounded-pill px-2 px-sm-4"> Clear</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                        </div>
                                        <?php
                                            $applied_filters = !empty($_GET) ? $_GET : array('filter' => array());
                                            $status_filters = $applied_filters;
                                            $status_filters['filter']['status'] = '';
                                            $btn_class = 'btn-default';
                                            ?>
                                        <div class="col-md-12">
                                            <div class="tab-data-sheet watsapp-traking">
                                                <div class="tab-pane fade show active ndr-dash-tab" id="Tracking_Page">
                                                    <ul class="nav nav-tabs " id="myTab" role="tablist">
                                                        <li class="nav-item mb-2 mb-sm-0">
                                                            <a href="<?= base_url('ndr') . '?' . http_build_query($status_filters); ?>" class="nav-link  <?= (  !isset($_GET['filter']['status'])   ||  (isset($_GET['filter']['status']) && $_GET['filter']['status']) == '') ? 'active' : ''; ?>" href="#Action_Required" ><span class="action_hide">Action</span> Required <span>(<?= (!empty($count_by_status->action_required)) ? $count_by_status->action_required : '0'; ?>)</span></a>
                                                        </li>
                                                        <?php
                                                            $status_filters['filter']['status'] = 'submitted';
                                                            ?>
                                                        <li class="nav-item ml-2 mb-2 mb-sm-0">
                                                            <a  href="<?= base_url('ndr') . '?' . http_build_query($status_filters); ?>" class="nav-link <?= (isset($_GET['filter']['status']) && $_GET['filter']['status'] == 'submitted') ? 'active' : ''; ?>"  href="#Action_Requested" ><span class="action_hide">Action</span>  Requested <span>(<?= (!empty($count_by_status->action_requested)) ? $count_by_status->action_requested : '0'; ?>)</span></a>
                                                        </li>
                                                        <li class="nav-item ml-2 mb-2 mb-sm-0">
                                                        <?php
                                                            $status_filters['filter']['status'] = 'delivered';
                                                            ?>
                                                            <a href="<?= base_url('ndr') . '?' . http_build_query($status_filters); ?>" class="nav-link <?= (isset($_GET['filter']['status']) && $_GET['filter']['status'] == 'delivered') ? 'active' : ''; ?>" href="#Delivered" >Delivered <span>(<?= (!empty($count_by_status->delivered)) ? $count_by_status->delivered : '0'; ?>)</span></a>
                                                        </li>
                                                        <li class="nav-item ml-2 mb-2 mb-sm-0">
                                                        <?php
                                                            $status_filters['filter']['status'] = 'rto';
                                                            ?>
                                                            <a href="<?= base_url('ndr') . '?' . http_build_query($status_filters); ?>" class="nav-link <?= (isset($_GET['filter']['status']) && $_GET['filter']['status'] == 'rto') ? 'active' : ''; ?>" href="#RTO" >RTO <span>(<?= (!empty($count_by_status->rto)) ? $count_by_status->rto : '0'; ?>)</span></a>
                                                        </li>
                                                    </ul>
                                                   
                                                    <div class="tab-content position-relative" id="myTabContent1">
                                                        <div class="tab-pane traking-page  py-4 px-2 fade show active" id="Action_Required" role="tabpanel" aria-labelledby="action-required-tab">

                                                            <div class="container-fluid">
                                                            <div class="row p-t-10 border-top p-b-10 action_row_selected bg-transparent sticky-top border-bottom" style="display: none;">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text  border-dark"> <b class="multiple_select_count">0</b>&nbsp;selected</span>
                            </div>
                            <div class="input-group-append">

                                <button type="button" class="btn btn-outline-dark btn-sm fill_bulk_ndr"><i class="mdi mdi-package-variant-closed"></i> Bulk NDR</button>
                                <button class="btn btn-outline-dark dropdown-toggle" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="mdi mdi-tag-multiple"></i> Tags
                                </button>

                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                    <button class="dropdown-item add_remove_tags_button" data-toggle="modal" data-tag-action="ndr/add" data-target=".add_remove_tags">Add Tags</button>
                                    <button class="dropdown-item add_remove_tags_button" data-toggle="modal" data-tag-action="ndr/remove" data-target=".add_remove_tags">Remove Tags</button>
                                </div>

                            </div>

                        </div>




                    </div>
                </div>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="table-responsive">
                                                                            <table class="table table-sm align-td-middle table-card">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>
                                                                                            <div class="d-flex p-0 card-header">
                                                                                                <input type="checkbox" class="form-check-input program_checkbox ml-0 mt-0 rounded-0" data-switch="true" style=" border: 2px solid #949799 !important;" id="select_all_checkboxes" >
                                                                                                <span class="ml-2">Channel</span>
                                                                                            </div>
                                                                                        </th>
                                                                                        <th style="width: 100px;">NDR Date</th>
                                                                                        <th> Order</th>
                                                                                        <th> Product</th>
                                                                                        <th> Payment</th>
                                                                                        <th> Customer</th>
                                                                                        <th> Carrier</th>
                                                                                        <th> Status</th>
                                                                                        <th> Tag</th>
                                                                                        <th> Exception Info</th>
                                                                                        <th width="108"> Action</th>
                                                                                        <?php if ($ivr_enabled) { ?>
                                                                                            <th>IVR</th>
                                                                                        <?php } ?>
                                                                                    </tr>
                                                                                </thead>

                                                                                <tbody>

                                                                                <?php
                                                                                    if (!empty($ndrs)) {
                                                                                        foreach ($ndrs as $ndr) {
                                                                                    ?>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <div class="d-flex p-0 card-header">
                                                                                            <?php if ($ndr->ndr_action == 'ndr' && !in_array($ndr->ship_status, array('rto', 'delivered'))) { ?>
                                                                                                <input value="<?= $ndr->id; ?>" type="checkbox" class="multiple_checkboxes" style=" border: 2px solid #949799 !important;" name="ndr_ids">
                                                                                            <?php } ?>
                                                                                                <span class="ml-2"><?= array_key_exists($ndr->channel_id, $user_channels) ? $user_channels[$ndr->channel_id] : 'Custom'; ?></span>
                                                                                            </div>
                                                                                        </td>
                                                                                        <td><?= date('Y-m-d', $ndr->created); ?></td>
                                                                                        <td><a target="_blank" class="text-info" href="orders/view/<?= $ndr->order_id; ?>"><?= $ndr->order_number; ?></a></td>
                                                                                         <td> <span data-toggle="tooltip" data-html="true" title="<?= $ndr->products; ?>"><?= mb_strimwidth($ndr->products, 0, 14, "..."); ?>
                                                                                          </span></td>
                                                                                        <td><?= $ndr->order_amount; ?>(<?= ucwords($ndr->order_payment_type) ?>)</td>
                                                                                        
                                                                                        <td>
                                                                                        <?php
                                                                                        $customername = ucwords($ndr->shipping_fname . ' ' . $ndr->shipping_lname);
                                                                                        $customerphn = isset($ndr->shipping_phone) ? $ndr->shipping_phone : '';
                                                                                        $customeradd1 = isset($ndr->shipping_address) ? $ndr->shipping_address : '';
                                                                                        $customeradd2 = isset($ndr->shipping_address_2) ? $ndr->shipping_address_2 : '';
                                                                                        $compltadd = $customeradd1 . ' ' . $customeradd2;
                                                                                        $shippcity = $ndr->shipping_city;
                                                                                        $shipstate = $ndr->shipping_state;
                                                                                        ?>
                                                                                        <span data-toggle="tooltip" data-html="true" title="<?= $customername . '<br>' . $customerphn . '<br>' . $compltadd . '<br>' . $shippcity . '<br>' . $shipstate; ?>">
                                                                                            <?= mb_strimwidth($customername, 0, 14, "..."); ?><br />
                                                                                        </span>
                                                                                        <?= $customerphn; ?>
                                                                                                                                    </td>
                                                                                                                                    <td> <?= strtoupper($ndr->courier_name); ?><br />
                                                                                        <a target="blank" class="text-info" href="shipping/tracking/<?= $ndr->awb_number ?>"><?= ucwords($ndr->awb_number); ?></a>
                                                                                                                                    </td>
                                                                                                                                    <td>
                                                                                                                                    <?= strtoupper($ndr->ship_status) ?> <?php if ($ndr->ship_status == 'rto' && !empty($ndr->rto_status)) {
                                                                                                                                    echo strtoupper($ndr->rto_status);
                                                                                                                                } ?> </td>
                                                                                                                                    <td> <?php if (!empty($ndr->ndr_applied_tags)) { ?>
                                                                                            <span data-toggle="tooltip" data-html="true" title="<?= str_replace(',', ', ', ucwords($ndr->ndr_applied_tags)); ?>">
                                                                                                <i class="mdi mdi-tag-multiple"></i>
                                                                                            </span>
                                                                                        <?php } ?></td>
                                                                                                                                    <td>

                                                                                                                                    <span class="d-block text-success"><?= $ndr->ndr_attempt; ?> Attempt(s)</span>
                                                                                        <?php if ($ndr->ndr_action == 'ndr') { ?>
                                                                                         <span class="d-block" >   <?= $ndr->ndr_remarks; ?></span>
                                                                                        <?php } ?>
                                                                                        <a class="text-info show_ndr_history" href="#" data-ndr-id="<?= $ndr->id; ?>">Show History</a>

                                                                                                                            
                                                                                                                                    </td>
                                                                                                                                    <td> <?php if ($ndr->ndr_action == 'ndr' && !in_array($ndr->ship_status, array('rto', 'delivered'))) { ?>
                                                                                            <button type="button" data-toggle="modal" data-target="#ndr_submit_model" class="btn btn-outline-primary btn-sm submit-ndr-response" data-ndr-id="<?= $ndr->id; ?>">Take Action</button>
                                                                                        <?php } elseif ($ndr->ndr_action != 'ndr') { ?>
                                                                                            <span class="text-success"><?= strtoupper($ndr->ndr_action) ?></span><br /> <?= $ndr->ndr_remarks; ?>
                                                                                        <?php } else { ?>
                                                                                            <?php echo '-'; ?>
                                                                                        <?php } ?>
                                                                                        </td>

                                                                                    <?php
                                                                                    if ($ivr_enabled) {
                                                                                    ?>
                                                                                        <td>
                                                                                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                                                <i class="mdi mdi-phone"></i>
                                                                                            </button>

                                                                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">

                                                                                                <button class="dropdown-item make_ivr_call" data-toggle="modal" data-target=".make_ivr_call_modal" data-ndr-id="<?= $ndr->id; ?>">Call</button>
                                                                                                <button class="dropdown-item view_ivr_history" data-toggle="modal" data-target=".ivr_history_modal" data-ndr-id="<?= $ndr->id; ?>">View History</button>
                                                                                            </div>
                                                                                        </td>
                                                                                    <?php
                                                                                    }
                                                                                    ?>
                                                                                    </tr>
                                                                                    <tr class="ndr_history_tr ndr_history_tr_<?= $ndr->id ?>" style="display: none;">
                                                                                        <td colspan="12" class="ndr_history_td_<?= $ndr->id ?>">
                                                                                        </td>
                                                                                    </tr>

                                                                                    <?php
                                                                                        }
                                                                                    } else {
                                                                                        ?>
                                                                                        <tr>
                                                                                            <td colspan="12" class="text-center">No Records Found</td>
                                                                                        </tr>
                                                                                    <?php
                                                                                    }
                                                                                    ?>
                                                                                   
                                                                                </tbody>
                                                                              
                                                                            </table>

                                                                              <!--Remark Popup start here-->
                                                                        <div class="modal fade modal-slide-right " id="ndr_submit_model" tabindex="-1" role="dialog" aria-labelledby="slideRightModalLabel" aria-hidden="true">
                                                                            <div class="modal-dialog" role="document" style="width: 415px !important;">
                                                                                <div class="modal-content" style="height:auto;">
                                                                                    <div class="modal-header">
                                                                                        <h5 class="modal-title" id="slideRightModalLabel">NDR Submit Form</h5>
                                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                            <span aria-hidden="true">&times;</span>
                                                                                        </button>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                        <form method="post" action="<?= base_url('ndr/action'); ?>" id="ndr_submit_form">
                                                                                            <div class="row">
                                                                                                <div class="col-lg-12">
                                                                                                    <div class="card-body">
                                                                                                        <div class="form-group">
                                                                                                            <input type="hidden" id="ndr_id" value="" name="ndr_id">
                                                                                                            <select name="action" class="form-control ndr_action_change">
                                                                                                                <option value="">Choose Action</option>
                                                                                                                <option value="re-attempt">Re-Attempt</option>
                                                                                                                <option value="change address">Change Address</option>
                                                                                                                <option value="change phone">Change Phone Number</option>
                                                                                                                <option value="rto">RTO</option>
                                                                                                            </select>
                                                                                                        </div>
                                                                                                        <div class="form-group ndr_action_change_fields ndr_action_change_field_reattempt" style="display: none;">
                                                                                                            <label>Re-Attempt Date</label>
                                                                                                            <select class="form-control" name="re_attempt_date_pre">
                                                                                                                <option value="">Choose Date</option>
                                                                                                                <option value="<?= strtotime('+1 day 23:59:59'); ?>"><?= date('d M (D)', strtotime('+1 day')) ?></option>
                                                                                                                <option value="<?= strtotime('+2 day 23:59:59'); ?>"><?= date('d M (D)', strtotime('+2 day')) ?></option>
                                                                                                                <option value="<?= strtotime('+3 day 23:59:59'); ?>"><?= date('d M (D)', strtotime('+3 day')) ?></option>
                                                                                                                <option value="<?= strtotime('+4 day 23:59:59'); ?>"><?= date('d M (D)', strtotime('+4 day')) ?></option>
                                                                                                                <option value="<?= strtotime('+5 day 23:59:59'); ?>"><?= date('d M (D)', strtotime('+5 day')) ?></option>
                                                                                                            </select>
                                                                                                        </div>
                                                                                                        <input type="hidden" value="<?= strtotime('+1 day 23:59:59'); ?>" name="re_attempt_date">


                                                                                                        <div class="form-group ndr_action_change_fields ndr_action_change_field_customer_details" style="display: none;">
                                                                                                            <label>Customer Name</label>
                                                                                                            <input class="form-control" type="text" value="" name="customer_details_name">
                                                                                                        </div>
                                                                                                        <div class="form-group ndr_action_change_fields ndr_action_change_field_customer_details" style="display: none;">
                                                                                                            <label>Customer Address 1</label>
                                                                                                            <input class="form-control" type="text" value="" name="customer_details_address_1">
                                                                                                        </div>
                                                                                                        <div class="form-group ndr_action_change_fields ndr_action_change_field_customer_details" style="display: none;">
                                                                                                            <label>Customer Address 2</label>
                                                                                                            <input class="form-control" type="text" value="" name="customer_details_address_2">
                                                                                                        </div>
                                                                                                        <div class="form-group ndr_action_change_fields ndr_action_change_field_customer_contact" style="display: none;">
                                                                                                            <label>Phone Number</label>
                                                                                                            <input class="form-control" type="text" value="" name="customer_contact_phone">
                                                                                                        </div>
                                                                                                        <div class="form-group">
                                                                                                            <label>Remark <small>(optional - Will not be shared with courier partner)</small></label>
                                                                                                            <textarea class="form-control" name="remarks" placeholder="Enter Remark"></textarea>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="modal-footer">
                                                                                                        <button type="submit" class="btn btn-primary">Submit</button>
                                                                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                                                                            Close
                                                                                                        </button>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row mt-2">
                                                                        <div class="col-sm-12 col-md-5">
                                                                            <div class="form-inline">
                                                                                <div class="form-group mb-2">
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
                                                                                <div class="form-group mx-2">
                                                                                    <div class="dataTables_info mt-n2" id="example-multi_info" role="status" aria-live="polite">Showing <?= $offset + 1; ?> to <?= $offset + $limit; ?> of <?= $total_records; ?> entries</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-12 col-md-7" >
                                                                                <div class="table-responsive mt-2 paging_simple_numbers">
                                                                                <ul class="pagination justify-content-start justify-content-sm-end" >
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
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            



        <div class="modal fade import_ndr_modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <form method="post" action="<?= base_url('ndr/import'); ?>" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="mySmallModalLabel">Bulk NDR Update</h5>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <div class="col-sm-12 p-all-15">
                            <div class="card">
                                <div class="card-body bg-info text-center text-white">NDR file format has been changed. Please follow the below given instruction.</div>
                            </div>
                        </div>


                        <div class="col-sm-12 p-b-10">
                            Export NDR and upload same file after updates.
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

                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-right">
                            <button type="submit" class="btn btn-primary">Upload</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>

                    <div class="row border-top m-t-20">
                        <div class="col-sm-12 p-t-10 text-center">
                            <b>How to use</b>
                        </div>
                        <div class="col-sm-12 p-t-10">
                            <ul>
                                <li>
                                    Export NDR using the export option given on this page.</li>
                                <li>
                                    Mention your NDR action in "Seller Action". Valid Values for Seller Action are <b>Re-Attempt</b>, <b>Change Phone</b>, <b>Change Address</b>, <b>RTO</b> </li>
                                <li>
                                    Depending on Seller action following fields are required: <br /><br />
                                    <b>Change Phone:</b>
                                    <ul>
                                        <li>Change Phone</li>
                                    </ul>
                                    <b>Change Address:</b>
                                    <ul>
                                        <li>Change Name</li>
                                        <li>Change Address 1</li>
                                    </ul>
                                </li>

                            </ul>
                        </div>

                    </div>
                </div>
            </form>

        </div>
    </div>
</div>


<div class="modal fade make_ivr_call_modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content" id="ivr_call_modal">

        </div>
    </div>
</div>


<div class="modal fade ivr_history_modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" id="ivr_history_data">

        </div>
    </div>
</div>


<script>
    <?php unset($_GET['perPage']); ?>

    function per_page_records(per_page = false) {
        var page_url = '<?= base_url('ndr/index') . '?' . http_build_query($_GET) . '&perPage=' ?>' + per_page;
        window.location.href = page_url;
    }

    $('.make_ivr_call').on('click', function(e) {
        e.preventDefault();
        var ndr_id = $(this).attr('data-ndr-id');
        $.ajax({
            url: 'apps/ivrcalls/call_ndr',
            type: "POST",
            data: {
                ndr_id: ndr_id,
            },
            cache: false,
            success: function(data) {
                $('#ivr_call_modal').html(data);

            }
        });
    });

    $('.view_ivr_history').on('click', function(e) {
        e.preventDefault();
        var ndr_id = $(this).attr('data-ndr-id');
        $.ajax({
            url: 'apps/ivrcalls/ndr_history',
            type: "POST",
            data: {
                ndr_id: ndr_id,
            },
            cache: false,
            success: function(data) {
                $('#ivr_history_data').html(data);

            }
        });
    });

    $('.ndr_action_change').on('change', function() {
        var ndr_action = $(this).val();
        $(".ndr_action_change_fields").hide();
        switch (ndr_action) {
            // case 're-attempt':
            //     $(".ndr_action_change_field_reattempt").show();
            //     break;
            case 'change address':
                $(".ndr_action_change_field_customer_details").show();
                break;
            case 'change phone':
                $(".ndr_action_change_field_customer_contact").show();
                break;
            default:
        }
    });
</script>