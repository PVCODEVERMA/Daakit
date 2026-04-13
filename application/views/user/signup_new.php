<div class="">
<!-- CONTAINER OPEN -->
<div class="col col-login mx-auto">
    <div class="main-logo text-center">
        <img src="<?php echo base_url();?>assets/images/daakit-log.jpg" class="header-brand-img" alt="">
        <img src="<?php echo base_url();?>assets/images/daakit-log.jpg" class="header-brand-img theme-logos" alt="">
    </div>
</div>
<div class="container-login100">
    <div class="wrap-login100 p-6">
        <form action="<?= current_url(); ?>" method="post">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
            <?php if (!empty($error)) { ?>
                <div class="alert alert-danger text-center"><?= $error; ?></div>
            <?php } ?>
            <?php if (!empty($success)) { ?>
                <div class="alert alert-success text-center"><?= $success; ?></div>
            <?php } ?>
            <span class="login100-form-title">
                Member Login
            </span>
            <div class="wrap-input100 validate-input" data-validate = "Valid email is required: ex@abc.xyz">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="input100" autofocus="1" required>
                    <span class="focus-input100"></span>
                    <span class="symbol-input100">
                        <i class="fa fa-envelope" aria-hidden="true" style="margin-bottom: 5px;"></i>
                    </span>
                </div>
            </div>
            <div class="wrap-input100 validate-input" data-validate = "Password is required">
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="input100" required>
                    <span class="focus-input100"></span>
                    <span class="symbol-input100">
                        <i class="fa fa-lock" aria-hidden="true" style="margin-bottom: 5px;"></i>
                    </span>
                </div>
            </div>
            <div class="text-end pt-1">
                <p class="mb-0"><a href="<?php echo base_url('users/forgot');?>" class="text-primary ms-1">Forgot Password?</a></p>
            </div>
            <div class="container-login100-form-btn">
            <button type="submit" class="login100-form-btn btn-primary">Login</button>
            </div>
            <div class="text-center pt-3">
                <p class="text-dark mb-0">Not a member? <a href="{{url('register')}}" class="text-primary ms-1">Sign UP now</a></p>
            </div>
            <div class=" flex-c-m text-center mt-3">
                <p>Or</p>
                <div class="btn-list">
                    <button type="button" class="btn btn-icon btn-primary-light"><i class="fa fa-facebook"></i></button>
                    <button type="button" class="btn btn-icon btn-secondary-light"><i class="fa fa-twitter"></i></button>
                    <button type="button" class="btn btn-icon btn-orange-light"><i class="fa fa-google"></i></button>
                    <button type="button" class="btn btn-icon btn-danger-light"><i class="fa fa-youtube"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- CONTAINER CLOSED -->
</div> 
<script>
    localStorage.setItem('token', '');
    localStorage.setItem('token', 'logout');
</script>