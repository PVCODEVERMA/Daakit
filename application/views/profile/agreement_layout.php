<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">Company Aggrement Details</h4>
    <ol class="breadcrumb">
    <?php if(!empty($agreements1[0])){ 
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
				<option value="profile/legalentity">GST Details</option>
				<option value="profile/cmpaccountdetails">Bank A/C Details</option>
				<option value="profile/agreement" selected>Aggrement</option>
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
				<h3 class="card-title">Aggrement</h3>
			</div>
			<div class="card-body">
                <div class="table-responsive">
                    <table class="table card-table table-bordered table-vcenter text-dark table-outline text-nowrap">
                        <thead>
                            <tr>
                                <th><span class="bold">Section Name</span></th>
                                <th><span class="bold">Version</span></th>
                                <th width="20%"><span class="bold">Change Description</span></th>
                                <th><span class="bold">Doc Link</span></th>
                                <th><span class="bold">User Name</span></th>
                                <th><span class="bold">Acceptance Date</span></th>
                                <th><span class="bold">Published On</span></th>
                                <!-- <th><span class="bold">IP Address</span></th> -->
                                <th><span class="bold">Status</span></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php     
                        ///pr($agreements1[0],1);
                        if(!empty($agreements)) {
                            $a=0;
                            $show_more='';
                            foreach ($agreements as $agr) {
                                if( $agreements1!='0' &&  isset($agreements1[$a]->seller_id)  )
                                {
                                    $doc_link=$agreements1[$a]->agreement_url;
                                    
                                }
                                else
                                {
                                    if(!empty($agr->doc_link)) { $doc_link=$agr->doc_link ;} else { $doc_link= ''; }
                                    $show_more="display:none";
                                }
                                if($agr->change_description!='')
                                    {
                                        $show_more="display:block";
                                    }
                                    else
                                    {
                                        $show_more="display:none";
                                    }
                            ?>
                            <tr>
                                <td> <?=$agr->section_name ;?> </td>
                                <td><?=$agr->version ;?></td>
                                <td><small> <?= ($agr->change_description) ? substr($agr->change_description, 0, 150) : '' ;?><a class="text-primary fw-600" style="cursor: pointer;<?php echo $show_more;?>"   onclick="showmodal(<?=$agr->id;?>)">.....Show more</a></a></small></td>
                                <td><?php if(!empty($doc_link)) { $doc_link=$doc_link ;} else { $doc_link= base_url('assets/agreement-1.pdf'); } ?>    <a target="_blank" style="cursor: pointer;"  href="<?=$doc_link;?>">View PDF</a></td>
                                <td><?php  if( $agreements1!='0' && !empty($agreements1[$a]->seller_id)) { echo $agreements1[$a]->fname." ". $agreements1[$a]->lname;  } else { echo "-";};?></td>
                                <td><?php  if($agreements1!='0' && !empty($agreements1[$a]->acceptence_date) ) { echo date("d M, Y",strtotime($agreements1[$a]->acceptence_date )); }?></td>
                                <td><?php if(!empty($agr->publish_on) ) { echo date("d M, Y",strtotime($agr->publish_on ));}?></td>
                                <!-- <td><?php if($agreements1!='0' && !empty($agreements1[$a]->ip_address) ) { echo $agreements1[$a]->ip_address; };?></td> -->
                                <td>
                                    <?php if( $agreements1!='0' &&  isset($agreements1[$a]->seller_id)) {?>
                                        <button class="btn btn-outline-success btn-sm"    value='<?=$agr->id;?>'  >Accepted
                                        </button>                                                                    
                                    <?php } else { ?>
                                        <button class="btn btn-outline-success btn-sm accept_button"    value='<?=$agr->id;?>'  >Accept</button>
                                    <?php } ?>
                            </td>
                            </tr>
                        <?php $a++; } }else {
                                ?>
                                <tr>
                                    <td colspan="6" align="center">No entries found</td>
                                </tr>
                            <?php } ?>


                        </tbody>
                    </table>
                </div>
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
            $('#file_url').attr('src', n[3]);
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
            alert_float("Invalid Access");
            return false;
        }
    }
});
} 
else
    {
    alert_float('Invalid acess');
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
                alert_float(data.success,'notice');
                setInterval(function() {
                    location.reload();
                }, 1000);
            }
            else if (data.error){
                    alert_float(data.error);
            }
        }
    });
});

$(".accept_button").click(function(){

        $.ajax({
        url: '<?php echo base_url('analytics/acpt_agreement');?>',
        type: "POST",
        data: { update_id: this.value },
        datatype: "JSON",
        cache: false,
        success: function(data) {
            if (data.success){
                alert_float(data.success,'notice');
                setInterval(function() {
                    location.reload();
                }, 1000);            }
            else if (data.error){
                    alert_float(data.error);
            }
        }
    });
});
function hrefUrlLocation(path)
{
    var baseURL = '<?php echo base_url(); ?>';
    // Redirect to a specific path
    window.location.href = baseURL + path;
}
</script>