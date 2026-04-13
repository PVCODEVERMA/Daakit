<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">Change Shipment Status</h4>
    <ol class="breadcrumb">
		<li class="breadcrumb-item">
		</li>
    </ol>
</div>
<!-- END PAGE-HEADER -->
<div class="main-container container-fluid">
<!-- START ROW-1 -->
<div class="row">
	<div class="col-lg-6 col-md-6">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">Shipment Status</h3>
			</div>
			<div class="card-body">
                <form method="post" action="<?php echo base_url('admin/shipmentstatus/all');?>">
                    <div class="card-body ">

                        <div class="form-group">
                            <label>Status</label>
                            <select id="shipmentstatus" name="shipmentstatus" class="form-control">
                                <option value="">Select Status</option>
                                <option value="booked">Booked</option>
                                <option value="pending pickup">Pending Pickup</option>
                                <option value="in transit">In Transit</option>
                                <option value="out for delivery">Out For Delivery</option>
                                <option value="delivered">Delivered</option>
                                <option value="lost">Lost</option>
                                <option value="damaged">Damaged</option>
                                <option value="rto in transit">RTO In Transit</option>
                                <option value="rto delivered">RTO Delivered</option>
                                <option value="rto lost">RTO Lost</option>
                                <option value="rto damaged">RTO Damaged</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>AWB Number</label>
                            <textarea class="form-control" name="awbnumbers" placeholder="AWB Numbers by comma"></textarea>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12" style="margin-top: 10px;padding: 0px !important; text-align:right">
                                <button type="submit" class="btn btn-info">Submit</button>
                            </div>
                        </div>

                    </div>
                </form>
			</div>
		</div>
	</div>

</div>
<!-- END ROW-1 -->

</div>