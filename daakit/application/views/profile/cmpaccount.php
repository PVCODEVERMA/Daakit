<?php //pr($checkProcessingState);?>

<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">Company Account Details</h4>
    <ol class="breadcrumb">
    <?php if(!empty($bankdetails[0]->cmp_accno)){ 
			?>
			<button class="btn btn-success" style="border-radius: unset;">
				<i class="fa fa-check-circle" style="font-size: large;" aria-hidden="true"></i>
			</button>
			<?php
		}
		else{
			?>
			<button class="btn btn-danger" style="border-radius: unset;">
				<i class="fa fa-times-circle" style="font-size: large;" aria-hidden="true"></i>
			</button>
		<?php
		}
		?>
      <li class="breadcrumb-item">						
         <select class="form-control" onchange="return hrefUrlLocation(this.value)" style="width: 100% !important;border-radius: unset;">
            <option value="profile">Company Profile</option>
				<option value="profile/kycdetails">KYC</option>
				<option value="profile/legalentity">GST Details</option>
				<option value="profile/cmpaccountdetails" selected>Bank A/C Details</option>
				<option value="profile/agreement">Aggrement</option>
			</select>
		</li>
    </ol>
</div>
<!-- END PAGE-HEADER -->
<div class="main-container container-fluid">

<!-- START ROW-1 -->
<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">Company Account Details</h3>
			</div>
			<div class="card-body">
            <?php
            if (!empty($bankdetails[0]->cmp_accntholder) && !empty($bankdetails[0]->cmp_accno) && !empty($bankdetails[0]->cmp_bankname) && !empty($bankdetails[0]->cmp_bankbranch) && !empty($bankdetails[0]->cmp_acctype) && !empty($bankdetails[0]->cmp_accifsc)) {
               ?>
                  <div class="table-responsive">
                     <table class="table card-table table-bordered table-vcenter text-dark table-outline text-nowrap">
                        <thead>
                           <tr>
                              <th  style="text-align:center"><span class="bold">Action</span></th>
                              <th><span class="bold">Account Name</span></th>
                              <th><span class="bold">Account No</span></th>
                              <th><span class="bold">Bank Name</span></th>
                              <th><span class="bold">Bank Branch</span></th>
                              <th><span class="bold">Account Type</span></th>   
                              <th><span class="bold">IFSC Code</span></th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php
                           foreach ($bankdetails as $pr) {
                           ?>
                              <tr>
                                 <td>
                                    <a  href="<?php echo ($pr->cmp_chequeimg) ? $pr->cmp_chequeimg : ''; ?>" target="_blank" class="btn btn-primary btn-sm">View</a>
                                    <?php if (empty($checkProcessingState)) { ?>
                                    <a href="<?php echo base_url('profile/request_bank_account');?>" target="_blank" class="btn btn-primary btn-sm">Update Bank Account</a>
                                 <?php } ?>

                                 </td>
                                 <td style="word-break: break-all;"><?= $pr->cmp_accntholder; ?></td>
                                 <td><?= $pr->cmp_accno; ?></td>
                                 <td style="word-break: break-all;"><?= $pr->cmp_bankname; ?></td>
                                 <td style="word-break: break-all;"><?= $pr->cmp_bankbranch; ?></td>
                                 <td><?= $pr->cmp_acctype; ?></td>
                                 <td><?= $pr->cmp_accifsc; ?></td>
                              </tr>
                           <?php
                           }
                           ?>
                        </tbody>
                     </table>
                  </div>
                  <br>
                  <?php if (!empty($bank_verification_details)) { ?>
                     <div class="bank_details text-center">
                        <h4 class="card-title">
                           <p>Account Changes History</p>
                        </h4>
                     </div>
                     <div class="table-responsive">
                        <table class="table card-table table-bordered table-vcenter text-dark table-outline text-nowrap">
                           <thead>
                              <tr>
                                 <th><span class="bold">Account name</span></th>
                                 <th><span class="bold">Account number</span></th>
                                 <th><span class="bold">Bank</span></th>
                                 <th><span class="bold">Bank</span></th>
                                 <th><span class="bold">Ifsc</span></th>
                                 <th><span class="bold">Account type</span></th>
                                 <th><span class="bold">Cheque Image</span></th>
                                 <th><span class="bold">Status</th>
                              </tr>
                           </thead>
                           <?php
                           foreach ($bank_verification_details as $prs) {
                           ?>
                              <tr>
                                 <!-- <td>
                           <a style="margin-top: 0%;margin-left: 10%;" href="<?php echo (strpos($pr->cmp_chequeimg, "amazonaws.com") !== false) ? ($pr->cmp_chequeimg) : (base_url() . 'assets/seller_company_Cheque/' . $pr->cmp_chequeimg); ?>" target="_blank" class="btn btn-primary btn-sm">View</a>
                           </td> -->
                                 <td style="word-break: break-all;"><?= $prs->cmp_accntholder; ?></td>
                                 <td><?= $prs->cmp_accno; ?></td>
                                 <td style="word-break: break-all;"><?= $prs->cmp_bankname; ?></td>
                                 <td style="word-break: break-all;"><?= $prs->cmp_bankbranch; ?></td>
                                 <td><?= $prs->cmp_accifsc; ?></td>
                                 <td><?= $prs->cmp_acctype; ?></td>
                                 <td>
                                    <a style="margin-top: 0%;margin-left: 10%;" href="<?php echo ($prs->cmp_chequeimg) ? $prs->cmp_chequeimg : ''; ?>" target="_blank" class="btn btn-primary btn-sm">View</a>
                                 </td>
                                 <td> <?php if ($prs->status == 1) {
                                          echo '<button type="button"  class="btn btn-success btn-sm">Approved</button>';
                                       } else if ($prs->status == 2) {
                                          echo '<button type="button"  class="btn btn-danger btn-sm">Rejected</button><br>';
                                       ?>
                                       <button type="button" data-toggle="modal" data-target="#rejectmodal" onclick="reject_reason('<?= trim($prs->reject_reason) ?>')" class="btn btn-warning btn-sm">Remarks</button>
                                    <?php    } else {
                                          echo '<button type="button"  class="btn btn-outline-warning btn-sm">Processing</button>';
                                       } ?>
                                 </td>
                              </tr>
                           <?php
                           }
                           ?>
                           </tbody>
                        </table>
                     </div>
                  <?php } ?>
               </div>
            <?php
            } else {
            ?>
               <form method="post" action="<?= current_url(); ?>" enctype="multipart/form-data">
                  <div class="card-body">
                     <div class="row">
                        <div class="col-md-12">
                           <div class="form-row">
                              <div class="form-group col-md-6 required">
                                 <label class="control-label">Account Name</label>
                                 <input type="text" name="cmp_accntholder" class="form-control" value="<?= set_value('cmp_accntholder', !empty($profile->cmp_accntholder) ? $profile->cmp_accntholder : ''); ?>" />
                              </div>
                              <div class="form-group col-md-6 required">
                                 <label class="control-label">Account No.</label>
                                 <input type="text" name="cmp_accno" class="form-control" value="<?= set_value('cmp_accno', !empty($profile->cmp_accno) ? $profile->cmp_accno : ''); ?>" />
                              </div>
                           </div>
                           <div class="form-row">
                              <div class="form-group col-md-6 required">
                                 <label class="control-label">Bank Name</label>
                                 <input type="text" name="cmp_bankname" class="form-control" value="<?= set_value('cmp_bankname', !empty($profile->cmp_bankname) ? $profile->cmp_bankname : ''); ?>" />
                              </div>
                              <div class="form-group col-md-6 required">
                                 <label class="control-label">Bank Branch</label>
                                 <input type="text" name="cmp_bankbranch" class="form-control" value="<?= set_value('cmp_bankbranch', !empty($profile->cmp_bankbranch) ? $profile->cmp_bankbranch : ''); ?>" />
                              </div>
                           </div>
                           <div class="form-row">
                              <div class="form-group col-md-6 required">
                                 <label class="control-label">Account Type</label>
                                 <select class="form-control" name="bankacctype">
                                    <option id="hidetandcpopup" <?php if ($profile->cmp_acctype == '' || empty($profile->cmp_acctype)) { ?> selected="" <?php } ?> value="">Select Account Type</option>
                                    <option <?php if ($profile->cmp_acctype == 'Current Account') { ?> selected="" <?php } ?> value="Current Account">Current Account</option>
                                    <option id="showtandcpopup" <?php if ($profile->cmp_acctype == 'Saving Account') { ?> selected="" <?php } ?> value="Saving Account">Saving Account</option>
                                 </select>
                              </div>
                              <div class="form-group col-md-6 required">
                                 <label class="control-label">IFSC Code</label>
                                 <input type="text" name="cmp_accifsc" class="form-control" value="<?= set_value('cmp_accifsc', !empty($profile->cmp_accifsc) ? $profile->cmp_accifsc : ''); ?>">
                              </div>
                           </div>
                        </div>
                        <div class="col-md-12">
                           <div class="form-row">
                              <div class="form-group col-md-6 required">
                                    <label class="control-label">Upload Cancelled Cheque</label>
                                    <input type="file" name="chequeimage" id="cancelcheque" class="form-control" >
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="form-group text-right">
                        <button class="btn btn-primary">Save</button>
                     </div>
                  </div>
            </div>
            </form>
         <?php
         }
         ?>
         </div>
		</div>
	</div>
</div>
<!-- END ROW-1 -->
</div>
<div class="modal fade bs-modal-sm" id="rejectmodal" tabindex="-1" role="dialog" aria-labelledby="rejectmodalLabel" aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title" id="rejectmodalLabel">Remark</h4>
         </div>
         <div class="modal-body">
            <p id="reject_reason">
            </p>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
         </div>
      </div>
   </div>
</div>
<script>
   function reject_reason(message) {
      $("#reject_reason").text(message);
   }

   function readURL(input) {
      if (input.files && input.files[0]) {
         var reader = new FileReader();

         reader.onload = function(e) {
            $('#cancelchequepreview').attr('src', e.target.result);
         }

         reader.readAsDataURL(input.files[0]);
      }
   }

   $("#cancelcheque").change(function() {
      readURL(this);
   });
	function hrefUrlLocation(path)
	{
		var baseURL = '<?php echo base_url(); ?>';
        // Redirect to a specific path
        window.location.href = baseURL + path;
	}
</script>