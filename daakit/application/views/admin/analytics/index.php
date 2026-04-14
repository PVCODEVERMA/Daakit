<?php
$color_codes = array('#19b5fe', '#4d148c', '#0c9', '#f7bc06', '#95aac9', '#20c997', '#FDAC53', '#5B55A30', '#E9897E', '#926AA6', '#D2386C', '#CD5C5C', '#581845', '#FFC300', '#C70039');
$courier_list = array();
foreach ($couriers as $courier) {
    $courier_list[$courier->id] = $courier;
}
?>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">Dashboard</h4>
    <form method="post" action="<?= current_url(); ?>" class="home_filter_form" >
        <ol class="breadcrumb">
            <div class="input-group datepicker-group" style="margin-bottom: 1rem;">
                <div class="input-group-text">
                    <i class="fa fa-calendar tx-16 lh-0 op-6"></i>
                </div>
                <input
                    data-start-date="<?= !empty($filter['start_date']) ? $filter['start_date'] : '' ?>" data-end-date="<?= !empty($filter['end_date']) ? $filter['end_date'] : '' ?>"
                    class="form-control-sm date-range-picker"
                    placeholder="MM/DD/YYYY"
                    type="text"
                    id="datepicker"
                    aria-label="Date picker"
                    autocomplete="off"
                    style="border-radius:2px;width:160px"
                >
                <input type="hidden" autocomplete="off" id="date-min" name="filter[start_date]" value="<?= !empty($filter['start_date']) ? $filter['start_date'] : '' ?>" placeholder="from">
                <input type="hidden" autocomplete="off" id="date-max" name="filter[end_date]" value="<?= !empty($filter['end_date']) ? $filter['end_date'] : '' ?>" placeholder="to">
            </div>
        </ol>
    </form>
</div>
<!-- END PAGE-HEADER -->

<!-- START MAIN-CONTAINER -->
<div class="main-container container-fluid">

    <!-- START ROW-1 -->
    <div class="row">
        <div class="col-sm-6 col-lg-6 col-xl-3">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <div class="d-flex mb-1">
                        <div>
                            <h6 class="mb-2">Total / Cancelled Orders</h6>
                            <h3 class="mb-0 text-dark display-5 fw-bold d-inline-flex"><?= (!empty($total_orders)) ? $total_orders : '0'; ?> / <?= (!empty($total_cancelled_orders)) ? $total_cancelled_orders : '0'; ?></h3>
                        </div>
                        <div class="avatar avatar-lg bg-primary-transparent mb-auto ms-auto br-2">
                            <i class="fa fa-bar-chart" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="progress h-1 mt-3">
                        <div class="progress-bar bg-primary w-100 " role="progressbar"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-6 col-xl-3">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <div class="d-flex mb-1">
                        <div>
                            <h6 class="mb-2">Total / Avg. Shipments</h6>
                            <h3 class="mb-0 text-dark display-5 fw-bold d-inline-flex"><?= (!empty($total_shipments)) ? $total_shipments : '0'; ?> / <?= (!empty($avg_shipments)) ? round($avg_shipments) : '0'; ?></h3>
                        </div>
                        <div class="avatar avatar-lg bg-danger-transparent mb-auto ms-auto br-4">
                            <i class="fa fa-ship" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="progress h-1 mt-3">
                        <div class="progress-bar bg-secondary w-100 " role="progressbar"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-6 col-xl-3">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <div class="d-flex mb-1">
                        <div>
                            <h6 class="mb-2">Active / New Users</h6>
                            <h3 class="mb-0 text-dark display-5 fw-bold d-inline-flex"><?= (!empty($active_users)) ? $active_users : '0'; ?> / <?= (!empty($total_users)) ? $total_users : '0'; ?></h3>
                        </div>
                        <div class="avatar avatar-lg bg-secondary-transparent mb-auto ms-auto br-4">
                            <i class="fa fa-users" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="progress h-1 mt-3">
                        <div class="progress-bar bg-secondary w-100" role="progressbar"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-6 col-xl-3">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <div class="d-flex mb-1">
                        <div>
                            <h6 class="mb-2">Total Payments</h6>
                            <h3 class="mb-0 text-dark display-5 fw-bold d-inline-flex"><?= (!empty($total_payments)) ? round($total_payments) : '0'; ?></h3>
                            <!-- <span class="badge bg-danger-transparent rounded-pill ms-1"><?= (!empty($stats->total_shipments) && $stats->total_shipments) ? (round(((!empty($stats->rto) ? $stats->rto : '0') / $stats->total_shipments) * 100, 2)) : '0'; ?>%</span> -->
                        </div>
                        <div class="avatar avatar-lg bg-danger-transparent mb-auto ms-auto br-4">
                        <i class="fa fa-inr" aria-hidden="true"></i>                        
                    </div>
                    </div>
                    <div class="progress h-1 mt-3">
                        <div class="progress-bar bg-warning w-<?= (!empty($stats->total_shipments) && $stats->total_shipments) ? (round(((!empty($count_by_status->action_required) ? $count_by_status->action_required : '0') / $stats->total_shipments) * 100, 2)) : '0'; ?>" role="progressbar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END ROW-1 -->
    <!-- START ROW-5 -->
    <div class="row">
        <div class="col-md-12 col-xl-6 col-lg-12">
                <div class="card">
                <div class="card-header">
                    <div class="card-title">Courier Wise Load</div>
                </div>
                <div class="card-body">
                    <?php
                    $courier_load_dataset = array();
                    $courier_load_labels = array();
                    if (!empty($courier_stats_new)) {
                        foreach ($courier_stats_new as $courier_load) {
                            $courier_load_dataset[] = array_sum(array_column($courier_load, 'total'));
                            $courier_load_labels[] = $courier_load[0]->display_name;
                        }
                    }
                    ?>
                    <div id="courier_wise_load" style="margin: 0 auto; width: 100%; max-width: 500px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-xl-6 col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="card-title">Orders</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive row_scroll" <?php if (!empty($seller_orders)) { ?>style="height: 362px;" <?php }?>>
                        <table class="table card-table table-bordered table-vcenter text-dark table-outline">
                            <thead class="position-sticky fixed-top bg-white">
                                <tr>
                                    <th>Seller ID</th>
                                    <th>Seller</th>
                                    <th>Orders</th>
                                    <th>Not Shipped</th>
                                    <th>Booked</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($seller_orders)) {
                                    foreach ($seller_orders as $seller_order) {
                                ?>
                                        <tr>
                                            <td><?= $seller_order->user_id; ?></td>
                                            <td><?= $seller_order->company_name; ?></td>
                                            <td><?= $seller_order->total; ?></td>
                                            <td><?= $seller_order->new; ?></td>
                                            <td><?= $seller_order->booked; ?></td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="4">No records found</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END ROW-5 -->
         <!-- START ROW-3 -->
    <div class="row">
        <div class="col-md-12 col-xl-12 col-lg-12">
                <div class="card">
                <div class="card-header">
                    <div class="card-title">Shipments</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive row_scroll" style="height: 350px;">
                        <table class="table card-table table-bordered table-vcenter text-dark table-outline">
                            <thead class="position-sticky fixed-top bg-white">
                                <tr>
                                    <th>Seller Id</th>
                                    <th>Seller</th>
                                    <th>Total</th>
                                    <th>NS</th>
                                    <th>PP</th>
                                    <th>IT</th>
                                    <th>DL</th>
                                    <th>NDR</th>
                                    <th>RTO</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($seller_shipments)) {
                                    foreach ($seller_shipments as $seller_shipment) {
                                ?>
                                        <tr>
                                            <td><?= $seller_shipment->user_id; ?></td>
                                            <td style="max-width: 120px;"><?= $seller_shipment->company_name; ?></td>
                                            <td><?= $seller_shipment->total; ?></td>
                                            <td><?= $seller_shipment->booked; ?> (<?= round(($seller_shipment->booked / $seller_shipment->total) * 100); ?>%)</td>
                                            <td><?= $seller_shipment->pending_pickup; ?> (<?= round(($seller_shipment->pending_pickup / $seller_shipment->total) * 100); ?>%)</td>
                                            <td><?= $seller_shipment->in_transit; ?> (<?= round(($seller_shipment->in_transit / $seller_shipment->total) * 100); ?>%)</td>
                                            <td><?= $seller_shipment->delivered; ?> (<?= round(($seller_shipment->delivered / $seller_shipment->total) * 100); ?>%)</td>
                                            <td><?= $seller_shipment->exception; ?> (<?= round(($seller_shipment->exception / $seller_shipment->total) * 100); ?>%)</td>
                                            <td><?= $seller_shipment->rto; ?> (<?= round(($seller_shipment->rto / $seller_shipment->total) * 100); ?>%)</td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="10" align="center">No records found</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END ROW-3 -->
    <!-- START ROW-4 -->
    <div class="row">
        <div class="col-md-12 col-xl-12 col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">Courier Wise Status</div>
                </div>
                <div class="card-body ">
                    <div class="table-responsive row_scroll" style="height: 350px;">
                        <table class="table card-table table-bordered table-vcenter text-dark table-outline ">
                            <thead class="position-sticky fixed-top bg-white">
                                <tr>

                                    <th>Courier</th>
                                    <th>Total</th>
                                    <th>NS</th>
                                    <th>PP</th>
                                    <th>IT</th>
                                    <th>OFD</th>
                                    <th>DL</th>
                                    <th>NDR</th>
                                    <th>RTO</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($courier_stats_new)) {
                                    foreach ($courier_stats_new as $k => $c_array) {
                                        $c_total = array_sum(array_column($c_array, 'total'));
                                        $booked = array_sum(array_column($c_array, 'booked'));
                                        $pending_pickup = array_sum(array_column($c_array, 'pending_pickup'));
                                        $out_for_delivery = array_sum(array_column($c_array, 'out_for_delivery'));
                                        $delivered = array_sum(array_column($c_array, 'delivered'));
                                        $rto = array_sum(array_column($c_array, 'rto'));
                                        $in_transit = array_sum(array_column($c_array, 'in_transit'));
                                        $exception = array_sum(array_column($c_array, 'exception'));

                                ?>
                                        <tr>
                                            <td style="max-width: 124px;"><a href="javascript:void(0);" onclick="$('tr.sub-<?= $k; ?>').toggleClass('d-none');"><?= ucfirst($c_array[0]->display_name); ?> <i class="fa fa-angle-double-down" aria-hidden="true"></i></a></td>
                                            <td><?= $c_total; ?></td>
                                            <td><?= $booked; ?> (<?= round(($booked / $c_total) * 100); ?>%)</td>
                                            <td><?= $pending_pickup; ?> (<?= round(($pending_pickup / $c_total) * 100); ?>%)</td>
                                            <td><?= $in_transit; ?> (<?= round(($in_transit / $c_total) * 100); ?>%)</td>
                                            <td><?= $out_for_delivery; ?> (<?= round(($out_for_delivery / $c_total) * 100); ?>%)</td>
                                            <td><?= $delivered; ?> (<?= round(($delivered / $c_total) * 100); ?>%)</td>
                                            <td><?= $exception; ?> (<?= round(($exception / $c_total) * 100); ?>%)</td>
                                            <td><?= $rto; ?> (<?= round(($rto / $c_total) * 100); ?>%)</td>
                                        </tr>
                                        <?php
                                        foreach ($c_array as $r) {
                                        ?>
                                            <tr class="sub-<?= $k; ?> d-none table-secondary">
                                                <td style="max-width: 124px;"><?= ucfirst($r->courier_name); ?></td>
                                                <td><?= $r->total; ?></td>
                                                <td><?= $r->booked; ?> (<?= round(($r->booked / $r->total) * 100); ?>%)</td>
                                                <td><?= $r->pending_pickup; ?> (<?= round(($r->pending_pickup / $r->total) * 100); ?>%)</td>
                                                <td><?= $r->in_transit; ?> (<?= round(($r->in_transit / $r->total) * 100); ?>%)</td>
                                                <td><?= $r->out_for_delivery; ?> (<?= round(($r->out_for_delivery / $r->total) * 100); ?>%)</td>
                                                <td><?= $r->delivered; ?> (<?= round(($r->delivered / $r->total) * 100); ?>%)</td>
                                                <td><?= $r->exception; ?> (<?= round(($r->exception / $r->total) * 100); ?>%)</td>
                                                <td><?= $r->rto; ?> (<?= round(($r->rto / $r->total) * 100); ?>%)</td>
                                            </tr>
                                    <?php

                                        }
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="10" align="center">No records found</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END ROW-4 -->

</div>
<!-- END MAIN-CONTAINER -->
<script src="<?php echo base_url();?>assets/build/assets/plugins/apexcharts/apexcharts.min.js"></script>
<script>
        //***********************First chart*****************************
        var options = {
          series: [<?= implode(',', $courier_load_dataset) ?>],
          chart: {
          width: 500,
          height:395,
          type: 'pie',
        },
        dataLabels: {
          enabled: false
        },
        labels: ['<?= implode("','", $courier_load_labels) ?>'],
        legend: {
            position: 'bottom', // Set legend position to bottom
            horizontalAlign: 'center', // Center the legend horizontally
        },
        responsive: [{
          breakpoint: 480,
          options: {
            chart: {
              width: 200
            },
            legend: {
              position: 'bottom'
            }
          }
        }]
        };

        var chart = new ApexCharts(document.querySelector("#courier_wise_load"), options);
        chart.render();
</script>
