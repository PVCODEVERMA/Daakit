	<div class="row">
		<div class="col-lg-12">
			<div class="card m-b-30">
				<div class="card-header">
					<div class="row">
						<div class="col-sm-2">
							<h6 class="m-b-0">
								<i class="mdi mdi-checkbox-intermediate"></i>Process Users
							</h6>
						</div>
						<div class="col-sm-2">
							<div class="row clear_fix">
								<div class="col-md-12" id="respose"></div>
							</div>
						</div>
						<div class="col-sm-8 text-right">
							<?php if (in_array('users_export', $user_details->permissions)) { ?>
								<a href="<?= base_url('admin/users/exportCSV/process'); ?>
								<?php if (!empty($filter)){echo "?" . http_build_query($_GET);} ?>" class="btn btn-outline-dark btn-sm"><i class="mdi mdi-arrow-down-bold-circle"></i>
								Export</a>
							<?php } ?>
							<?php if (in_array('users_push_to_leadsquared', $user_details->permissions)) { ?>
								<button class="btn btn-outline-dark btn-sm push_to_leadsquared" id="push_to_leadsquared"><i class="mdi mdi-upload"></i> Push to LeadSquared</button>
							<?php } ?>
							<?php if (in_array('users_account_manager', $user_details->permissions)) { ?>
							<button class="btn btn-outline-dark btn-sm remove_manager" id="remove_all_manager"><i class="mdi mdi-check"></i>Remove Manager</button>
							<?php } ?>
							<?php if (in_array('users_verify', $user_details->permissions)) { ?>
								<button class="btn btn-outline-dark btn-sm verify_all" id="verify_all_users"><i class="mdi mdi-check"></i> Verify Seller</button>
								<button class="btn btn-outline-dark btn-sm junk_all" id="junk_all_seller"><i class="mdi mdi-check"></i> Bulk Junk</button>
								<button class="btn btn-danger btn-sm delete_all" id="delete_all_users"><i class="mdi mdi-delete-forever"></i> Cancel</button>
							<?php } ?>
							<button type="button" class="btn btn-outline-dark show_hide_filter btn-sm" <?php if (!empty($filter)) { ?> style="display:none;" <?php } ?>><i class="icon-placeholder mdi mdi-filter"></i> Filters</button>
							<button type="button" class="btn btn-outline-dark show_hide_filter btn-sm" <?php if (empty($filter)) { ?> style="display:none;" <?php } ?>><i class="icon-placeholder mdi mdi-close"></i> Close</button>
						</div>
					</div>
				</div>
				<div class="card-body">
					<form method="get" action="<?= base_url('admin/users/processseller') ?>">
						<div class="row" id="filter_row" <?php if (empty($filter)) { ?> style="display:none;" <?php } ?>>
							<div class="col-sm-12">
								<div class="form-row">
									<div class="col-sm-3">
										<div class="row">
											<div class="col-sm-12">
												<label for="email">From Date:</label>
												<input type="text" autocomplete="off" data-start-date="<?= !empty($filter['start_date']) ? $filter['start_date'] : '' ?>" data-end-date="<?= !empty($filter['end_date']) ? $filter['end_date'] : '' ?>" class="form-control date-range-picker col-sm-12">
												<input type="hidden" autocomplete="off" id="date-min" name="filter[start_date]" value="<?= !empty($filter['start_date']) ? $filter['start_date'] : '' ?>" placeholder="from">
												<input type="hidden" autocomplete="off" id="date-max" name="filter[end_date]" value="<?= !empty($filter['end_date']) ? $filter['end_date'] : '' ?>" placeholder="to">
											</div>
										</div>
									</div>

									<div class="form-group col-sm-3" style="margin-top:2px;">
										<label class="font-secondary">Seller Id </label>
										<input type="text" autocomplete="off" name="filter[seller_ID]" value="<?= !empty($filter['seller_ID']) ? $filter['seller_ID'] : '' ?>" class="form-control" placeholder="Enter Seller ID With Comma">
									</div>

									<div class="form-group col-sm-3" style="margin-top:2px;">
										<label class="font-secondary">Multiple Seller Name </label>
										<select name="filter[id][]" multiple class="form-control js-select2" style="width: 100% !important;">
											<option value="">Select Seller</option>
											<?php
											foreach ($users as $values) {
											?>
												<option value="<?php echo $values->id; ?>" <?php if (!empty($filter['id']) && in_array($values->id, $filter['id'])) { ?> selected <?php } ?>>
													<?php echo ucwords($values->user_fname . ' ' . $values->user_lname); ?>
													(<?php echo ucwords($values->company_name) ?>)</option>
											<?php
											}
											?>
										</select>
									</div>

									<div class="form-group col-sm-3" style="margin-top:2px;">
										<label for="email">Email:</label>
										<input type="text" autocomplete="off" name="filter[email]" value="<?= !empty($filter['email']) ? $filter['email'] : '' ?>" class="form-control" placeholder="Email">
									</div>
									<div class="form-group col-sm-3" style="margin-top:2px;">
										<label for="email">Phone:</label>
										<input type="text" autocomplete="off" name="filter[phone]" value="<?= !empty($filter['phone']) ? $filter['phone'] : '' ?>" class="form-control" placeholder="Phone Number">
									</div>

									<div class="form-group col-sm-3">
										<label for="email">Recharge Status:</label>
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
									<div class="form-group col-sm-3">
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
										</select>
									</div>

									<div class="form-group col-sm-3" style="margin-top:2px;">
										<label for="email">Lead Source:</label>
										<input type="text" autocomplete="off" name="filter[lead_source]" value="<?= !empty($filter['lead_source']) ? $filter['lead_source'] : '' ?>" class="form-control">
									</div>

									<div class="form-group col-sm-3" style="margin-top:2px;">
										<label class="font-secondary">Multiple Account Manager </label>
										<select name="filter[manager_id][]" multiple class="form-control js-select2" style="width: 100% !important;">
											<option value="">Select Manager</option>
											<?php foreach ($admin_users as $a_user) {?>
												<option value="<?= $a_user->id; ?>" <?php if (!empty($filter['manager_id']) && in_array($a_user->id, $filter['manager_id'])) { ?> selected <?php } ?>><?= ucwords($a_user->fname . ' ' . $a_user->lname); ?></option>
											<?php } ?>

										</select>
									</div>
									<div class="form-group col-sm-3" style="margin-top:2px;">
										<label class="font-secondary">Multiple Sales Person </label>
										<select name="filter[sale_manager_id][]" multiple class="form-control js-select2" style="width: 100% !important;">
											<option value="">Select Sales Person</option>
											<?php foreach ($admin_users as $a_user) {?>
												<option value="<?= $a_user->id; ?>" <?php if (!empty($filter['sale_manager_id']) && in_array($a_user->id, $filter['sale_manager_id'])) { ?> selected <?php } ?>><?= ucwords($a_user->fname . ' ' . $a_user->lname); ?></option>
											<?php } ?>

										</select>
									</div>

									<div class="form-group col-sm-2" style="margin-top:34px;">
										<button type="submit" class="btn btn-sm btn-primary">Apply</button>
										<a href="<?= base_url('admin/users/processseller'); ?>" class="btn btn-sm btn-default">Clear</a>
									</div>
								</div>

							</div>
						</div>
					</form>
					<ul class="nav nav-tabs">
						<li class="nav-item">
							<a class="nav-link" href="admin/users/all">Active</a>
						</li>
						<li class="nav-item">
							<a class="nav-link active" href="admin/users/processseller">Process</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="admin/users/selleremployee">Seller Employess</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="admin/users/junkseller">Junk Account</a>
						</li>
					</ul>

					<div class="row p-t-10 border-top  p-b-10 action_row_selected sticky-top border-bottom" style="display: none;">
						<div class="col-sm-12">
							<div class="input-group">
								<div class="input-group-prepend" style="display: block !important;">
									<span class="input-group-text  border-dark"> <b class="multiple_select_count">0</b>&nbsp;selected</span>
								</div>
								<div class="input-group-append">
									<div class="col-md-12">
										<?php if (in_array('users_account_manager', $user_details->permissions)) { ?>
											<form method="post" id="apply_manager_form" action="admin/users/apply_manager">
												<div class="row">
													<div class="col-lg-9">
														<select required name='manager_id' class="form-control js-select2" style="width: 100% !important;">
															<option value="">Set Account Manager</option>
															<?php foreach ($admin_users as $a_user) { ?>
																<option value="<?= $a_user->id; ?>"><?= ucwords($a_user->fname . ' ' . $a_user->lname); ?></option>
															<?php } ?>
														</select>
													</div>
													<div class="col-md-3">
														<button type="submit" style="margin-top:5px;" class="btn btn-sm btn-outline-primary">Apply</button>
													</div>
												</div>
											</form>
										<?php } ?>
									</div>
								</div>

								<div class="input-group-append">
									<div class="col-md-12">
										<?php if (in_array('users_sales_manager', $user_details->permissions)) { ?>
											<form method="post" id="apply_sale_form">
												<div class="row">
													<div class="col-sm-9">
														<select required name='sale_id' class="form-control js-select2" style="width: 100% !important;">
															<option value="">Set Sale Person</option>
															<?php foreach ($admin_users as $a_user) { ?>
																<option value="<?= $a_user->id; ?>"><?= ucwords($a_user->fname . ' ' . $a_user->lname); ?></option>
															<?php } ?>
														</select>
													</div>
													<div class="col-md-3">
														<button type="submit" style="margin-top:5px;" class="btn btn-sm btn-outline-primary">Apply</button>
													</div>
												</div>
											</form>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="table-responsive">
						<table class="table table-hover table-sm">
							<thead>
								<tr>
									<th>
										<input data-switch="true" id="select_all_checkboxes" type="checkbox" id="allUsers">
									</th>
									<th>ID</th>
									<th style="width:10%;">Date</th>
									<th style="width:15%;">Seller & Company</th>
									<th>Email & Contact</th>
									<th>Balance</th>
									<th>Limit</th>
									<th>Acc. Manager</th>
									<th>Sale. Manager</th>
									<th>Lead Source</th>
									<th>Action</th>

								</tr>
							</thead>
							<tbody>
								<?php
								foreach ($userlist as $user) {
								?>
									<tr id="<?php echo $user->id ?>">
										<td>
											<?php if ($user->verified == '0') { ?>
												<input type="checkbox" name="checkbox[]" id="checkbox[]" class="multiple_checkboxes" value="<?php echo $user->id; ?>"><?php } ?>
										</td>
										<td><?php echo $user->id; ?></td>
										<td><?php echo date("j-M-Y", strtotime($user->created)); ?></td>
										<td>
										<?php echo $user->fname . ' ' . $user->lname; ?><br>
										<a target="_blank;" href="<?php echo base_url('admin/users/viewuser/'); ?><?php echo $user->id; ?>" style="color: #004080;font-weight:bold;">
										<?php echo $user->company_name; ?>
										</a>
										</td>
										<td>
										<?php echo $user->email; ?>
										<br>
										<?php echo $user->phone; ?>
										</td>
										<td><?= $user->wallet_balance; ?></td>
										<td><?= $user->wallet_limit; ?></td>
										<td><?= ucwords($user->manager_fname . ' ' . $user->manager_lname); ?></td>
										<td><?= ucwords($user->sale_fname . ' ' . $user->sale_lname); ?></td>
										<td><?= $user->lead_source; ?></td>
										<td>
											<?php if (in_array('users_login', $user_details->permissions)) { ?>
												<?php if (!$user->is_admin && !empty($user->login_token)) { ?>
													<a href="<?php echo base_url('users/login_with_token/' . $user->login_token); ?>" class="btn btn-primary btn-sm">Login</a>
												<?php } ?>
											<?php } ?>
										</td>
									</tr>
								<?php
								}
								?>
							</tbody>
						</table>
					</div>
					<div class="row">
						<div class="col-md-1">
							<?php
							$per_page_options = array(
								'10' => '10',
								'20' => '20',
								'50' => '50',
								'100' => '100',
								'200' => '200',
								'500' => '500',
							);

							$js = "class='form-control' onchange='per_page_records(this.value)'";
							echo form_dropdown('per_page', $per_page_options, $limit, $js);
							?>
						</div>

						<div class="col-sm-12 col-md-4">
							<div class="dataTables_info" id="example-multi_info" role="status" aria-live="polite">Showing
								<?= $offset + 1; ?> to <?= $offset + $limit; ?> of <?= $total_records; ?> entries</div>
						</div>
						<div class="col-sm-12 col-md-7">
							<ul class="pagination" style="float: right;margin-right: 40px;">
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
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script type="text/javascript">
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

			$("#apply_manager_form").submit(function(event) {
				event.preventDefault();
				var user_ids = [];
				$.each($("input[class='multiple_checkboxes']:checked"), function() {
					user_ids.push($(this).val());
				});

				if (user_ids.length === 0) {
					alert('Please select atleast one user');
					return;
				}

				var values = {};
				$.each($(this).serializeArray(), function(i, field) {
					values[field.name] = field.value;
				});

				event.preventDefault();
				$.ajax({
					url: 'admin/users/assign_manager',
					type: "POST",
					data: {
						user_ids: user_ids,
						manager_id: values['manager_id']
					},
					cache: false,
					success: function(data) {
						if (data.success)
							location.reload();
						else if (data.error)
							alert(data.error);
					}
				});
			});


			$("#apply_sale_form").submit(function(event) {
				event.preventDefault();
				var user_ids = [];
				$.each($("input[class='multiple_checkboxes']:checked"), function() {
					user_ids.push($(this).val());
				});

				if (user_ids.length === 0) {
					alert('Please select atleast one user');
					return;
				}

				var values = {};
				$.each($(this).serializeArray(), function(i, field) {
					values[field.name] = field.value;
				});

				event.preventDefault();
				$.ajax({
					url: 'admin/users/assign_sales',
					type: "POST",
					data: {
						user_ids: user_ids,
						sale_id: values['sale_id']
					},
					cache: false,
					success: function(data) {
						if (data.success)
							location.reload();
						else if (data.error)
							alert(data.error);
					}
				});
			});

			$("#remove_all_manager").on('click', function(e) {
				var verifyConfirm = confirm("Are you sure You want to Remove the Manager?");
				if (verifyConfirm == true) {
					e.preventDefault();
					var sellerid = [];
					$.each($(".multiple_checkboxes:checked"), function() {
						sellerid.push($(this).val());
					});
					var checkValues = sellerid.join(",");
					$.ajax({
							url: '<?php echo base_url() ?>admin/users/removemanager',
							type: 'post',
							data: 'ids=' + checkValues
						})
						.done(function(data) {
							location.reload();
						});
				}
			});

			$("#verify_all_users").on('click', function(e) {
				var verifyConfirm = confirm("Are you sure You want to Verify this User");
				if (verifyConfirm == true) {
					e.preventDefault();
					var sellerverify = [];
					$.each($(".multiple_checkboxes:checked"), function() {
						sellerverify.push($(this).val());
					});
					var checkValues = sellerverify.join(",");
					$.ajax({
							url: '<?php echo base_url() ?>admin/users/verifyusers',
							type: 'post',
							data: 'ids=' + checkValues
						})
						.done(function(data) {
							location.reload();
						});
				}
			});

			$("#push_to_leadsquared").on('click', function(e) {
				e.preventDefault();
				var verifyConfirm = confirm("Are you sure");
				if (verifyConfirm == true) {
					var sellerpush = [];
					$.each($(".multiple_checkboxes:checked"), function() {
						sellerpush.push($(this).val());
					});
					var checkValues = sellerpush.join(",");
					$.ajax({
							url: '<?php echo base_url() ?>admin/users/pushlead',
							type: 'post',
							data: 'ids=' + checkValues
						})
						.done(function(data) {
							location.reload();
						});
				}
			});

			$("#junk_all_seller").on('click', function(e) {
				var verifyConfirm = confirm("Are you sure You want to Junk this User");
				if (verifyConfirm == true) {
					e.preventDefault();
					var sellerverify = [];
					$.each($(".multiple_checkboxes:checked"), function() {
						sellerverify.push($(this).val());
					});
					var checkValues = sellerverify.join(",");
					$.ajax({
							url: '<?php echo base_url() ?>admin/users/junkusers',
							type: 'post',
							data: 'ids=' + checkValues
						})
						.done(function(data) {
							location.reload();
						});
				}
			});


			$("#delete_all_users").on('click', function(e) {
				var deleteConfirm = confirm("Are you sure You want to Delete this Seller ?");
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

		<?php unset($_GET['perPage']); ?>

		function per_page_records(per_page = false) {
			var page_url = '<?= base_url('admin/users/processseller') . '?' . http_build_query($_GET) . '&perPage=' ?>' +
				per_page;
			window.location.href = page_url;
		}
	</script>