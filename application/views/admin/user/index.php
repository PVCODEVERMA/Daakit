<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">Users summary</h4>
    <ol class="breadcrumb d-flex flex-wrap">
        <li class="breadcrumb-item btn-list">
			<?php if (in_array('users_export', $user_details->permissions)) { ?>
				<a href="<?= base_url('admin/users/exportCSV'); ?><?php if (!empty($filter)) {
					echo "?" . http_build_query($_POST);
				} ?>" class="btn btn-info btn-sm me-2"><i class="fa fa-download" aria-hidden="true"></i> Retrieve Export </a>
			<?php } ?>
			<a href="javascript:void(0);" class="btn btn-info btn-sm me-2" data-bs-toggle="sidebar-right" data-bs-target=".sidebar-right"><i class="fa fa-search" aria-hidden="true"></i> Filter </a>
        </li>
    </ol>
</div>
<!-- END PAGE-HEADER -->
 <!-- filter section start -->
 <div class="sidebar sidebar-right sidebar-animate">
	<div class="p-4">
		<a href="#" class="float-end sidebar-close" data-bs-toggle="sidebar-right" data-bs-target=".sidebar-right"><i class="fa fa-times"></i></a>
	</div>
    <br>
	<div class="panel-body tabs-menu-body side-tab-body p-0 border-0 ">
		<div class="tab-content border-top">
			<div class="tab-pane active" id="tab1">
				<div class="chat">
					<div class="contacts_card">
						<div class="input-group p-3">
							<form method="POST" action="<?= base_url('admin/users/all') ?>" id="searchSeller">
								<?php $status = !empty($filter['status']) ? $filter['status'] : ''; ?>
								<input type="hidden" name="filter[status]" id="status" value="<?php echo $status;?>" />
								<div class="row">
									<div class="col-sm-12">
										<div class="form-row">
											<div class="col-md-6">
												<div class="form-group" app-field-wrapper="from_date">
													<label for="from_date" class="control-label">From Date</label>
													<input type="date"  name="filter[start_date]" value="<?= !empty($filter['start_date']) ? $filter['start_date'] : '' ?>" class="form-control fc-datepicker"  autocomplete="off">
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group" app-field-wrapper="to_date">
													<label for="to_date" class="control-label">To Date</label>
													<input type="date" id="to_date" name="filter[end_date]" class="form-control" value="<?= !empty($filter['end_date']) ? $filter['end_date'] : '' ?>" autocomplete="off">
												</div>
											</div>
											<?php if (empty($account_level)) { ?>
												<div class="form-group col-sm-12" style="margin-top:2px;">
													<label class="font-secondary">Seller Id </label>
													<input type="text" autocomplete="off" name="filter[seller_ID]" value="<?= !empty($filter['seller_ID']) ? $filter['seller_ID'] : '' ?>" class="form-control" placeholder="Enter Seller ID With Comma">
												</div>
											<?php } ?>
											<div class="form-group col-sm-12" style="margin-top:2px;">
												<label for="email">Email:</label>
												<input type="text" autocomplete="off" name="filter[email]" value="<?= !empty($filter['email']) ? $filter['email'] : '' ?>" class="form-control" placeholder="Email">
											</div>
											<div class="form-group col-sm-12" style="margin-top:2px;">
												<label for="email">Phone:</label>
												<input type="text" autocomplete="off" name="filter[phone]" value="<?= !empty($filter['phone']) ? $filter['phone'] : '' ?>" class="form-control" placeholder="Phone Number">
											</div>
											<div class="form-group col-sm-12" style="margin-top:2px;">
												<label for="email">Seller Category:</label>
												<?php
												$support_categories = $this->config->item('seller_categories');
												foreach ($support_categories as $sc) {
													$esc_status[strtolower($sc)] = strtoupper($sc);
												}

												$js = "class='form-control js-select2' multiple style='width: 100% !important;' ";
												echo form_dropdown('filter[support_category][]', $esc_status, !empty($filter['support_category']) ? $filter['support_category'] : '', $js);
												?>
											</div>
											<div class="form-group col-sm-12" style="margin-top:2px;">
												<label for="email">Seller Cluster:</label>
												<select name="filter[seller_cluster]" class="form-control">
													<option value="">Select</option>
													<?php $support_clusters = $this->config->item('seller_clusters');
													foreach ($support_clusters as $sc_key =>  $sc) {
													?>
														<option disabled>-----<?= strtoupper($sc_key) ?>-----</option>
														<?php
														foreach ($sc as $sc_child_key => $sc_child_value) {
														?>
															<option <?php if ((!empty($filter['seller_cluster']) && strtolower($filter['seller_cluster']) == strtolower($sc_child_key))) { ?> selected <?php } ?> value="<?= $sc_child_key ?>"><?= strtoupper($sc_child_value) ?></option>
													<?php
														}
													}
													?>
												</select>

											</div>
											<div class="form-group col-sm-6">
												<label for="email">Recharge Done:</label>
												<select name="filter[recharge_status]" class="form-control js-select2" style="width: 100% !important;">
													<?php
													$recharge_status = '';
													if (!empty($filter['recharge_status']))
														$recharge_status = $filter['recharge_status'];
													?>
													<option <?php if ($recharge_status == '') { ?> selected <?php } ?> value="">All
													</option>
													<option <?php if ($recharge_status == 'yes') { ?> selected <?php } ?> value="yes">Yes</option>
													<option <?php if ($recharge_status == 'no') { ?> selected <?php } ?> value="no">No</option>
												</select>
											</div>
											<div class="form-group col-sm-6">
												<label for="email">KYC Done:</label>
												<select name="filter[kyc_done]" class="form-control js-select2" style="width: 100% !important;">
													<?php
													$kyc_done = '';
													if (!empty($filter['kyc_done']))
														$kyc_done = $filter['kyc_done'];
													?>
													<option <?php if ($kyc_done == '') { ?> selected <?php } ?> value="">All
													</option>
													<option <?php if ($kyc_done == 'yes') { ?> selected <?php } ?> value="yes">Yes
													</option>
													<option <?php if ($kyc_done == 'no') { ?> selected <?php } ?> value="no">No
													</option>
													<option <?php if ($kyc_done == 'e_verified') { ?> selected <?php } ?> value="e_verified">E-Verified
													</option>

												</select>
											</div>
											<div class="col-sm-12" style="margin-top:20px;text-align:right">
												<button type="submit" class="btn btn-sm btn-success">Filter</button>
												<a href="<?= base_url('admin/users/all'); ?>" class="btn btn-primary btn-sm">Reset</a>                                
											</div>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
 <!-- filter section end-->
<div class="main-container container-fluid">
<!-- START ROW-1 -->
<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">Users <span style="font-size:13px">(<?= date('d-m-Y',strtotime($filter['start_date']));?> - <?= date('d-m-Y',strtotime($filter['end_date']));?>)</span>
			</div>
            <div class="card-body">
            <div id="responsive-datatable_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
			<div class="row">
                    <div class="col-sm-1 col-md-1">
                        <div class="dataTables_length" id="responsive-datatable_length">
                        <label>
                            <?php
                                $per_page_options = array(
                                    '10' => '10',
                                    '20' => '20',
                                    '50' => '50',
                                    '100' => '100',
                                    '200' => '200',
                                    '500' => '500',
                                );

                                $js = "class='form-select form-select-sm select2' onchange='per_page_records(this.value)'";
                                echo form_dropdown('per_page', $per_page_options, $limit, $js);
                                ?>
                        </label>
                        </div>
                    </div>
					<div class="col-sm-8 col-md-8  action_row_default">
						<a href="javascript:void(0)" class="btn btn-block btn-sm m-b-15 ml-2 mr-2 btn-sm <?= (isset($status) && $status=='') ? 'btn-sm btn-primary' : 'btn-sm btn-outline-primary'; ?>" onclick="filter_data_status('')">ALL</a>
						<a href="javascript:void(0)" class="btn btn-block btn-sm m-b-15 ml-2 mr-2 btn-sm <?= (isset($status) && $status=='active') ? 'btn-sm btn-primary' : 'btn-sm btn-outline-primary'; ?>" onclick="filter_data_status('active')">Active</a>
						<a href="javascript:void(0)" class="btn btn-block btn-sm m-b-15 ml-2 mr-2 btn-sm <?= (isset($status) && $status=='process') ? 'btn-sm btn-primary' : 'btn-sm btn-outline-primary'; ?>" onclick="filter_data_status('process')">Process</a>
						<a href="javascript:void(0)" class="btn btn-block btn-sm m-b-15 ml-2 mr-2 btn-sm <?= (isset($status) && $status=='junk') ? 'btn-sm btn-primary' : 'btn-sm btn-outline-primary'; ?>" onclick="filter_data_status('junk')">Junk Account</a>
					</div>
                    <div class="col-sm-6 col-md-6 action_row_selected"  style="display: none;">
                        <div class="dataTables_length" id="responsive-datatable_length">
						<?php if (in_array('users_verify', $user_details->permissions)) { ?>
							<a href="javascript:void(0)"  class="btn btn-block btn-sm m-b-15 ml-2 mr-2 btn-sm btn-outline-primary add_change_status">Change Status</a>
						<?php  } ?>
						</div>
                    </div>
                </div>
                <div class="table-responsive">
                        <table class="table table-bordered border-bottom" id="responsive-datatable">
                        <thead>
                            <tr>
								<th><span class="bold">
								<input data-switch="true" id="select_all_checkboxes" type="checkbox">
								</th>
								<th><span class="bold">ID</th>
								<th><span class="bold">Seller Details</th>
								<th style="width:10%;">Date</th>
								<th style="width:10%;">Verified Date</th>
								<th style="width:10%;">Verified Type</th>
								<th><span class="bold">Plan Details</th>
								<th style="width:100px"><span class="bold">Action</th>
                            </tr>
                        </thead>
						<tbody>
								<?php
								$user_info = $this->session->userdata('user_info');
								$user_id = $user_info->user_id;
								//pr($userlist); die;

								foreach ($userlist as $user) {
									$userStatus = '';
									if ($user->verified == '1') {
										$userStatus = 'Active';
									}
									if ($user->verified == '2') {
										$userStatus = 'InActive';
									}
									if ($user->verified == '0') {
										$userStatus = 'Process';
									}
									if ($user->parent_id != 0) {
										$userStatus = 'Seller Employee';
									}
								?>
									<tr id="<?php echo $user->id; ?>">
										<td>
											<?php //if ($user->verified == '1') { 
											?>
											<input type="checkbox" name="checkbox[]" id="checkbox[]" class="multiple_checkboxes" value="<?php echo $user->id; ?>"><?php //} 
																																									?>
										</td>
										<td><a href="<?php echo base_url('admin/users/viewuser/'); ?><?php echo $user->id; ?>?email= <?php echo !empty($filter['email']) ? $filter['email'] : '' ?>&phone= <?php echo !empty($filter['phone']) ? $filter['phone'] :'' ?>"><?php echo $user->id; ?></a></td>
										<td>
											<?php echo $user->fname . ' ' . $user->lname; ?><br>
											<a target="_blank;" href="<?php echo base_url('admin/users/viewuser/'); ?><?php echo $user->id; ?>" style="color: #004080;font-weight:bold;">
												<?php echo $user->company_name; ?>
											</a><br>

										</td>
										<td><?php echo date("j-M-Y", strtotime($user->created)); ?></td>
										<td>
											<?php if ($user->verified_date == "0") { ?>
											<?php } else { ?> <?php echo date("j-M-Y", $user->verified_date); ?>
												<br>
											<?php } ?>
										</td>
										<td>
											<small><?php


													if ($user->e_verified == "1" && $user->verified == "1") {
														echo "(E-Verified)";
													} else if ($user->e_verified == "0" && ($user->verified == "1" || $user->verified == "2")) {
														echo "(Manually)";
													} else {
													}
													?></small>
										</td>
										<td>
											<?php echo ucwords($user->pricing_plan); ?><br>
											<b>Balance:</b><?= $user->wallet_balance; ?><br>
											<b>Limit:</b><?= $user->wallet_limit; ?>
										</td>
										<td>
										 <?php 
										    
											$loginuserid=$userDatapermission->id;
											$permissionData=$userDatapermission->admin_permission_level;
											
											$sale_manager_id = $user->sale_manager_id;
											$international_sale_manager_id = $user->international_sale_manager_id;
											$b2b_sale_manager_id = $user->b2b_sale_manager_id;
											$training_manager_id = $user->training_manager_id;
											$account_manager_id = $user->account_manager_id;
											
											$is_login_user = ($loginuserid == $sale_manager_id) || ($loginuserid == $international_sale_manager_id) || ($loginuserid == $b2b_sale_manager_id) || ($loginuserid == $training_manager_id) || ($loginuserid == $account_manager_id);
											$is_login_user_notmatch = ($loginuserid != $sale_manager_id) && ($loginuserid != $international_sale_manager_id) && ($loginuserid != $b2b_sale_manager_id) && ($loginuserid != $training_manager_id) && ($loginuserid != $account_manager_id);

											
											 if($permissionData=='restricted' && $is_login_user)
											{
												
												if (in_array('users_login', $user_details->permissions)) {
													if (!$user->is_admin && !empty($user->login_token)) {
														?>
														<a href="<?php echo base_url('users/login_with_token/' . $user->login_token . '/?admin_id=' . $user_id); ?>" class="btn btn-primary btn-sm">Login</a>
														<?php
													}
												}
											}
											else if($permissionData=='restricted' && $is_login_user_notmatch)
											{

											}
											else
											{

										 ?>


											<?php if (in_array('users_login', $user_details->permissions)) { ?>
												<?php if (!$user->is_admin && !empty($user->login_token)) { ?>
													<a href="<?php echo base_url('users/login_with_token/' . $user->login_token . '/?admin_id=' . $user_id); ?>" class="btn btn-primary btn-sm">Login as in Incognito Mode</a>
											<?php }
											}  }
										  ?>
										</td>
									</tr>
								<?php
								}
								?>
							</tbody>
                        </table>
                </div>
                    <div class="row mt-4">
                        <div class="col-sm-12 col-md-6">
                            <div class="dataTables_info" id="example-multi_info" role="status" aria-live="polite">Showing <?= $offset + 1; ?> to <?= $offset + $limit; ?> of <?= $total_records; ?> entries</div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <div class="dataTables_paginate paging_simple_numbers" id="responsive-datatable_paginate">
                                <ul class="pagination mb-0" style="float: right;">
                                    <?php if (isset($pagination)) { ?>
                                        <?php echo $pagination ?>
                                    <?php } ?>
                                </ul>                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END ROW-1 -->
</div>
<!--Change Status Popup start here-->
<div class="modal fade " id="change-status-modal" tabindex="-1" role="dialog" aria-labelledby="slideRightModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="slideRightModalLabel">Set Status</h5>
			</div>
			<div class="modal-body">
				<form method="post" id="change_stauts">
					<div class="row">
						<div class="col-lg-12">
							<input type="hidden" id="esc_ids" value="" name="escalation_ids">
							<div class="form-group">
								<select name="status" required class="form-control" id="esc_status_id">
									<option value="">Choose Action</option>
									<option rel="1" value="updatestatus">Verify Seller</option>
									<option rel="0" value="updatestatus">Process Seller</option>
									<option rel="2" value="updatestatus"> Move to Junk</option>
								</select>
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-sm btn-primary" id="change_stauts_submit">Submit</button>
								<button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">
									Close
								</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!--Account Status Popup start here-->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!--sale person Status Popup start here-->
<script type="text/javascript">
		function filter_data_status(status = false) {
			document.getElementById('status').value=status;
			document.getElementById('searchSeller').submit();
		}
		$("ul.ul-nav-tabs a").click(function(e) {
			e.preventDefault();
			$(this).tab('show');
		});
		$(document).ready(function() {
			$('#allUsers').click(function(event) {
				if (this.checked) {
					$('.multiple_checkboxes').each(function() {
						this.checked = true;
					});
				} else {
					$('.multiple_checkboxes').each(function() {
						this.checked = false;
					});
				}
			});
			$("#delete_all_users").on('click', function(e) {
				var deleteConfirm = confirm("Are you sure You want to Delete this User");
				if (deleteConfirm == true) {
					e.preventDefault();
					var checkValues = $('.multiple_checkboxes:checked').map(function() {
						return $(this).val();
					}).get();
					$.each(checkValues, function(i, val) {
						$("#" + val).remove();
					});
					$.ajax({
						url: '<?php echo base_url() ?>admin/users/deleteusers',
						type: 'post',
						data: 'ids=' + checkValues
					}).done(function(data) {
						$("#respose").html(data);
						$('#allUsers').attr('checked', false);
					});
				}
			});
		});

		$('.add_change_status').on('click', function(e) {
			var escalation_ids = [];
			$.each($("input[class='multiple_checkboxes']:checked"), function() {
				escalation_ids.push($(this).val());
			});
			if (escalation_ids.length < 1) {
				alert('Please select records');
				return;
			}

			$("#esc_ids").val(escalation_ids);
			$('#change-status-modal').modal('show');
		});

		$("#change_stauts_submit").on('click', function(e) {
			var esc_status_id = $('select#esc_status_id option:selected').val();
			e.preventDefault();
			var verifyConfirm = confirm("Are you sure?");
			if (verifyConfirm == true) {
				var checkValues = $('#esc_ids').val();
				var status = $("select#esc_status_id option:selected").attr("rel");
				$.ajax({
						url: '<?php echo base_url() ?>admin/users/' + esc_status_id,
						type: 'post',
						data: 'ids=' + checkValues + '&stauts=' + status
					})
					.done(function(data) {
						//alert('Succsfully update');
						location.reload();
					});
			}
		});

		<?php unset($_GET['perPage']); ?>
		function per_page_records(per_page = false) {
			var page_url = '<?= base_url('admin/users/all') . '?' . http_build_query($_GET) . '&perPage=' ?>' + per_page;
			window.location.href = page_url;
		}
	</script>