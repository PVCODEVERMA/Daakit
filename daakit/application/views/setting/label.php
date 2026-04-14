<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">Label Configuration</h4>
</div>
<!-- END PAGE-HEADER -->
<div class="main-container container-fluid">

<!-- START ROW-1 -->
<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">Label Setting</h3>
			</div>
			<div class="card-body">
				<form method="post" action="<?= current_url(); ?>" enctype="multipart/form-data">
					<div class="row">
                        <div class=" col-md-6">
                            <div class="panel panel-success">
                                    <div class="panel-body">
                                        <div class="col-md-12">
                                            <p class="bold p_style">Standard Desktop Printers - Size A4 (8"X11") <small class="d-block fw-500">(Four Label Printed on one Sheet)</small>
                                                <label class="custom-control custom-radio-md">
                                                    <input type="radio" required="" class="form-check-input" name="label_format" value="standard" <?php echo set_radio('label_format', 'standard', ((!empty($label_format) && $label_format == 'standard') ? TRUE : FALSE)); ?>>
                                                </label>
                                            </p>
                                            <hr class="hr_style">
                                            <div class="card shadow-none text-center" style="background: #f1f5f9;">
                                                <div class="card-header border-bottom" style="display: block;">
                                                    <img src="<?php echo base_url('assets/images/standard-printer.png');?>" width="200">
                                                </div>
                                            </div>
                                        </div>
                                </div>                           
                            </div>
                        </div>
                        <div class=" col-md-6">
                            <div class="panel panel-success">
                                <div class="panel-body">
                                    <div class="col-md-12">
                                        <p class="bold p_style">Thermal Label Printers - Size (4"X6") <small class="d-block fw-500">(Single Label on one Sheet)</small>
                                            <label class="custom-control custom-radio-md">
                                                <input type="radio" required="" class="form-check-input" name="label_format" value="thermal" <?php echo set_radio('label_format', 'thermal', ((!empty($label_format) && $label_format == 'thermal') ? TRUE : FALSE)); ?>>
                                            </label>
                                        </p>
                                            <hr class="hr_style">
                                            <div class="card shadow-none text-center" style="background: #f1f5f9;">
                                            <div class="card-header border-bottom" style="display: block;">
                                                <div class="custom-control custom-radio">
                                                    <img src="<?php echo base_url('assets/images/thermal-printer.png');?>" width="200">
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
				</form>
			</div>
		</div>
	</div>

</div>
<!-- END ROW-1 -->

</div>
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
