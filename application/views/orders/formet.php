<style>
.vl {
  border-left: 6px solid green;
  height: 500px;
  position: absolute;
  left: 50%;
  margin-left: -3px;
  top: 0;
}

#collapseOneCouriers .accordion-body {
  padding: 10px;
}

#collapseOneCouriers .card.couriourval {
  background-color: #fdfdfd;
  border-radius: 10px;
  padding: 15px 20px;
  margin-bottom: 10px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

#collapseOneCouriers .card.couriourval:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
}

#collapseOneCouriers .card-header {
  width: 100%;
  padding: 0;
  background: none;
  border: none;
}

#collapseOneCouriers .card-title {
  font-size: 16px;
  margin: 0;
}

#collapseOneCouriers .card-options .btn {
  padding: 6px 14px;
  font-size: 14px;
  border-radius: 6px;
}

#collapseOneCouriers .prefered {
  border-left: 5px solid #28a745; /* green */
}

#collapseOneCouriers .notprefered {
  border-left: 5px solid #28a745; /* red */
}


#collapseGoCouriers {
  
  padding: 10px;
}

#collapseGoCouriers > div > div {
  background-color: #f9f9f9;
  border-radius: 10px;
  padding: 15px 20px;
  margin-bottom: 10px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

#collapseGoCouriers > div > div:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
}

#collapseGoCouriers .card-options {
  margin-left: 2px;
}

#collapseGoCouriers .card-options .btn {
  padding: 6px 14px;
  font-size: 14px;
  border-radius: 6px;
}

#collapseGoCouriers .prefered {
  border-left: 5px solid #28a745; /* green */
}

#collapseGoCouriers .notprefered {
  border-left: 5px solid #28a745; /* red */
}


</style>

<div class="modal-content data">
    <div class="modal-header">
        <h5 class="modal-title">Start Shipping Your Package Today</h5><button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"></button>
    </div>
    <div class="modal-body">
        <form class="ship_form" method="post">
            <div class="row">
                <input type="hidden" name="order_id" value="<?= !empty($order->id) ? $order->id : ''; ?>">
                <input type="hidden"   id="courier_id" name="courier_id" class="form-check-input" >
                <div class="col-md-4">
                    <h6>Shipment Info</h6>
                    <div class="clearfix"></div>
                    <h5 class="no-mtop task-info-created">
                    <small class="text-dark">Created at <span class="text-dark"><?php echo date('d-m-Y h:i A'); ?></span>
                    </small>
                    </h5>
                    <hr class="task-info-separator">
                    <div class="clearfix"></div>
                    <h7>
                        <i class="fa fa-university" aria-hidden="true"></i> Warehouse for Pickup
                    </h7>
                    <div class="simple-bootstrap-select">
                    <div class="dropdown bootstrap-select text-muted task-action-select bs3" style="width: 100%;">
                    <?php if (!empty($warehouses)) { ?>
                        <select data-width="100%" data-order-id="<?= !empty($order->id) ? $order->id : ''; ?>" id="select_warehouse_dropdown" class="form-select form-select-sm select2" name="warehouse_id"  title="Warehouse for Pickup" >
                        <option class="bs-title-option" value="">--Select Warehouse--</option>
                        <?php
                            foreach ($warehouses as $warehouse) {
                            ?>
                                <option <?php if ($warehouse->id == $selected_warehouse) { ?> selected="" <?php } ?> value="<?= $warehouse->id; ?>"><?= ucwords($warehouse->name); ?><?php if ($warehouse->is_default) { ?> (Default) <?php } ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        <?php } ?>
                        <div class="dropdown-menu open">
                        <div class="bs-searchbox">
                            <input type="search" class="form-control" autocomplete="off" role="combobox" aria-label="Search" aria-controls="bs-select-8" aria-autocomplete="list">
                        </div>
                        <div class="inner open" role="listbox" id="bs-select-8" tabindex="-1">
                            <ul class="dropdown-menu inner " role="presentation"></ul>
                        </div>
                        </div>
                    </div>
                    </div>
                    <br>
                    <h7>
                    <i class="fa fa-university" aria-hidden="true"></i> Warehouse for RTO
                    </h7>
                    <div class="simple-bootstrap-select">
                    <div class="dropdown bootstrap-select text-muted task-action-select bs3" style="width: 100%;">
                    <?php if (!empty($warehouses)) { ?>
                        <select data-width="100%"  id="rto_warehouse_id" class="form-select form-select-sm select2" name="rto_warehouse_id" data-live-search="true" title="Warehouse for RTO" data-none-selected-text="Nothing selected" tabindex="-98">
                        <option class="bs-title-option" value="">--Select Warehouse--</option>
                        <?php
                            foreach ($warehouses as $warehouse) {
                            ?>
                                <option <?php if ($warehouse->id == $selected_warehouse) { ?> selected="" <?php } ?> value="<?= $warehouse->id; ?>"><?= ucwords($warehouse->name); ?><?php if ($warehouse->is_default) { ?> (Default) <?php } ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        <?php } ?>
                        <div class="dropdown-menu open">
                        <div class="bs-searchbox">
                            <input type="search" class="form-control" autocomplete="off" role="combobox" aria-label="Search" aria-controls="bs-select-8" aria-autocomplete="list">
                        </div>
                        <div class="inner open" role="listbox" id="bs-select-8" tabindex="-1">
                            <ul class="dropdown-menu inner " role="presentation"></ul>
                        </div>
                        </div>
                    </div>
                    </div>
                    <hr class="task-info-separator">
                    <div class="clearfix"></div>
                </div>  
                <div class="col-md-8">
                    <div class="task-single-related-wrapper">
                    <h6>Related Filters:   
                    </h6>
                    </div>
                    <?php
                        if (!empty($couriers)) {
                        ?>
                            <div class="modal-body " style="padding: 0px;">
                            <div class="row">
                                <div class="col-sm-12 col-md-4">
                                    <h7>Courier Mode Filter:</h7>
                                    <div class="input-group">
                                        <div class="input-group-append" data-toggle="buttons">
                                            <button type="button" class="btn btn-outline-secondary text-dark filter_by shadow-none" style="color:#03A9F4; border:1px solid #03A9F4;">
                                                <i class="mdi mdi-airplane"></i>
                                                <input type="checkbox" name="radio1" value="air" class="filter"> Air
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary text-dark filter_by shadow-none" style="color:#03A9F4; border:1px solid #03A9F4;">
                                                <i class="mdi mdi-truck-fast"></i>
                                                <input type="checkbox" name="radio1" value="surface" class="filter"> Surface
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-sm-12 col-md-8">
                                    <h7>Courier Weight Filter:</h7>
                                    <div class="input-group">
                                        <div class="input-group-append" data-toggle="buttons">
                                            <button type="button" class="btn btn-outline-secondary text-dark filter_by shadow-none" style="color:#03A9F4; border:1px solid #03A9F4;">
                                                <i class="mdi mdi-weight-kilogram"></i>
                                                <input type="checkbox" name="radio2" value="500" class="filter"> 0.5 KG
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary text-dark filter_by shadow-none" style="color:#03A9F4; border:1px solid #03A9F4;">
                                                <i class="mdi mdi-weight-kilogram"></i>
                                                <input type="checkbox" name="radio2" value="1000" class="filter"> 1 KG
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary text-dark filter_by shadow-none" style="color:#03A9F4; border:1px solid #03A9F4;">
                                                <i class="mdi mdi-weight-kilogram"></i>
                                                <input type="checkbox" name="radio2" value="2000" class="filter"> 2 KG
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary text-dark filter_by shadow-none" style="color:#03A9F4; border:1px solid #03A9F4;">
                                                <i class="mdi mdi-truck-delivery"></i>
                                                <input type="checkbox" name="radio2" value="20000" class="filter"> Heavy
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                <br>
                                <!-- <div class="">
                                        <?php
                                        foreach ($couriers as $courier) {
                                        // pr($couriers);exit;
                                        ?>
                                            <div class="card  couriourval  <?php  if(isset($courier->courier_type) and $courier->courier_type!='') { echo $courier->courier_type; } else { echo "";}  ;?>   <?php  if(isset($courier->weight) and $courier->weight!='') { echo $courier->weight; } else { echo "";}   if(isset($courier->prefered) && $courier->prefered=='1' )  { echo " prefered";} else { echo " notprefered";}  ?> "  data-target="<?php  if(isset($courier->courier_type) and $courier->courier_type!='') { echo $courier->courier_type; } else { echo "-";}  ;?> ">
                                                <div class="card-status card-status-left bg-primary br-bl-7 br-tl-4"></div>
                                                <div class="card-header">
                                                    <h3 class="card-title"><span class="custom-control" for="customRadio<?= $courier->id; ?>"><?= $courier->name; ?> <?php if (!empty($courier->charges)) { ?> (&#8377;<?= round($courier->charges, 2); ?>) <?php } ?></span></h3>
                                                    <div class="card-options">
                                                        <h3 class="card-title"><button type="submit" onclick="document.getElementById('courier_id').value='<?php echo $courier->id;?>'" class="btn btn-sm btn-primary">Ship Now</button>
                                                        </h3>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php
                                        }
                                        ?>
                                </div> -->
<?php
  
  $daakitGoIds = [1124, 1125, 1126, 1127, 1128, 1129, 1130, 1131, 1132];

  
  $daakitGoCouriers = array_filter($couriers, function($c) use ($daakitGoIds) {
    return in_array($c->id, $daakitGoIds);
  });

  $hasGo = !empty($daakitGoCouriers);

  $colClassOne = $hasGo ? 'col-md-6' : 'col-md-12';
?>


<div class="row">
  <!-- DAAKit One -->
  <div class="<?= $colClassOne ?>">
    <div class="accordion mb-3" id="daakitOneAccordion">
      <div class="accordion-item">
        <h2 class="accordion-header" id="headingOne">
          <button class="accordion-button collapsed"
                  type="button"
                  data-bs-toggle="collapse"
                  data-bs-target="#collapseOneCouriers"
                  aria-expanded="false"
                  aria-controls="collapseOneCouriers">
            DAAKit One
            <img src="<?php echo base_url(); ?>assets/images/daakit-one-icon.png" alt="Logo" style="height:50px; margin-left:10px;">
          </button>
        </h2>
        <div id="collapseOneCouriers"
             class="accordion-collapse collapse"
             aria-labelledby="headingOne"
             data-bs-parent="#daakitOneAccordion">
          <div class="accordion-body">
            <?php foreach ($couriers as $courier) {
              if (!in_array($courier->id, $daakitGoIds)) { ?>
                <div class="card couriourval <?= $courier->courier_type ?? ''; ?> <?= $courier->weight ?? ''; ?> <?= (isset($courier->prefered) && $courier->prefered == '1') ? 'prefered' : 'notprefered'; ?>">
                  <div class="">
                    <div class="">
                      <?= $courier->name; ?>
                      <?php if (!empty($courier->charges)) { ?>
                        (&#8377;<?= round($courier->charges, 2); ?>)
                      <?php } ?>
                      </div>
                    <div class="card-options">
                      <button type="submit"
                              onclick="document.getElementById('courier_id').value='<?= $courier->id; ?>'"
                              class="btn btn-sm btn-primary">Ship Now</button>
                    </div>
                  </div>
                </div>
            <?php }
            } ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- DAAKit Go -->
  <?php if ($hasGo) { ?>
    <div class="col-md-6">
      <div class="accordion mb-3" id="daakitGoAccordion">
        <div class="accordion-item">
          <h2 class="accordion-header" id="headingGo">
            <button class="accordion-button collapsed"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#collapseGoCouriers"
                    aria-expanded="false"
                    aria-controls="collapseGoCouriers">
              DAAKit Go
              <img src="<?php echo base_url(); ?>assets/images/daakit-go-icon.png" alt="Logo" style="height:50px; margin-left:10px;">
            </button>
          </h2>
          <div id="collapseGoCouriers"
               class="accordion-collapse collapse"
               aria-labelledby="headingGo"
               data-bs-parent="#daakitGoAccordion">
            <div>
              <?php foreach ($daakitGoCouriers as $courier) { ?>
                <div class=" <?= $courier->courier_type ?? ''; ?> <?= $courier->weight ?? ''; ?> <?= (isset($courier->prefered) && $courier->prefered == '1') ? 'prefered' : 'notprefered'; ?>">
                  <div class="">
                    <div class="">
                      <?= $courier->name; ?>
                      <?php if (!empty($courier->charges)) { ?>
                        (&#8377;<?= round($courier->charges, 2); ?>)
                      <?php } ?>
                    </div>
                    <div class="card-options">
                      <button type="submit"
                              onclick="document.getElementById('courier_id').value='<?= $courier->id; ?>'"
                              class="btn btn-sm btn-primary">Ship Now</button>
                    </div>
                  </div>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php } ?>
  
</div>


                                <div class="row p-t-10 mtop20">
                                    <div class="col-sm-12">
                                        <div class="alert alert-info show" role="alert">
                                            <div class="custom-control custom-checkbox custom-control-inline">
                                                <input type="checkbox" name="dg_order" style="border: 1px solid #b7b7b7;" class="form-check-input" id="dg_order" value="1">
                                                <label class="custom-control" for="dg_order">Is handling dangerous goods risky?</label>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if (!empty($secure_shipment)) { ?>
                                        <div class="col-sm-12">
                                            <div class="alert alert-info show" role="alert">
                                                <div class="custom-control custom-checkbox custom-control-inline">
                                                    <input type="checkbox" name="is_insurance" data-order-id="<?= !empty($order->id) ? $order->id : ''; ?>" class="form-check-input" id="is_insurance" value="1">
                                                    <label class="custom-control-label" for="is_insurance">Opt-in for shipment insurance?<?php if (!empty($insurance_price)) { ?> (&#8377;<?= round($insurance_price, 2); ?>) <?php } ?></label>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="col-sm-12">
                                    <p style="color: red;" id="delhiveryselectmessage"></p>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="modal-body">
                                <div class="m-b-10">
                                    <div class="label label inline-block" style="color:#ff6f00;border:1px solid #ff6f00;width:100%; line-height: 30px;">
                                        <?php if(!empty($error)){
                                            $error = (string)$error;
                                            if(trim($error) =='No credit available. Please recharge.'){
                                                echo $error."<span style='margin-left: 173px;'><a target='_blank' href='".base_url('billing/rechage_wallet')."'>Recharge</a></span>";
                                            }
                                            else if(trim($error) =='Please set default warehouse in settings')
                                            {
                                                echo $error."<span style='margin-left: 100px;'><a target='_blank' href='".base_url('warehouse')."'>Add Warehouse</a></span>";
                                            }
                                            else if(trim($error) =='Please complete your company profile')
                                            {
                                                echo $error."<span style='margin-left: 150px;'><a target='_blank' href='".base_url('profile')."'>Add KYC</a></span>";
                                            }
                                            else{
                                                if(!empty($error))
                                                    echo  $error;
                                                else 
                                                    echo 'Not Serviceable';
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        <?php
                        }
                        ?>
                    </p>
                    <div class="clearfix"></div>
                </div>
                <?php
                    if (!empty($couriers)) {
                    ?>
                    <div class="col-sm-12 text-right">
                        <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                <?php
                }?>
            </div>
        </form>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="<?php echo base_url();?>assets/build/assets/plugins/notify/js/jquery.growl.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script type="text/javascript">
      //for getting the filter checkbox data
      $(".filter_by").click(function(){
        $('input[name="radio1"]').on('change', function() {
   
              $('input[name="radio1"]').not(this).prop('checked', false);
              $('input[name="radio1"]').parent().removeClass('active');
              });
  
              $('input[name="radio2"]').on('change', function() {
                 
              $('input[name="radio2"]').not(this).prop('checked', false);
              $('input[name="radio2"]').parent().removeClass('active');
              });
  
             let timeout;
             timeout =  setTimeout(getfiltervalue, 100);
      });
     function getfiltervalue()
     {
         var favorite = [];
         var favorite2 = [];
         var pref = [];
         var length1=$("input[name='radio1']:checked").length;
         if(length1=='0')
         {
            $('input[name="radio1"]').parent().removeClass('active');
         }
         var length2=$("input[name='radio2']:checked").length;
         if(length2=='0')
         {
            $('input[name="radio2"]').parent().removeClass('active');
         }

            $.each($("input[name='radio1']:checked"), function() {

                if ($(this).val() === 'air' || $(this).val() === 'surface') {
                    favorite.push($(this).val());
                }
                $(".couriourval").hide();
            });
             $.each($("input[name='radio2']:checked"), function()
             {  
               
 
                if($(this).val()=='500' || $(this).val()=='1000' || $(this).val()=='2000' ||  $(this).val() =="20000" )
                     {
                       if($(this).val() =="20000")
                       {
                         favorite2.push("5000");
                         favorite2.push("10000");
                         favorite2.push("20000");
                       }
                       else{
                         favorite2.push($(this).val());
                       }
                     }
  
                   $(".couriourval").hide();
             }); 
  
             var preffered_type=$("input[name='radio']:checked").length;
             var length=$("input[name='radio1']:checked").length;
             if ( length> 0 || length2 > 0)
             {
                             var prefered=""+pref.join(","); 
                             var check_weight=""+favorite2.join(",");
                             var type_val="" + favorite.join(",");
 
                             if(prefered==='.'  ) { prefered=''; }
                             if( check_weight==='.' ) { check_weight=''; }
                             if(  type_val==='.' ){  type_val='';  }
                         
                             //array explode on brlh of commma
                             var weightarray = check_weight.split(',');
                             var prefred = prefered.split(',');
                             var type = type_val.split(',');
 
                             // make which div need to show aoording to the selected filter checkbox
 
                             // for make weight data (0.5 kg,1kg,2 kg,20kg,Heavy)
                             $.each(weightarray, function(index, value) { 
                                         
                                         // for make prefed data(is preferred or not)
                                         $.each(prefred, function(key1, value1) { 
 
                                                 // for make the type data (air ,surface)
                                                     $.each(type, function(typekey, typevalue1) { 
 
                                                         if(value1=="") { classvalue11=""; } else{  classvalue11=".";  }
                                                         if(typevalue1=="") { typevalue11=''; } else{ typevalue11=".";  }
                                                         if(value=="")  { classvalue="";  }  else{ classvalue=".";  }
 
                                                         //for show selected filter option
                                                         $(classvalue11+value1 +typevalue11+typevalue1 + classvalue+value).show();
                                                     
                                                     });
                                         });
                                 });
             }// if end for selected checkbox
             else
             {
                 $(".couriourval").hide();//1st hide all the data in div
                 $(".couriourval").show();  //show all the data 
             }
  
     } // end get filter values
    $(".ship_form").submit(function(event) {
        event.preventDefault();
        document.getElementById("global-loader").style.display = "";
        $.ajax({
            url: '<?php echo base_url('orders/ship');?>',
            type: "POST",
            data: $(this).serialize(),
            cache: false,
            success: function(data) {
                console.log(data);
                if (data.success)
                    location.reload();
                else if (data.error)
                    alert(data.error);

                document.getElementById("global-loader").style.display = "none";
            }
        });
    });

    $('#select_warehouse_dropdown').on('change', function() {
        var order_id = $(this).attr('data-order-id');
        var warehouse_id = $(this).val();
        $.ajax({
            url: '<?php echo base_url('orders/get_delivery_info/' );?>'+ order_id + '/' + warehouse_id,
            type: "GET",
            datatype: "JSON",
            cache: false,
            success: function(data) {
                $('#fulfillment_info').html(data);
            }
        });
    });
</script>
<!-- Put this at the bottom of your page or modal, AFTER Bootstrap and jQuery -->
<script>
  $(document).ready(function () {
    $('#collapseOneCouriers').on('show.bs.collapse', function () {
      $('#collapseGoCouriers').collapse('hide');
    });
    $('#collapseGoCouriers').on('show.bs.collapse', function () {
      $('#collapseOneCouriers').collapse('hide');
    });
  });
</script>

