<style>
    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    }

    html, body {
    height: 100%;
    width: 100%;
    }

    .container-inner {
    display: flex;
    height: 100vh;
    }

    .left {
    width: 70%;
    background-color: #554DC0;
    overflow: hidden;
    position: relative;
    }
    @media (min-width: 600px) {
  .left {
    display: none;
  }
  .right {
    width: 100%;
    background-color: white;
    }
}


    .slider-wrapper {
    width: 100%;
    height: 100%;
    overflow: hidden;
    position: relative;
    }

    .slider {
    display: flex;
    width: 100%; /* 3 images */
    height: 100%;
    transition: transform 0.7s ease-in-out;
    }

    .slide-img {
    width: 100%;
    height: 80%;
    margin: auto;
    object-fit: fit;
    flex-shrink: 0;
    }

    .right {
    width: 30%;
    background-color: white;
    }

</style>

<script>
  window.onload = function () {
    const slider = document.querySelector('.slider');
    const totalSlides = document.querySelectorAll('.slide-img').length;
    let currentIndex = 0;

    function showSlide(index) {
      const offset = -index * 100;
      slider.style.transform = `translateX(${offset}%)`;
    }

    function nextSlide() {
      currentIndex = (currentIndex + 1) % totalSlides;
      showSlide(currentIndex);
    }

    setInterval(nextSlide, 3000); // 3 seconds
  };
</script>



<div class="container-inner">
<!-- CONTAINER OPEN -->
    <div class="left">
        <div class="slider-wrapper">
        <div class="slider">
            <img src="<?php echo base_url();?>assets/images/daakitmap.png" class="slide-img" />
            <img src="<?php echo base_url();?>assets/images/fulfillmenthype_warehouse.png" class="slide-img" />
            <img src="<?php echo base_url();?>assets/images/warehouse5.png" class="slide-img" />
        </div>
        </div>
    </div>

    <div class="container-login100 right"> 
        <div class="wrap-login100 p-6" style="margin-top:-1%;">
            <div class="main-logo text-center">
                <img src="<?php echo base_url();?>assets/images/Go.png" style="background-color: #554DC0;height: 100px;border-radius: 3px; width:312px;" class="header-brand-img" alt="">
                <img src="<?php echo base_url();?>assets/images/daakit-log.jpg" style="background-color: #554DC0;height: 62px;border-radius: 3px;" class="header-brand-img theme-logos" alt="">
            </div>
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
                    <!-- <p class="mb-0"><a href="<?php echo base_url('users/forgot');?>" class="text-primary ms-1">Forgot Password?</a></p> -->
                </div>
                <div class="container-login100-form-btn">
                    <button type="submit" class="login100-form-btn btn-primary">Login</button>
                </div>
                <div class="text-center pt-3">
                    <p class="text-dark mb-0">Not a member? <a href="<?php echo base_url('users/register');?>" class="text-primary ms-1">Sign UP now</a></p>
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