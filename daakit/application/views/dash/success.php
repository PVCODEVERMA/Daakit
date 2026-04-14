<!DOCTYPE html>
<html>
<head>
<!-- Meta data -->
<meta charset="UTF-8">
<meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
<meta content="Daakit" name="description">
<meta content="Daakit Technologies Private Limited" name="author">
<meta name="keywords" content="template dashboard, dashboard, admin dashboard, admin panel, admindashboard, admin dashboard template, laravel admin, laravel template, laravel admin panel, bootstrap dashboard, admin panel template, bootstrap admin, bootstrap 5 dashboard, admin dashboard bootstrap" >
<!-- TITLE -->
<title>Daakit</title>
<!-- Favicon -->
<link rel="shortcut icon" id="favicon" href="<?php echo base_url('assets/images/dakit-favicon.gif');?>">
<link rel="apple-touch-icon”" id="favicon-apple-touch-icon" href="<?php echo base_url();?>assets/build/assets/iconfonts/icons.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" id="reset-css" href="<?php echo base_url();?>assets/build/assets/plugins/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" id="roboto-css" href="<?php echo base_url();?>assets/build/assets/app-d0aacae3.css">
<style>
.loader-img {
animation: rotate 2s linear infinite; /* Continuous rotation */
transform-origin: center; /* Rotate around the center */
z-index: 1; /* Bring image above the text */
}
</style>
</head>
<body class="jumbo-page" style="overflow: hidden;">
    <section class="gradient-01">
        <div class="container " style="margin-top: 2%;">
            <div class="row m-h-100 ">
                <div class="col-md-10 col-lg-8 m-auto">
                    <div class="rounded">
                        <div class="padding-box-2 p-all-25 ">
                            <div class="">
                                <p class="text-muted text-center" style="margin-top: 60px;">
                                    <img src="<?php echo base_url('assets/images/dakit-favicon.gif');?>" height="70" class="loader-img" alt="Loader"> 
                                </p><br><br><br><br>
                                <form method="post">
                                    <div class="row">
                                        <div class="col-sm-12  text-center bg-white rounded">
                                        <br><br><br><br><br><br>
                                                <h2>Welcome to Daakit!</h2>
                                            <h4 style="margin-bottom: 50px;">We are setting up your account . Please hold on for a moment.</h4>
                                        </div>
                                    </div>
                                    <div class="row m-t-40">
                                        <div class="col-sm-12 text-center">
                                            <h6> </h6>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
<script>
    setTimeout(function() {
        window.location.href = '<?php echo base_url('dash');?>';
    }, 3000);
</script>
</html>