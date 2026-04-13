<div class="row m-t-30">
    <div class="col-sm-6">
        <?php if (in_array('plans_create', $user_details->permissions)) { ?>
            <form method="post" action="<?= current_url(); ?>" id="uploadForm" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="col-sm-12">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="importFile" name="action" value="replace" checked class="custom-control-input">
                            <label class="custom-control-label" for="customCheckDisabled1">Update Records (Update all existing records and insert from the file)</label>
                        </div>
                    </div>
                    <div class="form-group col-sm-8 m-t-30">
                        <label for="exampleFormControlFile1"></label>
                        <input type="file" required="" id="importFileInput" name="importFile" class="form-control">
                        <small class="form-text">
                            <a href="<?= base_url('assets/upload_pricing_sample.csv?v2');?>" class="text-info">Download sample file : <i class="fa fa-download" aria-hidden="true"></i></a>
                        </small>
                    </div>
                    <div class="form-group col-sm-4">
                        <button type="submit" id="submitBtn" style="margin-top: 20px;" class="btn btn-primary">Upload</button>
                    </div>
                </div>
            </form>
        <?php } ?>
    </div>
</div>

<script>
let isValidFile = false;
let selectedFile = null;
let validationErrors = null;

document.getElementById('importFileInput').addEventListener("change", function () {
    selectedFile = this.files[0];
    isValidFile = false;
    validationErrors = null;
    
    if (selectedFile) {
        console.log("Selected File:", selectedFile);
        console.log("File Name:", selectedFile.name);
        check_pricing_with_landing(selectedFile);
    }
});

const calculatePercentage = (csvValue, landingValue) => {
    const csv = parseFloat(csvValue);
    const landing = parseFloat(landingValue);
    if (landing === 0) return 'N/A';
    const percentage = ((csv - landing) / landing) * 100;
    return percentage.toFixed(2);
};

const parseErrorMessages = (message) => {
    const errors = message.split('<br>');
    const parsedErrors = [];
    
    errors.forEach(error => {
        if (error.trim()) {
            const match = error.match(/Row (\d+) \| Plan: ([\d]+) \| Courier: ([\d]+) \| Type: ([\w]+) \| ([\w_]+) exceeds after GST \(CSV: ([\d.]+) > Landing: ([\d.]+)\)/);
            if (match) {
                const csvValue = match[6];
                const landingValue = match[7];
                const percentage =  landingValue;
                
                parsedErrors.push({
                    row: match[1],
                    plan: match[2],
                    courier: match[3],
                    type: match[4],
                    field: match[5],
                    csvValue: csvValue,
                    landingValue: landingValue,
                    percentage: percentage,
                    fullMessage: error.trim()
                });
            } else {
                parsedErrors.push({
                    fullMessage: error.trim(),
                    isInvalid: true
                });
            }
        }
    });
    
    return parsedErrors;
};

const showErrorTable = (errors) => {
    const parsedErrors = parseErrorMessages(errors);
    validationErrors = parsedErrors;
    
    // Create responsive HTML table
    let tableHtml = `
        <div style="overflow-x: auto; max-height: 60vh; overflow-y: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 12px; min-width: 600px;">
                <thead style="position: sticky; top: 0; z-index: 10;">
                    <tr style="background-color: #564ec1;">
                        <th style="padding: 10px 8px; text-align: left; border: 1px solid #dee2e6; color: white; font-weight: bold;">Row</th>
                        <th style="padding: 10px 8px; text-align: left; border: 1px solid #dee2e6; color: white; font-weight: bold;">Plan_Id</th>
                        <th style="padding: 10px 8px; text-align: left; border: 1px solid #dee2e6; color: white; font-weight: bold;">Courier_Id</th>
                        <th style="padding: 10px 8px; text-align: left; border: 1px solid #dee2e6; color: white; font-weight: bold;">Type</th>
                        <th style="padding: 10px 8px; text-align: left; border: 1px solid #dee2e6; color: white; font-weight: bold;">Field</th>
                        <th style="padding: 10px 8px; text-align: left; border: 1px solid #dee2e6; color: white; font-weight: bold;">CSV Value</th>
                        <th style="padding: 10px 8px; text-align: left; border: 1px solid #dee2e6; color: white; font-weight: bold;">Landing Value</th>
                        <th style="padding: 10px 8px; text-align: left; border: 1px solid #dee2e6; color: white; font-weight: bold;">Percentage %</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    parsedErrors.forEach(error => {
        if (error.row) {
            let percentageColor = '#dc3545';
            let percentageBg = '#f8d7da';
            const percentValue = error.percentage;
            // if (percentValue <= 10) {
            //     percentageColor = '#fd7e14';
            //     percentageBg = '#fff3cd';
            // }
            // if (percentValue <= 5) {
            //     percentageColor = '#ffc107';
            //     percentageBg = '#fff3cd';
            // }
            
            tableHtml += `
                <tr style="border-bottom: 1px solid #dee2e6;">
                    <td style="padding: 8px; border: 1px solid #dee2e6;">${error.row}</td>
                    <td style="padding: 8px; border: 1px solid #dee2e6;">${error.plan}</td>
                    <td style="padding: 8px; border: 1px solid #dee2e6;">${error.courier}</td>
                    <td style="padding: 8px; border: 1px solid #dee2e6;">${error.type}</td>
                    <td style="padding: 8px; border: 1px solid #dee2e6; font-weight: bold; background-color: #fff3cd;">${error.field}</td>
                    <td style="padding: 8px; border: 1px solid #dee2e6; color: #dc3545; font-weight: bold;">${error.csvValue}</td>
                    <td style="padding: 8px; border: 1px solid #dee2e6; color: #28a745; font-weight: bold;">${error.landingValue}</td>
                    <td style="padding: 8px; border: 1px solid #dee2e6; color: ${percentageColor}; background-color: ${percentageBg}; font-weight: bold; text-align: center;">${error.percentage}%</td>
                </tr>
            `;
        } else {
            tableHtml += `
                <tr style="border-bottom: 1px solid #dee2e6;">
                    <td colspan="8" style="padding: 10px; border: 1px solid #dee2e6; background-color: #fff3cd; color: #856404;">${error.fullMessage}</td>
                </tr>
            `;
        }
    });
    
    const totalErrors = parsedErrors.filter(e => e.row).length;
    const uniqueRows = new Set(parsedErrors.filter(e => e.row).map(e => e.row)).size;
    const uniqueCouriers = new Set(parsedErrors.filter(e => e.row).map(e => e.courier)).size;
    
    tableHtml += `
                </tbody>
            </table>
        </div>
        
    `;
    
    // Custom HTML with close icon at top right and buttons at bottom
    Swal.fire({
        title: '',
        html: `
            <div style="position: relative;">
                <button onclick="Swal.close()" style="position: absolute; top: -10px; right: -5px; background: none; border: none; font-size: 24px; cursor: pointer; color: #999; transition: color 0.3s;" onmouseover="this.style.color='#333'" onmouseout="this.style.color='#999'">✕</button>
                <div style="text-align: left;">
                    <p style="margin-bottom: 15px; color: #721c24; font-weight: bold; font-size: 14px;">
                        The file contains pricing that exceeds allowed limits:
                    </p>
                    ${tableHtml}
                </div>
            </div>
        `,
        width: '70%',
        showConfirmButton: true,
        showCancelButton: false,
        confirmButtonText: 'Proceed Anyway',
        confirmButtonColor: '#332e74',
        allowOutsideClick: false,
        customClass: {
            confirmButton: 'swal-proceed-anyway-btn'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Proceed anyway - submit the form
            document.getElementById('uploadForm').submit();
        }
    });
};

const showErrorList = (message) => {
    const errors = message.split('<br>');
    let errorList = '<div style="max-height: 50vh; overflow-y: auto; text-align: left;"><ul style="margin: 0; padding-left: 20px;">';
    
    errors.forEach(error => {
        if (error.trim()) {
            errorList += `<li style="margin-bottom: 8px; color: #dc3545; font-size: 13px;">${error.trim()}</li>`;
        }
    });
    
    errorList += '</ul></div>';
    
    Swal.fire({
        // icon: 'warning',
        title: '',
        html: `
            <div style="position: relative;">
                <button onclick="Swal.close()" style="position: absolute; top: -10px; right: -5px; background: none; border: none; font-size: 24px; cursor: pointer; color: #999; transition: color 0.3s;" onmouseover="this.style.color='#333'" onmouseout="this.style.color='#999'">✕</button>
                <div style="text-align: left;">
                    <p style="margin-bottom: 10px; color: #721c24; font-weight: bold;">The following errors were found:</p>
                    ${errorList}
                    <p style="margin-top: 15px; color: #856404; background-color: #fff3cd; padding: 10px; border-radius: 6px;">
                        <strong>⚠️ Warning:</strong> Uploading this file may cause issues. Are you sure you want to proceed?
                    </p>
                </div>
            </div>
        `,
        width: '50%',
        showConfirmButton: true,
        showCancelButton: false,
        confirmButtonText: 'Ok',
        confirmButtonColor: '#D62828'
    })
};

const check_pricing_with_landing = async (file) => {
    try {
        Swal.fire({
            title: 'Validating...',
            text: 'Please wait while we validate your file',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        const formData = new FormData();
        formData.append("importFile", file);
        
        const res = await fetch("https://app.daakit.com/admin/plans/check_pricing_with_landing", {
            method: "POST",
            body: formData,
        });

        const data = await res.json();
        console.log("Response:", data);
        
        if (data.status === "success") {
            isValidFile = true;
            
            Swal.fire({
                icon: 'success',
                title: '✅ Validation Passed',
                html: `
                    <div style="position: relative;">
                        <button onclick="Swal.close()" style="position: absolute; top: -20px; right: -20px; background: none; border: none; font-size: 24px; cursor: pointer; color: #999; transition: color 0.3s;" onmouseover="this.style.color='#333'" onmouseout="this.style.color='#999'">✕</button>
                        <div style="text-align: left; padding: 10px;">
                            <p style="font-size: 16px; margin-bottom: 15px; color: #155724;">${data.message || 'All pricing is valid and within allowed limits.'}</p>
                            
                            <p style="margin-top: 15px; color: #155724; font-size: 13px;">Click "Proceed to Upload" to submit this file.</p>
                        </div>
                    </div>
                `,
                confirmButtonText: 'Proceed to Upload',
                confirmButtonColor: '#28a745',
                showCancelButton: false,
                allowOutsideClick: false,
                customClass: {
                    confirmButton: 'swal-proceed-btn'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('uploadForm').submit();
                }
            });
        } else {
            isValidFile = false;
            
            if (data.message && data.message.includes('<br>')) {
                showErrorTable(data.message);
            } else {
                showErrorList(data.message);
            }
        }
        
    } catch (error) {
        console.error("Error:", error);
        isValidFile = false;
        Swal.fire({
            icon: 'error',
            title: '❌ Error',
            text: 'An error occurred while validating the file. Please try again.',
            confirmButtonText: 'OK',
            confirmButtonColor: '#dc3545'
        });
    }
};

document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!selectedFile) {
        Swal.fire({
            icon: 'warning',
            title: '⚠️ No File Selected',
            text: 'Please select a file to upload.',
            confirmButtonText: 'OK',
            confirmButtonColor: '#ffc107'
        });
        return;
    }
    
    if (validationErrors === null && !isValidFile) {
        Swal.fire({
            icon: 'warning',
            title: '⚠️ Validation Required',
            text: 'Please wait for file validation before submitting.',
            confirmButtonText: 'OK',
            confirmButtonColor: '#ffc107'
        });
        return;
    }
    
    this.submit();
});
</script>

<style>
/* Responsive SweetAlert styles */
.swal-wide {
    width: 90% !important;
    max-width: 1300px !important;
    padding: 1.5rem !important;
}

@media (max-width: 768px) {
    .swal-wide {
        width: 98% !important;
        max-width: 98% !important;
        padding: 1rem !important;
    }
    
    .swal2-popup .swal2-html-container {
        font-size: 12px !important;
        padding: 0 !important;
    }
    
    table {
        font-size: 10px !important;
    }
    
    table th, table td {
        padding: 6px 4px !important;
    }
}

@media (max-width: 576px) {
    table {
        font-size: 9px !important;
        min-width: 500px;
    }
    
    table th, table td {
        padding: 4px 2px !important;
    }
    
    .swal2-popup .swal2-title {
        font-size: 18px !important;
    }
    
    .swal2-confirm, .swal2-cancel {
        font-size: 12px !important;
        padding: 6px 12px !important;
    }
}

.swal2-popup .swal2-html-container {
    overflow: visible !important;
    max-height: 85vh;
    overflow-y: auto;
    padding: 0.5rem !important;
}

/* Table styles */
table {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    border-collapse: collapse;
    width: 100%;
}

table th {
    position: sticky;
    top: 0;
    z-index: 10;
    font-size: 12px;
}

@media (min-width: 769px) {
    table th {
        font-size: 13px;
    }
}

table td {
    word-break: break-word;
    font-size: 11px;
}

@media (min-width: 769px) {
    table td {
        font-size: 12px;
    }
}

/* Scrollbar styles */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: #564ec1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #3e36a1;
}

/* Animation for table rows */
table tbody tr:hover {
    background-color: #f8f9fa;
    transition: background-color 0.2s ease;
}

/* Button customizations */
.swal-proceed-btn {
    background-color: #332e74 ;
    font-size: 16px !important;
    padding: 12px 24px !important;
    font-weight: bold !important;
    border-radius: 6px !important;
}

.swal-proceed-anyway-btn {
    background-color: #332e74 ;
    font-size: 14px !important;
    padding: 10px 20px !important;
    font-weight: bold !important;
    margin-top: 10px !important;
}

@media (max-width: 576px) {
    .swal-proceed-btn {
        font-size: 12px !important;
        padding: 8px 16px !important;
    }
    
    .swal-proceed-anyway-btn {
        font-size: 11px !important;
        padding: 6px 12px !important;
    }
}
</style>

<!-- Include SweetAlert2 CSS and JS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
