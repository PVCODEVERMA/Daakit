
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">
  <title><?php echo $title;?></title>
  <link rel="shortcut icon" id="favicon" href="<?php echo base_url('assets/images/dakit-favicon.gif');?>">
<link rel="apple-touch-icon”" id="favicon-apple-touch-icon" href="<?php echo base_url();?>assets/build/assets/iconfonts/icons.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" id="reset-css" href="<?php echo base_url();?>assets/build/assets/plugins/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" id="bootstrap-css" href="<?php echo base_url();?>assets/build/assets/app-6d59ac94.css">
<link rel="stylesheet" type="text/css" id="roboto-css" href="<?php echo base_url();?>assets/build/assets/app-d0aacae3.css">
</head>
<body class="login_admin">
	<div class="container" style="max-width: 100%; padding: 0; min-height: 100%;">
			<div class="wrap-login100 p-6">
				<div class="row">
					<div class="col-md-7">
						<img src="<?php echo base_url();?>assets/images/signup-process.png" class="desktop-logo desktop-logo-dark" alt="soliclogo">
					</div>
					<div class="col-md-5">
					<div class="col col-login mx-auto">
						<div class="main-logo text-center">
							<img src="<?php echo base_url();?>assets/images/favicon.png" class="desktop-logo desktop-logo-dark" alt="Daakit" style="background-color: #554DC0;width: 50px;margin-bottom: 10px;border-radius: 5px;">
						</div>
					</div>
					<div class="authLayoutCard card" style="border-radius: 15px;">
						<div class="p-4 card-body">
							<div class="d-flex justify-content-center align-items-center">
								<h3 class="text-center">
									<span class="text-Orange">Registration</span>
								</h3>
							</div>
							<?php if (!empty($error_message)) { ?>
								<div class="alert alert-danger text-center"><?= $error_message; ?></div>
							<?php } ?>
							<?php if (!empty($success)) { ?>
								<!-- <div class="alert alert-success text-center"><?= $success; ?></div> -->
							<?php } ?>
							<form action="<?= current_url(); ?>" method="post">
								<div class="row">
									<div class="col-md-6 col-12">
										<div class="mb-3">
											<label class="form-label">First Name <span style="color:red">*</span></label>
											<input placeholder="" autocomplete="new-password" name="firstName" type="text" required value="<?= set_value('firstName');?>" class="new-form form-control">
											<small class="text-danger"><?= form_error('firstName');?></small>
										</div>
									</div>
									<div class="col-md-6 col-12">
										<div class="mb-3">
											<label class="form-label">Last Name</label>
											<input placeholder="" autocomplete="new-password" name="lastName"  value="<?= set_value('lastName');?>" type="text" class="new-form form-control">
											<small class="text-danger"><?= form_error('lastName');?></small>
										</div>
									</div>
									<div class="col-md-6 col-12">
										<div class="mb-3">
											<label class="form-label">Company Name <span style="color:red">*</span></label>
											<input placeholder="" autocomplete="new-password" name="companyName" required value="<?= set_value('companyName');?>" type="text" class="new-form form-control">
											<small class="text-danger"><?= form_error('companyName');?></small>
										</div>
									</div>
									<div class="col-md-6 col-12">
										<div class="mb-3">
											<label class="form-label">Phone Number <span style="color:red">*</span></label>
											<input placeholder="" autocomplete="new-password" name="phone" required value="<?= set_value('phone');?>" type="text" class="new-form form-control">
											<small class="text-danger"><?= form_error('phone');?></small>
										</div>
									</div>
									<div class="col-md-12 col-12">
										<div class="mb-3">
											<label class="form-label">Your monthly shipments? <span style="color:red">*</span></label>
											<select name="potential" class="new-form form-select" required>
												<option value="">Select shipment potential</option>
												<option value="Setting up a new business" <?php if(set_value('potential')=='Setting up a new business') echo 'selected';?>>Setting up a new business</option>
												<option value="Between 1-10 orders" <?php if(set_value('potential')=='Between 1-10 orders') echo 'selected';?>>Between 1-10 orders</option>
												<option value="11-100" <?php if(set_value('potential')=='11-100') echo 'selected';?>>11-100</option>
												<option value="101-1000" <?php if(set_value('potential')=='101-1000') echo 'selected';?>>101-1000</option>
												<option value="1001-5000" <?php if(set_value('potential')=='1001-5000') echo 'selected';?>>1001-5000</option>
												<option value="More than 5000 orders" <?php if(set_value('potential')=='More than 5000 orders') echo 'selected';?>>More than 5000 orders</option>
											</select>
											<input  name="is_data_validate" type="hidden" class="new-form form-control">
											<small class="text-danger"><?= form_error('potential');?></small>
										</div>
									</div>
									</div>
									<div class="col-md-12 col-12">
										<div class="mb-3">
											<label class="form-label">Email <span style="color:red">*</span></label>
											<input placeholder="" autocomplete="new-password" required name="email" value="<?= set_value('email');?>" type="text" class="new-form form-control">
											<small class="text-danger"><?= form_error('email');?></small>
										</div>
									</div>
									<div class="col-md-12 col-12">
										<div class="mb-3">
											<label class="form-label">Password <span style="color:red">*</span></label>
											<input placeholder="" autocomplete="new-password" required name="password" value="<?= set_value('password');?>" type="password" class="new-form form-control">
											<small class="text-danger"><?= form_error('password');?></small>
										</div>
									</div>
									<div class="col-12">
										<div class="mb-3 form-check">
											<input name="is_agree" type="checkbox" id="rememberMe" <?php if(set_value('is_agree')) echo 'checked';?>   required class="form-check-input">
											<label for="rememberMe" class="mb-0 text-700 form-check-label">By submitting Sign up form, you agree to daakit's	 user 
												<a href="https://daakit.com/terms_and_conditions.html" target="_blank">Terms Of Service</a> and 
												<a href="https://daakit.com/privacy_policy.html" target="_blank">Privacy Policy.</a>
											</label>
										</div>
									</div>
									<div class="col-12 d-flex justify-content-center">
										<div class="mb-2">
											<button type="submit" class="login100-form-btn btn-primary">Register</button>
										</div>
									</div>
								</div>
							</form>
							<div class="d-flex justify-content-center">
								<label class="mb-0 fs--1">
									<span class="fw-semi-bold">Already a User? </span>
									<a class="text-Orange"  href="<?php echo base_url('users/login');?>">Login</a>
								</label>
							</div>
						</div>
					</div>
				</div>
		</div>	
	</div>
<!-- JQUERY SCRIPTS -->
<script src="<?php echo base_url();?>assets/internal/plugins/vendors/jquery.min.js"></script>
<!-- BOOTSTRAP SCRIPTS -->
<script src="<?php echo base_url();?>assets/internal/plugins/bootstrap/js/popper.min.js"></script>
<!-- APP JS-->
<link rel="modulepreload" href="<?php echo base_url();?>assets/build/assets/app-bf868514.js" />
<link rel="modulepreload" href="<?php echo base_url();?>assets/build/assets/custom-switcher-9e4b603c.js" />
<link rel="modulepreload" href="<?php echo base_url();?>assets/build/assets/indexcharts-bd630866.js" />
<link rel="modulepreload" href="<?php echo base_url();?>assets/build/assets/apexcharts.common-e529367b.js" />
<script type="module" src="<?php echo base_url();?>assets/build/assets/app-bf868514.js"></script>        
<!-- END SCRIPTS -->
</body>
</html>
