<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">Request for bank account update</h4>
</div>
<!-- END PAGE-HEADER -->
<div class="main-container container-fluid">

<!-- START ROW-1 -->
<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">Bank account update</h3>
			</div>
			<div class="card-body">
				<form method="post" action="<?= current_url(); ?>" enctype="multipart/form-data">
				<div class="row">
					<div class=" col-md-12">
						<div class="panel panel-success">
								<div class="panel-body">
									<div class="card-body">
											<div class="row">
												<div class="col-md-12">
													<div class="form-row">
														<div class="form-group col-md-6 required">
															<label class="control-label">Account Name</label>
															<input type="text" name="cmp_accntholder" class="form-control" required />
														</div>

														<div class="form-group col-md-6 required">
															<label class="control-label">Account No.</label>
															<input type="text"  name="cmp_accno" class="form-control" required />
														</div>
													</div>

													<div class="form-row">
														<div class="form-group col-md-6 required">
															<label class="control-label">Bank Name</label>
															<input type="text" name="cmp_bankname" class="form-control" required />
														</div>

														<div class="form-group col-md-6 required">
															<label class="control-label">Bank Branch</label>
															<input type="text" name="cmp_bankbranch" class="form-control" required />
														</div>
													</div>

													<div class="form-row">
														<div class="form-group col-md-6 required">
															<label class="control-label">Account Type</label>
															<select required class="form-control" name="bankacctype">
																<option id="hidetandcpopup" value="">Select Account Type</option>
																<option value="Current Account">Current Account</option>
																<option id="showtandcpopup" value="Saving Account">Saving Account</option>
															</select>
														</div>

														<div class="form-group col-md-6 required">
															<label class="control-label">IFSC Code</label>
															<input type="text" name="cmp_accifsc" class="form-control" required>
														</div>
													</div>
													
													<div class="form-row">
														<div class="form-group col-md-6 required">
															<label class="control-label">Upload Cancelled Cheque</label>
															<input type="file" class="form-control" required name="chequeimage" id="cancelcheque">
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="clearfix"></div>
						<div class="btn-bottom-toolbar text-right">
							<button type="submit" data-form="#project_form" class="btn btn-info" autocomplete="off" data-loading-text="Please wait...">Save</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

</div>
<!-- END ROW-1 -->

</div>
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script>
	function readURL(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function(e) {
				$('#cancelchequepreview').attr('src', e.target.result);
			}

			reader.readAsDataURL(input.files[0]);
		}
	}

	$("#cancelcheque").change(function() {
		readURL(this);
	});
</script>