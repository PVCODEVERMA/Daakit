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
                            <h6 class="mb-2">Orders</h6>
                            <h3 class="mb-0 text-dark display-5 fw-bold d-inline-flex"><?= thousandsFormat(!empty($total_orders) ? $total_orders : '0') ?></h3>
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
                            <h6 class="mb-2">Shipments</h6>
                            <h3 class="mb-0 text-dark display-5 fw-bold d-inline-flex"><?= thousandsFormat(!empty($stats->total_shipments) ? $stats->total_shipments : '0') ?></h3>
                            <!-- <h3 class="mb-0 text-dark display-5 fw-bold d-inline-flex"><?= !empty($stats->delivered) ? $stats->delivered .'/' . $stats->total_delivered_shipments : '0' ?></h3> -->
                            <!-- <span class="badge bg-danger-transparent rounded-pill ms-1"><?= (!empty($stats->delivered) && $stats->delivered) ? (round(((!empty($stats->delivered) ? $stats->delivered : '0') / $stats->total_delivered_shipments) * 100, 2)) : '0'; ?>% </span> -->
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
                            <h6 class="mb-2">Net Revenue</h6>
                            <h3 class="mb-0 text-dark display-5 fw-bold d-inline-flex" data-bs-toggle="tooltip" data-bs-html="true" data-bs-original-title="Value of Dispatched Orders">&#8377;<?= !empty($stats->revenue) ? round($stats->revenue, 0) : '0'; ?></h3>
                        </div>
                        <div class="avatar avatar-lg bg-secondary-transparent mb-auto ms-auto br-4">
                            <i class="fa fa-inr" aria-hidden="true"></i>
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
                            <h6 class="mb-2">NDR</h6>
                            <h3 class="mb-0 text-dark display-5 fw-bold d-inline-flex"><?= thousandsFormat(!empty($count_by_status->action_required) ? $count_by_status->action_required : '0') ?></h3>
                            <!-- <span class="badge bg-danger-transparent rounded-pill ms-1"><?= (!empty($stats->total_shipments) && $stats->total_shipments) ? (round(((!empty($stats->rto) ? $stats->rto : '0') / $stats->total_shipments) * 100, 2)) : '0'; ?>%</span> -->
                        </div>
                        <div class="avatar avatar-lg bg-danger-transparent mb-auto ms-auto br-4">
                            <i class="fa fa-stop" aria-hidden="true"></i>
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

    <!-- START ROW-3 -->
    <div class="row">
        <div class="col-md-12 col-xl-6 col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="card-title">Courier Wise Summary</div>
                </div>
                <div class="card-body">
                    <div id="courier_wise_chart" style="margin: 0 auto; width: 100%; max-width: 500px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-xl-6 col-lg-12">
                <div class="card">
                <div class="card-header">
                    <div class="card-title">COD Vs Prepaid</div>
                </div>
                <div class="card-body">
                    <div id="payment_wise_chart" style="margin: 0 auto; width: 100%; max-width: 500px;"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- END ROW-3 -->
    <!-- START ROW-5 -->
    <div class="row">
        <div class="col-md-12 col-xl-6 col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="card-title">Zone Wise Summary</div>
                </div>
                <div class="card-body">
                    <div id="top_destination_chart"></div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-xl-6 col-lg-12">
                <div class="card">
                <div class="card-header">
                    <div class="card-title"> Delivery Vs RTO</div>
                </div>
                <div class="card-body">
                    <div id="delivery_rto_wise_chart" style="margin: 0 auto; width: 100%; max-width: 500px;"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- END ROW-5 -->
    <!-- START ROW-4 -->
    <div class="row">
        <div class="col-md-12 col-xl-12 col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">Product Wise Summary</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table card-table table-bordered table-vcenter text-dark table-outline text-nowrap">
                            <thead>
                                <tr>
                                    <th scope="col" class="w-25">Product Name</th>
                                    <th scope="col">Product SKU</th>
                                    <th scope="col">Total Shipments</th>
                                    <th scope="col">Not Shipped</th>
                                    <th scope="col">Pending Pickup</th>
                                    <th scope="col">In Transit</th>
                                    <th scope="col">Delivered</th>
                                    <th scope="col">RTO</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($product_wise_status)) {
                                    foreach ($product_wise_status as $product_status) {
                                ?>
                                        <tr>
                                            <td>
                                                <span data-toggle="tooltip" data-html="true" title="<?= ucwords($product_status->product_name); ?>">
                                                    <?= mb_strimwidth(ucwords($product_status->product_name), 0, 70, "..."); ?>
                                                </span>
                                            </td>
                                            <td><?= $product_status->pro_sku; ?></td>
                                            <td><?= $product_status->total; ?></td>
                                            <td><?= $product_status->booked; ?></td>
                                            <td><?= $product_status->pending_pickup; ?></td>
                                            <td><?= $product_status->in_transit; ?></td>
                                            <td><?= $product_status->delivered; ?> (<?= round(($product_status->delivered / $product_status->total) * 100, 2); ?>%)</td>
                                            <td><?= $product_status->rto; ?> (<?= round(($product_status->rto / $product_status->total) * 100, 2); ?>%)</td>

                                        </tr>
                                <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END ROW-4 -->
     <div class="row">
        <div class="col-md-12 col-xl-12 col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">Courier Wise Summary</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
    <table class="table card-table table-bordered table-vcenter text-dark table-outline text-nowrap">
        <thead>
            <tr>
                <th scope="col" class="w-25">Courier Name</th>
                <th scope="col">Booked</th>
                <th scope="col">Pending Pickup</th>
                <th scope="col">In Transit</th>
                <th scope="col">Out For Delivery</th>
                <th scope="col">Delivered</th>
                <th scope="col">RTO</th>
            </tr>
        </thead>
        <?php
        // -------------------------------
        // GROUP & SUM DATA BY COURIER NAME
        // -------------------------------
        $groupedData = [];

        if (!empty($courier_monthly_status)) {
            foreach ($courier_monthly_status as $status) {

                $name = $status->display_name;

                if (!isset($groupedData[$name])) {
                    $groupedData[$name] = [
                        'display_name'     => $name,
                        'total'            => 0,
                        'pending_pickup'   => 0,
                        'in_transit'       => 0,
                        'out_for_delivery' => 0,
                        'rto'              => 0,
                        'delivered'        => 0,
                    ];
                }

                $groupedData[$name]['total']            += (int) ($status->booked ?? 0);
                $groupedData[$name]['pending_pickup']   += (int) ($status->pending_pickup ?? 0);
                $groupedData[$name]['in_transit']       += (int) ($status->in_transit ?? 0);
                $groupedData[$name]['out_for_delivery'] += (int) ($status->out_for_delivery ?? 0);
                $groupedData[$name]['rto']              += (int) ($status->rto ?? 0);
                $groupedData[$name]['delivered']        += (int) ($status->delivered ?? 0);
            }
        }
        ?>

        <tbody>
        <?php if (!empty($groupedData)) : ?>
            
            <?php foreach ($groupedData as $row) : ?>
                <tr>
                    <td><?= htmlspecialchars($row['display_name']) ?></td>
                    <td><?= $row['total'] ?></td>
                    <td><?= $row['pending_pickup'] ?></td>
                    <td><?= $row['in_transit'] ?></td>
                    <td><?= $row['out_for_delivery'] ?></td>
                    <td><?= $row['delivered'] ?></td>
                    <td><?= $row['rto'] ?></td>
                </tr>
            <?php endforeach; ?>

        <?php else : ?>
            <tr>
                <td colspan="7" class="text-center">No data available</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

</div>
<!-- END MAIN-CONTAINER -->
<?php
    $top_destination_dataset = array();
    $top_destination_labels = array();
    if (!empty($top_destinations)) {
        $top_destination_dataset = array_column($top_destinations, 'total');
        $top_destination_labels = array_column($top_destinations, 'city');
    }
    $payment_load_dataset = array();
    $payment_load_labels = array();
    if (!empty($stats)) {
        $payment_load_dataset = array($stats->cod_shipments, $stats->prepaid_shipments);
        $payment_load_labels = array('COD', 'Prepaid');
    }
    $courier_load_dataset = array();
    $courier_load_labels = array();
    if (!empty($courier_stats)) {
        foreach ($courier_stats as $courier_load) {
            $courier_load_dataset[] = array_sum(array_column($courier_load, 'total'));
            $courier_load_labels[] = $courier_load[0]->display_name;
        }
    }
    $quoted_top_labels = array_map(function($city) {
        if(empty($city))
            $city="NA";
            
        return "'" . $city . "'";
    }, $top_destination_labels);

    //pr($quoted_top_labels);
?>
<script src="<?php echo base_url();?>assets/build/assets/plugins/apexcharts/apexcharts.min.js"></script>
<script>
    var options = {
          series: [{
          data: [<?= implode(",", $top_destination_dataset) ?>]
        }],
          chart: {
          height: 350,
          type: 'bar',
          events: {
            click: function(chart, w, e) {
              // console.log(chart, w, e)
            }
          }
        },
        plotOptions: {
          bar: {
            columnWidth: '45%',
            distributed: true,
          }
        },
        dataLabels: {
          enabled: false
        },
        legend: {
          show: false
        },
        xaxis: {
          categories: [<?= implode(",", $quoted_top_labels) ?>],
          labels: {
            style: {
              fontSize: '12px'
            }
          }
        }
        };

        var chart = new ApexCharts(document.querySelector("#top_destination_chart"), options);
        chart.render();

        //***********************Second chart*****************************
        var options = {
          series: [<?= implode(",", $payment_load_dataset) ?>],
          chart: {
            width: 500,
            height:398,
          type: 'pie',
        },
        dataLabels: {
          enabled: false
        },
        labels: ['<?= implode("','", $payment_load_labels) ?>'],
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

        var chart = new ApexCharts(document.querySelector("#payment_wise_chart"), options);
        chart.render();
        //***********************Third chart*****************************
        var options = {
          series: [<?= implode(",", $courier_load_dataset) ?>],
          chart: {
        width: 500,
        height:398,
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

        var chart = new ApexCharts(document.querySelector("#courier_wise_chart"), options);
        chart.render();
        //***********************Third chart*****************************
        //***********************Fourth chart*****************************
        var options = {
          series: [<?= !empty($stats->delivered) ? $stats->delivered : '0'?>,<?= (!empty($stats->rto)  ? $stats->rto : '0');?>],
          chart: {
          width: 500,
          height:395,
          type: 'pie',
        },
        dataLabels: {
          enabled: false
        },
        labels: ['Delivered','RTO'],
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

        var chart = new ApexCharts(document.querySelector("#delivery_rto_wise_chart"), options);
        chart.render();
</script>
