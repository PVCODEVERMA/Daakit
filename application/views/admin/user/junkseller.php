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
						<div class="col-sm-4">
							<div class="row clear_fix">
								<div class="col-md-12" id="respose"></div>
							</div>
						</div>
						<div class="col-sm-6 text-right">
							<?php if (in_array('users_export', $user_details->permissions)) { ?>
                            <a href="<?= base_url('admin/users/exportCSV/inactive'); ?><?php if (!empty($filter)) {
                                                                                    echo "?" . http_build_query($_GET);
                                                                                } ?>" class="btn btn-outline-dark btn-sm"><i class="mdi mdi-arrow-down-bold-circle"></i> Export</a>
                        <?php } ?>
							<?php if (in_array('users_verify', $user_details->permissions)) { ?>
								<button class="btn btn-outline-dark btn-sm verify_all" id="verify_all_users"><i class="mdi mdi-check"></i> Verify Seller</button>
								<button class="btn btn-outline-dark btn-sm process_all" id="process_all_users"><i class="mdi mdi-check"></i> Bulk Process</button>

								<button class="btn btn-danger btn-sm delete_all" id="delete_all_users"><i class="mdi mdi-delete-forever"></i> Delete</button>
							<?php } ?>
							<button type="button" class="btn btn-outline-dark show_hide_filter btn-sm" <?php if (!empty($filter)) { ?> style="display:none;" <?php } ?>><i class="icon-placeholder mdi mdi-filter"></i> Filters</button>
							<button type="button" class="btn btn-outline-dark show_hide_filter btn-sm" <?php if (empty($filter)) { ?> style="display:none;" <?php } ?>><i class="icon-placeholder mdi mdi-close"></i> Close</button>
						</div>
					</div>
				</div>
				<div class="card-body">
					<form method="get" action="<?= base_url('admin/users/junkseller') ?>">
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
										<label class="font-secondary">Seller Name </label>
										<select name="filter[id]" class="form-control js-select2" style="width: 100% !important;">
											<option value="">Select Seller</option>
											<?php
											foreach ($users as $values) {
												$sellerid = '';
												if (!empty($filter['id']))
													$sellerid = $filter['id'];
											?>
												<option <?php if ($sellerid == $values->id) { ?> selected <?php } ?> value="<?php echo $values->id; ?>"><?php echo ucwords($values->user_fname . ' ' . $values->user_lname); ?> (<?php echo ucwords($values->company_name) ?>)</option>
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
									<div class="form-group col-sm-3" style="margin-top:34px;">
										<button type="submit" class="btn btn-sm btn-primary">Apply</button>
										<a href="<?= base_url('admin/users/junkseller'); ?>" class="btn btn-sm btn-default">Clear</a>
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
							<a class="nav-link" href="admin/users/processseller">Process</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="admin/users/selleremployee">Employees</a>
						</li>
						<li class="nav-item">
							<a class="nav-link active" href="admin/users/junkseller">Junk Account</a>
						</li>
					</ul>
					<div class="table-responsive">
						<table class="table table-hover table-sm">
							<thead>
								<tr>
									<th>
										<input type="checkbox" id="allUsers">
									</th>
									<th>ID</th>
									<th style="width:10%;">Date</th>
									<th style="width:15%;">Seller Name</th>
									<th>Company Name</th>
									<th>Contact</th>
									<th>Balance</th>
									<th>Limit</th>
									<th>Action</th>

								</tr>
							</thead>
							<tbody>
								<?php
								foreach ($userlist as $user) {
								?>
									<tr id="<?php echo $user->id ?>">
										<td>
											<?php if ($user->verified == '2') { ?>
												<input type="checkbox" name="checkbox[]" id="checkbox[]" class="sub_chk" value="<?php echo $user->id; ?>"><?php } ?>
										</td>
										<td><?php echo $user->id; ?></td>
										<td><?php echo date("j-M-Y", strtotime($user->created)); ?></td>
										<td><?php echo $user->fname . ' ' . $user->lname; ?></td>
										<td style="width: 15%;">
											<a target="_blank;" href="<?php echo base_url('admin/users/viewuser/'); ?><?php echo $user->id; ?>" style="color: #004080;font-weight:bold;">
												<?php echo $user->company_name; ?>
											</a>
										</td>
										<td><?php echo $user->phone; ?></td>
										<td><?= $user->wallet_balance; ?></td>
										<td><?= $user->wallet_limit; ?></td>
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
							<div class="dataTables_info" id="example-multi_info" role="status" aria-live="polite">Showing <?= $offset + 1; ?> to <?= $offset + $limit; ?> of <?= $total_records; ?> entries</div>
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
					$('.sub_chk').each(function() {
						this.checked = true;
					});
				} else {
					$('.sub_chk').each(function() {
						this.checked = false;
					});
				}
			});
			$("#verify_all_users").on('click', function(e) {
				var verifyConfirm = confirm("Are you sure You want to Verify this User");
				if (verifyConfirm == true) {
					e.preventDefault();
					var sellerverify = [];
					$.each($(".sub_chk:checked"), function() {
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

			$("#process_all_users").on('click', function(e) {
				var verifyConfirm = confirm("Are you sure You want to Process this User");
				if (verifyConfirm == true) {
					e.preventDefault();
					var sellerverify = [];
					$.each($(".sub_chk:checked"), function() {
						sellerverify.push($(this).val());
					});
					var checkValues = sellerverify.join(",");
					$.ajax({
							url: '<?php echo base_url() ?>admin/users/processusers',
							type: 'post',
							data: 'ids=' + checkValues
						})
						.done(function(data) {
							location.reload();
						});
				}
			});

			$("#delete_all_users").on('click', function(e) {
				var deleteConfirm = confirm("Are you sure You want to Delete this User");
				if (deleteConfirm == true) {
					e.preventDefault();
					var checkValues = $('.sub_chk:checked').map(function() {
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
			var page_url = '<?= base_url('admin/users/junkseller') . '?' . http_build_query($_GET) . '&perPage=' ?>' + per_page;
			window.location.href = page_url;
		}
	</script>