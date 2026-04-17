<style>
/* Bank Modal CSS */
.bank-modal-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.4);
    display: none; /* hidden by default */
    justify-content: center;
    align-items: center;
    z-index: 9999;
    backdrop-filter: blur(2px);
}
.bank-modal-overlay.open {
    display: flex;
}
.bank-modal {
    background: #fff;
    width: 600px;
    max-width: 95vw;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
    max-height: 90vh;
}
.bank-modal-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    padding: 24px;
    border-bottom: 1px solid #E2E8F0;
}
.bank-modal-title {
    font-size: 24px;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 4px;
    margin-top: 0;
}
.bank-modal-sub {
    font-size: 14px;
    color: #64748B;
    margin: 0;
}
.bank-modal-close {
    background: transparent;
    border: none;
    font-size: 24px;
    color: #64748B;
    cursor: pointer;
    line-height: 1;
    padding: 0;
}
.bank-modal-body {
    padding: 24px;
    overflow-y: auto;
}
.bank-form-group {
    margin-bottom: 20px;
}
.bank-form-label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 8px;
}
.bank-form-label span.req { color: #E11D48; }
.bank-form-input {
    width: 100%;
    height: 44px;
    border: 1px solid #E2E8F0;
    border-radius: 8px;
    padding: 0 16px;
    font-size: 14px;
    color: #1E293B;
    background: #F8FAFC;
    transition: all 0.2s;
    outline: none;
}
.bank-form-input:focus {
    border-color: #0446DB;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(4, 70, 219, 0.1);
}
.bank-info-box {
    background: #F0F5FF;
    border: 1px solid #D6E4FF;
    border-radius: 8px;
    padding: 16px;
    display: flex;
    gap: 12px;
    margin-top: 8px;
}
.bank-info-icon {
    color: #0446DB;
    flex-shrink: 0;
}
.bank-info-content h4 {
    font-size: 14px;
    font-weight: 700;
    color: #0446DB;
    margin: 0 0 8px 0;
}
.bank-info-content ul {
    margin: 0; padding: 0 0 0 16px;
    list-style-type: disc;
    color: #0446DB;
    font-size: 12px;
    line-height: 1.6;
}
.bank-modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding: 20px 24px;
    border-top: 1px solid #E2E8F0;
}
.bank-btn {
    height: 44px;
    padding: 0 24px;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.bank-btn-outline {
    background: #fff;
    border: 1px solid #E2E8F0;
    color: #1E293B;
}
.bank-btn-outline:hover {
    background: #F8FAFC;
    border-color: #CBD5E1;
}
.bank-btn-primary {
    background: #0446DB;
    border: 1px solid #0446DB;
    color: #fff;
}
.bank-btn-primary:hover {
    background: #0336A8;
}
</style>

<div class="bank-modal-overlay" id="bankModal">
    <div class="bank-modal">
        <div class="bank-modal-header">
            <div>
                <h3 class="bank-modal-title">Bank Account Details</h3>
                <p class="bank-modal-sub">Add your bank account information for seamless transactions</p>
            </div>
            <button class="bank-modal-close" onclick="closeBankModal()">×</button>
        </div>
        <div class="bank-modal-body">
            <div class="bank-form-group">
                <label class="bank-form-label">Account Holder Name <span class="req">*</span></label>
                <input type="text" class="bank-form-input" placeholder="Enter account holder name">
            </div>
            <div class="bank-form-group">
                <label class="bank-form-label">Account Number <span class="req">*</span></label>
                <input type="text" class="bank-form-input" placeholder="Enter account number">
            </div>
            <div class="bank-form-group">
                <label class="bank-form-label">Confirm Account Number <span class="req">*</span></label>
                <input type="text" class="bank-form-input" placeholder="Re-enter account number">
            </div>
            <div class="bank-form-group">
                <label class="bank-form-label">IFSC Code <span class="req">*</span></label>
                <input type="text" class="bank-form-input" placeholder="Enter IFSC code">
            </div>
            <div class="bank-form-group">
                <label class="bank-form-label">Account Type <span class="req">*</span></label>
                <select class="bank-form-input">
                    <option value="" disabled selected>Select account type</option>
                    <option value="savings">Savings Account</option>
                    <option value="current">Current Account</option>
                </select>
            </div>
            
            <div class="bank-info-box">
                <div class="bank-info-icon">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                </div>
                <div class="bank-info-content">
                    <h4>Important Information</h4>
                    <ul>
                        <li>Ensure the account holder name matches your KYC documents</li>
                        <li>Double-check your account number to avoid payment delays</li>
                        <li>This account will be used for all payouts and settlements</li>
                        <li>You can update these details later from settings</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="bank-modal-footer">
            <button class="bank-btn bank-btn-outline" onclick="closeBankModal()">Cancel</button>
            <button class="bank-btn bank-btn-primary" onclick="closeBankModal()">Save</button>
        </div>
    </div>
</div>

<script>
function openBankModal() {
    document.getElementById('bankModal').classList.add('open');
}
function closeBankModal() {
    document.getElementById('bankModal').classList.remove('open');
}
</script>
