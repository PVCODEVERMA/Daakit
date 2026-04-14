<style>
	.bg-dark
	{
		background-color: #554DC0 !important;
	}
	.deltagloabal_cmp {
		text-align: center;
		color: #004080;
		font-weight: 600;
	}

	.text_right {
		float: right;
		margin: auto;
	}

	.row_scroll {

		max-height: 300px;
		overflow-y: scroll;
		/* Add the ability to scroll */
	}

	/* Hide scrollbar for Chrome, Safari and Opera */
	.row_scroll::-webkit-scrollbar {
		display: none;
	}

	/* Hide scrollbar for IE and Edge */
	.row_scroll {
		-ms-overflow-style: none;
	}

	.form-group.col-md-4.required .control-label:after {
		content: "*";
		color: red;
		font-weight: bold;
	}

	.form-group.col-md-6.required .control-label:after {
		content: "*";
		color: red;
		font-weight: bold;
	}

	.form-group.col-md-12.required .control-label:after {
		content: "*";
		color: red;
		font-weight: bold;
	}

	.form-group .bootstrap-tagsinput {
		display: inline-block;
		padding: 2px 6px;
		/* display:none; */
	}

	.sti_container {
		position: relative;
	}

	.hover-btn {
		position: relative;
		display: inline-block;
		padding: 0px;
		/* background-color: white; */
		cursor: pointer;
		outline: none;
		border: 0;
		vertical-align: middle;
		text-decoration: none;
		color: #fff;
		/* border-radius: 25px; */
		-webkit-transition: width 0.5s;
		transition: width 0.5s;
	}

	.hover-btn .btn-icon {
		max-width: 0;
		display: inline-block;
		-webkit-transition: color .25s 1.5s, max-width 2s;
		transition: color .25s 1.5s, max-width 2s;
		vertical-align: top;
		white-space: nowrap;
		overflow: hidden;
		color: black;
	}

	.hover-btn:hover .btn-icon {
		max-width: 300px;
		color: white;
	}

	.search_gst,
	.gst_fetch {
		display: none;
	}

	select[readonly].select2-hidden-accessible+.select2-container {
		pointer-events: none;
		touch-action: none;
	}

	select[readonly].select2-hidden-accessible+.select2-container .select2-selection {
		background: #eee;
		box-shadow: none;
	}

	select[readonly].select2-hidden-accessible+.select2-container .select2-selection__arrow,
	select[readonly].select2-hidden-accessible+.select2-container .select2-selection__clear {
		display: none;
	}
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function openCourierModal(userId) {
    $('#modal_user_id').val(userId);

    // Step 1: Fetch disabled couriers of this user
    $.getJSON("<?php echo base_url(); ?>admin/users/get_user_disabled_couriers/" + userId, function(disabledData) {
        // Step 2: Fetch all active couriers
        $.getJSON("<?php echo base_url(); ?>admin/users/get_active_couriers", function(couriers) {
            let html = '';
            couriers.forEach(courier => {
                // ✅ Flip logic: if NOT disabled, then checked
                let checked = disabledData.includes(courier.id.toString()) ? '' : 'checked';
                html += `
                    <tr>
                        <td>${courier.id}</td>
                        <td>${courier.name}</td>
                        <td><input type="checkbox" name="enabled_couriers[]" value="${courier.id}" ${checked}></td>
                    </tr>`;
            });
            $('#courierTable tbody').html(html);
            $('#courierModal').modal('show');
        });
    });
}


// Submit the form
$(document).on('submit', '#updateCourierForm', function(e) {
      e.preventDefault();

      const formData = $(this).serialize();

      $.ajax({
          url: "<?php echo base_url(); ?>admin/users/update_disabled_couriers",
          method: "POST",
          data: formData,
          success: function(response) {
              try {
                  const res = JSON.parse(response);
                  alert(res.message);
                  $('#courierModal').modal('hide');
              } catch (e) {
                  console.error("Invalid JSON from server:", response);
                  alert("Something went wrong. Please try again.");
              }
          },
          error: function(xhr, status, error) {
              console.error("AJAX error:", error);
              alert("Server error. Please try again.");
          }
      });
  });

function closeCourierModal() {
    const modalElement = document.getElementById('courierModal');
    const modalInstance = bootstrap.Modal.getInstance(modalElement);
    modalInstance.hide();
  }


</script>

<?php
function calculate_time($start_time, $end_time)
{
	if (empty($end_time))
		$end_time = strtotime(date('Y-m-d H:i:s'));

	$seconds  = $end_time - $start_time;
	$months = floor($seconds / (3600 * 24 * 30));
	$day = floor($seconds / (3600 * 24));
	$hours = floor($seconds / 3600);
	$mins = floor(($seconds - ($hours * 3600)) / 60);
	$secs = floor($seconds % 60);

	if ($seconds < 60)
		$time = $secs . " seconds";
	else if ($seconds < 60 * 60)
		$time = $mins . " minutes";
	else if ($seconds < 24 * 60 * 60)
		$time = $hours . " hours";
	else if ($seconds < 24 * 60 * 60)
		$time = $day . " day";
	else
		$time = $months . " month";

	return $time;
}
?>
<div class="row">
	<div class="col-md-12">
		<div class="card m-b-30">
			<div class="card-body">
				<div class="row">
					<div class="col-lg-4">
						<div class="card m-b-30">
							<div class="card-header bg-dark text-white">
								Seller Details
								<?php if (in_array('users_edit_details', $user_details->permissions)) { ?>
									<i class="mdi mdi-lead-pencil" data-toggle="modal" data-target="#personaldetailsModal" style="float: right;"></i>
								<?php } ?>
							</div><br>

							<div class="list-group list list-group-flush">
								<?php
								if (!empty($singleuserdetail[0]->cmp_logo)) {
								?>
									<div class="text-center">
										<div class="avatar avatar-xl">
											<img class="avatar-img rounded-circle" src="<?php echo $singleuserdetail[0]->cmp_logo; ?>">
										</div>
									</div><br>
								<?php
								} else {
								?>
									<div class="text-center">
										<div class="avatar avatar-xl">
											<img class="avatar-img rounded-circle" src="<?php echo base_url(); ?>assets/seller_company_logo/dummy_img.jpg">
										</div>
									</div><br>
								<?php
								}
								?>

								<div class="list-group-item">
									<strong>Seller Id:</strong>
									<p class="text_right"><?php echo $singleuserdetail[0]->sellerid; ?></p>
								</div>
								<div class="list-group-item">
									<strong>Seller Name:</strong>
									<p class="text_right"><?php echo $singleuserdetail[0]->fname . ' ' . $singleuserdetail[0]->lname; ?></p>
								</div>
								<div class="list-group-item">
									<strong>Company Name:</strong>
									<p class="text_right"><?php echo ucwords($singleuserdetail[0]->company_name); ?></p>
								</div>
								<div class="list-group-item">
									<strong>Company Add:</strong>
									<p class="text_right"><?php echo ucwords($singleuserdetail[0]->cmp_address); ?></p>
								</div>
								<div class="list-group-item">
									<strong>City:</strong>
									<p class="text_right"><?php echo ucwords($singleuserdetail[0]->cmp_city); ?></p>
								</div>
								<div class="list-group-item">
									<strong>State:</strong>
									<p class="text_right"><?php echo ucwords($singleuserdetail[0]->cmp_state); ?></p>
								</div>
								<div class="list-group-item">
									<strong>Pin Code:</strong>
									<p class="text_right"><?php echo ucwords($singleuserdetail[0]->cmp_pincode); ?></p>
								</div>
								<div class="list-group-item">
									<strong>Pan Number:</strong>
									<p class="text_right"><?php echo ucwords($singleuserdetail[0]->cmp_pan); ?></p>
								</div>
								<div class="list-group-item">
									<strong>GST Number:</strong>
									<p class="text_right"><?php echo ucwords($singleuserdetail[0]->cmp_gstno); ?></p>
								</div>
								<div class="list-group-item">
									<strong>Signup Date:</strong>
									<p class="text_right"><?php echo date("j-M-Y", strtotime($singleuserdetail[0]->created)); ?></p>
								</div>
								<div class="list-group-item">
									<strong>Verified Date:</strong>
									<p class="text_right">
										<?php if ($singleuserdetail[0]->verified_date == "0") { ?>
											<?php echo ""; ?>
										<?php } else { ?>
											<?php echo date("j-M-Y", $singleuserdetail[0]->verified_date) . '<br/>'; ?>
										<?php
										}
										?>

										<?php if ($singleuserdetail[0]->e_verified == "0" && ($singleuserdetail[0]->verified == '1' || $singleuserdetail[0]->verified == '2')) { ?>
											<?php echo "<small>Manually Verified</small>"; ?>
										<?php } else if ($singleuserdetail[0]->e_verified == "1" && ($singleuserdetail[0]->verified == '1' || $singleuserdetail[0]->verified == '2')) { ?>
											<?php echo "<small>E-Verified</small>" ?>
										<?php
										} else {
											echo "-";
										}
										?>
									</p>
								</div>
								<div class="list-group-item">
									<strong>Account Manager:</strong>
									<p class="text_right">
										<?php echo $singleuserdetail[0]->manager_fname . ' ' . $singleuserdetail[0]->manager_lname; ?>
									</p>
								</div>
								<?php if (!empty($singleuserdetail[0]->sale_fname)) { ?>
									<div class="list-group-item">
										<strong>Domestic Sales Person:</strong>
										<p class="text_right">
											<?php echo $singleuserdetail[0]->sale_fname . ' ' . $singleuserdetail[0]->sale_lname; ?>
										</p>
									</div>
								<?php } ?>
								<?php if (!empty($singleuserdetail[0]->b2b_sale_fname)) { ?>
									<div class="list-group-item">
										<strong>B2B Sales Manager:</strong>
										<p class="text_right">
											<?php echo $singleuserdetail[0]->b2b_sale_fname . ' ' . $singleuserdetail[0]->b2b_sale_lname; ?>
										</p>
									</div>
								<?php } ?>

								<?php if (!empty($singleuserdetail[0]->int_sale_fname)) { ?>
									<div class="list-group-item">
										<strong>International Sales Person:</strong>
										<p class="text_right">
											<?php echo $singleuserdetail[0]->int_sale_fname . ' ' . $singleuserdetail[0]->int_sale_lname; ?>
										</p>
									</div>
								<?php } ?>
								<?php if (!empty($singleuserdetail[0]->training_fname)) { ?>
									<div class="list-group-item">
										<strong>Training Manager:</strong>
										<p class="text_right">
											<?php echo $singleuserdetail[0]->training_fname . ' ' . $singleuserdetail[0]->training_lname; ?>
										</p>
									</div>
								<?php } ?>
								<div class="list-group-item">
									<strong>Lead Source:</strong>
									<p class="text_right"><?php echo ucwords($singleuserdetail[0]->lead_source); ?></p>
								</div>
								<div class="list-group-item">
									<strong>Service:</strong>
									<p class="text_right"><?php if ($singleuserdetail[0]->service_type == '0') {
																echo "Domestic";
															} elseif ($singleuserdetail[0]->service_type == '1') {
																echo "International";
															} ?></p>
								</div>
								<div class="list-group-item">
									<strong>Refered By:</strong>
									<p class="text_right">
										<?php
										if ($singleuserdetail[0]->referral_id == "0") {
											echo "Self Signup";
										} else {
											echo $referral_name[0]->referral_fname . ' ' . $referral_name[0]->referral_lname;
										}
										?>
									</p>
								</div>
								<?php if (!in_array('user_contact', $is_enable)) { ?>
									<div class="list-group-item">
										<a href="<?= base_url('admin/users/enable_user_view/' . $singleuserdetail[0]->sellerid . '/' . 'user_contact') ?>" class="btn btn-info btn-sm" onclick="return confirm('Are you sure ?')">Unlock Contact Details</a>
									</div>
								<?php } ?>
								<div class="list-group-item">
									<strong>Email:</strong>
									<p class="text_right"><?php echo (!empty($singleuserdetail[0]->email) && !in_array('user_contact', $is_enable)) ? substr($singleuserdetail[0]->email, 0, 2) . str_repeat("*", strlen($singleuserdetail[0]->email) - 4) . substr($singleuserdetail[0]->email, -2) : $singleuserdetail[0]->email; ?></p>

								</div>
								<div class="list-group-item">
									<strong>Contact No:</strong>
									<p class="text_right"><?php echo (!empty($singleuserdetail[0]->phone) && !in_array('user_contact', $is_enable)) ? substr($singleuserdetail[0]->phone, 0, 2) . str_repeat("*", strlen($singleuserdetail[0]->phone) - 4) . substr($singleuserdetail[0]->phone, -2) : $singleuserdetail[0]->phone;  ?></p>
								</div>

								<div class="list-group-item">
									<strong>Company Email:</strong>
									<p class="text_right"><?php echo (!empty($singleuserdetail[0]->cmp_email) && !in_array('user_contact', $is_enable)) ? substr($singleuserdetail[0]->cmp_email, 0, 2) . str_repeat("*", strlen($singleuserdetail[0]->cmp_email) - 4) . substr($singleuserdetail[0]->cmp_email, -2) : $singleuserdetail[0]->cmp_email; ?></p>
								</div>
								<div class="list-group-item">
									<strong>Company Contact:</strong>
									<p class="text_right"><?php echo (!empty($singleuserdetail[0]->cmp_phone) && !in_array('user_contact', $is_enable)) ? substr($singleuserdetail[0]->cmp_phone, 0, 2) . str_repeat("*", strlen($singleuserdetail[0]->cmp_phone) - 4) . substr($singleuserdetail[0]->cmp_phone, -2) : $singleuserdetail[0]->cmp_phone; ?></p>
								</div>
								<div class="list-group-item">
									<strong>Current Plan:</strong>
									<p class="text_right"><?php echo ucwords($singleuserdetail[0]->pricing_plan); ?></p>
								</div>
								<div class="list-group-item">
									<strong>Current International Plan:</strong>
									<p class="text_right"><?php echo ucwords($singleuserdetail[0]->international_pricing_plan); ?></p>
								</div>
								<div class="list-group-item">
									<strong>Current B2B Plan:</strong>
									<p class="text_right"><?php echo ucwords($singleuserdetail[0]->cargo_pricing_plan); ?></p>
								</div>
								<div class="list-group-item">
									<strong>Current Balance</strong>
									<p class="text_right"><?php echo ucwords($singleuserdetail[0]->wallet_balance); ?></p>
								</div>
								<div class="list-group-item">
									<strong>Advance Amount Limit:</strong>
									<p class="text_right"><?php echo ucwords($singleuserdetail[0]->wallet_limit); ?></p>
								</div>
								<div class="list-group-item">
									<strong>LeadSquared ID:</strong>
									<p class="text_right"><?php echo ucwords($singleuserdetail[0]->leadsquared_id); ?></p>
								</div>
								<div class="list-group-item">
									<strong>Exotel Number:</strong>
									<p class="text_right"><?php echo (!empty($userIVR) ? $userIVR : ''); ?></p>
								</div>
							</div>
						</div>
						<!-- <div class="card m-b-30">
							<div class="card-header bg-dark text-white">
								NDR Call API
								<?php // if (in_array('ndrcall_api', $user_details->permissions)) { 
								?>
								<i class="mdi mdi-lead-pencil" data-toggle="modal" data-target="#ndrcalldetailsModal" style="float: right;"></i>
								<?php //  } 
								?>
							</div><br>
							<div class="list-group list list-group-flush">
								<div class="list-group-item ">
									<?php if ($seller_ndrcall && ($seller_ndrcall[0]->is_active  == 1)) { ?>
										<strong> Enabled </strong>
									<?php } else { ?>
										<strong> Disabled </strong>
									<?php } ?>
								</div>
							</div>
						</div> -->
					</div>
					<div class="col-sm-4" style="word-break: break-all;">
						<div class="card m-b-30">
							<div class="card-header bg-dark text-white">
								Bank Details
								<?php if (in_array('users_bank_details', $user_details->permissions)) { ?>
									<i class="mdi mdi-lead-pencil" data-toggle="modal" data-target="#bankdetailsModal" style="float: right;"></i>
								<?php } ?>
							</div><br>
							<div class="list-group list  list-group-flush">
								<?php
								if (!empty($singleuserdetail[0]->cmp_chequeimg)) {
								?>
									<div class="text-center">
										<div class="avatar avatar-xl">
											<a class="btn btn-success btn-sm" href="<?php echo $singleuserdetail[0]->cmp_chequeimg; ?>" target="_blank">View</a>
										</div>
									</div>
								<?php
								} else {
								?>
									<div class="text-center">
										<div class="avatar avatar-xl">
											<img class="img-fluid img-thumbnail" src="<?php echo base_url(); ?>assets/seller_company_logo/dummy_img.jpg">
										</div>
									</div><br>
								<?php
								}
								?>
								<div class="list-group-item">
									<strong>Account Name:</strong>
									<p class="text_right"><?= ucwords($singleuserdetail[0]->cmp_accntholder); ?></p>
								</div>
								<div class="list-group-item">
									<strong>Account No:</strong>
									<p class="text_right"><?= $singleuserdetail[0]->cmp_accno; ?></p>
								</div>
								<div class="list-group-item">
									<strong>Bank Name:</strong>
									<p class="text_right"><?= $singleuserdetail[0]->cmp_bankname; ?></p>
								</div>
								<div class="list-group-item">
									<strong>Bank Branch:</strong>
									<p class="text_right"><?= $singleuserdetail[0]->cmp_bankbranch; ?></p>
								</div>
								<div class="list-group-item">
									<strong>Account Type:</strong>
									<p class="text_right"><?= $singleuserdetail[0]->cmp_acctype; ?></p>
								</div>
								<div class="list-group-item">
									<strong>IFSC Code:</strong>
									<p class="text_right"><?= $singleuserdetail[0]->cmp_accifsc; ?></p>
								</div>
							</div>
						</div>

						<div class="card m-b-30">
							<div class="card-header bg-dark text-white">
								Agreement Accepted
							</div><br>
							<div class="list-group list  list-group-flush">
								<div class="list-group-item">
									<strong>Download Status</strong><br>
									<?php
									if ($singleuserdetail[0]->agreement_status == '0') {
									?>
										<p style="">Customer Not Accept the Agreement Yet</p>
									<?php
									} else {
										$agreement_url = $singleuserdetail[0]->agreement_url;
									?>
										<a target="_blanks" href="<?php echo $agreement_url; ?>" class="btn btn-outline-primary btn-sm">Download</a>
									<?php
									}
									?>
								</div>
							</div>
						</div>

						<div class="card m-b-30">
							<div class="card-header bg-dark text-white">
								Remarks
							</div>
							<div class="card-body">
								<blockquote class="trello-card"><a href="<?= $trello_card_url; ?>"></a></blockquote>
							</div>
						</div>

						<div class="card m-b-30">
							<div class="card-header bg-dark text-white">
								Change Plan
							</div><br>
							<div class="list-group list list-group-flush">
								<div class="list-group-item">
									<strong>Plan</strong>
									<?php
									if (!empty($singleuserdetail[0]->pricing_plan)) {
										echo '<span style="float: right;color: #00cc99;font-size: 14px;font-weight: bold;">' . ucfirst($singleuserdetail[0]->pricing_plan) . '</span>';
									}
									?>
									<br />
								</div>
								<?php if (in_array('users_pricing_plan', $user_details->permissions)) { ?>
									<div class="list-group-item">
										<form method="post" action="<?= base_url('admin/users/sellerplan'); ?>">
											<input type="hidden" name="order_type" value="ecom">
											<?php if (!empty($singleuserdetail[0]->sellerid)) { ?>
												<input type="hidden" name="seller_id" value="<?= $singleuserdetail[0]->sellerid; ?>">
											<?php } ?>
											<select name="planname" class="form-control js-select2" style="width: 100% !important;">
												<option value="">Select</option>
												<?php
												if (!empty($plan)) {
													foreach ($plan as $plan) {
												?>
														<option value="<?php echo $plan->plan_name; ?>" <?php if ($singleuserdetail[0]->pricing_plan == $plan->plan_name) { ?> selected <?php } ?>><?php echo ucwords($plan->plan_name); ?></option>
												<?php }
												} ?>
											</select>
											<br>
											<div class="col-sm-12 text-right" style="margin-top: 10px;float: right;padding: 0px !important;">
												<button type="submit" class="btn btn-outline-primary btn-sm">Submit</button>
											</div>
										</form>
									</div>
								<?php } ?>
							</div>
						</div>
						<div class="card m-b-30">
							<div class="card-header bg-dark text-white">
								User Settings
							</div><br>
							<div class="list-group list  list-group-flush">
								<div class="list-group-item">
									<strong>Projected Shipments</strong>
									<?php
									if (!empty($singleuserdetail[0]->projected_shipments)) {
										echo '<span style="float: right;color: #00cc99;font-size: 14px;font-weight: bold;">' . ucfirst($singleuserdetail[0]->projected_shipments) . '</span>';
									}
									?>
									<br><br>
									<strong>NDR Action Type</strong>
									<?php
									if (!empty($singleuserdetail[0]->ndr_action_type)) {
										echo '<span style="float: right;color: #00cc99;font-size: 14px;font-weight: bold;">' . ucfirst($singleuserdetail[0]->ndr_action_type) . '</span>';
									}
									?>
									<br><br>
								</div>
								<?php /* if (in_array('users_settings', $user_details->permissions)) { */ ?>
								<div class="list-group-item">
									<form method="post" id="FormId">
										<?php
										if (!empty($singleuserdetail[0]->sellerid)) {
										?>
											<input type="hidden" name="seller_id" id="seller_id" value="<?= $singleuserdetail[0]->sellerid; ?>">
										<?php
										}
										?>
										<input type="text" name="projected_shipments" class="form-control" placeholder="Projected Shipments" value="<?php if (!empty($singleuserdetail[0]->projected_shipments)) {
																																						echo $singleuserdetail[0]->projected_shipments;
																																					} ?>">
										<br>
										<select name="ndr_action_type" class="form-control">
											<option value="">Select NDR Type</option>
											<option <?php if ($singleuserdetail[0]->ndr_action_type == 'fake') { ?> selected <?php } ?> value="fake">Fake</option>
											<option <?php if ($singleuserdetail[0]->ndr_action_type == 'genuine') { ?> selected <?php } ?> value="genuine">Genuine</option>

										</select><br>
										<div class="col-sm-12 text-right" style="margin-top: 10px;float: right;padding: 0px !important;">
											<button type="submit" id="user_seting_form" class="btn btn-outline-primary btn-sm">Submit</button>
										</div>
									</form>
								</div>
								<?php // } 
								?>
							</div>
						</div>
						<div class="card m-b-30">
							<div class="card-header bg-dark text-white">
								Referred By
							</div><br>
							<div class="list-group list list-group-flush">
								<div class="list-group-item">
									<form method="post" action="<?php echo base_url(); ?>admin/users/referral_ids/<?php echo $singleuserdetail[0]->sellerid; ?>">


										<select class="getUserlist form-control" style="width: 100% !important;" name="referralid">
											<option value="">Select</option>
											<?php
											if (!empty($users)) {
												foreach ($users as $values) {
											?>
													<option value="<?php echo $values->id; ?>"><?php echo ucwords($values->user_fname . ' ' . $values->user_lname); ?> (<?php echo ucwords($values->company_name) ?>)</option>
											<?php }
											} ?>
										</select>
										<div class="col-sm-12 text-right" style="margin-top: 10px;float: right;padding: 0px !important;">
											<button type="submit" class="btn btn-outline-primary btn-sm">Submit</button>
										</div>
									</form>
								</div>
							</div>
						</div>

<!-- Manage Couriers Section -->
<div class="card m-b-30">
    <div class="card-header bg-dark text-white">
        Manage Couriers (Enable/Disable for User)
    </div>
    <div class="list-group list-group-flush">
        <div class="list-group-item text-center">
            <button class="btn btn-outline-info btn-sm" onclick="openCourierModal(<?php echo $singleuserdetail[0]->sellerid; ?>)">
                Manage Couriers
            </button>
        </div>
    </div>
</div>


<div class="card m-b-30">
    <div class="card-header bg-dark text-white">
        Message Provider
        <form id="providerForm" style="padding: 10px 0 0 10px">
            <input type="radio" name="message_provider" value="msg91" id="msg91">
            <label for="msg91">msg91</label>    

            <input type="radio" name="message_provider" value="Vertex" id="Vertex" disabled>
            <label for="Vertex">Vertex</label>    
        </form>                                
    </div>

    <div class="list-group list-group-flush">
        <div class="list-group-item p-2">
            <div class="table-responsive" style="max-height: 700px; overflow-y: hidden;">
				<div id="perStatusTable">
				
					<table class="table table-sm table-bordered text-center mb-2">
						<thead class="table-light">
							<tr>
								<th style="width:30%;">Status</th>
								<th>SMS</th>
								<th>Email</th>
								<th>WhatsApp</th>
								<th>IVR</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>Order Verification</td>
								<td style="background-color:#f8d7da;" id="seller_new_sms"></td>
								<td style="background-color:#f8d7da;" id="seller_new_email"></td>
								<td style="background-color:#f8d7da;" id="seller_new_whatsapp"></td>
								<td style="background-color:#f8d7da;" id="seller_new_ivr"></td>
							</tr>
							<tr>
								<td>Confirmation / Acknowledgement</td>
								<td style="background-color:#f8d7da;" id="seller_confirmation_acknowledgement_sms"></td>
								<td style="background-color:#f8d7da;" id="seller_confirmation_acknowledgement_email"></td>
								<td style="background-color:#f8d7da;" id="seller_confirmation_acknowledgement_whatsapp"></td>
								<td style="background-color:#f8d7da;" id="seller_confirmation_acknowledgement_ivr"></td>
							</tr>
							<tr>
								<td>Pending Pickup</td>
								<td style="background-color:#f8d7da;" id="seller_pending_pickup_sms"></td>
								<td style="background-color:#f8d7da;" id="seller_pending_pickup_email"></td>
								<td style="background-color:#f8d7da;" id="seller_pending_pickup_whatsapp"></td>
								<td style="background-color:#f8d7da;" id="seller_pending_pickup_ivr"></td>
							</tr>
							<tr>
								<td>Shipped / In Transit</td>
								<td style="background-color:#f8d7da;" id="seller_in_transit_sms"></td>
								<td style="background-color:#f8d7da;" id="seller_in_transit_email"></td>
								<td style="background-color:#f8d7da;" id="seller_in_transit_whatsapp"></td>
								<td style="background-color:#f8d7da;" id="seller_in_transit_ivr"></td>
							</tr>
							<tr>
								<td>Out For Delivery</td>
								<td style="background-color:#f8d7da;" id="seller_out_for_delivery_sms"></td>
								<td style="background-color:#f8d7da;" id="seller_out_for_delivery_email"></td>
								<td style="background-color:#f8d7da;" id="seller_out_for_delivery_whatsapp"></td>
								<td style="background-color:#f8d7da;" id="seller_out_for_delivery_ivr"></td>
							</tr>
							<tr>
								<td>Delivered</td>
								<td style="background-color:#f8d7da;" id="seller_delivered_sms"></td>
								<td style="background-color:#f8d7da;" id="seller_delivered_email"></td>
								<td style="background-color:#f8d7da;" id="seller_delivered_whatsapp"></td>
								<td style="background-color:#f8d7da;" id="seller_delivered_ivr"></td>
							</tr>
							<tr>
								<td>NDR</td>
								<td style="background-color:#f8d7da;" id="seller_exception_sms"></td>
								<td style="background-color:#f8d7da;" id="seller_exception_email"></td>
								<td style="background-color:#f8d7da;" id="seller_exception_whatsapp"></td>
								<td style="background-color:#f8d7da;" id="seller_exception_ivr"></td>
							</tr>
							<tr>
								<td>RTO</td>
								<td style="background-color:#f8d7da;" id="seller_rto_sms"></td>
								<td style="background-color:#f8d7da;" id="seller_rto_email"></td>
								<td style="background-color:#f8d7da;" id="seller_rto_whatsapp"></td>
								<td style="background-color:#f8d7da;" id="seller_rto_ivr"></td>
							</tr>
						</tbody>
					</table>
				</div>
				<div id="bundleTable">
				
					<table class="table table-sm table-bordered text-center">
						<thead class="table-light">
							<tr>
								<th style="width:30%;">Label</th>
								<th>SMS</th>
								<th>Email</th>
								<th>WhatsApp</th>
								<th>IVR</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>Bundle Price</td>
								<td style="background-color:#f8d7da;" id="seller_bundled_sms"></td>
								<td style="background-color:#f8d7da;" id="seller_bundled_email"></td>
								<td style="background-color:#f8d7da;" id="seller_bundled_whatsapp"></td>
								<td style="background-color:#f8d7da;" id="seller_bundled_ivr"></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
        </div>
    </div>
</div>


<div class="modal fade" id="courierModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg">
    <form id="updateCourierForm">
      <div class="modal-content">
        <div class="modal-header bg-dark text-white">
          <h5 class="modal-title">Manage Disabled Couriers</h5>
          <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="user_id" id="modal_user_id">
          <table class="table table-bordered table-sm" id="courierTable">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Enable/Disable</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success btn-sm">Update</button>
          <button type="button" class="btn btn-secondary btn-sm close" data-dismiss="modal" onclick="closeCourierModal()">Close</button>
        </div>
      </div>
    </form>
  </div>
</div>





						<?php if (in_array('account_master', $user_details->permissions)) { ?>
							<!-- <div class="card m-b-30">
								<div class="card-header bg-dark text-white">
									Set Master Account
								</div><br>
								<div class="list-group list list-group-flush">
									<div class="list-group-item">
										<form method="post" action="<?php echo base_url(); ?>admin/users/account_master_id/<?php echo $singleuserdetail[0]->sellerid; ?>">
											<select class="getUserlist form-control" style="width: 100% !important;" name="account_master">
												<option value="">Select Account Master</option>
												<?php if (!empty($allusers)) {
													foreach ($allusers as $values) {
														if ($singleuserdetail[0]->account_master_id == $values->id) {  ?>
															<option value="<?php echo $values->id; ?>" <?php if ($singleuserdetail[0]->account_master_id == $values->id) {
																											echo "selected";
																										} ?>><?php echo ucwords($values->user_fname . ' ' . $values->user_lname); ?> (<?php echo ucwords($values->company_name) ?>)</option>
												<?php }
													}
												} ?>
											</select>
											<?php if (!empty($singleuserdetail[0]->account_master_id)) { ?>
												<div style="margin-top:5px;color: red;font-family: auto;">
													<a href="<?php echo base_url('admin/users/viewuser/' . $singleuserdetail[0]->account_master_id); ?>" target="_blank"><span><u>Click here to check their master</u></span> </a>
												</div>
											<?php } ?>

											<div class="col-sm-12 text-right" style="margin-top: 10px;float: right;padding: 0px !important;">
												<button type="submit" class="btn btn-outline-primary btn-sm">Submit</button>
											</div>
										</form>
									</div>
								</div>
							</div> -->
						<?php }  ?>

					</div>

					<div class="col-sm-4">
						<div class="card m-b-30">
							<div class="card-header bg-dark text-white">
								Legal Entity Details
								<?php if (in_array('legal_details_edit', $user_details->permissions)) { ?>
									<i class="mdi mdi-lead-pencil" data-toggle="modal" data-target="#LegalEntityModal" style="float: right;"></i>
								<?php } ?>
							</div><br>

							<div class="list-group list  list-group-flush">
								<div class="list-group-item">
									<strong>Legal Business Name:</strong>
									<p class="text_right"><?= ucwords($legalEntity->legal_name ?? ''); ?></p>
								</div>
								<div class="list-group-item">
									<strong>GST No:</strong>
									<p class="text_right"><?= $legalEntity->legal_gstno ?? ''; ?></p>
								</div>
								<div class="list-group-item">
									<strong>Address:</strong>
									<p class="text_right"><?= $legalEntity->legal_address ?? ''; ?></p>
								</div>
								<div class="list-group-item">
									<strong>City :</strong>
									<p class="text_right"><?= $legalEntity->legal_city ?? ''; ?></p>
								</div>
								<div class="list-group-item">
									<strong>State:</strong>
									<p class="text_right"><?= $legalEntity->legal_state ?? ''; ?></p>
								</div>
								<div class="list-group-item">
									<strong>Pincode:</strong>
									<p class="text_right"><?= $legalEntity->legal_pincode ?? ''; ?></p>
								</div>
							</div>
						</div>
						<div class="card m-b-30">
							<div class="card-header bg-dark text-white">
								KYC Details
								<?php if (in_array('users_kyc_details', $user_details->permissions)) { ?>
									<i class="mdi mdi-lead-pencil" data-toggle="modal" data-target="#KYCdetailsModal" style="float: right;"></i>
								<?php } ?>
							</div><br>
							<?php
							if (
								$singleuserdetail[0]->companytype == 'Private Limited Company' ||
								$singleuserdetail[0]->companytype == 'Partnership' ||
								$singleuserdetail[0]->companytype == 'Limited Liability Partnership' ||
								$singleuserdetail[0]->companytype == 'Public Limited Company'
							) {
							?>
								<div class="list-group list  list-group-flush">
									<div class="list-group-item">
										<strong>Company Type:</strong>
										<p class="text_right"><?= ucwords($singleuserdetail[0]->companytype); ?></p>
									</div>
									<div class="list-group-item">
										<strong>Pan Card Name:</strong>
										<p class="text_right"><?= $singleuserdetail[0]->cmppanname; ?></p>
									</div>
									<div class="list-group-item">
										<strong>Pan Card No:</strong>
										<p class="text_right"><?= $singleuserdetail[0]->cmppanno; ?></p>
									</div>
									<br>
									<?php
									if (!empty($singleuserdetail[0]->pancarddocumentimage)) {
									?>
										<div class="text-center">
											<div class="avatar avatar-xl" style="height:35px !important;">
												<a class="btn btn-success btn-sm" href="<?php echo $singleuserdetail[0]->pancarddocumentimage; ?>" target="_blank">View</a>
											</div>
										</div>
									<?php
									} else {
									?>
										<div class="text-center">
											<div class="avatar avatar-xl">
												<img class="avatar-img rounded-circle" src="<?php echo base_url(); ?>assets/seller_company_logo/dummy_img.jpg">
											</div>
										</div><br>
									<?php
									}
									?>
									<div>
										<strong style="text-align: center;display: block;background: green;color: #fff;margin: 10px;">Second Details</strong>
									</div>

									<div class="list-group-item">
										<strong>Document Type:</strong>
										<p class="text_right"><?= $singleuserdetail[0]->document_type; ?></p>
									</div>
									<div class="list-group-item">
										<strong>Document Id:</strong>
										<p class="text_right"><?= $singleuserdetail[0]->kycdoc_id; ?></p>
									</div>
									<div class="list-group-item">
										<strong>Document Name:</strong>
										<p class="text_right"><?= $singleuserdetail[0]->kycdoc_name; ?></p>
									</div>
									<br>
									<div class="text-center">
										<div class="avatar avatar-xl">

											<a class="btn btn-success btn-sm" href="<?php echo $singleuserdetail[0]->documentimage; ?>" target="_blank">View</a>
										</div>
									</div>
								</div>
							<?php
							} else {
							?>
								<div class="list-group list  list-group-flush">

									<?php
									if (!empty($singleuserdetail[0]->documentimage)) {
									?>
										<div class="text-center">
											<div class="avatar avatar-xl">

												<a class="btn btn-success btn-sm" href="<?php echo $singleuserdetail[0]->documentimage ?>" target="_blank">View</a>
											</div>
										</div>
									<?php
									} else {
									?>
										<div class="text-center">
											<div class="avatar avatar-xl">
												<img class="avatar-img rounded-circle" src="<?php echo base_url(); ?>assets/seller_company_logo/dummy_img.jpg">
											</div>
										</div><br>
									<?php
									}
									?>
									<div class="list-group-item">
										<strong>Company Type:</strong>
										<p class="text_right"><?= ucwords($singleuserdetail[0]->companytype); ?></p>
									</div>
									<div class="list-group-item">
										<strong>Document Type:</strong>
										<p class="text_right"><?= $singleuserdetail[0]->document_type; ?></p>
									</div>
									<div class="list-group-item">
										<strong>Document Id:</strong>
										<p class="text_right"><?= $singleuserdetail[0]->kycdoc_id; ?></p>
									</div>
									<div class="list-group-item">
										<strong>Document Name:</strong>
										<p class="text_right"><?= $singleuserdetail[0]->kycdoc_name; ?></p>
									</div>
								</div>
							<?php
							}
							?>
						</div>
						<!-- <div class="card m-b-30">
							<div class="card-header bg-dark text-white">
								Notes
								<?php //if (in_array('users_kyc_details', $user_details->permissions)) { 
								?>
								<a href="#" style="float: right;" data-toggle="modal" data-target="#NotedetailsModal" class="btn btn-sm btn-info"><i class="mdi mdi-plus"></i></a>

								<?php //} 
								?>
							</div>
							<br>
							<?php if (!empty($notes)) { ?>
								<div class="list-group list  list-group-flush" style="height: 200px; overflow-y: scroll;">
									<?php
									foreach ($notes as $key => $value) {
									?>
										<div class="list-group-item">
											<strong><?php echo ucfirst($value->category_issue); ?></strong> <span style="float: right;font-size: 10px;font-weight:bold; text-align:right;">Post by :<?php echo ucfirst($value->fname . ' ' . $value->lname); ?> <br><?php echo date("j-M-Y h:i:s", $value->created); ?></span><br>
											<p style="padding-left:5px;"><?php echo html_entity_decode($value->remarks); ?></p>


										</div>
									<?php
									} ?>

								</div> <?php } else { ?>
								<div class="list-group-item">
									<a href="#" style="float:initial;font-size:10px;font-weight:bold; margin-left:160px;" data-toggle="modal" data-target="#NotedetailsModal" class="btn btn-sm btn-info">ADD NOTES</a>
								</div>
							<?php } ?>
						</div> -->
						<div class="card m-b-30">
							<div class="card-header bg-dark text-white">
								Referrial Details
							</div><br>
							<div class="list-group list  list-group-flush">
								<div class="list-group-item">
									<strong>Current Status</strong>
									<?php
									if ($singleuserdetail[0]->can_refer == '1') {
									?>
										<span style="float: right;color: #00cc99;font-size: 14px;font-weight: bold;">Enabled</span>
									<?php
									} else {
									?>
										<span style="float: right;color: #f2545b;font-size: 14px;font-weight: bold;">Disabled</span>
									<?php
									}
									?>
									<br><br>
									<?php if (in_array('users_referral_details', $user_details->permissions)) { ?>
										<?php
										if ($singleuserdetail[0]->can_refer == '0' && $singleuserdetail[0]->cod == null && $singleuserdetail[0]->prepaid == null) {
										?>
											<a href="#" data-toggle="modal" data-target="#enable_model" class="btn btn-outline-primary btn-sm">Enable</a>
										<?php
										} elseif ($singleuserdetail[0]->can_refer == '1' && $singleuserdetail[0]->cod != null && $singleuserdetail[0]->prepaid != null) {
										?>
											<span>COD Price: <?php echo $singleuserdetail[0]->cod; ?></span><br>
											<span>Prepaid Price: <?php echo $singleuserdetail[0]->prepaid; ?></span><br><br>
											<a href="#" data-toggle="modal" data-target="#update_model" class="btn btn-outline-primary btn-sm">Update</a>
											<a href="<?php echo base_url('admin/users/disablesellerreferrial/' . $singleuserdetail[0]->sellerid); ?>" class="btn btn-outline-primary btn-sm">Disable</a>
										<?php
										} elseif ($singleuserdetail[0]->can_refer == '1' && $singleuserdetail[0]->cod == null && $singleuserdetail[0]->prepaid == null) {
										?>
											<span>COD Price: <?php echo $singleuserdetail[0]->cod; ?></span><br>
											<span>Prepaid Price: <?php echo $singleuserdetail[0]->prepaid; ?></span><br><br>
											<a href="#" data-toggle="modal" data-target="#update_model" class="btn btn-outline-primary btn-sm">Update</a>
											<a href="<?php echo base_url('admin/users/disablesellerreferrial/' . $singleuserdetail[0]->sellerid); ?>" class="btn btn-outline-primary btn-sm">Disable</a>
										<?php
										} else {
										?>
											<span>COD Price: <?php echo $singleuserdetail[0]->cod; ?></span><br>
											<span>Prepaid Price: <?php echo $singleuserdetail[0]->prepaid; ?></span><br><br>
											<a href="#" data-toggle="modal" data-target="#update_model" class="btn btn-outline-primary btn-sm">Update</a>
											<a href="<?php echo base_url('admin/users/enablesellerreferrial/' . $singleuserdetail[0]->sellerid); ?>" class="btn btn-outline-primary btn-sm">Enable</a>
										<?php
										}
										?>
									<?php } ?>
								</div>
							</div>
						</div>
						<!---->
						<div class="card m-b-30">
							<div class="card-header bg-dark text-white">
								Remittance
							</div><br>
							<div class="list-group list  list-group-flush">

								<div class="list-group-item">
									<strong>Remittance Cycle</strong>
									<?php
									if (!empty($singleuserdetail[0]->remittance_cycle)) {
										echo '<span style="float: right;color: #00cc99;font-size: 14px;font-weight: bold;">T+' . $singleuserdetail[0]->remittance_cycle . '</span>';
									}
									?>
									<br><br>
									<strong>Remittance On Hold</strong>
									<?php
									echo '<span style="float: right;color: #00cc99;font-size: 14px;font-weight: bold;">' . $singleuserdetail[0]->remittance_on_hold_amount . '</span>';
									?>
									<br><br>
									<strong>Remittance Freeze</strong>
									<?php
									$freeze = !empty($singleuserdetail[0]->freeze_remittance) ? $singleuserdetail[0]->freeze_remittance : '0';
									if ($freeze == '0') {
										echo '<span style="float: right;color: #00cc99;font-size: 14px;font-weight: bold;">No</span>';
									} else {
										echo '<span style="float: right;color: #00cc99;font-size: 14px;font-weight: bold;">Yes</span>';
									}
									?>
									<?php $wallet_adjustment_cycle = "";
									if ((isset($singleuserdetail[0]->wallet_adjustment_cycle)) && ($singleuserdetail[0]->wallet_adjustment_cycle == '1')) {
										$wallet_adjustment_cycle = "Daily";
									}
									if ((isset($singleuserdetail[0]->wallet_adjustment_cycle)) && ($singleuserdetail[0]->wallet_adjustment_cycle == '2')) {
										$wallet_adjustment_cycle = "Twice a week";
									}
									if ((isset($singleuserdetail[0]->wallet_adjustment_cycle)) && ($singleuserdetail[0]->wallet_adjustment_cycle == '3')) {
										$wallet_adjustment_cycle = "Thrice a week";
									}

									$settle_wallet = "";
									$settle_bank = "";
									if ((isset($singleuserdetail[0]->remitence_term)) && (!empty($singleuserdetail[0]->remitence_term))) {

										$remitence_term = unserialize($singleuserdetail[0]->remitence_term);
										$settle_wallet = isset($remitence_term['settle_wallet']) ? $remitence_term['settle_wallet'] : "";
										$settle_bank  = isset($remitence_term['settle_bank']) ? $remitence_term['settle_bank'] : "";
									} ?>
									<?php
									if (isset($singleuserdetail[0]->early_cod_charges)) {
										$eary_cod_charges  = isset($singleuserdetail[0]->early_cod_charges) ? $singleuserdetail[0]->early_cod_charges : "0";
									}
									?>
									<br><br>
									<strong>Wallet Adjustment Cycle</strong>
									<?php
									echo '<span style="float: right;color: #00cc99;font-size: 14px;font-weight: bold;">' . $wallet_adjustment_cycle . '</span>';
									?>
									<br><br>
									<strong>Remitance term</strong>
									<ul>
										<li>Settled to wallet(%)
											<?php
											echo '<span style="float: right;color: #00cc99;font-size: 14px;font-weight: bold;">' . $settle_wallet . '</span>';
											?></li>
										<li>Settled to Bank(%)
											<?php
											echo '<span style="float: right;color: #00cc99;font-size: 14px;font-weight: bold;">' . $settle_bank . '</span>';
											?></li>


									</ul>




								</div>
								<form method="post" id="freeze_remittance" action="<?php echo base_url(); ?>admin/users/freeze_remittance/<?php echo $singleuserdetail[0]->sellerid; ?>">

									<div class="list-group-item">

										<div class="form-group">
											<?php if (in_array('users_remittance_cycle', $user_details->permissions)) { ?>
												<div class="custom-control custom-checkbox custom-control-inline">
													<?= form_checkbox('freeze_remittance', '1', set_value('freeze_remittance', (isset($singleuserdetail[0]->freeze_remittance) && $singleuserdetail[0]->freeze_remittance == '1') ? true : false), 'class="custom-control-input" id="customCheckDisabled1"'); ?>
													<label class="custom-control-label" for="customCheckDisabled1">Freeze Remittance</label>
												</div><br><br>

												<strong>Remittance Cycle:</strong>
												<select name="remittance_cycle" class="form-control">
													<option <?php if ($singleuserdetail[0]->remittance_cycle == '1') { ?> selected <?php } ?> value="1">T+1</option>
													<option <?php if ($singleuserdetail[0]->remittance_cycle == '2') { ?> selected <?php } ?> value="2">T+2</option>
													<option <?php if ($singleuserdetail[0]->remittance_cycle == '3') { ?> selected <?php } ?> value="3">T+3</option>
													<option <?php if ($singleuserdetail[0]->remittance_cycle == '4') { ?> selected <?php } ?> value="4">T+4</option>
													<option <?php if ($singleuserdetail[0]->remittance_cycle == '5') { ?> selected <?php } ?> value="5">T+5</option>
													<option <?php if ($singleuserdetail[0]->remittance_cycle == '6') { ?> selected <?php } ?> value="6">T+6</option>
													<option <?php if ($singleuserdetail[0]->remittance_cycle == '7') { ?> selected <?php } ?> value="7">T+7</option>
													<option <?php if ($singleuserdetail[0]->remittance_cycle == '8') { ?> selected <?php } ?> value="8">T+8</option>
													<option <?php if ($singleuserdetail[0]->remittance_cycle == '9') { ?> selected <?php } ?> value="9">T+9</option>
													<option <?php if ($singleuserdetail[0]->remittance_cycle == '10') { ?> selected <?php } ?> value="10">T+10</option>
												</select>
											<?php } ?>
											<?php

											if (in_array('wallet_adjustment_cycle', $user_details->permissions)) {
												$selected = "";
												if ((isset($singleuserdetail[0]->wallet_adjustment_cycle)) && ($singleuserdetail[0]->wallet_adjustment_cycle == '0')) {
													$selected = "selected";
												} ?>
												<br><strong>Wallet Adjustment Cycle</strong>
												<select name="wallet_adjustment_cycle" class="form-control">
													<option value="1" <?php if ((isset($singleuserdetail[0]->wallet_adjustment_cycle)) && ($singleuserdetail[0]->wallet_adjustment_cycle == '1')) { ?> selected <?php } ?>>Daily</option>
													<option value="2" <?php if ((isset($singleuserdetail[0]->wallet_adjustment_cycle)) && ($singleuserdetail[0]->wallet_adjustment_cycle == '2')) { ?> selected <?php }
																																																			echo $selected; ?>>Twice a week</option>
													<option value="3" <?php if ((isset($singleuserdetail[0]->wallet_adjustment_cycle)) && ($singleuserdetail[0]->wallet_adjustment_cycle == '3')) { ?> selected <?php } ?>>Thrice a week</option>
												</select>
											<?php } ?>
											<?php
											if (in_array('remittance_term', $user_details->permissions)) {
												$remitence_term = array();
												if (isset($singleuserdetail[0]->remitence_term) && !empty($singleuserdetail[0]->remitence_term)) {
													$remitence_term = unserialize($singleuserdetail[0]->remitence_term);
												} ?>
												<br><strong>Remittance Term</strong><br>
												<div class="row">
													<div class="col">
														<label for="exampleInputEmail1">Settled to wallet(%)</label>
														<input id="settel_to_wallet" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1').replace(/^0[^.]/, '0');" type="text" name="remitence_term[]" value="<?= isset($remitence_term['settle_wallet']) ? $remitence_term['settle_wallet'] : "" ?>" placeholder="enter in percentage" maxlength="3" class="form-control input-sm">
													</div>
													<div class="col">
														<label for="exampleInputEmail1">Settled to Bank(%)</label>
														<input id="settel_to_bank" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1').replace(/^0[^.]/, '0');" maxlength="3" name="remitence_term[]" value="<?= isset($remitence_term['settle_wallet']) ? $remitence_term['settle_bank'] : "" ?>" placeholder="enter in percentage" class="form-control input-sm">
													</div>

												</div>
											<?php } ?>
											<!-- start -->
											<?php
											if (in_array('early_cod_charges', $user_details->permissions)) { ?>
												<!-- <br><strong>Early Cod Charges</strong><br> -->
												<br>
												<div class="row">
													<div class="col">
														<label for="exampleInputEmail1">Early Cod Charges</label>
														<input id="settel_to_cod" type="text" maxlength="8" name="eary_cod_charges" value="<?= isset($eary_cod_charges) ? $eary_cod_charges : "" ?>" placeholder="enter in percentage" class="form-control input-sm">
													</div>
												</div>

											<?php } ?>

											<!-- end -->
											<?php if ((in_array('users_remittance_cycle', $user_details->permissions)) || (in_array('wallet_adjustment_cycle', $user_details->permissions)) || (in_array('remittance_term', $user_details->permissions))) { ?>
												<div class="col-sm-2" style="margin-left: -14px;margin-top: 10px;">
													<button type="submit" class="btn btn-outline-primary btn-sm">Submit</button>
												</div>
											<?php } ?>
										</div>

									</div>

								</form>
							</div>
						</div>

						<!---->

						<!-- Section for Weigth TAT Management -->
						<?php if (in_array('enable_tat_management', $user_details->permissions)) { ?>

							<div class="card m-b-30">
								<div class="card-header bg-dark text-white">
									Manage Weight TAT
								</div>
								<div class="list-group list  list-group-flush">

									<div class="list-group-item">
										<form method="post" action="<?php echo base_url(); ?>admin/users/save_tat_weight/<?php echo $singleuserdetail[0]->sellerid; ?>">
											<div class="form-group">
												<strong>Enter TAT:</strong>


												<select name="tat_time_limt" id="tat_time_limt" class="form-control">
													<option value=''>Select</option>
													<?php
													if (!empty($get_dispute_time_limit)) {
														$wet_val = $get_dispute_time_limit->time_limt;
													} else {
														$wet_val = '7';
													}

													for ($i = 1; $i <= 60; $i++) { ?>
														<option value='<?php echo $i; ?>' <?php if ($i == $wet_val) {
																								echo "selected";
																							}  ?>><?php echo $i; ?></option>
													<?php } ?>

												</select>

												<!--<input type="text" onkeypress="return onlyNumberKey(event)" name="tat_time" class="form-control" >--->
												<div class="col-sm-2" style="margin-left: -14px;margin-top: 10px;">
													<button type="submit" class="btn btn-outline-primary btn-sm">Submit</button>
												</div>
											</div>
										</form>
									</div>

								</div>
							</div>
						<?php } ?>

						<!--- end section for weight tat -->



						<div class="card m-b-30">
							<div class="card-header bg-dark text-white">
								Wallet Limit
							</div><br>
							<div class="list-group list list-group-flush">
								<div class="list-group-item">
									<strong>Invoice Mode</strong>
									<span style="float: right;color: #00cc99;font-size: 14px;font-weight: bold;">
										<?php
										echo ($singleuserdetail[0]->is_postpaid == '0') ? 'Prepaid' : 'Postpaid';
										?>
									</span>
									<br>
									<strong>Limit</strong>
									<?php
									echo '<span style="float: right;color: #00cc99;font-size: 14px;font-weight: bold;">' . set_value('wallet_limit', !empty($singleuserdetail[0]->wallet_limit) ? $singleuserdetail[0]->wallet_limit : '0') . '</span>';
									?>
									<br>
								</div>
								<?php if (in_array('users_wallet_limit', $user_details->permissions)) { ?>
									<div class="list-group-item">
										<form method="post" action="<?php echo base_url(); ?>admin/users/wallet_limit/<?php echo $singleuserdetail[0]->sellerid; ?>">
											<?php if (in_array('update_invoice_mode', $user_details->permissions)) { ?>
												<div class="form-group">
													<label>Invoice Mode</label>
													<input type="hidden" value="invoice_mode" name="invoicemode">
													<select name="is_postpaid" class="form-control js-select2" style="width: 100% !important;">
														<option <?php if ($singleuserdetail[0]->is_postpaid == '0') { ?> selected <?php } ?> value="0">Prepaid</option>
														<option <?php if ($singleuserdetail[0]->is_postpaid == '1') { ?> selected <?php } ?> value="1">Postpaid</option>
													</select>
												</div>
											<?php
											}
											?>
											<div class="form-group">
												<input type="text" name="wallet_limit" required="" class="form-control" placeholder="Enter Limit Amount" value="<?= set_value('wallet_limit', !empty($singleuserdetail[0]->wallet_limit) ? $singleuserdetail[0]->wallet_limit : '0'); ?>" />
											</div>
											<div class="col-sm-2" style="margin-top: 10px;float: right;padding: 0px !important;">
												<button type="submit" class="btn btn-outline-primary btn-sm">Submit</button>
											</div>
										</form>
									</div>
								<?php } ?>
							</div>
						</div>


						<div class="card m-b-30">
							<div class="card-header bg-dark text-white">
								User Category
							</div><br>
							<div class="list-group list list-group-flush">
								<div class="list-group-item">
									<strong>Seller Category</strong>
									<?php
									echo '<span style="float: right;color: #00cc99;font-size: 14px;font-weight: bold;">' . ucfirst(set_value('support_category', !empty($singleuserdetail[0]->support_category) ? $singleuserdetail[0]->support_category : '')) . '</span>';



									?>
									<br><br>
								</div>
								<div class="list-group-item">
									<strong>Seller Cluster</strong>
									<?php

									$cluster_html = '';

									$cluster_html .= '<span style="float: right;color: #00cc99;font-size: 14px;font-weight: bold;">';

									if (isset($singleuserdetail[0]->seller_region))
										$cluster_html .= ucwords($singleuserdetail[0]->seller_region);

									$cluster_html .=  ' - ';

									if (isset($singleuserdetail[0]->seller_territory))
										$cluster_html .=  $singleuserdetail[0]->seller_territory;

									$cluster_html .=  '</span>';

									echo $cluster_html;

									?>
									<br><br>
								</div>

								<?php if (in_array('user_category_update', $user_details->permissions)) { ?>
									<form method="post" id="FormId_support_category">
										<div class="list-group-item">
											<?php if (!empty($singleuserdetail[0]->sellerid)) { ?>
												<input type="hidden" name="seller_id" id="seller_id" value="<?= $singleuserdetail[0]->sellerid; ?>">
											<?php } ?>
											<div class="form-group">
												<strong>Seller Category</strong>
												<select name="support_category" class="form-control">
													<option value="">Select</option>
													<?php $support_categories = $this->config->item('seller_categories');
													foreach ($support_categories as $sc) {
														$sc = strtolower($sc);
													?>
														<option <?php if ($singleuserdetail[0]->support_category == $sc) { ?> selected <?php } ?> value="<?= $sc ?>"><?= strtoupper($sc) ?></option>
													<?php
													}
													?>

												</select><br>
											</div>

											<div class="form-group">
												<strong>Seller Cluster</strong>
												<select name="seller_cluster" class="form-control">
													<option value="">Select</option>
													<?php $support_clusters = $this->config->item('seller_clusters');
													foreach ($support_clusters as $sc_key =>  $sc) {
													?>
														<option disabled>-----<?= strtoupper($sc_key) ?>-----</option>
														<?php
														foreach ($sc as $sc_child_key => $sc_child_value) {
														?>
															<option <?php if (isset($singleuserdetail[0]->seller_territory) && $singleuserdetail[0]->seller_territory == $sc_child_key) { ?> selected <?php } ?> value="<?= $sc_child_key ?>"><?= strtoupper($sc_child_value) ?></option>
													<?php
														}
													}
													?>
												</select><br>
											</div>



											<div class="form-group d-flex justify-content-between">
												<strong>Is Premium Seller</strong>
												<span><input type="checkbox" id="seller_premium" name="seller_premium" value="1" <?php if ($singleuserdetail[0]->seller_premium_status == '1') echo 'checked="checked"'; ?>>
												</span>
											</div>

											<div class="col-sm-2" style="margin-top: 10px;float: right;padding: 0px!important;">
												<button type="submit" id="user_support_form" class="btn btn-outline-primary btn-sm">Submit</button>
											</div>

										</div>
									</form>
								<?php
								}
								?>


							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>



<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('providerForm');
    const editBtn = document.getElementById('editBtn');
    const saveBtn = document.getElementById('saveBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const viewElements = document.querySelectorAll('.view-mode');
    const editInputs = document.querySelectorAll('.edit-mode');

	const baseUrl = '<?php echo base_url(); ?>'

    const token = localStorage.getItem('token');
    const seller_id = (<?php echo $singleuserdetail[0]->sellerid; ?>);

	const fetchServiceProvider = async () => {
		await fetch(`${baseUrl}index.php/api/CommunicationSettings/get_service_provider`, {
			method: "POST",
			headers: {
				Authorization: `Bearer ${token}`,
				'Content-Type': 'application/json',
			},
			body: JSON.stringify({
				seller_id: seller_id
			})
		})
		.then(res => res.json())
		.then(data => {
				if (data.status && data.service_provider) {
					// Populate view and input fields
					const provider = data.service_provider.trim();
					
					const radio = form.querySelector(`input[name="message_provider"][value="${provider}"]`);
					if(radio){
						radio.checked = true;
					}else{
						alert("Failed to fetch radio button")
					}
				} else {
					alert('Failed to fetch data');
				}
			})
		.catch(err => {
			alert("Failed to fetch Service Provider");
			console.error('Error fetching service provider:', err);
		});
	}
	fetchServiceProvider();

	form.addEventListener("change", (e) => {
		// Only respond to changes on the message_provider radio group
		if (e.target.name === "message_provider") {
			const selectedProvider = e.target.value;
			fetch(`${baseUrl}index.php/api/CommunicationSettings/update_service_provider`, {
				method: "POST",
				headers: {
					Authorization: `Bearer ${token}`, // your token
					"Content-Type": "application/json",
				},
				body: JSON.stringify({
					seller_id: seller_id, // your seller ID
					serviceprovider: selectedProvider
				}),
			})
			.then(res => res.json())
			.then(data => {
					if (data.status) {
						// Populate view and input fields
						alert("Service Provider changed successfully");
					} else {
						alert("Failed to update service provider");
						fetchServiceProvider();
					}
				})
			.catch(err => {
				alert("Failed to update service provider");
			});
		}
	});

	const fetchTableData = async () => {
		let pricingData = { bundled: {}, individual: [] };
		try {
			// Run both fetches in parallel
			const [indResp, bunResp] = await Promise.all([
				fetch(`${baseUrl}index.php/api/CommunicationSettings/get_seller_individual_pricings`, {
					method: "POST",
					headers: {
						"Content-Type": "application/json",
						"Authorization": "Bearer " + token
					},
					body: JSON.stringify({ seller_id: seller_id })
				}).then(res => res.json()),

				fetch(`${baseUrl}index.php/api/CommunicationSettings/get_seller_bundled_pricings`, {
					method: "POST",
					headers: {
						"Content-Type": "application/json",
						"Authorization": "Bearer " + token
					},
					body: JSON.stringify({ seller_id: seller_id })
				}).then(res => res.json())
			]);

			let sellerPricingData = { bundled: {}, individual: [] };
			if (indResp.status) sellerPricingData.individual = indResp.data;
			if (bunResp.status) sellerPricingData.bundled = bunResp.data;

			if(!indResp.status && !bunResp.status){
				console.log("Hello");
				
				try {
					const res = await fetch(`${baseUrl}index.php/api/CommunicationSettings/get_seller_pricings_service`, {
					headers: { Authorization: `Bearer ${token}` }
					});
					const json = await res.json();
					if (json.status) {
					sellerPricingData.individual = json.data.individual;
					sellerPricingData.bundled = json.data.bundled?.[0];
					console.log(sellerPricingData);
					} else {
					alert("Failed to load pricing data.");
					}
				} catch (error) {
					alert("API call failed.");
				} 
			}
			renderSellerTables(sellerPricingData);
		}catch(err){
			console.error(err);
		}
	}
	fetchTableData();
	
	function renderSellerTables(sellerPricingData) {
        console.log(sellerPricingData);
        const idMap = {
            "new": "seller_new",
            "confirmation acknowledgement": "seller_confirmation_acknowledgement",
            "pending pickup": "seller_pending_pickup",
            "in transit": "seller_in_transit",
            "out for delivery": "seller_out_for_delivery",
            "delivered": "seller_delivered",
            "exception": "seller_exception",
            "rto in transit": "seller_rto"
        };

        sellerPricingData.individual.forEach(item => {
            const prefix = idMap[item.status];
            if (prefix) {
                document.getElementById(`${prefix}_sms`).textContent  = item.sms;
                document.getElementById(`${prefix}_email`).textContent  = item.email;
                document.getElementById(`${prefix}_whatsapp`).textContent  = item.whatsapp;
                document.getElementById(`${prefix}_ivr`).textContent  = item.ivr;
            }
        });

        if (sellerPricingData.bundled) {
            const bundled = sellerPricingData.bundled;
            document.getElementById("seller_bundled_sms").textContent  = bundled.sms ? bundled.sms : "" ;
            document.getElementById("seller_bundled_email").textContent  = bundled.email ? bundled.email : "";
            document.getElementById("seller_bundled_whatsapp").textContent  = bundled.whatsapp ? bundled.whatsapp : "";
            document.getElementById("seller_bundled_ivr").textContent  = bundled.ivr ? bundled.ivr : "";
        }
    }


	function getStatus(){
		fetch(`${baseUrl}index.php/api/CommunicationSettings/get_communication_plan_seller`, {
			method: "POST",
			headers: {
				"Content-Type": "application/json",
				"Authorization": "Bearer " + token
			},
			body: JSON.stringify({ seller_id: seller_id })
		})
		.then(res => res.json())
		.then(resp => {
			if (resp.communication_plan == "bundled") {
				// highlight bundle
				document.getElementById("bundleTable").classList.add("active-plan");
				document.getElementById("perStatusTable").classList.add("inactive-plan");

				// fill bundled values...
				if (resp.communication_specific1 == "sms") {
					document.getElementById("seller_bundled_sms").style.backgroundColor = "#4bbf69";
					document.getElementById("seller_bundled_sms").style.color = "white";
				}
				if (resp.communication_specific2 == "email") {
					document.getElementById("seller_bundled_email").style.backgroundColor = "#4bbf69";
					document.getElementById("seller_bundled_email").style.color = "white";
				}
				if (resp.communication_specific3 == "whatsapp") {
					document.getElementById("seller_bundled_whatsapp").style.backgroundColor = "#4bbf69";
					document.getElementById("seller_bundled_whatsapp").style.color = "white";
				}
				if (resp.communication_specific4 == "ivr") {
					document.getElementById("seller_bundled_ivr").style.backgroundColor = "#4bbf69";
					document.getElementById("seller_bundled_ivr").style.color = "white";
				}
			} else {
				// highlight per-status
				document.getElementById("perStatusTable").classList.add("active-plan");
				document.getElementById("bundleTable").classList.add("inactive-plan");

				// fetch and fill per-status table
				fetch(`${baseUrl}index.php/api/CommunicationSettings/getSettings_seller`, {
					method: "POST",
					headers: {
						"Content-Type": "application/json",
						"Authorization": "Bearer " + token
					},
					body: JSON.stringify({ seller_id: seller_id })
				})
				.then(res => res.json())
				.then(res => {
					if(res.status){
						const data = res.data;
						const statusMap = {
							"new": "seller_new",
							"confirmation acknowledgement": "seller_confirmation_acknowledgement",
							"pending pickup": "seller_pending_pickup",
							"in transit": "seller_in_transit",
							"out for delivery": "seller_out_for_delivery",
							"delivered": "seller_delivered",
							"ndr": "seller_exception",
							"rto in transit": "seller_rto",
							"exception": "seller_exception"
						};

						data.forEach(item => {
							const prefix = statusMap[item.status.toLowerCase()];
							if(prefix){
								["sms","email","whatsapp","ivr"].forEach(key => {
									const td = document.getElementById(`${prefix}_${key}`);
									if(td){
										td.style.backgroundColor = (item[key] === "yes") ? "#4bbf69" : "#f8d7da";
										td.style.color = "white";
									}
								});
							}
						});
					}
				});
			}

		})
	}
	getStatus();



});
</script>

<style>
/* Compact table styling */
#pricing table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 0.5rem;
  font-family: Arial, sans-serif;
  font-size: 13px;
}

/* Compact header */
#pricing th {
  background-color: #f0f0f0;
  text-align: left;
  padding: 6px 8px;
  border-bottom: 1px solid #ccc;
}

/* Compact cell */
#pricing td {
  padding: 6px 8px;
  vertical-align: middle;
  border-bottom: 1px solid #e0e0e0;
}

/* View mode with minimal padding */
#pricing .view-mode {
  margin: 0;
  padding: 4px 6px;
  background-color: #f9f9f9;
  border-radius: 3px;
  min-height: 30px;
  font-weight: 500;
  font-size: 13px;
  line-height: 1.2;
}

/* Edit mode input - compact */
#pricing .edit-mode {
  width: 40%;
  padding: 4px 6px;
  font-size: 13px;
  border-radius: 3px;
  border: 1px solid #ccc;
  line-height: 1.2;
}

#pricing .edit-mode:focus {
  border-color: #007bff;
  outline: none;
}

/* Reduce spacing between buttons */
#pricing .mt-3 {
  margin-top: 12px;
}

/* Reduce button gap */
#pricing .btn + .btn {
  margin-left: 6px;
}

.active-plan {
    border: 1.5px solid #28a745; /* softer green */
    box-shadow: 0 2px 6px rgba(40, 167, 69, 0.15); /* subtle shadow */
    background-color: #f8fdf9; /* very light green background */
    border-radius: 6px; /* smooth corners */
    transition: all 0.3s ease-in-out; /* smooth hover/active transition */
}

.inactive-plan {
    border: 1px solid #e0e0e0;
    background-color: #fafafa;
    opacity: 0.8;
    border-radius: 6px;
}

.active-plan:hover {
    box-shadow: 0 4px 10px rgba(40, 167, 69, 0.25); /* slight lift on hover */
}

.inactive-plan:hover {
    opacity: 0.9; /* hover hint */
}

/* Internal tables */
.table {
  width: 100%;
  border-collapse: collapse;
  font-family: Arial, sans-serif;
  font-size: 14px;
  margin-bottom: 0.5rem;
}

/* Table header */
.table thead th {
  background-color: #f4f6f8; /* softer light gray */
  font-weight: 600;
  text-align: center;
  padding: 10px;
  border: 1px solid #dee2e6;
  color: #333;
}

/* Table rows */
.table tbody td {
  padding: 10px;
  border: 1px solid #dee2e6;
  text-align: center;
  vertical-align: middle;
  font-size: 13px;
  font-weight: 500;
}

/* Row hover effect */
.table tbody tr:hover {
  background-color: #f9fdfb; /* very light greenish hover */
  transition: background-color 0.2s ease-in-out;
}

/* Status cells with colors already applied via JS */
.table tbody td[style] {
  border-radius: 4px;
  font-weight: 600;
}

/* First column (labels/status names) */
.table tbody td:first-child {
  text-align: left;
  font-weight: 600;
  color: #444;
  background-color: #fdfdfd;
}




</style>

