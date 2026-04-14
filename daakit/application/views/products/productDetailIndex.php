<?php
?>
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
               <h4 class="m-b-0"><i class="mdi mdi-checkbox-intermediate"></i> Products Weight Freeze </h4>
            </div>
            <div class="col-sm-6 text-right">
            <a href="/product/invoice_settings" class="btn btn-outline-dark btn-sm"><i class="mdi mdi-arrow-down-bold-circle"></i>Invoice Settings</a>
               <?php if (in_array('orders_export', $user_details->permissions)) { ?>
               <a href="<?= base_url('products/exportProductSkuCSV'); ?><?php if (!empty($filter)) {
                  echo "?" . http_build_query($_GET);
                  } ?>" class="btn btn-outline-dark btn-sm"><i class="mdi mdi-arrow-down-bold-circle"></i> Export</a>
               <?php } ?>
               
               <a href="/product/exportProductSkuCSV?" class="btn btn-outline-dark btn-sm"><i class="mdi mdi-arrow-down-bold-circle"></i> Export</a>
               <button class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target=".import_bulk_skumapping_modal"> <i class="mdi mdi-arrow-up-bold-circle"></i> Import</button>
               <button type="button" class="btn btn-outline-dark show_hide_filter btn-sm" <?php if (!empty($_GET['filter'])) { ?> style="display:none;" <?php } ?>><i class="icon-placeholder mdi mdi-filter"></i> Filters</button>
               <button type="button" class="btn btn-outline-dark show_hide_filter btn-sm" <?php if (empty($_GET['filter'])) { ?> style="display:none;" <?php } ?>><i class="icon-placeholder mdi mdi-close"></i> Close</button>
            </div>
         </div>
      </div>
      <div class="card-body">
   <form method="get" action="<?= base_url('product/all') ?>">
      <div class="row" id="filter_row" <?php if (empty($_GET['filter'])) { ?> style="display:none;" <?php } ?>>
         <div class="col-sm-12">
            <div class="card m-b-30">
               <div class="card-body">
                  <div class="form-row">
                     <div class="col-sm-4">
                        <label for="email"><span data-toggle="tooltip" data-html="true" title="" data-original-title="Search by Product name or SKU Details">Search by Product name or SKU Details:</span></label>
                        <input type="text" autocomplete="off" name="filter[search_query]" value="<?= !empty($filter['search_query']) ? $filter['search_query'] : '' ?>" class="form-control" placeholder="Search by Product name or SKU">
                     </div>
                      <div class="col-sm-2">
                        <label for="email"><span data-toggle="tooltip" data-html="true" title="" data-original-title="Search by Product id">Search by Product id:</span></label>
                        <input type="text" autocomplete="off" name="filter[pid]" value="<?= !empty($filter['pid']) ? $filter['pid'] : '' ?>" class="form-control" placeholder="Search by Product id">
                     </div>
                     <div class="form-group col-sm-4" style="margin-top:32px;">
                        <button type="submit" class="btn btn-sm btn-primary">Apply</button>
                        <a href="<?= base_url('product/all'); ?>" class="btn btn-sm btn-default">Clear</a>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </form>
  <!--  <div class="col-sm-12 bg-info text-white p-t-10 m-b-10 " style="
      padding-bottom: 1px;">
      <p><b>This section is under development. It can be used to raise weight freeze requests only. 
         </b>
      </p>
   </div> -->
   <div class="row border-top p-t-10 action_row_default">
      <div class="col-sm-1">
         <?php
            $applied_filters = !empty($_GET) ? $_GET : array('filter' => array());
            $status_filters = $applied_filters;
            $status_filters['filter']['status'] = '';
            $btn_class = 'btn-default';
            ?>
         <a href="<?= base_url('product/all') . '?' . http_build_query($status_filters); ?>" class="btn btn-block btn-sm m-b-15 ml-2 mr-2 <?= (!isset($_GET['filter']['status']) || $_GET['filter']['status'] == '') ? 'btn-info' : 'btn-default'; ?>">All</a>
      </div>
      <div class="col-sm-2">
         <?php
            $status_filters['filter']['status'] = '1';
            ?>
         <a href="<?= base_url('product/all') . '?' . http_build_query($status_filters); ?>" class="btn btn-block m-b-15 ml-2 mr-2 btn-sm  <?= (isset($_GET['filter']['status']) && $_GET['filter']['status'] == '1') ? 'btn-info' : 'btn-default'; ?>">Requested</a>
      </div>
      <div class="col-sm-2">
         <?php
            $status_filters['filter']['status'] = '2';
            ?>
         <a href="<?= base_url('product/all') . '?' . http_build_query($status_filters); ?>" class="btn btn-block m-b-15 ml-2 mr-2 btn-sm <?= (isset($_GET['filter']['status']) && $_GET['filter']['status'] == '2') ? 'btn-info' : 'btn-default'; ?>">Accepted</a>
      </div>
      <div class="col-sm-2">
         <?php
            $status_filters['filter']['status'] = '3';
            ?>
         <a href="<?= base_url('product/all') . '?' . http_build_query($status_filters); ?>" class="btn btn-block m-b-15 ml-2 mr-2 btn-sm <?= (isset($_GET['filter']['status']) && $_GET['filter']['status'] == '3') ? 'btn-info' : 'btn-default'; ?>">Rejected</a>
      </div>
   </div>
   <div class="row p-t-10 border-top p-b-10 action_row_selected sticky-top border-bottom" style="display: none;">
      <div class="col-sm-12">
         <div class="input-group">
            <div class="input-group-prepend">
               <span class="input-group-text  border-dark"> <b class="multiple_select_count">0</b>&nbsp;selected</span>
            </div>
            <div class="input-group-append">
               <button class="btn btn-outline-dark freeze-button" rel="1"> <i class="mdi mdi-arrow-up-bold-circle"></i>Request to Freeze</button>
            </div>
         </div>
      </div>
   </div>
   <div class="table-responsive">
      <table class="table table-hover table-sm">
         <thead>
            <tr>
               <th width="6%"><input data-switch="true" id="select_all_checkboxes" type="checkbox"></th>
               <th width="10%">PID</th>
               <th width="19%">Product Details</th>
               <th width="23%" align="center">Dimensions (LBH) in CM</th>
               <th width="12%">Weight (in grams) </th>
               <th width="10%">Auto Apply</th>
               <th width="10%">Weight Freeze Status</th>
               <th width="10%" class="text-center">Action</th>
            </tr>
         </thead>
         <tbody>
            <?php
               if (!empty($orders)) {
               // pr($orders);exit;

                   foreach ($orders as $order) {

                       $redonly = '';
                       $disabled = '';
                       if ($order->weight_locked == 2 || $order->weight_locked == 1) {
                           $redonly = 'readonly';
                           $disabled = 'disabled';
                       }
                       $product_sku = !empty($order->map_sku) ? $order->map_sku : $order->product_sku;
                       $weight = !empty($order->weight) ? $order->weight : $order->weight;
               ?>
            <tr>
               <form class="formid_update_products" method="post" id="formid_<?= $order->id; ?>">

                  <td> <?php
                     if (empty($order->weight_locked) || $order->weight_locked == 3) {                                                    ?>
                     <input value="<?= $order->id; ?>" type="checkbox" class="multiple_checkboxes">
                     <input type="hidden" name="product_ids[]" class="form-control" value="<?php if (!empty($order->id)) {
                        echo $order->id;
                        } ?>">
                     <?php }
                        ?>
                  </td>
                  <td><?= $order->id; ?></td>
                  <td>
                     <input type="hidden" value="<?php if (!empty($order->product_name)) {
                           echo $order->product_name;
                           } ?>" name="product_name" >
                     Product Name : <span data-toggle="tooltip" data-html="true" title="<?= $order->product_name; ?>">
                     <?= mb_strimwidth($order->product_name, 0, 20, "..."); ?>
                     </span><br>
                     <?php if(!empty($order->product_sku)){
                        echo "Product Sku  : ".trim($order->product_sku);
                     } ?></br>

                      <?php if(!empty($order->product_qty)){
                        echo "Product quantity : ".trim($order->product_qty);
                     } ?>

                  </td>
                 <!--  <td>
                     <input type="text"  id="product_sku_<?= $order->id; ?>" name="product_sku" class="form-control" placeholder="SKU" 
                        readonly value="<?php echo trim($order->product_sku); ?>">
                     <input type="hidden" name="product_id" class="form-control" value="<?php if (!empty($order->id)) {
                        echo $order->id;
                        } ?>">
                  </td> -->
                  <td>
                     <div class="input-group mb-3">
                        <div class="input-group-prepend">
                           <span class="input-group-text" id="basic-addon1" style="background-color: #edf2f9;">LBH</span>
                        </div>
                        <input type="text" id="length_<?= $order->id; ?>" name="length" maxlength="4" class="dimnsion numbers" <?php echo $redonly; ?> placeholder="CM" value="<?php if (!empty($order->length)) {
                           echo $order->length;
                           } ?>">
                        <input type="text" id="breadth_<?= $order->id; ?>" name="breadth" class="dimnsion numbers" maxlength="4" <?php echo $redonly; ?> placeholder="CM" value="<?php if (!empty($order->breadth)) {
                           echo $order->breadth;
                           } ?>">
                        <input type="text" id="height_<?= $order->id; ?>" name="height" class="dimnsion numbers" maxlength="4" <?php echo $redonly; ?> placeholder="CM" value="<?php if (!empty($order->height)) {
                           echo $order->height;
                           } ?>">
                             <input type="hidden"  id="product_sku_<?= $order->id; ?>" name="product_sku" class="form-control" 
                           value="<?php echo trim($order->product_sku); ?>">

                             <input type="hidden"   name="product_qty"  
                        value="<?php echo trim($order->product_qty); ?>">

                     <input type="hidden" name="product_id" class="form-control" value="<?php if (!empty($order->id)) {
                        echo $order->id;
                        } ?>">
                     </div>
                  </td>
                  <td>
                     <div class="input-group mb-2">
                        <input type="text" id="weight_<?= $order->id; ?>" name="weight" style="width: 60% !important" class="dimnsion numbers" placeholder="gram" maxlength="6" <?php echo $redonly; ?> value="<?php
                           echo $weight;
                           ?>">
                     </div>
                  </td>
                 <!--  <td>
                     <div class="input-group mb-2">
                        <input type="text" id="igst_<?= $order->id; ?>" name="igst" style="width: 45% !important" class="dimnsion numbers" maxlength="10" placeholder=""  value="<?php if (!empty($order->igst)) {
                           echo $order->igst;
                           } ?>">
                     </div>
                  </td>
                  <td>
                     <div class="input-group mb-2">
                        <input type="text" id="hsn_<?= $order->id; ?>" maxlength="10" name="hsn" class="dimnsion numbers" placeholder="" style="width: 45% !important" value="<?php if (!empty($order->hsn_code)) {
                           echo $order->hsn_code;
                           } ?>">
                     </div>
                  </td> -->
                    <!--  <div class="input-group mb-2">
                        <input name="is_weight" id="is_weight_<?= $order->id; ?>" value="1" <?php echo !empty($order->is_weight) ? 'checked' : ''; ?> type="checkbox" class="dimnsion numbers"> 
                        <label class="cstm-switch">
                                    <input type="checkbox" id="is_weight_<?= $order->id; ?>"  <?php echo !empty($order->is_weight) ? 'checked' : ''; ?> onchange="this.checked =   change_status(<?= ($order->is_weight==1)?0:1; ?>,<?= $order->id;?>,<?= $order->weight_locked;?>)" name="option" value="0" class="cstm-switch-input">
                                     <span class="cstm-switch-indicator bg-success"></span>
                                    <span class="cstm-switch-description" id="weight_apply_success"> </span>                            </label>
                     </div> -->

                   <td>  
                     <div class="input-group mb-2">
                        <input name="is_weight" id="is_weight_<?= $order->id; ?>" value="1" <?php echo !empty($order->is_weight) ? 'checked' : ''; ?> type="checkbox" <?php 
                           echo $disabled; ?> class="dimnsion numbers">
                     </div>
                  </td>
                  </td>
                  <td>
                     <div class="input-group mb-2">
                        <?php if (!empty($order->weight_locked)) {
                           echo ($order->weight_locked == 1) ? 'Requested' : (($order->weight_locked == '2') ? 'Accepted' : 'Rejected');
                           } else{
                              echo "Not Requested";
                           } ?>
                     </div>
                  </td>
                  <td width="10%" align="center">
                    <!--  <button class="btn btn-outline-info btn-sm dropdown-toggle" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding-top: 8px;">
                     Take Action
                     </button>
                     <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        <button type="submit" class="dropdown-item" title="Save">Update</button>
                        <?php if ((empty($order->weight_locked) || $order->weight_locked == 3)&&(!empty($order->id))) { ?>
                        <a href="#" rel="1" data-id="<?= $order->id; ?>" class="dropdown-item freeze-button_action">Request to Freeze</a>
                        <?php  } ?>
                       <?php if (!empty($order->escalation_id)) { ?>
                        <a href="escalations/view/<?php echo $order->escalation_id; ?>" target="_blank" class="dropdown-item">View Escalations</a>
                        <?php  } ?>
                     </div> -->

                               <button type="submit" class="btn btn-outline-success btn-sm">Update</button>
                                <button class="btn btn-sm btn-outline-info dropdown-toggle" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                               
                                                    
                                                </button>

                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                   <?php if ((empty($order->weight_locked) || $order->weight_locked == 3)&&(!empty($order->id))) { ?>
                                                    <button class="dropdown-item freeze-button_action"  rel="1"  data-id="<?= $order->id; ?>" >Request to Freeze</button>
                                                     <?php  } ?>
                                                                            <?php if (!empty($order->escalation_id)) { ?>
                        <a href="escalations/view/<?php echo $order->escalation_id; ?>" target="_blank" class="dropdown-item">View Escalation</a>
                        <?php  } ?>
                                                    <!-- <button class="dropdown-item view_ivr_history" data-toggle="modal" data-target=".ivr_history_modal" data-order-id="<?= $order->id; ?>">View History</button> -->
                                                </div>
                                                                                  
   </div>
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
         <form method="post" action="<?= base_url('product/import'); ?>" enctype="multipart/form-data">
            <div class="modal-header">
               <h5 class="modal-title" id="mySmallModalLabel">Bulk Upload Products Weight Freeze</h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-sm-12 p-b-10">
                     Download sample products weight freeze upload file : <a class="text-info" href="<?= base_url('assets/bulk_product_sku_mapping_sample.csv'); ?>">Download</a>
                  </div>
                  <div class="col-sm-12 m-t-10">
                     <div class="m-b-10">
                        <div class="mb-3">
                           <div class="">
                              <input type="file" class="" name="importFile">
                           </div>
                        </div>
                     </div>
                     
                     <p style="color:red;margin-bottom: 0rem; font-size:13px;">Note:</p>
                     <p style="color:red;margin-bottom: 0rem; font-size:13px;">For Updation of any product PID Column is required.</p>
                     <p style="color:red; font-size:13px;">For Enter the New product Leave Blank the PID column.</p>
         
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
<div class="modal fade escalation_remarks_modal" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="slideRightModalLabel">Request to weight freeze</h5>
            </div>
            <div class="modal-body">
                <form method="post" enctype="multipart/form-data" id="submit">
                    <div class="row">
                        <div class="col-lg-12">

                            <div class="form-group">
                                <label>Remarks </label>
                                <textarea class="form-control" required name="remarks" placeholder="Enter Remark"></textarea>
                                <small>After Submit you can't change LBH and Weight</small>
                           
                            </div>

                            <div class="form-group files">
                                <label>Attachments <span style="color:red">*</span></label>
                                <input type="file" name="importFile[]" multiple="multiple" id="filetoupload" class="form-control" multiple="">
                                <small>Maximum file size : 5 MB</small>
                             </div>
                            <!-- <div class="gallery col-lg-12">
                             </div> -->
                            <div class="modal-footer">
                            
                                <input type="hidden" name="weight_freze_status" id="weight_freze_status" class="form-control" value="1">
                                <input type="hidden" name="product_ids" id="product_ids" class="form-control" value="">
                                <input type="hidden" name="length_ids" id="length_ids" class="form-control" value="">
                                <input type="hidden" name="breadth_ids" id="breadth_ids" class="form-control" value="">
                                <input type="hidden" name="height_ids" id="height_ids" class="form-control" value="">
                                <input type="hidden" name="weight_ids" id="weight_ids" class="form-control" value="">
                               <!--  <input type="hidden" name="gst_ids" id="gst_ids" class="form-control" value="">
                                <input type="hidden" name="hsn_ids" id="hsn_ids" class="form-control" value=""> -->
                                <input type="hidden" name="is_weights" id="is_weights" class="form-control" value="">
                                <input type="hidden" name="product_sku_ids" id="product_sku_ids" value="">
     
                                <button type="submit" id="freeze_submit" class="btn btn-primary">Submit</button>
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
<script>
  
   $(document).ready(function(){
   $("#freeze_submit").prop("disabled",false);

   });
   function change_status(weight_apply_status,id,weight_freze_status){
      if(weight_apply_status){
         if(!(weight_freze_status)){
            alert("Your need to update and freeze weight first")
            return false;
         }
         if(weight_freze_status != 2){
          alert("Your weight freeze request is not accepted")
          return false;
         }
      }
        
        statusname = "remove apply";
        if(weight_apply_status==1){
            statusname = "apply";
        }
        if(!confirm("Do you want to "+statusname+" weight")){
            return false;
        }

         $.ajax({
            url: 'product/apply_weight',
            type: "POST",
            data: {
                weight_apply_status: weight_apply_status,
                id :id,
                weight_freze_status:weight_freze_status

            },
            cache: false,
            success: function(data) {
                if (data.success){
                     alert(data.success);
                     location.reload();
                }else if (data.error)
                    alert(data.error);
            }
        });
    }
   <?php unset($_GET['perPage']); ?>
   
   function per_page_records(per_page = false) {
       var page_url = '<?= base_url('product/all') . '?' . http_build_query($_GET) . '&perPage=' ?>' + per_page;
       window.location.href = page_url;
   }
   $(".formid_update_products").submit(function(event) {
       event.preventDefault();
       $.ajax({
           url: "product/productUpdate",
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


     $('#submit').submit(function(e) {
        e.preventDefault();
            $.ajax({
                url: 'product/modalproductUpdate',
                type: "post",
                data: new FormData(this),
                processData: false,
                contentType: false,
                cache: false,
                async: false,
                dataType: "json",
                success: function(json) {
                    if (json.success) {
                        alert(json.success);
                        $("#freeze_submit").prop("disabled",true);
                        location.reload();
                    } else {
                        alert(json.error);
                    }
                }
            });
     });


       $('.freeze-button').on('click', function(event) {
       // alert('freeze-button');
        event.preventDefault();
        var weight_freze_status = $(this).attr('rel');
        var product_ids = [];
        var length_ids = [];
        var breadth_ids = [];
        var height_ids = [];
        var weight_ids = [];
        // var gst_ids = [];
        // var hsn_ids = [];
        var product_skuid = [];
        var is_weightid = [];
     
        valid = true;
        $("input:text").removeClass('invalid');
        $.each($("input[class='multiple_checkboxes']:checked"), function() {
            var length = $('#length_' + $(this).val()).val();
            var breadth = $('#breadth_' + $(this).val()).val();
            var height = $('#height_' + $(this).val()).val();
            var weight = $('#weight_' + $(this).val()).val();
            // var gst = $('#igst_' + $(this).val()).val();
            // var hsn = $('#hsn_' + $(this).val()).val();
            var product_sku = $('#product_sku_' + $(this).val()).val();
            var is_weight  = $('#is_weight_' + $(this).val()).is(":checked");
            if (length == "") {
                valid = false;
                $('#length_' + $(this).val()).addClass('invalid');
            }
            if (breadth == "") {
                valid = false;
                $('#breadth_' + $(this).val()).addClass('invalid');
            }
            if (height == "") {
                valid = false;
                $('#height_' + $(this).val()).addClass('invalid');
            }
            if (weight == "") {
                valid = false;
                $('#weight_' + $(this).val()).addClass('invalid');
            }

            product_ids.push($(this).val());
            length_ids.push(length);
            breadth_ids.push(breadth);
            height_ids.push(height);
            weight_ids.push(weight);
            // gst_ids.push(gst);
            // hsn_ids.push(hsn);
            is_weightid.push(is_weight);
            product_skuid.push(product_sku.replace(/,/g, '@'));
            
        });
        if (valid) {
            $('#exampleModal').modal('show');
            $('#weight_freze_status').val(weight_freze_status);
            $('#product_ids').val(product_ids.join(","));
            $('#length_ids').val(length_ids.join(","));
            $('#breadth_ids').val(breadth_ids.join(","));
            $('#height_ids').val(height_ids.join(","));
            $('#weight_ids').val(weight_ids.join(","));
            // $('#gst_ids').val(gst_ids.join(","));
            // $('#hsn_ids').val(hsn_ids.join(","));
            $('#product_sku_ids').val(product_skuid.join(","));
            $('#is_weights').val(is_weightid.join(","));
            
        }else {
            alert("Please enter the valid LBH and Weight");
            return true;
        }
      
    });

    $('.freeze-button_action').on('click', function(event) {
        event.preventDefault();
        var weight_freze_status = $(this).attr('rel');
        var product_ids = [];
        var length_ids = [];
        var breadth_ids = [];
        var height_ids = [];
        var weight_ids = [];
        // var gst_ids = [];
        // var hsn_ids = [];
        var product_skuid = [];
        var is_weightid = [];
     
        valid = true;
        $("input:text").removeClass('invalid');

        $('input[name="locationthemes"]:checked')
        var length = $('#length_' + $(this).attr('data-id')).val();
            var breadth = $('#breadth_' + $(this).attr('data-id')).val();
            var height = $('#height_' + $(this).attr('data-id')).val();
            var weight = $('#weight_' + $(this).attr('data-id')).val();
            //var gst = $('#igst_' + $(this).attr('data-id')).val();
            var product_sku = $('#product_sku_' + $(this).attr('data-id')).val();
            //var hsn = $('#hsn_' + $(this).attr('data-id')).val();
            var is_weight  = $('#is_weight_' + $(this).attr('data-id')).is(":checked");

            if (length == "") {
                valid = false;
                $('#length_' + $(this).attr('data-id')).addClass('invalid');
            }
            if (breadth == "") {
                valid = false;
                $('#breadth_' + $(this).attr('data-id')).addClass('invalid');
            }
            if (height == "") {
                valid = false;
                $('#height_' + $(this).attr('data-id')).addClass('invalid');
            }
            if (weight == "") {
                valid = false;
                $('#weight_' + $(this).attr('data-id')).addClass('invalid');
            }

            product_ids.push($(this).attr('data-id'));
            length_ids.push(length);
            breadth_ids.push(breadth);
            height_ids.push(height);
            weight_ids.push(weight);
            // gst_ids.push(gst);
            // hsn_ids.push(hsn);
            product_skuid.push(product_sku);
            is_weightid.push(is_weight);
  
            if (valid) {
            $('#exampleModal').modal('show');
            $('#weight_freze_status').val(weight_freze_status);
            $('#product_ids').val(product_ids.join(","));
            $('#length_ids').val(length_ids.join(","));
            $('#breadth_ids').val(breadth_ids.join(","));
            $('#height_ids').val(height_ids.join(","));
            $('#weight_ids').val(weight_ids.join(","));
            // $('#gst_ids').val(gst_ids.join(","));
            // $('#hsn_ids').val(hsn_ids.join(","));
             $('#product_sku_ids').val(product_skuid.join(","));
             $('#is_weights').val(is_weightid.join(","));
        }else {
            alert("Please enter the valid LBH and Weight");
            return false;
       }
       
    });
</script>