<style>
		 img {max-width: 100%;}
		.img{width:100% !important;}
		.fixedButton{
                position: fixed;
                bottom: 0px;
                right: 0px; 
                padding: 20px;
                    z-index: 9;
            }
        .object {
        animation: MoveUpDown 4s linear infinite;
        
        
        bottom: 0;
        }  

        @keyframes MoveUpDown {
        0%, 100% {
            bottom: 0;
        }
        80% {
            bottom: 100px;
        }
        }
        .roundedFixedBtn{
        height: 60px;
        line-height: 80px;  
        width: 60px;  
        font-size: 2em;
        font-weight: bold;
        border-radius: 50%;
        background-image: url("<?= $this->config->item('assets_base') ?>assets/signup/images/delta.png");
        color: white;
        text-align: center;
        cursor: pointer;
        }

		.radio-toolbar{margin-left: -5px; margin-right: -5px;}
		.radio-toolbar .col-sm-4{padding-left: 5px; padding-right: 5px;}
		.radio-toolbar .moa, .radio-toolbar .mob, .radio-toolbar .moc{text-align: center;  margin-bottom: 10px; width: 100%;}
		.radio-toolbar input[type="radio"]{opacity:0;position:fixed;width:0;}
		.radio-toolbar label{display:inline-block;background-color:#f8f8f8;padding:5px;font-family:sans-serif, Arial;font-size:1rem;}
		.radio-toolbar input[type="radio"]:checked+label{background-color:#cc0033;color:#ffffff;}
		.firstsectionn{font-size:2rem;font-weight:600;color:#000;line-height:48px;margin-top:38px;margin-top:0px; line-height: 1;margin-bottom: 1rem;}
		.formerror{color:#cc0033 !important;margin-left:17px;}
		.formerror p{color:#cc0033 !important;font-weight:600;font-family:sans-serif;}
		.channel-logo , .brand-logo{margin-top: 25px; width: auto; max-width: 100%;}
		.brand-logo{margin-bottom: 35px; max-height: 50px;object-fit: contain;  object-position: center;}
		.seller_portfolio img{width: 100%;}
		.sellers-slider .owl-dots{margin: 3rem 2rem 0;text-align: center;}
		.sellers-slider .owl-dots .owl-dot{display: inline-block; width: auto;}
		.sellers-slider .owl-dots .owl-dot + .owl-dot{margin-left: 15px!important;}
		.sellers-slider .owl-dots .owl-dot span{display:inline-block; height: 0.8rem; width: 0.8rem; border-radius: 50%; background: #cc0033; opacity: 0.1;margin-left:-44px;}
		.sellers-slider .owl-dots .owl-dot.active span{opacity: 1;}
		.one { background-image: url("<?= $this->config->item('assets_base') ?>assets/signup/images/BannerBg.png");}

		.testimonialone{ min-height: 42px;}
		.testimonialtwo {min-height: 150px;}
		.fact-logo{ width:auto; object-fit: contain; object-position: center;height:40px;}
        .secondsection .icon-image{-ms-flex: 0 0 35px;  flex: 0 0 35px; max-width: 35px;}
		
		
		@media (min-width: 320px) and (max-width: 480px){
		  
			.seller_portfolio {height: auto;}
			.icom{width:122px;}
		}
		@media (min-width: 1200px){
			#brand-logo-listing .col-xl-2{-ms-flex: 0 0 20%; flex: 0 0 20%; max-width: 20%;}
			
		}
		@media only screen and (max-width:1200px){
			.signupform {height: auto!important;}
			
		}
		@media (min-width:1281px){
		    .ecart{height: 35px;}
		    .space{padding-bottom:70px;padding-top:70px;}
			input[type=text], input[type=password] { margin: -6px 0 5px;}
			.one{background-image:url("<?= $this->config->item('assets_base') ?>assets/signup/images/BannerBg.png");}
			.tr{margin-top:-25px;}
			.cs{display:none;}
			.tsb{margin-top:-31px;}
			.sticky{width:100%;position:fixed;right:100px;left:auto;z-index:1;}
			.secondsection{margin-top:8px !important;}
			.mobile{display:none;}
		}
		@media only screen and (max-width:767px){
			.one .container{margin-top: 0!important;}
			.deltagloabal_mobile_logo { margin: auto;}
			.testimonialone{ min-height: 42px;}
			.testimonialtwo {min-height: 150px;}

			
			.secondsection {text-align: left!important;}
			
			
            .signupform {
                max-width: calc(100% - 30px);
                margin: auto;
                margin-top: -32px;
            }
            .tsa {  margin-top: -14px!important;}
        }
		@media only screen and (max-width:600px){
			.te{margin-top:1px !important;}
			.mnb{font-size:1.5rem;font-weight: 700;}
			.deltagloabal_mobile_logo{margin-left:14px !important;}
			.df{margin-top:30px;}
			.moc{width:80%;text-align:center;}
			.desk{display:none;}
			.ps{display:none;}
			.sad{font-size:1.5rem;margin-top:20px;}
			.tra{font-size:1.25rem;}
			.signupform .tra{font-size:1rem!important;}
            
		}
		.tqr{margin-top:-56px;}
		.bng{display:none;}
		.cvb{width:100%;text-align:center;}
		.six{display:none;}
		.tsa{margin-top:-20px;}
        .formerror {
        color: #cc0033 !important;
        margin-left: 17px;

	        margin-bottom: 10px;
       }

    .formerror p {
        color: #cc0033 !important;
        font-weight: 100;
        font-family: sans-serif;
    }
	

	</style>
	<section class="one" id="about" style="padding-bottom: 25px;">
		<div class="container" style="margin-top: -58px;">
			<div class="text-center"><img src="<?= $this->config->item('assets_base') ?>assets/signup/images/deltagloaballogo.png" alt="deltagloabal"
						class="d-md-none"></div>

			<div class="row abc">
				<div class="col-md-7 col-xl-7 left_section" id="xcz">
					<img src="<?= $this->config->item('assets_base') ?>assets/signup/images/deltagloaballogo.png" alt="deltagloabal"
						class="deltagloabal_logo">
					<div class="firstsection" style="margin-left:0px;"><h1>Empowering Businesses<br>With Quick &
						Hassle-Free<br>Logistic Solutions</h1></div>
					<div class="secondsection" style="margin-top:8px">
						<div> 
						   <div class="d-flex align-items-center mb-3" style="margin-top:20px;">
						     <div class="icon-image"><img class="firstimg" src="<?= $this->config->item('assets_base') ?>assets/signup/image/bannerIcon1.webp" /></div>
						     <span style="line-height:normal;">Reduce RTO by 25%*</span>
						   </div>
						   <div class="d-flex align-items-center mb-3">
						     <div class="icon-image"><img class="secondimg" src="<?= $this->config->item('assets_base') ?>assets/signup/image/bannerIcon2.webp" /></div>
						     <span style="line-height:normal;">Cloud-Based Support Solution</span>
						   </div>
						   <div class="d-flex align-items-center mb-3">
						     <div class="icon-image"><img class="thirdimg" src="<?= $this->config->item('assets_base') ?>assets/signup/image/bannerIcon3.webp" /></div>
						     <span style="line-height:normal;">Easy Channel Integration</span>
						   </div>
						   <div class="d-flex align-items-center mb-3">
						     <div class="icon-image"><img class="fourthimg" src="<?= $this->config->item('assets_base') ?>assets/signup/image/bannerIcon4.webp" /></div>
						     <span style="line-height:normal;">AI-Fraud Detection and Order Filtering</span>
						   </div>
						   <div class="d-flex align-items-center mb-3">
						     <div class="icon-image"><img class="fifthimg" src="<?= $this->config->item('assets_base') ?>assets/signup/image/bannerIcon5.webp" /></div>
						     <span style="line-height:normal;">27+ Courier Partners For The Best Delivery Output</span>
						   </div>
							
						</div>

						<div class="row align-items-center">
							<div class="col-xl-10">
								<div class="row align-items-center text-center">

									<div class="col-6 col-md-3 mt-5 mt-md-3">
										<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/a.webp" alt="bluedart" style="width: 90%;"><br>

									</div>
									<div class="col-6 col-md-3 mt-5 mt-md-3 ">
										<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/e.webp" alt="ekart" class="ecart"><br>

									</div>
									<div class="col-6 col-md-3 mt-5 mt-md-3">
										<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/b.webp" alt="delhivery"><br>

									</div>
									<div class="col-6 col-md-3 mt-5 mt-md-3">
										<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/express.webp" alt="xpreesbees" style="height:24px;max-width:700%"><br>

									</div>
									<div class="col-6 col-md-3 mt-5 mt-md-3">
										<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/f.webp" alt="rapid logistics"><br>

									</div>
									<div class="col-6 col-md-3 mt-5 mt-md-3">
										<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/c.webp" alt="shadowfax"><br>

									</div>
									<div class="col-6 col-md-3 mt-5 mt-md-3">
										<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/d.webp" alt="dtdc"><br>

									</div>
									<div class="col-6 col-md-3 mt-5 mt-md-3">
										<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/ecomexpress (1).webp" alt="ecom express" class="ecart"><br>

									</div>

								</div>
							</div>
						</div>

					</div>
				</div>
				<div class="col-md-7 col-xl-7 left_section mobile d-none" style="margin-top:210px;">
					<img src="<?= $this->config->item('assets_base') ?>assets/signup/images/deltagloaballogo.png" alt="Italian Trulli"
						class="deltagloabal_logo">
					<div class="firstsection" style="margin-left:0px;">Empowering Businesses<br>With Quick &
						Hassle-Free<br>Logistic Solutions</div>
					<div class="secondsection" style="margin-top:8px">
						<p> <img class="firstimg" src="<?= $this->config->item('assets_base') ?>assets/signup/image/bannerIcon1.webp" />Reduce
							RTO by
							25%*<br></p>
						<p><img class="secondimg"
								src="<?= $this->config->item('assets_base') ?>assets/signup/image/bannerIcon2.webp" />Cloud-Based
							Support Solution<br></p>
						<p> <img class="thirdimg" src="<?= $this->config->item('assets_base') ?>assets/signup/image/bannerIcon3.webp" />Easy
							Channel
							Integration<br></p>
						<p><img class="fourthimg" src="<?= $this->config->item('assets_base') ?>assets/signup/image/bannerIcon4.webp" />AI-Fraud
							Detection and Order Filtering<br></p>
						<p><img class="fifthimg" style="margin-left:45px;"
								src="<?= $this->config->item('assets_base') ?>assets/signup/image/bannerIcon5.webp" />17+ Courier
							Partners For The Best Delivery Output<br></p>

						<div class="row abc" style="margin-top:27px;width:100%;">

							<div class="col-6">
								<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/a.webp" style="width: 90%;"><br>

							</div>
							<div class="col-6" style="margin-top:-18px;">
								<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/e.webp" style="width: 65%;"><br>

							</div>
							<div class="col-6" style="margin-top:25px;">
								<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/b.webp" style="width: 65%;"><br>

							</div>
							<div class="col-6" style="margin-top:25px;">
								<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/express.webp" style="    height: 24px;
    max-width: 700%;"><br>

							</div>
							<div class="col-6" style="
        margin-top: 10px;
    ">
								<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/f.webp" style="width: 65%;margin-top:20px;"><br>

							</div>
							<div class="col-6" style="
        margin-top: 10px;
    ">
								<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/c.webp" style="margin-top:20px;width: 91%;"><br>

							</div>
							<div class="col-6" style="
        margin-top: 10px;
    ">
								<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/d.webp" style="margin-top:23px;width: 65%;"><br>

							</div>
							<div class="col-6" style="
        margin-top: 10px;
    ">
								<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/ecomexpress (1).webp" style="margin-top:11px;width: 65%;"><br>

							</div>

						</div>




					</div>
				</div>
								   
				<div class="col-md-5 col-xl-5 signupform right_section" id="xcz" style="height:610px;">

					


					<form action="<?= current_url(); ?>" method="post">
					    <img src="<?= $this->config->item('assets_base') ?>assets/signup/images/deltagloaballogo.png" alt="Italian Trulli" class="deltagloabal_mobile_logo  d-none">
    					<div class="firstsectionn mnb">Sign Up </div>

                        <?php if (!empty($error)) { ?>
                            <div class="formerror"><?= $error; ?></div>
                        <?php } ?>
                        <?php if (!empty($error_message)) { ?>
                            <div class="formerror"><?= $error_message; ?></div>
                        <?php } ?>
                        <?php if (!empty($success)) { ?>
                            <div class="formsuccess"><?= $success; ?></div>
                        <?php } ?>
    					<p class="tq tra" style="font-size:1.25rem;font-weight: 600;">Select your monthly shipping volume<span style="color:red;">*</span> </p>

						<div class="radio-toolbar te row justify-content-center" style="margin-bottom:0.75rem;">
							<div class="col-4 col-sm-4">
                            <input type="radio" id="radioApple" name="potential" value="1-100"  <?php echo set_radio('potential', '1-100'); ?>>
								<label class="moa" for="radioApple">1-100</label>
							</div>
							<div class="col-4 col-sm-4">
							<input type="radio" id="radioBanana" name="potential" value="101-1000" <?php echo set_radio('potential', '101-1000'); ?>>
								<label class="mob" for="radioBanana">101-1000</label>
							</div>
							<div class="col-4 col-sm-4">
							<input type="radio" id="radioOrange" name="potential" value="Above 1000" <?php echo set_radio('potential', 'Above 1000'); ?>>
								<label class="moc" for="radioOrange">Above 1000</label>
							</div>
						</div>

						<div class="row">

							<div class="col-6 col-sm-6 v_button ">
							  <div class="form-group"> 
								<label for="text" class="label-na" style="height: 21px;  font-size: 1rem;">First Name<span style="color:red;">*</span></label>
                                <input type="text" name="firstName"  value="<?= set_value('firstName'); ?>" required>
							  </div>
							</div>
							<div class="col-6 col-sm-6 v_button ">
							  <div class="form-group"> 
								<label for="email" style="height: 21px; font-size: 1rem;">Last Name<span style="color:red;">*</span></label>
								<input type="text" name="lastName" value="<?= set_value('lastName'); ?>" required>
							  </div>
							</div>
							<div class="col-6 col-sm-6  v_button">
							  <div class="form-group"> 
								<label for="email" style="height: 21px; font-size: 1rem;">Email<span style="color:red;">*</span></label>
								<input type="text" name="email" value="<?= set_value('email'); ?>" required>
							  </div>
							</div>
							<div class="col-6 col-sm-6  v_button">
							  <div class="form-group"> 
								<label for="psw" style="height: 21px; font-size: 1rem;">Password<span style="color:red;">*</span></label>
								<input type="password" name="password" value="<?= set_value('password'); ?>" required>
							  </div>
							</div>
							<div class="col-6 col-6 col-sm-12  v_button">
							  <div class="form-group"> 
								<label for="text" style="height: 21px;  font-size: 1rem;">Contact No<span style="color:red;">*</span></label>
								<input type="text" name="phone" value="<?= set_value('phone'); ?>" required>
							  </div>
							</div>
							<div class="col-6 col-sm-12  v_button">
							  <div class="form-group mb-md-0"> 
								<label for="text" style="height: 24px;    font-size: 1rem;">Company Name<span style="color:red;">*</span></label>
								<input type="text" name="companyName" value="<?= set_value('companyName'); ?>" required>
							  </div>
							</div>
							<div class="col-md-12 d-flex align-items-start tc " style="margin-top:10px;">
								<input type="checkbox" name="is_agree" value="1" required checked>
								<span class="checkmark col" id="checktc" style="margin-top: -5px; padding-left: 10px;">By submitting this form, you agree to deltagloabal's user <a href="https://deltagloabal.com/terms-conditions" target="_blank;" style="text-decoration:none;" class="d-block d-sm-inline">Privacy Statement.</a></span>
							</div>
							<div class="col-md-12 formbb">
								<button type="submit" style="border-radius:7px;font-size:1rem;" class="d-block d-xl-inline mb-2 mb-md-0">START FREE</button>
							</div>
						</div>
					</form>

					<div class="account tsa tsb">Have an account?<a href="<?php echo base_url();?>"> Login Now</a>
					</div>
	
			
				</div>
			</div>
	</section>

	<section class="two space" >
		<div class="container">
			<div class="row abc">
				<div class="col-12 col-md-12 col-sm-12 col-lg-12 col-xl-12  signupform tqr">
					<div class="container" style="margin-top: -25px;">
						<div class="row justify-content-center align-items-start row-cols-5 mt-4 " style="text-align:center">
							<div class="col-6 col-sm-6 col-md-4 col-lg">
								<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/StatIcon1 (1).webp" class="fact-logo"><br>
								<div class="icon"> 60000+<br></div>
								<div class="iconone"> Happy Clients </div>
							</div>
							<div class="col-6 col-sm-6 col-md-4 col-lg">
								<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/StatIcon2.webp" class="fact-logo"><br>
								<div class="icon"> 25+<br></div>
								<div class="iconone"> Countries</div>
							</div>
							<div class="col-6 col-sm-6 col-md-4 col-lg df">
								<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/StatIcon3.webp" class="fact-logo"><br>
								<div class="icon" > 29000+<br></div>
								<div class="iconone"> Pin Codes Covered</div>
							</div>
							<div class="col-6 col-sm-6 col-md-4 col-lg df">
								<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/StatIcon4.webp" class="fact-logo"><br>
								<div class="icon" > 2M+<br></div>
								<div class="iconone">Transactions Every Day</div>
							</div>
							<div class="col-6 col-sm-6 col-md-4 col-lg">
								<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/StatIcon5.webp" class="fact-logo"><br>
								<div class="icon" > 200+<br></div>
								<div class="iconone">Experts In
									Team </div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- //container -->
			<div class="row space"style="margin-top:25px;">
				<div class="col-12" style="text-align:center;font-size:2.25rem;font-family:poppins;">Why deltagloabal?</div>
			</div>
			<div class="row justify-content-center" style="text-align:center;">
				<div class="col-12 col-md-6 col-sm-6 col-lg-4 col-xl-4">
					<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/WCU1.webp"  alt="E-commerce business" style="height: 110px;object-fit: contain;object-position: center;width: auto;"><br>
					<div class="whydelta">Grow your E-commerce business and<br> fetch maximum profits<br></div>
					<div class="whydeltaone">• Quick COD remittance & support<br> • Multiple courier service options
					</div>
				</div>
				<div class="col-12 col-md-6 col-sm-6 col-lg-4 col-xl-4">
					<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/WCU2.webp" alt="Shipping experience" style="height: 110px;object-fit: contain;object-position: center;width: auto;"><br>
					<div class="whydelta"> Shipping experience just <br>got better<br></div>
					<div class="whydeltaone"> • Industry’s smartest shipping automation<br>
						• Enhanced post-shipment experience</div>
				</div>
				<div class="col-12 col-md-6 col-sm-6 col-lg-4 col-xl-4">
					<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/WCU3.webp" alt="Superior automated experience" style="height: 110px;object-fit: contain;object-position: center;width: auto;"><br>
					<div class="whydelta"> Superior automated experience <br>& reduced efforts</div>
					<div class="whydeltaone">• AI-based order allocation engine<br>
						• Plug & Play tech integration
					</div>
				</div>
			</div>
		</div>
	</section>
	<section class="three space" >
		<div class="container" style="text-align:center;">
			<div class="row" style="margin-top:25px;">
				<div class="col-12" style="text-align:center;font-size:2.25rem;font-family:poppins;">Start
					Shipping in 4 Steps</div>
			</div>
			<div class="row" style="margin-top:45px;">
				<div class="col-6 col-md-6 col-sm-6 col-lg-3 col-xl-3">
					<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/Journey1.webp" alt="SignUp" class="icom"><br>
					<div class="shipping"> Sign Up<br>
						with deltagloabal</div>
				</div>
				<div class="col-6 col-md-6 col-sm-6 col-lg-3 col-xl-3">
					<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/Journey2.webp" alt="kyc" class="icom"><br>
					<div class="shipping"> Complete<br>
						Your KYC</div>
				</div>
				<div class="col-6 col-md-6 col-sm-6 col-lg-3 col-xl-3">
					<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/Journey3.webp" alt="recharge" class="icom"><br>
					<div class="shipping"> Recharge<br> Your Account</div>
				</div>
				<div class="col-6 col-md-6 col-sm-6 col-lg-3 col-xl-3">
					<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/Journey4.webp" alt="Shipping" class="icom"><br>
					<div class="shipping"> Start Shipping<br>
						With Us</div>
				</div>
			</div>
		</div>
	</section>
	<section class="four space " >
		<div class="container" style="text-align:center;">
			<div class="row" >
				<div class="col-12" style="text-align:center;font-size:2.25rem;font-family:poppins;">
					Channel Integrations</div>
			</div>
			<div class="row justify-content-center align-items-center" id="channel-logo-listing">
				<div class="col-6 col-sm-4 col-md-3 col-xl-2">
					<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/magento.webp" alt="magento" class="channel-logo" ><br>

				</div>
				<div class="col-6 col-sm-4 col-md-3 col-xl-2">
					<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/woocommerce.webp" alt="woocommerce" class="channel-logo"><br>

				</div>
				<div class="col-6 col-sm-4 col-md-3 col-xl-2">
					<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/shopify.webp" alt="shopify" class="channel-logo"><br>

				</div>
				<div class="col-6 col-sm-4 col-md-3 col-xl-2">
					<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/Opencart.webp" alt="Opencart" class="channel-logo"><br>

				</div>
				<div class="col-6 col-sm-4 col-md-3 col-xl-2">
					<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/instamojo.webp" alt="instamojo" class="channel-logo"><br>

				</div>
				<div class="col-6 col-sm-4 col-md-3 col-xl-2">
					<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/unicommerce (1).webp" alt="unicommerce" class="channel-logo"><br>

				</div>
				
			</div>
		</div>
	</section>

	<section class="three space" style="padding-bottom: 25px;">
		<div class="container" style="text-align:center;">
			<div class="row"style="margin-top:25px;">
				<div class="col-12" style="text-align:center;font-size:2.25rem;font-family:poppins;">
					Brands That Trust Us</div>
			</div>

			<div class="row justify-content-center align-items-center" style="margin-top:15px;" id="brand-logo-listing">
				<div class="col-6 col-sm-4 col-md-3 col-xl-2">
					<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/aa.webp" alt="d-power" class="brand-logo"><br>

				</div>
				<div class="col-6 col-sm-4 col-md-3 col-xl-2">
					<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/bb.webp" alt="meena bazaar" class="brand-logo"><br>

				</div>
				<div class="col-6 col-sm-4 col-md-3 col-xl-2">
					<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/cc.webp"  alt="karagiri" class="brand-logo"><br>

				</div>
				<div class="col-6 col-sm-4 col-md-3 col-xl-2">
					<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/SleepyOwl.webp" alt="sleepyowl" class="brand-logo"><br>

				</div>
				<div class="col-6 col-sm-4 col-md-3 col-xl-2">
					<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/ee.webp" alt="suta" class="brand-logo"><br>

				</div>
				
				<div class="col-6 col-sm-4 col-md-3 col-xl-2">
					<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/IndianOil_Hover.webp" alt="indian oil" class="brand-logo"><br>

				</div>
				<div class="col-6 col-sm-4 col-md-3 col-xl-2">
					<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/HappyJar_hover.webp" alt="happy jars" class="brand-logo"><br>

				</div>
				<div class="col-6 col-sm-4 col-md-3 col-xl-2">
					<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/mamabug.webp" alt="mamabug" class="brand-logo"><br>

				</div>
				<div class="col-6 col-sm-4 col-md-3 col-xl-2">
					<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/babyrobe (1).webp" alt="babyrobe" class="brand-logo"><br>

				</div>
				<div class="col-6 col-sm-4 col-md-3 col-xl-2">
					<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/leviluna (1).webp" alt="levi and luna" class="brand-logo"><br>

				</div>

				<div class="col-6 col-sm-4 col-md-3 col-xl-2 ">
					<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/khadiglobal_hover.webp" alt="khadi" class="brand-logo"><br>

				</div>
				<div class="col-6 col-sm-4 col-md-3 col-xl-2">
					<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/fabricpandit_hover.webp" alt="fabric pandit"  class="brand-logo"><br>

				</div>
				<div class="col-6 col-sm-4 col-md-3 col-xl-2">
					<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/GIVA.webp" alt="giva" class="brand-logo"><br>

				</div>
				<div class="col-6 col-sm-4 col-md-3 col-xl-2">
					<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/shopclues.webp" alt="shopclues"  class="brand-logo"><br>

				</div>
				<div class="col-6 col-sm-4 col-md-3 col-xl-2">
					<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/stayclassy_hover.webp" alt="stay classy" class="brand-logo"><br>

				</div>

				<div class="col-6 col-sm-4 col-md-3 col-xl-2">
					<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/millioncase.webp" alt="million cases" class="brand-logo"><br>

				</div>
				<div class="col-6 col-sm-4 col-md-3 col-xl-2">
					<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/dailyobject.webp" alt="daily objects" class="brand-logo"><br>

				</div>
				<div class="col-6 col-sm-4 col-md-3 col-xl-2">
					<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/chaika_hover.webp" alt="chaika" class="brand-logo"><br>

				</div>
				<div class="col-6 col-sm-4 col-md-3 col-xl-2">
					<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/boodmoHover.webp"  alt="boodmo" class="brand-logo"><br>

				</div>
				<div class="col-6 col-sm-4 col-md-3 col-xl-2">
					<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/gururandhawa.webp" alt="Guru Randhawa Edition" class="brand-logo"><br>

				</div>

			</div>
		</div>
	</section>

	<section class="four space" >
		<div class="container">
			<div class="row"style="margin-top:25px;">
				<div class="col-12" style="text-align:center;font-size:2.25rem;font-family:poppins;">This
					is What Our Sellers Want to Say About Us</div>
			</div>
			<div class="d-none d-xl-block">
				<div class="row " style="text-align:center;margin-top: 25px;">
					<div class="col-md-3">
						<div class="seller_portfolio">
							<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/1.webp" alt="Rashmi Chauhanke"><br>
							<div class="testimonial"> Rashmi Chauhanke</div><br>
							<div class="testimonialone">
								Business Owner<br>Kaira Creations, New Delhi<br>
							</div>
							<div class="testimonialtwo">With deltagloabal superior<br>Automated Tech &
								AI-driven<br>offerings, we now ensure<br>enhanced customer service.
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="seller_portfolio">
							<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/2.webp" alt="Darshan Dave"><br>
							<div class="testimonial"> Darshan Dave</div><br>
							<div class="testimonialone">COO- The Preeti International<br></div>
							<div class="testimonialtwo"> They provide multiple shipping options that give great service and
								our orders reach on time and making us and our clients happy. Thank you deltagloabal.</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="seller_portfolio">
							<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/3.webp" alt="Achin Simhal"><br>
							<div class="testimonial"> Achin Simhal</div><br>
							<div class="testimonialone">Business Owner<br>Communication & U, Mumbai<br></div>
							<div class="testimonialtwo">deltagloabal offers the best service in the market in terms of ease
								of use, cost-effectiveness, & fast implementation. Their Automation system and solid
								Customer support backed up my business growth.</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="seller_portfolio">
							<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/4.webp" alt="Binish Desai"><br>
							<div class="testimonial"> Binish Desai</div><br>
							<div class="testimonialone">Business Owner<br>SS Leathers– Gurugram<br></div>
							<div class="testimonialtwo">deltagloabal processes are easy and hassle-free to understand. Their
								customer care team is professional. The logistic solution provided by them helped in
								increasing my customer base as well. A true savior for SME’S business must say!
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="d-xl-none">
				<div class="sellers-slider owl-carousel owl-theme" style="text-align:center;margin-top: 25px;">
					<div class="item">
						<div class="seller_portfolio">
							<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/1.webp"><br>
							<div class="testimonial"> Rashmi Chauhanke</div><br>
							<div class="testimonialone">
								Business Owner<br>Kaira Creations, New Delhi<br>
							</div>
							<div class="testimonialtwo">With deltagloabal superior<br>Automated Tech &
								AI-driven<br>offerings, we now ensure<br>enhanced customer service.
							</div>
						</div>
					</div>
					<div class="item">
						<div class="seller_portfolio">
							<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/2.webp"><br>
							<div class="testimonial"> Darshan Dave</div><br>
							<div class="testimonialone">COO- The Preeti International<br></div>
							<div class="testimonialtwo" style="text-align:justify;"> They provide multiple shipping options that give great service and
								our orders reach on time and making us and our clients happy. Thank you deltagloabal.</div>
						</div>
					</div>
					<div class="item">
						<div class="seller_portfolio">
							<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/3.webp"><br>
							<div class="testimonial"> Achin Simhal</div><br>
							<div class="testimonialone">Business Owner<br>Communication & U, Mumbai<br></div>
							<div class="testimonialtwo">deltagloabal offers the best service in the market in terms of ease
								of use, cost-effectiveness, & fast implementation. Their Automation system and solid
								Customer support backed up my business growth.</div>
						</div>
					</div>
					<div class="item">
						<div class="seller_portfolio">
							<img src="<?= $this->config->item('assets_base') ?>assets/signup/image/4.webp"><br>
							<div class="testimonial"> Binish Desai</div><br>
							<div class="testimonialone">Business Owner<br>SS Leathers– Gurugram<br></div>
							<div class="testimonialtwo">deltagloabal processes are easy and hassle-free to understand. Their
								customer care team is professional. The logistic solution provided by them helped in
								increasing my customer base as well. A true savior for SME’S business must say!
							</div>
						</div>
					</div>
				</div>
			</div>
			
				<a class="fixedButton object" onclick="topFunction()" id="myBtn" style="display:none;" ><span><b style="font-size: 1.375rem;
    
    /* border: 1px solid black; */
   
    color: #cc0033;
    padding: 4px;">Free Sign Up</b>  </span>
                  <div class="roundedFixedBtn" ><img src="<?= $this->config->item('assets_base') ?>assets/signup/images/delta.png" style="margin-left: 40px;"></div></a>
                 
		</div>
	</section>
    <section style="margin:10px;">
		<div>
			<center>Copyright © deltagloabal. All Rights Reserved</center>
		</div>
	</section>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?= $this->config->item('assets_base') ?>assets/signup/owl.carousel/owl.carousel.js"></script>
	<script  type="text/javascript">
	 $(document).ready(function () {
				$(".sellers-slider").owlCarousel({
						autoplay: true,
						nav: false,
						dots: true,
						loop: true,
						margin:30,
						responsive: {
								0: {
										items: 1
								},
								768: {
										items: 2
								},
								900: {
										items: 3
								}
						}
				});
			});
			//Get the button
var mybutton = document.getElementById("myBtn");



// When the user scrolls down 20px from the top of the document, show the button
window.onscroll = function() {scrollFunction()};

function scrollFunction() {
  if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
    mybutton.style.display = "block";
  } else {
    mybutton.style.display = "none";
  }
}

// When the user clicks on the button, scroll to the top of the document
function topFunction() {

  document.body.scrollTop = 0;
  document.documentElement.scrollTop = 0;
}
</script> 