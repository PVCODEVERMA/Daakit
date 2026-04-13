<script>
    $(".ship_form").submit(function(event) {
        event.preventDefault();
        $.ajax({
            url: 'orders/ship',
            type: "POST",
            data: $(this).serialize(),
            cache: false,
            success: function(data) {
                if (data.success)
                    location.reload();
                else if (data.error)
                    alert(data.error);
            }
        });
    });

    $('#select_warehouse_dropdown').on('change', function() {
        var order_id = $(this).attr('data-order-id');
        var warehouse_id = $(this).val();
        $.ajax({
            url: 'orders/get_delivery_info/' + order_id + '/' + warehouse_id,
            type: "GET",
            datatype: "JSON",
            cache: false,
            success: function(data) {
                $('#fulfillment_info').html(data);
            }
        });
    });
</script>
<form class="ship_form" method="post">
    <input type="hidden" name="order_id" value="<?= !empty($order->id) ? $order->id : ''; ?>">
    <div class="modal-header">
        <h5 class="modal-title" id="mySmallModalLabel">Ship Your Package Now</h5>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-sm-6">
                <?php if (!empty($warehouses)) { ?>
                <div class="form-group">
                    <label for="inputPassword" class="text-right col-form-label">Pickup Warehouse</label>
                    <div>
                        <select class="form-control" name="warehouse_id" data-order-id="<?= !empty($order->id) ? $order->id : ''; ?>" id="select_warehouse_dropdown">
                            <?php foreach ($warehouses as $warehouse) { ?>
                            <option <?php if ($warehouse->id == $selected_warehouse) { ?> selected="" <?php } ?> value="<?= $warehouse->id; ?>"><?= ucwords($warehouse->name); ?><?php if ($warehouse->is_default) { ?> (Default) <?php } ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <?php } ?>
            </div>
            <div class="col-sm-6">
                <?php if (!empty($warehouses)) { ?>
                <div class="form-group">
                    <label for="inputPassword" class="text-right col-form-label">RTO Warehouse</label>
                    <div>
                        <select class="form-control" name="rto_warehouse_id">
                            <?php foreach ($warehouses as $warehouse) { ?>
                            <option <?php if ($warehouse->id == $selected_warehouse) { ?> selected="" <?php } ?> value="<?= $warehouse->id; ?>"><?= ucwords($warehouse->name); ?><?php if ($warehouse->is_default) { ?> (Default) <?php } ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
        <div class="row border-top p-t-10  m-b-10">
                <div class="col-sm-12">
                   
                <div class="input-group">
                         <div class="input-group-prepend">
                             <span class="input-group-text  border-secondary"> <i class="mdi mdi-filter"></i> Filter By</span>
                         </div>
                         <div class="input-group-append btn-group-toggle" data-toggle="buttons">
                             <button type="button" class="btn btn-outline-secondary text-dark filter_by"><img src="/assets/favicon.png" width="20px" height="20px">
                                 <input type="checkbox" name="radio" value='prefered'  class="filter" >
                                 Preferred</button>
                             <button type="button" class="btn btn-outline-secondary text-dark filter_by"><i class="mdi mdi-airplane"></i>
                                 <input type="checkbox" name="radio1"  value='air'  class="filter"  >
                                 Air</button>
                             <button type="button" class="btn btn-outline-secondary text-dark filter_by ">
                                 <i class="mdi mdi-truck-fast"></i>
                                 <input type="checkbox" name="radio1"  value='surface'  class="filter" > Surface
                             </button>
 
                             <button type="button" class="btn btn-outline-secondary text-dark filter_by">
                                 <i class="mdi mdi-weight-kilogram"></i>
                                 <input type="checkbox" name="radio2"  value='500'  class="filter"> 0.5 KG
                             </button>
                             <button type="button" class="btn btn-outline-secondary bulk-cancel-order-button text-dark filter_by">
                                 <i class="mdi mdi-weight-kilogram"></i>
                                 <input type="checkbox" name="radio2"  value='1000'  class="filter"> 1 KG
                             </button>
                             <button type="button" class="btn btn-outline-secondary bulk-cancel-order-button text-dark filter_by">
                                 <i class="mdi mdi-weight-kilogram"></i>
                                 <input type="checkbox" name="radio2"  value='2000'  class="filter" > 2 KG
                             </button>
                             <button type="button" class="btn btn-outline-secondary bulk-cancel-order-button text-dark filter_by">
                                 <i class="mdi mdi-truck-delivery"></i>
                                 <input type="checkbox" name="radio2"  value='20000'  class="filter" name="radio1"> Heavy
                             </button>

                        </div>

                    </div>

                </div>
            </div>
       
        <div class="row">
            <?php
            //echo pr($couriers);
            foreach ($couriers as $courier) {
                $custom_plan = explode("_", $courier->courier);

                $courier_type = ucwords($custom_plan[0]);
                $courier_weight = $custom_plan[1];
                
                
            ?>
            <div class="col-sm-6 couriourval  <?php  if(isset($courier_type) and $courier_type!='') { echo strtolower($courier_type); } else { echo "";}  ;?>   <?php  if(isset($courier_weight) and $courier_weight!='') { echo $courier_weight; } else { echo "";}   if(isset($courier->prefered) && $courier->prefered=='1' )  { echo " prefered";} else { echo " notprefered";}  ?> "  data-target="<?php  if(isset($courier->courier_type) and $courier->courier_type!='') { echo $courier->courier_type; } else { echo "-";}  ;?> ">
          
                <div class="alert alert-success fade show" role="alert">
                    <div class="custom-control custom-radio">
                        <input type="radio" required="" id="customRadio<?= ucwords($courier->courier); ?>" name="courier_id" class="custom-control-input" value="<?= strtolower($courier->courier); ?>">
                        <label class="custom-control-label" for="customRadio<?= ucwords($courier->courier); ?>"><?= ucwords($courier_type) . ' ' . round($courier_weight/1000, 2) . 'kg' . (!empty($courier->charges) ? ' (&#8377;' . $courier->charges . ')' : ''); ?></label>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
        <div class="row border-bottom m-b-10">
        <div class="col-sm-6">
                    <div class="alert alert-info fade show" role="alert">
                        <div class="custom-control custom-checkbox custom-control-inline">
                            <input type="checkbox" name="dg_order" class="custom-control-input" id="dg_order" value="1">
                            <label class="custom-control-label" for="dg_order">Is dangerous good ?</label>
                        </div>
                    </div>
                </div>
            <?php if(!empty($secure_shipment)){?>
                <div class="col-sm-6">
                    <div class="alert alert-info fade show" role="alert">
                        <div class="custom-control custom-checkbox custom-control-inline">
                            <input type="checkbox" name="is_insurance" class="custom-control-input" id="is_insurance" value="1">
                            <label class="custom-control-label" for="is_insurance">Opt-in for shipment insurance ?</label>
                        </div>
                    </div>
                </div> 
                <?php }?>
        </div>
    </div>
    <div class="modal-footer">
        <?php if (!empty($couriers)) { ?><button type="submit" class="btn btn-primary">Ship</button><?php } ?>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    </div>
</form>

<script>
//for getting the filter checkbox data
$(".filter_by").click(function(){

    $('input[name="radio"]').on('change', function() {
              //$('input[name="radio"]').not(this).prop('checked', false);
              //$('input[name="radio"]').parent().removeClass('active');
              });

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

         $.each($("input[name='radio']:checked"), function()
         {
            if($(this).val()=='prefered'  )
                     {
                       pref.push($(this).val());
                     }
                     $(".couriourval").hide();

        });
  


         $.each($("input[name='radio1']:checked"), function()
         {
           
  
                 if($(this).val()=='air' || $(this).val()=='surface'  )
                     {
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
             if ( length> 0 || preffered_type > 0 || length2 > 0)
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

     </script>