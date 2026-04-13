<div class="modal-header">
   <h5 class="modal-title" id="mySmallModalLabel"><i class="mdi mdi-account"></i> <span id="action_text"></span> employee access</h5>
</div>
<div class="modal-body">
   <div class="form-row">
      <div class="form-group col-6">
         <label for="inputPassword" class="col-form-label">Employee Name</label>
         <input type="text" class="form-control" id="name" required="" name="name" value="<?php if (!empty($employee_info->fname)) echo $employee_info->fname; ?>">
      </div>
      <div class="form-group col-6">
         <?php
            $set_def='';
             if (!empty($employee_info->id)) {
                 $set_def=false;
             ?>
         <input type="hidden"  name="employee_id" value="<?= $employee_info->id; ?>">
         <?php
            } else {
                $set_def=true;
            }
            ?>
         <label for="inputPassword" class="col-form-label">Employee Email</label>
         <input type="email" id="email" class="form-control" required="" name="email" value="<?php if (!empty($employee_info->email)) echo $employee_info->email; ?>">
      </div>
      <div class="form-group col-6">
         <label for="inputPassword" class="col-form-label">Employee Mobile No.</label>
         <input type="text" id="mobile" class="form-control" required="" name="phone" value="<?php if (!empty($employee_info->phone)) echo $employee_info->phone; ?>">                            
      </div>
      <div class="form-group col-6">
         <label for="inputPassword" class="col-form-label">Set Password  <?php if (!empty($employee_info)) { ?><small id="passwordHelpBlock" class="text-muted">
         (Leave blank if you don't want to update) <?php } ?>
         </small></label>
         <input type="password" id="password" <?php if (empty($employee_info)) { ?> required="" <?php } ?>class="form-control" name="password">
      </div>
      <div class="form-group col-12">
         <label for="inputPassword" class="label-title-cust">Set Permissions</label>
      </div>
      <?php
         $permissions = array();
         if (!empty($employee_info->permissions)) {
             $permissions = explode(',', $employee_info->permissions);
         }
         
         ?>
         <style>
            .mrg-cut .form-group{margin-bottom: 5px;}
         </style>

<div class="col-12 mrg-cut">
         <div class="form-row">
            <div class="form-group col-2">
               <label for="inputPassword" class="label-title-cust" style="height: 40px; display: flex; align-items:center;">Dashboard</label>
            </div>
            <div class="form-group col-4">
               <div class="option-box-grid d-block cust_chk">
                  <?= form_checkbox('permission[]', 'dashboard', set_value('', in_array('dashboard', $permissions) ? true : $set_def), 'class="custom-control-input ord_permission" id="check34"'); ?>
                  <label for="check34" class="w-100">
                  <span class="radio-content">
                  <span class="">Dashboard</span>
                  </span>
                  </label>
               </div>
            </div>
         </div>
      </div>

      <div class="col-12 mrg-cut">
         <div class="form-row">
            <div class="form-group col-2">
               <label for="inputPassword" class="label-title-cust" style="height: 40px; display: flex; align-items:center;">Orders</label>
            </div>
            <div class="form-group col-3">
               <div class="option-box-grid d-block cust_chk">
                  <?= form_checkbox('permission[]', 'orders', set_value('', in_array('orders', $permissions) ? true : $set_def), 'class="custom-control-input magage_orders_main ord_permission" id="check1"'); ?>
                  <label for="check1" class="w-100">
                  <span class="radio-content">
                  <span class="">Manage Orders</span>
                  </span>
                  </label>
               </div>
            </div>
            <div class="form-group col-3">
               <div class="option-box-grid d-block cust_chk">
                  <?= form_checkbox('permission[]', 'cancel_orders', set_value('', in_array('cancel_orders', $permissions) ? true : $set_def), 'class="custom-control-input magage_orders_chk ord_permission" id="check2"'); ?>
                  <label for="check2" class="w-100">
                  <span class="radio-content">
                  <span class="">Cancel Orders</span>
                  </span>
                  </label>
               </div>
            </div>
            <div class="form-group col-4">
               <div class="option-box-grid d-block cust_chk">
                  <?= form_checkbox('permission[]', 'change_payment_mode', set_value('', in_array('change_payment_mode', $permissions) ? true : $set_def), 'class="custom-control-input magage_orders_chk ord_permission" id="check3"'); ?>
                  <label for="check3" class="w-100">
                  <span class="radio-content">
                  <span class="">Change Payment Mode</span>
                  </span>
                  </label>
               </div>
            </div>
         </div>
      </div>
      <div class="col-12 mrg-cut">
         <div class="form-row">
            <div class="form-group col-2">
               <label for="inputPassword" class="label-title-cust" style="height: 40px; display: flex; align-items:center;">Shipments</label>
            </div>
            <div class="form-group col-3">
               <div class="option-box-grid d-block cust_chk" data-toggle="collapse" data-target="#demo1">
                  <?= form_checkbox('permission[]', 'shipments', set_value('', in_array('shipments', $permissions) ? true : $set_def), 'class="custom-control-input magage_shipments_main ord_permission" id="check5"'); ?>
                  <label for="check5" class="w-100">
                  <span class="radio-content">
                  <span class="">Manage Shipments</span>
                  </span>
                  </label>
               </div>
            </div>
            <div class="form-group col-3">
               <div class="option-box-grid d-block cust_chk">
                  <?= form_checkbox('permission[]', 'cancel_shipments', set_value('', in_array('cancel_shipments', $permissions) ? true : $set_def), 'class="custom-control-input magage_shipments_chk ord_permission" id="check6"'); ?>    
                  <label for="check6" class="w-100">
                  <span class="radio-content">
                  <span class="">Cancel Shipments</span>
                  </span>
                  </label>
               </div>
            </div>
         </div>
      </div>
      <div class="col-12 mrg-cut">
         <div class="form-row">
            <div class="form-group col-2">
               <label for="inputPassword" class="label-title-cust" style="height: 40px; display: flex; align-items:center;">Weight</label>
            </div>
            <div class="form-group col-4">
               <div class="option-box-grid d-block cust_chk">
                  <?= form_checkbox('permission[]', 'weight', set_value('', in_array('weight', $permissions) ? true : $set_def), 'class="custom-control-input ord_permission" id="check8"'); ?>
                  <label for="check8" class="w-100">
                  <span class="radio-content">
                  <span class="">Weight Reconciliation</span>
                  </span>
                  </label>
               </div>
            </div>
         </div>
      </div>
      <div class="col-12 mrg-cut">
         <div class="form-row">
            <div class="form-group col-2">
               <label for="inputPassword" class="label-title-cust" style="height: 40px; display: flex; align-items:center;">Settings</label>
            </div>
            <div class="form-group col-3">
               <div class="option-box-grid d-block cust_chk">
                  <?= form_checkbox('permission[]', 'apps', set_value('', in_array('apps', $permissions) ? true : $set_def), 'class="custom-control-input ord_permission" id="check4"'); ?>
                  <label for="check4" class="w-100">
                  <span class="radio-content">
                  <span class="">Addons</span>
                  </span>
                  </label>
               </div>
            </div>
            <div class="form-group col-3">
               <div class="option-box-grid d-block cust_chk">
                  <?= form_checkbox('permission[]', 'ndr', set_value('', in_array('ndr', $permissions) ? true : $set_def), 'class="custom-control-input ord_permission" id="check14"'); ?>
                  <label for="check14" class="w-100">
                  <span class="radio-content">
                  <span class="">Manage Exception</span>
                  </span>
                  </label>
               </div>
            </div>
            <div class="form-group col-3">
               <div class="option-box-grid d-block cust_chk">
                  <?= form_checkbox('permission[]', 'settings', set_value('', in_array('settings', $permissions) ? true : $set_def), 'class="custom-control-input ord_permission" id="check7"'); ?>
                  <label for="check7" class="w-100">
                  <span class="radio-content">
                  <span class="">Manage Settings</span>
                  </span>
                  </label>
               </div>
            </div>
         </div>
      </div>
      <div class="col-12 mrg-cut">
         <div class="form-row">
            <div class="form-group col-2">
               <label for="inputPassword" class="label-title-cust" style="height: 40px; display: flex; align-items:center;">Others</label>
            </div>
            <div class="form-group col-3">
               <div class="option-box-grid d-block cust_chk" data-toggle="collapse" data-target="#demo1">
                  <?= form_checkbox('permission[]', 'international,international_orders,international_shipments', set_value('', in_array('international', $permissions) ? true : $set_def), 'class="custom-control-input magage_shipments_main_type ord_permission" id="check17"'); ?>
                  <label for="check17" class="w-100">
                  <span class="radio-content">
                  <span class="">International</span>
                  </span>
                  </label>
               </div>
            </div>
            <div class="form-group col-3">
               <div class="option-box-grid d-block cust_chk">
                  <?= form_checkbox('permission[]', 'b2b,cargo_orders,cargo_shipments', set_value('', in_array('b2b', $permissions) ? true : $set_def), 'class="custom-control-input magage_shipments_chk_b2b ord_permission" id="check18"'); ?>    
                  <label for="check18" class="w-100">
                  <span class="radio-content">
                  <span class="">B2B</span>
                  </span>
                  </label>
               </div>
            </div>
            <div class="form-group col-3">
               <div class="option-box-grid d-block cust_chk">
                  <?= form_checkbox('permission[]', 'reports', set_value('', in_array('reports', $permissions) ? true : $set_def), 'class="custom-control-input ord_permission" id="check9"'); ?>                                    <label for="check9" class="w-100">
                  <span class="radio-content">
                  <span class="">Reports</span>
                  </span>
                  </label>
               </div>
            </div>
            <div class="form-group col-2"></div>
            <div class="form-group col-3">
               <div class="option-box-grid d-block cust_chk">
                  <?= form_checkbox('permission[]', 'billing', set_value('', in_array('billing', $permissions) ? true : $set_def), 'class="custom-control-input ord_permission" id="check10"'); ?>
                  <label for="check10" class="w-100">
                  <span class="radio-content">
                  <span class="">Billing</span>
                  </span>
                  </label>
               </div>
            </div>
            <div class="form-group col-3">
               <div class="option-box-grid d-block cust_chk">
                  <?= form_checkbox('permission[]', 'abandoned_checkouts', set_value('', in_array('abandoned_checkouts', $permissions) ? true : $set_def), 'class="custom-control-input ord_permission" id="check11"'); ?>
                  <label for="check11" class="w-100">
                  <span class="radio-content">
                  <span class="">Shopify Abandoned</span>
                  </span>
                  </label>
               </div>
            </div>
            
            
         </div>
      </div>
   </div>
</div>
<div class="modal-footer">
   <button type="submit" class="btn btn-primary">Save</button>
   <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>
<script>
   $(".magage_orders_chk").change(function () {
      
       let isChecked = $('.magage_orders_main').prop('checked');
       if (isChecked === false) {
           alert('Please Checked Manage Order first');
           $(this).prop('checked', false); // Unchecks it
           return false;
       }
   });
   $(".magage_orders_main").change(function () {
   
       let isChecked = $('.magage_orders_main').prop('checked');
       if (isChecked === false) {
           $('.magage_orders_chk').prop('checked', false); // Unchecks it
       }else{
         $('.magage_orders_chk').prop('checked', true);
       }
   });
   
   $(".magage_shipments_chk").change(function () {
       let isChecked = $('.magage_shipments_main').prop('checked');
       if (isChecked === false) {
           alert('Please Checked Manage Shipment first');
           $(this).prop('checked', false); // Unchecks it
           return false;
       }
   });
   $(".magage_shipments_main").change(function () {
       let isChecked = $('.magage_shipments_main').prop('checked');
       if (isChecked === false) {
           $('.magage_shipments_chk').prop('checked', false); // Unchecks it
       }else{
         $('.magage_shipments_chk').prop('checked', true);
       }
   });
   $(".magage_shipments_main_type").change(function () {
       let isChecked = $('.magage_shipments_main_type').prop('checked');
   });

   $(".magage_shipments_chk_b2b").change(function () {
       let isChecked = $('.magage_shipments_main_type').prop('checked');
   });
</script>