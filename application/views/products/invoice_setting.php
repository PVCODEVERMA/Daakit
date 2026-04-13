
<?php $custom_name = $custom_value=array();
if(isset($invoice_setting_data) && !empty($invoice_setting_data->custom_name))
{
    $custom_name = explode(",",$invoice_setting_data->custom_name);
}
if(isset($invoice_setting_data) && !empty($invoice_setting_data->custom_value))
{
    $custom_value = explode(",",$invoice_setting_data->custom_value);
}
?>
<form method="post" action="<?= current_url(); ?>" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-12">
        <div class="panel_s">
            <div class="panel-body">
            <h4 class="customer-profile-group-heading">Invoice Configuration</h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class=" col-md-12">
                            <div class="panel panel-success">
                                <div class="panel-heading">Invoice Setting</div>
                                    <div class="panel-body">
                                    <div class="col-md-12 col-sm-12 border-right">
                                                <div class="form-group">
                                                    <h5>Show/hide Company Name</h5>
                                                </div>
                                                <div class="form-group border-bottom pb-4">
                                                    <label class="cstm-switch">
                                                        <span class="cstm-switch-description ml-0 h6 mr-2">Hide company name in your invoice </span>
                                                        <div class="onoffswitch">
                                                            <input type="checkbox" name="option" value="1"  <?php if(isset($invoice_setting_data) && $invoice_setting_data->hide_compony=='1'  ) { echo "checked";} ?> id="c_22" data-id="22" class="onoffswitch-checkbox">
                                                            <label class="onoffswitch-label" for="c_22"></label>
                                                        </div>
                                                        <span class="cstm-switch-indicator bg-success"></span>
                                                    </label>
                                                </div>
                                                <div class="form-group mt-4 pb-4 border-bottom">
                                                    <h5>Set prefix in your invoice number</h5>
                                                    <label for="inputAddress">This prefix will shown on your invoice along with the invoice number for Ex. DGCL001</label>
                                                    <input type="text" name="comp_prifix"  value="<?= set_value('comp_prifix', !empty($invoice_setting_data->invoice_prefix) ? $invoice_setting_data->invoice_prefix : ''); ?>" maxlength="10" class="form-control" id="inputAddress" placeholder="Input Prefix">
                                                </div>
                                                <div class="mt-4 border-bottom">
                                                    <div class="form-group mb-4">
                                                        <h5 class="mb-0">Set Logo  <span data-toggle="tooltip" data-placement="top" title="Avoid blurry or pixelated images by uploading your file in the required size and aspect ratio.">
                                                                <i class="fa fa-question-circle"></i>
                                                            </span></h5>
                                                      <!--  <label for="inputAddress">Required Image Size : 800x600-->
                                                           
                                                        </label>
                                                        <div class="custom-file">
                                                            <input type="file" class="form-control" id="inputGroupFile02"  name="picture"  accept="image/png, image/jpg, image/jpeg"   onchange="loadImg()">
                                                        </div>
                                                        <div class="image-load text-left mtop10">
                                                            <img id="frame" src="<?php if(isset($invoice_setting_data) && !empty($invoice_setting_data->invoice_banner ) ) { echo $invoice_setting_data->invoice_banner ;} else { echo base_url("assets/images/preview-not-available.jpg");} ?>" width="100px" height="100px" src="assets/img/no-image.png" />
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <h5 class="" mb-0>Set Signature   <span data-toggle="tooltip" data-placement="top" title="Avoid blurry or pixelated images by uploading your file in the required size and aspect ratio.">
                                                                <i class="fa fa-question-circle"></i>
                                                            </span></h5>
                                                       <!-- <label for="inputAddress">Required Image Size : 800x600 -->
                                                          
                                                        </label>
                                                        <div class="fileinput fileinput-new" data-provides="fileinput"></div>
                                                        <div class="custom-file">
                                                            <input type="file" class="form-control" id="inputGroupFile02" id="img_file"  name="signatureimg" accept="image/png, image/jpg, image/jpeg"  onChange="img_pathUrl(this);">
                                                        </div>
                                                        <div class="image-load text-left mtop10">
                                                            <img src="<?php if(isset($invoice_setting_data) && !empty($invoice_setting_data->invoice_signature ) ) { echo $invoice_setting_data->invoice_signature ;} else { echo base_url("assets/images/preview-not-available.jpg");} ?>" id="img_url" width="100px" height="100px" src="assets/img/no-image.png" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="table-sections mt-4">
                                                        <h5>Customize Field</h5>
                                                        <p>This option enables you to add the field along with value you want to show/print on your invoice</p>
                                                        <div class="table-responsive mt-4">
                                                            <table class="table table dataTable no-footer">
                                                                <thead>
                                                                    <tr>
                                                                        <th><span class="bold">Column Name </span></th>
                                                                        <th colspan="2"><span class="bold">Value</span></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="field_wrapperb2c" class="customize_field">

                                                <?php
                                                $a=0;
                                                $b=50;
                                                if(set_value('column_name')!='' && set_value('value')!=''){
                                            $custom_name= set_value('column_name');
                                            $custom_value= set_value('value');
                                                }

                                            if(!empty($custom_value) && !empty($custom_name)) { foreach($custom_name as $ck=>$cn)  { ?>
                                                <tr class=" custom_filds" id="filter_number_<?php if($a>0) { echo $b; }?>">
                                                        <td class="">
                                                        <input type="text" class="form-control nameval" maxlength="50"  <?php if($a>0) {  ?> required <?php } ?>  name="column_name[]" value="<?php echo $cn;?>" >
                                                        </td>
                                                        
                                                        <td class=""  >
                                                        <input type="text" class="form-control nameval" maxlength="50" <?php if($a>0) {  ?> required <?php } ?>  name="value[]" value="<?php echo $custom_value[$ck];?>" > 
                                                        </td>
                                                        <?php if($a>0) {  ?>
                                                        <td >
                                                        <a href="javascript:void(0);" class="btn btn-danger btn-sm" id="remove_button" href="javascript:void(0);"  onclick="deleteFilterRow('<?php echo $b;?>');"  title="Remove Field" ><i class="fa fa-minus"></i></a></td>

                                                    
                                                        </td>
                                                        <?php } else {  ?>
                                                        <td>
                                                                                    <a class="btn btn-primary btn-sm" id="addmorefieldsb2c" href="javascript:void(0);" title="Add Field"><i
                                                                                                class="fa fa-plus"></i></a>
                                                                                </td>

                                                        <?php } ?>
                                                    </tr>

                                                    <?php $a++; $b++; } } else {?>
                                                                            <tr id="customerfield0" class=" custom_filds">
                                                                                <td>
                                                                                    <input class="form-control nameval" maxlength="50"   name="column_name[]" type="text" >
                                                                                </td>
                                                                                <td>
                                                                                    <input class="form-control nameval" maxlength="50"  name="value[]"   type="text">
                                                                                </td>
                                                                                <td>
                                                                                    <a class="btn btn-primary btn-sm" id="addmorefieldsb2c" href="javascript:void(0);" title="Add Field"><i
                                                                                                class="fa fa-plus"></i></a>
                                                                                </td>
                                                                            </tr>

                                                                            <?php } ?>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-md-6">
                        <div class=" col-md-12">
                            <div class="panel panel-success">
                                <div class="panel-heading">Label Details</div>
                                <div class="panel-body">
                                    <div class="col-md-6">
                                        <div class="invoice_exa">
                                            <img src="<?php echo base_url("assets/img/invoice-option.jpg");?>" alt="" class="img-fluid">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->
                </div>
                <div class="clearfix"></div>
                <div class="btn-bottom-toolbar text-right">
                    <button type="submit" data-form="#project_form" class="btn btn-info" autocomplete="off" data-loading-text="Please wait...">Save</button>
                </div>
            </div>
        </div>
    </div>
<form>
<script type="text/javascript">
        $(document).ready(function() {
            var addButton = $('#addmorefieldsb2c');
            var wrapper = $('#field_wrapperb2c');
            var x = 1;
            $(addButton).click(function() {
               if($('.custom_filds').length >=5)
      {
         alert("Can't add more than 5 columns");
         return false;
      }
                var fieldHTML = '<tr id="customerfield' + x + '" class="custom_filds">\
        <td>\
        <input type="text" class="form-control nameval" onkeypress name="column_name[]" maxlength="50"  required placeholder="">\
        </td>\
        <td>\
        <input type="text" class="form-control nameval" name="value[]" maxlength="50"  required placeholder="">\
        </td>\
        <td>\
        <a href="javascript:void(0);" class="btn btn-danger btn-sm" id="remove_button" href="javascript:void(0);" onclick="removedivB2C(' + x + ');"  title="Remove Field" ><i class="fa fa-minus"></i></a></td>\
        </tr>';
                x++;
                $(wrapper).append(fieldHTML);
                $('.js-example-data-array').select2();
            });
        });

        function removedivB2C(id) {
            var element = document.getElementById("customerfield" + id);
            element.parentNode.removeChild(element);
        }
    </script>
    <script>
        function loadImg() {
            $('#frame').attr('src', URL.createObjectURL(event.target.files[0]));
        }
    </script>
    <script>
        function img_pathUrl(input) {
            $('#img_url')[0].src = (window.URL ? URL : webkitURL).createObjectURL(input.files[0]);
        }

      //   $('.nameval').keypress(function (e) { 
      //       var regex = new RegExp("^[a-zA-Z0-9 ]+$");
      //       var strigChar = String.fromCharCode(!e.charCode ? e.which : e.charCode);
      //       if (regex.test(strigChar)) {
      //          return true;
      //       }
      //       return false
      //    });

  $(document).ready(function() {
   $('.nameval1').keypress(function (e) {  alert('hello');
    var regex = new RegExp("^[a-zA-Z0-9 ]+$");
    var strigChar = String.fromCharCode(!e.charCode ? e.which : e.charCode);
    if (regex.test(strigChar)) {
        return true;
    }
    return false
  });

});


  $('.save_changs').click(function (e) { //##
   $('.nameval').each(function( value) {
      var regex = new RegExp("^[a-zA-Z0-9 ]+$");
      if(this.value!=''){
         if (!regex.test(this.value)) 
         {
            event.preventDefault();
            alert('Special Character not allowed in Column Name and Value');
            return false
         }
      }
     });
 });


 function deleteFilterRow(id) {
      var checkstr =  confirm('Are you sure you want to delete this?');
         if(checkstr == true){
                  var element = document.getElementById("filter_number_" + id);
                  element.parentNode.removeChild(element);
         }else{
            return false;
            }
    }
    </script>