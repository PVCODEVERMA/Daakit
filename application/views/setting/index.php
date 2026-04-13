	<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">Profile Setting</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item">						
		</li>
    </ol>
</div>
<!-- END PAGE-HEADER -->
<div class="main-container container-fluid">

<!-- START ROW-1 -->
<form method="post" action="<?= current_url(); ?>">
    <div class="row">
        <div class="col-lg-6 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Profile</h3>
                </div>
                <div class="card-body">
                    <div class="col-md-12">
						<div class="form-group col-md-12">
						<label>First Name</label>
						<input type="text" name="fname" value="<?= set_value('fname', !empty($setting->fname) ? $setting->fname : '') ?>" class="form-control" placeholder="First Name">
						</div>
						<div class="form-group col-md-12">
						<label>Last Name</label>
						<input type="text" name="lname" value="<?= set_value('lname', !empty($setting->lname) ? $setting->lname : '') ?>" class="form-control" placeholder="Last Name">
						</div>
						<div class="form-group col-md-12">
						<label>Email</label>
						<input type="email" disabled name="email" class="form-control" id="inputEmail4" placeholder="Email" value="<?php echo $setting->email;?>">
						</div>
						<div class="form-group col-md-12">
						<label>Phone</label>
						<input type="text" name="phone" class="form-control" placeholder="Phone" value="<?= set_value('phone', !empty($setting->phone) ? $setting->phone : '') ?>">
						</div>
						<div class="form-group col-md-12">
						<label>New Password</label>
						<input type="password" class="form-control" name="currentpassword" placeholder="Enter Password" value="<?php echo set_value('currentpassword');?>">
						</div>
						<div class="form-group col-md-12">
						<label>Confirm Password</label>
						<input type="password" class="form-control" name="checkpassword" placeholder="Enter confirm Password" value="<?php echo set_value('checkpassword');?>">
						</div>
						<div class="form-group" style="text-align:right">
							<button class="btn btn-primary">Modify</button>
						</div>                
				</div>
            </div>
        </div>
    </div>
</form>
<!-- END ROW-1 -->
</div>
<script src="http://code.jquery.com/jquery-3.4.1.min.js"></script>
	<Script> 
	function companychangefunction()
	{
		var value = document.getElementById("companybvalue").value;
		if(value == 'soloproprietorship')
		{
			document.getElementById("soloproprietorship_div").style.display = "block";
			document.getElementById("partnership_div").style.display = "none";
		}
		else if(value == 'partnership')
		{
			document.getElementById("soloproprietorship_div").style.display = "none";
			document.getElementById("partnership_div").style.display = "block";
		}
		else
		{
			document.getElementById("individualrow").style.display = "none";
			document.getElementById("companyrow").style.display = "none";
		}
	}
	</Script>