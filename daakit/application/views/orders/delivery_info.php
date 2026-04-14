<style>
.vl {
  border-left: 6px solid green;
  height: 500px;
  position: absolute;
  left: 50%;
  margin-left: -3px;
  top: 0;
}

#collapseOneCouriers .accordion-body {
  padding: 10px;
}

#collapseOneCouriers .card.couriourval {
  background-color: #fdfdfd;
  border-radius: 10px;
  padding: 15px 20px;
  margin-bottom: 10px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

#collapseOneCouriers .card.couriourval:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
}

#collapseOneCouriers .card-header {
  width: 100%;
  padding: 0;
  background: none;
  border: none;
}

#collapseOneCouriers .card-title {
  font-size: 16px;
  margin: 0;
}

#collapseOneCouriers .card-options .btn {
  padding: 6px 14px;
  font-size: 14px;
  border-radius: 6px;
}

#collapseOneCouriers .prefered {
  border-left: 5px solid #28a745;
}

#collapseOneCouriers .notprefered {
  border-left: 5px solid #dc3545;
}

#collapseGoCouriers {
  padding: 10px;
}

#collapseGoCouriers > div > div {
  background-color: #f9f9f9;
  border-radius: 10px;
  padding: 15px 20px;
  margin-bottom: 10px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

#collapseGoCouriers > div > div:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
}

#collapseGoCouriers .card-options {
  margin-left: 2px;
}

#collapseGoCouriers .card-options .btn {
  padding: 6px 14px;
  font-size: 14px;
  border-radius: 6px;
}

#collapseGoCouriers .prefered {
  border-left: 5px solid #28a745;
}

#collapseGoCouriers .notprefered {
  border-left: 5px solid #dc3545;
}

/* New styles from recommendation system */
.recommendation-container {
  max-width: 1400px;
  margin:5px 0;
  background: white;
  border-radius: 12px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
  overflow: hidden;
  border-radius: 10px;
}

.recommendation-header {
  background: linear-gradient(135deg, #564ec1, #564ec1);
  color: white;
  padding: 20px 25px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.recommendation-header h2 {
  font-size: 24px;
  margin-bottom: 5px;
  display: flex;
  align-items: center;
  gap: 12px;
}

.recommendation-header h2 i {
  color: #ffcc00;
}

.recommendation-subtitle {
  font-size: 14px;
  opacity: 0.9;
  font-weight: 300;
}

.recommendation-content {
  padding: 20px 25px;
}

.controls {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  flex-wrap: wrap;
  gap: 15px;
}

.tab-container {
  display: flex;
  background-color: #f0f2f5;
  border-radius: 8px;
  overflow: hidden;
  border: 1px solid #ddd;
}

.tab {
  padding: 10px 20px;
  cursor: pointer;
  font-weight: 600;
  color: #555;
  transition: all 0.3s ease;
  border-right: 1px solid #ddd;
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
}

.tab:last-child {
  border-right: none;
}

.tab:hover {
  background-color: #e4e7eb;
}

.tab.active {
  background-color: #564ec1;
  color: white;
}

.tab i {
  font-size: 14px;
}

.refresh-btn {
  background-color: #564ec1;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.3s ease;
  font-size: 14px;
}

.refresh-btn:hover {
  background-color: #3a5a80;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.loading {
  text-align: center;
  padding: 30px;
  font-size: 16px;
  color: #4a6fa5;
  display: none;
}

.loading i {
  font-size: 24px;
  margin-bottom: 10px;
  display: block;
}

.results-container {
  display: none;
}

.results-container.active {
  display: block;
  animation: fadeIn 0.5s ease;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.results-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
  padding-bottom: 10px;
  border-bottom: 2px solid #eaeaea;
}

.results-title {
  font-size: 18px;
  color: #2c3e50;
}

.results-count {
  background-color: #eef5ff;
  color: #4a6fa5;
  padding: 4px 12px;
  border-radius: 20px;
  font-weight: 600;
  font-size: 13px;
}

.data-table-container {
  overflow-x: auto;
  border-radius: 8px;
  border: 1px solid #e0e0e0;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  max-height: 400px;
  overflow-y: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
  min-width: 1000px;
}

.data-table thead {
  background: linear-gradient(135deg, #564ec1, #564ec1);
  color: white;
  position: sticky;
  top: 0;
  z-index: 10;
}

.data-table th {
  padding: 12px 10px;
  text-align: left;
  font-weight: 600;
  font-size: 13px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border-right: 1px solid rgba(255, 255, 255, 0.1);
  position: relative;
  cursor: pointer;
}

.data-table th:last-child {
  border-right: none;
}

.data-table th i {
  margin-right: 6px;
}

.sort-icon {
  margin-left: 6px;
  font-size: 12px;
  opacity: 0.6;
}

.data-table th.sorted-asc .sort-icon::before {
  content: "\f0de";
  font-family: "Font Awesome 6 Free";
  font-weight: 900;
}

.data-table th.sorted-desc .sort-icon::before {
  content: "\f0dd";
  font-family: "Font Awesome 6 Free";
  font-weight: 900;
}

.data-table tbody tr {
  border-bottom: 1px solid #f0f0f0;
  transition: background-color 0.2s ease;
}

.data-table tbody tr:hover {
  background-color: #f8fafc;
}

.data-table tbody tr:nth-child(even) {
  background-color: #f9f9f9;
}

.data-table td {
  padding: 12px 10px;
  vertical-align: middle;
  border-right: 1px solid #f0f0f0;
  font-size: 13px;
}

.data-table td:last-child {
  border-right: none;
}

.rank-cell {
  text-align: center;
  font-weight: 700;
  font-size: 14px;
  color: #4a6fa5;
}

.courier-name-cell {
  font-weight: 600;
  color: #2c3e50;
}

.id-cell {
  text-align: center;
  font-weight: 500;
  color: #666;
}

.cost-cell {
  font-weight: 700;
  color: #2ecc71;
  text-align: right;
}

.days-cell {
  text-align: center;
  font-weight: 500;
  color: #666;
}

.weight-cell {
  text-align: center;
  font-weight: 500;
  color: #666;
  background-color: #f8f9fa;
}

.rto-cell {
  text-align: center;
  font-weight: 600;
}

.rto-cell.low-risk {
  color: #27ae60;
}

.rto-cell.medium-risk {
  color: #f39c12;
}

.rto-cell.high-risk {
  color: #e74c3c;
}

.confidence-cell {
  font-weight: 600;
  color: #3498db;
  text-align: center;
}

.tags-cell {
  padding: 6px 10px !important;
}

.tags-container {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
}

.tag {
  padding: 3px 8px;
  border-radius: 12px;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  display: inline-block;
}

.tag.recommended {
  background-color: #e8f5e9;
  color: #2e7d32;
  border: 1px solid #c8e6c9;
}

.tag.cheapest {
  background-color: #e3f2fd;
  color: #1565c0;
  border: 1px solid #bbdefb;
}

.tag.fastest {
  background-color: #fff3e0;
  color: #f57c00;
  border: 1px solid #ffe0b2;
}

.tag.least-rto {
  background-color: #fce4ec;
  color: #c2185b;
  border: 1px solid #f8bbd9;
}

.tag.balanced {
  background-color: #f3e5f5;
  color: #7b1fa2;
  border: 1px solid #e1bee7;
}

.no-data {
  text-align: center;
  padding: 40px 20px;
  color: #7f8c8d;
  font-size: 16px;
  background: #f8f9fa;
  border-radius: 8px;
  border: 2px dashed #dee2e6;
}

.no-data i {
  font-size: 36px;
  margin-bottom: 15px;
  color: #bdc3c7;
}

.rto-legend {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  gap: 12px;
  margin-bottom: 12px;
  padding: 8px 12px;
  background-color: #f8f9fa;
  border-radius: 6px;
  border: 1px solid #eaeaea;
  font-size: 12px;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 5px;
  font-weight: 500;
}

.legend-color {
  width: 12px;
  height: 12px;
  border-radius: 3px;
}

.legend-color.low {
  background-color: #27ae60;
}

.legend-color.medium {
  background-color: #f39c12;
}

.legend-color.high {
  background-color: #e74c3c;
}

.notification {
  position: fixed;
  top: 20px;
  right: 20px;
  color: white;
  padding: 12px 18px;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  display: flex;
  align-items: center;
  gap: 10px;
  z-index: 10000;
  animation: slideIn 0.3s ease;
  font-size: 14px;
}

.notification.success {
  background: #2ecc71;
}

.notification.error {
  background: #e74c3c;
}

.close-notification {
  cursor: pointer;
  margin-left: auto;
}

@keyframes slideIn {
  from { transform: translateX(100%); opacity: 0; }
  to { transform: translateX(0); opacity: 1; }
}

@keyframes slideOut {
  from { transform: translateX(0); opacity: 1; }
  to { transform: translateX(100%); opacity: 0; }
}

/* Modal adjustments */
.modal-content {
  max-height: 90vh;
  overflow-y: auto;
}

.modal-body {
  padding: 20px;
}

/* Responsive */
@media (max-width: 768px) {
  .recommendation-content {
    padding: 15px;
  }
  
  .controls {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .tab-container {
    width: 100%;
  }
  
  .tab {
    padding: 8px 12px;
    font-size: 13px;
    flex: 1;
    justify-content: center;
  }
  
  .refresh-btn {
    width: 100%;
    justify-content: center;
  }
  
  .data-table th,
  .data-table td {
    padding: 8px 6px;
    font-size: 12px;
  }
  
  .rto-legend {
    flex-wrap: wrap;
    justify-content: center;
  }
}

/* Nested tabs styles */
.nested-tabs {
  margin: 15px 0;
  display: flex;
  gap: 10px;
}

.nested-tab {
  padding: 8px 16px;
  background: #f0f2f5;
  border-radius: 6px;
  cursor: pointer;
  font-size: 13px;
  font-weight: 500;
  transition: all 0.2s ease;
}

.nested-tab:hover {
  background: #e4e7eb;
}

.nested-tab.active {
  background: #564ec1;
  color: white;
}

.nested-content {
  display: none;
}

.nested-content.active {
  display: block;
}

/* Filter info message */
.filter-info {
  background-color: #e3f2fd;
  border-left: 4px solid #2196F3;
  padding: 10px 15px;
  margin: 10px 0;
  border-radius: 4px;
  font-size: 13px;
  color: #0d47a1;
}

.filter-info i {
  margin-right: 8px;
  color: #2196F3;
}

/* Weight info styling */
.weight-info {
  display: inline-block;
  background: #f0f0f0;
  padding: 2px 6px;
  border-radius: 4px;
  font-size: 11px;
  color: #555;
  margin-left: 4px;
}
</style>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="modal-content data">
    <div class="modal-header">
        <h5 class="modal-title">Start Shipping Your Package Today</h5>
        <button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"></button>
    </div>
    <div class="modal-body">
        <form class="ship_form" method="post" id="shipForm">
            <div class="row">
                <input type="hidden" name="order_id" value="<?= !empty($order->id) ? $order->id : ''; ?>">
                <input type="hidden" id="courier_id" name="courier_id" class="form-check-input">
                
                <!-- Left Column - Shipment Info -->
                <div class="col-md-3">
                    <h6>Shipment Info</h6>
                    <div class="clearfix"></div>
                    <h5 class="no-mtop task-info-created">
                        <small class="text-dark">Created at <span class="text-dark"><?php echo date('d-m-Y h:i A'); ?></span></small>
                    </h5>
                    <hr class="task-info-separator">
                    <div class="clearfix"></div>
                    
                    <h7>
                        <i class="fa fa-university" aria-hidden="true"></i> Warehouse for Pickup
                    </h7>
                    <div class="simple-bootstrap-select">
                        <div class="dropdown bootstrap-select text-muted task-action-select bs3" style="width: 100%;">
                            <?php if (!empty($warehouses)) { ?>
                                <select data-width="100%" data-order-id="<?= !empty($order->id) ? $order->id : ''; ?>" id="select_warehouse_dropdown" class="form-select form-select-sm select2" name="warehouse_id" title="Warehouse for Pickup">
                                    <option class="bs-title-option" value="">--Select Warehouse--</option>
                                    <?php foreach ($warehouses as $warehouse) { ?>
                                        <option <?php if ($warehouse->id == $selected_warehouse) { ?> selected="" <?php } ?> value="<?= $warehouse->id; ?>"><?= ucwords($warehouse->name); ?><?php if ($warehouse->is_default) { ?> (Default) <?php } ?></option>
                                    <?php } ?>
                                </select>
                            <?php } ?>
                        </div>
                    </div>
                    <br>
                    
                    <h7>
                        <i class="fa fa-university" aria-hidden="true"></i> Warehouse for RTO
                    </h7>
                    <div class="simple-bootstrap-select">
                        <div class="dropdown bootstrap-select text-muted task-action-select bs3" style="width: 100%;">
                            <?php if (!empty($warehouses)) { ?>
                                <select data-width="100%" id="rto_warehouse_id" class="form-select form-select-sm select2" name="rto_warehouse_id" data-live-search="true" title="Warehouse for RTO" data-none-selected-text="Nothing selected" tabindex="-98">
                                    <option class="bs-title-option" value="">--Select Warehouse--</option>
                                    <?php foreach ($warehouses as $warehouse) { ?>
                                        <option <?php if ($warehouse->id == $selected_warehouse) { ?> selected="" <?php } ?> value="<?= $warehouse->id; ?>"><?= ucwords($warehouse->name); ?><?php if ($warehouse->is_default) { ?> (Default) <?php } ?></option>
                                    <?php } ?>
                                </select>
                            <?php } ?>
                        </div>
                    </div>
                    <hr class="task-info-separator">
                    <div class="clearfix"></div>
                </div>
                
                <!-- Right Column - Courier Selection with Recommendation System -->
                <div class="col-md-9">
                     
                    
                    <?php if (!empty($couriers)) { ?>
                        <?php
                        // Define DAAKit Go IDs
                        $daakitGoIds = [1124, 1125, 1126, 1127, 1128, 1129, 1130, 1131, 1132];
                        
                        // Filter DAAKit Go couriers
                        $daakitGoCouriers = array_filter($couriers, function($c) use ($daakitGoIds) {
                            return in_array($c->id, $daakitGoIds);
                        });
                        
                        $daakitOneCouriers = array_filter($couriers, function($c) use ($daakitGoIds) {
                            return !in_array($c->id, $daakitGoIds);
                        });
                        
                        $hasGo = !empty($daakitGoCouriers);
                        $hasOne = !empty($daakitOneCouriers);
                        ?>
                        
                        <!-- Recommendation System Section with Three Tabs -->
                        <div class="recommendation-container">
                            <!-- <div class="recommendation-header">
                                <h2></i> System Recommended</h2>
                                
                            </div> -->
                            
                            <div class="recommendation-content">
                                <div class="controls">
                                    <div class="tab-container" id="mainTabContainer">
                                        <div class="tab active" data-tab="ai">
                                            AI-Powered Recommendation 
                                        </div>
                                        <div class="tab" data-tab="daakitone">
                                             DAAKIT One
                                        </div>
                                        <div class="tab" data-tab="daakitgo">
                                            </i> Daakit Go
                                        </div>
                                    </div>
                                    
                                    <!-- <button class="refresh-btn" id="fetchRecommendations" type="button">
                                        <i class="fas fa-sync-alt"></i> Refresh  Predictions
                                    </button> -->
                                </div>
                                
                                <!-- AI Prediction Tab Content -->
                                <div class="results-container active" id="aiResults">
                                    <div class="row">
                                                 <div class="col-sm-12 col-md-4">
                                        <h7>Courier Mode Filter:</h7>
                                        <div class="input-group">
                                            <div class="input-group-append" data-toggle="buttons">
                                                <button type="button" data-nested="all" class="btn nested-tab active btn-outline-secondary text-dark filter_by shadow-none" style="color:#03A9F4; border:1px solid #03A9F4;">
                                                    <i class="mdi mdi-airplane"></i>
                                                    <input type="checkbox" name="radio1" value="all" class="filter"> All
                                                </button>
                                                <button type="button" data-nested="air" class="btn nested-tab btn-outline-secondary text-dark filter_by shadow-none" style="color:#03A9F4; border:1px solid #03A9F4;">
                                                    <i class="mdi mdi-airplane"></i>
                                                    <input type="checkbox" name="radio1" value="air" class="filter"> Air
                                                </button>
                                                <button type="button" data-nested="surface" class="btn nested-tab btn-outline-secondary text-dark filter_by shadow-none" style="color:#03A9F4; border:1px solid #03A9F4;">
                                                    <i class="mdi mdi-truck-fast"></i>
                                                    <input type="checkbox" name="radio1" value="surface" class="filter"> Surface
                                                </button>
                                            </div>
                                        </div>
                                        </div>
                           
                                        <div class="col-sm-12 col-md-8">
                                            <h7>Courier Weight Filter:</h7>
                                            <div class="input-group">
                                                <div class="input-group-append" data-toggle="buttons">
                                                    <button type="button" class="btn btn-outline-secondary text-dark filter_by shadow-none" style="color:#03A9F4; border:1px solid #03A9F4;">
                                                        <i class="mdi mdi-weight-kilogram"></i>
                                                        <input type="checkbox" name="radio2" value="500" class="filter"> 0.5 KG
                                                    </button>
                                                    <button type="button" class="btn btn-outline-secondary text-dark filter_by shadow-none" style="color:#03A9F4; border:1px solid #03A9F4;">
                                                        <i class="mdi mdi-weight-kilogram"></i>
                                                        <input type="checkbox" name="radio2" value="1000" class="filter"> 1 KG
                                                    </button>
                                                    <button type="button" class="btn btn-outline-secondary text-dark filter_by shadow-none" style="color:#03A9F4; border:1px solid #03A9F4;">
                                                        <i class="mdi mdi-weight-kilogram"></i>
                                                        <input type="checkbox" name="radio2" value="2000" class="filter"> 2 KG
                                                    </button>
                                                    
                                                    <button type="button" class="btn btn-outline-secondary text-dark filter_by shadow-none" style="color:#03A9F4; border:1px solid #03A9F4;">
                                                        <i class="mdi mdi-truck-delivery"></i>
                                                        <input type="checkbox" name="radio2" value="20000" class="filter"> Heavy
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- <div class="nested-tabs" id="aiNestedTabs">
                                        <div class="nested-tab active" data-nested="all">All Couriers</div>
                                        <div class="nested-tab" data-nested="air">Air Couriers</div>
                                        <div class="nested-tab" data-nested="surface">Surface Couriers</div>
                                    </div> -->
                                    
                                    <div class="rto-legend" id="aiLegend" style="display: flex;">
                                        <div class="legend-item">
                                            <div class="legend-color low"></div>
                                            <span>Lowest RTO Risk</span>
                                        </div>
                                        <div class="legend-item">
                                            <div class="legend-color medium"></div>
                                            <span>Medium RTO Risk</span>
                                        </div>
                                        <div class="legend-item">
                                            <div class="legend-color high"></div>
                                            <span>Highest RTO Risk</span>
                                        </div>
                                    </div>
                                    
                                    <div class="loading" id="recommendationLoading">
                                        <i class="fas fa-spinner fa-spin"></i>
                                        <p>Fetching AI recommendations...</p>
                                    </div>
                                    
                                    <!-- Nested Content for AI Predictions -->
                                    <div class="nested-content active" id="aiAllContent">
                                        <div class="results-header">
                                            <h3 class="results-title" style="display:flex;align-items:center;gap:10px;margin-top:5px;">
                                            <img src="<?= base_url('assets/images/Plan_iq.jpeg'); ?>" width="90">
                                           Powered by DAAKIT PlanIQ – Inventory & Demand Forecasting
                                        </h3>
                                            
                                            <div class="results-count" id="aiAllCount">0 couriers</div>
                                        </div>
                                        <div class="data-table-container">
                                            <table class="data-table" id="aiAllTable">
                                                <thead>
                                                    <tr>
                                                        <th data-key="Rank"><i class="fas fa-hashtag"></i> Rank</th>
                                                        <th data-key="Courier Name"><i class="fas fa-shipping-fast"></i> Courier Name</th>
                                                        <th data-key="Courier Id"><i class="fas fa-id-badge"></i> ID</th>
                                                        <th data-key="Weight Slab"><i class="fas fa-weight-scale"></i> Weight (g)</th>
                                                        <th data-key="Estimated Cost"><i class="fas fa-rupee-sign"></i> Est. Cost</th>
                                                        <th data-key="Estimated Delivery Days"><i class="fas fa-calendar-day"></i> Days</th>
                                                        <th data-key="RTO Risk"><i class="fas fa-exclamation-triangle"></i> RTO Risk</th>
                                                        <th data-key="Confidence Score"><i class="fas fa-chart-line"></i> Score</th>
                                                        <th><i class="fas fa-tags"></i> Tags</th>
                                                        <th><i class="fas fa-check-circle"></i> Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="aiAllTableBody">
                                                    <tr>
                                                        <td colspan="9">
                                                            <div class="no-data">
                                                                <i class="fas fa-cloud-download-alt"></i>
                                                                <p>Click "Refresh AI Predictions" to load data</p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    
                                    <div class="nested-content" id="aiAirContent">
                                        <div class="results-header">
                                            
                                            <h3 class="results-title" style="display:flex;align-items:center;gap:10px;margin-top:5px;">
                                            <img src="<?= base_url('assets/images/Plan_iq.jpeg'); ?>" width="90">
                                            Powered by DAAKIT PlanIQ – Inventory & Demand Forecasting 
                                        </h3>
                                            
                                            <div class="results-count" id="aiAirCount">0 couriers</div>
                                        </div>
                                        <div class="data-table-container">
                                            <table class="data-table" id="aiAirTable">
                                                <thead>
                                                    <tr>
                                                        <th data-key="Rank"><i class="fas fa-hashtag"></i> Rank</th>
                                                        <th data-key="Courier Name"><i class="fas fa-shipping-fast"></i> Courier Name</th>
                                                        <th data-key="Courier Id"><i class="fas fa-id-badge"></i> ID</th>
                                                        <th data-key="Weight Slab"><i class="fas fa-weight-scale"></i> Weight (g)</th>
                                                        <th data-key="Estimated Cost"><i class="fas fa-rupee-sign"></i> Est. Cost</th>
                                                        <th data-key="Estimated Delivery Days"><i class="fas fa-calendar-day"></i> Days</th>
                                                        <th data-key="RTO Risk"><i class="fas fa-exclamation-triangle"></i> RTO Risk</th>
                                                        <th data-key="Confidence Score"><i class="fas fa-chart-line"></i> Score</th>
                                                        <th><i class="fas fa-tags"></i> Tags</th>
                                                        <th><i class="fas fa-check-circle"></i> Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="aiAirTableBody">
                                                    <tr>
                                                        <td colspan="9">
                                                            <div class="no-data">
                                                                <i class="fas fa-plane-slash"></i>
                                                                <p>No air courier predictions available</p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    
                                    <div class="nested-content" id="aiSurfaceContent">
                                        <div class="results-header">
                                            <h3 class="results-title" style="display:flex;align-items:center;gap:10px;margin-top:5px;">
                                            <img src="<?= base_url('assets/images/Plan_iq.jpeg'); ?>" width="90">
                                            Powered by DAAKIT PlanIQ – Inventory & Demand Forecasting 
                                        </h3>
                                            
                                            <div class="results-count" id="aiSurfaceCount">0 couriers</div>
                                        </div>
                                        <div class="data-table-container">
                                            <table class="data-table" id="aiSurfaceTable">
                                                <thead>
                                                    <tr>
                                                        <th data-key="Rank"><i class="fas fa-hashtag"></i> Rank</th>
                                                        <th data-key="Courier Name"><i class="fas fa-shipping-fast"></i> Courier Name</th>
                                                        <th data-key="Courier Id"><i class="fas fa-id-badge"></i> ID</th>
                                                        <th data-key="Weight Slab"><i class="fas fa-weight-scale"></i> Weight (g)</th>
                                                        <th data-key="Estimated Cost"><i class="fas fa-rupee-sign"></i> Est. Cost</th>
                                                        <th data-key="Estimated Delivery Days"><i class="fas fa-calendar-day"></i> Days</th>
                                                        <th data-key="RTO Risk"><i class="fas fa-exclamation-triangle"></i> RTO Risk</th>
                                                        <th data-key="Confidence Score"><i class="fas fa-chart-line"></i> Score</th>
                                                        <th><i class="fas fa-tags"></i> Tags</th>
                                                        <th><i class="fas fa-check-circle"></i> Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="aiSurfaceTableBody">
                                                    <tr>
                                                        <td colspan="9">
                                                            <div class="no-data">
                                                                <i class="fas fa-truck-slash"></i>
                                                                <p>No surface courier predictions available</p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Daakit One Tab -->
                                <div class="results-container" id="daakitoneResults">
                                    <div class="row">
                                                 <div class="col-sm-12 col-md-4">
                                        <h7>Courier Mode Filter:</h7>
                                        <div class="input-group">
                                            <div class="input-group-append" data-toggle="buttons">
                                                <button type="button" data-nested="all" class="btn nested-tab active btn-outline-secondary text-dark filter_by shadow-none" style="color:#03A9F4; border:1px solid #03A9F4;">
                                                    <i class="mdi mdi-airplane"></i>
                                                    <input type="checkbox" name="radio1" value="all" class="filter"> All
                                                </button>
                                                <button type="button" data-nested="air" class="btn nested-tab btn-outline-secondary text-dark filter_by shadow-none" style="color:#03A9F4; border:1px solid #03A9F4;">
                                                    <i class="mdi mdi-airplane"></i>
                                                    <input type="checkbox" name="radio1" value="air" class="filter"> Air
                                                </button>
                                                <button type="button" data-nested="surface" class="btn nested-tab btn-outline-secondary text-dark filter_by shadow-none" style="color:#03A9F4; border:1px solid #03A9F4;">
                                                    <i class="mdi mdi-truck-fast"></i>
                                                    <input type="checkbox" name="radio1" value="surface" class="filter"> Surface
                                                </button>
                                            </div>
                                        </div>
                                        </div>
                           
                                        <div class="col-sm-12 col-md-8">
                                            <h7>Courier Weight Filter:</h7>
                                            <div class="input-group">
                                                <div class="input-group-append" data-toggle="buttons">
                                                    <button type="button" class="btn btn-outline-secondary text-dark filter_by shadow-none" style="color:#03A9F4; border:1px solid #03A9F4;">
                                                        <i class="mdi mdi-weight-kilogram"></i>
                                                        <input type="checkbox" name="radio2" value="500" class="filter"> 0.5 KG
                                                    </button>
                                                    <button type="button" class="btn btn-outline-secondary text-dark filter_by shadow-none" style="color:#03A9F4; border:1px solid #03A9F4;">
                                                        <i class="mdi mdi-weight-kilogram"></i>
                                                        <input type="checkbox" name="radio2" value="1000" class="filter"> 1 KG
                                                    </button>
                                                    <button type="button" class="btn btn-outline-secondary text-dark filter_by shadow-none" style="color:#03A9F4; border:1px solid #03A9F4;">
                                                        <i class="mdi mdi-weight-kilogram"></i>
                                                        <input type="checkbox" name="radio2" value="2000" class="filter"> 2 KG
                                                    </button>
                                                    
                                                    <button type="button" class="btn btn-outline-secondary text-dark filter_by shadow-none" style="color:#03A9F4; border:1px solid #03A9F4;">
                                                        <i class="mdi mdi-truck-delivery"></i>
                                                        <input type="checkbox" name="radio2" value="20000" class="filter"> Heavy
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="results-header">
                                        <h3 class="results-title" style="display:flex;align-items:center;gap:10px;margin-top:5px;">
                                            <img src="<?= base_url('assets/images/daakit_one.jpeg'); ?>" width="90">
                                            Powered by DAAKIT One – Pan-India Logistics Aggregation (B2C)
                                        </h3>
                                        <div class="results-count" id="daakitoneCount"><?= count($daakitOneCouriers) ?> couriers</div>
                                    </div>
                                    
                                    <div class="data-table-container">
                                        <table class="data-table" id="daakitoneTable">
                                            <thead>
                                                <tr>
                                                    <th><i class="fas fa-hashtag"></i> ID</th>
                                                    <th><i class="fas fa-shipping-fast"></i> Courier Name</th>
                                                    <th><i class="fas fa-rupee-sign"></i> Charges</th>
                                                    <th><i class="fas fa-tag"></i> Type</th>
                                                    <th><i class="fas fa-weight-scale"></i> Weight</th>
                                                    <th><i class="fas fa-check-circle"></i> Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="daakitoneTableBody">
                                                <?php if ($hasOne) { ?>
                                                    <?php foreach ($daakitOneCouriers as $courier) { ?>
                                                        <tr data-type="<?= strtolower($courier->courier_type ?? '') ?>" data-weight="<?= $courier->weight ?? '' ?>">
                                                            <td class="id-cell"><?= $courier->id ?></td>
                                                            <td class="courier-name-cell"><?= $courier->name ?></td>
                                                            <td class="cost-cell"><?= !empty($courier->charges) ? '₹' . round($courier->charges, 2) : 'N/A' ?></td>
                                                            <td class="days-cell"><?= $courier->courier_type ?? 'N/A' ?></td>
                                                            <td class="days-cell"><?= $courier->weight ?? 'N/A' ?></td>
                                                            <td class="tags-cell">
                                                                 <button type="button" onclick="submitShipment('<?= $courier->id ?>')" class="btn btn-sm btn-primary">Ship Now</button>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <tr>
                                                        <td colspan="6">
                                                            <div class="no-data">
                                                                <i class="fas fa-box-open"></i>
                                                                <p>No Daakit One couriers available</p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <!-- Daakit Go Tab -->
                                <div class="results-container" id="daakitgoResults">
                                    <div class="row">
                                                 <div class="col-sm-12 col-md-4">
                                        <h7>Courier Mode Filter:</h7>
                                        <div class="input-group">
                                            <div class="input-group-append" data-toggle="buttons">
                                                <button type="button" data-nested="all" class="btn nested-tab active btn-outline-secondary text-dark filter_by shadow-none" style="color:#03A9F4; border:1px solid #03A9F4;">
                                                    <i class="mdi mdi-airplane"></i>
                                                    <input type="checkbox" name="radio1" value="all" class="filter"> All
                                                </button>
                                                <button type="button" data-nested="air" class="btn nested-tab btn-outline-secondary text-dark filter_by shadow-none" style="color:#03A9F4; border:1px solid #03A9F4;">
                                                    <i class="mdi mdi-airplane"></i>
                                                    <input type="checkbox" name="radio1" value="air" class="filter"> Air
                                                </button>
                                                <button type="button" data-nested="surface" class="btn nested-tab btn-outline-secondary text-dark filter_by shadow-none" style="color:#03A9F4; border:1px solid #03A9F4;">
                                                    <i class="mdi mdi-truck-fast"></i>
                                                    <input type="checkbox" name="radio1" value="surface" class="filter"> Surface
                                                </button>
                                            </div>
                                        </div>
                                        </div>
                           
                                        <div class="col-sm-12 col-md-8">
                                            <h7>Courier Weight Filter:</h7>
                                            <div class="input-group">
                                                <div class="input-group-append" data-toggle="buttons">
                                                    <button type="button" class="btn btn-outline-secondary text-dark filter_by shadow-none" style="color:#03A9F4; border:1px solid #03A9F4;">
                                                        <i class="mdi mdi-weight-kilogram"></i>
                                                        <input type="checkbox" name="radio2" value="500" class="filter"> 0.5 KG
                                                    </button>
                                                    <button type="button" class="btn btn-outline-secondary text-dark filter_by shadow-none" style="color:#03A9F4; border:1px solid #03A9F4;">
                                                        <i class="mdi mdi-weight-kilogram"></i>
                                                        <input type="checkbox" name="radio2" value="1000" class="filter"> 1 KG
                                                    </button>
                                                    <button type="button" class="btn btn-outline-secondary text-dark filter_by shadow-none" style="color:#03A9F4; border:1px solid #03A9F4;">
                                                        <i class="mdi mdi-weight-kilogram"></i>
                                                        <input type="checkbox" name="radio2" value="2000" class="filter"> 2 KG
                                                    </button>
                                                    
                                                    <button type="button" class="btn btn-outline-secondary text-dark filter_by shadow-none" style="color:#03A9F4; border:1px solid #03A9F4;">
                                                        <i class="mdi mdi-truck-delivery"></i>
                                                        <input type="checkbox" name="radio2" value="20000" class="filter"> Heavy
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="results-header">
                                        <h3 class="results-title" style="display:flex;align-items:center;gap:10px;margin-top:5px;">
                                            <img src="<?= base_url('assets/images/daakit_go.jpeg'); ?>" width="90">
                                            Powered by DAAKIT Go – Hyperlocal Delivery, Hyperfast
                                        </h3>
                                        <div class="results-count" id="daakitgoCount"><?= count($daakitGoCouriers) ?> couriers</div>
                                    </div>
                                    
                                    <div class="data-table-container">
                                        <table class="data-table" id="daakitgoTable">
                                            <thead>
                                                <tr>
                                                    <th><i class="fas fa-hashtag"></i> ID</th>
                                                    <th><i class="fas fa-shipping-fast"></i> Courier Name</th>
                                                    <th><i class="fas fa-rupee-sign"></i> Charges</th>
                                                    <th><i class="fas fa-tag"></i> Type</th>
                                                    <th><i class="fas fa-weight-scale"></i> Weight</th>
                                                    <th><i class="fas fa-check-circle"></i> Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="daakitgoTableBody">
                                                <?php if ($hasGo) { ?>
                                                    <?php foreach ($daakitGoCouriers as $courier) { ?>
                                                        <tr data-type="<?= strtolower($courier->courier_type ?? '') ?>" data-weight="<?= $courier->weight ?? '' ?>">
                                                            <td class="id-cell"><?= $courier->id ?></td>
                                                            <td class="courier-name-cell"><?= $courier->name ?></td>
                                                            <td class="cost-cell"><?= !empty($courier->charges) ? '₹' . round($courier->charges, 2) : 'N/A' ?></td>
                                                            <td class="days-cell"><?= $courier->courier_type ?? 'N/A' ?></td>
                                                            <td class="days-cell"><?= $courier->weight ?? 'N/A' ?></td>
                                                            <td class="tags-cell">
                                                                <button type="button" onclick="submitShipment('<?= $courier->id ?>')" class="btn btn-sm btn-primary">Ship Now</button>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } else { ?>
OBOBOB                                                    <tr>
                                                        <td colspan="6">
                                                            <div class="no-data">
                                                                <i class="fas fa-box-open"></i>
                                                                <p>No Daakit Go couriers available</p>
OBOBOB                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
OBOBOB                        <!-- Additional Options -->
                        <div class="row p-t-10 mtop20">
                            <div class="col-sm-12">
                                <div class="alert alert-info show" role="alert">
                                    <div class="custom-control custom-checkbox custom-control-inline">
                                        <input type="checkbox" name="dg_order" style="border: 1px solid #b7b7b7;" class="form-check-input" id="dg_order" value="1">
                                        <label class="custom-control" for="dg_order">Is handling dangerous goods risky?</label>
                                    </div>
                                </div>
                            </div>
                            <?php if (!empty($secure_shipment)) { ?>
                                <div class="col-sm-12">
                                    <div class="alert alert-info show" role="alert">
                                        <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" name="is_insurance" data-order-id="<?= !empty($order->id) ? $order->id : ''; ?>" class="form-check-input" id="is_insurance" value="1">
                                            <label class="custom-control-label" for="is_insurance">Opt-in for shipment insurance?<?php if (!empty($insurance_price)) { ?> (&#8377;<?= round($insurance_price, 2); ?>) <?php } ?></label>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>

                        <div class="col-sm-12">
                            <p style="color: red;" id="delhiveryselectmessage"></p>
                        </div>
                        
                    <?php } else { ?>
                        <div class="modal-body">
                            <div class="m-b-10">
                                <div class="label label inline-block" style="color:#ff6f00;border:1px solid #ff6f00;width:100%; line-height: 30px;">
                                    <?php if(!empty($error)){
                                        $error = (string)$error;
                                        if(trim($error) == 'No credit available. Please recharge.'){
                                            echo $error."<span style='margin-left: 173px;'><a target='_blank' href='".base_url('billing/rechage_wallet')."'>Recharge</a></span>";
                                        }
                                        else if(trim($error) == 'Please set default warehouse in settings')
                                        {
                                            echo $error."<span style='margin-left: 100px;'><a target='_blank' href='".base_url('warehouse')."'>Add Warehouse</a></span>";
                                        }
                                        else if(trim($error) == 'Please complete your company profile')
                                        {
                                            echo $error."<span style='margin-left: 150px;'><a target='_blank' href='".base_url('profile')."'>Add KYC</a></span>";
                                        }
                                        else{
                                            if(!empty($error))
                                                echo $error;
                                            else 
                                                echo 'Not Serviceable';
                                        }
                                    } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                
                <?php if (!empty($couriers)) { ?>
                    <div class="col-sm-12 text-right">
                        <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                <?php } ?>
            </div>
        </form>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="<?php echo base_url();?>assets/build/assets/plugins/notify/js/jquery.growl.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script type="text/javascript">
// Global function to submit shipment
function submitShipment(courierId) {
    document.getElementById('courier_id').value = courierId;
    document.getElementById("global-loader").style.display = "";
    
    $.ajax({
        url: '<?php echo base_url('orders/ship');?>',
        type: "POST",
        data: $(".ship_form").serialize(),
        cache: false,
        success: function(data) {
            console.log(data);
            if (data.success) {
                // Close modal and reload page
                var modal = bootstrap.Modal.getInstance(document.querySelector('.modal'));
                if (modal) modal.hide();
                location.reload();
            } else if (data.error) {
                alert(data.error);
            }
            document.getElementById("global-loader").style.display = "none";
        },
        error: function(xhr, status, error) {
            alert('Error: ' + error);
            document.getElementById("global-loader").style.display = "none";
        }
    });
}

// ================= FILTER FUNCTIONALITY FOR ALL TABS =================
$(".filter_by").click(function(){
    $('input[name="radio1"]').on('change', function() {
        $('input[name="radio1"]').not(this).prop('checked', false);
        $('input[name="radio1"]').parent().removeClass('active');
    });

    $('input[name="radio2"]').on('change', function() {
        $('input[name="radio2"]').not(this).prop('checked', false);
        $('input[name="radio2"]').parent().removeClass('active');
    });

    let timeout;
    timeout = setTimeout(getfiltervalue, 100);
});

function getfiltervalue() {
    var favorite = [];
    var favorite2 = [];
    var length1 = $("input[name='radio1']:checked").length;
    if(length1 == '0') {
        $('input[name="radio1"]').parent().removeClass('active');
    }
    var length2 = $("input[name='radio2']:checked").length;
    if(length2 == '0') {
        $('input[name="radio2"]').parent().removeClass('active');
    }

    $.each($("input[name='radio1']:checked"), function() {
        if ($(this).val() === 'air' || $(this).val() === 'surface') {
            favorite.push($(this).val());
        }
    });
    
    $.each($("input[name='radio2']:checked"), function() {  
        if($(this).val() == '500' || $(this).val() == '1000' || $(this).val() == '2000' || $(this).val() == "20000") {
            if($(this).val() == "20000") {
                favorite2.push("5000");
                favorite2.push("10000");
                favorite2.push("20000");
            } else {
                favorite2.push($(this).val());
            }
        }
    }); 

    var length = $("input[name='radio1']:checked").length;
    if (length > 0 || length2 > 0) {
        var check_weight = "" + favorite2.join(",");
        var type_val = "" + favorite.join(",");
        
        if(check_weight === '.' ) { check_weight = ''; }
        if(type_val === '.' ) { type_val = ''; }
        
        // Get current active tab
        const activeTab = document.querySelector('#mainTabContainer .tab.active')?.dataset.tab || 'ai';
        const activeNestedTab = document.querySelector('#aiNestedTabs .nested-tab.active')?.dataset.nested || 'all';
        
        // Apply filters based on active tab
        if (activeTab === 'daakitone') {
            filterDaakitOneTable(type_val, check_weight,activeNestedTab);
        } else if (activeTab === 'daakitgo') {
            filterDaakitGoTable(type_val, check_weight,activeNestedTab);
        } else if (activeTab === 'ai') {
            filterAITable(type_val, check_weight, activeNestedTab);
        }
        
        // Show filter info
        showFilterInfo(type_val, check_weight);
    } else {
        // Show all rows when no filters are selected
        showAllRows();
        hideFilterInfo();
    }
}

// Filter Daakit One Table
function filterDaakitOneTable(type, weight) {
    const typeArray = type ? type.split(',') : [];
    const weightArray = weight ? weight.split(',').map(w => parseInt(w)) : [];
    
    const rows = document.querySelectorAll('#daakitoneTableBody tr');
    let visibleCount = 0;
    let totalCount = 0;
    
    rows.forEach(row => {
        if (row.querySelector('td[colspan="6"]')) return;
        totalCount++;
        
        let showRow = true;
        const courierType = row.getAttribute('data-type')?.toLowerCase() || '';
        const courierWeight = row.getAttribute('data-weight')?.toLowerCase() || '';
        
        // Filter by type
        if (typeArray.length > 0) {
            const typeMatch = typeArray.some(t => {
                if (t === 'air') return courierType.includes('air');
                if (t === 'surface') return courierType.includes('surface') || courierType.includes('ground');
                return false;
            });
            showRow = showRow && typeMatch;
        }
        
        // Filter by weight
        if (weightArray.length > 0) {
            const weightMatch = weightArray.some(w => {
                const weightNum = parseInt(w);
                if (weightNum >= 5000) {
                    return courierWeight.includes('heavy') || 
                           courierWeight.includes('5000') || 
                           courierWeight.includes('10000') || 
                           courierWeight.includes('20000');
                } else {
                    return courierWeight.includes(weightNum.toString());
                }
            });
            showRow = showRow && weightMatch;
        }
        
        row.style.display = showRow ? '' : 'none';
        if (showRow) visibleCount++;
    });
    
    updateCount('daakitoneCount', visibleCount, totalCount);
}

// Filter Daakit Go Table
function filterDaakitGoTable(type, weight) {
    const typeArray = type ? type.split(',') : [];
    const weightArray = weight ? weight.split(',').map(w => parseInt(w)) : [];
    
    const rows = document.querySelectorAll('#daakitgoTableBody tr');
    let visibleCount = 0;
    let totalCount = 0;
    
    rows.forEach(row => {
        if (row.querySelector('td[colspan="6"]')) return;
        totalCount++;
        
        let showRow = true;
        const courierType = row.getAttribute('data-type')?.toLowerCase() || '';
        const courierWeight = row.getAttribute('data-weight')?.toLowerCase() || '';
        
        // Filter by type
        if (typeArray.length > 0) {
            const typeMatch = typeArray.some(t => {
                if (t === 'air') return courierType.includes('air');
                if (t === 'surface') return courierType.includes('surface') || courierType.includes('ground');
                return false;
            });
            showRow = showRow && typeMatch;
        }
        
        // Filter by weight
        if (weightArray.length > 0) {
            const weightMatch = weightArray.some(w => {
                const weightNum = parseInt(w);
                if (weightNum >= 5000) {
                    return courierWeight.includes('heavy') || 
                           courierWeight.includes('5000') || 
                           courierWeight.includes('10000') || 
                           courierWeight.includes('20000');
                } else {
                    return courierWeight.includes(weightNum.toString());
                }
            });
            showRow = showRow && weightMatch;
        }
        
        row.style.display = showRow ? '' : 'none';
        if (showRow) visibleCount++;
    });
    
    updateCount('daakitgoCount', visibleCount, totalCount);
}

// Filter AI Tables
function filterAITable(type, weight, nestedTab) {
    let tableBody;
    let countElement;
    
    if (nestedTab === 'all') {
        tableBody = document.getElementById('aiAllTableBody');
        countElement = document.getElementById('aiAllCount');
    } else if (nestedTab === 'air') {
        tableBody = document.getElementById('aiAirTableBody');
        countElement = document.getElementById('aiAirCount');
    } else if (nestedTab === 'surface') {
        tableBody = document.getElementById('aiSurfaceTableBody');
        countElement = document.getElementById('aiSurfaceCount');
    } else {
        return;
    }
    
    if (!tableBody) return;
    
    const typeArray = type ? type.split(',') : [];
    const weightArray = weight ? weight.split(',').map(w => parseInt(w)) : [];
    
    const rows = tableBody.querySelectorAll('tr');
    let visibleCount = 0;
    let totalCount = 0;
    
    rows.forEach(row => {
        if (row.querySelector('td[colspan="9"]')) return;
        totalCount++;
        
        let showRow = true;
        const courierType = row.getAttribute('data-type')?.toLowerCase() || '';
        const courierWeight = row.getAttribute('data-weight');
        const weightSlab = parseFloat(row.getAttribute('data-weight-slab') || '0.5');
        
        // Filter by type (air/surface)
        if (typeArray.length > 0) {
            const typeMatch = typeArray.some(t => {
                if (t === 'air') return courierType.includes('air');
                if (t === 'surface') return courierType.includes('surface') || courierType.includes('ground');
                return false;
            });
            showRow = showRow && typeMatch;
        }
        
        // Filter by weight slab
        if (weightArray.length > 0) {
            const weightMatch = weightArray.some(selectedWeight => {
                // Selected weight is in grams from the filter buttons
                if (selectedWeight >= 5000) { // Heavy category (5000g+)
                    return weightSlab >= 5; // 5 KG or more
                } else {
                    // For specific weights like 500g, 1000g, 2000g
                    // Check if the courier's weight slab matches the selected weight
                    const weightInGrams = weightSlab * 1000;
                    return Math.abs(weightInGrams - selectedWeight) < 100; // Allow small tolerance
                }
            });
            showRow = showRow && weightMatch;
        }
        
        row.style.display = showRow ? '' : 'none';
        if (showRow) visibleCount++;
    });
    
    if (countElement) {
        if (visibleCount === totalCount) {
            countElement.textContent = `${totalCount} couriers`;
        } else {
            countElement.textContent = `${visibleCount} of ${totalCount} couriers`;
        }
    }
}

// Show all rows when filters are cleared
function showAllRows() {
    // Show all Daakit One rows
    const daakitOneRows = document.querySelectorAll('#daakitoneTableBody tr');
    let daakitOneCount = 0;
    let daakitOneTotal = 0;
    daakitOneRows.forEach(row => {
        if (!row.querySelector('td[colspan="6"]')) {
            row.style.display = '';
            daakitOneCount++;
            daakitOneTotal++;
        }
    });
    updateCount('daakitoneCount', daakitOneCount, daakitOneTotal);
    
    // Show all Daakit Go rows
    const daakitGoRows = document.querySelectorAll('#daakitgoTableBody tr');
    let daakitGoCount = 0;
    let daakitGoTotal = 0;
    daakitGoRows.forEach(row => {
        if (!row.querySelector('td[colspan="6"]')) {
            row.style.display = '';
            daakitGoCount++;
            daakitGoTotal++;
        }
    });
    updateCount('daakitgoCount', daakitGoCount, daakitGoTotal);
    
    // Show all AI rows - re-render with current data
    if (apiData.all && apiData.all.length > 0) {
        renderAIData(apiData); // Re-render to show all rows
        
        // Update counts
        const activeNestedTab = document.querySelector('#aiNestedTabs .nested-tab.active')?.dataset.nested || 'all';
        const totalCount = apiData[activeNestedTab === 'all' ? 'all' : (activeNestedTab === 'air' ? 'air' : 'surface')].length;
        
        if (activeNestedTab === 'all') {
            aiAllCount.textContent = `${totalCount} couriers`;
        } else if (activeNestedTab === 'air') {
            aiAirCount.textContent = `${totalCount} couriers`;
        } else if (activeNestedTab === 'surface') {
            aiSurfaceCount.textContent = `${totalCount} couriers`;
        }
    }
}

// Update count display
function updateCount(elementId, visible, total) {
    const element = document.getElementById(elementId);
    if (element) {
        element.textContent = `${visible} of ${total} couriers`;
    }
}

function showFilterInfo(type, weight) {
    const filterInfo = document.getElementById('filterInfo');
    const filterInfoText = document.getElementById('filterInfoText');
    
    let typeText = '';
    let weightText = '';
    
    if (type) {
        const types = type.split(',').map(t => t.charAt(0).toUpperCase() + t.slice(1)).join(', ');
        typeText = `Mode: ${types}`;
    }
    
    if (weight) {
        const weights = weight.split(',').map(w => {
            if (w === '5000' || w === '10000' || w === '20000') {
                return 'Heavy';
            }
            return (parseInt(w) / 1000) + ' KG';
        }).join(', ');
        weightText = `Weight: ${weights}`;
    }
    
    if (typeText || weightText) {
        filterInfoText.textContent = `Active filters: ${typeText} ${weightText}`.trim();
        filterInfo.style.display = 'block';
    } else {
        hideFilterInfo();
    }
}

function hideFilterInfo() {
    document.getElementById('filterInfo').style.display = 'none';
}

// ================= WAREHOUSE CHANGE =================
$('#select_warehouse_dropdown').on('change', function() {
    var order_id = $(this).attr('data-order-id');
    var warehouse_id = $(this).val();
    $.ajax({
        url: '<?php echo base_url('orders/get_delivery_info/');?>' + order_id + '/' + warehouse_id,
        type: "GET",
        datatype: "JSON",
        cache: false,
        success: function(data) {
            $('#fulfillment_info').html(data);
        }
    });
});

// ================= ACCORDION CONTROL =================
$(document).ready(function () {
    $('#collapseOneCouriers').on('show.bs.collapse', function () {
        $('#collapseGoCouriers').collapse('hide');
    });
    $('#collapseGoCouriers').on('show.bs.collapse', function () {
        $('#collapseOneCouriers').collapse('hide');
    });
});

// ================= RECOMMENDATION SYSTEM =================
let currentSort = {
    key: null,
    order: 'asc'
};

let apiData = {
    all: [],
    air: [],
    surface: []
};

// DOM Elements for recommendation system
const mainTabs = document.querySelectorAll('#mainTabContainer .tab');
const resultsContainers = document.querySelectorAll('.results-container');
// const fetchButton = document.getElementById('fetchRecommendations');
const loadingIndicator = document.getElementById('recommendationLoading');
const aiLegend = document.getElementById('aiLegend');

// Nested tabs elements
const nestedTabs = document.querySelectorAll('.nested-tab');
const nestedContents = document.querySelectorAll('.nested-content');

// AI table bodies and count elements
const aiAllTableBody = document.getElementById('aiAllTableBody');
const aiAirTableBody = document.getElementById('aiAirTableBody');
const aiSurfaceTableBody = document.getElementById('aiSurfaceTableBody');

const aiAllCount = document.getElementById('aiAllCount');
const aiAirCount = document.getElementById('aiAirCount');
const aiSurfaceCount = document.getElementById('aiSurfaceCount');

// API CONFIG
const API_URL = 'https://pulse.daakit.com/recommend_courier';
const API_KEY = '67e8a932d4b1c8f5a2e0d3c7b4a1f9e8d2c6b5a4f3e2d1c0b9a8f7e6d5c4b3a2';

// ================= MAIN TAB SYSTEM =================
mainTabs.forEach(tab => {
    tab.addEventListener('click', () => {
        const tabId = tab.dataset.tab;
        
        // Update active tab
        mainTabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        
        // Show/hide legend based on tab
        if (tabId === 'ai') {
            aiLegend.style.display = 'flex';
        } else {
            aiLegend.style.display = 'none';
        }
        
        // Show active results container
        resultsContainers.forEach(container => {
            container.classList.remove('active');
        });
        document.getElementById(`${tabId}Results`).classList.add('active');
    });
});

// ================= NESTED TAB SYSTEM =================
nestedTabs.forEach(tab => {
    tab.addEventListener('click', () => {
        const nestedId = tab.dataset.nested;
        
        // Update active nested tab
        nestedTabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        
        // Show active nested content
        nestedContents.forEach(content => {
            content.classList.remove('active');
        });
        document.getElementById(`ai${nestedId.charAt(0).toUpperCase() + nestedId.slice(1)}Content`).classList.add('active');
        
        // Re-apply current filters if any
        const type_val = $("input[name='radio1']:checked").map(function() { return $(this).val(); }).get().join(',');
        const check_weight = $("input[name='radio2']:checked").map(function() { return $(this).val(); }).get().join(',');
        
        if (type_val || check_weight) {
            filterAITable(type_val, check_weight, nestedId);
        }
    });
});

// ================= HELPERS =================
function parseRtoRisk(val) {
    return parseFloat((val || '0').toString().replace('%','')) || 0;
}

function categorizeRtoRisk(value, min, max) {
    if (min === max) return 'medium-risk';
    const pos = (value - min) / (max - min);
    if (pos < 0.33) return 'low-risk';
    if (pos < 0.67) return 'medium-risk';
    return 'high-risk';
}

function analyzeRtoRisks(list) {
    if (!list?.length) return {min:0, max:0};
    const values = list.map(c => parseRtoRisk(c['RTO Risk']));
    return {
        min: Math.min(...values),
        max: Math.max(...values)
    };
}

// ================= TABLE ROW =================
function createTableRow(courier, rtoAnalysis) {
    const tags = courier.Tags ? courier.Tags.split(', ') : [];
    
    const tagHTML = tags.map(tag => {
        let cls = 'tag';
        if(tag.toLowerCase().includes('recommended')) cls += ' recommended';
        else if(tag.toLowerCase().includes('cheapest')) cls += ' cheapest';
        else if(tag.toLowerCase().includes('fastest')) cls += ' fastest';
        else if(tag.toLowerCase().includes('least rto')) cls += ' least-rto';
        else if(tag.toLowerCase().includes('balanced')) cls += ' balanced';
        return `<span class="${cls}">${tag}</span>`;
    }).join('');

    const rtoValue = parseRtoRisk(courier['RTO Risk']);
    const rtoClass = categorizeRtoRisk(rtoValue, rtoAnalysis.min, rtoAnalysis.max);

    const estimatedCost = typeof courier['Estimated Cost'] === 'number'
        ? courier['Estimated Cost'].toFixed(2)
        : courier['Estimated Cost'];

    // Extract type from courier name
    const courierType = courier['Courier Name']?.toLowerCase().includes('air') ? 'air' : 'surface';
    
    // Get weight slab from API response (convert to grams for display)
    const weightSlab = courier['Weight Slab'] || 0.5; // Default to 0.5 KG if not provided
    const weightInGrams = Math.round(weightSlab * 1000); // Convert to grams and round

    return `
    <tr data-type="${courierType}" data-weight="${weightInGrams}" data-weight-slab="${weightSlab}">
        <td class="rank-cell">${courier.Rank}</td>
        <td class="courier-name-cell">${courier['Courier Name']}</td>
        <td class="id-cell">${courier['Courier Id']}</td>
        <td class="weight-cell">${weightInGrams}</td>
        <td class="cost-cell">₹${estimatedCost}</td>
        <td class="days-cell">${courier['Estimated Delivery Days']} days</td>
        <td class="rto-cell ${rtoClass}">${courier['RTO Risk']}</td>
        <td class="confidence-cell">${courier['Score']}</td>
        <td class="tags-cell"><div class="tags-container">${tagHTML}</div></td>
        <td class="action-cell">
            <button type="button" onclick="submitShipment('${courier['Courier Id']}')" class="btn btn-sm btn-primary">
                Ship Now
            </button>
        </td>
    </tr>`;
}

// ================= SORTING FUNCTIONS =================
function sortData(dataArray, key) {
    if (!key || !dataArray) return dataArray;

    if (currentSort.key === key) {
        currentSort.order = currentSort.order === 'asc' ? 'desc' : 'asc';
    } else {
        currentSort.key = key;
        currentSort.order = 'asc';
    }

    return [...dataArray].sort((a, b) => {
        let valA = a[key];
        let valB = b[key];

        if (typeof valA === 'string' && valA.includes('%')) {
            valA = parseFloat(valA.replace('%', ''));
            valB = parseFloat(valB.replace('%', ''));
        }

        if (!isNaN(valA) && !isNaN(valB)) {
            valA = Number(valA);
            valB = Number(valB);
        }

        if (valA < valB) return currentSort.order === 'asc' ? -1 : 1;
        if (valA > valB) return currentSort.order === 'asc' ? 1 : -1;
        return 0;
    });
}

function handleSortClick(event) {
    const th = event.currentTarget;
    
    if (!apiData.all || apiData.all.length === 0) {
        return;
    }

    const key = th.getAttribute('data-key');
    const tableId = th.closest('table').id;

    document.querySelectorAll(`#${tableId} th[data-key]`).forEach(header => {
        header.classList.remove('sorted-asc', 'sorted-desc');
    });

    if (tableId === 'aiAllTable') {
        apiData.all = sortData(apiData.all, key);
    } else if (tableId === 'aiAirTable') {
        apiData.air = sortData(apiData.air, key);
    } else if (tableId === 'aiSurfaceTable') {
        apiData.surface = sortData(apiData.surface, key);
    }

    th.classList.add(currentSort.order === 'asc' ? 'sorted-asc' : 'sorted-desc');
    renderAIData(apiData);
}

function initializeSorting() {
    document.querySelectorAll('#aiAllTable th[data-key], #aiAirTable th[data-key], #aiSurfaceTable th[data-key]').forEach(th => {
        const existingIcon = th.querySelector('.sort-icon');
        if (existingIcon) {
            existingIcon.remove();
        }
        
        const iconSpan = document.createElement('span');
        iconSpan.classList.add('sort-icon');
        th.appendChild(iconSpan);
        
        th.removeEventListener('click', handleSortClick);
        th.addEventListener('click', handleSortClick);
    });
}

// ================= RENDER AI DATA =================
function renderAIData(data) {
    apiData = data;

    // Clear all tables
    aiAllTableBody.innerHTML = '';
    aiAirTableBody.innerHTML = '';
    aiSurfaceTableBody.innerHTML = '';

    // Analyze RTO risks for each category
    const allR = analyzeRtoRisks(data.all);
    const airR = analyzeRtoRisks(data.air);
    const surfaceR = analyzeRtoRisks(data.surface);

    // Render All couriers
    if (data.all && data.all.length > 0) {
        data.all.forEach(c => aiAllTableBody.innerHTML += createTableRow(c, allR));
        aiAllCount.textContent = `${data.all.length} couriers`;
    } else {
        aiAllCount.textContent = '0 couriers';
        aiAllTableBody.innerHTML = `
            <tr>
                <td colspan="9">
                    <div class="no-data">
                        <i class="fas fa-box-open"></i>
                        <p>No AI prediction data available</p>
                    </div>
                </td>
            </tr>
        `;
    }

    // Render Air couriers
    if (data.air && data.air.length > 0) {
        data.air.forEach(c => aiAirTableBody.innerHTML += createTableRow(c, airR));
        aiAirCount.textContent = `${data.air.length} couriers`;
    } else {
        aiAirCount.textContent = '0 couriers';
        aiAirTableBody.innerHTML = `
            <tr>
                <td colspan="9">
                    <div class="no-data">
                        <i class="fas fa-plane-slash"></i>
                        <p>No air courier predictions available</p>
                    </div>
                </td>
            </tr>
        `;
    }

    // Render Surface couriers
    if (data.surface && data.surface.length > 0) {
        data.surface.forEach(c => aiSurfaceTableBody.innerHTML += createTableRow(c, surfaceR));
        aiSurfaceCount.textContent = `${data.surface.length} couriers`;
    } else {
        aiSurfaceCount.textContent = '0 couriers';
        aiSurfaceTableBody.innerHTML = `
            <tr>
                <td colspan="9">
                    <div class="no-data">
                        <i class="fas fa-truck-slash"></i>
                        <p>No surface courier predictions available</p>
                    </div>
                </td>
            </tr>
        `;
    }

    initializeSorting();
}

// ================= NOTIFICATION =================
function showNotification(msg, type) {
    const div = document.createElement('div');
    div.className = `notification ${type}`;
    div.innerHTML = `
        <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
        <span>${msg}</span>
        <i class="fas fa-times close-notification"></i>
    `;

    const closeBtn = div.querySelector('.close-notification');
    closeBtn.addEventListener('click', () => {
        div.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => div.remove(), 300);
    });

    document.body.appendChild(div);

    setTimeout(() => {
        div.style.animation = 'slideOut .3s ease';
        setTimeout(() => div.remove(), 300);
    }, 4000);
}

// ================= GET DYNAMIC ORDER DATA =================
function getDynamicOrderData() {
    // Get order ID from hidden input
    const orderId = document.querySelector('input[name="order_id"]')?.value || '';
    
    // Get warehouse information
    const warehouseSelect = document.getElementById('select_warehouse_dropdown');
    const selectedWarehouseId = warehouseSelect?.value || '';
    const selectedWarehouseText = warehouseSelect?.options[warehouseSelect.selectedIndex]?.text || '';
    
    // Get RTO warehouse
    const rtoWarehouseSelect = document.getElementById('rto_warehouse_id');
    const selectedRtoWarehouseId = rtoWarehouseSelect?.value || '';
    
    // Check if order is COD
    const isCodElement = document.querySelector('input[name="is_cod"]');
    const isCod = isCodElement ? (isCodElement.checked ? 1 : 0) : 0;
    
    // Check for dangerous goods
    const dgOrderElement = document.getElementById('dg_order');
    const isDangerousGoods = dgOrderElement?.checked ? 1 : 0;
    
    // Check for insurance
    const insuranceElement = document.getElementById('is_insurance');
    const hasInsurance = insuranceElement?.checked ? 1 : 0;
    
    // Get active filters
    const selectedMode = document.querySelector('input[name="radio1"]:checked')?.value || '';
    const selectedWeight = document.querySelector('input[name="radio2"]:checked')?.value || '';
    
    // Get order amount
    const orderAmountElement = document.querySelector('input[name="order_amount"]') || 
                               document.querySelector('[data-order-amount]');
    const orderAmount = orderAmountElement ? 
                       parseFloat(orderAmountElement.value || orderAmountElement.getAttribute('data-order-amount')) : 
                       800;
    
    // Get weight slab (this is the actual order weight)
    const weightSlabElement = document.querySelector('input[name="weight_slab"]') || 
                              document.querySelector('[data-weight-slab]');
    let weightSlab = weightSlabElement ? 
                    parseFloat(weightSlabElement.value || weightSlabElement.getAttribute('data-weight-slab')) : 
                    0.5; // Default to 0.5 KG
    
    // Override with selected weight filter if active
    if (selectedWeight) {
        if (selectedWeight === '20000') {
            weightSlab = 20; // Heavy category
        } else {
            weightSlab = parseInt(selectedWeight) / 1000;
        }
    }
    
    // Get zone
    const zoneElement = document.querySelector('input[name="zone"]') || 
                        document.querySelector('[data-zone]');
    const zone = zoneElement ? 
                parseInt(zoneElement.value || zoneElement.getAttribute('data-zone')) : 
                2;
    
    // Get total products
    const totalProductsElement = document.querySelector('input[name="total_products"]') || 
                                 document.querySelector('[data-total-products]');
    const totalProducts = totalProductsElement ? 
                         parseInt(totalProductsElement.value || totalProductsElement.getAttribute('data-total-products')) : 
                         1;
    
    // Get order date
    const orderDateElement = document.querySelector('input[name="order_date"]') || 
                             document.querySelector('[data-order-date]');
    let orderDate = orderDateElement ? 
                   (orderDateElement.value || orderDateElement.getAttribute('data-order-date')) : 
                   new Date().toISOString().split('T')[0];
    
    if (orderDate && orderDate.includes('-')) {
        // Already in correct format
    } else if (orderDate) {
        const date = new Date(orderDate);
        if (!isNaN(date.getTime())) {
            orderDate = date.toISOString().split('T')[0];
        }
    }
    
    // Get pickup warehouse zip code
    const warehouseZipElement = document.querySelector('input[name="warehouse_zip"]') || 
                                document.querySelector('[data-warehouse-zip]') ||
                                document.querySelector('#warehouse_zip');
    const pickupWarehouseZip = warehouseZipElement ? 
                              (warehouseZipElement.value || warehouseZipElement.getAttribute('data-warehouse-zip') || warehouseZipElement.textContent) : 
                              110024;
    
    // Get shipping district
    const shippingDistrictElement = document.querySelector('input[name="shipping_district"]') || 
                                    document.querySelector('[data-shipping-district]') ||
                                    document.querySelector('#shipping_district');
    const shippingDistrict = shippingDistrictElement ? 
                            (shippingDistrictElement.value || shippingDistrictElement.getAttribute('data-shipping-district') || shippingDistrictElement.textContent) : 
                            "jaipur";
    
    // Get shipping state
    const shippingStateElement = document.querySelector('input[name="shipping_state"]') || 
                                 document.querySelector('[data-shipping-state]') ||
                                 document.querySelector('#shipping_state');
    const shippingState = shippingStateElement ? 
                         (shippingStateElement.value || shippingStateElement.getAttribute('data-shipping-state') || shippingStateElement.textContent) : 
                         "rajasthan";
    
    // Seller ID
    const sellerIdElement = document.querySelector('input[name="seller_id"]') || 
                            document.querySelector('[data-seller-id]');
    const sellerId = sellerIdElement ? 
                    parseInt(sellerIdElement.value || sellerIdElement.getAttribute('data-seller-id')) : 
                    20;
    
    return {
        seller_id: sellerId,
        order_amount: orderAmount,
        weight_slab: weightSlab,
        zone: zone,
        is_cod: isCod,
        total_products: totalProducts,
        order_date: orderDate,
        pickup_warehouse_zip: pickupWarehouseZip.toString(),
        shipping_district: shippingDistrict,
        shipping_state: shippingState,
        order_id: orderId,
        warehouse_id: selectedWarehouseId,
        warehouse_name: selectedWarehouseText,
        rto_warehouse_id: selectedRtoWarehouseId,
        is_dangerous_goods: isDangerousGoods,
        has_insurance: hasInsurance,
        selected_mode: selectedMode,
        selected_weight: selectedWeight
    };
}

// Auto-fetch when page loads after a short delay
setTimeout(() => {
    fetchRecommendations();
}, 500);

// ================= API CALL =================
async function fetchRecommendations() {
    loadingIndicator.style.display = 'block';
    
    // Hide all results while loading
    resultsContainers.forEach(container => {
        container.classList.remove('active');
    });

    try {
        const orderData = getDynamicOrderData();
        
        const payload = {
            seller_id: orderData.seller_id,
            order_amount: orderData.order_amount,
            weight_slab: orderData.weight_slab,
            zone: orderData.zone,
            is_cod: orderData.is_cod,
            total_products: orderData.total_products,
            order_date: orderData.order_date,
            pickup_warehouse_zip: orderData.pickup_warehouse_zip,
            shipping_district: orderData.shipping_district,
            shipping_state: orderData.shipping_state
        };

        console.log('=== SENDING DYNAMIC PAYLOAD ===');
        console.log('Order Data Collected:', orderData);
        console.log('API Payload:', payload);
        console.log('===============================');

        const res = await fetch(API_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'x-api-key': API_KEY
            },
            body: JSON.stringify(payload)
        });

        if (!res.ok) throw new Error(`Status ${res.status}`);

        const data = await res.json();
        
        apiResponse = {
            ...data,
            request_payload: payload,
            order_data: orderData
        };
        
        console.log('=== COMPLETE API RESPONSE ===');
        console.log('API Response Data:', data);
        console.log('===============================');
        
        loadingIndicator.style.display = 'none';
        
        document.getElementById('aiResults').classList.add('active');
        renderAIData(data);
        showNotification('AI recommendations loaded successfully!', 'success');

    } catch(err) {
        loadingIndicator.style.display = 'none';
        
        apiResponse = { error: err.message };
        
        console.log('=== API ERROR ===');
        console.error('Error:', err);
        console.log('=================');
        
        document.getElementById('aiResults').classList.add('active');

        const errorHtml = `
            <tr>
                <td colspan="9">
                    <div class="no-data">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Failed to fetch AI recommendations. Please try again.</p>
                        <p style="font-size: 14px; margin-top: 10px;">${err.message}</p>
                    </div>
                </td>
            </tr>
        `;
        
        aiAllTableBody.innerHTML = errorHtml;
        aiAirTableBody.innerHTML = errorHtml;
        aiSurfaceTableBody.innerHTML = errorHtml;
        
        aiAllCount.textContent = '0 couriers';
        aiAirCount.textContent = '0 couriers';
        aiSurfaceCount.textContent = '0 couriers';

        showNotification('Failed to fetch AI recommendations. Please try again.', 'error');
        console.error(err);
    }
}

// ================= EVENT LISTENERS =================
// if (fetchButton) {
//     fetchButton.addEventListener('click', fetchRecommendations);
// }

// Auto-fetch when modal opens
document.addEventListener('DOMContentLoaded', function() {
    const modalElement = document.querySelector('.modal');
    if (modalElement) {
        modalElement.addEventListener('shown.bs.modal', function() {
            setTimeout(function() {
                console.log('Fetching recommendations 1 second after modal is shown...');
                fetchRecommendations();
            }, 1000);
        });
    }
});

// Make submitShipment function globally available
window.submitShipment = submitShipment;
</script>
