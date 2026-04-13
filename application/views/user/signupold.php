<style>
    .formerror {
        color: #cc0033 !important;
        margin-left: 17px;
    }

    .formerror p {
        color: #cc0033 !important;
        font-weight: 600;
        font-family: sans-serif;
    }
</style>
<section class="one" style="padding-bottom: 25px;">
    <div class="container" style="margin-top: -58px;">
        <div class="row abc">
            <div class="col-md-6 left_section">
                <img src="<?= $this->config->item('assets_base') ?>assets/signup/images/deltagloaballogo.png" alt="Italian Trulli" class="deltagloabal_logo">
                <div class="firstsection">Empowering Businesses<br>With Quick & Hassle-Free<br>Logistic Solutions</div>
                <div class="secondsection">
                    <img class="firstimg" src="<?= $this->config->item('assets_base') ?>assets/signup/images/bannerIcon1.png"/>Reduce RTO by 25%*<br>
                    <img class="secondimg" src="<?= $this->config->item('assets_base') ?>assets/signup/images/bannerIcon2.png"/>Cloud-Based Support Solution<br>
                    <img class="thirdimg" src="<?= $this->config->item('assets_base') ?>assets/signup/images/bannerIcon3.png"/>Easy Channel Integration<br>
                    <img class="fourthimg" src="<?= $this->config->item('assets_base') ?>assets/signup/images/bannerIcon4.png"/>AI-Fraud Detection and Order Filtering<br>
                    <img class="fifthimg" src="<?= $this->config->item('assets_base') ?>assets/signup/images/bannerIcon5.png"/>COD Remittance at Zero* Cost
                </div>
            </div>
            <div class="col-md-6 signupform right_section">
                <img src="<?= $this->config->item('assets_base') ?>assets/signup/images/deltagloaballogo.png" alt="Italian Trulli" class="deltagloabal_mobile_logo">
                <div class="firstsection">Sign Up For Simplified Shipping</div>
                <!--<div class="signup">Sign Up</div>
                <div class="signuptwo">Redefine Your Shipping Experience</div><br>-->
                <?php if (!empty($error)) { ?>
                <div class="formerror"><?= $error; ?></div><br>
                <?php } ?>
                <?php if (!empty($success)) { ?>
                <div class="formsuccess"><?= $success; ?></div><br>
                <?php } ?>
                <form action="<?= current_url(); ?>" method="post">
                    <div class="row">
                        <div class="col-sm-6 v_button" >
                        <label for="text">First Name</label>
                        <input type="text"  name="fname" value="<?= set_value('fname'); ?>" required>
                    </div>
                    <div class="col-sm-6 v_button " >
                        <label for="email">Last Name</label>
                        <input type="text"  name="lname" value="<?= set_value('lname'); ?>" required>
                    </div>
                    <div class="col-sm-6  v_button" >
                        <label for="email">Email</label>
                        <input type="text"  name="email" value="<?= set_value('email'); ?>" required>
                    </div>
                    <div class="col-sm-6  v_button" >
                        <label for="psw">Password</label>
                        <input type="password"  name="password" value="<?= set_value('password'); ?>" required>
                    </div> 
                    <div class="col-sm-12  v_button" >
                        <label for="text">Contact No</label>
                        <input type="text"  name="phone" value="<?= set_value('phone'); ?>" required>
                    </div>
                    <div class="col-sm-12  v_button" >
                        <label for="text">Company Name</label>
                        <input type="text"  name="companyname" value="<?= set_value('companyname'); ?>" required>
                    </div>
                    <div class="col-md-12 tc">
                        <input type="checkbox" name="aggreetc" value="1">
                        <span class="checkmark" id="checktc">By submitting this form, you agree to delta Post's User <a href="https://deltagloabal.com/terms-conditions" target ="_blank;">Privacy Statement.</a></span>
                    </div>
                    <div class="col-md-12 formbb" >
                        <button type="submit"style="border-radius:7px;">Register Now</button>
                    </div>
                </form>
            </div>
            <div class="account">Have an account?<a href="<?php echo base_url();?>"> Login Now</a></div>
        </div>
    </div>
</section>
<section class="two" style="padding-bottom: 25px;">
    <div class="container">
        <div class="row abc">
            <div class="col-12 col-md-12 col-sm-12 col-lg-12 col-xl-12  signupform">
        <div class="container">
        <div class="row row-cols-5 " style="text-align:center">
        <div class="col">
        <img src="<?= $this->config->item('assets_base') ?>assets/signup/images/StatIcon1 (1).png" ><br>
        <div class="icon"> 10000+<br></div>
        <div class="iconone"> Happy Clients</div>
        </div>
        <div class="col">
        <img src="<?= $this->config->item('assets_base') ?>assets/signup/images/StatIcon2.png" ><br>
        <div class="icon"> 25+<br></div>
        <div class="iconone"> Countries</div>
        </div>
        <div class="col">
        <img src="<?= $this->config->item('assets_base') ?>assets/signup/images/StatIcon3.png" ><br>
        <div class="icon"> 27000+<br></div>
        <div class="iconone"> Pincodes Covered</div>
        </div>
        <div class="col">
        <img src="<?= $this->config->item('assets_base') ?>assets/signup/images/StatIcon4.png" ><br>
        <div class="icon">    2M+<br></div>
        <div class="iconone">Transaction Everyday</div>
        </div>
        <div class="col">
        <img src="<?= $this->config->item('assets_base') ?>assets/signup/images/StatIcon5.png" ><br>
        <div class="icon"> 200+<br></div>
        <div class="iconone">Experts In<br>
        Team </div>
</div>
</div>
</div>
</div>
</div>
<!-- //container -->
<div class="row">
<div class="col-12" style="text-align:center;font-size:36px;margin-top: 25px;font-family:poppins;">Why delta Post</div>
</div>
<div class="row" style="text-align:center;margin-top:25px;">
<div class="col-12 col-md-4 col-sm-4 col-lg-4 col-xl-4">
<img src="<?= $this->config->item('assets_base') ?>assets/signup/images/WCU1.png" ><br>
<div class="whydelta">Grow your E-commerce business and<br> fetch maximum profits<br></div>
<div class="whydeltaone">• Quick COD remittance & support<br> • Multiple courier service options</div>
</div>
<div class="col-12 col-md-4 col-sm-4 col-lg-4 col-xl-4">
<img src="<?= $this->config->item('assets_base') ?>assets/signup/images/WCU2.png" ><br>
<div class="whydelta"> Shipping experience just got better<br></div>
<div class="whydeltaone"> • Industry’s smartest shipping automation<br>
• Enhanced post shipment experience</div>
</div>
<div class="col-12 col-md-4 col-sm-4 col-lg-4 col-xl-4">
<img src="<?= $this->config->item('assets_base') ?>assets/signup/images/WCU3.png" ><br>
<div class="whydelta">  Superior automated experience <br>& reduced efforts</div>
<div class="whydeltaone">• AI based order allocation engine<br>
• Plug & Play tech integration
</div>
</div>
</div>
</div>
</div>
</section>
<section class="three" style="padding-bottom: 25px;">
    <div class="container" style="text-align:center;">
        <div class="row">
            <div class="col-12" style="text-align:center;font-size:36px;margin-top: 25px;font-family:poppins;">Start Shippinig in 4 Steps</div>
        </div>
        <div class="row" style="margin-top:15px;">
            <div class="col-12 col-md-3 col-sm-3 col-lg-3 col-xl-3">
                <img src="<?php echo base_url(); ?>assets/signup/images/Journey1.png"><br>
                <div class="shipping"> Sign Up<br>
                    with delta Post</div>
            </div>
            <div class="col-12 col-md-3 col-sm-3 col-lg-3 col-xl-3">
                <img src="<?php echo base_url(); ?>assets/signup/images/Journey2.png"><br>
                <div class="shipping"> Complete<br>
                    Your KYC</div>
            </div>
            <div class="col-12 col-md-3 col-sm-3 col-lg-3 col-xl-3">
                <img src="<?php echo base_url(); ?>assets/signup/images/Journey3.png"><br>
                <div class="shipping"> Recharge<br> Your Account</div>
            </div>
            <div class="col-12 col-md-3 col-sm-3 col-lg-3 col-xl-3">
                <img src="<?php echo base_url(); ?>assets/signup/images/Journey4.png"><br>
                <div class="shipping"> Start Shipping<br>
                    With Us</div>
            </div>
        </div>
    </div>
</section>
<section class="four" style="padding-bottom: 25px;">
    <div class="container">
        <div class="row">
            <div class="col-12" style="text-align:center;font-size:36px;margin-top: 25px;font-family:poppins;">That is What Our Sellers Want to Say About Us</div>
</div>
<div class="row" style="text-align:center;margin-top: 25px;">
    <div class="col-md-3">
        <div class="seller_portfolio">
            <img src="<?= $this->config->item('assets_base') ?>assets/signup/images/1.png" ><br>
            <div class="testimonial">  Rashmi Chauhanke</div><br>
            <div class="testimonialone">
                Business Owner<br>Kaira Creations, New Delhi<br>
            </div>
            <div class="testimonialtwo">With delta Post superior<br>Automated Tech & AI-driven<br>offerings, we now ensure<br>enhanced customer service.
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="seller_portfolio">
            <img src="<?= $this->config->item('assets_base') ?>assets/signup/images/2.png" ><br>
            <div class="testimonial"> Darshan Dave</div><br>
            <div class="testimonialone">COO- The Preeti International<br></div>
            <div class="testimonialtwo"> They provide multiple shipping options that give great service and our orders reach on time and making us and our clients happy. Thank you delta Post.</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="seller_portfolio">
            <img src="<?= $this->config->item('assets_base') ?>assets/signup/images/3.png" ><br>
            <div class="testimonial">  Achin Simhal</div><br>
            <div class="testimonialone">Business Owner<br>Communication & U, Mumbai<br></div>
            <div class="testimonialtwo">delta Post offers the best service in the market in terms of ease of use, cost-effectiveness, & fast implementation. Their Automation system and solid Customer support backed up my business growth.</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="seller_portfolio">
            <img src="<?= $this->config->item('assets_base') ?>assets/signup/images/4.png" ><br>
            <div class="testimonial"> Binish Desai</div><br>
            <div class="testimonialone">Business Owner<br>SS Leathers– Gurugram<br></div>
            <div class="testimonialtwo">delta Post processes are easy and hassle-free to understand. Their customer care team is professional. The logistic solution provided by them helped in increasing my customer base as well. A true savior for SME’S business must say!
        </div>
    </div>
</div>
</div>
</section>
<section style="margin:10px;">
    <div>
        <center>Copyright © deltagloabal. All Rights Reserved</center>
    </div>
</section>