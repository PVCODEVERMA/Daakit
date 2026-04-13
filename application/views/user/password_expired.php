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
    <style>
        #submit_form {
            margin-top: 40px;
        }

        .formerror {
            font-size: 12px;
            margin-bottom: 20px;
            padding-left: 12px;
            margin-top: -24px;
        }

        .formerror p {
            color: #cc0033 !important;
            margin-bottom: 6px;
            text-align: left;
        }
    </style>
    <div class="col-md-4 abc">
        <img src="<?= $this->config->item('assets_base') ?>assets/login/images/deltagloaballogo.png" alt="deltagloabal" style="width:50%;"><br>
        <div id="login">
            <div class="welcome"> Password expired<br></div>
            <div class="login">Your password has expired. <br /> Please create a new password for your account.<br></div>

            <form action="<?= current_url(); ?>" method="post" id="submit_form">
                <?php if (!empty($error)) { ?>
                    <div class="formerror"><?= $error; ?></div><br>
                <?php } ?>
                <?php if (!empty($success)) { ?>
                    <div class="formsuccess"><?= $success; ?></div><br>
                <?php } ?>
                <input type="hidden" name="resend" id="resend" value="" />
                <div class="container" style="margin-top:-14px;">
                    <div class="row">
                        <div class="col-sm-12 v_button label">
                            <label for="text">New Password</label> <span style="color:red">*</span>
                            <input type="password" name="password" value="<?= set_value('password'); ?>" id="password" required>
                        </div>
                        <div class="col-sm-12 v_button label">
                            <label for="text">Confirm Password</label> <span style="color:red">*</span>
                            <input type="password" name="passconf" id="passconf" value="<?= set_value('passconf'); ?>" required>
                        </div>
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
    $(document).ready(function() {
        setTimeout(function() {
            document.getElementById('re-btn').innerHTML = 'Resend';
        }, 10000)
    });

    function resend_otp() {
        document.getElementById('re-btn').innerHTML = '';
        document.getElementById('resend').value = 're-send';
        $('#entered_otp').removeAttr('required');
        document.getElementById('submit_form').submit();
    }
</script>