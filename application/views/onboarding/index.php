<?php
$completed_steps = 0;
if ($onboarding_kyc) $completed_steps++;
if ($onboarding_recharge) $completed_steps++;
if ($onboarding_warehouse) $completed_steps++;
if ($onboarding_shipment) $completed_steps++;
$total_steps = 4;
$percentage = ($completed_steps / $total_steps) * 100;
?>

<style>
    /* STEPS SECTION (Vanilla CSS for now as requested specifically for banner) */
    .steps-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    .step-card {
        background: #fff;
        border: 2px solid #737D9B;
        border-radius: 12px;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        transition: transform 0.2s, box-shadow 0.2s;
        position: relative;
    }
    .step-card:hover { transform: translateY(-4px); box-shadow: 0 12px 20px -10px rgba(0,0,0,0.1); border-color: #0446DB; }
    .card-top { display: flex; align-items: flex-start; gap: 1rem; width: 100%; position: relative; }
    .icon-box {
        width: 52px; height: 52px;
        background: #F1F5F9;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        color: #737D9B;
        flex-shrink: 0;
    }
    .card-info { flex: 1; }
    .card-info h4 { font-size: 1.125rem; font-weight: 700; color: #1E293B; margin-bottom: 4px; }
    .card-info p { font-size: 0.875rem; color: #64748B; line-height: 1.5; }
    
    .check-status {
        width: 24px; height: 24px;
        border-radius: 50%;
        border: 1px solid #737D9B;
        flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        background: white;
        font-size: 12px;
        font-weight: 700;
        color: #737D9B;
    }
    .check-status.done {
        background: #737D9B;
        border-color: #737D9B;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='white' stroke-width='4'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M5 13l4 4L19 7' /%3E%3C/svg%3E");
        background-size: 60%; background-repeat: no-repeat; background-position: center;
    }

    .card-action-btn {
        background: #737D9B;
        color: #fff;
        border: 1px solid #737D9B;
        border-radius: 8px;
        padding: 0 16px;
        height: 44px;
        font-size: 0.8125rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        width: 100%;
        transition: all 0.2s;
    }
    .card-action-btn:hover { background: #0446DB; border-color: #0446DB; color: #fff; }
    .card-action-btn.disabled { opacity: 0.4; pointer-events: none; background: #94A3B8; border-color: #94A3B8; }


    @media (max-width: 767px) {
        .steps-grid { grid-template-columns: 1fr; gap: 12px; }
        .step-card { padding: 1.25rem; text-align: left; align-items: flex-start; }
        .card-top { flex-direction: row; align-items: center; text-align: left; }
        .icon-box { width: 42px; height: 42px; margin-bottom: 0; }
        .card-info h4 { font-size: 0.9375rem; }
        .card-info p { display: block; font-size: 0.8125rem; }
        .card-action-btn { width: auto; min-width: 130px; }
        .check-status { top: 0.75rem; right: 0.75rem; width: 14px; height: 14px; }
    }
</style>

<div class="mb-4">
    <p class="text-slate-800 text-lg md:text-xl font-medium">Welcome to DAAKIT, <span class="text-[#0446DB] font-bold"><?php echo $user_details->fname ?? 'Admin'; ?>!</span></p>
</div>

<!-- ONBOARDING BANNER (TAILWIND UI) -->
<section class="onboard-banner bg-gradient-to-br from-[#0446DB] to-[#0446DB] rounded-2xl p-5 md:p-6 text-white mb-8 shadow-xl shadow-blue-500/20 flex flex-col gap-3 md:gap-4">
    <div class="flex flex-row justify-between items-start gap-4">
        <div class="flex-1">
            <h2 class="text-xl md:text-3xl font-extrabold mb-1 tracking-tight">Start Your Logistics Journey</h2>
            <p class="text-xs md:text-sm opacity-95 w-full mb-1 leading-relaxed">Complete your onboarding to unlock the full power of Daakit's logistics platform</p>
           
        </div>
        <div class="text-right flex-shrink-0">
            <div class="text-3xl md:text-5xl font-black leading-none"><?php echo $completed_steps; ?>/<?php echo $total_steps; ?></div>
            <div class="text-[10px] md:text-xs font-semibold opacity-90 mt-1 uppercase tracking-wider">Steps Complete</div>
        </div>
    </div>
    <div class="w-full mt-1">
        <div class="bg-white/25 rounded-full h-3 md:h-4 overflow-hidden">
            <div class="bg-white h-full transition-[width] duration-700 ease-in-out rounded-full" style="width: <?php echo $percentage; ?>%;"></div>
        </div>
        <div class="text-sm md:text-base font-bold opacity-100 mt-2"><?php echo round($percentage); ?>% Complete</div>
    </div>
</section>

<!-- STEPS SECTION -->
<div class="mb-5">
    <h3 class="text-lg font-bold text-slate-800 mb-1">Get Started in 4 Easy Steps</h3>
    <p class="text-sm text-slate-500">Follow these steps to complete your onboarding and start shipping</p>
</div>

<div class="steps-grid">
    <!-- KYC Step -->
    <div class="step-card">
        <div class="card-top">
            <div class="icon-box">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
            </div>
            <div class="card-info">
                <h4>Complete KYC</h4>
                <p>Verify your business identity</p>
            </div>
            <div class="check-status <?php echo $onboarding_kyc ? 'done' : ''; ?>"><?php echo $onboarding_kyc ? '' : '1'; ?></div>
        </div>
        <a href="<?php echo base_url('kyc'); ?>" class="card-action-btn <?php echo $onboarding_kyc ? 'disabled' : ''; ?>">
            <?php echo $onboarding_kyc ? 'Verified' : 'Verify Now →'; ?>
        </a>
    </div>

    <!-- Recharge Step -->
    <div class="step-card">
        <div class="card-top">
            <div class="icon-box">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
            </div>
            <div class="card-info">
                <h4>Recharge Wallet</h4>
                <p>Add funds to your wallet</p>
            </div>
            <div class="check-status <?php echo $onboarding_recharge ? 'done' : ''; ?>"><?php echo $onboarding_recharge ? '' : '2'; ?></div>
        </div>
        <a href="<?php echo base_url('billing/rechage_wallet'); ?>" class="card-action-btn <?php echo $onboarding_recharge ? 'disabled' : ''; ?>">
            <?php echo $onboarding_recharge ? 'Recharged' : 'Add Funds →'; ?>
        </a>
    </div>

    <!-- Warehouse Step -->
    <div class="step-card">
        <div class="card-top">
            <div class="icon-box">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            </div>
            <div class="card-info">
                <h4>Add Pickup</h4>
                <p>Set up warehouse address</p>
            </div>
            <div class="check-status <?php echo $onboarding_warehouse ? 'done' : ''; ?>"><?php echo $onboarding_warehouse ? '' : '3'; ?></div>
        </div>
        <a href="<?php echo base_url('warehouse'); ?>" class="card-action-btn <?php echo $onboarding_warehouse ? 'disabled' : ''; ?>">
            <?php echo $onboarding_warehouse ? 'Added' : 'Add Pickup →'; ?>
        </a>
    </div>

    <!-- Order Step -->
    <div class="step-card">
        <div class="card-top">
            <div class="icon-box">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
            </div>
            <div class="card-info">
                <h4>Create Order</h4>
                <p>Start your first shipment</p>
            </div>
            <div class="check-status <?php echo $onboarding_shipment ? 'done' : ''; ?>"><?php echo $onboarding_shipment ? '' : '4'; ?></div>
        </div>
        <a href="<?php echo base_url('orders/all'); ?>" class="card-action-btn <?php echo $onboarding_shipment ? 'disabled' : ''; ?>">
            <?php echo $onboarding_shipment ? 'Created' : 'Create Order →'; ?>
        </a>
    </div>
</div>
