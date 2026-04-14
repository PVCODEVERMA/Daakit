<style>
        .scroll_area {
            max-height: 60vh;
            overflow: auto;
            padding: 0px 15px;
        }
        
        .scroll_area::-webkit-scrollbar {
            width: 8px;
        }
        
        .scroll_area::-webkit-scrollbar-track {
            background-color: #E7E7E7
        }
        
        .scroll_area::-webkit-scrollbar-thumb {
            background-color: rgba(137, 129, 250, 0.69);
            border-radius: 10px;
        }
        
        .scroll_area::-webkit-scrollbar-thumb:hover {
            background-color: #000;
        }
        
        p.right-text small {
            font-size: 92%;
        }
        
        .listing {
            font-size: 80%;
            font-weight: 400;
        }
        
        .listing li {
            line-height: 22px;
        }
    </style>

   
<section class="admin-content">
            <div class="bg-dark">
                <div class="container  m-b-30">
                    <div class="row">
                        <div class="col-12 text-white p-t-20 p-b-40" style="height:120px;"> </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid  pull-up">


                <div class="row">
                    <div class="col-md-12">
                        <div class="card m-b-30">
                            <div class="card-header">
                                <h5 class="m-b-0">
                                    <i class="mdi mdi-checkbox-intermediate"></i> Agreement
                                </h5>

                            </div>
                            <div class="card-body">
                                <div class="table-responsive">

                                    <table class="table table-sm table-hover ">
                                        <thead>
                                            <tr>
                                                <th>Section Name</th>
                                                <th>Version</th>
                                                <th width="20%">Change Description</th>
                                                <th>Doc Link</th>
                                                <th>User Name</th>
                                                <th>Acceptance Date</th>
                                                <th>Published On</th>
                                                <th>IP Address</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        
                                         <?php     
                                       //pr($agreements1);
                                         if(!empty($agreements)) {
                                             $a=0;
                                         foreach ($agreements as $agr) {
                                          
                                         ?>
                                            <tr>
                                                <td> <?=$agr->section_name ;?> </td>
                                                <td><?=$agr->version ;?></td>
                                                <td><small> <?=substr($agr->change_description, 0, 150)."....";?><a class="text-primary fw-600" style="cursor: pointer;"   onclick="showmodal(<?=$agr->id;?>)">Show more</a></a></small></td>
                                                <td><?php if(!empty($agr->doc_link)) { $doc_link=$agr->doc_link ;} else { $doc_link= ''; } ?>    <a target="_blank" style="cursor: pointer;"  href="<?=$doc_link;?>">View PDF</a></td>
                                                <td><?php  if( $agreements1!='0' && !empty($agreements1[$a]->seller_id)) { echo $agreements1[$a]->fname." ". $agreements1[$a]->lname;  } else { echo "-";};?></td>
                                                <td><?php  if($agreements1!='0' && !empty($agreements1[$a]->acceptence_date) ) { echo date("d M, Y",strtotime($agreements1[$a]->acceptence_date )); }?></td>
                                                <td><?php if(!empty($agr->publish_on) ) { echo date("d M, Y",strtotime($agr->publish_on ));}?></td>
                                                <td><?php if($agreements1!='0' && !empty($agreements1[$a]->ip_address) ) { echo $agreements1[$a]->ip_address; };?></td>
                                                <td>
                                                    <?php if( $agreements1!='0' &&  isset($agreements1[$a]->seller_id)) {?>
                                                        <span class="text-success fw-600">Accepted</span>
                                                    
                                                    <?php } else { ?>
                                                        <button class="btn btn-info btn-sm accept_button"    value='<?=$agr->id;?>'  >Accept</button>
                                                    <?php } ?>
                                            </td>
                                            </tr>
                                           <?php $a++; } }else {
                                                ?>
                                                <tr>
                                                    <td colspan="6" align="center">No records found</td>
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
        </section>

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
                                        <!--<li>Clause 2.11: It has been clarified that Shiprocket shall have the right to adjust the COD amounts and dispose of the shipments of the User, in case of any outstanding amounts/charges.</li>
                                        <li>Clause 2.12: It has been clarified that Shiprocket shall have the right to adjust the COD amounts and dispose of the shipments of the User, in case of any outstanding amounts/charges.</li>
                                        <li>Clause 2.13: It has been clarified that Shiprocket shall have the right to adjust the COD amounts and dispose of the shipments of the User, in case of any outstanding amounts/charges.</li>-->
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
        function showmodal(id)
        {
            if(id!=''){
                var aggrement_id = id;
                $.ajax({
                url: 'analytics/markagreement',
                type: "POST",
                data: { aggrement_id: aggrement_id },
                datatype: "JSON",
                cache: false,
                success: function(data) {
                if(data!='error'){
                    
                    var n = data.split('||');
                    $('#section_name').html(n[1]); 
                    $('#file_url').attr('src', "");
                    $('.listing').html(n[5]);
                    $('#version').html(n[2]);
                    $('#agrementdate').html(n[4]); 
                    $('#update_id').val(n[0]); 
                    if(n[6]!='')
                    {
                        $("#close").show(); 
                        $("#skip").hide(); 
                        $("#i_accept").hide();
                    }
                    else
                    {
                        $("#close").hide(); 
                        $("#skip").show(); 
                        $("#skip").show();
                        $("#i_accept").show();
                    }
                    $('#exampleModal').modal('show');
                }
                else
                {
                    alert("Invalid Access");
                    return false;
                }
            }
        });
        } 
        else
         {
            alert('Invalid acess');
            return false;
         }

        }


        $(".accept_agreement").click(function(){
           var update_id= $('#update_id').val();
            $.ajax({
                url: 'analytics/acpt_agreement',
                type: "POST",
                data: { update_id: update_id },
                datatype: "JSON",
                cache: false,
                success: function(data) {
                    if (data.success){
                          alert(data.success);
                          location.reload();
                    }
                    else if (data.error){
                          alert(data.error);
                    }
                }
            });
        });

        $(".accept_button").click(function(){

                $.ajax({
                url: 'analytics/acpt_agreement',
                type: "POST",
                data: { update_id: this.value },
                datatype: "JSON",
                cache: false,
                success: function(data) {
                    if (data.success){
                          alert(data.success);
                          location.reload();
                    }
                    else if (data.error){
                          alert(data.error);
                    }
                }
            });
        });
    </script>