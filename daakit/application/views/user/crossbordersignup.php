

<section class="header">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <a href="" class="logo inter-font">deltaGlobal</a>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-7 col-md-6 mobl_ord2">
                    <div class="banner-content">
                        <h1 class="inter-font">Big Or Small, We  Ship It All.</h1>
                        <p>Experience seamless international shipping with an advanced, integrated logistics platform</p>
                        <ul>
                            <li><span><img src="<?php echo base_url(); ?>assets/cb/img/banner-icon.png" alt=""></span><p>Ship Across <strong>196+ Countries</strong></p></li>
                            <li><span><img src="<?php echo base_url(); ?>assets/cb/img/banner-icon2.png" alt=""></span><p>Start <strong>International Shipping</strong> at INR 215/50gm</p></li>
                            <li><span><img src="<?php echo base_url(); ?>assets/cb/img/banner-icon3.png" alt=""></span><p><strong>11+</strong> Courier Service Partners</p></li>
                            <li><span><img src="<?php echo base_url(); ?>assets/cb/img/banner-icon4.png" alt=""></span><p>Seamless <strong>Clearance</strong></p></li>
                            <li><span><img src="<?php echo base_url(); ?>assets/cb/img/banner-icon5.png" alt=""></span><p>Hassle-free <strong>End-to-end Tracking</strong></p></li>
                        </ul>
                    </div>
                </div>
              
                <div class="col-md-5 col-sm-12 col-xs-12">
                        <div class="signup_form">
                            <h2>Sign Up</h2>
                            <form action="<?= current_url(); ?>" class="formerror" method="post">  
                            <?php if (!empty($error)) { ?>
                            <div class="formerror"><?= $error; ?></div>
                        <?php } ?>
                        <?php if (!empty($success)) { ?>
                            <div class="formsuccess"><?= $success; ?></div>
                        <?php } ?>
                                <div class="form-group input-wrapper">
                                    <input type="text" class="form-control" placeholder="" name="fname"  value="<?= set_value('fname'); ?>" required>
                                    <span class="placeholder" data-placeholder=" Name"></span>
                                    <?php echo form_error('fname'); ?>
                                </div>
                                <div class="form-group input-wrapper">
                                    <input type="text" class="form-control" placeholder="" name="email" value="<?= set_value('email'); ?>" required>
                                    <span class="placeholder" data-placeholder="Email"></span>
                                    <?php echo form_error('email'); ?>
                                </div>
                                <div class="form-group input-wrapper"> 
					
								<input type="password" class="form-control" name="password" value="<?=set_value('password'); ?>" placeholder="" required>
								<span class="placeholder" data-placeholder="Password"></span>
                                <?php echo form_error('password'); ?>
							  </div>
                                <div class="form-group input-wrapper">
                                    <input type="text" class="form-control" placeholder="" name="phone" value="<?=set_value('phone'); ?>" required>
                                    <span class="placeholder" data-placeholder="Contact No"></span>
                                    <?php echo form_error('phone'); ?>
                                </div>

                                <div class="form-group input-wrapper">
                                    <input type="text" class="form-control" placeholder="" name="companyname" value="<?=set_value('companyname'); ?>" required>
                                    <span class="placeholder" data-placeholder="Company Name"></span>
                                    <?php echo form_error('companyname'); ?>
                                </div>

                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="exampleCheck1"  name="aggreetc" value="1" required checked>
                                    <label class="form-check-label" for="exampleCheck1"> By submitting this form, you
                                        agree to delta Global's user <a href="https://deltagloabal.com/terms-conditions" target="_blank;" style="text-decoration:none; color:#cc0033 !important" class="d-block d-sm-inline">Privacy Statement.</a>
									</label>
                                        <?php echo form_error('aggreetc'); ?>
                                </div>
                                <button type="submit" class="btn btn-success btn_custom">Start Free</button>
                              
                            </form>
                            <p class="form_login text-right mb-0">
                            Have an account? <a href="<?php echo base_url();?>">Login Now</a>
                        </p>
                        </div>
                    </div>
            </div>
        </div>
    </section>
    <section class="company-section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="owl-carousel company-carousel">
                        <div class="item"><img src="<?php echo base_url(); ?>assets/cb/img/company-logo.png"></div>
                        <div class="item"><img src="<?php echo base_url(); ?>assets/cb/img/company-logo1.png"></div>
                        <div class="item"><img src="<?php echo base_url(); ?>assets/cb/img/company-logo2.png"></div>
                        <div class="item"><img src="<?php echo base_url(); ?>assets/cb/img/company-logo3.png"></div>
                        <div class="item"><img src="<?php echo base_url(); ?>assets/cb/img/company-logo4.png"></div>
                        <div class="item"><img src="<?php echo base_url(); ?>assets/cb/img/company-logo5.png"></div>
                        <div class="item"><img src="<?php echo base_url(); ?>assets/cb/img/company-logo6.png"></div>
                        <div class="item"><img src="<?php echo base_url(); ?>assets/cb/img/company-logo7.png"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="deltaglobal">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 m-auto common-content">
                    <h3 class="common-title">Why <span>deltaGlobal</span>?</h3>
                    <p class="common-para">Growing an eCommerce business from your home country to across the globe is a big challenge. Let us scale your international logistics journey through our automated shipping platform.</p>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-6 d-flex">
                    <div class="deltaglobal-box">
                        <div class="deltaglobal-icon">
                            <figure>
                                <img src="<?php echo base_url(); ?>assets/cb/img/deltaglobal-icon.png" alt="">
                            </figure>
                        </div>
                        <div class="deltaglobal-content">
                            <h4>Ease of international shipping</h4>
                            <ul>
                                <li>Quick documentation</li>
                                <li>Multiple international courier partners</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 d-flex">
                    <div class="deltaglobal-box">
                        <div class="deltaglobal-icon">
                            <figure>
                                <img src="<?php echo base_url(); ?>assets/cb/img/deltaglobal-icon1.png" alt="">
                            </figure>
                        </div>
                        <div class="deltaglobal-content">
                            <h4>Latest tech at your fingertips</h4>
                            <ul>
                                <li>One-click API integration</li>
                                <li>Advanced NDR panel</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 d-flex">
                    <div class="deltaglobal-box">
                        <div class="deltaglobal-icon">
                            <figure>
                                <img src="<?php echo base_url(); ?>assets/cb/img/deltaglobal-icon2.png" alt="">
                            </figure>
                        </div>
                        <div class="deltaglobal-content">
                            <h4>Enhanced shipping experience</h4>
                            <ul>
                                <li>Competitive international shipping rates</li>
                                <li>Unified tracking feature</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="keyservice">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 m-auto common-content">
                    <h3 class="common-title"><span>Key</span> Services</h3>
                    <p class="common-para">We create business value with hassle-free import/export for eCommerce sellers, e-tailers, marketplaces, and individuals with our unsurpassed international courier services.</p>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-6 d-flex">
                    <div class="keyservice-box">
                        <figure>
                            <img src="<?php echo base_url(); ?>assets/cb/img/key-service.jpg" class="img-fluid" alt="">
                        </figure>
                        <h4>delta Ecom+</h4>
                        <p>delta Ecom+ makes international trading easy for eCommerce entrepreneurs by eliminating the need to clear customs and handle the complex documentation process.</p>
                        <ul>
                            <li>Minimum documentation & fastest global shipping</li>
                            <li>Hassle-free eCommerce export across 36 countries</li>
                            <li>Competitive shipping rates for lucrative profit margin</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 d-flex">
                    <div class="keyservice-box">
                        <figure>
                            <img src="<?php echo base_url(); ?>assets/cb/img/key-service1.jpg" class="img-fluid" alt="">
                        </figure>
                        <h4>delta Priority+</h4>
                        <p>Manage international eCommerce deliveries at competitive rates with quick onboarding, one-click API integration, and unified shipment tracking.</p>
                        <ul>
                            <li>Seamless dashboard experience with easy multi-carrier shipping</li>
                            <li>Top international courier companies for uninterrupted logistics operations</li>
                            <li>End-to-end customs handling, instant AWB generation & fast-track shipments</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 d-flex">
                    <div class="keyservice-box">
                        <figure>
                            <img src="<?php echo base_url(); ?>assets/cb/img/key-service2.jpg" class="img-fluid" alt="">
                        </figure>
                        <h4>delta D2DAC+</h4>
                        <p>Maximize your B2B potential with door-to-door air cargo services. We offer simplified door-to-door deliveries based on your specific international shipping needs with zero need for storage of goods.</p>
                        <ul>
                            <li>Hub & spoke console services with all major countries covered like USA, UK, Dubai, & others</li>
                            <li>Highly competitive shipping price, professional staff, rigorous security & fast transit time</li>
                            <li>Seamless clearance, and efficient and affordable distribution of consignments</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 d-flex">
                    <div class="keyservice-box">
                        <figure>
                            <img src="<?php echo base_url(); ?>assets/cb/img/key-service3.jpg" class="img-fluid" alt="">
                        </figure>
                        <h4>delta WF+</h4>
                        <p>Store your inventory close to your customers across major countries and leverage the advanced features of our WMS to handle the order flow seamlessly.</p>
                        <ul>
                            <li>Safest inventory storage across the USA, UK, Indonesia & Dubai for a streamlined supply chain</li>
                            <li>Reduced transit distance lets you deliver orders fastest in 2 days at a lesser price</li>
                            <li>Track and trace every international shipment through a unified tracking system</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 d-flex">
                    <div class="keyservice-box">
                        <figure>
                            <img src="<?php echo base_url(); ?>assets/cb/img/key-service4.jpg" class="img-fluid" alt="">
                        </figure>
                        <h4>delta FF+</h4>
                        <p>Book shipments in minutes and move your commodities across international locations with our advanced port-to-port freight forwarding services.</p>
                        <ul>
                            <li>Deliver consignments on time, every time across the world, at exclusive freight shipping rates</li>
                            <li>Extensive airline and sea line choices to ensure the fastest freight forwarding</li>
                            <li>CHA facility for smooth entry & departure of import/export items at the customs station</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="featurebenefit">
        <div class="container">
            <div class="row">
                <div class="col-lg-9 m-auto common-content">
                    <h3 class="common-title"><span>Key</span> Features & Benefits</h3>
                    <p class="common-para">Leverage extraordinary features and benefits to ship your eCommerce orders, and enjoy sky-rocketing growth in international markets.</p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-9 m-auto">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-sm-6 d-flex">
                            <div class="featurebenefit-box">
                                <figure>
                                    <img src="<?php echo base_url(); ?>assets/cb/img/featurebenefit-icon.png" alt="">
                                </figure>
                                <h4>Minimum documentation</h4>
                                <p>Experience safest import/export with minimum documents</p>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-6 d-flex">
                            <div class="featurebenefit-box">
                                <figure>
                                    <img src="<?php echo base_url(); ?>assets/cb/img/featurebenefit-icon1.png" alt="">
                                </figure>
                                <h4>Widest Reach</h4>
                                <p>Expand your business presence with international shipping across 196+ countries</p>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-6 d-flex">
                            <div class="featurebenefit-box">
                                <figure>
                                    <img src="<?php echo base_url(); ?>assets/cb/img/featurebenefit-icon2.png" alt="">
                                </figure>
                                <h4>Quickest Delivery</h4>
                                <p>Automated shipping solutions & inventory storage near customers for the fastest delivery</p>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-6 d-flex">
                            <div class="featurebenefit-box">
                                <figure>
                                    <img src="<?php echo base_url(); ?>assets/cb/img/featurebenefit-icon3.png" alt="">
                                </figure>
                                <h4>Competitive Shipping Rates</h4>
                                <p>Competitive import/export expenses through combined consignments</p>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-6 d-flex">
                            <div class="featurebenefit-box">
                                <figure>
                                    <img src="<?php echo base_url(); ?>assets/cb/img/featurebenefit-icon4.png" alt="">
                                </figure>
                                <h4>Post-shipment Tracking</h4>
                                <p>Spare the hassle and track your shipments in real-time with unified tracking</p>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-6 d-flex">
                            <div class="featurebenefit-box">
                                <figure>
                                    <img src="<?php echo base_url(); ?>assets/cb/img/featurebenefit-icon5.png" alt="">
                                </figure>
                                <h4>Hassle-free Clearance</h4>
                                <p>End-to-end customs clearance through in-house CHA</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="landpg_count_main">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="landpg_count">
                        <div class="count_box">
                            <div class="count_number">
                                42,000+
                            </div>
                            <p>Happy Clients</p>
                        </div>
                        <div class="count_box">
                            <div class="count_number">
                                196+
                            </div>
                            <p>Countries</p>
                        </div>
                        <div class="count_box">
                            <div class="count_number">
                                2 M+
                            </div>
                            <p>Transactions Everyday</p>
                        </div>
                        <div class="count_box">
                            <div class="count_number">
                                200+
                            </div>
                            <p>Experts in Team</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="partners">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 m-auto common-content">
                    <h3 class="common-title">Integrations</h3>
                    <p class="common-para">Fastest one-click API integration with leading carrier partners, marketplaces, and channels for maximum ease of import/export for your online business.</p>
                </div>
            </div>
            <div class="row">
                <div class="col-12 partners-box">
                    <ul>
                        <li><img src="<?php echo base_url(); ?>assets/cb/img/partner_icon.png" alt=""></li>
                        <li><img src="<?php echo base_url(); ?>assets/cb/img/partner_icon1.png" alt=""></li>
                        <li><img src="<?php echo base_url(); ?>assets/cb/img/partner_icon2.png" alt=""></li>
                        <li><img src="<?php echo base_url(); ?>assets/cb/img/partner_icon3.png" alt=""></li>
                        <li><img src="<?php echo base_url(); ?>assets/cb/img/partner_icon4.png" alt=""></li>
                        <li><img src="<?php echo base_url(); ?>assets/cb/img/partner_icon5.png" alt=""></li>
                        <li><img src="<?php echo base_url(); ?>assets/cb/img/partner_icon6.png" alt=""></li>
                        <li><img src="<?php echo base_url(); ?>assets/cb/img/partner_icon7.png" alt=""></li>
                        <li><img src="<?php echo base_url(); ?>assets/cb/img/partner_icon8.png" alt=""></li>
                        <li><img src="<?php echo base_url(); ?>assets/cb/img/partner_icon9.png" alt=""></li>
                        <li><img src="<?php echo base_url(); ?>assets/cb/img/partner_icon10.png" alt=""></li>
                        <li><img src="<?php echo base_url(); ?>assets/cb/img/partner_icon11.png" alt=""></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <section class="clients">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 m-auto common-content">
                    <h3 class="common-title">Our Clients Say It All</h3>
                    <p class="common-para">42,000+ clients trust us for an extraordinary global shipping experience. Let’s hear it from them.</p>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="owl-carousel clients-carousel">
                        <div class="item">
                            <div class="clients-box">
                                <div class="clients-review">
                                    <h4>Most economical 3PL solution</h4>
                                    <p>The majority of our customer base is in the US and deltaGlobal assists us in fulfilling orders the fastest. Leveraging their international 3PL services has let us improve our customers’ delivery experience and it has significantly impacted our fulfilment expense as well.</p>
                                </div>
                                <div class="about-clients">
                                    <div class="clients-img">
                                        <figure>
                                            <img src="<?php echo base_url(); ?>assets/cb/img/client-img.jpg" alt="">
                                        </figure>
                                    </div>
                                    <div class="clients-info">
                                        <h5>Mang Oleh</h5>
                                        <p>Business Owner</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="item">
                            <div class="clients-box">
                                <div class="clients-review">
                                    <h4>Quick and Compliant</h4>
                                    <p>We can always count on deltaGlobal for compliance, speed, and accuracy. deltaGlobal has been able to reduce our turnaround time to 4 days, which is a great improvement for us. It helps us strengthen our relationship with the customers.</p>
                                </div>
                                <div class="about-clients">
                                    <div class="clients-img">
                                        <figure>
                                            <img src="<?php echo base_url(); ?>assets/cb/img/client-img1.jpg" alt="">
                                        </figure>
                                    </div>
                                    <div class="clients-info">
                                        <h5>Meenal Ahuja</h5>
                                        <p>Entrepreneur, Bangalore</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="item">
                            <div class="clients-box">
                                <div class="clients-review">
                                    <h4>Fast and Fantastic!</h4>
                                    <p>The deltaGlobal team is always available, proactive, and knowledgeable. They work with us closely to ensure shipments are delivered on time and in a safe condition. The team also educates us on the nuances of global logistics and customs which helps us in avoiding delays and penalties. We look forward to continuing working with them.</p>
                                </div>
                                <div class="about-clients">
                                    <div class="clients-img">
                                        <figure>
                                            <img src="<?php echo base_url(); ?>assets/cb/img/client-img2.jpg" alt="">
                                        </figure>
                                    </div>
                                    <div class="clients-info">
                                        <h5>Harsh Mittal</h5>
                                        <p>Entrepreneur, Mumbai</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-12 footer-box">
                    <p>© 2022 deltagloabal, All Rights Reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.slim.min.js"></script> -->
    <script src="<?php echo base_url(); ?>assets/cb/js/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/cb/js/owl.carousel.min.js"></script>
    <script>
        $('.company-carousel').owlCarousel({
            loop: true,
            nav: false,
            autoplay: true,
            autoplayTimeout: 1000,
            autoplaySpeed: 1000,
            slideTransition: 'linear',
            autoplayHoverPause: true,
            margin: 10,
            responsiveClass: true,
            responsive: {
                320: {
                    items: 1
                },
                361: {
                    items: 2
                },
                481: {
                    items: 3
                },
                768: {
                    items: 4
                },
                992: {
                    items: 7
                }
            }
        })

        $(document).ready(function() {
            var owl = $('.clients-carousel');
            owl.owlCarousel({
                loop: true,
                margin: 0,
                responsive: {
                    320: {
                        items: 1
                    },
                    481: {
                        items: 2
                    },
                    768: {
                        items: 2
                    },
                    992: {
                        items: 3
                    }
                }
            });
        });
    </script>