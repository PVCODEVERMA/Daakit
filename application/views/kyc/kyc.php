<style>
  :root {
    --blue: #0047FF;
    --blue-dark: #003ACC;
    --blue-light: #EEF2FF;
    --green: #16A34A;
    --green-light: #DCFCE7;
    --red: #DC2626;
    --red-light: #FEE2E2;
    --orange: #D97706;
    --orange-light: #FEF3C7;
    --gray-50: #F8FAFC;
    --gray-100: #F1F5F9;
    --gray-200: #E2E8F0;
    --gray-400: #94A3B8;
    --gray-500: #64748B;
    --gray-700: #334155;
    --gray-900: #0F172A;
    --text: #1E293B;
    --radius: 12px;
  }

  * { box-sizing: border-box; margin: 0; padding: 0; }

  /* ── CONTAINER ── */
  .kyc-wrap {
    width: 100%;
    max-width: 780px;
  }

  .back-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: .875rem;
    font-weight: 500;
    color: var(--gray-500);
    cursor: pointer;
    border: none;
    background: none;
    margin-bottom: 24px;
    transition: color .2s;
    text-decoration: none;
  }
  .back-btn:hover { color: var(--text); }
  .back-btn svg { width: 16px; height: 16px; }

  /* ── STEPPER ── */
  .stepper {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
    margin-bottom: 32px;
  }
  .step {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: .8125rem;
    font-weight: 500;
    color: var(--gray-400);
  }
  .step.done { color: var(--gray-500); }
  .step.active { color: var(--text); font-weight: 600; }

  .step-circle {
    width: 28px; height: 28px;
    border-radius: 50%;
    border: 2px solid var(--gray-200);
    display: flex; align-items: center; justify-content: center;
    font-size: .75rem; font-weight: 700;
    background: #fff;
    color: var(--gray-400);
    flex-shrink: 0;
  }
  .step.done .step-circle {
    background: var(--blue);
    border-color: var(--blue);
    color: #fff;
  }
  .step.active .step-circle {
    background: var(--blue);
    border-color: var(--blue);
    color: #fff;
  }

  .step-line {
    width: 80px; height: 2px;
    background: var(--gray-200);
    margin: 0 8px;
    flex-shrink: 0;
  }
  .step-line.done { background: var(--blue); }

  /* ── SCREENS ── */
  .screen { display: none; }
  .screen.active { display: block; }

  /* ── PAGE HEADING ── */
  .page-title {
    font-size: 1.625rem;
    font-weight: 800;
    color: var(--gray-900);
    margin-bottom: 6px;
    letter-spacing: -.5px;
  }
  .page-sub {
    font-size: .875rem;
    color: var(--gray-500);
    margin-bottom: 28px;
    line-height: 1.5;
  }

  /* ── ENTITY GRID ── */
  .entity-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin-bottom: 28px;
  }
  .entity-card {
    background: #fff;
    border: 2px solid var(--gray-200);
    border-radius: var(--radius);
    padding: 24px 20px;
    cursor: pointer;
    transition: border-color .2s, box-shadow .2s;
    text-align: center;
  }
  .entity-card:hover {
    border-color: var(--blue);
    box-shadow: 0 0 0 3px rgba(0,71,255,.08);
  }
  .entity-card.selected {
    border-color: var(--blue);
    background: var(--blue-light);
    box-shadow: 0 0 0 3px rgba(0,71,255,.12);
  }
  .entity-icon {
    width: 52px; height: 52px;
    border-radius: 14px;
    background: var(--gray-100);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 12px;
    transition: background .2s;
  }
  .entity-card.selected .entity-icon { background: var(--blue); }
  .entity-card.selected .entity-icon svg { color: #fff; }
  .entity-icon svg { width: 24px; height: 24px; color: var(--gray-500); }
  .entity-name {
    font-size: .9375rem;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 8px;
  }
  .entity-desc {
    font-size: .75rem;
    color: var(--gray-500);
    line-height: 1.5;
  }

  /* ── SELFIE SCREEN ── */
  .selfie-layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 28px;
  }
  @media (max-width: 560px) { .selfie-layout { grid-template-columns: 1fr; } }

  .camera-box {
    background: #fff;
    border: 2px dashed var(--gray-200);
    border-radius: var(--radius);
    aspect-ratio: 4/3;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 12px;
    position: relative;
    overflow: hidden;
    min-height: 220px;
  }
  .camera-box.success {
    border-color: var(--green);
    background: #f0fdf4;
  }
  .camera-placeholder svg { width: 56px; height: 56px; color: var(--gray-400); }
  .camera-label { font-size: .8125rem; color: var(--gray-500); }

  .camera-success-icon {
    width: 72px; height: 72px;
    border-radius: 50%;
    border: 3px solid var(--blue);
    display: flex; align-items: center; justify-content: center;
  }
  .camera-success-icon svg { width: 36px; height: 36px; color: var(--blue); }
  .camera-success-text { font-size: .875rem; font-weight: 600; color: var(--blue); }

  .selfie-actions {
    display: flex; gap: 10px; margin-top: 12px;
  }

  .instructions-box {
    background: var(--gray-50);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius);
    padding: 18px;
  }
  .instructions-section { margin-bottom: 14px; }
  .instructions-section:last-child { margin-bottom: 0; }
  .ins-title {
    display: flex; align-items: center; gap: 6px;
    font-size: .8125rem; font-weight: 700;
    color: var(--text); margin-bottom: 10px;
  }
  .ins-title svg { width: 14px; height: 14px; color: var(--blue); }
  .ins-list { list-style: none; display: flex; flex-direction: column; gap: 6px; }
  .ins-list li {
    font-size: .75rem; color: #475569;
    padding-left: 14px; position: relative; line-height: 1.4;
  }
  .ins-list li::before {
    content: '•'; position: absolute; left: 0;
    color: var(--blue); font-weight: 700;
  }
  .ins-list.avoid li::before { color: var(--red); }

  .verified-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px; border-radius: 999px;
    font-size: .75rem; font-weight: 600; margin-top: 10px;
  }
  .verified-badge.ok { background: var(--green-light); color: var(--green); }
  .verified-badge.fail { background: var(--red-light); color: var(--red); }

  /* ── UPLOAD OPTIONS ── */
  .upload-options {
    display: grid; grid-template-columns: 1fr 1fr; gap: 16px;
    margin-bottom: 24px;
  }
  .upload-option {
    background: #fff; border: 2px solid var(--gray-200);
    border-radius: var(--radius); padding: 24px 20px;
    cursor: pointer; transition: border-color .2s, box-shadow .2s;
    position: relative;
  }
  .upload-option:hover { border-color: var(--blue); }
  .upload-option.selected {
    border-color: var(--blue); background: var(--blue);
  }
  .upload-option.selected * { color: #fff !important; }
  .upload-opt-icon {
    width: 44px; height: 44px; border-radius: 12px;
    background: rgba(255,255,255,.2); display: flex;
    align-items: center; justify-content: center; margin-bottom: 12px;
  }
  .upload-option:not(.selected) .upload-opt-icon { background: var(--blue-light); }
  .upload-opt-icon svg { width: 22px; height: 22px; color: var(--blue); }
  .upload-option.selected .upload-opt-icon svg { color: #fff; }
  .upload-opt-name { font-size: 1rem; font-weight: 700; margin-bottom: 6px; color: var(--text); }
  .upload-opt-desc { font-size: .8125rem; color: var(--gray-500); line-height: 1.5; }
  .badge-row { display: flex; gap: 6px; margin-top: 12px; flex-wrap: wrap; }
  .badge {
    padding: 3px 8px; border-radius: 999px; font-size: .6875rem; font-weight: 600;
  }
  .badge.rec { background: var(--blue); color: #fff; }
  .upload-option.selected .badge.rec { background: rgba(255,255,255,.3); }
  .badge.time { background: rgba(255,255,255,.2); color: rgba(255,255,255,.9); }
  .upload-option:not(.selected) .badge.time { background: var(--gray-100); color: var(--gray-500); }

  /* ── MODALS ── */
  .modal-overlay {
    position: fixed; inset: 0;
    background: rgba(15,23,42,.5);
    backdrop-filter: blur(4px);
    display: none; align-items: center; justify-content: center;
    z-index: 9999; padding: 16px;
  }
  .modal-overlay.open { display: flex; }
  .modal {
    background: #fff; border-radius: 16px;
    padding: 28px; max-width: 440px; width: 100%;
    position: relative; box-shadow: 0 24px 48px rgba(0,0,0,.18);
  }
  .modal-close {
    position: absolute; top: 16px; right: 16px;
    width: 32px; height: 32px; border-radius: 50%;
    border: none; background: var(--gray-100);
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    color: var(--gray-500); font-size: 1rem; transition: background .2s;
  }
  .modal-close:hover { background: var(--gray-200); }

  /* ── MANUAL UPLOAD ── */
  .doc-card {
    background: #fff; border: 1px solid var(--gray-200);
    border-radius: var(--radius); margin-bottom: 16px; overflow: hidden;
  }
  .upload-zone {
    border-top: 1px solid var(--gray-100);
    padding: 28px 20px;
    display: flex; flex-direction: column; align-items: center;
    cursor: pointer; transition: background .2s;
  }
  .upload-zone:hover { background: var(--gray-50); }
  .upload-zone svg { width: 28px; height: 28px; color: var(--gray-400); margin-bottom: 8px; }
  .upload-zone-text { font-size: .875rem; font-weight: 600; color: var(--text); margin-bottom: 4px; }
  .upload-zone-hint { font-size: .75rem; color: var(--gray-400); }

  /* ── BUTTONS ── */
  .btn {
    display: inline-flex; align-items: center; justify-content: center;
    gap: 6px; padding: 0 24px; height: 44px;
    border-radius: 8px; font-weight: 600; font-size: .875rem;
    cursor: pointer; border: none; transition: all .2s;
    text-decoration: none;
  }
  .btn-primary {
    background: var(--blue); color: #fff;
    box-shadow: 0 4px 12px rgba(0,71,255,.25);
  }
  .btn-primary:hover { background: var(--blue-dark); transform: translateY(-1px); }
  .btn-primary:disabled { background: var(--gray-300); opacity: 0.6; cursor: not-allowed; }
  .btn-outline {
    background: #fff; color: var(--text);
    border: 1.5px solid var(--gray-200);
  }
  .btn-outline:hover { border-color: var(--blue); color: var(--blue); }

  @media (max-width: 480px) {
    .entity-grid, .upload-options { grid-template-columns: 1fr; }
    .page-title { font-size: 1.35rem; }
  }
</style>
</head>
<body>

<div class="kyc-wrap">

  <a href="<?php echo base_url('analytics'); ?>" class="back-btn">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
    Back
  </a>

  <!-- Stepper -->
  <div class="stepper" id="stepper">
    <div class="step active" id="step1">
      <div class="step-circle">1</div>
      Entity Type
    </div>
    <div class="step-line" id="line1"></div>
    <div class="step" id="step2">
      <div class="step-circle">2</div>
      Selfie
    </div>
    <div class="step-line" id="line2"></div>
    <div class="step" id="step3">
      <div class="step-circle">3</div>
      Documents
    </div>
  </div>

  <!-- Screen 1: Entity -->
  <div class="screen active" id="screen-entity">
    <h1 class="page-title">How do you operate your business?</h1>
    <p class="page-sub">Select the option that best describes your business structure. This helps us set up the right verification flow for you.</p>

    <div class="entity-grid">
      <div class="entity-card" onclick="selectEntity(this, 'individual')">
        <div class="entity-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg></div>
        <div class="entity-name">Individual</div>
        <div class="entity-desc">You run the business yourself with no separate legal entity. Most common for freelancers, small traders, and home sellers.</div>
      </div>
      <div class="entity-card" onclick="selectEntity(this, 'company')">
        <div class="entity-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="15" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg></div>
        <div class="entity-name">Sole Proprietor</div>
        <div class="entity-desc">You own and run a business in your own name like a local shop or a self-employed trade. No co-owners, no separate legal entity.</div>
      </div>
      <div class="entity-card" onclick="selectEntity(this, 'individual')">
        <div class="entity-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg></div>
        <div class="entity-name">Partnership</div>
        <div class="entity-desc">You own and run a business in your own name like a local shop or a self-employed trade. No co-owners, no separate legal entity.</div>
      </div>
      <div class="entity-card" onclick="selectEntity(this, 'company')">
        <div class="entity-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="15" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg></div>
        <div class="entity-name">Company</div>
        <div class="entity-desc">A formally registered business - Private Limited, LLP, or Public Limited. Has its own legal identity, separate from its owners or directors.</div>
      </div>
    </div>

    <div style="display:flex; justify-content:flex-end;">
      <button class="btn btn-primary" id="entityNextBtn" onclick="goToScreen('screen-selfie')" disabled>Next Step</button>
    </div>
  </div>

  <!-- Screen 2: Selfie -->
  <div class="screen" id="screen-selfie">
    <h1 class="page-title">Verify your Identity</h1>
    <p class="page-sub">Position your face within the frame for a clear selfie.</p>

    <div class="selfie-layout">
      <div class="camera-box" id="cameraBox">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="56" height="56" color="#E2E8F0"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
        <div class="camera-label">Waiting for camera...</div>
      </div>
      <div class="instructions-box">
        <div class="ins-title">Instructions:</div>
        <ul class="ins-list">
          <li>Ensure your face is well-lit.</li>
          <li>Remove glasses and hats.</li>
          <li>Look directly at the camera.</li>
        </ul>
      </div>
    </div>

    <div style="display:flex; justify-content:space-between;">
      <button class="btn btn-outline" onclick="goToScreen('screen-entity')">Back</button>
      <button class="btn btn-primary" onclick="takePhoto()">Capture & Verify</button>
    </div>
  </div>

  <!-- Screen 3: Upload Options -->
  <div class="screen" id="screen-upload">
    <h1 class="page-title">Upload Documents</h1>
    <p class="page-sub">Choose your preferred verification method.</p>

    <div class="upload-options">
      <div class="upload-option selected" id="opt-instant" onclick="selectUpload('instant')">
        <div class="upload-opt-name">Instant KYC (DigiLocker)</div>
        <div class="upload-opt-desc">Fastest & most secure method. Recommended.</div>
      </div>
      <div class="upload-option" id="opt-manual" onclick="selectUpload('manual')">
        <div class="upload-opt-name">Manual Upload</div>
        <div class="upload-opt-desc">Upload scans of your documents manually.</div>
      </div>
    </div>

    <div style="display:flex; justify-content:space-between;">
      <button class="btn btn-outline" onclick="goToScreen('screen-selfie')">Back</button>
      <button class="btn btn-primary" onclick="proceed()">Continue</button>
    </div>
  </div>

  <!-- Success Screen -->
  <div class="screen" id="screen-success">
    <div style="text-center; background: #fff; padding: 48px; border-radius: 20px; border: 1px solid var(--gray-200); text-align: center;">
      <div style="width: 72px; height: 72px; border-radius: 50%; background: var(--blue); display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" width="36" height="36" color="#fff"><path d="M20 6L9 17l-5-5"/></svg>
      </div>
      <h2 class="page-title" style="margin-bottom: 12px;">KYC Submitted Successfully!</h2>
      <p class="page-sub">Verification takes 24-48 hours. You'll be notified via email.</p>
      <a href="<?php echo base_url('analytics'); ?>" class="btn btn-primary">Back to Dashboard</a>
    </div>
  </div>

  <!-- DigiLocker Modal -->
  <div class="modal-overlay" id="digiModal">
    <div class="modal">
      <button class="modal-close" onclick="closeModal()">✕</button>
      <h3 style="font-weight:700; margin-bottom: 14px;">DigiLocker Authentication</h3>
      <p style="font-size: .875rem; color: var(--gray-500); line-height: 1.6; margin-bottom: 24px;">
        You will be redirected to the secure government portal to authenticate via Aadhaar.
      </p>
      <div style="display:flex; gap: 10px;">
        <button class="btn btn-outline" style="flex:1" onclick="closeModal()">Cancel</button>
        <button class="btn btn-primary" style="flex:1" onclick="digiDone()">Continue</button>
      </div>
    </div>
  </div>

</div>

<script>
  let currentScreen = 'screen-entity';
  let selectedEntity = null;
  let uploadMethod = 'instant';

  function goToScreen(id) {
    document.querySelectorAll('.screen').forEach(s => s.classList.remove('active'));
    document.getElementById(id).classList.add('active');
    currentScreen = id;
    updateStepper();
  }

  function updateStepper() {
    const s1 = document.getElementById('step1'), s2 = document.getElementById('step2'), s3 = document.getElementById('step3');
    const l1 = document.getElementById('line1'), l2 = document.getElementById('line2');
    
    [s1,s2,s3].forEach(s => s.classList.remove('active', 'done'));
    [l1,l2].forEach(l => l.classList.remove('done'));

    if (currentScreen === 'screen-entity') { s1.classList.add('active'); }
    else if (currentScreen === 'screen-selfie') { s1.classList.add('done'); l1.classList.add('done'); s2.classList.add('active'); }
    else { s1.classList.add('done'); l1.classList.add('done'); s2.classList.add('done'); l2.classList.add('done'); s3.classList.add('active'); }
  }

  function selectEntity(el, type) {
    document.querySelectorAll('.entity-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    selectedEntity = type;
    document.getElementById('entityNextBtn').disabled = false;
  }

  function takePhoto() {
    const box = document.getElementById('cameraBox');
    box.classList.add('success');
    box.innerHTML = '<div style="color:var(--green); font-weight:700;">Selfie Verified!</div>';
    setTimeout(() => goToScreen('screen-upload'), 1000);
  }

  function selectUpload(type) {
    uploadMethod = type;
    document.querySelectorAll('.upload-option').forEach(o => o.classList.remove('selected'));
    document.getElementById('opt-' + (type === 'instant' ? 'instant' : 'manual')).classList.add('selected');
  }

  function proceed() {
    if (uploadMethod === 'instant') document.getElementById('digiModal').classList.add('open');
    else alert('Manual upload restricted in demo.');
  }

  function closeModal() { document.getElementById('digiModal').classList.remove('open'); }
  function digiDone() { closeModal(); goToScreen('screen-success'); }
</script>
</script>
