<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">Invoicing & Legal Entity</h4>
    <ol class="breadcrumb">
    <?php if(!empty($profile)){ 
			?>
			<button class="btn btn-success" style="border-radius: unset;">
				<i class="fa fa-check-circle" style="font-size: large;" aria-hidden="true"></i>
			</button>
			<?php
		}
		else{
			?>
			<button class="btn btn-danger" style="border-radius: unset;">
				<i class="fa fa-times-circle" style="font-size: large;" aria-hidden="true"></i>
			</button>
		<?php
		}
		?>
        <li class="breadcrumb-item">						
            <select class="form-control" onchange="return hrefUrlLocation(this.value)" style="width: 100% !important;border-radius: unset;">
                <option value="profile">Company Profile</option>
				<option value="profile/kycdetails">KYC</option>
				<option value="profile/legalentity" selected>GST Details</option>
				<option value="profile/cmpaccountdetails">Bank A/C Details</option>
				<option value="profile/agreement">Aggrement</option>
			</select>
		</li>
    </ol>
</div>
<!-- END PAGE-HEADER -->
<div class="main-container container-fluid">

<!-- START ROW-1 -->
<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">Legal Entity</h3>
			</div>
			<div class="card-body">
            <?php if(empty($profile)) { ?>
            <form method="post" action="<?= current_url(); ?>" enctype="multipart/form-data">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-row">
                                <!-- <div class="form-group col-md-6 required">
                                    <label class="control-label">Do You Have Registered GST Entity?</label>
                                    <select name="type" class="form-control" onchange="gst_type(this.value)" required>
                                        <option value=""> Select</option>
                                        <option value="1" <?php echo (set_value('type')=='1')?" selected=' selected'":""?>>Yes</option>
                                        <option value="0" <?php echo (set_value('type')=='0')?" selected=' selected'":""?>>No</option>
                                    </select>
                                </div> -->
                                <!-- <div class="form-group col-md-6 required d-none">
                                    <label class="control-label">GST Number</label>
                                    <div class="input-group">
                                    <input type="text" name="legal_gstno" autocomplete="off" class="form-control" value="<?= set_value('legal_gstno', empty($profile->legal_gstno) ? '': $profile->legal_gstno); ?>" required/>                                            
                                    <span class="input-group-btn">
                                    <button class="btn btn-primary search_gst" style="height: 36px;" type="button" onclick="searchGST()"><span class="glyphicon glyphicon-search" aria-hidden="true">
                                    </span> Search</button>
                                    </span>
                                    </div>
                                </div> -->
                            </div>   
                            <div class="gst_fetch" <?php echo (set_value('type')=='0') ? 'style="display:block;"': ''; ?> > 
                            <div class="form-row">
                                <div class="form-group col-md-6 required">
                                    <label class="control-label">GST No. <i class="fa fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="The Legal Entity GST No field must be exactly 15 characters in length." aria-hidden="true"></i></label></label>
                                    <input type="text" name="legal_gstno" autocomplete="off" class="form-control" value="<?= set_value('legal_gstno', empty($profile->legal_gstno) ? '': $profile->legal_gstno); ?>" required/>                                            
                                </div>
                                <div class="form-group col-md-6 required">
                                    <label class="control-label">Company Name </label>
                                    <input type="text" name="legal_name" class="form-control" value="<?= set_value('legal_name', empty($profile->legal_name) ? '': $profile->legal_name ); ?>" required <?php echo (set_value('type')=='1') ? 'readonly': ''; ?>/>
                                </div>
                                <div class="form-group col-md-6 required">
                                    <label class="control-label">Company Address</label>
                                    <textarea class="form-control" name="legal_address" required <?php echo (set_value('type')=='1') ? 'readonly': ''; ?>><?= set_value('legal_address', !empty($profile->legal_address) ? $profile->legal_address : ''); ?></textarea>
                                </div>
                                <div class="form-group col-md-6 required">
                                    <label class="control-label">Pin Code <i class="fa fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="The Pincoce field must be exactly 6 characters in length." aria-hidden="true"></i></label></label>
                                    <input type="text" name="legal_pincode" class="form-control" value="<?= set_value('legal_pincode', !empty($profile->legal_pincode) ? $profile->legal_pincode : ''); ?>" required <?php echo (set_value('type')=='1') ? 'readonly': ''; ?>/>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6 required">
                                    <label class="control-label">City</label>
                                    <input type="text" name="legal_city" class="form-control" value="<?= set_value('legal_city', !empty($profile->legal_city) ? $profile->legal_city : ''); ?>" required <?php echo (set_value('type')=='1') ? 'readonly': ''; ?>/>
                                </div>
                                <div class="form-group col-md-6 required">
                                    <label class="control-label">State</label>
                                    <select name="legal_state" class="form-control js-select2 legal_state" style="width: 100% !important;" required <?php echo (set_value('type')=='1') ? 'readonly': ''; ?>>
                                        <option value="">Select State</option>
                                        <?php
                                        asort($state_codes);
                                        foreach ($state_codes as $key => $values) {
                                        ?>
                                            <option <?php if (strtolower(set_value('legal_state')) == strtolower($values)) { ?> selected <?php } ?> value="<?php echo str_replace('&', 'and', ucwords($values)); ?>"><?php echo ucwords($values); ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>

                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                        <label>Upload Your GST</label>
                                        <div class="input-group mb-6">
                                            <div class="custom-file">
                                                <input type="file" class="form-control" name="gstimage" id="gstimage" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <span class="image-box-content">
                                            <?php if (!empty($profile->cmp_gstimg)) {
                                            ?>
                                                <img src="<?php echo (strpos($profile->cmp_gstimg, "amazonaws.com") !== false) ? ($profile->cmp_gstimg) : ($profile->cmp_gstimg); ?>" width="100" height="100">
                                            <?php } else { ?>
                                                <!-- <img src="assets/seller_company_logo/dummy_img.jpg" id="logo_img" width="100" height="100"> -->
                                            <?php } ?>
                                        </span>
                                    </div>
                            </div> 
                        </div>
                        </div>
                        <div class="col-md-6">
                        </div>
                        <div class="form-group text-right">
                            <button class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </div>
            </form>
            <?php } else{ ?>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <?php //if($profile->type=='1'){ ?>
                            <div class="form-row">
                                <div class="form-group col-md-6 ">
                                    <label class="control-label">GST Number</label>
                                    <div class="input-group">
                                    <input type="text" name="legal_gstno" autocomplete="off" class="form-control" value="<?= set_value('legal_gstno', empty($profile->legal_gstno) ? '': $profile->legal_gstno); ?>" readonly />                                            
                                
                                    </div>
                                </div>
                            </div> 
                            <?php //} ?>  
                            <div class=""> 
                            <div class="form-row">
                                <div class="form-group col-md-6 required">
                                    <label>Company Name </label>
                                    <input type="text"  class="form-control" value="<?= set_value('legal_name', empty($profile->legal_name) ? '': $profile->legal_name ); ?>"  readonly/>
                                </div>
                                <div class="form-group col-md-6 required">
                                    <label class="control-label">Company Address</label>
                                    <textarea class="form-control"  readonly><?= set_value('legal_address', !empty($profile->legal_address) ? $profile->legal_address : ''); ?></textarea>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6 required">
                                    <label class="control-label">City</label>
                                    <input type="text" class="form-control" value="<?= set_value('legal_city', !empty($profile->legal_city) ? $profile->legal_city : ''); ?>" disabled/>
                                </div>
                                <div class="form-group col-md-6 required">
                                    <label class="control-label">State</label>
                                    <select class="form-control js-select2 legal_state" style="width: 100% !important;"  disabled>
                                        <option value="">Select State</option>
                                        <?php
                                        asort($state_codes);
                                        foreach ($state_codes as $key => $values) {
                                            if (!empty($profile->legal_state) && strtolower($profile->legal_state) == strtolower($values)) { 
                                        ?>
                                            <option  selected value="<?php echo ucwords($values); ?>"><?php echo ucwords($values); ?></option>
                                        <?php
                                        } }
                                        ?>
                                    </select>

                                </div>
                                <div class="form-group col-md-6 required">
                                    <label class="control-label">Pin Code</label>
                                    <input type="text" class="form-control" value="<?= set_value('legal_pincode', !empty($profile->legal_pincode) ? $profile->legal_pincode : ''); ?>"  readonly/>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="control-label">GST Image</label><br>
                                    <span class="image-box-content">
                                        <?php if (!empty($profile->cmp_gstimg)) {
                                        ?>
											<a style="margin-top: 0%" href="<?php echo (strpos($profile->cmp_gstimg, "amazonaws.com") !== false) ? ($profile->cmp_gstimg) : ($profile->cmp_gstimg); ?>" target="_blank" class="btn btn-primary btn-sm">View</a>
                                        <?php } else { ?>
                                            <!-- <img src="assets/seller_company_logo/dummy_img.jpg" id="logo_img" width="100" height="100"> -->
                                        <?php } ?>
                                    </span>
                                </div> 

                            </div> 
                        </div>
                    </div>
                </div>
            <?php } ?>			
        </div>
		</div>
	</div>

</div>
<!-- END ROW-1 -->
</div>
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-xl  modal-dialog-align-top-left" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Important: Our  <span id="section_name"></span> Have Changed</h5>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="update_id" >
                <div class="agreement_modal_textarea">
                    <p>Following are key changes in the terms. Please read carefully and click "Accept" to use deltagloabal</p>
                    <div class="scroll_area">
                        <div class="row">
                            <div class="col-sm-6 border-right">
                                <iframe id="file_url"  style="width:100%;height:700px;"></iframe>
                            </div>
                            <div class="col-sm-6">
                                <p class="mb-0 right-text"><small>
                                    <strong>Key Changes: </strong></small></p>
                                <ul class="listing">
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <div class="f_left">
                    <p>
                        Current Version: <span id="version"></span><br/> Agreement update on: <span id="agrementdate"></span> </p>
                </div>
                <div class="button-group">
                    <button type="button" class="btn btn-warning" class="close" id="skip" data-dismiss="modal" aria-label="Close">Skip for now</button>
                    <button type="button" class="btn btn-danger" class="close"  id="close"  data-dismiss="modal" aria-label="Close">Close</button>
                    <button type="button" id="i_accept" class="btn btn-primary accept_agreement">I Accept</button>
                    
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script>
function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
            $('#logo_img').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}

$("#imglogo").change(function() {
    readURL(this);
});


function readURLgst(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
            $('#gst_image').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}

$("#gstimage").change(function() {
    readURLgst(this);
});

function readURLsign(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
            $('#signature_image').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}

$("#signatureimg").change(function() {
    readURLsign(this);
});

function companychangefunction() {
    var value = document.getElementById("companybvalue").value;
    if (value == 'soloproprietorship') {
        document.getElementById("soloproprietorship_div").style.display = "block";
        document.getElementById("partnership_div").style.display = "none";
    } else if (value == 'partnership') {
        document.getElementById("soloproprietorship_div").style.display = "none";
        document.getElementById("partnership_div").style.display = "block";
    } else {
        document.getElementById("individualrow").style.display = "none";
        document.getElementById("companyrow").style.display = "none";
    }
}
<?php if(empty($profile)) { ?>
function searchGST()
{
    let gstNo = $('input[name="legal_gstno"]').val();
    if(gstNo.length !=15)
    {
        alert('Invalid GST');
        return false;
    }
    $.ajax({
        url: "profile/verify_gst/",
        type: "POST",
        data: {
            gst_no: gstNo,
        },
        datatype: "JSON",
        cache: false,
        success: function (data) {
            if(data.error) 
            {
                $('.gst_fetch').hide();
                alert('Invalid GST no');
                $('input[name="legal_name"]').val('').attr('readonly', false);
                $('textarea[name="legal_address"]').val('').attr('readonly', false);
                $('input[name="legal_city"]').val('').attr('readonly', false);
                $('input[name="legal_pincode"]').val('').attr('readonly', false);
                $('.legal_state').val('').trigger('change');
                // $('.legal_state').select2("enable", true);
                return false;
            }
            row = data.data.data;
            legal_name = row.business_name;
            legal_address = row.address;
            $('input[name="legal_name"]').val(legal_name).attr('readonly', true);
            $('textarea[name="legal_address"]').val(legal_address).attr('readonly', true);
            legal_address_string = legal_address.split(','); 
            pincode =  legal_address_string[legal_address_string.length - 1].trim();
            state =    legal_address_string[legal_address_string.length - 2].trim();
            city =     legal_address_string[legal_address_string.length - 3].trim();
            $('input[name="legal_city"]').val(city).attr('readonly', true);
            $('input[name="legal_pincode"]').val(pincode).attr('readonly', true);
            $('.legal_state').val(state).trigger('change');
            $('.legal_state').attr('readonly', true);
            $('.gst_fetch').show();
        },
    });  
}

function gst_type(type)
{
    if(type =='1')
    {
        $('.search_gst').show();
        $('.gst_fetch').hide();
        $('input[name="legal_gstno"]').attr('required', true);
        $('input[name="legal_gstno"]').parent().parent().addClass('required');
        $('input[name="legal_gstno"]').parent().parent().removeClass('d-none');
    }
    else if(type =='0')
    {
        $('.gst_fetch').show();
        $('.search_gst').hide();
        $('input[name="legal_name"]').val('').attr('readonly', false);
        $('textarea[name="legal_address"]').val('').attr('readonly', false);
        $('input[name="legal_city"]').val('').attr('readonly', false);
        $('input[name="legal_pincode"]').val('').attr('readonly', false);
        $('.legal_state').val('').trigger('change');
        $('.legal_state').attr('readonly', false);
        $('input[name="legal_gstno"]').attr('required', false);
        $('input[name="legal_gstno"]').parent().parent().removeClass('required');
        $('input[name="legal_gstno"]').parent().parent().addClass('d-none');
    }
    else
    {   
        $('.gst_fetch').hide();
        $('.search_gst').hide();
    }
}
<?php } ?>
function hrefUrlLocation(path)
{
    var baseURL = '<?php echo base_url(); ?>';
    // Redirect to a specific path
    window.location.href = baseURL + path;
}
</script>