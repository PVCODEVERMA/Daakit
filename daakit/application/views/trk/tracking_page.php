<?php 
$order_info = $shipment->order;
$shipment_info = $shipment->shipment;
$courier = $shipment->courier;
$company = $shipment->company;
$user = $shipment->user;
$tracking = $shipment->tracking;
$awb_number = $shipment->awb_number;
$additional_tracking = !empty($shipment_info->additional_tracking_info) ? json_decode($shipment_info->additional_tracking_info) : "";
$banners = isset($shipment->user_tracking_setting['banners'])?unserialize($shipment->user_tracking_setting['banners']):"";
$menus = isset($shipment->user_tracking_setting['menu_links'])?unserialize($shipment->user_tracking_setting['menu_links']):"";
if(isset($return_setting['encrpt_shipment_id'])){
  $return_link = base_url()."return/setting?sid=".urlencode($return_setting['encrpt_shipment_id']);
//echo $return_link;
}

//echo "<pre>";print_r();exit;
?>
<style>
body{
    min-height: 50vh;
    font-family: 'Exo 2';
    font-size: 14px;   
    color: #fff;
    background:#eee;	
}
/* .progress-track {
    background-color: #42469d;
    border-color: #42469d;
} */
@media (min-width: 576px){
.modal-dialog {
    max-width: 750px;
    margin: 1.75rem auto;
}
}

#progressbar {
    margin-bottom: 3vh;
    overflow: hidden;
    color: white;
    padding-left: 0px;
    margin-top: 3vh
}

#progressbar li {
    list-style-type: none;
    font-size: 0.8rem;
    width: 20%;
    float: left;
    position: relative;
    font-weight: 400;
    color: #000000;
}

#progressbar #step1:before {
    content: "";
    color: white;
    width: 20px;
    height: 20px;
    margin-left: 0px !important;
}

#progressbar #step2:before {
    content: "";
    color: #fff;
    width: 20px;
    height: 20px;
    margin-left: 30%;
}

#progressbar #step3:before {
    content: "";
    color: #fff;
    width: 20px;
    height: 20px;
    margin-right: 30% ; 
}

#progressbar #step4:before {
    content: "";
    color: #fff;
    width: 20px;
    height: 20px;
    margin-right: 30% ; 
}

#progressbar #step5:before {
    content: "";
    color: rgb(151, 149, 149, 0.651);
    width: 20px;
    height: 20px;
    margin-right: 0px !important;
}

#progressbar li:before {
    line-height: 29px;
    display: block;
    font-size: 12px;
    background: rgb(151, 149, 149);
    border-radius: 50%;
    margin: auto;
    z-index: -1;
    margin-bottom: 1vh;
}

#progressbar li:after {
    content: '';
    height: 3px;
    background: rgb(151, 149, 149, 0.651);
    position: absolute;
    left: 0%;
    right: 0%;
    margin-bottom: 2vh;
    top: 8px;
    z-index: 1;
}
.progress-track{
    padding: 0 8%;
    margin-top: 50px;
}

#header{
  text-align: center;
  background: #554DC0;
  color: #fff;
  border-radius: 5px;
}
#progressbar li:nth-child(2):after {
    margin-right: auto;
}

#progressbar li:nth-child(1):after {
    margin: auto;
}

#progressbar li:nth-child(3):after {
    float: left;
    width: 68%;
}
#progressbar li:nth-child(4):after {
    margin-left: auto;
    width: 130%;
}

#progressbar li.active:before,
#progressbar li.active:after {
    background: #554DC0;
}

#three{
    font-size: 1.2rem;
}
@media (max-width: 767px){
    #three{
        font-size: 1rem;
    } 
}
</style>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">
  <title>AWB Tracking</title>
  <link rel="shortcut icon" id="favicon" href="https://daakit.com/assets/images/daakit/ico.png">
<link rel="apple-touch-icon”" id="favicon-apple-touch-icon" href="<?php  echo  base_url();?>assets/build/assets/iconfonts/icons.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" id="reset-css" href="<?php  echo  base_url();?>assets/build/assets/plugins/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" id="bootstrap-css" href="<?php  echo  base_url();?>assets/build/assets/app-6d59ac94.css">
<link rel="stylesheet" type="text/css" id="roboto-css" href="<?php  echo  base_url();?>assets/build/assets/app-d0aacae3.css">
</head>
<body class="login_admin">
	<div class="container" style="margin-top:50px">
    <div class="main-container container-fluid">
      <!-- START ROW-1 -->
      <div class="row">
          <div class="col-md-12">
              <div class="card overflow-hidden">
                  <div class="card-body p-0">
                      <div class="d-lg-flex">
                          <div class="border-end border-bottom bd-lg-b-0 d-flex flex-column mn-wd-20p">
                              <ul class="nav nav-pills main-nav-column p-3" style="height: 60vh;">
                                  <li class="nav-item"><a class="nav-link active" href="javascript:void(0)"><i class="fa fa-shopping-cart" aria-hidden="true"></i>Order details (<?=  isset($shipment_info->ship_status)?ucfirst($shipment_info->ship_status):"Booked";?>)</a></li>
                                  <li class="nav-item"><a class="nav-link" href="javascript:void(0)"><span>ORDER PLACED ON :&nbsp;</span> <?= isset($order_info->order_date)?date('M d, Y H:i', $order_info->order_date):"" ?></a></li>
                                  <li class="nav-item"><a class="nav-link" href="javascript:void(0)"><span>COURIER :&nbsp;</span>  <?= isset($courier->display_name)?$courier->display_name:"" ?></a></li>
                                  <li class="nav-item"><a class="nav-link" href="javascript:void(0)"><span>ORDER ID :&nbsp;</span>  <?= isset($order_info->order_no)?$order_info->order_no:"" ?></a></li>
                              </ul>
                          </div>
                          <div class="flex-grow-1">
                              <div class="tab-content">
                                  <div class="tab-pane active" id="general">
                                      <div class="p-4 border-bottom" id="header" style="text-align: center;">
                                          <h5 class="mb-0">AWB Number - <?= isset($awb_number)?$awb_number:"" ?></h5>
                                      </div>
                                        <div class="row">
                                          <div class="col-md-12">
                                          <div class="progress-track">
                                              <ul id="progressbar">
                                              <?php 
                                                  $status_code = isset($shipment_info->ship_status)?$shipment_info->ship_status:"";
                                                  $status_code  = str_replace(" ","_",$status_code);
                                                  $ship_status_array = array("booked","pending_pickup","in_transit","out_for_delivery","delivered");
                                                  if($status_code=='cancelled'){
                                                    $ship_status_array = array("cancelled","pending_pickup","in_transit","out_for_delivery","delivered");
                                                  }
                                                  if($status_code=='rto'){
                                                    $ship_status_array = array("booked","pending_pickup","in_transit","out_for_delivery","rto");
                                                  }

                                                  if($status_code=='exception'){
                                                    $ship_status_array = array("booked","pending_pickup","in_transit","out_for_delivery","exception","delivered");
                                                  }
                                                  $result_key = array_search($status_code, $ship_status_array);
                                                  if($result_key==""){
                                                    $result_key = 0;
                                                  }
                                            
                                              $status_array = array();

                                                foreach($ship_status_array as $key=> $value){
                                                    if($key<=$result_key){
                                                      $status_array[$value] = array("class"=>"step_active","icon"=>"check");
                                                    }else if($key==$result_key){
                                                      $status_array[$value] = array("class"=>"active","icon"=>"truck"); 
                                                    }else{
                                                      $status_array[$value] = array("class"=>"text-center","icon"=>"none");
                                                    }
                                                }
                                                //pr($status_array);
                                                if($status_code=='rto'){
                                                    if($status_array['rto']['class'] == 'step_active'){
                                                      $status_array['rto']['class']='active';
                                                    }else{
                                                      $status_array['rto']['class']='step_last';
                                                    }
                                                }else{
                                                  if($status_array['delivered']['class'] == 'step_active'){
                                                      $status_array['delivered']['class']='active';
                                                  }else{
                                                    $status_array['delivered']['class']='step_last';
                                                  }
                                                }  
                                              
                                              //pr($status_array,1);
                                              if(!empty($status_array)){
                                                $sn=1;
                                                foreach($status_array as $status => $status_class){
                                                  $status = str_replace("_"," ",$status);   
                                                  $icon = (isset($status_class['class'])&& ($status_class['class']!='none'))?$status_class['icon']:"";
                                                  if(!empty($icon)){
                                                    $icon = "<i class='fa fa-".$status_class['icon']."'></i>";
                                                  }else{
                                                    $icon = "";
                                                  }
                                                  $status_class = str_replace("_"," ",$status_class['class']);
                                                  if($status_class=='active'){
                                                    $status_class='text-right active';
                                                  }
                                                  else if($sn == '5'){
                                                    $status_class='text-right';
                                                  }
                                                  echo "<li class='step0 ".$status_class."' id='step".$sn."'>".(($status_class=='active') ? '<span id="three">'.ucwords($status).'</span>' : ucwords($status))."</li>";   
                                                  $sn++;
                                                }
                                              }
                                            ?>
                                                  <!-- <li class="step0 active" id="step1">Order placed</li>
                                                  <li class="step0 active text-center" id="step2">In Transit</li>
                                                  <li class="step0 active text-center" id="step2">In Transit</li>
                                                  <li class="step0 active text-right" id="step3"><span id="three">Out for Delivery</span></li>
                                                  <li class="step0 text-right" id="step4">Delivered</li> -->
                                              </ul>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <div class="card-body">
                            <div class="panel-group" id="accordion1">
                              <div class="panel panel-default mb-4">
                                <div class="panel-heading">
                                  <h4 class="panel-title">
                                    <a class="accordion-toggle" data-bs-toggle="collapse" data-parent="#accordion" href="#collapseFour" aria-expanded="true">Tracking History</a>
                                  </h4>
                                </div>
                                <div id="collapseFour" class="panel-collapse collapse show" role="tabpanel" aria-expanded="false" style="">                                  
                                  <div class="panel-body" style="overflow-y: scroll;height: 450px;">
                                      <div class="timeline">
                                          <?php
                                          if (!empty($tracking)) {
                                            $sn=0;
                                              foreach ($tracking as $track) {
                                          ?>
                                                  <div class="timeline-item">
                                                      <div class="timeline-wrapper">
                                                          <div class="">
                                                              <div class="avatar avatar-sm">
                                                                  <div class="avatar-title bg-info rounded-circle">
                                                                      <?php if ($courier->courier_type == 'air') { ?>
                                                                          <i class="mdi mdi-airplane"></i>
                                                                      <?php } else { ?>
                                                                          <i class="mdi mdi-truck"></i>
                                                                      <?php } ?>
                                                                  </div>
                                                              </div>
                                                          </div>

                                                          <div class="col-auto">
                                                              <div style=" float: inline-start;"><i class="fa fa-dot-circle-o" style="color: green;font-size: x-large;" aria-hidden="true"></i></div>
                                                              <div style="margin-left: 30px;">
                                                                  <h6 class="m-0">Scan - <?php echo count($tracking)-$sn++;?> : <?= !empty($track->view_status) ? $track->view_status : $track->status; ?></h6>
                                                                  <h6 class="m-0"><i class="mdi mdi-map-marker"></i> <?= $track->location; ?></h6>
                                                                  <p class="m-0"> <?= $track->message; ?></p>
                                                                  <?php if ($this->agent->is_mobile()) { ?>
                                                                      <p class="m-0"> <?= date('M d, Y H:i', $track->event_time); ?></p>
                                                                  <?php } ?>
                                                              <?php if (!$this->agent->is_mobile()) { ?>
                                                                  <div class="ml-auto col-auto text-muted"><?= date('M d, Y H:i', $track->event_time); ?></div>
                                                              <?php } ?>
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                              <?php
                                              }
                                          } else {
                                              ?>
                                              <div class="timeline-item">
                                                  <div class="timeline-wrapper text-danger">
                                                      <p>We are unable to retrieve tracking history right now. Please check again shortly.</p>
                                                  </div>
                                              </div>
                                          <?php } ?>
                                      </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>

                      </div>
                  </div>
              </div>
          </div>
          <div class="row">
              <div class="col-md-12">
                <footer class="footer">
                    <div class="container">
                      <div class="row align-items-center flex-row-reverse">
                        <div class="col-md-12 col-sm-12 text-center">
                          <a href="https://daakit.com/">Powered by : Daakit Technologies Pvt. ltd</a>
                        </div>
                      </div>
                    </div>
                  </footer>
                </div>
          </div>
      </div>
      <!-- END ROW-1 -->
  </div>
</div> 
</body>
</html>


