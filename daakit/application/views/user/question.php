<body id="bannerform_scroll">
    <header class="head_custom">
        <div class="container">
            <div class="row">
                <div class="col-12 d-flex justify-content-between">
                    <a class="brand_logo" href=""><img src="<?php echo base_url();?>assets/signup/img/logo.png" alt=""></a>
                </div>
            </div>
        </div>
    </header>
    <main>
        <section class="question_head">
            <div class="container">
                <div class="row">
                    <div class="col-lg-9 col-md-10 col-sm-12 m-auto">
                        <div class="question_common_box fixed-position mb-3">
                            <div class="question_content_box">
                                <p class="display-block">Thank You For Choosing deltagloabal

                                      <span class="pb-3" style="font-size: 16px;display: block;">Let’s understand more about your business needs in 3 quick steps.</span>
                                </p>
                                <ul>
                                    <li class="active_first active"><span>01</span></li>
                                    <li class="active_seconds active"><span>02</span></li>
                                    <li class="active_thirds active"><span>03</span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="question_common_box">
                            <form  action="<?=current_url();?>" method="post">
                            
                          
                                <div class="question_box active">
                                    <p><strong>What’s your monthly shipping volume?</strong></p>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                          <input type="radio" class="form-check-input" name="shipping_volume" value="0 - 10">0 - 100
                                        </label>
                                      </div>
                                      <div class="form-check">
                                        <label class="form-check-label">
                                          <input type="radio" class="form-check-input" name="shipping_volume" value="100 - 1000">100 - 1000
                                        </label>
                                      </div>
                                      <div class="form-check">
                                        <label class="form-check-label">
                                          <input type="radio" class="form-check-input" name="shipping_volume" value="1000 or above">1000 or above
                                        </label>
                                      </div>
                                      <small class="text-danger"> <?php echo form_error('shipping_volume'); ?></small>
                                </div>
                                <div class="question_box active_second">
                                    <p><strong>Which industry does your business belong to?</strong></p>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                          <input type="radio" class="form-check-input" name="industry_type" value="FMCG">FMCG
                                        </label>
                                      </div>
                                      <div class="form-check">
                                        <label class="form-check-label">
                                          <input type="radio" class="form-check-input" name="industry_type" value="Appare">Apparel
                                        </label>
                                      </div>
                                      <div class="form-check">
                                        <label class="form-check-label">
                                          <input type="radio" class="form-check-input" name="industry_type" value="Appliances">Appliances
                                        </label>
                                      </div>
                                      <div class="form-check">
                                        <label class="form-check-label">
                                          <input type="radio" class="form-check-input" name="industry_type" value="Electronics">Electronics
                                        </label>
                                      </div>
                                      <div class="form-check">
                                        <label class="form-check-label">
                                          <input type="radio" class="form-check-input" name="industry_type" value="Others">Others
                                        </label>
                                      </div>
                                      <small class="text-danger"> <?php echo form_error('industry_type'); ?></small>

                                </div>
                                <div class="question_box text-left active_third">
                                    <p><strong>Reasons that best describe your need for a shipping partner.</strong></p>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                          <input type="checkbox" class="form-check-input multiple_checkbo" name="shipping_partner[]" value="Low shipping rates">Low shipping rates
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                          <input type="checkbox" class="form-check-input multiple_checkbo" name="shipping_partner[]" value="Wide pin code reach">Wide pin code reach
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                          <input type="checkbox" class="form-check-input multiple_checkbo" name="shipping_partner[]" value="Multiple courier partners" >Multiple courier partners
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                          <input type="checkbox" class="form-check-input multiple_checkbo" name="shipping_partner[]" value="Low RTO percentage">Low RTO percentage
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                          <input type="checkbox" class="form-check-input multiple_checkbo" name="shipping_partner[]" value="Others" >Others 
                                        </label>
                                    </div>

                                    <small class="text-danger"> <?php echo form_error('shipping_partner[]'); ?></small>

                                </div>
                                <button type="submit" class="btn btn-primary signup_custom_btn">Let’s sTART SHIPPING</button>
                                <!-- <a href="question_design_flow.html" class="btn btn-primary signup_custom_btn">Let’s sTART SHIPPING</a> -->
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <footer class="home-footer">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <p> Copyright © deltagloabal. All Rights Reserved </p>
                </div>
            </div>
        </div>
    </footer>
    
<script type="text/javascript">
    $(document).ready(function() {
        
        $(".form-check-input").click(function(){
          var shipping_volume=  $('input[name="shipping_volume"]:checked').val();
          var industry_type=  $('input[name="industry_type"]:checked').val();


        var shipping_partner = [];
        $.each($("input[name='shipping_partner[]']:checked"), function() {
            shipping_partner.push($(this).val());
        });
        
            if(shipping_volume)
            {

             $(".active_second").addClass("active");
             $(".active_first").addClass("step_done");
             
             

            }
            if(industry_type)
            {
              if(!shipping_volume)
              {
                return false;
              }

             $(".active_third").addClass("active");
             $(".active_seconds").addClass("step_done");

            }
            if(shipping_partner.length>0)
            {
              
              if(!industry_type)
              {
                return false;
              }
                $(".active_thirds").addClass("step_done");  
            }
            

});

    });
        </script>
         <script> 
      document.addEventListener('contextmenu', event=> event.preventDefault()); 
      document.onkeydown = function(e) { 
      if(event.keyCode == 123) { 
      return false; 
      } 
      if(e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)){ 
      return false; 
      } 
      if(e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)){ 
      return false; 
      } 
      if(e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)){ 
      return false; 
      } 
      } 
      </script> 
 
</body>

