<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">Profile and Company Details</h4>
    <ol class="breadcrumb">
		<?php if(!empty($profile->cmp_email)){ 
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
				<h3 class="card-title">Company Details</h3>
			</div>
			<div class="card-body">
				<form method="post" action="<?= current_url(); ?>" enctype="multipart/form-data">
					<div class="row">
						<div class="col-md-12">
							<div class="form-row">
								<div class="form-group col-md-6">
									<label class="control-label">Company Name </label>
									<input type="text" disabled name="company_name" class="form-control" value="<?= set_value('company_name', !empty($profile->company_name) ? $profile->company_name : ''); ?>" />
								</div>
								<div class="form-group col-md-6">
									<label class="control-label">Website URL</label>
									<input type="text" name="cmp_url" class="form-control" value="<?= set_value('cmp_url', !empty($profile->cmp_url) ? $profile->cmp_url : ''); ?>" />
								</div>
							</div>
							<div class="form-row">
								<div class="form-group col-md-6 required">
									<label class="control-label">Email</label>
									<input type="email" name="cmp_email" class="form-control" value="<?= set_value('cmp_email', !empty($profile->cmp_email) ? (!empty($profile->cmp_email) ? $profile->cmp_email : '') : $profile->email); ?>" />
								</div>
								<div class="form-group col-md-6 required">
									<label class="control-label">Contact No. <i class="fa fa-question-circle" aria-hidden="true" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="The Contact Number field must be exactly 10 characters in length."></i>
									</label>
									<input type="text" name="cmp_phone" class="form-control" value="<?= set_value('cmp_phone', !empty($profile->cmp_phone) ? (!empty($profile->cmp_phone) ? $profile->cmp_phone : '') : $profile->phone); ?>">
								</div>
							</div>
							<div class="form-row">
								<div class="form-group col-md-3 required">
									<label class="control-label">Pan Number <i class="fa fa-question-circle" aria-hidden="true" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="The Pan Number field must be exactly 10 characters in length"></i>
									</label>
									<input type="text" name="cmp_pan" class="form-control" value="<?= set_value('cmp_pan', !empty($profile->cmp_pan) ? $profile->cmp_pan : ''); ?>" />
								</div>
								<div class="form-group col-md-3">
									<label class="control-label">CIN <i class="fa fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="The Company CIN field must be exactly 21 characters in length." aria-hidden="true"></i>
									</label>
									<input type="text" name="cmp_cin" class="form-control" value="<?= set_value('cmp_cin', !empty($profile->cmp_cin) ? $profile->cmp_cin : ''); ?>" />
								</div>
								<div class="form-group col-md-3">
									<label class="control-label">Upload Your Company Logo</label>
									<div class="input-group mb-6">
										<div class="custom-file">
											<input type="file" class="form-control" name="picture" id="imglogo">
										</div>
									</div>
								</div>
								<div class="form-group col-md-3">
									<span class="image-box-content">
										<?php if (!empty($profile->cmp_logo)) { ?>
											<img src="<?php echo (strpos($profile->cmp_logo, "amazonaws.com") !== false) ? ($profile->cmp_logo) : ($profile->cmp_logo); ?>" width="100" height="100">
										<?php } else { ?>
											<!-- <img src="assets/seller_company_logo/dummy_img.jpg" id="logo_img" width="100" height="100"> -->
										<?php } ?>
									</span>
								</div>	
							</div>
							<div class="form-row">
								<div class="form-group col-md-6 required">
									<label class="control-label">Company Address</label>
									<textarea class="form-control" name="cmp_address"><?= set_value('cmp_address', !empty($profile->cmp_address) ? $profile->cmp_address : ''); ?></textarea>
								</div>
								<div class="form-group col-md-6 required">
									<label class="control-label">Pin Code <i class="fa fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="The Pincoce field must be exactly 6 characters in length." aria-hidden="true"></i></label>
									<input type="text" name="cmp_pincode" class="form-control" value="<?= set_value('cmp_pincode', !empty($profile->cmp_pincode) ? $profile->cmp_pincode : ''); ?>" />
								</div>
							</div>
							<div class="form-row">
								<div class="form-group col-md-6 required">
									<label class="control-label">City</label>
									<input type="text" name="cmp_city" class="form-control" value="<?= set_value('cmp_city', !empty($profile->cmp_city) ? $profile->cmp_city : ''); ?>" />
								</div>
								<div class="form-group col-md-6 required">
									<label class="control-label">State</label>
									<select name="cmp_state" class="form-control js-select2" style="width: 100% !important;">
										<option value="">Select State</option>
										<?php
										asort($state_codes);
										foreach ($state_codes as $key => $values) {
										?>
											<option <?php if (!empty($profile->cmp_state) && strtolower($profile->cmp_state) == strtolower($values)) { ?> selected <?php } ?> value="<?php echo ucwords($values); ?>"><?php echo ucwords($values); ?></option>
										<?php
										}
										?>
									</select>

								</div>
							</div>
							<div class="form-row">
							</div>
						</div>
						<div class="col-md-12" >
							<div class="form-row">

								</div>
								<div class="form-row">
									<!-- <div class="form-group col-md-6">
										<label>Upload Your GST</label>
										<div class="input-group mb-6">
											<div class="custom-file">
												<input type="file" class="form-control" name="gstimage" id="gstimage">
											</div>
										</div>
									</div> -->
									<!-- <div class="form-group col-md-6">
										<span class="image-box-content">
											<?php if (!empty($profile->cmp_gstimg)) {
											?>
												<img src="<?php echo (strpos($profile->cmp_gstimg, "amazonaws.com") !== false) ? ($profile->cmp_gstimg) : ($profile->cmp_gstimg); ?>" width="100" height="100">
											<?php } else { ?>
												 <img src="assets/seller_company_logo/dummy_img.jpg" id="logo_img" width="100" height="100"> 
											<?php } ?>
										</span>
									</div> -->
								</div>
							</div>
						</div>
					<div class="clearfix"></div>
					<div class="btn-bottom-toolbar text-right">
						<button type="submit" data-form="#project_form" class="btn btn-info" autocomplete="off" data-loading-text="Please wait...">Save</button>
					</div>
				</form>
			</div>
		</div>
	</div>

</div>
<!-- END ROW-1 -->

</div>
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script>
	function readURL(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function(e) {
				$('#logo_img').attr('src', e.target.result);
			}

			reader.readAsDataURL(input.files[0]);
		}
	}

	$("#imglogo").change(function() {
		readURL(this);
	});


	function readURLgst(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function(e) {
				$('#gst_image').attr('src', e.target.result);
			}

			reader.readAsDataURL(input.files[0]);
		}
	}

	$("#gstimage").change(function() {
		readURLgst(this);
	});

	function readURLsign(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function(e) {
				$('#signature_image').attr('src', e.target.result);
			}

			reader.readAsDataURL(input.files[0]);
		}
	}

	$("#signatureimg").change(function() {
		readURLsign(this);
	});

	function companychangefunction() {
		var value = document.getElementById("companybvalue").value;
		if (value == 'soloproprietorship') {
			document.getElementById("soloproprietorship_div").style.display = "block";
			document.getElementById("partnership_div").style.display = "none";
		} else if (value == 'partnership') {
			document.getElementById("soloproprietorship_div").style.display = "none";
			document.getElementById("partnership_div").style.display = "block";
		} else {
			document.getElementById("individualrow").style.display = "none";
			document.getElementById("companyrow").style.display = "none";
		}
	}
	function hrefUrlLocation(path)
	{
		var baseURL = '<?php echo base_url(); ?>';
        // Redirect to a specific path
        window.location.href = baseURL + path;
	}
</script>