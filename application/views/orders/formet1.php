<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        background-color: #f5f7fa;
        color: #333;
        line-height: 1.6;
        padding: 10px;
    }

    .container {
        max-width: 1400px;
        margin: 0 auto;
        background: white;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        border-radius: 10px;
    }

    header {
        background: linear-gradient(135deg, #564ec1, #564ec1);
        color: white;
        padding: 25px 30px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    h1 {
        font-size: 28px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    h1 i {
        color: #ffcc00;
    }

    .subtitle {
        font-size: 16px;
        opacity: 0.9;
        font-weight: 300;
    }

    .content {
        padding: 25px 30px;
    }

    .controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
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
        padding: 12px 24px;
        cursor: pointer;
        font-weight: 600;
        color: #555;
        transition: all 0.3s ease;
        border-right: 1px solid #ddd;
        display: flex;
        align-items: center;
        gap: 8px;
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
        padding: 12px 24px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }

    .refresh-btn:hover {
        background-color: #3a5a80;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .loading {
        text-align: center;
        padding: 40px;
        font-size: 18px;
        color: #4a6fa5;
        display: none;
    }

    .loading i {
        font-size: 24px;
        margin-bottom: 15px;
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
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    .results-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #eaeaea;
    }

    .results-title {
        font-size: 22px;
        color: #2c3e50;
    }

    .results-count {
        background-color: #eef5ff;
        color: #4a6fa5;
        padding: 6px 15px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 14px;
    }

    .data-table-container {
        overflow-x: auto;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 1000px;
    }

    .data-table thead {
        background: linear-gradient(135deg, #564ec1, #564ec1);
        color: white;
    }

    .data-table th {
        padding: 16px 12px;
        text-align: left;
        font-weight: 600;
        font-size: 14px;
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
        margin-right: 8px;
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

    .data-table tbody tr:nth-child(even):hover {
        background-color: #f0f7ff;
    }

    .data-table td {
        padding: 14px 12px;
        vertical-align: middle;
        border-right: 1px solid #f0f0f0;
    }

    .data-table td:last-child {
        border-right: none;
    }

    .rank-cell {
        text-align: center;
        font-weight: 700;
        font-size: 16px;
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
        padding: 8px 12px !important;
    }

    .tags-container {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .tag {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
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
        padding: 60px 20px;
        color: #7f8c8d;
        font-size: 18px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 2px dashed #dee2e6;
    }

    .no-data i {
        font-size: 48px;
        margin-bottom: 20px;
        color: #bdc3c7;
    }

    footer {
        text-align: center;
        padding: 20px;
        margin-top: 30px;
        color: #7f8c8d;
        font-size: 14px;
        border-top: 1px solid #eaeaea;
    }

    .response-time {
        font-weight: bold;
        color: #4a6fa5;
    }

    .api-success {
        color: #2ecc71;
    }

    .api-error {
        color: #e74c3c;
    }

    .rto-legend {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
        padding: 10px 15px;
        background-color: #f8f9fa;
        border-radius: 6px;
        border: 1px solid #eaeaea;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 13px;
        font-weight: 500;
    }

    .legend-color {
        width: 15px;
        height: 15px;
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

    @media (max-width: 768px) {
        .container {
            border-radius: 0;
        }

        .content {
            padding: 15px;
        }

        .controls {
            flex-direction: column;
            align-items: flex-start;
        }

        .tab-container {
            width: 100%;
            justify-content: center;
        }

        .tab {
            padding: 10px 15px;
            font-size: 14px;
            flex: 1;
            justify-content: center;
        }

        .refresh-btn {
            width: 100%;
            justify-content: center;
        }

        .data-table-container {
            border-radius: 6px;
            margin: 0 -15px;
        }

        .data-table th,
        .data-table td {
            padding: 10px 8px;
            font-size: 13px;
        }

        .tag {
            padding: 3px 8px;
            font-size: 10px;
        }

        .rto-legend {
            flex-wrap: wrap;
            justify-content: center;
        }
    }

    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        gap: 10px;
        z-index: 1000;
        animation: slideIn 0.3s ease;
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
</style>

<div class="container">
    <header>
        <h1><i class="fas fa-shipping-fast"></i> Courier Recommendation System</h1>
        <p class="subtitle">Get optimized courier suggestions based on your order details</p>
    </header>

    <div class="content">
        <div class="controls">
            <div class="tab-container">
                <div class="tab active" data-tab="all">
                    <i class="fas fa-list"></i> All Couriers
                </div>
                <div class="tab" data-tab="air">
                    <i class="fas fa-plane"></i> Air Couriers
                </div>
                <div class="tab" data-tab="surface">
                    <i class="fas fa-truck"></i> Surface Couriers
                </div>
            </div>

            <button class="refresh-btn" id="fetchData">
                <i class="fas fa-sync-alt"></i> Fetch Recommendations
            </button>
        </div>

        <div class="rto-legend">
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

        <div class="loading" id="loadingIndicator">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Fetching courier recommendations from API...</p>
        </div>

        <div class="results-container active" id="allResults">
            <div class="results-header">
                <h2 class="results-title"><i class="fas fa-list"></i> All Courier Recommendations</h2>
                <div class="results-count" id="allCount">0 couriers</div>
            </div>

            <div class="data-table-container">
                <table class="data-table" id="allTable">
                    <thead>
                        <tr>
                            <th data-key="Rank"><i class="fas fa-hashtag"></i> Rank</th>
                            <th data-key="Courier Name"><i class="fas fa-shipping-fast"></i> Courier Name</th>
                            <th data-key="Courier Id"><i class="fas fa-id-badge"></i> ID</th>
                            <th data-key="Estimated Cost"><i class="fas fa-rupee-sign"></i> Estimated Cost</th>
                            <th data-key="Estimated Delivery Days"><i class="fas fa-calendar-day"></i> Delivery Days</th>
                            <th data-key="RTO Risk"><i class="fas fa-exclamation-triangle"></i> RTO Risk</th>
                            <th data-key="Confidence Score"><i class="fas fa-chart-line"></i> Score</th>
                            <th><i class="fas fa-tags"></i> Tags</th>
                        </tr>
                    </thead>
                    <tbody id="allTableBody">
                        <tr>
                            <td colspan="8">
                                <div class="no-data">
                                    <i class="fas fa-cloud-download-alt"></i>
                                    <p>Click "Fetch Recommendations" to load data</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="results-container" id="airResults">
            <div class="results-header">
                <h2 class="results-title"><i class="fas fa-plane"></i> Air Courier Recommendations</h2>
                <div class="results-count" id="airCount">0 couriers</div>
            </div>

            <div class="data-table-container">
                <table class="data-table" id="airTable">
                    <thead>
                        <tr>
                            <th data-key="Rank"><i class="fas fa-hashtag"></i> Rank</th>
                            <th data-key="Courier Name"><i class="fas fa-shipping-fast"></i> Courier Name</th>
                            <th data-key="Courier Id"><i class="fas fa-id-badge"></i> ID</th>
                            <th data-key="Estimated Cost"><i class="fas fa-rupee-sign"></i> Estimated Cost</th>
                            <th data-key="Estimated Delivery Days"><i class="fas fa-calendar-day"></i> Delivery Days</th>
                            <th data-key="RTO Risk"><i class="fas fa-exclamation-triangle"></i> RTO Risk</th>
                            <th data-key="Confidence Score"><i class="fas fa-chart-line"></i> Confidence</th>
                            <th><i class="fas fa-tags"></i> Tags</th>
                        </tr>
                    </thead>
                    <tbody id="airTableBody">
                        <tr>
                            <td colspan="8">
                                <div class="no-data">
                                    <i class="fas fa-cloud-download-alt"></i>
                                    <p>Click "Fetch Recommendations" to load data</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="results-container" id="surfaceResults">
            <div class="results-header">
                <h2 class="results-title"><i class="fas fa-truck"></i> Surface Courier Recommendations</h2>
                <div class="results-count" id="surfaceCount">0 couriers</div>
            </div>

            <div class="data-table-container">
                <table class="data-table" id="surfaceTable">
                    <thead>
                        <tr>
                            <th data-key="Rank"><i class="fas fa-hashtag"></i> Rank</th>
                            <th data-key="Courier Name"><i class="fas fa-shipping-fast"></i> Courier Name</th>
                            <th data-key="Courier Id"><i class="fas fa-id-badge"></i> ID</th>
                            <th data-key="Estimated Cost"><i class="fas fa-rupee-sign"></i> Estimated Cost</th>
                            <th data-key="Estimated Delivery Days"><i class="fas fa-calendar-day"></i> Delivery Days</th>
                            <th data-key="RTO Risk"><i class="fas fa-exclamation-triangle"></i> RTO Risk</th>
                            <th data-key="Confidence Score"><i class="fas fa-chart-line"></i> Confidence</th>
                            <th><i class="fas fa-tags"></i> Tags</th>
                        </tr>
                    </thead>
                    <tbody id="surfaceTableBody">
                        <tr>
                            <td colspan="8">
                                <div class="no-data">
                                    <i class="fas fa-cloud-download-alt"></i>
                                    <p>Click "Fetch Recommendations" to load data</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <footer>
        <p>Courier Recommendation System &copy; 2023 | API Response Time: <span id="responseTime" class="response-time">-</span> ms</p>
    </footer>
</div>
<script>
let currentSort = {
    key: null,
    order: 'asc'
};

let apiData = {
    all: [],
    air: [],
    surface: []
};

// DOM Elements
const tabs = document.querySelectorAll('.tab');
const resultsContainers = document.querySelectorAll('.results-container');
const fetchButton = document.getElementById('fetchData');
const loadingIndicator = document.getElementById('loadingIndicator');

const allTableBody = document.getElementById('allTableBody');
const airTableBody = document.getElementById('airTableBody');
const surfaceTableBody = document.getElementById('surfaceTableBody');

const allCountElement = document.getElementById('allCount');
const airCountElement = document.getElementById('airCount');
const surfaceCountElement = document.getElementById('surfaceCount');
const responseTimeElement = document.getElementById('responseTime');

// API CONFIG
const API_URL = 'https://pulse.daakit.com/recommend_courier';
const API_KEY = '67e8a932d4b1c8f5a2e0d3c7b4a1f9e8d2c6b5a4f3e2d1c0b9a8f7e6d5c4b3a2';


// ================= TAB SYSTEM =================
tabs.forEach(tab => {
    tab.addEventListener('click', () => {

        const tabId = tab.dataset.tab;

        tabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');

        resultsContainers.forEach(container =>
            container.classList.remove('active')
        );

        document
            .getElementById(`${tabId}Results`)
            .classList.add('active');
    });
});

function restoreActiveTab() {
    const activeTab = document.querySelector('.tab.active');
    if (!activeTab) return;

    const tabId = activeTab.dataset.tab;

    resultsContainers.forEach(c =>
        c.classList.remove('active')
    );

    document
        .getElementById(`${tabId}Results`)
        .classList.add('active');
}


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
    if (!list?.length) return {min:0,max:0};

    const values = list.map(c => parseRtoRisk(c['RTO Risk']));
    return {
        min: Math.min(...values),
        max: Math.max(...values)
    };
}


// ================= TABLE ROW =================
function createTableRow(courier, rtoAnalysis) {

    const tags = courier.Tags ? courier.Tags.split(', ') : [];

    const tagHTML = tags.map(tag=>{
        let cls='tag';
        if(tag.toLowerCase().includes('recommended')) cls+=' recommended';
        else if(tag.toLowerCase().includes('cheapest')) cls+=' cheapest';
        else if(tag.toLowerCase().includes('fastest')) cls+=' fastest';
        else if(tag.toLowerCase().includes('least rto')) cls+=' least-rto';
        else if(tag.toLowerCase().includes('balanced')) cls+=' balanced';

        return `<span class="${cls}">${tag}</span>`;
    }).join('');

    const rtoValue=parseRtoRisk(courier['RTO Risk']);
    const rtoClass=categorizeRtoRisk(
        rtoValue,
        rtoAnalysis.min,
        rtoAnalysis.max
    );

    return `
    <tr>
        <td class="rank-cell">${courier.Rank}</td>
        <td class="courier-name-cell">${courier['Courier Name']}</td>
        <td class="id-cell">${courier['Courier Id']}</td>
        <td class="cost-cell">₹${courier['Estimated Cost']}</td>
        <td class="days-cell">${courier['Estimated Delivery Days']} days</td>
        <td class="rto-cell ${rtoClass}">${courier['RTO Risk']}</td>
        <td class="confidence-cell">${courier['Confidence Score']}</td>
        <td class="tags-cell"><div class="tags-container">${tagHTML}</div></td>
    </tr>`;
}


// ================= RENDER =================
function renderCourierData(data){

    apiData=data;

    allTableBody.innerHTML='';
    airTableBody.innerHTML='';
    surfaceTableBody.innerHTML='';

    const allR=analyzeRtoRisks(data.all);
    const airR=analyzeRtoRisks(data.air);
    const surfR=analyzeRtoRisks(data.surface);

    data.all?.forEach(c=>allTableBody.innerHTML+=createTableRow(c,allR));
    data.air?.forEach(c=>airTableBody.innerHTML+=createTableRow(c,airR));
    data.surface?.forEach(c=>surfaceTableBody.innerHTML+=createTableRow(c,surfR));

    allCountElement.textContent=`${data.all?.length||0} couriers`;
    airCountElement.textContent=`${data.air?.length||0} couriers`;
    surfaceCountElement.textContent=`${data.surface?.length||0} couriers`;

    restoreActiveTab();
}


// ================= NOTIFICATION =================
function showNotification(msg,type){

    const div=document.createElement('div');
    div.className=`notification ${type}`;
    div.innerHTML=`
        <i class="fas ${type==='success'?'fa-check-circle':'fa-exclamation-circle'}"></i>
        ${msg}
    `;

    document.body.appendChild(div);

    setTimeout(()=>{
        div.style.animation='slideOut .3s ease';
        setTimeout(()=>div.remove(),300);
    },4000);
}


// ================= API CALL =================
async function fetchDataFromAPI(){

    const start=Date.now();
    loadingIndicator.style.display='block';

    try{

        const payload={
            seller_id:20,
            order_amount:800,
            weight_slab:5,
            zone:2,
            is_cod:0,
            total_products:1,
            order_date:"2026-01-27",
            pickup_warehouse_zip:110024,
            shipping_district:"jaipur",
            shipping_state:"rajasthan"
        };

        const res=await fetch(API_URL,{
            method:'POST',
            headers:{
                'Content-Type':'application/json',
                'x-api-key':API_KEY
            },
            body:JSON.stringify(payload)
        });

        if(!res.ok) throw new Error(`Status ${res.status}`);

        const data=await res.json();

        loadingIndicator.style.display='none';

        renderCourierData(data);

        const time=Date.now()-start;
        responseTimeElement.textContent=time;
        responseTimeElement.className='response-time api-success';

        showNotification('Courier recommendations loaded','success');

    }catch(err){

        loadingIndicator.style.display='none';

        responseTimeElement.textContent='Error';
        responseTimeElement.className='response-time api-error';

        showNotification('API Failed','error');

        console.error(err);
    }
}


// ================= EVENTS =================
fetchButton.addEventListener('click',fetchDataFromAPI);

document.addEventListener('DOMContentLoaded',()=>{
    fetchDataFromAPI();
});
</script> -->

<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        background-color: #f5f7fa;
        color: #333;
        line-height: 1.6;
        padding: 10px;
    }

    .container {
        max-width: 1400px;
        margin: 0 auto;
        background: white;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        border-radius: 10px;
    }

    header {
        background: linear-gradient(135deg, #564ec1, #564ec1);
        color: white;
        padding: 25px 30px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    h1 {
        font-size: 28px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    h1 i {
        color: #ffcc00;
    }

    .subtitle {
        font-size: 16px;
        opacity: 0.9;
        font-weight: 300;
    }

    .content {
        padding: 25px 30px;
    }

    .controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
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
        padding: 12px 24px;
        cursor: pointer;
        font-weight: 600;
        color: #555;
        transition: all 0.3s ease;
        border-right: 1px solid #ddd;
        display: flex;
        align-items: center;
        gap: 8px;
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
        padding: 12px 24px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }

    .refresh-btn:hover {
        background-color: #3a5a80;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .loading {
        text-align: center;
        padding: 40px;
        font-size: 18px;
        color: #4a6fa5;
        display: none;
    }

    .loading i {
        font-size: 24px;
        margin-bottom: 15px;
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
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    .results-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #eaeaea;
    }

    .results-title {
        font-size: 22px;
        color: #2c3e50;
    }

    .results-count {
        background-color: #eef5ff;
        color: #4a6fa5;
        padding: 6px 15px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 14px;
    }

    .data-table-container {
        overflow-x: auto;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 1000px;
    }

    .data-table thead {
        background: linear-gradient(135deg, #564ec1, #564ec1);
        color: white;
    }

    .data-table th {
        padding: 16px 12px;
        text-align: left;
        font-weight: 600;
        font-size: 14px;
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
        margin-right: 8px;
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

    .data-table tbody tr:nth-child(even):hover {
        background-color: #f0f7ff;
    }

    .data-table td {
        padding: 14px 12px;
        vertical-align: middle;
        border-right: 1px solid #f0f0f0;
    }

    .data-table td:last-child {
        border-right: none;
    }

    .rank-cell {
        text-align: center;
        font-weight: 700;
        font-size: 16px;
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
        padding: 8px 12px !important;
    }

    .tags-container {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .tag {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
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
        padding: 60px 20px;
        color: #7f8c8d;
        font-size: 18px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 2px dashed #dee2e6;
    }

    .no-data i {
        font-size: 48px;
        margin-bottom: 20px;
        color: #bdc3c7;
    }

    footer {
        text-align: center;
        padding: 20px;
        margin-top: 30px;
        color: #7f8c8d;
        font-size: 14px;
        border-top: 1px solid #eaeaea;
    }

    .response-time {
        font-weight: bold;
        color: #4a6fa5;
    }

    .api-success {
        color: #2ecc71;
    }

    .api-error {
        color: #e74c3c;
    }

    .rto-legend {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
        padding: 10px 15px;
        background-color: #f8f9fa;
        border-radius: 6px;
        border: 1px solid #eaeaea;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 13px;
        font-weight: 500;
    }

    .legend-color {
        width: 15px;
        height: 15px;
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

    @media (max-width: 768px) {
        .container {
            border-radius: 0;
        }

        .content {
            padding: 15px;
        }

        .controls {
            flex-direction: column;
            align-items: flex-start;
        }

        .tab-container {
            width: 100%;
            justify-content: center;
        }

        .tab {
            padding: 10px 15px;
            font-size: 14px;
            flex: 1;
            justify-content: center;
        }

        .refresh-btn {
            width: 100%;
            justify-content: center;
        }

        .data-table-container {
            border-radius: 6px;
            margin: 0 -15px;
        }

        .data-table th,
        .data-table td {
            padding: 10px 8px;
            font-size: 13px;
        }

        .tag {
            padding: 3px 8px;
            font-size: 10px;
        }

        .rto-legend {
            flex-wrap: wrap;
            justify-content: center;
        }
    }

    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        gap: 10px;
        z-index: 1000;
        animation: slideIn 0.3s ease;
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
</style>

<div class="container">
    <header>
        <h1><i class="fas fa-shipping-fast"></i> Courier Recommendation System</h1>
        <p class="subtitle">Get optimized courier suggestions based on your order details</p>
    </header>

    <div class="content">
        <div class="controls">
            <div class="tab-container">
                <div class="tab active" data-tab="all">
                    <i class="fas fa-list"></i> All Couriers
                </div>
                <div class="tab" data-tab="air">
                    <i class="fas fa-plane"></i> Air Couriers
                </div>
                <div class="tab" data-tab="surface">
                    <i class="fas fa-truck"></i> Surface Couriers
                </div>
            </div>

            <button class="refresh-btn" id="fetchData">
                <i class="fas fa-sync-alt"></i> Fetch Recommendations
            </button>
        </div>

        <div class="rto-legend">
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

        <div class="loading" id="loadingIndicator">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Fetching courier recommendations from API...</p>
        </div>

        <div class="results-container active" id="allResults">
            <div class="results-header">
                <h2 class="results-title"><i class="fas fa-list"></i> All Courier Recommendations</h2>
                <div class="results-count" id="allCount">0 couriers</div>
            </div>

            <div class="data-table-container">
                <table class="data-table" id="allTable">
                    <thead>
                        <tr>
                            <th data-key="Rank"><i class="fas fa-hashtag"></i> Rank</th>
                            <th data-key="Courier Name"><i class="fas fa-shipping-fast"></i> Courier Name</th>
                            <th data-key="Courier Id"><i class="fas fa-id-badge"></i> ID</th>
                            <th data-key="Estimated Cost"><i class="fas fa-rupee-sign"></i> Estimated Cost</th>
                            <th data-key="Estimated Delivery Days"><i class="fas fa-calendar-day"></i> Delivery Days</th>
                            <th data-key="RTO Risk"><i class="fas fa-exclamation-triangle"></i> RTO Risk</th>
                            <th data-key="Confidence Score"><i class="fas fa-chart-line"></i> Score</th>
                            <th><i class="fas fa-tags"></i> Tags</th>
                        </tr>
                    </thead>
                    <tbody id="allTableBody">
                        <tr>
                            <td colspan="8">
                                <div class="no-data">
                                    <i class="fas fa-cloud-download-alt"></i>
                                    <p>Click "Fetch Recommendations" to load data</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="results-container" id="airResults">
            <div class="results-header">
                <h2 class="results-title"><i class="fas fa-plane"></i> Air Courier Recommendations</h2>
                <div class="results-count" id="airCount">0 couriers</div>
            </div>

            <div class="data-table-container">
                <table class="data-table" id="airTable">
                    <thead>
                        <tr>
                            <th data-key="Rank"><i class="fas fa-hashtag"></i> Rank</th>
                            <th data-key="Courier Name"><i class="fas fa-shipping-fast"></i> Courier Name</th>
                            <th data-key="Courier Id"><i class="fas fa-id-badge"></i> ID</th>
                            <th data-key="Estimated Cost"><i class="fas fa-rupee-sign"></i> Estimated Cost</th>
                            <th data-key="Estimated Delivery Days"><i class="fas fa-calendar-day"></i> Delivery Days</th>
                            <th data-key="RTO Risk"><i class="fas fa-exclamation-triangle"></i> RTO Risk</th>
                            <th data-key="Confidence Score"><i class="fas fa-chart-line"></i> Confidence</th>
                            <th><i class="fas fa-tags"></i> Tags</th>
                        </tr>
                    </thead>
                    <tbody id="airTableBody">
                        <tr>
                            <td colspan="8">
                                <div class="no-data">
                                    <i class="fas fa-cloud-download-alt"></i>
                                    <p>Click "Fetch Recommendations" to load data</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="results-container" id="surfaceResults">
            <div class="results-header">
                <h2 class="results-title"><i class="fas fa-truck"></i> Surface Courier Recommendations</h2>
                <div class="results-count" id="surfaceCount">0 couriers</div>
            </div>

            <div class="data-table-container">
                <table class="data-table" id="surfaceTable">
                    <thead>
                        <tr>
                            <th data-key="Rank"><i class="fas fa-hashtag"></i> Rank</th>
                            <th data-key="Courier Name"><i class="fas fa-shipping-fast"></i> Courier Name</th>
                            <th data-key="Courier Id"><i class="fas fa-id-badge"></i> ID</th>
                            <th data-key="Estimated Cost"><i class="fas fa-rupee-sign"></i> Estimated Cost</th>
                            <th data-key="Estimated Delivery Days"><i class="fas fa-calendar-day"></i> Delivery Days</th>
                            <th data-key="RTO Risk"><i class="fas fa-exclamation-triangle"></i> RTO Risk</th>
                            <th data-key="Confidence Score"><i class="fas fa-chart-line"></i> Confidence</th>
                            <th><i class="fas fa-tags"></i> Tags</th>
                        </tr>
                    </thead>
                    <tbody id="surfaceTableBody">
                        <tr>
                            <td colspan="8">
                                <div class="no-data">
                                    <i class="fas fa-cloud-download-alt"></i>
                                    <p>Click "Fetch Recommendations" to load data</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <footer>
        <p>Courier Recommendation System &copy; 2023 | API Response Time: <span id="responseTime" class="response-time">-</span> ms</p>
    </footer>
</div> -->

<style>
/* Original styles from your shipping modal */
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
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.recommendation-container {
  max-width: 1400px;
  margin: 20px 0;
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
  min-width: 900px;
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
</style>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="modal-content data">
    <div class="modal-header">
        <h5 class="modal-title">Start Shipping Your Package Today</h5>
        <button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"></button>
    </div>
    <div class="modal-body">
        <form class="ship_form" method="post">
            <div class="row">
                <input type="hidden" name="order_id" value="<?= !empty($order->id) ? $order->id : ''; ?>">
                <input type="hidden" id="courier_id" name="courier_id" class="form-check-input">
                
                <!-- Left Column - Shipment Info -->
                <div class="col-md-4">
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
                <div class="col-md-8">
                    <div class="task-single-related-wrapper">
                        <h6>Related Filters:</h6>
                    </div>
                    
                    <?php if (!empty($couriers)) { ?>
                        <!-- Filter Buttons -->
                        <div class="row">
                            <div class="col-sm-12 col-md-4">
                                <h7>Courier Mode Filter:</h7>
                                <div class="input-group">
                                    <div class="input-group-append" data-toggle="buttons">
                                        <button type="button" class="btn btn-outline-secondary text-dark filter_by shadow-none" style="color:#03A9F4; border:1px solid #03A9F4;">
                                            <i class="mdi mdi-airplane"></i>
                                            <input type="checkbox" name="radio1" value="air" class="filter"> Air
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary text-dark filter_by shadow-none" style="color:#03A9F4; border:1px solid #03A9F4;">
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
                        <br>
                        
                        <?php
                        // Define DAAKit Go IDs
                        $daakitGoIds = [1124, 1125, 1126, 1127, 1128, 1129, 1130, 1131, 1132];
                        
                        // Filter DAAKit Go couriers
                        $daakitGoCouriers = array_filter($couriers, function($c) use ($daakitGoIds) {
                            return in_array($c->id, $daakitGoIds);
                        });
                        
                        $hasGo = !empty($daakitGoCouriers);
                        $colClassOne = $hasGo ? 'col-md-6' : 'col-md-12';
                        ?>
                        
                        <!-- DAAKit Accordions -->
                        <div class="row">
                            <!-- DAAKit One -->
                            <div class="<?= $colClassOne ?>">
                                <div class="accordion mb-3" id="daakitOneAccordion">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingOne">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOneCouriers" aria-expanded="false" aria-controls="collapseOneCouriers">
                                                DAAKit One
                                                <img src="<?php echo base_url(); ?>assets/images/daakit-one-icon.png" alt="Logo" style="height:50px; margin-left:10px;">
                                            </button>
                                        </h2>
                                        <div id="collapseOneCouriers" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#daakitOneAccordion">
                                            <div class="accordion-body" style="overflow: auto; max-height: 300px;">
                                                <?php 
                                                echo $couriers;
                                                $nonGoCouriersDisplayed = false;
                                                foreach ($couriers as $courier) {
                                                      
                                                    if (!in_array($courier->id, $daakitGoIds)) { 
                                                        $nonGoCouriersDisplayed = true;
                                                ?>
                                                        <div class="card couriourval <?= $courier->courier_type ?? ''; ?> <?= $courier->weight ?? ''; ?> <?= (isset($courier->prefered) && $courier->prefered == '1') ? 'prefered' : 'notprefered'; ?>" data-target="<?= $courier->courier_type ?? '-'; ?>">
                                                            <div class="card-status card-status-left bg-primary br-bl-7 br-tl-4"></div>
                                                            <div class="card-header">
                                                                <h3 class="card-title">
                                                                    <span class="custom-control" for="customRadio<?= $courier->id; ?>">
                                                                        <?= $courier->name; ?> 
                                                                        <?php if (!empty($courier->charges)) { ?> 
                                                                            (&#8377;<?= round($courier->charges, 2); ?>) 
                                                                        <?php } ?>
                                                                    </span>
                                                                </h3>
                                                                <div class="card-options">
                                                                    <h3 class="card-title">
                                                                        <button type="submit" onclick="document.getElementById('courier_id').value='<?php echo $courier->id;?>'" class="btn btn-sm btn-primary">Ship Now</button>
                                                                    </h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                <?php 
                                                    }
                                                } 
                                                if (!$nonGoCouriersDisplayed) {
                                                    echo '<p class="text-muted">No couriers available</p>';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                            
                            <!-- DAAKit Go -->
                            <?php if ($hasGo) { ?>
                                <div class="col-md-6">
                                    <div class="accordion mb-3" id="daakitGoAccordion">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingGo">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseGoCouriers" aria-expanded="false" aria-controls="collapseGoCouriers">
                                                    DAAKit Go
                                                    <img src="<?php echo base_url(); ?>assets/images/daakit-go-icon.png" alt="Logo" style="height:50px; margin-left:10px;">
                                                </button>
                                            </h2>
                                            <div id="collapseGoCouriers" class="accordion-collapse collapse" aria-labelledby="headingGo" data-bs-parent="#daakitGoAccordion">
                                                <div style="overflow: auto; max-height: 300px;">
                                                    <?php foreach ($daakitGoCouriers as $courier) { ?>
                                                        <div class="card couriourval <?= $courier->courier_type ?? ''; ?> <?= $courier->weight ?? ''; ?> <?= (isset($courier->prefered) && $courier->prefered == '1') ? 'prefered' : 'notprefered'; ?>" data-target="<?= $courier->courier_type ?? '-'; ?>">
                                                            <div class="card-status card-status-left bg-primary br-bl-7 br-tl-4"></div>
                                                            <div class="card-header">
                                                                <h3 class="card-title">
                                                                    <span class="custom-control" for="customRadio<?= $courier->id; ?>">
                                                                        <?= $courier->name; ?> 
                                                                        <?php if (!empty($courier->charges)) { ?> 
                                                                            (&#8377;<?= round($courier->charges, 2); ?>) 
                                                                        <?php } ?>
                                                                    </span>
                                                                </h3>
                                                                <div class="card-options">
                                                                    <h3 class="card-title">
                                                                        <button type="submit" onclick="document.getElementById('courier_id').value='<?php echo $courier->id;?>'" class="btn btn-sm btn-primary">Ship Now</button>
                                                                    </h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        
                        <!-- Recommendation System Section -->
                        <div class="recommendation-container mt-3">
                            <div class="recommendation-header">
                                <h2><i class="fas fa-robot"></i> AI Courier Recommendations</h2>
                                <p class="recommendation-subtitle">Optimized suggestions based on your order details</p>
                            </div>
                            
                            <div class="recommendation-content">
                                <div class="controls">
                                    <div class="tab-container">
                                        <div class="tab active" data-tab="all">
                                            <i class="fas fa-list"></i> All Couriers
                                        </div>
                                        <div class="tab" data-tab="air">
                                            <i class="fas fa-plane"></i> Air Couriers
                                        </div>
                                        <div class="tab" data-tab="surface">
                                            <i class="fas fa-truck"></i> Surface Couriers
                                        </div>
                                    </div>
                                    
                                    <button class="refresh-btn" id="fetchRecommendations">
                                        <i class="fas fa-sync-alt"></i> Refresh Recommendations
                                    </button>
                                </div>
                                
                                <div class="rto-legend">
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
                                
                                <div class="results-container active" id="allResults">
                                    <div class="results-header">
                                        <h3 class="results-title"><i class="fas fa-list"></i> All Courier Recommendations</h3>
                                        <div class="results-count" id="allCount">0 couriers</div>
                                    </div>
                                    
                                    <div class="data-table-container">
                                        <table class="data-table" id="allTable">
                                            <thead>
                                                <tr>
                                                    <th data-key="Rank"><i class="fas fa-hashtag"></i> Rank</th>
                                                    <th data-key="Courier Name"><i class="fas fa-shipping-fast"></i> Courier Name</th>
                                                    <th data-key="Courier Id"><i class="fas fa-id-badge"></i> ID</th>
                                                    <th data-key="Estimated Cost"><i class="fas fa-rupee-sign"></i> Est. Cost</th>
                                                    <th data-key="Estimated Delivery Days"><i class="fas fa-calendar-day"></i> Days</th>
                                                    <th data-key="RTO Risk"><i class="fas fa-exclamation-triangle"></i> RTO Risk</th>
                                                    <th data-key="Confidence Score"><i class="fas fa-chart-line"></i> Score</th>
                                                    <th><i class="fas fa-tags"></i> Tags</th>
                                                </tr>
                                            </thead>
                                            <tbody id="allTableBody">
                                                <tr>
                                                    <td colspan="8">
                                                        <div class="no-data">
                                                            <i class="fas fa-cloud-download-alt"></i>
                                                            <p>Click "Refresh Recommendations" to load data</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <div class="results-container" id="airResults">
                                    <div class="results-header">
                                        <h3 class="results-title"><i class="fas fa-plane"></i> Air Courier Recommendations</h3>
                                        <div class="results-count" id="airCount">0 couriers</div>
                                    </div>
                                    
                                    <div class="data-table-container">
                                        <table class="data-table" id="airTable">
                                            <thead>
                                                <tr>
                                                    <th data-key="Rank"><i class="fas fa-hashtag"></i> Rank</th>
                                                    <th data-key="Courier Name"><i class="fas fa-shipping-fast"></i> Courier Name</th>
                                                    <th data-key="Courier Id"><i class="fas fa-id-badge"></i> ID</th>
                                                    <th data-key="Estimated Cost"><i class="fas fa-rupee-sign"></i> Est. Cost</th>
                                                    <th data-key="Estimated Delivery Days"><i class="fas fa-calendar-day"></i> Days</th>
                                                    <th data-key="RTO Risk"><i class="fas fa-exclamation-triangle"></i> RTO Risk</th>
                                                    <th data-key="Confidence Score"><i class="fas fa-chart-line"></i> Confidence</th>
                                                    <th><i class="fas fa-tags"></i> Tags</th>
                                                </tr>
                                            </thead>
                                            <tbody id="airTableBody">
                                                <tr>
                                                    <td colspan="8">
                                                        <div class="no-data">
                                                            <i class="fas fa-cloud-download-alt"></i>
                                                            <p>Click "Refresh Recommendations" to load data</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <div class="results-container" id="surfaceResults">
                                    <div class="results-header">
                                        <h3 class="results-title"><i class="fas fa-truck"></i> Surface Courier Recommendations</h3>
                                        <div class="results-count" id="surfaceCount">0 couriers</div>
                                    </div>
                                    
                                    <div class="data-table-container">
                                        <table class="data-table" id="surfaceTable">
                                            <thead>
                                                <tr>
                                                    <th data-key="Rank"><i class="fas fa-hashtag"></i> Rank</th>
                                                    <th data-key="Courier Name"><i class="fas fa-shipping-fast"></i> Courier Name</th>
                                                    <th data-key="Courier Id"><i class="fas fa-id-badge"></i> ID</th>
                                                    <th data-key="Estimated Cost"><i class="fas fa-rupee-sign"></i> Est. Cost</th>
                                                    <th data-key="Estimated Delivery Days"><i class="fas fa-calendar-day"></i> Days</th>
                                                    <th data-key="RTO Risk"><i class="fas fa-exclamation-triangle"></i> RTO Risk</th>
                                                    <th data-key="Confidence Score"><i class="fas fa-chart-line"></i> Confidence</th>
                                                    <th><i class="fas fa-tags"></i> Tags</th>
                                                </tr>
                                            </thead>
                                            <tbody id="surfaceTableBody">
                                                <tr>
                                                    <td colspan="8">
                                                        <div class="no-data">
                                                            <i class="fas fa-cloud-download-alt"></i>
                                                            <p>Click "Refresh Recommendations" to load data</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Additional Options -->
                          
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
// Global variable to store API response
let apiResponse = null;

// ================= FILTER FUNCTIONALITY =================
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
    var pref = [];
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
        $(".couriourval").hide();
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
        $(".couriourval").hide();
    }); 

    var length = $("input[name='radio1']:checked").length;
    if (length > 0 || length2 > 0) {
        var prefered = "" + pref.join(","); 
        var check_weight = "" + favorite2.join(",");
        var type_val = "" + favorite.join(",");

        if(prefered === '.' ) { prefered = ''; }
        if(check_weight === '.' ) { check_weight = ''; }
        if(type_val === '.' ) { type_val = ''; }
        
        var weightarray = check_weight.split(',');
        var prefred = prefered.split(',');
        var type = type_val.split(',');

        $.each(weightarray, function(index, value) { 
            $.each(prefred, function(key1, value1) { 
                $.each(type, function(typekey, typevalue1) { 
                    if(value1 == "") { var classvalue11 = ""; } else { var classvalue11 = "."; }
                    if(typevalue1 == "") { var typevalue11 = ''; } else { var typevalue11 = "."; }
                    if(value == "") { var classvalue = ""; } else { var classvalue = "."; }

                    $(classvalue11 + value1 + typevalue11 + typevalue1 + classvalue + value).show();
                });
            });
        });
    } else {
        $(".couriourval").hide();
        $(".couriourval").show();
    }
}

// ================= SHIP FORM SUBMIT =================
$(".ship_form").submit(function(event) {
    event.preventDefault();
    document.getElementById("global-loader").style.display = "";
    $.ajax({
        url: '<?php echo base_url('orders/ship');?>',
        type: "POST",
        data: $(this).serialize(),
        cache: false,
        success: function(data) {
            console.log(data);
            if (data.success)
                location.reload();
            else if (data.error)
                alert(data.error);

            document.getElementById("global-loader").style.display = "none";
        }
    });
});

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
const tabs = document.querySelectorAll('.tab');
const resultsContainers = document.querySelectorAll('.results-container');
const fetchButton = document.getElementById('fetchRecommendations');
const loadingIndicator = document.getElementById('recommendationLoading');

const allTableBody = document.getElementById('allTableBody');
const airTableBody = document.getElementById('airTableBody');
const surfaceTableBody = document.getElementById('surfaceTableBody');

const allCountElement = document.getElementById('allCount');
const airCountElement = document.getElementById('airCount');
const surfaceCountElement = document.getElementById('surfaceCount');

// API CONFIG
const API_URL = 'https://pulse.daakit.com/recommend_courier';
const API_KEY = '67e8a932d4b1c8f5a2e0d3c7b4a1f9e8d2c6b5a4f3e2d1c0b9a8f7e6d5c4b3a2';

// ================= TAB SYSTEM =================
tabs.forEach(tab => {
    tab.addEventListener('click', () => {
        const tabId = tab.dataset.tab;
        tabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        resultsContainers.forEach(container => container.classList.remove('active'));
        document.getElementById(`${tabId}Results`).classList.add('active');
    });
});

function restoreActiveTab() {
    const activeTab = document.querySelector('.tab.active');
    if (!activeTab) return;
    const tabId = activeTab.dataset.tab;
    resultsContainers.forEach(c => c.classList.remove('active'));
    document.getElementById(`${tabId}Results`).classList.add('active');
}

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

    return `
    <tr>
        <td class="rank-cell">${courier.Rank}</td>
        <td class="courier-name-cell">${courier['Courier Name']}</td>
        <td class="id-cell">${courier['Courier Id']}</td>
        <td class="cost-cell">₹${estimatedCost}</td>
        <td class="days-cell">${courier['Estimated Delivery Days']} days</td>
        <td class="rto-cell ${rtoClass}">${courier['RTO Risk']}</td>
        <td class="confidence-cell">${courier['Confidence Score']}</td>
        <td class="tags-cell"><div class="tags-container">${tagHTML}</div></td>
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
    const activeTab = document.querySelector('.tab.active').getAttribute('data-tab');

    document.querySelectorAll('.data-table th').forEach(header => {
        header.classList.remove('sorted-asc', 'sorted-desc');
    });

    if (activeTab === 'all') {
        apiData.all = sortData(apiData.all, key);
    } else if (activeTab === 'air') {
        apiData.air = sortData(apiData.air, key);
    } else if (activeTab === 'surface') {
        apiData.surface = sortData(apiData.surface, key);
    }

    th.classList.add(currentSort.order === 'asc' ? 'sorted-asc' : 'sorted-desc');
    renderCourierData(apiData);
}

function initializeSorting() {
    document.querySelectorAll('.data-table th[data-key]').forEach(th => {
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

// ================= RENDER =================
function renderCourierData(data) {
    apiData = data;

    allTableBody.innerHTML = '';
    airTableBody.innerHTML = '';
    surfaceTableBody.innerHTML = '';

    const allR = analyzeRtoRisks(data.all);
    const airR = analyzeRtoRisks(data.air);
    const surfR = analyzeRtoRisks(data.surface);

    if (data.all && data.all.length > 0) {
        data.all.forEach(c => allTableBody.innerHTML += createTableRow(c, allR));
        allCountElement.textContent = `${data.all.length} couriers`;
    } else {
        allCountElement.textContent = '0 couriers';
        allTableBody.innerHTML = `
            <tr>
                <td colspan="8">
                    <div class="no-data">
                        <i class="fas fa-box-open"></i>
                        <p>No courier data available</p>
                    </div>
                </td>
            </tr>
        `;
    }

    if (data.air && data.air.length > 0) {
        data.air.forEach(c => airTableBody.innerHTML += createTableRow(c, airR));
        airCountElement.textContent = `${data.air.length} couriers`;
    } else {
        airCountElement.textContent = '0 couriers';
        airTableBody.innerHTML = `
            <tr>
                <td colspan="8">
                    <div class="no-data">
                        <i class="fas fa-plane-slash"></i>
                        <p>No air courier data available</p>
                    </div>
                </td>
            </tr>
        `;
    }

    if (data.surface && data.surface.length > 0) {
        data.surface.forEach(c => surfaceTableBody.innerHTML += createTableRow(c, surfR));
        surfaceCountElement.textContent = `${data.surface.length} couriers`;
    } else {
        surfaceCountElement.textContent = '0 couriers';
        surfaceTableBody.innerHTML = `
            <tr>
                <td colspan="8">
                    <div class="no-data">
                        <i class="fas fa-truck-slash"></i>
                        <p>No surface courier data available</p>
                    </div>
                </td>
            </tr>
        `;
    }

    initializeSorting();
    restoreActiveTab();
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
    
    // Check if order is COD (you might need to add a COD checkbox or get from order data)
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
    
    // Get order amount from page (you may need to add this hidden field)
    const orderAmountElement = document.querySelector('input[name="order_amount"]') || 
                               document.querySelector('[data-order-amount]');
    const orderAmount = orderAmountElement ? 
                       parseFloat(orderAmountElement.value || orderAmountElement.getAttribute('data-order-amount')) : 
                       800;
    
    // Get weight slab
    const weightSlabElement = document.querySelector('input[name="weight_slab"]') || 
                              document.querySelector('[data-weight-slab]');
    let weightSlab = weightSlabElement ? 
                    parseFloat(weightSlabElement.value || weightSlabElement.getAttribute('data-weight-slab')) : 
                    5;
    
    // Override weight slab with selected filter if available
    if (selectedWeight) {
        weightSlab = parseInt(selectedWeight) / 1000; // Convert grams to kg
    }
    
    // Get zone (you may need to calculate this based on shipping address)
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
    
    // Format date if it's in a different format
    if (orderDate && orderDate.includes('-')) {
        // Already in correct format
    } else if (orderDate) {
        // Try to parse and format
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
    
    // Seller ID (you may want to get this from session or config)
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
        // Additional fields for debugging/info
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

// ================= API CALL =================
async function fetchRecommendations() {
    loadingIndicator.style.display = 'block';
    
    resultsContainers.forEach(container => {
        container.classList.remove('active');
    });

    try {
        // Get dynamic order data
        const orderData = getDynamicOrderData();
        
        // Prepare payload with required fields only
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
        
        // Store the complete API response in the global variable
        apiResponse = {
            ...data,
            request_payload: payload,
            order_data: orderData
        };
        
        // Log the complete API response to console
        console.log('=== COMPLETE API RESPONSE ===');
        console.log('API Response Data:', data);
        console.log('Request Payload:', payload);
        console.log('Order Data:', orderData);
        console.log('All Couriers:', data.all);
        console.log('Air Couriers:', data.air);
        console.log('Surface Couriers:', data.surface);
        console.log('Total All Couriers:', data.all?.length || 0);
        console.log('Total Air Couriers:', data.air?.length || 0);
        console.log('Total Surface Couriers:', data.surface?.length || 0);
        console.log('===============================');
        
        loadingIndicator.style.display = 'none';
        renderCourierData(data);
        showNotification('AI recommendations loaded successfully!', 'success');

    } catch(err) {
        loadingIndicator.style.display = 'none';
        
        // Store error in apiResponse
        apiResponse = { error: err.message };
        
        console.log('=== API ERROR ===');
        console.error('Error:', err);
        console.log('=================');
        
        resultsContainers.forEach(container => {
            container.classList.add('active');
        });

        const errorHtml = `
            <tr>
                <td colspan="8">
                    <div class="no-data">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Failed to fetch recommendations. Please try again.</p>
                        <p style="font-size: 14px; margin-top: 10px;">${err.message}</p>
                    </div>
                </td>
            </tr>
        `;
        
        allTableBody.innerHTML = errorHtml;
        airTableBody.innerHTML = errorHtml;
        surfaceTableBody.innerHTML = errorHtml;
        
        allCountElement.textContent = '0 couriers';
        airCountElement.textContent = '0 couriers';
        surfaceCountElement.textContent = '0 couriers';

        showNotification('Failed to fetch recommendations. Please try again.', 'error');
        console.error(err);
    }
}

// ================= EVENT LISTENERS =================
if (fetchButton) {
    fetchButton.addEventListener('click', fetchRecommendations);
}

// Auto-fetch on page load
document.addEventListener('DOMContentLoaded', () => {
    // Add hidden input fields if they don't exist to store order data
    // This is optional - you can also get this data from existing elements
    
    fetchRecommendations();
});

// Optional: Add a helper function to access the stored API response
function getApiResponse() {
    console.log('Current API Response:', apiResponse);
    return apiResponse;
}

// Make the global variable accessible in browser console
window.apiResponse = apiResponse;

// Add a helper function to manually trigger with custom data (for testing)
window.testWithCustomData = function(customData) {
    const defaultData = getDynamicOrderData();
    const testData = { ...defaultData, ...customData };
    console.log('Test Data:', testData);
    return testData;
};
</script>