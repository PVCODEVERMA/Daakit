<div class="row">
    <div class="col-md-8 mobile">
        <div class="img_a">
            <img src="<?= $this->config->item('assets_base') ?>assets/login/images/Loginimage3.webp" alt="delta login">
        </div>
    </div>
    <?php
        $number = $this->session->userdata('mobile_no');
        $masked =  str_pad(substr($number, -4), strlen($number), '*', STR_PAD_LEFT);
    ?>    
    <div class="col-md-4 abc">
        <img src="<?= $this->config->item('assets_base') ?>assets/login/images/deltagloaballogo.png" alt="deltagloabal" style="width:50%;"><br>
        <div id="login">
            <div class="welcome"> OTP Verification<br></div>
            <div class="login">Enter OTP received on your moblie no. <?= $masked;?><br></div>
            <?php if (!empty($error)) { ?>
                <div class="formerror"><?= $error; ?></div><br>
            <?php } ?>
            <?php if (!empty($success)) { ?>
                <div class="formsuccess"><?= $success; ?></div><br>
            <?php } ?>
            <form action="<?= current_url(); ?>" method="post" id="submit_form">
                <input type="hidden" name="resend" id="resend" value="" />
                <div class="container" style="margin-top:-14px;">
                    <div class="row" style="margin-top:50px;">
                        <!-- <div class="col-sm-12 v_button label">
                            <label for="text"><strong>OTP sent to</label> <?= $masked;?></strong>
                        </div> -->
                        <div class="col-sm-12 v_button label">
                            <label for="text">Enter OTP</label> <span style="color:red">*</span>
                            <input type="text"  onkeypress="if (isNaN( String.fromCharCode(event.keyCode))) return false;" maxlength="4" autocomplete="off" name="entered_otp" id="entered_otp" required>
                        </div>
                        <div class="col-12 col-md-12 col-sm-12 col-lg-12 col-xl-12 formbb" style="text-align: end;"><a href="javascript:void(0)" onclick="resend_otp()"  id="re-btn"></a></div>

                        <div class="col-12 col-md-12 col-sm-12 col-lg-12 col-xl-12 formbb">
                            <button type="button btn" style="border-radius:7px;margin-top: 20px;">Submit</button>
                        </div>
                    </div>
            </form>
        </div>
    </div>
</div>
</div>
<script type='text/javascript'>
$(document).ready(function(){
        setTimeout(function() {
        document.getElementById('re-btn').innerHTML ='Resend';
    }, 10000)
});
function resend_otp()
{
    document.getElementById('re-btn').innerHTML ='';
    document.getElementById('resend').value='re-send';
    $('#entered_otp').removeAttr('required');
    document.getElementById('submit_form').submit();  
}
</script>
