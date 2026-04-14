<!-- START PAGE-HEADER -->
<div class="page-header main-container container-fluid px-5">
    <h4 class="page-title">CX</h4>
</div>
<!-- END PAGE-HEADER -->

<!-- Loader Overlay -->
<div id="page-loader">
  <div class="loader-content">
    <img src="assets/images/dakit-favicon.gif" alt="Loading..." />
  </div>
</div>

<!-- START ROW-1 -->
<div class="row px-5">
	<div class="col-lg-12 col-md-12">
		<div class="card">
			<div class="card-body">
                <div class="table-responsive">
                    <div style="display: flex; justify-content: right; align-items: center; gap: 10px; padding-bottom: 10px;">
                        <button class="btn btn-sm btn-success" id="open-branding">Set Brand</button>
                        <div id="communication_plan">
                        
                        </div>
                    </div>
                    <div id="main-content" style="display: flex; justify-content: center; align-items: center;">

                    </div>
                    <div>*GST Additional(18%)</div>
                    <div>*For API orders, the delivery of all status messages for each order is not guaranteed.</div>
                     
                </div>
            </div>
		</div>
	</div>
</div>
<!-- END ROW-1 -->

<!-- Branding Popup Modal -->
<div id="branding-modal" class="branding-modal d-none">
  <div class="branding-modal-content" id="branding-modal-content">
    <span class="branding-close-btn" onclick="toggleBrandingModal()">✖</span>
    <h5>Set Brand</h5>
    <form id="branding-form">
      <div class="form-check">
        <input class="form-check-input" type="radio" name="branding" id="branding-company" value="" checked>
        <label class="form-check-label" for="branding-company">Use Company Name</label>
      </div>
      <div style="color: gray;">-----OR-----</div>
      <div class="form-check mt-2">
        <input class="form-check-input" type="radio" name="branding" id="branding-manual" value="manual">
        <label class="form-check-label" for="branding-manual">Set Brand Name</label>
      </div>
      <input type="text" id="manual-branding-input" class="form-control mt-2 d-none" placeholder="Enter custom branding" />
      <button type="submit" class="btn btn-sm btn-success mt-3">Save Branding</button>
    </form>
  </div>
</div>

<script>
    function showLoader() {
        document.getElementById("page-loader").classList.remove("hidden-loader");
    }

    function hideLoader() {
        document.getElementById("page-loader").classList.add("hidden-loader");
    }

    function getServicesList(type) {
        const services = {
            sms: ["Pending Pickup", "Shipped / In Transit", "Out For Delivery", "Delivered", "NDR", "RTO in Transit"],
            email: ["Pending Pickup", "Shipped / In Transit", "Out For Delivery", "Delivered", "NDR", "RTO in Transit"],
            whatsapp: ["Order Verification", "Confirmation / Acknowledgement", "Pending Pickup", "Shipped / In Transit", "Out For Delivery", "Delivered", "NDR", "RTO in Transit"],
            ivr: ["Comming Soon..."],
        };
        return services[type] || [];
    }


    document.addEventListener('DOMContentLoaded', function () {
        var baseUrl = "<?php echo base_url(); ?>";    
        const token = localStorage.getItem('token');
        if (!token) {
            alert("Token missing in localStorage");
            return;
        }
        showLoader();
        fetch(`${baseUrl}index.php/api/CommunicationSettings/get_communication_plan`, {
            method: "POST",
            headers: {Authorization: `Bearer ${token}`},
        })
        .then(res =>{
            if(!res.ok) throw new Error(`HTTP error ${res.status}`);
            return res.json();
        })
        .then(data=>{
            if (data.communication_plan === "bundled") {
                showLoader();
                fetch(`${baseUrl}index.php/api/CommunicationSettings/get_seller_bundled_pricings`, {
                    method: "POST",
                    headers: { Authorization: `Bearer ${token}` }
                })
                .then(res => res.json())
                .then(pricingData => {
                    price = pricingData.data;

                    document.getElementById("communication_plan").innerHTML = `
                        <button class="btn btn-sm btn-success" id="open-individual">Set Individual Services</button>
                    `;

                    const div = document.getElementById("main-content");
                    div.innerHTML = `
                        <div class="bundle-info-box">
                            <h5>Set Bundle Plan</h5>

                            ${["sms", "email", "whatsapp", "ivr"].map((type, i) => {
                            const checked = data[`communication_specific${i + 1}`] === type ? "checked" : "";
                            const disabled = type === "ivr" ? "disabled" : "";
                            const priceVal = price[type] || 0;
                            return `
                                <div class="form-group">
                                    <label>
                                        ${type === 'sms' ? "SMS" : type === 'ivr' ? "IVR" : type.charAt(0).toUpperCase() + type.slice(1)}
                                        
                                    </label>
                                    <label class="toggle-container">
                                        <input
                                            type="checkbox"
                                            id="bundle-${type}"
                                            class="cstm-switch-bundle"
                                            ${checked}
                                            ${disabled}
                                        />
                                        <span class="slider"></span>
                                    </label>
                                    <div class="price-label">₹${priceVal}</div>
                                    <span class="info-icon" title="Available Services">
                                        &#9432;
                                        <div class="info-popup">
                                            <ul>
                                                ${getServicesList(type).map(service => `<li>${service}</li>`).join('')}
                                            </ul>
                                        </div>
                                    </span>

                                </div>
                            `;

                            }).join("")}
                        </div>
                    `;
                    
                    // Apply toggle styles
                    updateBundleToggleUI();
                })
                .catch(err =>{
                    fetch(`${baseUrl}index.php/api/CommunicationSettings/get_bundel_price`, {
                        method: "POST",
                        headers: { Authorization: `Bearer ${token}` }
                    })
                    .then(res => res.json())
                    .then(pricingData => {
                        price = pricingData.data;

                        document.getElementById("communication_plan").innerHTML = `
                            <button class="btn btn-sm btn-success" id="open-individual">Set Individual Services</button>
                        `;

                        const div = document.getElementById("main-content");
                        div.innerHTML = `
                            <div class="bundle-info-box">
                                <h5>Set Bundle Plan</h5>

                                ${["sms", "email", "whatsapp", "ivr"].map((type, i) => {
                                const checked = data[`communication_specific${i + 1}`] === type ? "checked" : "";
                                const disabled = type === "ivr" ? "disabled" : "";
                                const priceVal = price[type] || 0;
                                return `
                                    <div class="form-group">
                                        <label>
                                            ${type === 'sms' ? "SMS" : type === 'ivr' ? "IVR" : type.charAt(0).toUpperCase() + type.slice(1)}
                                            
                                        </label>
                                        <label class="toggle-container">
                                            <input
                                                type="checkbox"
                                                id="bundle-${type}"
                                                class="cstm-switch-bundle"
                                                ${checked}
                                                ${disabled}
                                            />
                                            <span class="slider"></span>
                                        </label>
                                        <div class="price-label">₹${priceVal}</div>
                                        <span class="info-icon" title="Available Services">
                                            &#9432;
                                            <div class="info-popup">
                                                <ul>
                                                    ${getServicesList(type).map(service => `<li>${service}</li>`).join('')}
                                                </ul>
                                            </div>
                                        </span>

                                    </div>
                                `;

                                }).join("")}
                            </div>
                        `;
                        
                        // Apply toggle styles
                        updateBundleToggleUI();
                    })
                    .catch(err => {
                        alert("Failed to fetch bundle pricing. Please try again later.");
                    })
                    .finally(()=>{
                        hideLoader();
                    })
                })
                .finally(()=>hideLoader());
            }
            else{
                document.getElementById("communication_plan").innerHTML = `
                    <button class="btn btn-sm btn-success" id="open-bundle">Set Bundle Plan</button>
                `;

                const temp = [
                    { status: "new" },
                    { status: "confirmation acknowledgement" },
                    { status: "pending pickup" },
                    { status: "in transit" },
                    { status: "out for delivery" },
                    { status: "delivered" },
                    { status: "exception" },
                    { status: "rto in transit" },
                    
                ];

                // Declare global pricing map
                const pricingMap = new Map();

                // Fetch pricing first
                showLoader();
                fetch(`${baseUrl}index.php/api/CommunicationSettings/get_seller_individual_pricings`, {
                    method: "POST",
                    headers: { Authorization: `Bearer ${token}` }
                })
                .then(res => {
                    if (!res.ok) throw new Error(`HTTP error ${res.status}`);
                    return res.json();
                })
                .then(pricingData => {
                    console.log(pricingData);
                    
                    pricingData.data?.forEach(item => {
                        if (item.status) pricingMap.set(item.status.toLowerCase(), item);
                    });
                })
                .catch(err =>{
                    console.log("No individual data exist");
                    showLoader();
                    fetch(`${baseUrl}index.php/api/CommunicationSettings/get_individual_price`, {
                        method: "POST",
                        headers: { Authorization: `Bearer ${token}` }
                    })
                    .then(res => {
                        if (!res.ok) throw new Error(`HTTP error ${res.status}`);
                        return res.json();
                    })
                    .then(pricingData => {
                        console.log(pricingData);
                        
                        pricingData.data?.forEach(item => {
                            if (item.status) pricingMap.set(item.status.toLowerCase(), item);
                        });
                    })
                    .catch(err => {
                        alert("Error fetching pricing info");
                    })
                    .finally(()=>{
                        hideLoader();
                    })
                })
                .finally(()=>{
                    hideLoader();
                    window.showImage = function(id) {
                        document.getElementById(id).style.display = 'block';
                    };
            
                    window.hideImage = function(id) {
                        document.getElementById(id).style.display = 'none';
                    };
                    fetch(`${baseUrl}index.php/api/CommunicationSettings/getSettings`, {
                        headers: { Authorization: `Bearer ${token}` }
                    })
                    .then(res => {
                        if (!res.ok) throw new Error(`HTTP error ${res.status}`);
                        return res.json();
                    })
                    .then(data => {
                        if (!data.status) {
                            throw new Error("Invalid data format received from API");
                        }

                        function createChannelCell(status, channel, apiItem, priceItem) {
                            const td = document.createElement("td");
                            const isChecked = apiItem[channel] === 'yes';
                            const uniqueId = `${status}-${channel}-modal`.replace(/\s+/g, '-');

                            td.innerHTML = `
                                <div style="position: relative; display: flex; align-items: center; gap: 6px;">
                                    <label style="position: relative; display: inline-block; width: 40px; height: 20px;">
                                    <input
                                        type="checkbox"
                                        class="cstm-switch-input"
                                        data-status="${status}"
                                        data-channel="${channel}"
                                        style="opacity: 0; width: 0; height: 0;"
                                        ${isChecked ? 'checked' : ''}
                                        ${channel === 'ivr' && 'disabled'}
                                        ${status === 'new' && channel !== 'whatsapp' && 'disabled'}
                                        ${status === 'confirmation acknowledgement' && channel !== 'whatsapp' && 'disabled'}
                                        
                                    />
                                    <span style="
                                        position: absolute;
                                        ${channel === 'ivr' || (status === 'new' && channel !== 'whatsapp') || (status === 'confirmation acknowledgement' && channel !== 'whatsapp') ? "cursor:not-allowed;" : "cursor:pointer;"}
                                        top: 0; left: 0; right: 0; bottom: 0;
                                        background-color: ${channel === 'ivr' || (status === 'new' && channel != 'whatsapp') || (status === 'confirmation acknowledgement' && channel !== 'whatsapp') ? '#f8d7da' : isChecked ? '#4CAF50' : '#ccc'};
                                        transition: .4s;
                                        border-radius: 34px;
                                    "></span>
                                    <span style="
                                        position: absolute;
                                        height: 16px;
                                        width: 16px;
                                        left: 2px;
                                        bottom: 2px;
                                        background-color: white;
                                        transition: .4s;
                                        border-radius: 50%;
                                        transform: ${isChecked ? 'translateX(20px)' : 'translateX(0)'};
                                    "></span>
                                    </label>

                                    <div style="display: flex; flex-direction: column; align-items: center;">
                                        ${channel === 'ivr' ? `` : `<i class="icon" onclick="showImage('${uniqueId}')">ℹ️</i>` }
                                        <small style="color: #888; font-size: 12px;">
                                            ₹${priceItem[channel] || '0.00'}
                                        </small>
                                    </div>

                                    <div id="${uniqueId}" class="centered-image" style="display: none;">
                                        <span class="close-btn" style="color: red" onclick="hideImage('${uniqueId}')">X</span>
                                        <img src="${baseUrl}assets/images/popups/${status}${channel}.png" alt="Preview Image" />
                                    </div>
                                </div>
                            `;
                            updateBundleToggleUI();

                            return td;
                        }

                        
                        // Create a map of status -> settings from API
                        const apiMap = new Map();
                        data.data?.forEach(item => {
                            if (item.status) apiMap.set(item.status.toLowerCase(), item);
                        });
                        const div = document.getElementById("main-content");
                        div.innerHTML = `
                            <table class="table" id="responsive-datatable" style="border: 1px solid #ddd; border-radius: 8px;">
                                <thead>
                                    <tr>
                                        <th style="border: 1px solid #ddd; border-radius: 8px;"><span class="bold">ORDER STATUS</span></th>
                                        <th style="border: 1px solid #ddd; border-radius: 8px;"><span class="bold">SMS <span style="color: gray" id="sms-pricing"></span> </span></th>
                                        <th style="border: 1px solid #ddd; border-radius: 8px;"><span class="bold">EMAIL <span style="color: gray" id="email-pricing"></span> </span></th>
                                        <th style="border: 1px solid #ddd; border-radius: 8px;"><span class="bold">WHATSAPP <span style="color: gray" id="whatsapp-pricing"></span> </span></th>
                                        <th style="border: 1px solid #ddd; border-radius: 8px;"><span class="bold">IVR  <span style="color: gray" id="ivr-pricing"></span></span></th>
                                    </tr>
                                </thead>
                                <tbody id="settings-tbody">
                                
                                </tbody>
                            </table>
                        `;
            
                        const tbody = document.getElementById("settings-tbody");
                        tbody.innerHTML = '';

                        // Insert "NEW ORDER" header row (merged visually)
                        const headerRow = document.createElement("tr");
                        headerRow.style.borderBottom = "none"; // remove border
                        const headerTd = document.createElement("td");
                        const headerTd2 = document.createElement("td");
                        const headerTd3 = document.createElement("td");
                        const headerTd4 = document.createElement("td");
                        const headerTd5 = document.createElement("td");
                        headerTd.textContent = "NEW ORDER";
                        headerTd.style.fontWeight = "bold";
                        headerTd.style.paddingBottom = "0"; // remove spacing
                        headerTd.style.border= "1px solid #ddd"; // seamless join
                        headerTd.style.borderBottom = "none"; // seamless join
                        headerTd2.style.border= "1px solid #ddd"; // seamless join
                        headerTd2.style.borderBottom = "none"; // seamless join
                        headerTd3.style.border= "1px solid #ddd"; // seamless join
                        headerTd3.style.borderBottom = "none"; // seamless join
                        headerTd4.style.border= "1px solid #ddd"; // seamless join
                        headerTd4.style.borderBottom = "none"; // seamless join
                        headerTd5.style.border= "1px solid #ddd"; // seamless join
                        headerTd5.style.borderBottom = "none"; // seamless join
                        headerRow.appendChild(headerTd);
                        headerRow.appendChild(headerTd2);
                        headerRow.appendChild(headerTd3);
                        headerRow.appendChild(headerTd4);
                        headerRow.appendChild(headerTd5);
                        tbody.appendChild(headerRow);

                        // Sub-statuses under NEW ORDER
                        ["new", "confirmation acknowledgement"].forEach((subStatus, index) => {
                            const statusItem = temp.find(item => item.status.toLowerCase() === subStatus);
                            if (!statusItem) return;

                            const apiItem = apiMap.get(subStatus) || {};
                            const priceItem = pricingMap.get(subStatus) || {};

                            const row = document.createElement("tr");
                            row.setAttribute("data-status", subStatus);
                            row.style.borderTop = "none"; // remove top border to join with previous
                            if (index === 0) row.style.paddingTop = "0"; // remove top padding on first sub-row

                            const tdStatus = document.createElement("td");
                            tdStatus.textContent =
                                subStatus === "new" ? "→ ORDER VERIFICATION" :
                                subStatus === "confirmation acknowledgement" ? "→ CONFIRMATION / ACKNOWLEDGEMENT" :
                                subStatus.toUpperCase();

                            tdStatus.style.borderTop = "none"; // remove cell border
                            row.appendChild(tdStatus);

                            ['sms', 'email', 'whatsapp', 'ivr'].forEach(channel => {
                                const td = createChannelCell(subStatus, channel, apiItem, priceItem);
                                td.style.borderTop = "none";
                                td.style.borderRight = "1px solid #ddd";
                                td.style.borderLeft = "1px solid #ddd";
                                row.appendChild(td);
                            });

                            tbody.appendChild(row);
                        });


                        // Now add the rest of the statuses (excluding new + confirmation acknowledgement)
                        temp.forEach(statusItem => {
                            const status = statusItem.status.toLowerCase();
                            if (["new", "confirmation acknowledgement"].includes(status)) return;

                            const apiItem = apiMap.get(status) || {};
                            const priceItem = pricingMap.get(status) || {};

                            const row = document.createElement("tr");
                            row.setAttribute("data-status", status);

                            const tdStatus = document.createElement("td");
                            tdStatus.style.border = "1px solid #ddd";

                            tdStatus.textContent =
                                status === "pending pickup" ? "PENDING PICKUP" :
                                status === "in transit" ? "SHIPPED / IN TRANSIT" :
                                status === "out for delivery" ? "OUT FOR DELIVERY" :
                                status === "delivered" ? "DELIVERED" :
                                status === "exception" ? "NDR" :
                                status === "rto" ? "RTO" :
                                status.toUpperCase();
                            tdStatus.style.fontWeight = "bold";
                            row.appendChild(tdStatus);

                            ['sms', 'email', 'whatsapp', 'ivr'].forEach(channel => {
                                const td = createChannelCell(status, channel, apiItem, priceItem);
                                td.style.border = "1px solid #ddd";
                                row.appendChild(td);
                            });

                            tbody.appendChild(row);
                        });
                    })
                    .catch(err => {
                        alert("Failed to fetch current settings. Please try again later.");
                    })
                    .finally(()=>{
                        hideLoader();
                    })
                })
            }
        })
        .catch(err=>{
            alert(`Error while fetching settings`)
        })
        .finally(()=>{
            hideLoader();
        })

        // Handle toggle change
        document.addEventListener('change', function (e) {
            if (!e.target.classList.contains('cstm-switch-input')) return;

            const input = e.target;
            const prevValue = !input.checked; // This is the *previous* value
            const row = input.closest("tr");
            const status = input.dataset.status;

            const getChannelValue = (channel) => (
                row.querySelector(`[data-channel="${channel}"]`)?.checked ? 'yes' : 'no'
            );

            const sendSettings = (payload) => {
                    showLoader();
                    fetch(`${baseUrl}index.php/api/CommunicationSettings/saveSettings`, {
                        method: 'POST',
                        headers: {
                            Authorization: `Bearer ${token}`,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(res => {
                        if (!res.ok) throw new Error(`HTTP error ${res.status}`);
                        return res.json();
                    })
                    .finally(()=>{
                        hideLoader();
                    })
            };

            const handleError = (message) => {
                alert("Update failed: " + message);
                input.checked = prevValue; // revert the toggle
                updateUI(input);           // restore UI
            };

            const updateUI = (input) => {
                const track = input.nextElementSibling;
                const knob = track?.nextElementSibling;
                if (input.checked) {
                    track.style.backgroundColor = "#4CAF50";
                    knob.style.transform = "translateX(20px)";
                } else {
                    track.style.backgroundColor = "#ccc";
                    knob.style.transform = "translateX(0)";
                }
            };

            updateUI(input); // Apply new UI (optimistic update)

            if (status === "new") {
                const payload = {
                    status: "new",
                    whatsapp: getChannelValue("whatsapp"),
                };

                sendSettings(payload)
                    .then(resp => {
                        if (!resp.status) return handleError(resp.message);

                        const ackPayload = {
                            status: "confirmation acknowledgement",
                            whatsapp: getChannelValue("whatsapp"),
                        };

                        return sendSettings(ackPayload);
                    })
                    .then(resp => {
                        if (resp && !resp.status) handleError(resp.message);
                    })
                    .catch(err => {
                        handleError("Please try again.");
                    });

            } else {
                const payload = {
                    status,
                    sms: getChannelValue("sms"),
                    email: getChannelValue("email"),
                    whatsapp: getChannelValue("whatsapp"),
                    ivr: getChannelValue("ivr")
                };

                sendSettings(payload)
                    .then(resp => {
                        if (!resp.status) handleError(resp.message);
                    })
                    .catch(err => {
                        handleError("Please try again.");
                    });
            }
        });

        function toggleBrandingModal(show = null) {
            const modal = document.getElementById("branding-modal");
            if (show === null) {
                modal.classList.toggle("d-none");
            } else {
                modal.classList.toggle("d-none", !show);
            }
        }
        window.toggleBrandingModal = toggleBrandingModal;

        document.getElementById("open-branding").addEventListener("click", function () {
            showLoader();
            toggleBrandingModal(true);
            fetch(`${baseUrl}index.php/api/CommunicationSettings/getbrandname`, {
                method: "POST",
                headers: {
                    Authorization: `Bearer ${token}`
                }
            })
            .then(res => {
                if (!res.ok) throw new Error(`HTTP error ${res.status}`);
                return res.json();
            })
            .then(data => {
                const value = data.brand_name;
                const input = document.getElementById("manual-branding-input");
                const radioCompany = document.getElementById("branding-company");
                const radioManual = document.getElementById("branding-manual");

                if (!value || value.trim() === "") {
                    radioCompany.checked = true;
                    input.classList.add("d-none");
                    input.value = "";
                } else {
                    radioManual.checked = true;
                    input.classList.remove("d-none");
                    input.value = value.trim();
                }

                toggleBrandingModal(true);
            })
            .catch(err => {
                alert("Unable to fetch branding preference.");
            })
            .finally(()=>{
                hideLoader();
            })
        });

        // Hide manual input based on selection
        document.querySelectorAll('input[name="branding"]').forEach((radio) => {
            radio.addEventListener("change", () => {
                const input = document.getElementById("manual-branding-input");
                input.classList.toggle("d-none", !document.getElementById("branding-manual").checked);
            });
        });

        // Handle form submit
        document.getElementById("branding-form").addEventListener("submit", function (e) {
            e.preventDefault();
            let value = document.querySelector('input[name="branding"]:checked').value;
            if (value === "manual") {
                value = document.getElementById("manual-branding-input").value.trim();
                if (!value) {
                    alert("Please enter a custom branding name.");
                    return;
                }
            }
            showLoader();
            fetch(`${baseUrl}index.php/api/CommunicationSettings/savebrandname`, {
                method: "POST",
                headers: {
                    Authorization: `Bearer ${token}`,
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ brand_name: value })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    toggleBrandingModal(false);
                } else {
                    alert("Failed to update branding: " + data.message);
                }
            })
            .catch(err => {
                alert("Something went wrong while saving the branding name.");
            })
            .finally(()=>{
                hideLoader();
            })
        });

        // Close modal when clicking outside the modal content
        document.getElementById("branding-modal").addEventListener("click", function (e) {
            const content = document.getElementById("branding-modal-content");
            if (!content.contains(e.target)) {
                toggleBrandingModal(false);
            }
        });

        // Toggle styling for bundle switches
        function updateBundleToggleUI() {
            document.querySelectorAll('.cstm-switch-bundle').forEach(input => {
                const track = input.nextElementSibling;
                const knob = track?.nextElementSibling;
                if (!track || !knob) return;

                if(input.id == 'bundle-ivr'){
                    track.style.backgroundColor = "#f8d7da";
                }
                else if (input.checked) {
                    track.style.backgroundColor = "#4CAF50";
                    knob.style.transform = "translateX(20px)";
                } 
                else {
                    track.style.backgroundColor = "#ccc";
                    knob.style.transform = "translateX(0)";
                }
            });
        }


        function toggleBundle(payload) {
            showLoader();
            fetch(`${baseUrl}index.php/api/CommunicationSettings/set_communication_plan`, {
                method: "POST",
                headers: {
                    Authorization: `Bearer ${token}`,
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(resp => {
                if (resp.status) {
                    location.reload();
                } else {
                    alert("Failed to update: " + resp.message);
                }
            })
            .catch(err => {
                alert("Something went wrong while saving the bundle settings.");
            })
            .finally(()=>{
                hideLoader();
            })
        }


        // Listen for changes on bundle toggles
        document.addEventListener('change', function (e) {
            if (e.target.classList.contains('cstm-switch-bundle')) {
                updateBundleToggleUI();
                const payload = {
                    communication_plan: "bundled",
                    communication_specific1: document.getElementById('bundle-sms').checked ? 'sms' : null,
                    communication_specific2: document.getElementById('bundle-email').checked ? 'email' : null,
                    communication_specific3: document.getElementById('bundle-whatsapp').checked ? 'whatsapp' : null,
                    communication_specific4: document.getElementById('bundle-ivr').checked ? 'ivr' : null
                };

                toggleBundle(payload);
            }
        });
        
        document.addEventListener("click", function(e) {
            if (e.target && e.target.id === "open-bundle") {
                //toggleBundleModal(true);
                const payload = {
                    communication_plan: "bundled",
                    communication_specific1: null,
                    communication_specific2: null,
                    communication_specific3: null,
                    communication_specific4: null,
                };
                showLoader();
                fetch(`${baseUrl}index.php/api/CommunicationSettings/set_communication_plan`, {
                    method: "POST",
                    headers: {
                        Authorization: `Bearer ${token}`,
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(payload)
                })
                .then(res => res.json())
                .then(resp => {
                    if (resp.status) {
                        location.reload();
                    } else {
                        alert("Failed to update: " + resp.message);
                    }
                })
                .catch(err => {
                    alert("Something went wrong while saving the bundle settings.");
                })
                .finally(()=>{
                    hideLoader();
                })
            }
        });

        document.addEventListener('click', function (e) {
            if (e.target && e.target.id === "open-individual") {
                const payload = {
                    communication_plan: "individual"
                };
                showLoader();
                fetch(`${baseUrl}index.php/api/CommunicationSettings/set_communication_plan`, {
                    method: "POST",
                    headers: {
                        Authorization: `Bearer ${token}`,
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(payload)
                })
                .then(res => res.json())
                .then(resp => {
                    if (resp.status) {
                        location.reload(); // Re-fetch page data
                    } else {
                        alert("Failed to update: " + resp.message);
                    }
                })
                .catch(err => {
                    alert("Something went wrong while switching the plan.");
                })
                .finally(()=>{
                    hideLoader();
                })
            }
        });


    
    });
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

    .bundle-info-box {
        padding: 18px;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        background: linear-gradient(145deg, #ffffff, #f5f7fa);
        max-width: 420px;
        font-family: 'Segoe UI', Arial, sans-serif;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .bundle-info-box h5 {
        margin-bottom: 15px;
        font-size: 20px;
        font-weight: 600;
        color: #2c3e50;
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 5px;
    }

    .form-group {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
        padding: 8px 0;
        border-bottom: 1px dashed #ddd;
    }

    .form-group:last-child {
        border-bottom: none;
    }

    .form-group label:first-child {
        flex: 1;
        font-weight: 500;
        color: #34495e;
        display: flex;
        align-items: center;
    }

    .price-label {
        margin-left: auto;
        font-weight: bold;
        color: #2d6a4f;
        min-width: 70px;
        text-align: right;
        font-size: 14px;
    }

    /* Toggle Switch */
    .toggle-container {
        position: relative;
        display: inline-block;
        width: 46px;
        height: 24px;
        margin: 0 10px;
    }

    .toggle-container input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #dcdcdc;
        transition: .3s;
        border-radius: 34px;
        box-shadow: inset 0 1px 3px rgba(0,0,0,0.2);
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    input:checked + .slider {
        background-color: #4CAF50;
    }

    input:checked + .slider:before {
        transform: translateX(22px);
    }

    input:disabled + .slider {
        background-color: #ffe0e0;
        cursor: not-allowed;
    }

    input:disabled + .slider:before {
        background-color: #f1f1f1;
    }

    /* Info icon popup */
    .info-icon {
        position: relative;
        display: inline-block;
        font-size: 14px;
        margin-left: 6px;
        cursor: pointer;
        color: #3498db;
        width: 18px;
        height: 18px;
        text-align: center;
        line-height: 16px;
        font-weight: bold;
    }

    .info-popup ul {
        margin: 0;
        padding-left: 18px; /* space for bullets */
        list-style-type: disc;
    }

    .info-popup li {
        margin-bottom: 1px;
        font-size: 12px;
        color: #333;
    }


    .info-popup {
        display: none;
        text-align: left;
        position: absolute;
        top: -10px;
        left: 25px;
        background: #ffffff;
        color: #333;
        padding: 10px 12px;
        border-radius: 8px;
        box-shadow: 0px 3px 12px rgba(0,0,0,0.15);
        z-index: 1000;
        white-space: nowrap;
        font-size: 12px;
        animation: fadeIn 0.2s ease-in-out;
    }

    .info-icon:hover .info-popup {
        display: block;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-3px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .icon {
        cursor: pointer;
    }

    .centered-image {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 9999;
        background: white;
        padding: 10px;
        border: 2px solid #444;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    }

    .centered-image img {
        max-width: 90vw;
        max-height: 80vh;
    }

    .close-btn {
        position: absolute;
        top: 5px;
        right: 10px;
        cursor: pointer;
        font-weight: bold;
        font-size: 35px;
    }

    .branding-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .branding-modal-content {
        background-color: white;
        padding: 20px;
        width: 300px;
        border-radius: 8px;
        position: relative;
    }

    .branding-close-btn {
        position: absolute;
        top: 8px;
        right: 12px;
        cursor: pointer;
        font-size: 18px;
        font-weight: bold;
    }

    


</style>
