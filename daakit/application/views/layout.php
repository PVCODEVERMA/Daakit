<?php
$controller = $this->uri->segment(1);
$mehtod = $this->uri->segment(2);
$mehtod1 = $this->uri->segment(3);
if ($controller != '')
    if (!empty($mehtod)) {
        if (!empty($mehtod1)) {
            $url = $controller . '/' . $mehtod . '/' . $mehtod1;
        } else {
            $url = $controller . '/' . $mehtod;
        }
    } else {

        $url = $controller;
    }

//pr($user_details,1);
?>

<!doctype html>
<html lang="en" dir="ltr">
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
        <link rel="stylesheet" type="text/css" id="bootstrap-css" href="<?php echo base_url();?>assets/build/assets/app-6d59ac94.css">
        <link rel="stylesheet" type="text/css" id="roboto-css" href="<?php echo base_url();?>assets/build/assets/app-d0aacae3.css">
		<style>
			@keyframes rotate {
				from {
					transform: rotate(0deg);
				}
				to {
					transform: rotate(360deg);
				}
			}
			.loader-img {
				animation: rotate 2s linear infinite; /* Continuous rotation */
				transform-origin: center; /* Rotate around the center */
				z-index: 1; /* Bring image above the text */
			}
			.processing-text {
				position: absolute;
				top: 50%;
				left: 50%;
				transform: translate(-50%, -50%); /* Center the text */
				color: black; /* Use a solid color for visibility */
				font-size: 16px; /* Adjust font size as needed */
				z-index: 0; /* Ensure text is behind the image */
				white-space: nowrap; /* Prevent text wrapping */
			}
			.user-icon-style:hover {
				border: 1px solid #fff; /* Border appears on hover */
			}
			/* For disabled input fields */
			input:disabled {
				background-color: #ededed !important; /* Light grey background */
			}
			/* For readonly input fields */
			input[readonly], textarea[readonly] {
				background-color: #ededed !important;
			}
			/* For disabled input fields */
			select:disabled {
				background-color: #ededed !important; /* Light grey background */
			}
		</style>
    </head>

    <body class="sidebar-mini2 app sidebar-mini sidenav-toggled">
    
        <!-- GLOBAL-LOADER -->

        <!-- END GLOBAL-LOADER -->

        <!-- START PAGE -->
        <div class="page">

            <div class="page-main">
                <div>
                    <!-- Main-Header -->
                    <div class="app-header sticky">
						<div class="main-container container-fluid d-flex ">
							<div class="d-flex header-left">
								<div class="responsive-logo">
									<a class="main-logo" href="<?php echo base_url();?>">
										<img src="<?php echo base_url();?>assets/images/Go.png" class="desktop-logo desktop-logo-dark" alt="soliclogo">
										<img src="<?php echo base_url();?>assets/images/Go.png" class="desktop-logo" alt="soliclogo">
									</a>
								</div>
								<div class="header-nav-link">
									<a href="javascript:void(0);" data-bs-toggle="sidebar" class="nav-link icon toggle app-sidebar__toggle">
										<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M4 11h12v2H4zm0-5h16v2H4zm0 12h7.235v-2H4z"></path></svg>
									</a>
								</div>
								<div class="header-nav-link d-none d-lg-flex">
									<a href="javascript:void(0);" class="d-flex nav-link icon" data-bs-toggle="dropdown">
										<span class="country-text ms-2 fs-14 fw-600">Quick Links<i class="mdi mdi-chevron-down ms-2"></i></span>
									</a>
									<ul class="dropdown-menu dropdown-menu-arrow">
										<li>
											<div class="dropdown-item d-flex align-items-center position-relative">
												<a href="<?php echo base_url('billing/rechage_wallet');?>" class="stretched-link"></a>
												<span>Recharge</span>
											</div>
										</li>
										<li>
											<div class="dropdown-item d-flex align-items-center position-relative">
												<a href="<?php echo base_url('warehouse');?>" class="stretched-link"></a>
												<span>Warehouse</span>
											</div>
										</li>
										<li>
											<div class="dropdown-item d-flex align-items-center position-relative">
												<a href="<?php echo base_url('orders/all');?>" class="stretched-link"></a>
												<span>Orders</span>
											</div>
										</li>
										<li>
											<div class="dropdown-item d-flex align-items-center position-relative">
												<a href="<?php echo base_url('shipping/all');?>" class="stretched-link"></a>
												<span>Shipments</span>
											</div>
										</li>
										<li>
											<div class="dropdown-item d-flex align-items-center position-relative">
												<a href="<?php echo base_url('pickups');?>" class="stretched-link"></a>
												<span>Manifest</span>
											</div>
										</li>

									</ul>
								</div><!-- language -->
							</div>
							<div class="d-flex header-right ms-auto">
								<div class="header-nav-link">
									<a href="javascript:void(0);" class="nav-link icon d-lg-none" role="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent-4">
										<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 10c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0-6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 12c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"></path></svg>
									</a>
								</div>
								<div class="responsive-navbar align-items-stretch navbar-expand-lg navbar-dark p-0 mb-0">
									<div class="collapse align-items-stretch navbar-collapse" id="navbarSupportedContent-4">
										<ul class="list-unstyled nav">

											<li class="header-nav-link">
												<a href="javascript:void(0);" class="nav-link icon" data-bs-toggle="modal" data-bs-target="#searchModal">
													<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M10 18a7.952 7.952 0 0 0 4.897-1.688l4.396 4.396 1.414-1.414-4.396-4.396A7.952 7.952 0 0 0 18 10c0-4.411-3.589-8-8-8s-8 3.589-8 8 3.589 8 8 8zm0-14c3.309 0 6 2.691 6 6s-2.691 6-6 6-6-2.691-6-6 2.691-6 6-6z"></path></svg>
												</a>
											</li><!-- Search -->

											<li class="header-nav-link header-fullscreen">
												<a href="javascript:void(0);" class="nav-link icon" id="fullscreen-button">
													<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M10 4H8v4H4v2h6zM8 20h2v-6H4v2h4zm12-6h-6v6h2v-4h4zm0-6h-4V4h-2v6h6z"></path></svg>
												</a>
											</li><!-- Fullscreen -->

											<li class="header-nav-link">
												<a href="javascript:void(0);" class="nav-link icon layout-setting light-layout">
													<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M20.742 13.045a8.088 8.088 0 0 1-2.077.271c-2.135 0-4.14-.83-5.646-2.336a8.025 8.025 0 0 1-2.064-7.723A1 1 0 0 0 9.73 2.034a10.014 10.014 0 0 0-4.489 2.582c-3.898 3.898-3.898 10.243 0 14.143a9.937 9.937 0 0 0 7.072 2.93 9.93 9.93 0 0 0 7.07-2.929 10.007 10.007 0 0 0 2.583-4.491 1.001 1.001 0 0 0-1.224-1.224zm-2.772 4.301a7.947 7.947 0 0 1-5.656 2.343 7.953 7.953 0 0 1-5.658-2.344c-3.118-3.119-3.118-8.195 0-11.314a7.923 7.923 0 0 1 2.06-1.483 10.027 10.027 0 0 0 2.89 7.848 9.972 9.972 0 0 0 7.848 2.891 8.036 8.036 0 0 1-1.484 2.059z"></path></svg>
												</a>
												<a href="javascript:void(0);" class="nav-link icon layout-setting dark-layout">
													<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M6.993 12c0 2.761 2.246 5.007 5.007 5.007s5.007-2.246 5.007-5.007S14.761 6.993 12 6.993 6.993 9.239 6.993 12zM12 8.993c1.658 0 3.007 1.349 3.007 3.007S13.658 15.007 12 15.007 8.993 13.658 8.993 12 10.342 8.993 12 8.993zM10.998 19h2v3h-2zm0-17h2v3h-2zm-9 9h3v2h-3zm17 0h3v2h-3zM4.219 18.363l2.12-2.122 1.415 1.414-2.12 2.122zM16.24 6.344l2.122-2.122 1.414 1.414-2.122 2.122zM6.342 7.759 4.22 5.637l1.415-1.414 2.12 2.122zm13.434 10.605-1.414 1.414-2.122-2.122 1.414-1.414z"></path></svg>
												</a>
											</li><!-- theme-layout -->
											<?php if (in_array('billing', $user_details->permissions)) { ?>
												<li class="header-nav-link">
													<button class="btn btn-sm"  data-bs-toggle="tooltip" data-bs-html="true"  <?php if ($user_details->wallet_limit < 0) { ?> data-bs-original-title="Available Limit: <?= round($user_details->wallet_balance - $user_details->wallet_limit, 2); ?>" <?php } ?>><a href="<?php echo base_url('billing/rechage_wallet');?>"  style="color:#fff"><b>Available Balance &#8377; <?= (!empty($user_details->wallet_balance) ? round($user_details->wallet_balance, 2) : '0') ?></b></a></button>
												</li>
											<?php } ?>
											<li class="header-nav-link dropdown">
												<!-- <a href="javascript:void(0);" class="nav-link icon text-center" data-bs-toggle="dropdown">
													<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 13.586V10c0-3.217-2.185-5.927-5.145-6.742C13.562 2.52 12.846 2 12 2s-1.562.52-1.855 1.258C7.185 4.074 5 6.783 5 10v3.586l-1.707 1.707A.996.996 0 0 0 3 16v2a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1v-2a.996.996 0 0 0-.293-.707L19 13.586zM19 17H5v-.586l1.707-1.707A.996.996 0 0 0 7 14v-4c0-2.757 2.243-5 5-5s5 2.243 5 5v4c0 .266.105.52.293.707L19 16.414V17zm-7 5a2.98 2.98 0 0 0 2.818-2H9.182A2.98 2.98 0 0 0 12 22z"></path></svg>
													<span class="pulse bg-success"></span>
												</a> -->
												<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
													<!-- <div class="drop-heading">
														<div class="d-flex">
															<h5 class="mb-0 text-dark">Notifications</h5>
															<span class="badge bg-danger ms-auto br-5">5</span>
														</div>
													</div> -->
													<div class="dropdown-divider mt-0"></div>
													<div class="header-dropdown-scroll1">
														<a href="<?php echo base_url();?>assets/emailinbox" class="dropdown-item d-flex">
															<div class="notifyimg bg-success-transparent">
																<i class="fa fa-thumbs-o-up text-success"></i>
															</div>
															<div>
																<strong>Someone likes our posts.</strong>
																<div class="small text-muted">3 hours ago</div>
															</div>
														</a>
														<a href="<?php echo base_url();?>assets/emailinbox" class="dropdown-item d-flex">
															<div class="notifyimg bg-primary-transparent">
																<i class="fa fa-exclamation-triangle text-primary"></i>
															</div>
															<div>
																<strong> Issues Fixed</strong>
																<div class="small text-muted">30 mins ago</div>
															</div>
														</a>
														<a href="<?php echo base_url();?>assets/emailinbox" class="dropdown-item d-flex">
															<div class="notifyimg bg-warning-transparent">
																<i class="fa fa-commenting-o text-warning"></i>
															</div>
															<div>
																<strong> 3 New Comments</strong>
																<div class="small text-muted">5  hour ago</div>
															</div>
														</a>
														<a href="<?php echo base_url();?>assets/emailinbox" class="dropdown-item d-flex">
															<div class="notifyimg bg-danger-transparent">
																<i class="fa fa-cogs text-danger"></i>
															</div>
															<div>
																<strong> Server Rebooted.</strong>
																<div class="small text-muted">45 mintues ago</div>
															</div>
														</a>
														<a href="<?php echo base_url();?>assets/emailinbox" class="dropdown-item d-flex">
															<div class="notifyimg bg-info-transparent">
																<i class="fa fa-thumbs-o-up text-info"></i>
															</div>
															<div>
																<strong>Someone likes our posts.</strong>
																<div class="small text-muted">3 hours ago</div>
															</div>
														</a>
													</div>
													<div class="dropdown-divider mb-0"></div>
													<div class=" text-center p-2">
														<a href="<?php echo base_url();?>assets/emailinbox" class="text-dark pt-0">View All Notifications</a>
													</div>
												</div>
											</li><!-- Notification -->

											<li class="header-nav-link dropdown">
												<!-- <a href="javascript:void(0);" class="nav-link icon" data-bs-toggle="dropdown">
													<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M16 2H8C4.691 2 2 4.691 2 8v13a1 1 0 0 0 1 1h13c3.309 0 6-2.691 6-6V8c0-3.309-2.691-6-6-6zm4 14c0 2.206-1.794 4-4 4H4V8c0-2.206 1.794-4 4-4h8c2.206 0 4 1.794 4 4v8z"></path><path d="M7 9h10v2H7zm0 4h7v2H7z"></path></svg>
													<span class="badge badge-secondary pulse-secondary">4</span>
												</a> -->
												<div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
													<div class="drop-heading">
														<div class="d-flex">
															<h5 class="mb-0 text-dark">Messages</h5>
															<span class="badge bg-danger ms-auto br-5">4</span>
														</div>
													</div>
													<div class="dropdown-divider mt-0"></div>
													<div class="header-dropdown-scroll2">
														<a href="<?php echo base_url();?>assets/chat" class="dropdown-item d-flex mt-2 pb-3">
															<div class="avatar avatar-md rounded-circle me-3 d-block cover-image" data-image-src="<?php echo base_url();?>assets/build/assets/images/users/male/41.jpg">
																<span class="avatar-status bg-green"></span>
															</div>
															<div>
																<strong>Madeleine</strong>
																<p class="mb-0 fs-13 text-muted ">Hey! there I' am available</p>
																<div class="small text-muted">3 hours ago</div>
															</div>
														</a>
														<a href="<?php echo base_url();?>assets/chat" class="dropdown-item d-flex">
															<div class="avatar avatar-md rounded-circle me-3 d-block cover-image" data-image-src="<?php echo base_url();?>assets/build/assets/images/users/female/1.jpg">
																<span class="avatar-status bg-red"></span>
															</div>
															<div>
																<strong>Anthony</strong>
																<p class="mb-0 fs-13 text-muted ">New product Launching</p>
																<div class="small text-muted">5  hour ago</div>
															</div>
														</a>
														<a href="<?php echo base_url();?>assets/chat" class="dropdown-item d-flex">
															<div class="avatar avatar-md rounded-circle me-3 d-block cover-image" data-image-src="<?php echo base_url();?>assets/build/assets/images/users/female/18.jpg">
																<span class="avatar-status bg-yellow"></span>
															</div>
															<div>
																<strong>Olivia</strong>
																<p class="mb-0 fs-13 text-muted ">New Schedule Realease</p>
																<div class="small text-muted">45 mintues ago</div>
															</div>
														</a>
														<a href="<?php echo base_url();?>assets/chat" class="dropdown-item d-flex">
															<div class="avatar avatar-md rounded-circle me-3 d-block cover-image" data-image-src="<?php echo base_url();?>assets/build/assets/images/users/male/32.jpg">
																<span class="avatar-status bg-green"></span>
															</div>
															<div>
																<strong>Madeleine</strong>
																<p class="mb-0 fs-13 text-muted ">Hey! there I' am available</p>
																<div class="small text-muted">3 hours ago</div>
															</div>
														</a>
													</div>
													<div class="dropdown-divider mb-0"></div>
													<div class=" text-center p-2">
														<a href="<?php echo base_url();?>assets/chat" class="text-dark pt-0">View All Messages</a>
													</div>
												</div>
											</li><!-- Message-box -->

											<li class="header-nav-link dropdown">
												<a href="javascript:void(0);" class="nav-link icon" data-bs-toggle="dropdown">
													<button type="button"  aria-expanded="false" class="p-2 nav-link bg-transparent user-icon-style toggle btn btn-primary"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 448 512" class="fs-1" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg" style="color: rgb(43, 10, 97);"><path d="M224 256c70.7 0 128-57.3 128-128S294.7 0 224 0 96 57.3 96 128s57.3 128 128 128zm89.6 32h-16.7c-22.2 10.2-46.9 16-72.9 16s-50.6-5.8-72.9-16h-16.7C60.2 288 0 348.2 0 422.4V464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-41.6c0-74.2-60.2-134.4-134.4-134.4z"></path></svg></button>												</a>
												<ul class="dropdown-menu w-250 pt-0 dropdown-menu-arrow dropdown-menu-right">
													<li>
														<div class="dropdown-header mb-2 p-3 text-center">
														<i class="fa fa-user" style="font-size: xx-large;" aria-hidden="true"></i>
														<h6 class="mb-0" style="white-space: pre-wrap;"><?= $user_details->fname;?> (<?= $user_details->id;?>)</h6>
															<p class="mb-0 fs-13 opacity-75"><?= $user_details->email; ;?></p>
														</div>
													</li>
													<li>
														<a href="<?php echo base_url('setting');?>" class="dropdown-item d-flex align-items-center alert_notice">
															<i class="ri-user-line fs-18 me-2 text-primary"></i>
															<span>Profile</span>
														</a>
													</li>
													<li>
														<a href="<?php echo base_url();?>billing/rechage_wallet" class="dropdown-item d-flex align-items-center">
															<i class="ri-settings-5-line fs-18 me-2 text-primary"></i>
															<span>Wallet</span>
														</a>
													</li>
													<li>
														<a href="<?php echo base_url('profile');?>" class="dropdown-item d-flex align-items-center">
															<i class="ri-mail-line fs-18 me-2 text-primary"></i>
															<span>Settings</span>
														</a>
													</li>
													<li>
														<a href="<?php echo base_url('support');?>" class="dropdown-item d-flex align-items-center">
															<i class="ri-mail-line fs-18 me-2 text-primary"></i>
															<span>Support (24x7)</span>
														</a>
													</li>
													<li>
														<a href="<?php echo base_url('users/logout')?>" class="dropdown-item d-flex align-items-center">
															<i class="ri-logout-circle-r-line fs-18 me-2 text-primary"></i>
															<span>Sign out</span>
														</a>
													</li>
												</ul>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>
                    <!-- End Main-Header -->

                    <!--Main-Sidebar-->
                    <div class="sticky">
						<div class="app-sidebar__overlay" data-bs-toggle="sidebar"></div>
						<aside class="app-sidebar">
							<div class="app-sidebar__header" style="background-color:#554DC0; padding:0;">
								<a class="main-logo" href="<?php echo base_url();?>">
									<img src="<?php echo base_url();?>assets/images/Go.png" class="desktop-logo desktop-logo-dark" alt="soliclogo" style="padding-top:20px;">
									<img src="<?php echo base_url();?>assets/images/favicon.png" class="mobile-logo mobile-logo-dark" alt="soliclogo" style="padding:10px">
									<img src="<?php echo base_url();?>assets/images/Go.png" class="desktop-logo" alt="soliclogo" style="padding-top:20px;">
									<img src="<?php echo base_url();?>assets/images/favicon.png" class="mobile-logo" alt="soliclogo" style="padding:10px;">
								</a>
							</div>
							<div class="main-sidemenu">
								<div class="slide-left disabled" id="slide-left">
									<svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
										<path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path>
									</svg>
								</div>
								<ul class="side-menu">
									<li class="sub-category">
										<h3>Main</h3>
									</li>
									<li class="slide">
										<a class="side-menu__item"  href="<?php echo base_url('analytics');?>">
											<span class="side-menu__icon">
												<svg xmlns="http://www.w3.org/2000/svg" class="side_menu_img" viewBox="0 0 24 24"><path d="M3 13h1v7c0 1.103.897 2 2 2h12c1.103 0 2-.897 2-2v-7h1a1 1 0 0 0 .707-1.707l-9-9a.999.999 0 0 0-1.414 0l-9 9A1 1 0 0 0 3 13zm7 7v-5h4v5h-4zm2-15.586 6 6V15l.001 5H16v-5c0-1.103-.897-2-2-2h-4c-1.103 0-2 .897-2 2v5H6v-9.586l6-6z"></path></svg>
											</span>
											<span class="side-menu__label text-truncate">Dashboard</span>
										</a>
									</li>
									<li class="slide">
										<a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);">
											<span class="side-menu__icon">
											<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
												<path d="M6 6h15l-1.5 9H7.5L6 6z"/>
												<path d="M6 6l1.5 9m9-9l-1.5 9M6 18a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm12 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
											</svg>
											</span>
											<span class="side-menu__label text-truncate">Orders & Shipments</span>
											<i class="angle fa fa-angle-right"></i>
										</a>
										<ul class="slide-menu">
											<li class="panel sidetab-menu">
												<div class="panel-body tabs-menu-body p-0 border-0">
													<div class="tab-content">
														<div class="tab-pane active" id="side1">
															<ul class="sidemenu-list">
																<li><a class="slide-item" href="<?php echo base_url('orders/all');?>"><i class="fa fa-shopping-cart" aria-hidden="true"></i> Orders</a></li>
															</ul>
														</div>
													</div>
												</div>
											</li>

										</ul>
									</li>	
									<li class="slide">
										<a class="side-menu__item"  href="<?php echo base_url('ndr');?>">
											<span class="side-menu__icon">
											<i class="fa fa-ban" aria-hidden="true"></i>
											</span>
											<span class="side-menu__label text-truncate"> NDR</span>
										</a>
									</li>
									<li class="slide">
										<a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);">
											<span class="side-menu__icon">
											<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
												<path d="M3 17h18M3 7h18M3 12h18"/>
											</svg>
											</span>
											<span class="side-menu__label text-truncate">Finance</span>
											<i class="angle fa fa-angle-right"></i>
										</a>
										<ul class="slide-menu">
											<li class="panel sidetab-menu">
												<div class="panel-body tabs-menu-body p-0 border-0">
													<div class="tab-content">
														<div class="tab-pane active" id="side1">
															<ul class="sidemenu-list">
																<li><a class="slide-item" href="<?php echo base_url('billing/rechage_wallet');?>">&#8377; Wallet Recharge</a></li>
																<li><a class="slide-item" href="<?php echo base_url('billing');?>"><i class='fa fa-file-text'></i> Billing</a></li>
																<li><a class="slide-item" href="<?php echo base_url('weight');?>"><i class='fa fa-balance-scale'></i> Weight (Reconciliation)</a></li>
															</ul>
														</div>
													</div>
												</div>
											</li>

										</ul>
									</li>	
									<li class="slide">
										<a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);">
											<span class="side-menu__icon">
												<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="tools" class="svg-inline--fa fa-tools fa-w-16 " role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M501.1 395.7L384 278.6c-23.1-23.1-57.6-27.6-85.4-13.9L192 158.1V96L64 0 0 64l96 128h62.1l106.6 106.6c-13.6 27.8-9.2 62.3 13.9 85.4l117.1 117.1c14.6 14.6 38.2 14.6 52.7 0l52.7-52.7c14.5-14.6 14.5-38.2 0-52.7zM331.7 225c28.3 0 54.9 11 74.9 31l19.4 19.4c15.8-6.9 30.8-16.5 43.8-29.5 37.1-37.1 49.7-89.3 37.9-136.7-2.2-9-13.5-12.1-20.1-5.5l-74.4 74.4-67.9-11.3L334 98.9l74.4-74.4c6.6-6.6 3.4-17.9-5.7-20.2-47.4-11.7-99.6.9-136.6 37.9-28.5 28.5-41.9 66.1-41.2 103.6l82.1 82.1c8.1-1.9 16.5-2.9 24.7-2.9zm-103.9 82l-56.7-56.7L18.7 402.8c-25 25-25 65.5 0 90.5s65.5 25 90.5 0l123.6-123.6c-7.6-19.9-9.9-41.6-5-62.7zM64 472c-13.2 0-24-10.8-24-24 0-13.3 10.7-24 24-24s24 10.7 24 24c0 13.2-10.7 24-24 24z"></path></svg>
											</span>
											<span class="side-menu__label text-truncate">Services</span>
											<i class="angle fa fa-angle-right"></i>
										</a>
										<ul class="slide-menu">
											<li class="panel sidetab-menu">
												<div class="panel-body tabs-menu-body p-0 border-0">
													<div class="tab-content">
														<div class="tab-pane active" id="side1">
															<ul class="sidemenu-list">
																<li><a class="slide-item" href="<?php echo base_url('allocation');?>"><i class="fa fa-link" aria-hidden="true"></i> Rule Allocation</a></li>
																<li><a class="slide-item" href="<?php echo base_url('cx');?>"><i class="fa fa-link" aria-hidden="true"></i> CX</a></li>
															</ul>
														</div>
													</div>
												</div>
											</li>

										</ul>
									</li>	
									<li class="slide">
										<a class="side-menu__item" data-bs-toggle="slide" href="javascript:void(0);">
											<span class="side-menu__icon">
											<i class="fa fa-cogs" aria-hidden="true"></i>											
										</span>
											<span class="side-menu__label text-truncate">Settings</span>
											<i class="angle fa fa-angle-right"></i>
										</a>
										<ul class="slide-menu">
											<li class="panel sidetab-menu">
												<div class="panel-body tabs-menu-body p-0 border-0">
													<div class="tab-content">
														<div class="tab-pane active" id="side1">
															<ul class="sidemenu-list">
																<li><a class="slide-item" href="<?php echo base_url('channels');?>"><i class="fa fa-link" aria-hidden="true"></i> Channels</a></li>
															</ul>
														</div>
													</div>
												</div>
												<div class="panel-body tabs-menu-body p-0 border-0">
													<div class="tab-content">
														<div class="tab-pane active" id="side1">
															<ul class="sidemenu-list">
																<li><a class="slide-item" href="<?php echo base_url('warehouse');?>"><i class="fa fa-bank"></i> Warehouse</a></li>
															</ul>
														</div>
													</div>
												</div>
												<div class="panel-body tabs-menu-body p-0 border-0">
													<div class="tab-content">
														<div class="tab-pane active" id="side1">
															<ul class="sidemenu-list">
																<li><a class="slide-item" href="<?php echo base_url('profile');?>"><i class="fa fa-building-o"></i> Company Profile</a></li>
															</ul>
														</div>
													</div>
												</div>
												<div class="panel-body tabs-menu-body p-0 border-0">
													<div class="tab-content">
														<div class="tab-pane active" id="side1">
															<ul class="sidemenu-list">
																<li><a class="slide-item" href="<?php echo base_url('product/mapping');?>"><i class="fa fa-map" aria-hidden="true"></i>	HSN/GST Mapping</a></li>
															</ul>
														</div>
													</div>
												</div>
												<div class="panel-body tabs-menu-body p-0 border-0">
													<div class="tab-content">
														<div class="tab-pane active" id="side1">
															<ul class="sidemenu-list">
																<li><a class="slide-item" href="<?php echo base_url('setting/label');?>"><i class="fa fa-cog" aria-hidden="true"></i> Label Setting</a></li>
															</ul>
														</div>
													</div>
												</div>
											</li>
										</ul>
									</li>	
									<li class="slide">
										<a class="side-menu__item"  href="<?php echo base_url('support');?>">
											<span class="side-menu__icon">
												<i class="fa fa-phone" aria-hidden="true"></i>
											</span>
											<span class="side-menu__label text-truncate"> Support 24x7</span>
										</a>
									</li>
                                </ul>
								<div class="slide-right" id="slide-right">
									<svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
										<path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"></path>
									</svg>
								</div>
							</div>
						</aside>
					</div>
					<!-- End Main-Sidebar-->
                </div>
                <!-- START APP-CONTENT -->
                <div class="main-content app-content">
                    <?= $maincontent; ?>					
                    <!-- END MAIN-CONTAINER -->
                </div>
                <!-- END APP-CONTENT -->
            </div>
            <!-- start modals -->
			<form method="post"  id="AwbSerach">
				<div class="modal fade header-search-modal" id="searchModal" tabindex="-1" aria-hidden="true">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-header p-0">
									<div class="input-group">
										<input type="search" class="form-control border-0 py-3 ps-4" name="filter[awb_no]" placeholder="Search Awb Numbers...">
											<div class="btn-list">
												<div class="dropdown">
													<button type="button" style="margin-top: 4px;" class="btn btn-primary dropdown-toggle d-inline-flex align-items-center" data-bs-toggle="dropdown" fdprocessedid="gbm0z8" aria-expanded="false">
														<i class="fa fa-search me-2"></i><span>AWB</span>
													</button>
													<div class="dropdown-menu" style="">
														<a class="dropdown-item searchByAwb" href="#shipment_b2c">Shipment</a>
														<a class="dropdown-item searchByAwb" href="#ndr">NDR</a>
														<a class="dropdown-item searchByAwb" href="#wallet_history">Wallet History</a>
														<a class="dropdown-item searchByAwb" href="#shipping_charges">Shipping Cost</a>
													</div>
												</div>
											</div>
									</div>
							</div>
							<!-- <div class="modal-body">
								<h6 class="mb-2">Recent Searches</h6>
								<div class="list-group">
									<a href="javascript:window.location.reload(true)" class="list-group-item list-group-item-action text-truncate">
										<i class="ri-history-fill align-middle fs-15 me-2 opacity-75 d-inline-block"></i>
										<span>Nowa Admin Dashboard - Themeforest</span>
									</a>
									<a href="javascript:window.location.reload(true)" class="list-group-item list-group-item-action text-truncate">
										<i class="ri-history-fill align-middle fs-15 me-2 opacity-75 d-inline-block"></i>
										<span>Bootstrap 5 Latest - Bootstrap</span>
									</a>
									<a href="javascript:window.location.reload(true)" class="list-group-item list-group-item-action text-truncate">
										<i class="ri-history-fill align-middle fs-15 me-2 opacity-75 d-inline-block"></i>
										<span>Sash – Bootstrap Responsive Flat Admin Dashboard HTML5 Template</span>
									</a>
									<a href="javascript:window.location.reload(true)" class="list-group-item list-group-item-action text-truncate">
										<i class="ri-history-fill align-middle fs-15 me-2 opacity-75 d-inline-block"></i>
										<span>Sparci Admin Template - Themeforest</span>
									</a>
								</div>
							</div> -->
						</div>
					</div>
				</div>
			</form>
			<div class="modal fade add_remove_tags" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered modal-md" role="document">
					<div class="modal-content" id="add_remove_tags">
					</div>
				</div>
			</div>
			<div class="modal fade single_add_remove_tags" role="dialog" id="lgscrollmodal">
				<div class="modal-dialog modal-dialog-centered modal-md" role="document">
					<div class="modal-content" id="single_add_remove_tags">
					</div>
				</div>
			</div>
            <!-- End modals -->
			<div id="global-loader"> 
				<img src="<?php echo base_url('assets/images/dakit-favicon.gif');?>" height="80" class="loader-img" alt="Loader"> 
				<!-- <div class="processing-text">Processing...</div> -->
			</div>
            <!-- Footer opened -->
            <footer class="footer">
			<div class="container">
				<div class="row align-items-center flex-row-reverse">
					<div class="col-md-12 col-sm-12 text-center">
						Copyright © 2024 <a href="<?php echo base_url();?>">Daakit</a>. All rights reserved.
					</div>
				</div>
			</div>
		</footer>            <!-- End Footer -->

        </div>    
        <!-- END PAGE-->
        <!-- SCRIPTS -->
        <!-- BACK-TO-TOP -->
        <a href="#top" id="back-to-top"><i class="fa fa-level-up"></i></a>
        <!-- JQUERY SCRIPTS -->
        <script src="<?php echo base_url();?>assets/build/assets/plugins/vendors/jquery.min.js"></script>
        <!-- BOOTSTRAP SCRIPTS -->
        <script src="<?php echo base_url();?>assets/build/assets/plugins/bootstrap/js/popper.min.js"></script>
        <script src="<?php echo base_url();?>assets/build/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
        <!-- STICKY JS-->        
        <script src="<?php echo base_url();?>assets/build/assets/sticky.js"></script>
        <!-- SIDEMENU JS-->
        <script src="<?php echo base_url();?>assets/build/assets/plugins/sidemenu/sidemenu.js"></script>
        <!-- PERFECT SCROLL BAR JS-->
        <!-- <script src="<?php echo base_url();?>assets/build/assets/plugins/pscrollbar/perfect-scrollbar.js"></script> -->
        <!-- <script src="<?php echo base_url();?>assets/build/assets/plugins/pscrollbar/pscroll-sidemenu.js"></script> -->
		<!-- CHART-CIRCLE -->
		<script src="<?php echo base_url();?>assets/build/assets/plugins/vendors/circle-progress.min.js"></script>
		<!-- SELECT2 JS -->
		<script src="<?php echo base_url();?>assets/build/assets/plugins/select2/select2.full.min.js"></script>
		<!-- CHARTJS CHART -->
		<script src="<?php echo base_url();?>assets/build/assets/plugins/chart/Chart.bundle.js"></script>
		<script src="<?php echo base_url();?>assets/build/assets/plugins/chart/utils.js"></script>
		<!-- PIETY CHART -->
		<script src="<?php echo base_url();?>assets/build/assets/plugins/peitychart/jquery.peity.min.js"></script>
		<!-- <script src="<?php echo base_url();?>assets/build/assets/plugins/peitychart/peitychart.init.js"></script> -->

		<!-- APEX-CHARTS JS -->
		<script src="<?php echo base_url();?>assets/build/assets/plugins/apexcharts/apexcharts.min.js"></script>

		<!-- INDEX-JS  -->
		<script src="<?php echo base_url();?>assets/build/assets/plugins/sidebar/sidebar.js"></script>
		<script type="module" src="<?php echo base_url();?>assets/build/assets/app-bf868514.js"></script> 
        <script type="module" src="<?php echo base_url();?>assets/internal/plugins/datatable/dataTables.responsive.min.js"></script> 
        <script type="module" src="<?php echo base_url();?>assets/internal/plugins/datatable/responsive.bootstrap5.min.js"></script> 
		<script src="<?php echo base_url();?>assets/internal/plugins/datatable/js/jquery.dataTables.min.js"></script>
		<script src="<?php echo base_url();?>assets/internal/plugins/datatable/js/dataTables.bootstrap5.js"></script>
		<script src="<?php echo base_url();?>assets/internal/plugins/datatable/js/dataTables.buttons.min.js"></script>
		<script src="<?php echo base_url();?>assets/js/main.js"></script> 
		<!-- INTERNAL Bootstrap-Datepicker js-->
        <script src="<?php echo base_url();?>assets/build/assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
        <!-- BOOTSTRAP-DATERANGEPICKER JS -->
        <!-- <script src="<?php echo base_url();?>assets/build/assets/plugins/bootstrap-daterangepicker/moment.min.js"></script> -->
        <!-- INTERNAL Bootstrap-Datepicker js-->
        <!-- <script src="<?php echo base_url();?>assets/build/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.js"></script> -->
		<!-- NOTIFICATIONS JS -->
		<script src="<?php echo base_url();?>assets/build/assets/plugins/notify/js/jquery.growl.js"></script>
		<script>
			<?php if(!empty($error)) { ?>
				$(document).ready(function() {
					// Replace this with your PHP error message
					const errorMessage = `<?php echo $error; ?>`;
					if (errorMessage) {
						$.growl.error({
							message: errorMessage
						});
					}
				});
			<?php  unset($_SESSION['error']); unset($_SESSION['success']);} ?>
			<?php if(!empty($success)) { ?>
				$(document).ready(function() {
					// Replace this with your PHP error message
					const successMessage = `<?php echo $success; ?>`;
					if (successMessage) {
						$.growl.notice({
							message: successMessage
						});
					}
				});
			<?php unset($_SESSION['error']); unset($_SESSION['success']); } ?>
			var baseUrl = "<?php echo base_url(); ?>";
			$(document).ready(function() {
				dateRangeFunction = function() {
					$('.home_filter_form').submit();
				}
			});
		</script>
		<script>
			window.fwSettings={
			'widget_id':1070000002405
			};
			!function(){if("function"!=typeof window.FreshworksWidget){var n=function(){n.q.push(arguments)};n.q=[],window.FreshworksWidget=n}}() 
		</script>
		<script type='text/javascript' src='https://ind-widget.freshworks.com/widgets/1070000002405.js' async defer></script>
	</body> 

</html>
