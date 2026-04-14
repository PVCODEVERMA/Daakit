<div class="row">
    <div class="col-12 col-md-8 col-sm-8 col-lg-8 col-xl-8 mobile">
        <div class="img_a">
            <img src="<?php echo base_url(); ?>assets/login/images/LeftImage.png">
        </div>
    </div>
    <div class="col-12 col-md-4 col-sm-4 col-lg-4 col-xl-4 abc">
        <img src="<?php echo base_url(); ?>assets/login/images/deltagloaballogo.png" alt="Italian Trulli" style="width:50%;"><br>
        <div class="login">Reset Your Password<br></div>
        <?php if (!empty($error)) { ?>
            <div class="formerror"><?= $error; ?></div><br>
        <?php } ?>
        <?php if (!empty($success)) { ?>
            <div class="formsuccess"><?= $success; ?></div><br>
        <?php } ?>
        <form action="<?= current_url(); ?>" method="post" class="needs-validation">
            <input type="hidden" name="r" value="<?= $this->input->get('r'); ?>" >
            <div class="container" style="margin-top:-14px;">
                <div class="row" style="margin-top:50px;">
                    <div class="col-sm-12 v_button label btn">
                        <div class="col-sm-12  v_button label">
                            <label for="email">New Password</label>
                            <input type="password" name="password" style="border-radius:0px;" required="" class="form-control" placeholder="Enter New Password"/>
                        </div>
                        <div class="col-sm-12  v_button label">
                            <label for="email">Confirm Password</label>
                            <input type="password" name="passconf" style="border-radius:0px;" required="" class="form-control" placeholder="Enter Again"/>
                        </div>
                        <div class="col-12 col-md-12 col-sm-12 col-lg-12 col-xl-12 formbb">
                            <button type="submit" style="border-radius:7px;margin-top: 20px;">Reset Password</button>
                        </div>
                        <div class="account">Dont have an account?<a href="users/signup"> Sign Up Now</a></div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>