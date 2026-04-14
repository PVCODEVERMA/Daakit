<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">HSN/GST Mapping</h4>
    <ol class="breadcrumb">
      <li class="breadcrumb-item btn-list">
      <a href="<?= base_url('product/exportProductSkuBillingCSV'); ?><?php if (!empty($filter)) {
                echo "?" . http_build_query($_GET);
            } ?>" class="btn btn-info btn-sm me-2"> Retrieve Export </a>
            <a href="javascript:void(0);" class="btn btn-info btn-sm me-2"  data-bs-toggle="modal" data-bs-target=".import_bulk_skumapping_modal">Import</a>
            <a href="javascript:void(0);" class="btn btn-info btn-sm me-2" data-bs-toggle="sidebar-right" data-bs-target=".sidebar-right"> Filter </a>
        </li>
    </ol>
</div>
<!-- END PAGE-HEADER -->
 <!-- filter section start -->

 <div class="sidebar sidebar-right sidebar-animate">
	<div class="p-4">
		<a href="#" class="float-end sidebar-close" data-bs-toggle="sidebar-right" data-bs-target=".sidebar-right"><i class="fa fa-times"></i></a>
	</div>
    <br>
	<div class="panel-body tabs-menu-body side-tab-body p-0 border-0 ">
		<div class="tab-content border-top">
			<div class="tab-pane active" id="tab1">
				<div class="chat">
					<div class="contacts_card">
						<div class="input-group p-3">
                     <form method="post" action="<?= base_url('product/mapping') ?>">
                        <div class="row">
                              <div class="col-md-12">
                                 <div class="form-group" app-field-wrapper="from_date">
                                    <label for="email" class="control-label">Product/SKU</label>
                                    <input type="text" autocomplete="off" style="width:275px" name="filter[search_query]" value="<?= !empty($filter['search_query']) ? $filter['search_query'] : '' ?>" class="form-control" placeholder="Search by Product name or SKU">
                                 </div>
                              </div>
                           </div>
                           <div class="row">
                              <div class="col-md-12">
                                 <div class="form-group" app-field-wrapper="from_date">
                                    <label for="email" class="control-label"><span data-toggle="tooltip" data-html="true" title="" data-original-title="Search by Product id">Search by Product id:</span></label>
                                    <input type="text" autocomplete="off" name="filter[pid]" value="<?= !empty($filter['pid']) ? $filter['pid'] : '' ?>" class="form-control" placeholder="Search by Product id">
                                 </div>
                              </div>
                           </div>
                           <div class="row">
                              <div class="col-md-6" style="margin-top:29px;">
                                    <div class="form-group" app-field-wrapper="to_date">
                                       <button type="submit" class="btn btn-sm btn-success">Filter</button>
                                       <a href="<?= current_url() ?>" class="btn btn-sm btn-primary">Reset</a>
                                    </div>
                              </div>
                           </div>
                        </form>
                  </div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
 <!-- filter section end-->
<div class="main-container container-fluid">
<!-- START ROW-1 -->
<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="card">
			<div class="card-header">
                <h3 class="card-title">Product - HSN/GST Mapping</h3>
			</div>
            <div class="card-body">
            <div id="responsive-datatable_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                <div class="row">
                    <div class="col-sm-1 col-md-1">
                        <div class="dataTables_length" id="responsive-datatable_length">
                        <label>Show
                            <?php
                                $per_page_options = array(
                                    '10' => '10',
                                    '20' => '20',
                                    '50' => '50',
                                    '100' => '100',
                                    '200' => '200',
                                    '500' => '500',
                                );

                                $js = "class='form-select form-select-sm select2' onchange='per_page_records(this.value)'";
                                echo form_dropdown('per_page', $per_page_options, $limit, $js);
                                ?>
                        </label>
                        </div>
                    </div>
                    <div class="col-sm-11 col-md-11 text-right action_row_selected" style="display: none;" style="margin-top: -7px;">
                        <div class="dataTables_length" id="responsive-datatable_length">
                            <div class="btn btn-sm ms-auto">
                              <a href="javascript:void(0)" class="btn btn-sm btn-primary"><b class="multiple_select_count">0</b>&nbsp;selected</a>
                              <button class="btn btn-sm btn-primary update_hsn_gst" rel="1">Update HSN & GST</button>
                           </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                     <table class="table table-bordered border-bottom dataTable no-footer">
                        <thead>
                           <tr>
                              <th width="10%">PID</th>
                              <th width="15%">Product Name</th>
                              <th width="15%">Product Sku</th>
                              <th width="12%">GST %</th>
                              <th width="15%">HSN</th>
                              <th width="8%"><input data-switch="true" id="select_all_checkboxes" type="checkbox"></th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php
                              if (!empty($orders)) {
                                 foreach ($orders as $order) {
                              ?>
                              <tr>
                              <form class="formid_update_products" method="post" id="formid_<?= $order->id; ?>">

                                 <td><?= $order->id ?></td>
                                 
                                 <td>
                                    <input type="hidden" value="<?php if (!empty($order->product_name)) {
                                          echo $order->product_name;
                                          } ?>" name="product_name" >
                                    <span data-toggle="tooltip" data-html="true" title="<?= $order->product_name; ?>">
                                    <?= mb_strimwidth($order->product_name, 0, 20, "..."); ?>
                                    </span><br>
                                 
                                 </td>
                                 
                                 <td>
                                    <input type="hidden" value="<?php if (!empty($order->product_sku)) {
                                          echo $order->product_sku;
                                          } ?>" name="product_sku" >
                                 <?php if(!empty($order->product_sku)){
                                       echo trim($order->product_sku);
                                    } ?></br>
                                 </td> 
                              
                                 <input type="hidden" name="product_id" class="form-control" value="<?php if (!empty($order->id)) {
                                       echo $order->id;
                                       } ?>">
                                       <input type="hidden" id="product_name_<?= $order->id ?>" value="<?= $order->product_name; ?>" name="product_name">
                                       <input type="hidden" id="product_sku_<?= $order->id ?>" value="<?= $order->product_sku; ?>" name="product_name">
                                 <td>
                                    <div class="input-group mb-2">
                                       <input type="text" id="igst_<?= $order->id; ?>" name="igst" style="width: 45% !important" class="form-control" maxlength="10" placeholder=""  value="<?php if (isset($order->igst)) {
                                          echo $order->igst;
                                          } ?>">
                                    </div>
                                 </td>
                                 <td>
                                    <div class="input-group mb-2">
                                       <input type="text" id="hsn_<?= $order->id; ?>" maxlength="10" name="hsn" class="form-control" placeholder="" style="width: 45% !important" value="<?php if (!empty($order->hsn_code)) {
                                          echo $order->hsn_code;
                                          } ?>">
                                    </div>
                                 </td>
                                 

                              
                                 <td>
                                 <input value="<?= $order->id ?>" type="checkbox" class="multiple_checkboxes" name="product_ids" data-gtm-form-interact-field-id="0">                                    
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
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END ROW-1 -->
</div>
<!-- SCROLLING WITH COTENT MODAL START -->
<div class="modal fade import_bulk_skumapping_modal" id="scrollmodal">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content modal-content-demo">
            <div class="modal-header">
                <h6 class="modal-title">HSN/GST Mapping (Bulk)</h6><button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"></button>
            </div>
            <form action="<?= base_url('product/importbilling'); ?>" enctype="multipart/form-data" method="post" accept-charset="utf-8" data-gtm-form-interact-id="0">
                <div class="modal-body">
                        <div class="col-lg-12 col-sm-12 mb-4 mb-lg-0">
                        <div class="form-group">
                        <p>Download sample file : <a class="text-info" href="<?= base_url('assets/bulk_product_sku_billing_mapping_sample.csv'); ?>"><i class="fa fa-download" aria-hidden="true"></i></a></p>
                        <br>
                        <input class="form-control" type="file" name="importFile" required>
                    </div>               
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn ripple btn-primary" type="submit">Upload File</button>
                    <button class="btn ripple btn-secondary" data-bs-dismiss="modal" type="button">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade bd-modal-lg"  role="dialog" id="lgscrollmodal">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" id="show_information">
        </div>
    </div>
</div>
<form id="tab_filter" action="<?php base_url();?>" method="POST">
    <input type="hidden" name="perPage" id="perPage" />
    <input type="hidden" autocomplete="off" name="filter[fulfillment]" id="fulfillment" value="<?= !empty($filter['fulfillment']) ? $filter['fulfillment'] : '' ?>">
    <input type="hidden" autocomplete="off" name="filter[segment_id]" id="segment_id" value="<?= !empty($filter['segment_id']) ? $filter['segment_id'] : '' ?>">
</form>
<!-- SCROLLING WITH COTENT MODAL END -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
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
       var page_url = '<?= base_url('product/mapping') . '?' . http_build_query($_GET) . '&perPage=' ?>' + per_page;
       window.location.href = page_url;
   }
   $(".formid_update_products").submit(function(event) {

       event.preventDefault();
       $.ajax({
           url: baseUrl+"product/productBillingUpdate",
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
        var gst_ids = [];
        var hsn_ids = [];
        var product_skuid = [];
        var is_weightid = [];
     
        valid = true;
        $("input:text").removeClass('invalid');
        $.each($("input[class='multiple_checkboxes']:checked"), function() {
            var length = $('#length_' + $(this).val()).val();
            var breadth = $('#breadth_' + $(this).val()).val();
            var height = $('#height_' + $(this).val()).val();
            var weight = $('#weight_' + $(this).val()).val();
            var gst = $('#igst_' + $(this).val()).val();
            var hsn = $('#hsn_' + $(this).val()).val();
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
            gst_ids.push(gst);
            hsn_ids.push(hsn);
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
            $('#gst_ids').val(gst_ids.join(","));
            $('#hsn_ids').val(hsn_ids.join(","));
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
        var gst_ids = [];
        var hsn_ids = [];
        var product_skuid = [];
        var is_weightid = [];
     
        valid = true;
        $("input:text").removeClass('invalid');

        $('input[name="locationthemes"]:checked')
        var length = $('#length_' + $(this).attr('data-id')).val();
            var breadth = $('#breadth_' + $(this).attr('data-id')).val();
            var height = $('#height_' + $(this).attr('data-id')).val();
            var weight = $('#weight_' + $(this).attr('data-id')).val();
            var gst = $('#igst_' + $(this).attr('data-id')).val();
            var product_sku = $('#product_sku_' + $(this).attr('data-id')).val();
            var hsn = $('#hsn_' + $(this).attr('data-id')).val();
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
            gst_ids.push(gst);
            hsn_ids.push(hsn);
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
            $('#gst_ids').val(gst_ids.join(","));
            $('#hsn_ids').val(hsn_ids.join(","));
             $('#product_sku_ids').val(product_skuid.join(","));
             $('#is_weights').val(is_weightid.join(","));
        }else {
            alert("Please enter the valid LBH and Weight");
            return false;
       }
       
    });


    $(document).ready(function() {
      $('.update_hsn_gst').on('click',function(){
         var orderids = [];
         $.each($("input[class='multiple_checkboxes']:checked"), function() {
            orderids.push($(this).val());
         });
         if(orderids==''){
            alert_float(`Please check at least one checkbox.`);
         }
         $('#product_ids').val(orderids.join());
         var orders = new Set(orderids);
         if(orders!=[])
         {
            // Validate Both Fields 
            let data = {};
            let cnt=0;
            orders.forEach((value)=>{
               if($(`#igst_${value}`).val()=='' && cnt==0)
               {
                  alert_float(`GST % can not be left blank at PID ${value} row.`);
                  $(`#igst_${value}`).focus();
                  return true;
               }
               else if($(`#hsn_${value}`).val()=='' && cnt==0)
               {
                  alert_float(`HSN can not be left blank at PID ${value} row.`);
                  $(`#hsn_${value}`).focus();
                  return true;
               }
               cnt=1;
               data={
                  "product_id":value,
                  "igst":$(`#igst_${value}`).val(),
                  'hsn':$(`#hsn_${value}`).val(),
                  'product_name' : $(`#product_name_${value}`).val(),
                  'product_sku' : $(`#product_sku_${value}`).val(),
               };
               $.ajax({
                  url: baseUrl+"product/productBillingUpdate",
                  type: "POST",
                  data: data,
                  cache: false,
                  success: function(data) {
                  }
               });
            });
            if(cnt=='1')
            {
               console.log('in one conditoin');
               alert_float('Data has been updated successfully.','notice');
            }
         }
      });

       $('.numbers').keyup(function() {
           this.value = this.value.replace(/[^0-9\.]/g, '');
       });
   });

</script>