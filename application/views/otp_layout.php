
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
<body class="login_admin" style="
    margin-left: 0px;
    margin-right: 0px;
    padding-left: 0px;
    padding-right: 0px;
">
	<div class="" style="
    margin-left: 0px;
    margin-right: 0px;
    padding-left: 0px;
    padding-right: 0px;
">
        <?= $maincontent;?>
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
