<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <div class="d-flex justify-content-between align-items-center w-100">
        <h4 class="page-title">Communication Pricing</h4>
        <div class="d-flex gap-2">
            <div id="partner-edit">
                <button onclick="openPopup()" class="btn btn-sm btn-success">Set Pricing for Individual Partner</button>
            </div>
            <div id="edit-buttons">
                <button onclick="editAll()" class="btn btn-sm btn-success">Edit</button>
            </div>
            <div id="save-cancel-buttons" style="display: none;">
                <button onclick="saveAll()" class="btn btn-sm btn-success">Save</button>
                <button onclick="cancelAll()" class="btn btn-sm btn-success">Cancel</button>
            </div>
        </div>
    </div>
</div>


<!-- END PAGE-HEADER -->


<!-- Loader Overlay -->
<div id="page-loader">
  <div class="loader-content">
    <img src="../assets/images/dakit-favicon.gif" alt="Loading..." />
  </div>
</div>


<!-- START ROW-1 -->
<div class="row px-5">
	<div class="col-lg-12 col-md-12">
		<div class="card">
			<div class="card-body">
                <div class="table-responsive">
                    
                    <div id="main-content" style="display: flex; justify-content: center; align-items: center;">
                        <h2>Individual Communication Pricing</h2>
                        
                        <table id="individual-table">
                        <thead>
                            <tr>
                            <th style="width:25%;">Status</th>
                            <th>SMS</th>
                            <th>Email</th>
                            <th>WhatsApp</th>
                            <th>IVR</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Order Verification</td>
                                <td><input type="text" value="" id="new_sms" disabled /></td>
                                <td><input type="text" value="" id="new_email" disabled /></td>
                                <td><input type="text" value="" id="new_whatsapp" disabled /></td>
                                <td><input type="text" value="" id="new_ivr" disabled /></td>
                            </tr>
                            <tr>
                                <td>Confirmation / Acknowledgement</td>
                                <td><input type="text" value="" id="confirmation_acknowledgement_sms" disabled /></td>
                                <td><input type="text" value="" id="confirmation_acknowledgement_email" disabled /></td>
                                <td><input type="text" value="" id="confirmation_acknowledgement_whatsapp" disabled /></td>
                                <td><input type="text" value="" id="confirmation_acknowledgement_ivr"disabled /></td>
                            </tr>
                            <tr>
                                <td>Pending Pickup</td>
                                <td><input type="text" value="" id="pending_pickup_sms" disabled /></td>
                                <td><input type="text" value="" id="pending_pickup_email" disabled /></td>
                                <td><input type="text" value="" id="pending_pickup_whatsapp" disabled /></td>
                                <td><input type="text" value="" id="pending_pickup_ivr" disabled /></td>
                            </tr>
                            <tr>
                                <td>Shipped / In Transit</td>
                                <td><input type="text" value="" id="in_transit_sms" disabled /></td>
                                <td><input type="text" value="" id="in_transit_email" disabled /></td>
                                <td><input type="text" value="" id="in_transit_whatsapp" disabled /></td>
                                <td><input type="text" value="" id="in_transit_ivr" disabled /></td>
                            </tr>
                            <tr>
                                <td>Out For Delivery</td>
                                <td><input type="text" value="" id="out_for_delivery_sms" disabled /></td>
                                <td><input type="text" value="" id="out_for_delivery_email" disabled /></td>
                                <td><input type="text" value="" id="out_for_delivery_whatsapp" disabled /></td>
                                <td><input type="text" value="" id="out_for_delivery_ivr" disabled /></td>
                            </tr>
                            <tr>
                                <td>Delivered</td>
                                <td><input type="text" value="" id="delivered_sms" disabled /></td>
                                <td><input type="text" value="" id="delivered_email" disabled /></td>
                                <td><input type="text" value="" id="delivered_whatsapp" disabled /></td>
                                <td><input type="text" value="" id="delivered_ivr" disabled /></td>
                            </tr>
                            <tr>
                                <td>NDR</td>
                                <td><input type="text" value="" id="exception_sms" disabled /></td>
                                <td><input type="text" value="" id="exception_email" disabled /></td>
                                <td><input type="text" value="" id="exception_whatsapp" disabled /></td>
                                <td><input type="text" value="" id="exception_ivr" disabled /></td>
                            </tr>
                            <tr>
                                <td>RTO</td>
                                <td><input type="text" value="" id="rto_sms" disabled /></td>
                                <td><input type="text" value="" id="rto_email" disabled /></td>
                                <td><input type="text" value="" id="rto_whatsapp" disabled /></td>
                                <td><input type="text" value="" id="rto_ivr" disabled /></td>
                            </tr>
                        </tbody>
                        </table>

                        <h2>Bundled Communication Pricing</h2>
                        <table id="bundled-table">
                        <thead>
                            <tr>
                            <th style="width:25%;">Label</th>
                            <th>SMS</th>
                            <th>Email</th>
                            <th>WhatsApp</th>
                            <th>IVR</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                            <td>Bundle Price</td>
                            <td><input type="text" disabled /></td>
                            <td><input type="text" disabled /></td>
                            <td><input type="text" disabled /></td>
                            <td><input type="text" disabled /></td>
                            </tr>
                        </tbody>
                        </table>
                    </div>
                     
                </div>
            </div>
		</div>
	</div>
</div>
<!-- END ROW-1 -->


<!-- Modal -->
<div class="modal fade" id="sellerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5 class="modal-title">Select Seller</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        
        <!-- Search box -->
        <input 
          type="text" 
          id="sellerSearch" 
          class="form-control mb-2" 
          placeholder="Search seller..." 
          onkeyup="filterSellers()" 
        />

        <!-- Dropdown -->
        <select id="sellerSelect" class="form-select" size="5">
          <!-- options will be filled dynamically -->
        </select>
      </div>
      <div class="modal-footer">
        <!-- Submit button -->
        <button type="button" class="btn btn-primary" onclick="submitSeller()">Submit</button>
      </div>
    </div>
  </div>
</div>


<!-- Seller Pricing Modal -->
<div class="modal fade" id="sellerPricingModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5 class="modal-title">Seller Communication Pricing</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div id="seller-main-content" style="display: flex; flex-direction: column; align-items: center;">
          <h2>Individual Communication Pricing</h2>
          <table id="seller-individual-table">
            <thead>
              <tr>
                <th style="width:25%;">Status</th>
                <th>SMS</th>
                <th>Email</th>
                <th>WhatsApp</th>
                <th>IVR</th>
              </tr>
            </thead>
            <tbody>
              <tr><td>Order Verification</td>
                <td><input type="text" id="seller_new_sms" placeholder="0.00"></td>
                <td><input type="text" id="seller_new_email" placeholder="0.00"></td>
                <td><input type="text" id="seller_new_whatsapp" placeholder="0.00"></td>
                <td><input type="text" id="seller_new_ivr" placeholder="0.00"></td>
              </tr>
              <tr><td>Confirmation / Acknowledgement</td>
                <td><input type="text" id="seller_confirmation_acknowledgement_sms" placeholder="0.00"></td>
                <td><input type="text" id="seller_confirmation_acknowledgement_email" placeholder="0.00"></td>
                <td><input type="text" id="seller_confirmation_acknowledgement_whatsapp" placeholder="0.00"></td>
                <td><input type="text" id="seller_confirmation_acknowledgement_ivr" placeholder="0.00"></td>
              </tr>
              <tr><td>Pending Pickup</td>
                <td><input type="text" id="seller_pending_pickup_sms" placeholder="0.00"></td>
                <td><input type="text" id="seller_pending_pickup_email" placeholder="0.00"></td>
                <td><input type="text" id="seller_pending_pickup_whatsapp" placeholder="0.00"></td>
                <td><input type="text" id="seller_pending_pickup_ivr" placeholder="0.00"></td>
              </tr>
              <tr><td>Shipped / In Transit</td>
                <td><input type="text" id="seller_in_transit_sms" placeholder="0.00"></td>
                <td><input type="text" id="seller_in_transit_email" placeholder="0.00"></td>
                <td><input type="text" id="seller_in_transit_whatsapp" placeholder="0.00"></td>
                <td><input type="text" id="seller_in_transit_ivr" placeholder="0.00"></td>
              </tr>
              <tr><td>Out For Delivery</td>
                <td><input type="text" id="seller_out_for_delivery_sms" placeholder="0.00"></td>
                <td><input type="text" id="seller_out_for_delivery_email" placeholder="0.00"></td>
                <td><input type="text" id="seller_out_for_delivery_whatsapp" placeholder="0.00"></td>
                <td><input type="text" id="seller_out_for_delivery_ivr" placeholder="0.00"></td>
              </tr>
              <tr><td>Delivered</td>
                <td><input type="text" id="seller_delivered_sms" placeholder="0.00"></td>
                <td><input type="text" id="seller_delivered_email" placeholder="0.00"></td>
                <td><input type="text" id="seller_delivered_whatsapp" placeholder="0.00"></td>
                <td><input type="text" id="seller_delivered_ivr" placeholder="0.00"></td>
              </tr>
              <tr><td>NDR</td>
                <td><input type="text" id="seller_exception_sms" placeholder="0.00"></td>
                <td><input type="text" id="seller_exception_email" placeholder="0.00"></td>
                <td><input type="text" id="seller_exception_whatsapp" placeholder="0.00"></td>
                <td><input type="text" id="seller_exception_ivr" placeholder="0.00"></td>
              </tr>
              <tr><td>RTO</td>
                <td><input type="text" id="seller_rto_sms" placeholder="0.00"></td>
                <td><input type="text" id="seller_rto_email" placeholder="0.00"></td>
                <td><input type="text" id="seller_rto_whatsapp" placeholder="0.00"></td>
                <td><input type="text" id="seller_rto_ivr" placeholder="0.00"></td>
              </tr>
            </tbody>
          </table>

          <h2>Bundled Communication Pricing</h2>
          <table id="seller-bundled-table">
            <thead>
              <tr>
                <th style="width:25%;">Label</th>
                <th>SMS</th>
                <th>Email</th>
                <th>WhatsApp</th>
                <th>IVR</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Bundle Price</td>
                <td><input type="text" id="seller_bundled_sms" placeholder="0.00"></td>
                <td><input type="text" id="seller_bundled_email" placeholder="0.00"></td>
                <td><input type="text" id="seller_bundled_whatsapp" placeholder="0.00"></td>
                <td><input type="text" id="seller_bundled_ivr" placeholder="0.00"></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Buttons Section -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" id="removeSellerPricingBtn" onclick="removeSellerPricing()">Remove Seller Pricing</button>
        <button type="button" class="btn btn-primary" id="submitSellerPricingBtn" onclick="submitSellerPricing()">Submit</button>
      </div>
    </div>
  </div>
</div>





<script>
    function showLoader() {
        document.getElementById("page-loader").classList.remove("hidden-loader");
    }

    function hideLoader() {
        document.getElementById("page-loader").classList.add("hidden-loader");
    }

    var baseUrl = "<?php echo base_url(); ?>";
    let pricingData = { bundled: {}, individual: [] };
    const token = localStorage.getItem("token"); // or set manually
    let selectedId = "";

    async function fetchPricingData() {
        showLoader();
        try {
            const res = await fetch(`${baseUrl}index.php/api/CommunicationSettings/get_seller_pricings_service`, {
            headers: { Authorization: `Bearer ${token}` }
            });
            const json = await res.json();
            if (json.status) {
            pricingData = json.data;
            
            renderTables();
            } else {
            alert("Failed to load pricing data.");
            }
        } catch (error) {
            alert("API call failed.");
        } finally{
            hideLoader();
        }
    }

    let allSellers = [];

    // Open modal and fetch sellers
    function openPopup() {
        const myModal = new bootstrap.Modal(document.getElementById('sellerModal'));
        myModal.show();

        showLoader();
        fetch(`${baseUrl}index.php/api/CommunicationSettings/get_all_users`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Authorization": "Bearer " + token
            }
            })
        .then(res => res.json())
        .then(json => {
        if (json.status) {
            allSellers = json.data;
            populateDropdown(allSellers);
        } else {
            alert("Failed to load sellers.");
        }
        })
        .catch(() => alert("API call failed."))
        .finally(()=>hideLoader());
    }

    // Fill dropdown
    function populateDropdown(sellers) {
        const select = document.getElementById("sellerSelect");
        select.innerHTML = "";

        sellers.forEach(seller => {
            const option = document.createElement("option");
            option.value = seller.id;
            option.textContent = `${seller.fname} ${seller.lname} (${seller.email})`;
            select.appendChild(option);
        });
    }

    // Search filter
    function filterSellers() {
        const query = document.getElementById("sellerSearch").value.toLowerCase();
        const filtered = allSellers.filter(seller =>
            (`${seller.fname} ${seller.lname} ${seller.email}`).toLowerCase().includes(query)
        );
        populateDropdown(filtered);
    }

    // Submit selected seller
    async function submitSeller() {
        document.getElementById("sellerSearch").value = "";
        const select = document.getElementById("sellerSelect");
        selectedId = select.value;
        if (!selectedId) {
            alert("Please select a seller.");
            return;
        }

        const selectedSeller = allSellers.find(s => s.id == selectedId);

        showLoader();
        try {
            // Run both fetches in parallel
            const [indResp, bunResp] = await Promise.all([
                fetch(`${baseUrl}index.php/api/CommunicationSettings/get_seller_individual_pricings`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Authorization": "Bearer " + token
                    },
                    body: JSON.stringify({ seller_id: selectedId })
                }).then(res => res.json()),

                fetch(`${baseUrl}index.php/api/CommunicationSettings/get_seller_bundled_pricings`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Authorization": "Bearer " + token
                    },
                    body: JSON.stringify({ seller_id: selectedId })
                }).then(res => res.json())
            ]);

            let sellerPricingData = { bundled: {}, individual: [] };
            if (indResp.status) sellerPricingData.individual = indResp.data;
            if (bunResp.status) sellerPricingData.bundled = bunResp.data;

            if(!indResp.status && !bunResp.status){
                console.log("Hello");
                
                showLoader();
                try {
                    const res = await fetch(`${baseUrl}index.php/api/CommunicationSettings/get_seller_pricings_service`, {
                    headers: { Authorization: `Bearer ${token}` }
                    });
                    const json = await res.json();
                    if (json.status) {
                    sellerPricingData.individual = json.data.individual;
                    sellerPricingData.bundled = json.data.bundled?.[0];
                    console.log(sellerPricingData);
                    
                    
                    renderTables();
                    } else {
                    alert("Failed to load pricing data.");
                    }
                } catch (error) {
                    alert("API call failed.");
                } finally{
                    hideLoader();
                }
            }

            renderSellerTables(sellerPricingData);

            // Close seller selection modal
            const myModal = bootstrap.Modal.getInstance(document.getElementById('sellerModal'));
            myModal.hide();

            // Open Seller Pricing Modal
            const modal = new bootstrap.Modal(document.getElementById('sellerPricingModal'));
            modal.show();

        } finally {
            hideLoader();
        }
    }

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
                document.getElementById(`${prefix}_sms`).value = item.sms;
                document.getElementById(`${prefix}_email`).value = item.email;
                document.getElementById(`${prefix}_whatsapp`).value = item.whatsapp;
                document.getElementById(`${prefix}_ivr`).value = item.ivr;
            }
        });

        if (sellerPricingData.bundled) {
            const bundled = sellerPricingData.bundled;
            document.getElementById("seller_bundled_sms").value = bundled.sms ? bundled.sms : "" ;
            document.getElementById("seller_bundled_email").value = bundled.email ? bundled.email : "";
            document.getElementById("seller_bundled_whatsapp").value = bundled.whatsapp ? bundled.whatsapp : "";
            document.getElementById("seller_bundled_ivr").value = bundled.ivr ? bundled.ivr : "";
        }
    }

    // Reset seller pricing modal when closed
    document.getElementById("sellerPricingModal").addEventListener("hidden.bs.modal", function () {
        // Reset all input fields inside seller pricing modal
        const inputs = this.querySelectorAll("input[type='text']");
        inputs.forEach(input => input.value = "");
        selectedId = "";

        // Optionally clear seller-specific data if needed
        // Example: reset bundled and individual pricing objects
        // sellerPricingData = { bundled: {}, individual: [] };
    });

    async function removeSellerPricing(){
        showLoader();
        try {
            // Run both fetches in parallel
            const [indResp, bunResp] = await Promise.all([
                fetch(`${baseUrl}index.php/api/CommunicationSettings/remove_seller_individual_pricing`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Authorization": "Bearer " + token
                    },
                    body: JSON.stringify({ seller_id: selectedId })
                }).then(res => res.json())
                .then(resp=>{
                    if(!resp.status){
                        alert("Error occured while removing Seller's Individual Pricing");
                    }
                }),
                
                fetch(`${baseUrl}index.php/api/CommunicationSettings/remove_seller_bundled_pricing`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Authorization": "Bearer " + token
                    },
                    body: JSON.stringify({ seller_id: selectedId })
                }).then(res => res.json())
                .then(resp=>{
                    if(!resp.status){
                        alert("Error occured while removing Seller's Bundle Pricing");
                    }
                })
            ]);
            
            if(indResp.status || bunResp.status){
                if(indResp.status && bunResp.status){
                    alert("Seller's individual and bundle pricing plan removed successfully");

                }
                else if(indResp.status){
                    alert("Seller's individual pricing plan removed successfully");
                }
                else{
                    alert("Seller's bundle pricing plan removed successfully");
                }
            }
        } finally {
            selectedId = "";
            // Open Seller Pricing Modal
            const modal = new bootstrap.Modal(document.getElementById('sellerPricingModal'));
            modal.hide();
            hideLoader();
            location.reload();
        }
    }

    async function submitSellerPricing() {
        if (!selectedId) {
            alert("No seller selected.");
            return;
        }

        // Map of status → prefix (to match your seller input fields)
        const idMap = {
            "new": { status: "new", prefix: "seller_new" },
            "confirmation acknowledgement": { status: "confirmation acknowledgement", prefix: "seller_confirmation_acknowledgement" },
            "pending pickup": { status: "pending pickup", prefix: "seller_pending_pickup" },
            "in transit": { status: "in transit", prefix: "seller_in_transit" },
            "out for delivery": { status: "out for delivery", prefix: "seller_out_for_delivery" },
            "delivered": { status: "delivered", prefix: "seller_delivered" },
            "exception": { status: "exception", prefix: "seller_exception" },
            "rto in transit": { status: "rto in transit", prefix: "seller_rto" }
        };

        // Build individual pricing array
        const individualData = Object.values(idMap).map(({ status, prefix }) => ({
            status: status,
            sms: document.getElementById(`${prefix}_sms`).value,
            email: document.getElementById(`${prefix}_email`).value,
            whatsapp: document.getElementById(`${prefix}_whatsapp`).value,
            ivr: document.getElementById(`${prefix}_ivr`).value
        }));

        // Bundled pricing
        const bundledData = {
            sms: document.getElementById("seller_bundled_sms").value,
            email: document.getElementById("seller_bundled_email").value,
            whatsapp: document.getElementById("seller_bundled_whatsapp").value,
            ivr: document.getElementById("seller_bundled_ivr").value
        };

        showLoader();
        try {
            // 1. Save individual pricing
            const resIndividual = await fetch(`${baseUrl}index.php/api/CommunicationSettings/update_seller_individual_pricing`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Authorization: `Bearer ${token}`
                },
                body: JSON.stringify({
                    seller_id: selectedId,
                    pricings: individualData
                })
            });

            const resultIndividual = await resIndividual.json();
            if (!resultIndividual.status) {
                alert("Failed to save individual pricing.");
                console.error(resultIndividual);
                return; // stop execution if fails
            }

            // 2. Save bundled pricing
            const resBundled = await fetch(`${baseUrl}index.php/api/CommunicationSettings/update_seller_bundled_pricing`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Authorization: `Bearer ${token}`
                },
                body: JSON.stringify({
                    seller_id: selectedId,
                    sms: bundledData.sms,
                    email: bundledData.email,
                    whatsapp: bundledData.whatsapp,
                    ivr: bundledData.ivr,
                })
            });

            const resultBundled = await resBundled.json();
            if (!resultBundled.status) {
                alert("Failed to save bundled pricing.");
                console.error(resultBundled);
                return;
            }

            // Success only if both saved properly
            alert("Seller pricing saved successfully.");
            const modal = bootstrap.Modal.getInstance(document.getElementById('sellerPricingModal'));
            modal.hide();
            location.reload();

        } catch (error) {
            console.error(error);
            alert("Failed to save seller pricing due to network/server error.");
        } finally {
            hideLoader();
        }

    }



    function renderTables() {
        // Map for individual input field IDs
        const idMap = {
            "new": "new",
            "confirmation acknowledgement": "confirmation_acknowledgement",
            "pending pickup": "pending_pickup",
            "in transit": "in_transit",
            "out for delivery": "out_for_delivery",
            "delivered": "delivered",
            "exception": "exception",
            "rto in transit": "rto"
        };

        pricingData.individual.forEach(item => {
            const prefix = idMap[item.status];
            
            if (prefix) {
                document.getElementById(`${prefix}_sms`).value = item.sms;
                document.getElementById(`${prefix}_email`).value = item.email;
                document.getElementById(`${prefix}_whatsapp`).value = item.whatsapp;
                document.getElementById(`${prefix}_ivr`).value = item.ivr;
            }
        });

        const bundled = pricingData.bundled[0];
        const bundledInputs = document.querySelectorAll("#bundled-table tbody input");
        bundledInputs[0].value = bundled.sms;
        bundledInputs[1].value = bundled.email;
        bundledInputs[2].value = bundled.whatsapp;
        bundledInputs[3].value = bundled.ivr;
    }

    function editAll() {
        document.querySelectorAll("input").forEach(input => input.disabled = false);

        // Show Save/Cancel, hide Edit
        document.getElementById("edit-buttons").style.display = "none";
        document.getElementById("save-cancel-buttons").style.display = "block";
    }

    function cancelAll() {
        renderTables();
        document.querySelectorAll("input").forEach(input => input.disabled = true);

        // Show Edit, hide Save/Cancel
        document.getElementById("edit-buttons").style.display = "block";
        document.getElementById("save-cancel-buttons").style.display = "none";
    }

    async function saveAll() {
        const idMap = {
            "new": { id: "1", prefix: "new" },
            "pending pickup": { id: "2", prefix: "pending_pickup" },
            "in transit": { id: "3", prefix: "in_transit" },
            "out for delivery": { id: "4", prefix: "out_for_delivery" },
            "delivered": { id: "5", prefix: "delivered" },
            "exception": { id: "6", prefix: "exception" },
            "confirmation acknowledgement": { id: "8", prefix: "confirmation_acknowledgement" },
            "rto in transit": { id: "9", prefix: "rto" },
        };

        const individualData = Object.entries(idMap).map(([status, { id, prefix }]) => ({
            id: id,
            status: status,
            sms: document.getElementById(`${prefix}_sms`).value,
            email: document.getElementById(`${prefix}_email`).value,
            whatsapp: document.getElementById(`${prefix}_whatsapp`).value,
            ivr: document.getElementById(`${prefix}_ivr`).value
        }));

        const bundledInputs = document.querySelectorAll("#bundled-table tbody input");
        const bundledData = [
            {
                id: "1",  // Assuming ID 1 for bundled pricing
                sms: bundledInputs[0].value,
                email: bundledInputs[1].value,
                whatsapp: bundledInputs[2].value,
                ivr: bundledInputs[3].value
            }
        ];
        showLoader();
        try {
            const res = await fetch(`${baseUrl}index.php/api/CommunicationSettings/set_seller_pricings_service`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Authorization: `Bearer ${token}`
                },
                body: JSON.stringify({
                    bundled: bundledData,
                    individual: individualData
                })
            });

            const result = await res.json();
            if (result.status) {
                alert("Pricing saved successfully.");
                cancelAll();
            } else {
                alert("Save failed. Check server logs.");
            }
        } catch (error) {
            alert("Failed to save data.");
        } finally{
            hideLoader();
        }
        fetchPricingData();
    }




    fetchPricingData();
</script>


<style>
    #page-loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255,255,255,0.9); /* semi-transparent white */
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 99999;
    }
    .loader-content img {
        width: 80px;
        height: 80px;
        animation: spin 2s linear infinite;
    }
    .hidden-loader {
        display: none !important;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    table {
        width: 90%;
        margin: 10px auto;
        border-collapse: collapse;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        background-color: #ffffff;
    }

    th {
        background-color: #f0f4f8;
        color: #333;
        font-weight: 600;
    }

    th, td {
        text-align: center;
    }

    input[type="text"] {
        width: 100%;
        padding: 6px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 14px;
        text-align: center;
    }

    button {
        margin: 10px 8px;
        padding: 10px 20px;
        background-color: #007bff;
        border: none;
        color: white;
        border-radius: 5px;
        font-size: 15px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #0056b3;
    }

    h2 {
        color: #444;
        font-size: 22px;
        display: inline-block;
        margin: 10px 0;
    }

    #main-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
    }

    .button-group {
        text-align: center;
        margin: 30px auto;
    }
</style>
