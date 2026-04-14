<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">Company KYC Details</h4>
    <ol class="breadcrumb">
	<?php if(!empty($kycdetails[0]->companytype)){ 
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
				<option value="profile/kycdetails" selected>KYC</option>
				<option value="profile/legalentity">GST Details</option>
				<option value="profile/cmpaccountdetails">Bank A/C Details</option>
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
				<h3 class="card-title">Company KYC</h3>
			</div>
			<div class="card-body">
			<?php
			if (!empty($kycdetails[0]->companytype) && $kycdetails[0]->verified !=0) {
				if ($kycdetails[0]->companytype == "Sole Proprietorship") {
			?>
					<div class="table-responsive">
						<table class="table card-table table-bordered table-vcenter text-dark table-outline text-nowrap">
							<thead>
								<tr>
									<th><span class="bold">Document Image</span></th>
									<th><span class="bold">Type</span></th>
									<th><span class="bold">Document Type</span></th>
									<th><span class="bold">Document No</span></th>
									<th><span class="bold">Document Name</span></th>
								</tr>
							</thead>
							<tbody>
								<?php
								foreach ($kycdetails as $pr) {
								?>
									<tr>
										<td>
											<a style="margin-top: 0%;margin-left: 10%;" href="<?php
											echo ($pr->documentimage) ? $pr->documentimage : '';?>" target="_blank" class="btn btn-primary btn-sm">View
											</a>
										</td>
										<td><?= $pr->companytype; ?></td>
										<td><?= $pr->document_type; ?></td>
										<td><?= $pr->kycdoc_id; ?></td>
										<td><?= $pr->kycdoc_name; ?></td>
									</tr>
								<?php
								}
								?>
							</tbody>
						</table>
					</div>
				<?php
				} else {
				?>
					<div class="table-responsive">
						<table class="table card-table table-bordered table-vcenter text-dark table-outline text-nowrap">
							<thead>
								<tr>
									<th><span class="bold">Document Image</span></th>
									<th><span class="bold">Pan Card Image</span></th>
									<th><span class="bold">Pan No</span></th>
									<th><span class="bold">Pan Name</span></th>
									<th><span class="bold">Type</span></th>
									<th><span class="bold">Document Type</span></th>
									<th><span class="bold">Document Id</span></th>
									<th><span class="bold">Document Name</span></th>
								</tr>
							</thead>
							<tbody>
								<?php
								foreach ($kycdetails as $pr) {
								?>
									<tr>
										<td>
											<a style="margin-top: 0%;margin-left: 10%;" href="<?php
											echo ($pr->documentimage) ? $pr->documentimage : '';?>" target="_blank" class="btn btn-primary btn-sm">View
											</a>
										</td>
										<td>
											<a style="margin-top:0%;margin-left: 10%;" href="<?php
											echo ($pr->pancarddocumentimage) ? $pr->pancarddocumentimage : '' ?>" target="_blank" class="btn btn-primary btn-sm">View</a>
										</td>
										<td><?= $pr->cmppanno; ?></td>
										<td><?= $pr->cmppanname; ?></td>
										<td><?= $pr->companytype; ?></td>
										<td><?= $pr->document_type; ?></td>
										<td><?= $pr->kycdoc_id; ?></td>
										<td><?= $pr->kycdoc_name; ?></td>
									</tr>
								<?php
								}
								?>
							</tbody>
						</table>
					</div>
				<?php
				}
			}  else {
			?>
			<form method="post" action="<?= current_url(); ?>" enctype="multipart/form-data">
				<div class="card-body" id="companyform" style="padding: 0px !important;">
					<div class="row">
						<div class="col-md-6">
							<br>
							<div class="form-row">
								<div class="form-group col-md-12">
									<label>Select Type of KYC</label>
									<select class="form-control" name="companytype" id="companytype">
										<option value="">Select Type</option>
										<option value="Sole Proprietorship" <?php if (!empty($kycdetails[0]->companytype) && $kycdetails[0]->companytype=='Sole Proprietorship') echo 'selected';?>  >Sole Proprietorship</option>
										<option value="Partnership" <?php if (!empty($kycdetails[0]->companytype) && $kycdetails[0]->companytype=='Partnership') echo 'selected';?>>Partnership</option>
										<option value="Limited Liability Partnership" <?php if (!empty($kycdetails[0]->companytype) && $kycdetails[0]->companytype=='Limited Liability Partnership') echo 'selected';?>>Limited Liability Partnership</option>
										<option value="Public Limited Company" <?php if (!empty($kycdetails[0]->companytype) && $kycdetails[0]->companytype=='Public Limited Company') echo 'selected';?>>Public Limited Company</option>
										<option value="Private Limited Company" <?php if (!empty($kycdetails[0]->companytype) && $kycdetails[0]->companytype=='Private Limited Company') echo 'selected';?>>Private Limited Company</option>
									</select>
								</div>
							</div>
						</div>
					</div>

					<!--Company Subcategory Type  soleproprietorship form start here-->
					<div class="row" id="soleproprietorship" <?php if (!empty($kycdetails[0]->companytype) && $kycdetails[0]->companytype=='Sole Proprietorship') {echo 'style="display:block;"';} else {echo 'style="display:none;"';};?>>
						<div class="col-md-6">
							<div class="form-row">
								<div class="form-group col-md-12 required">
									<label class="control-label">Document Type</label>
									<select class="form-control" name="document_type">
										<option value="">Select Document Type</option>
										<option <?php if($profile->document_type=='Aadhar Card')echo "selected"; ?> value="Aadhar Card">Aadhar Card</option>
										<option <?php if($profile->document_type=='Bank Account Statment')echo "selected"; ?> value="Bank Account Statment">Bank Account Statment</option>
										<option <?php if($profile->document_type=='Driving License')echo "selected"; ?> value="Driving License">Driving License</option>
										<option <?php if($profile->document_type=='Valid Passport')echo "selected"; ?> value="Valid Passport">Valid Passport</option>
										<option <?php if($profile->document_type=='Voter Id Card')echo "selected"; ?> value="Voter Id Card">Voter Id Card</option>
										<option <?php if($profile->document_type=='Pan Card')echo "selected"; ?> value="Pan Card">Pan Card</option>
									</select>
								</div>
							</div>
							<div class="form-row">
								<div class="form-group col-md-6 required">
									<label class="control-label">Document ID</label>
									<input type="text" name="kycdoc_id" class="form-control" value="<?= set_value('kycdoc_id', !empty($profile->kycdoc_id) ? $profile->kycdoc_id : ''); ?>" />
								</div>
								<div class="form-group col-md-6 required">
									<label class="control-label">Name on Document</label>
									<input type="text" name="kycdoc_name" class="form-control" value="<?= set_value('kycdoc_name', !empty($profile->kycdoc_name) ? $profile->kycdoc_name : ''); ?>" />
								</div>
							</div>

							<div class="form-row">
								<div class="form-group col-md-6 required">
									<label class="control-label">Upload Document Image</label>
									<div class="input-group mb-3">
										<input type="file" name="documentimage">
									</div>
								</div>
								<div class="form-group col-md-3">
									<span class="image-box-content">
										<?php if (!empty($profile->documentimage)) { ?>
											<a style="margin-top: 17%;margin-left: 0%;" href="<?php echo (strpos($profile->documentimage, "amazonaws.com") !== false) ? ($profile->documentimage) : ($profile->documentimage); ?>" target="_blank" class="btn btn-primary btn-sm">View</a>
										<?php } else { ?>
											<!-- <img src="assets/seller_company_logo/dummy_img.jpg" id="logo_img" width="100" height="100"> -->
										<?php } ?>
									</span>
								</div>	
							</div>
							<div class="form-group text-right">
								<button class="btn btn-primary"  style="margin-top: 26px;">Save</button>
							</div>
					</div>
						</div>
					<!--Company Subcategory Type soloproprietorship form stop here-->

					<!--Company Subcategory Type partnership form start here-->
					<div class="row" id="partnership" <?php if (!empty($kycdetails[0]->companytype) && $kycdetails[0]->companytype!='Sole Proprietorship') {echo 'style="display:block;"';} else {echo 'style="display:none;"';};?>>
						<div class="col-md-6">
							<div class="form-row">
								<div class="form-group col-md-12 required">
									<label class="control-label">Document 1 - Company PAN Card Number</label>
									<input type="text" name="cmppanno" readonly class="form-control" value="<?= set_value('cmppanno', !empty($profile->cmppanno) ? $profile->cmppanno : (!empty($kycdetails[0]->cmp_pan) ? $kycdetails[0]->cmp_pan :'') ); ?>" />
								</div>
								<div class="form-group col-md-12 required">
									<label class="control-label">Enter Name on Pan Card</label>
									<input type="text" name="cmppanname" class="form-control" value="<?= set_value('cmppanname', !empty($profile->cmppanname) ? $profile->cmppanname : ''); ?>" />
								</div>
							</div>

							<div class="form-row">
								<div class="form-group col-md-6 required">
									<label class="control-label">Upload Document Image</label>
									<input type="file" class="form-control" name="pancarddocumentimage">
								</div>
								<div class="form-group col-md-3">
									<span class="image-box-content">
										<?php if (!empty($profile->pancarddocumentimage)) { ?>
											<a style="margin-top: 20%" href="<?php echo (strpos($profile->pancarddocumentimage, "amazonaws.com") !== false) ? ($profile->pancarddocumentimage) : ($profile->pancarddocumentimage); ?>" target="_blank" class="btn btn-primary btn-sm">View</a>
										<?php } else { ?>
										<?php } ?>
									</span>
								</div>	
							</div>
							<br>
							<h6>Select Supporting documents</h6>
							<p></p>
							<div class="form-row">
								<div class="form-group col-md-12 required">
									<label class="control-label">Document Type</label>
									<select class="form-control" name="company_document_type">
										<option value="">Select Document Type</option>
										<option value="Bank Account Statment" <?php if($profile->document_type=='Bank Account Statment')echo "selected"; ?>>Bank Account Statment</option>
										<option value="Electricity Bill" <?php if($profile->document_type=='Electricity Bill')echo "selected"; ?>>Electricity Bill</option>
										<option value="Lease / Rent Agreement" <?php if($profile->document_type=='Lease / Rent Agreement')echo "selected"; ?>>Lease / Rent Agreement</option>
										<option value="Telephone Bill" <?php if($profile->document_type=='Telephone Bill')echo "selected"; ?>>Telephone Bill</option>
									</select>
								</div>
							</div>
							<div class="form-row">
								<div class="form-group col-md-6 required">
									<label class="control-label">Document ID</label>
									<input type="text" name="company_kycdoc_id" class="form-control" value="<?= set_value('kycdoc_id', !empty($profile->kycdoc_id) ? $profile->kycdoc_id : ''); ?>" />
								</div>
								<div class="form-group col-md-6 required">
									<label class="control-label">Name on Document</label>
									<input type="text" name="company_kycdoc_name" class="form-control" value="<?= set_value('kycdoc_name', !empty($profile->kycdoc_name) ? $profile->kycdoc_name : ''); ?>" />
								</div>
							</div>

							<div class="form-row">
								<div class="form-group col-md-6 required">
									<label class="control-label">Upload Document Image</label>
									<div class="input-group mb-3">
										<input type="file" class="form-control" name="companydocumentimage">
									</div>
								</div>
								<div class="form-group col-md-3">
									<span class="image-box-content">
										<?php if (!empty($profile->documentimage)) { ?>
											<a style="margin-top: 20%" href="<?php echo (strpos($profile->documentimage, "amazonaws.com") !== false) ? ($profile->documentimage) : ($profile->documentimage); ?>" target="_blank" class="btn btn-primary btn-sm">View</a>
										<?php } else { ?>
										<?php } ?>
									</span>
								</div>
							</div>
							<div class="form-group">
								<button class="btn btn-primary"style="margin-top: 26px;">Save</button>
							</div>
						</div>
					</div>
					<!--Company Subcategory Type partnership form stop here-->
				</div>
				<!--Company form stop here-->
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
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script>
	$('#companytype').change(function() {
		var cmpvalue = $(this).val();
		if (cmpvalue == "Sole Proprietorship") {
			$("#soleproprietorship").show();
			$("#partnership").hide();
		} else if (cmpvalue == "Partnership" || cmpvalue == "Limited Liability Partnership" || cmpvalue == "Public Limited Company" || cmpvalue == "Private Limited Company") {
			$("#soleproprietorship").hide();
			$("#partnership").show();
		} else {
			$("#soleproprietorship").hide();
			$("#partnership").hide();
		}
	})
	function hrefUrlLocation(path)
	{
		var baseURL = '<?php echo base_url(); ?>';
        // Redirect to a specific path
        window.location.href = baseURL + path;
	}
</script>