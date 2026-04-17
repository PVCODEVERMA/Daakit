<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KYC Verification</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link
    href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;0,9..40,800;1,9..40,400&family=DM+Serif+Display:ital@0;1&display=swap"
    rel="stylesheet">
  <style>
    :root {
      --blue: #1a4fd6;
      --blue-mid: #2563eb;
      --blue-light: #eff6ff;
      --blue-border: #bfdbfe;
      --green: #16a34a;
      --green-light: #dcfce7;
      --green-border: #86efac;
      --red: #dc2626;
      --red-light: #fee2e2;
      --orange: #d97706;
      --orange-light: #fffbeb;
      --orange-border: #fde68a;
      --gray-50: #f8fafc;
      --gray-100: #f1f5f9;
      --gray-200: #e2e8f0;
      --gray-300: #cbd5e1;
      --gray-400: #94a3b8;
      --gray-500: #64748b;
      --gray-600: #475569;
      --gray-700: #334155;
      --gray-900: #0f172a;
      --text: #1e293b;
      --radius: 14px;
      --shadow-sm: 0 1px 3px rgba(0, 0, 0, .08), 0 1px 2px rgba(0, 0, 0, .04);
      --shadow: 0 4px 16px rgba(0, 0, 0, .08), 0 2px 6px rgba(0, 0, 0, .04);
      --shadow-lg: 0 12px 40px rgba(0, 0, 0, .12), 0 4px 16px rgba(0, 0, 0, .06);
    }

    /* ── NOTES BOX ── */
    .notes-box {
      background: #fffbef;
      border: 1px solid #fef3c7;
      border-radius: 12px;
      padding: 20px 24px;
      margin-top: 32px;
      margin-bottom: 32px;
      display: flex;
      gap: 16px;
      align-items: flex-start;
    }

    .notes-icon {
      color: #d97706;
      width: 24px;
      height: 24px;
      flex-shrink: 0;
      margin-top: 2px;
    }

    .notes-content {
      flex: 1;
    }

    .notes-title {
      font-size: 1rem;
      font-weight: 700;
      color: #92400e;
      margin-bottom: 8px;
    }

    .notes-list {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .notes-list li {
      font-size: 0.875rem;
      color: #92400e;
      margin-bottom: 6px;
      position: relative;
      padding-left: 14px;
      line-height: 1.5;
      opacity: 0.9;
    }

    .notes-list li::before {
      content: '•';
      position: absolute;
      left: 0;
      color: #d97706;
      font-weight: bold;
    }

    /* ── REVIEW BANNER ── */
    .review-banner {
      background: #eff6ff;
      border: 1.5px solid #dbeafe;
      border-radius: 12px;
      padding: 16px 20px;
      margin-top: 32px;
      display: flex;
      gap: 16px;
      align-items: center;
    }

    .review-banner-icon {
      width: 36px;
      height: 36px;
      background: #fff;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--blue);
      flex-shrink: 0;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .review-banner-text {
      font-size: 0.875rem;
      color: var(--blue);
      font-weight: 500;
      line-height: 1.4;
    }

    .review-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin: 24px 0;
    }

    .review-card {
      background: #fff;
      border: 1px solid var(--gray-200);
      border-radius: 16px;
      padding: 20px;
      position: relative;
    }

    .review-card-head {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 16px;
    }

    .review-doc-title {
      display: flex;
      align-items: center;
      gap: 10px;
      font-weight: 700;
      font-size: 1rem;
      color: var(--gray-900);
    }

    .review-doc-icon {
      width: 32px;
      height: 32px;
      background: #f1f5f9;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--gray-500);
    }

    .review-check {
      color: var(--green);
    }

    .review-img-box {
      width: 100%;
      aspect-ratio: 16/10;
      background: #f8fafc;
      border-radius: 12px;
      margin-bottom: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      border: 1px solid var(--gray-100);
    }

    .review-img-box img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .review-details {
      border-top: 1px solid var(--gray-100);
      padding-top: 14px;
    }

    .review-row {
      display: flex;
      flex-direction: column;
      gap: 4px;
      margin-bottom: 12px;
    }

    .review-row:last-child {
      margin-bottom: 0;
    }

    .review-label {
      font-size: 0.75rem;
      font-weight: 600;
      color: var(--gray-500);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .review-value {
      font-size: 0.875rem;
      font-weight: 600;
      color: var(--gray-900);
      background: #f8fafc;
      padding: 10px 14px;
      border: 1.5px solid #edf2f7;
      border-radius: 10px;
      margin-top: 4px;
      display: block;
      word-break: break-all;
      line-height: 1.4;
    }

    .selfie-hero {
      background: #fff;
      border: 1px solid var(--gray-200);
      border-radius: 16px;
      padding: 24px;
      margin-bottom: 24px;
      display: flex;
      gap: 24px;
      align-items: center;
    }

    .selfie-hero-img {
      width: 120px;
      height: 120px;
      border-radius: 12px;
      object-fit: cover;
      background: #f1f5f9;
      border: 1px solid var(--gray-100);
    }

    .selfie-hero-content {
      flex: 1;
    }

    .selfie-hero-title {
      font-size: 1.125rem;
      font-weight: 700;
      color: var(--gray-900);
      margin-bottom: 8px;
    }

    .selfie-hero-tag {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: #f0fdf4;
      border: 1px solid #bcf0da;
      color: #166534;
      padding: 6px 12px;
      border-radius: 8px;
      font-size: 0.8125rem;
      font-weight: 600;
    }

    *,
    *::before,
    *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'DM Sans', sans-serif;
      background: #f0f4fa;
      min-height: 100vh;
      color: var(--text);
    }

    /* ── TOAST ── */
    #toast-container {
      position: fixed;
      top: 20px;
      left: 50%;
      transform: translateX(-50%);
      z-index: 99999;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 10px;
      pointer-events: none;
      width: 100%;
      padding: 0 16px;
    }

    .toast {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 8px 16px;
      border-radius: 99px;
      min-width: 250px;
      max-width: 420px;
      background: #fff;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08), 0 2px 4px rgba(0, 0, 0, 0.04);
      pointer-events: all;
      animation: toastIn .35s cubic-bezier(.21, 1.02, .73, 1);
      position: relative;
      border: 1px solid rgba(0, 0, 0, 0.05);
    }

    @media (min-width: 1200px) {
      .toast {
        max-width: 500px;
      }
    }

    .toast::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 20px;
      right: 20px;
      height: 2px;
      border-radius: 2px;
      transform-origin: left;
      animation: toastBar 5s linear forwards;
      opacity: 0.6;
    }

    .toast.success::after { background: #10b981; }
    .toast.error::after { background: #ef4444; }
    .toast.info::after { background: #3b82f6; }
    .toast.warning::after { background: #f59e0b; }

    .toast-icon {
      width: 24px;
      height: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .toast.success .toast-icon { color: #3b82f6; } /* Blue as requested */
    .toast.error .toast-icon { color: #ef4444; }
    .toast.info .toast-icon { color: #3b82f6; }
    .toast.warning .toast-icon { color: #f59e0b; }

    .toast-loader {
      width: 18px;
      height: 18px;
      border: 2px solid rgba(59, 130, 246, 0.15);
      border-top-color: #3b82f6;
      border-radius: 50%;
      animation: toastSpinner 0.6s linear infinite;
    }

    @keyframes toastSpinner {
      to { transform: rotate(360deg); }
    }

    .toast-icon svg {
      animation: toastIconPop 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    }

    @keyframes toastIconPop {
      0% { transform: scale(0.5); opacity: 0; }
      100% { transform: scale(1); opacity: 1; }
    }

    .toast-body {
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 1px;
    }

    .toast-title {
      font-size: .875rem;
      font-weight: 600;
      color: #1e293b;
    }

    .toast-msg {
      font-size: .75rem;
      color: #64748b;
      line-height: 1.2;
    }

    .toast-close {
      background: none;
      border: none;
      cursor: pointer;
      color: var(--gray-400);
      padding: 2px;
      display: flex;
      align-items: center;
      flex-shrink: 0;
      transition: color .15s;
    }

    .toast-close:hover {
      color: var(--text);
    }

    .toast.hiding {
      animation: toastOut .25s ease forwards;
    }

    @keyframes toastIn {
      from {
        opacity: 0;
        transform: translateY(-20px) scale(.95);
      }

      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    @keyframes toastOut {
      from {
        opacity: 1;
        transform: translateY(0) scale(1);
      }

      to {
        opacity: 0;
        transform: translateY(-20px) scale(.95);
      }
    }

    @keyframes toastBar {
      from {
        transform: scaleX(1);
      }

      to {
        transform: scaleX(0);
      }
    }

    /* ── SCANNING OVERLAY ── */
    .scan-overlay {
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, .75);
      backdrop-filter: blur(6px);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 9998;
      flex-direction: column;
      gap: 20px;
    }

    .scan-overlay.open {
      display: flex;
    }

    .scan-box {
      width: 280px;
      height: 180px;
      border: 2px solid var(--blue);
      border-radius: 16px;
      position: relative;
      overflow: hidden;
      background: rgba(26, 79, 214, .05);
    }

    .scan-line {
      position: absolute;
      left: 0;
      right: 0;
      height: 3px;
      background: linear-gradient(90deg, transparent, var(--blue), transparent);
      top: 0;
      animation: scanMove 1.5s ease-in-out infinite;
      box-shadow: 0 0 12px rgba(26, 79, 214, .6);
    }

    .scan-corner {
      position: absolute;
      width: 20px;
      height: 20px;
      border-color: var(--blue);
      border-style: solid;
    }

    .scan-corner.tl {
      top: -1px;
      left: -1px;
      border-width: 3px 0 0 3px;
      border-radius: 4px 0 0 0;
    }

    .scan-corner.tr {
      top: -1px;
      right: -1px;
      border-width: 3px 3px 0 0;
      border-radius: 0 4px 0 0;
    }

    .scan-corner.bl {
      bottom: -1px;
      left: -1px;
      border-width: 0 0 3px 3px;
      border-radius: 0 0 0 4px;
    }

    .scan-corner.br {
      bottom: -1px;
      right: -1px;
      border-width: 0 3px 3px 0;
      border-radius: 0 0 4px 0;
    }

    .scan-label {
      color: #fff;
      font-size: .9rem;
      font-weight: 600;
      text-align: center;
    }

    .scan-sub {
      color: rgba(255, 255, 255, .6);
      font-size: .78rem;
      text-align: center;
    }

    @keyframes scanMove {

      0%,
      100% {
        top: 10%;
      }

      50% {
        top: 80%;
      }
    }

    /* ── WRAP ── */
    .kyc-wrap {
      width: 100%;
      max-width: 800px;
      margin: 0 auto;
    }

    @media (min-width: 1200px) {
      .kyc-wrap {
        max-width: 1100px;
      }
    }

    /* ── BACK ── */
    .back-btn {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: .875rem;
      font-weight: 500;
      color: var(--gray-600);
      cursor: pointer;
      border: none;
      background: none;
      margin-bottom: 28px;
      padding: 0;
      transition: color .15s;
    }

    .back-btn:hover {
      color: var(--text);
    }

    .back-btn svg {
      width: 16px;
      height: 16px;
    }

    /* ── STEPPER ── */
    .stepper {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0;
      margin-bottom: 36px;
      flex-wrap: nowrap;
    }

    .step {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: .8125rem;
      font-weight: 500;
      color: var(--gray-400);
      white-space: nowrap;
    }

    .step.done {
      color: var(--gray-500);
    }

    .step.active {
      color: var(--text);
      font-weight: 700;
    }

    .step-circle {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      border: 2px solid var(--gray-300);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: .75rem;
      font-weight: 700;
      background: #fff;
      color: var(--gray-400);
      flex-shrink: 0;
      transition: all .25s;
    }

    .step.done .step-circle,
    .step.active .step-circle {
      background: var(--blue);
      border-color: var(--blue);
      color: #fff;
      box-shadow: 0 2px 8px rgba(26, 79, 214, .35);
    }

    .step-line {
      flex: 1;
      max-width: 80px;
      height: 2px;
      background: var(--gray-200);
      margin: 0 6px;
      flex-shrink: 0;
      transition: background .25s;
    }

    .step-line.done {
      background: var(--blue);
    }

    /* ── SCREENS ── */
    .screen {
      display: none;
      animation: fadeUp .3s ease;
    }

    .screen.active {
      display: block;
    }

    @keyframes fadeUp {
      from {
        opacity: 0;
        transform: translateY(10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* ── PAGE HEAD ── */
    .page-title {
      font-size: 2rem;
      font-weight: 700;
      color: var(--gray-900);
      margin-bottom: 8px;
      text-align: center;
      letter-spacing: -0.5px;
    }

    .page-sub {
      font-size: 1rem;
      color: var(--gray-500);
      margin-bottom: 32px;
      line-height: 1.5;
      text-align: center;
    }

    /* ================= SCREEN 1: ENTITY SELECTION CSS ================= */
    .entity-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 14px;
      margin-bottom: 32px;
    }

    @media (min-width: 1200px) {
      .entity-grid {
        grid-template-columns: 1fr 1fr; /* Keep 2 columns (2x2 grid) */
        gap: 24px; /* Reduced gap for laptops */
        max-width: 860px;
        margin-left: auto;
        margin-right: auto;
      }

      .entity-card {
        padding: 20px 16px !important; /* Compact padding */
        min-height: 150px; /* Further reduced height for 13-16" screens */
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
      }

      .entity-icon {
        width: 64px !important; /* Scaled down icons */
        height: 64px !important;
        margin-bottom: 16px !important;
      }

      .entity-name {
        font-size: 1.125rem !important; /* Slightly smaller title */
        margin-bottom: 8px !important;
      }

      .entity-desc {
        font-size: 0.85rem !important; /* Compact description text */
        line-height: 1.4 !important;
      }
        max-width: 320px;
        margin: 0 auto;
      }
    }

    .doc-card {
      background: #fff;
      border: 1.5px solid var(--gray-200);
      border-radius: 16px;
      margin-bottom: 24px;
      overflow: hidden;
      transition: all 0.3s ease;
    }

    .doc-card.completed {
      border-color: var(--green);
      background: #f0fdf4;
    }

    .doc-card-head {
      padding: 24px;
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
    }

    .doc-head-left {
      display: flex;
      gap: 16px;
    }

    .doc-icon {
      width: 48px;
      height: 48px;
      background: #eff6ff;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--blue);
      flex-shrink: 0;
    }

    .doc-icon.green {
      background: #dcfce7;
      color: var(--green);
    }

    .doc-title {
      font-size: 1.1rem;
      font-weight: 700;
      color: var(--gray-900);
      margin-bottom: 4px;
    }

    .doc-subtitle {
      font-size: 0.875rem;
      color: var(--gray-500);
      line-height: 1.4;
    }

    .badge-mandatory {
      background: #fee2e2;
      color: #991b1b;
      font-size: 0.65rem;
      font-weight: 800;
      text-transform: uppercase;
      padding: 3px 8px;
      border-radius: 6px;
      margin-left: 8px;
      vertical-align: middle;
    }

    /* ================= SCREEN 4: MANUAL UPLOAD CSS ================= */
    .upload-zone {
      margin: 0 24px 24px;
      border: 2px dashed var(--gray-200);
      border-radius: 12px;
      padding: 32px;
      text-align: center;
      cursor: pointer;
      transition: all 0.2s;
    }

    .upload-zone:hover {
      border-color: var(--blue);
      background: #f8faff;
    }

    .upload-zone-icon {
      color: var(--blue);
      margin-bottom: 12px;
    }

    .upload-zone-icon svg {
      width: 32px;
      height: 32px;
    }

    .upload-zone-text {
      font-size: 1rem;
      font-weight: 600;
      color: var(--gray-700);
      margin-bottom: 4px;
    }

    .upload-zone-hint {
      font-size: 0.8rem;
      color: var(--gray-400);
    }

    .uploaded-file {
      margin: 0 24px 24px;
      background: #f0fdf4;
      border: 1.5px solid #bcf0da;
      border-radius: 12px;
      padding: 16px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .uploaded-left {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .uploaded-info {
      line-height: 1.2;
    }

    .uploaded-name {
      font-size: 0.95rem;
      font-weight: 700;
      color: #065f46;
    }

    .uploaded-status {
      font-size: 0.8rem;
      color: #059669;
      margin-top: 2px;
    }

    .btn-remove {
      color: #fca5a5;
      cursor: pointer;
      padding: 4px;
      border-radius: 50%;
      transition: all 0.2s;
    }

    .btn-remove:hover {
      background: #fee2e2;
      color: #ef4444;
    }

    /* ── DOC FORM ── */
    .doc-form {
      padding: 0 24px 24px;
      border-top: 1px solid var(--gray-100);
      padding-top: 24px;
      background: #f8fafc;
    }

    .doc-form-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 16px;
    }

    .doc-form-title {
      font-size: 0.9rem;
      font-weight: 700;
      color: var(--gray-600);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .form-row {
      margin-bottom: 20px;
    }

    .form-label {
      display: block;
      font-size: 0.9rem;
      font-weight: 600;
      color: var(--gray-700);
      margin-bottom: 8px;
    }

    .form-label span {
      color: var(--red);
    }

    .form-input,
    .form-select {
      width: 100%;
      padding: 12px 16px;
      border: 1.5px solid var(--gray-200);
      border-radius: 10px;
      font-size: 1rem;
      color: var(--gray-900);
      background: #fff;
      transition: all 0.2s;
    }

    .form-input:focus,
    .form-select:focus {
      outline: none;
      border-color: var(--blue);
      box-shadow: 0 0 0 4px rgba(26, 79, 214, 0.08);
    }

    .form-input::placeholder {
      color: var(--gray-400);
    }

    .form-input.auto-filled {
      border-color: var(--green);
      background-color: #f0fdf4;
    }

    .entity-card {
      background: #fff;
      border: 2px solid var(--gray-200);
      border-radius: var(--radius);
      padding: 24px 20px;
      cursor: pointer;
      transition: border-color .2s, box-shadow .2s, transform .15s;
      text-align: center;
    }

    .entity-card:hover {
      border-color: #0446DB;
      transform: translateY(-4px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
    }

    .entity-card:hover .entity-icon {
      background-color: #0446DB;
      color: #fff;
    }

    .entity-card:hover .entity-icon svg {
      color: #fff;
      stroke: #fff;
    }

    .entity-card.selected {
      border-color: #0446DB;
      background: #f8faff;
      box-shadow: 0 0 0 1px #0446DB;
    }

    .entity-card.selected .entity-name {
      color: #0446DB;
    }

    .entity-card.selected .entity-icon {
      background-color: #0446DB;
      color: #fff;
    }

    .entity-card.selected .entity-icon svg {
      color: #fff;
      stroke: #fff;
    }

    .entity-icon {
      width: 52px;
      height: 52px;
      border-radius: 14px;
      background: var(--gray-100);
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 14px;
      transition: background .2s;
    }

    .entity-card.selected .entity-icon {
      background: var(--blue);
    }

    .entity-icon svg {
      width: 24px;
      height: 24px;
      color: var(--gray-500);
    }

    .entity-card.selected .entity-icon svg {
      color: #fff;
    }

    .entity-name {
      font-size: .9375rem;
      font-weight: 700;
      color: var(--text);
      margin-bottom: 6px;
    }

    .entity-desc {
      font-size: .775rem;
      color: var(--gray-500);
      line-height: 1.5;
    }

    /* ================= SCREEN 2: SELFIE VERIFICATION CSS ================= */
    .selfie-layout {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
      margin-bottom: 24px;
      align-items: stretch;
    }

    .camera-wrapper {
      display: flex;
      flex-direction: column;
      height: 100%;
    }

    .camera-box {
      background: #f8fbff;
      border: 1.5px dashed #bfdbfe;
      border-radius: 14px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 22px;
      min-height: 280px;
      position: relative;
      overflow: hidden;
      flex: 1;
    }

    .cam-circle {
      width: 140px;
      height: 140px;
      border-radius: 50%;
      border: 4px solid #dbeafe;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #fff;
    }

    .cam-circle svg {
      width: 52px;
      height: 52px;
      color: #2563eb;
    }

    .cam-label {
      font-size: .85rem;
      color: #64748b;
      font-weight: 500;
    }

    .selfie-btn-row {
      display: flex;
      gap: 10px;
      margin-top: 14px;
      flex-shrink: 0;
    }

    .selfie-btn-row .btn {
      padding: 0 12px;
      white-space: nowrap;
      font-size: 0.825rem;
    }

    .btn-cam-icon {
      width: 44px;
      height: 44px;
      border-radius: 10px;
      border: 1.5px solid var(--gray-200);
      background: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      flex-shrink: 0;
    }

    .btn-cam-icon svg {
      width: 20px;
      height: 20px;
      color: var(--blue);
    }

    .ins-wrapper {
      display: flex;
      flex-direction: column;
      height: 100%;
    }

    .ins-box {
      background: #f3f8ff;
      border: none;
      border-radius: 14px;
      padding: 24px 28px;
      flex: 1;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
    }

    .ins-section {
      margin-bottom: 24px;
    }

    .ins-section:last-child {
      margin-bottom: 0;
    }

    .ins-head {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: .85rem;
      font-weight: 800;
      color: var(--text);
      margin-bottom: 14px;
    }

    .ins-head svg {
      width: 16px;
      height: 16px;
      color: #2563eb;
    }

    .ins-list {
      list-style: none;
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .ins-list li {
      font-size: .8rem;
      color: #64748b;
      padding-left: 16px;
      position: relative;
      line-height: 1.45;
    }

    .ins-list li::before {
      content: '';
      position: absolute;
      left: 0;
      top: 6px;
      width: 4px;
      height: 4px;
      border-radius: 50%;
      background: #2563eb;
    }

    .verified-tag {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 6px 16px;
      border-radius: 6px;
      font-size: .85rem;
      font-weight: 600;
      margin-top: 16px;
    }

    .verified-tag.ok {
      background: #86efac;
      color: #166534;
    }

    .verified-tag.fail {
      background: #fca5a5;
      color: #991b1b;
    }

    /* ================= SCREEN 3: UPLOAD OPTIONS CSS ================= */
    .upload-options {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 14px;
      margin-bottom: 20px;
    }

    .upload-opt {
      background: #fff;
      border: 2px solid var(--gray-200);
      border-radius: var(--radius);
      padding: 22px 20px;
      cursor: pointer;
      transition: border-color .2s, box-shadow .2s;
    }

    .upload-opt:hover {
      border-color: var(--blue);
    }

    .upload-opt.selected {
      background: var(--blue);
      border-color: var(--blue);
    }

    .upload-opt.selected * {
      color: #fff !important;
    }

    .upl-icon {
      width: 44px;
      height: 44px;
      border-radius: 12px;
      background: var(--blue-light);
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 12px;
    }

    .upload-opt.selected .upl-icon {
      background: rgba(255, 255, 255, .2);
    }

    .upl-icon svg {
      width: 20px;
      height: 20px;
      color: var(--blue);
    }

    .upload-opt.selected .upl-icon svg {
      color: #fff;
    }

    .upl-name {
      font-size: .9375rem;
      font-weight: 700;
      color: var(--text);
      margin-bottom: 5px;
    }

    .upl-desc {
      font-size: .775rem;
      color: var(--gray-500);
      line-height: 1.5;
      margin-bottom: 12px;
    }

    .badge-row {
      display: flex;
      gap: 6px;
      flex-wrap: wrap;
    }

    .badge {
      padding: 3px 10px;
      border-radius: 999px;
      font-size: .6875rem;
      font-weight: 600;
    }

    .badge.rec {
      background: var(--blue);
      color: #fff;
    }

    .upload-opt.selected .badge.rec {
      background: rgba(255, 255, 255, .25);
      color: #fff;
    }

    .badge.muted {
      background: var(--gray-100);
      color: var(--gray-500);
    }

    .upload-opt.selected .badge.muted {
      background: rgba(255, 255, 255, .18);
      color: rgba(255, 255, 255, .85);
    }

    .req-docs-box {
      background: #fff;
      border: 1px solid var(--gray-200);
      border-radius: var(--radius);
      padding: 16px 20px;
      margin-bottom: 28px;
      box-shadow: var(--shadow-sm);
    }

    .req-head {
      display: flex;
      align-items: center;
      gap: 7px;
      font-size: .8125rem;
      font-weight: 700;
      color: var(--text);
      margin-bottom: 12px;
    }

    .req-head svg {
      width: 14px;
      height: 14px;
      color: var(--blue);
    }

    .req-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 8px 16px;
    }

    .req-item {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: .8125rem;
      color: var(--gray-600);
    }

    .req-item svg {
      width: 13px;
      height: 13px;
      color: var(--green);
      flex-shrink: 0;
    }

    /* ── DOC CARD ── */
    .doc-card {
      background: #fff;
      border: 1.5px solid var(--gray-200);
      border-radius: var(--radius);
      margin-bottom: 14px;
      overflow: hidden;
      box-shadow: var(--shadow-sm);
      transition: border-color .2s;
    }

    .doc-card.completed {
      border-color: var(--green-border);
    }

    .doc-card-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 14px 18px;
    }

    .doc-head-left {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .doc-icon {
      width: 36px;
      height: 36px;
      border-radius: 10px;
      background: var(--blue);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .doc-icon.green {
      background: var(--green);
    }

    .doc-icon svg {
      width: 16px;
      height: 16px;
      color: #fff;
    }

    .doc-title {
      font-size: .9375rem;
      font-weight: 700;
      color: var(--text);
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .doc-subtitle {
      font-size: .75rem;
      color: var(--gray-500);
      margin-top: 2px;
    }

    .badge-mandatory {
      padding: 2px 8px;
      border-radius: 999px;
      background: var(--red-light);
      color: var(--red);
      font-size: .6875rem;
      font-weight: 700;
    }

    .doc-head-right {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .doc-check {
      color: var(--green);
    }

    .doc-check svg {
      width: 20px;
      height: 20px;
    }

    /* ── UPLOAD ZONE ── */
    .upload-zone {
      padding: 24px 18px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: background .15s;
      gap: 8px;
    }


    .upload-zone-icon {
      width: 42px;
      height: 42px;
      border-radius: 50%;
      background: var(--blue-light);
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .upload-zone-icon svg {
      width: 18px;
      height: 18px;
      color: var(--blue);
    }

    .upload-zone-text {
      font-size: .875rem;
      font-weight: 600;
      color: var(--text);
    }

    .upload-zone-hint {
      font-size: .75rem;
      color: var(--gray-400);
    }

    /* ── SCAN BTN ── */
    .scan-btn-row {
      display: flex;
      gap: 10px;
      padding: 0 18px 14px;
    }

    .btn-scan {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      padding: 9px 16px;
      border-radius: 9px;
      border: 1.5px solid var(--blue-border);
      background: var(--blue-light);
      font-size: .8125rem;
      font-weight: 600;
      color: var(--blue);
      cursor: pointer;
      transition: all .2s;
      font-family: inherit;
    }

    .btn-scan:hover {
      background: var(--blue);
      color: #fff;
      border-color: var(--blue);
    }

    .btn-scan svg {
      width: 14px;
      height: 14px;
    }

    .scan-badge {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 2px 8px;
      border-radius: 999px;
      background: #ecfdf5;
      color: var(--green);
      font-size: .675rem;
      font-weight: 700;
      border: 1px solid var(--green-border);
    }

    .scan-badge svg {
      width: 9px;
      height: 9px;
    }

    /* ── UPLOADED FILE ── */
    .uploaded-file {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 10px 18px;
      background: #f0fdf4;
      border-top: 1px solid #dcfce7;
    }

    .uploaded-left {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .file-icon {
      width: 30px;
      height: 30px;
      border-radius: 8px;
      background: var(--green);
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .file-icon svg {
      width: 13px;
      height: 13px;
      color: #fff;
    }

    .file-name {
      font-size: .8125rem;
      font-weight: 600;
      color: var(--text);
    }

    .file-status {
      font-size: .725rem;
      color: var(--green);
    }

    .file-remove {
      background: none;
      border: none;
      cursor: pointer;
      color: var(--red);
      display: flex;
      align-items: center;
    }

    .file-remove svg {
      width: 15px;
      height: 15px;
    }

    /* ── DOC FORM ── */
    .doc-form {
      padding: 14px 18px 18px;
      border-top: 1px solid var(--gray-100);
    }

    .doc-form-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 12px;
    }

    .doc-form-title {
      font-size: .8125rem;
      font-weight: 700;
      color: var(--text);
    }

    .btn-edit {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 5px 12px;
      border-radius: 7px;
      border: 1.5px solid var(--blue-border);
      background: var(--blue-light);
      font-size: .75rem;
      font-weight: 600;
      color: var(--blue);
      cursor: pointer;
      transition: all .2s;
      font-family: inherit;
    }

    .btn-edit:hover {
      background: var(--blue);
      color: #fff;
      border-color: var(--blue);
    }

    .btn-edit svg {
      width: 12px;
      height: 12px;
    }

    .btn-save {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 5px 12px;
      border-radius: 7px;
      border: 1.5px solid var(--green-border);
      background: var(--green-light);
      font-size: .75rem;
      font-weight: 600;
      color: var(--green);
      cursor: pointer;
      transition: all .2s;
      font-family: inherit;
    }

    .btn-save:hover {
      background: var(--green);
      color: #fff;
      border-color: var(--green);
    }

    .btn-save svg {
      width: 12px;
      height: 12px;
    }

    /* ── AUTO-FILLED INDICATOR ── */
    .auto-filled-banner {
      display: flex;
      align-items: center;
      gap: 8px;
      background: #ecfdf5;
      border: 1px solid var(--green-border);
      border-radius: 8px;
      padding: 8px 12px;
      margin-bottom: 12px;
      font-size: .775rem;
      color: var(--green);
      font-weight: 600;
    }

    .auto-filled-banner svg {
      width: 14px;
      height: 14px;
      flex-shrink: 0;
    }

    .form-row {
      margin-bottom: 12px;
    }

    .form-label {
      display: block;
      font-size: .775rem;
      font-weight: 600;
      color: var(--gray-700);
      margin-bottom: 5px;
    }

    .form-label span {
      color: var(--red);
      margin-left: 2px;
    }

    .form-input {
      width: 100%;
      padding: 9px 12px;
      border: 1.5px solid var(--gray-200);
      border-radius: 8px;
      font-size: .875rem;
      font-family: inherit;
      color: var(--text);
      background: #fff;
      outline: none;
      transition: border-color .15s;
    }

    .form-input:focus {
      border-color: var(--blue);
      box-shadow: 0 0 0 3px rgba(26, 79, 214, .08);
    }

    .form-input:disabled {
      background: var(--gray-50);
      color: var(--gray-500);
      cursor: not-allowed;
    }

    .form-input.auto-filled {
      border-color: var(--green-border);
      background: #f0fdf4;
    }

    .form-select {
      width: 100%;
      padding: 9px 12px;
      border: 1.5px solid var(--gray-200);
      border-radius: 8px;
      font-size: .875rem;
      font-family: inherit;
      color: var(--text);
      background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E") no-repeat right 10px center;
      appearance: none;
      outline: none;
      cursor: pointer;
      transition: border-color .15s;
    }

    .form-select:focus {
      border-color: var(--blue);
      box-shadow: 0 0 0 3px rgba(26, 79, 214, .08);
    }

    .notes-box {
      background: var(--orange-light);
      border: 1px solid var(--orange-border);
      border-radius: 12px;
      padding: 14px 18px;
      margin-bottom: 28px;
    }

    .notes-head {
      display: flex;
      align-items: center;
      gap: 7px;
      font-size: .8125rem;
      font-weight: 700;
      color: var(--orange);
      margin-bottom: 8px;
    }

    .notes-head svg {
      width: 14px;
      height: 14px;
      color: var(--orange);
    }

    .notes-list {
      list-style: none;
      display: flex;
      flex-direction: column;
      gap: 5px;
    }

    .notes-list li {
      font-size: .775rem;
      color: #92400e;
      padding-left: 14px;
      position: relative;
      line-height: 1.4;
    }

    .notes-list li::before {
      content: '•';
      position: absolute;
      left: 0;
      color: var(--orange);
    }

    /* ================= SCREEN 5: REVIEW CSS ================= */
    .review-section {
      margin-bottom: 16px;
    }

    .review-card {
      background: #fff;
      border: 1.5px solid var(--gray-200);
      border-radius: var(--radius);
      overflow: hidden;
      box-shadow: var(--shadow-sm);
    }

    .review-card-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 14px 18px;
      border-bottom: 1px solid var(--gray-100);
    }

    .review-card-title {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: .9375rem;
      font-weight: 700;
      color: var(--text);
    }

    .review-card-title svg {
      width: 18px;
      height: 18px;
      color: var(--green);
    }

    .review-thumb {
      background: var(--gray-100);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
      text-align: center;
    }

    .review-thumb-inner {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 8px;
    }

    .review-thumb-inner svg {
      width: 32px;
      height: 32px;
      width: 48px;
      height: 48px;
      opacity: 0.3;
    }

    .review-thumb-label {
      font-size: 0.8rem;
      color: var(--gray-400);
      font-weight: 500;
    }

    .review-fields {
      padding: 20px;
      display: grid;
      gap: 16px;
    }

    .review-field-label {
      font-size: 0.75rem;
      font-weight: 700;
      color: var(--gray-500);
      text-transform: uppercase;
      margin-bottom: 4px;
    }

    .review-field-value {
      font-size: 0.95rem;
      font-weight: 600;
      color: var(--gray-800);
      background: #f8fafc;
      padding: 8px 12px;
      border: 1px solid #edf2f7;
      border-radius: 8px;
    }

    .review-field-value.placeholder {
      font-style: italic;
    }

    .review-docs-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-bottom: 24px;
    }

    /* Custom Image Match CSS */
    .selfie-row {
      display: flex;
      flex-direction: row;
      gap: 24px;
      align-items: flex-start;
    }

    @media (max-width: 600px) {
      .selfie-row {
        flex-direction: column;
        align-items: center;
      }
    }

    .selfie-col-left {
      width: 130px;
      display: flex;
      flex-direction: column;
      align-items: center;
      flex-shrink: 0;
    }

    .selfie-placeholder {
      width: 130px;
      height: 130px;
      background: #eff6ff;
      border: 1.5px solid #bfdbfe;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .selfie-lbl {
      font-size: 0.65rem;
      font-weight: 500;
      color: var(--gray-500);
      margin-top: 10px;
    }

    .selfie-col-right {
      flex: 1;
      width: 100%;
    }

    .selfie-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 20px;
    }

    .selfie-title {
      font-size: 0.95rem;
      font-weight: 700;
      color: var(--gray-800);
    }

    .selfie-success-box {
      background: #f8fafc;
      border-radius: 12px;
      padding: 16px;
    }

    .ssb-title {
      font-size: 0.85rem;
      font-weight: 600;
      color: var(--gray-800);
    }

    .ssb-desc {
      font-size: 0.75rem;
      color: var(--gray-500);
      margin-top: 6px;
    }

    .doc-preview-card {
      padding: 24px;
    }

    .dpc-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 24px;
    }

    .dpc-head-left {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .dpc-icon-box {
      width: 32px;
      height: 32px;
      border-radius: 8px;
      background: #dcfce7;
      color: #16a34a;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .dpc-icon-box svg {
      width: 16px;
      height: 16px;
    }

    .dpc-title {
      font-size: 0.85rem;
      font-weight: 700;
      color: var(--gray-800);
    }

    .dpc-check {
      color: var(--green);
    }

    .dpc-check svg {
      width: 20px;
      height: 20px;
    }

    .dpc-thumb {
      width: 100%;
      height: 180px;
      background: #f0f7ff;
      border: 1px solid #e0effe;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 8px;
    }

    .dpc-thumb svg {
      width: 40px;
      height: 40px;
      color: #93c5fd;
    }

    .dpc-thumb-lbl {
      text-align: center;
      font-size: 0.65rem;
      font-weight: 500;
      color: #9ca3af;
      margin-bottom: 24px;
    }

    .dpc-fields {
      display: flex;
      flex-direction: column;
      gap: 14px;
    }

    .dpc-dl {
      display: flex;
      flex-direction: column;
      gap: 4px;
    }

    .dpc-lbl {
      font-size: 0.65rem;
      font-weight: 700;
      color: var(--gray-800);
    }

    .dpc-val {
      font-size: 0.7rem;
      color: #9ca3af;
      background: #fff;
      border: 1px solid var(--gray-100);
      border-radius: 6px;
      padding: 6px 10px;
    }

    .info-box-new {
      background: #eff6ff;
      border: 1px solid #bfdbfe;
      border-radius: 12px;
      padding: 18px 24px;
      margin-bottom: 30px;
      display: flex;
      gap: 14px;
    }

    .info-box-new svg {
      width: 20px;
      height: 20px;
      color: #2563eb;
      flex-shrink: 0;
      margin-top: 2px;
    }

    .info-box-content-new {
      flex: 1;
    }

    .info-box-title-new {
      font-size: .875rem;
      font-weight: 700;
      color: #1e40af;
      margin-bottom: 6px;
    }

    .info-box-text-new {
      font-size: .775rem;
      color: #2563eb;
      line-height: 1.5;
    }

    /* ── SUCCESS ── */
    .success-card {
      background: #fff;
      border: 1px solid var(--gray-200);
      border-radius: 20px;
      padding: 56px 40px;
      text-align: center;
      box-shadow: var(--shadow);
    }

    .success-icon {
      width: 72px;
      height: 72px;
      border-radius: 50%;
      background: var(--blue);
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 24px;
      box-shadow: 0 8px 24px rgba(26, 79, 214, .35);
    }

    .success-icon svg {
      width: 34px;
      height: 34px;
      color: #fff;
    }

    .success-note {
      background: var(--gray-100);
      border-radius: 10px;
      padding: 14px 18px;
      margin-top: 20px;
      font-size: .8125rem;
      color: var(--gray-600);
      line-height: 1.6;
    }

    .success-note strong {
      color: var(--text);
    }

    /* ── MODAL ── */
    .modal-overlay {
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, .55);
      backdrop-filter: blur(4px);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 9999;
      padding: 16px;
    }

    .modal-overlay.open {
      display: flex;
    }

    .modal {
      background: #fff;
      border-radius: 18px;
      padding: 28px;
      max-width: 480px;
      width: 100%;
      position: relative;
      box-shadow: var(--shadow-lg);
      animation: fadeUp .25s ease;
    }

    .modal-close {
      position: absolute;
      top: 16px;
      right: 16px;
      width: 30px;
      height: 30px;
      border-radius: 50%;
      border: none;
      background: var(--gray-100);
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--gray-500);
      transition: background .15s;
    }

    .modal-close:hover {
      background: var(--gray-200);
    }

    .modal-header {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 20px;
    }

    .digi-logo {
      width: 44px;
      height: 44px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      background: none !important;
      box-shadow: none !important;
    }

    .digi-logo svg {
      filter: none !important;
    }

    .modal-title {
      font-size: 1.0625rem;
      font-weight: 800;
      color: var(--text);
    }

    .modal-subtitle {
      font-size: .8rem;
      color: var(--gray-500);
      margin-top: 2px;
    }

    .digi-info-box {
      background: var(--blue-light);
      border-radius: 10px;
      padding: 16px;
      margin-bottom: 14px;
    }

    .digi-info-title {
      font-size: .875rem;
      font-weight: 700;
      color: var(--text);
      margin-bottom: 8px;
    }

    .digi-info-desc {
      font-size: .8rem;
      color: var(--gray-600);
      line-height: 1.5;
      margin-bottom: 12px;
    }

    .digi-check-list {
      display: flex;
      flex-direction: column;
      gap: 7px;
    }

    .digi-check-item {
      display: flex;
      align-items: flex-start;
      gap: 8px;
      font-size: .8rem;
      color: var(--gray-600);
    }

    .digi-check-item svg {
      width: 13px;
      height: 13px;
      color: var(--green);
      flex-shrink: 0;
      margin-top: 2px;
    }

    .digi-redirect-box {
      background: #eff6ff;
      border: 1px solid #bfdbfe;
      border-radius: 10px;
      padding: 12px 14px;
      margin-bottom: 22px;
    }

    .digi-redirect-title {
      font-size: .8125rem;
      font-weight: 700;
      color: var(--blue);
      margin-bottom: 4px;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .digi-redirect-title svg {
      width: 13px;
      height: 13px;
    }

    .digi-redirect-text {
      font-size: .775rem;
      color: var(--gray-600);
      line-height: 1.4;
    }

    /* ── BUTTONS ── */
    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      padding: 0 22px;
      height: 44px;
      border-radius: 10px;
      font-weight: 600;
      font-size: .875rem;
      cursor: pointer;
      border: none;
      transition: all .2s;
      font-family: inherit;
    }

    .btn-primary {
      background: var(--blue);
      color: #fff;
      box-shadow: 0 3px 10px rgba(26, 79, 214, .3);
    }

    .btn-primary:hover {
      background: #163fb5;
      transform: translateY(-1px);
      box-shadow: 0 5px 14px rgba(26, 79, 214, .35);
    }

    .btn-primary:disabled {
      background: var(--gray-200);
      color: var(--gray-400);
      box-shadow: none;
      cursor: not-allowed;
      transform: none;
    }

    .btn-outline {
      background: #fff;
      color: var(--text);
      border: 1.5px solid var(--gray-200);
    }

    .btn-outline:hover {
      border-color: var(--blue);
      color: var(--blue);
    }

    .action-row {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(15px);
      padding: 16px 0;
      border-top: 1px solid var(--gray-100);
      z-index: 2000;
      box-shadow: 0 -8px 30px rgba(0, 0, 0, 0.04);
    }

    .action-row-inner {
      width: 100%;
      max-width: 800px;
      margin: 0 auto;
      padding: 0 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 16px;
    }

    .action-row.end .action-row-inner {
      justify-content: flex-end;
    }

    .action-row.center .action-row-inner {
      justify-content: center;
    }

    /* Padding for screens to not be hidden by fixed action bar */
    .screen {
      padding-bottom: 120px;
    }

    @media (max-width: 600px) {
      body {
        padding: 20px 0 48px;
      }

      .action-row-inner {
        padding: 0 16px;
        flex-direction: column-reverse;
        gap: 10px;
      }

      .page-title {
        font-size: 1.5rem;
        padding: 0 16px;
      }

      .page-sub {
        padding: 0 16px;
      }

      .entity-grid,
      .upload-options,
      .req-grid,
      /* grid handled by @media */

      .selfie-layout {
        grid-template-columns: 1fr;
      }

      .step-line {
        max-width: 32px;
        margin: 0 2px;
      }

      .step {
        font-size: 0;
      }

      .step-circle {
        width: 28px;
        height: 28px;
        font-size: .7rem;
      }

      .stepper {
        margin-bottom: 24px;
      }

      .btn {
        width: 100%;
        font-size: .8rem;
        padding: 0 12px;
      }

      .success-card {
        padding: 40px 24px;
      }

      .modal {
        padding: 22px 18px;
      }

      .review-fields {
        grid-template-columns: 1fr;
      }

      #toast-container {
        top: 12px;
      }

      .screen {
        padding-bottom: 140px;
        /* Space for stacked buttons on mobile */
      }
    }

    @media (max-width: 600px) {
      .review-grid, .review-docs-grid {
        grid-template-columns: 1fr 1fr !important;
        gap: 10px !important;
      }
      .review-card {
        padding: 12px !important;
      }
      .review-card-head,
      .dpc-head {
        justify-content: center !important;
        flex-direction: column !important;
        gap: 12px !important;
        margin-bottom: 20px !important;
      }

      .review-doc-title,
      .dpc-head-left,
      .dpc-title {
        font-size: 0.85rem !important;
        gap: 8px !important;
        justify-content: center !important;
        flex-direction: column !important;
        align-items: center !important;
        width: 100% !important;
      }

      .review-doc-icon,
      .dpc-icon-box {
        width: 32px !important;
        height: 32px !important;
        flex-shrink: 0;
        margin: 0 auto !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
      }

      .dpc-check {
        position: absolute;
        top: 12px;
        right: 12px;
      }
      .review-row, .dpc-dl {
        flex-direction: column !important;
        align-items: center !important;
        gap: 4px !important;
        display: flex !important;
        text-align: center !important;
      }
      .review-label, .dpc-lbl {
        font-size: 0.65rem !important;
      }
      .review-value {
        padding: 6px 8px !important;
        font-size: 0.75rem !important;
        border-radius: 8px !important;
        width: 100% !important;
        background: #f8fafc !important;
        border: 1.5px solid #edf2f7 !important;
        text-align: center !important;
        word-break: break-all;
      }

      .selfie-hero {
        flex-direction: row !important;
        gap: 12px !important;
        padding: 12px !important;
        align-items: center !important;
      }
      .review-img-box {
        width: 80px !important;
        height: 80px !important;
        margin: 0 auto 12px !important;
      }
      .selfie-hero-left {
        width: 80px !important;
        margin-bottom: 0 !important;
      }
      .selfie-hero-title {
        font-size: 0.9rem !important;
        margin-bottom: 4px !important;
      }
      .selfie-status-title {
        font-size: 0.85rem !important;
        white-space: nowrap !important;
        margin-bottom: 2px !important;
      }
      .selfie-status-text {
        font-size: 0.65rem !important;
      }
      .success-card {
        padding: 40px 20px;
      }
      .success-icon-circle {
        width: 80px !important;
        height: 80px !important;
      }
      .success-icon-circle svg {
        width: 40px !important;
        height: 40px !important;
      }
      .success-card .page-title {
        font-size: 1.5rem !important;
      }
    }

    @media (min-width: 1024px) {
      .selfie-layout {
        grid-template-columns: 1fr 1.25fr;
      }
    }
  </style>
</head>

<body>

  <!-- ── TOAST CONTAINER ── -->
  <div id="toast-container"></div>

  <!-- ── SCANNING OVERLAY ── -->
  <div class="scan-overlay" id="scanOverlay">
    <div class="scan-box">
      <div class="scan-corner tl "></div>
      <div class="scan-corner tr"></div>
      <div class="scan-corner bl"></div>
      <div class="scan-corner br"></div>
      <div class="scan-line"></div>
    </div>
  </div>

  <div class="kyc-wrap">
    <button class="back-btn" onclick="history.back()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <path d="M19 12H5M12 5l-7 7 7 7" />
      </svg>
      Back

    </button>

    <!-- STEPPER -->
    <div class="stepper" id="stepper">
      <div class="step active" id="step1">
        <div class="step-circle" id="sc1">1</div>
        <span>Entity Type</span>
      </div>
      <div class="step-line" id="line1"></div>
      <div class="step" id="step2">
        <div class="step-circle" id="sc2">2</div>
        <span id="step2label">Take Photo</span>
      </div>
      <div class="step-line" id="line2"></div>
      <div class="step" id="step3">
        <div class="step-circle" id="sc3">3</div>
        <span>Documents</span>
      </div>
    </div>


    <!-- ================= SCREEN 1: ENTITY SELECTION LAYOUT ================= -->
    <div class="screen active" id="screen-entity">
      <h1 class="page-title">How do you operate your business?</h1>
      <p class="page-sub">Select the option that best describes your business structure. This helps us set up the right verification flow for you.</p>
      <div class="entity-grid">
        <div class="entity-card" onclick="selectEntity(this,'individual')">
          <div class="entity-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="8" r="4" />
              <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7" />
            </svg></div>
          <div class="entity-name">Individual</div>
          <div class="entity-desc">You run the business yourself with no separate legal entity. Most common for freelancers, small traders, and home sellers.</div>
        </div>
        <div class="entity-card" onclick="selectEntity(this,'sole_proprietor')">
          <div class="entity-icon"><svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M11.8008 43.2685V7.8653C11.8008 6.82202 12.2152 5.82147 12.9529 5.08376C13.6906 4.34605 14.6912 3.93161 15.7345 3.93161H31.4692C32.5125 3.93161 33.513 4.34605 34.2507 5.08376C34.9885 5.82147 35.4029 6.82202 35.4029 7.8653V43.2685H11.8008Z" stroke="currentColor" stroke-width="3.93369" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M11.801 23.5979H7.86728C6.824 23.5979 5.82345 24.0124 5.08574 24.7501C4.34803 25.4878 3.93359 26.4883 3.93359 27.5316V39.3327C3.93359 40.376 4.34803 41.3765 5.08574 42.1142C5.82345 42.8519 6.824 43.2664 7.86728 43.2664H11.801" stroke="currentColor" stroke-width="3.93369" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M35.4033 17.6993H39.337C40.3803 17.6993 41.3808 18.1137 42.1185 18.8514C42.8563 19.5891 43.2707 20.5897 43.2707 21.633V39.3346C43.2707 40.3778 42.8563 41.3784 42.1185 42.1161C41.3808 42.8538 40.3803 43.2682 39.337 43.2682H35.4033" stroke="currentColor" stroke-width="3.93369" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M19.668 11.8006H27.5353" stroke="currentColor" stroke-width="3.93369" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M19.668 19.6655H27.5353" stroke="currentColor" stroke-width="3.93369" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M19.668 27.5345H27.5353" stroke="currentColor" stroke-width="3.93369" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M19.668 35.3994H27.5353" stroke="currentColor" stroke-width="3.93369" stroke-linecap="round" stroke-linejoin="round" />
            </svg></div>
          <div class="entity-name">Sole Proprietor</div>
          <div class="entity-desc">You own and run a business in your own name like a local shop or a self-employed trade. No co-owners, no separate legal entity.</div>
        </div>
        <div class="entity-card" onclick="selectEntity(this,'partnership')">
          <div class="entity-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="9" cy="7" r="3" />
              <circle cx="15" cy="7" r="3" />
              <path d="M3 21c0-3.3 2.7-6 6-6h6c3.3 0 6 2.7 6 6" />
            </svg></div>
          <div class="entity-name">Partnership</div>
          <div class="entity-desc">Two or more co-owners sharing profits and liabilities based on a formal partnership agreement or deed.</div>
        </div>
        <div class="entity-card" onclick="selectEntity(this,'company')">
          <div class="entity-icon"><svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M35.403 3.93463H11.8009C9.62836 3.93463 7.86719 5.6958 7.86719 7.86832V39.3378C7.86719 41.5103 9.62836 43.2715 11.8009 43.2715H35.403C37.5755 43.2715 39.3367 41.5103 39.3367 39.3378V7.86832C39.3367 5.6958 37.5755 3.93463 35.403 3.93463Z" stroke="currentColor" stroke-width="3.93369" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M17.7012 43.2697V35.4024H29.5022V43.2697" stroke="currentColor" stroke-width="3.93369" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M15.7344 11.7995H15.7537" stroke="currentColor" stroke-width="3.93369" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M31.4697 11.7995H31.489" stroke="currentColor" stroke-width="3.93369" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M23.6025 11.7995H23.6218" stroke="currentColor" stroke-width="3.93369" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M23.6025 19.6644H23.6218" stroke="currentColor" stroke-width="3.93369" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M23.6025 27.5375H23.6218" stroke="currentColor" stroke-width="3.93369" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M31.4697 19.6644H31.489" stroke="currentColor" stroke-width="3.93369" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M31.4697 27.5375H31.489" stroke="currentColor" stroke-width="3.93369" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M15.7344 19.6644H15.7537" stroke="currentColor" stroke-width="3.93369" stroke-linecap="round" stroke-linejoin="round" />
              <path d="M15.7344 27.5375H15.7537" stroke="currentColor" stroke-width="3.93369" stroke-linecap="round" stroke-linejoin="round" />
            </svg></div>
          <div class="entity-name">Company</div>
          <div class="entity-desc">A formally registered business — Private Limited, LLP, or Public Limited. Has its own legal identity, separate from its owners or directors.
          </div>
        </div>
      </div>
      <div class="action-row end" id="entityNextRow" style="display: none;">
        <div class="action-row-inner">
          <button class="btn btn-primary" id="entityNextBtn" onclick="goTo('screen-selfie')">Next Step →</button>
        </div>
      </div>
    </div>


    <!-- ================= SCREEN 2: SELFIE VERIFICATION LAYOUT ================= -->
    <div class="screen" id="screen-selfie">
      <h1 class="page-title">Verify your Identity.</h1>
      <p class="page-sub">We need to verify your identity to protect your account. This takes about 30 seconds.</p>
      <div class="selfie-layout">
        <div class="camera-wrapper">
          <div class="camera-box" id="cameraBox">
            <div class="cam-circle" id="camCircle">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <rect x="3" y="8" width="18" height="12" rx="2"></rect>
                <path d="M7 8v-2a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v2"></path>
                <circle cx="12" cy="14" r="3"></circle>
              </svg>
            </div>
            <div class="cam-label" id="camLabel">Position your face within the frame</div>
          </div>
          <div class="selfie-btn-row">
            <button class="btn btn-primary" id="captureBtn" style="flex:1" onclick="takePhoto()">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z" />
                <circle cx="12" cy="13" r="4" />
              </svg>
              Take Photo
            </button>
            <button class="btn btn-outline" id="selectBtn" style="flex:1"
              onclick="document.getElementById('selfieInput').click()">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                <polyline points="17 8 12 3 7 8" />
                <line x1="12" y1="3" x2="12" y2="15" />
              </svg>
              Select Image
            </button>
            <input type="file" id="selfieInput" style="display:none" accept="image/*"
              onchange="handleSelfieSelect(this)">
            <div class="btn-cam-icon" title="Switch camera">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 4v6h6" />
                <path d="M23 20v-6h-6" />
                <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4-4.64 4.36A9 9 0 0 1 3.51 15" />
              </svg>
            </div>
          </div>
        </div>
        <div class="ins-wrapper">
          <div class="ins-box">
            <div class="ins-section">
              <div class="ins-head">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                  <polyline points="14 2 14 8 20 8"></polyline>
                  <line x1="16" y1="13" x2="8" y2="13"></line>
                  <line x1="16" y1="17" x2="8" y2="17"></line>
                  <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
                Instructions for a clear selfie:
              </div>
              <ul class="ins-list">
                <li>Ensure your face is clearly visible and well-lit</li>
                <li>Remove glasses, hats, or any face coverings</li>
                <li>Look directly at the camera with a neutral expression</li>
                <li>Make sure the background is plain and uncluttered</li>
              </ul>
            </div>
            <div class="ins-section">
              <div class="ins-head">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                  <polyline points="14 2 14 8 20 8"></polyline>
                  <line x1="16" y1="13" x2="8" y2="13"></line>
                  <line x1="16" y1="17" x2="8" y2="17"></line>
                  <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
                Please Avoid
              </div>
              <ul class="ins-list">
                <li>Beauty filters or edited photos</li>
                <li>Photos saved from your gallery</li>
              </ul>
            </div>
          </div>
          <div style="display:flex;">
            <div id="verifiedTag" style="display:none;"></div>
          </div>
        </div>
      </div>
      <div class="action-row">
        <div class="action-row-inner">
          <button class="btn btn-outline" onclick="goTo('screen-entity')">Back</button>
          <button class="btn btn-outline" onclick="goTo('screen-upload')">Next</button>
        </div>
      </div>
    </div>


    <!-- ================= SCREEN 3: UPLOAD OPTIONS LAYOUT ================= -->
    <div class="screen" id="screen-upload">
      <h1 class="page-title">Upload Documents</h1>
      <p class="page-sub">Choose how you'd like to upload your verification documents</p>
      <div class="upload-options">
        <div class="upload-opt selected" id="opt-instant" onclick="selectUpload('instant')">
          <div class="upl-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
              <polyline points="15,3 21,3 21,9" />
              <line x1="10" y1="14" x2="21" y2="3" />
            </svg></div>
          <div class="upl-name">Instant KYC</div>
          <div class="upl-desc">Instantly fetch your documents from DigiLocker – fast and secure</div>
          <div class="badge-row"><span class="badge rec">Recommended</span><span class="badge muted">2 mins</span></div>
        </div>
        <div class="upload-opt" id="opt-manual" onclick="selectUpload('manual')">
          <div class="upl-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="16,16 12,12 8,16" />
              <line x1="12" y1="12" x2="12" y2="21" />
              <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3" />
            </svg></div>
          <div class="upl-name">Manual Upload</div>
          <div class="upl-desc">Upload scanned copies or photos of your documents manually</div>
          <div class="badge-row"><span class="badge muted">Traditional</span><span class="badge muted">5-10 mins</span>
          </div>
        </div>
      </div>
      <div class="req-docs-box">
        <div class="req-head">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
            <polyline points="14,2 14,8 20,8" />
          </svg>
          Required Documents for <span id="entityLabel" style="margin-left:4px;">Individual</span>:
        </div>
        <div class="req-grid">
          <div class="req-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M20 6L9 17l-5-5" />
            </svg>PAN Card</div>
          <div class="req-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M20 6L9 17l-5-5" />
            </svg>Aadhaar Card</div>
          <div class="req-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M20 6L9 17l-5-5" />
            </svg>Driving License</div>
          <div class="req-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M20 6L9 17l-5-5" />
            </svg>Passport</div>
        </div>
      </div>
      <div class="action-row">
        <div class="action-row-inner">
          <button class="btn btn-outline" onclick="goTo('screen-selfie')">Back</button>
          <button class="btn btn-primary" onclick="proceedUpload()">Continue</button>
        </div>
      </div>
    </div>


    <div class="screen" id="screen-manual">
      <div class="manual-container">
        <h1 class="page-title">Upload Your Documents</h1>
        <p class="page-sub">Please upload clear photos or scans and fill in the document details</p>

        <div class="doc-card" id="panCard">
          <div class="doc-card-head">
            <div class="doc-head-left">
              <div class="doc-icon" id="panIconEl"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                  stroke-width="2">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                  <polyline points="14,2 14,8 20,8" />
                </svg></div>
              <div>
                <div class="doc-title">Document 1: PAN Card <span class="badge-mandatory">Mandatory</span></div>
                <div class="doc-subtitle">Permanent Account Number card issued by Income Tax Department</div>
              </div>
            </div>
            <div id="panCheck" style="display:none;" class="doc-check"><svg viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2.5">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                <polyline points="22,4 12,14.01 9,11.01" />
              </svg></div>
          </div>

          <div id="panUploadZone">
            <div class="upload-zone" onclick="document.getElementById('panFileInput').click()">
              <div class="upload-zone-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="16,16 12,12 8,16" />
                  <line x1="12" y1="12" x2="12" y2="21" />
                  <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3" />
                </svg></div>
              <div class="upload-zone-text" id="panUploadLabel">Click to upload PAN Card</div>
              <div class="upload-zone-hint">PNG, JPG or PDF (max. 5MB)</div>
            </div>
            <input type="file" id="panFileInput" style="display:none" accept="image/*,.pdf"
              onchange="handleUIUpload(this, 'pan')">
          </div>

          <div id="panFileRow"></div>

          <div id="panForm" style="display:none;">
            <div class="doc-form">
              <div class="doc-form-header">
                <div class="doc-form-title" id="panFormTitle">PAN Card Details</div>
              </div>
              <div id="panDynamicFields">
                <div class="form-row"><label class="form-label">PAN Number <span>*</span></label><input
                    class="form-input" type="text" id="panNumber" placeholder="ABCDE1234F" oninput="checkManualReady()">
                </div>
                <div class="form-row" style="margin-bottom:0;"><label class="form-label">Name as per PAN
                    <span>*</span></label><input class="form-input" type="text" id="panName"
                    placeholder="Enter full name" oninput="checkManualReady()"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="doc-card" id="cinCard">
          <div class="doc-card-head">
            <div class="doc-head-left">
              <div class="doc-icon" id="cinIconEl"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                  stroke-width="2">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                  <polyline points="14,2 14,8 20,8" />
                  <line x1="16" y1="13" x2="8" y2="13" />
                  <line x1="16" y1="17" x2="8" y2="17" />
                  <polyline points="10 9 9 9 8 9" />
                </svg></div>
              <div>
                <div class="doc-title">Document 2: CIN / Registration <span class="badge-mandatory">Mandatory</span>
                </div>
                <div class="doc-subtitle">Official Registration Document</div>
              </div>
            </div>
            <div id="cinCheck" style="display:none;" class="doc-check"><svg viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2.5">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                <polyline points="22,4 12,14.01 9,11.01" />
              </svg></div>
          </div>

          <div id="cinUploadZone">
            <div class="upload-zone" onclick="document.getElementById('cinFileInput').click()">
              <div class="upload-zone-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="16,16 12,12 8,16" />
                  <line x1="12" y1="12" x2="12" y2="21" />
                  <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3" />
                </svg></div>
              <div class="upload-zone-text" id="cinUploadLabel">Click to upload CIN / Registration</div>
              <div class="upload-zone-hint">PNG, JPG or PDF (max. 5MB)</div>
            </div>
            <input type="file" id="cinFileInput" style="display:none" accept="image/*,.pdf"
              onchange="handleUIUpload(this, 'cin')">
          </div>

          <div id="cinFileRow"></div>

          <div id="cinForm" style="display:none;">
            <div class="doc-form">
              <div class="doc-form-header">
                <div class="doc-form-title" id="cinFormTitle">CIN / Registration Details</div>
              </div>
              <div id="cinDynamicFields">
                <div class="form-row"><label class="form-label">CIN / Registration Number <span>*</span></label><input
                    class="form-input" type="text" id="cinNum" placeholder="U12345KA2023PTC123456"
                    oninput="checkManualReady()"></div>
                <div class="form-row"><label class="form-label">Entity Name <span>*</span></label><input
                    class="form-input" type="text" id="cinName" placeholder="Enter registered company name"
                    oninput="checkManualReady()"></div>
                <div class="form-row" style="margin-bottom:0;"><label class="form-label">Date of Incorporation
                    <span>*</span></label><input class="form-input" type="date" id="cinDate"
                    oninput="checkManualReady()"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="doc-card" id="gstCard">
          <div class="doc-card-head">
            <div class="doc-head-left">
              <div class="doc-icon" id="gstIconEl"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                  stroke-width="2">
                  <line x1="12" y1="1" x2="12" y2="23" />
                  <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                </svg></div>
              <div>
                <div class="doc-title">Document 3: GST Registration <span class="badge-mandatory">Mandatory</span></div>
                <div class="doc-subtitle">Goods and Services Tax Registration Certificate</div>
              </div>
            </div>
            <div id="gstCheck" style="display:none;" class="doc-check"><svg viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2.5">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                <polyline points="22,4 12,14.01 9,11.01" />
              </svg></div>
          </div>

          <div id="gstUploadZone">
            <div class="upload-zone" onclick="document.getElementById('gstFileInput').click()">
              <div class="upload-zone-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="16,16 12,12 8,16" />
                  <line x1="12" y1="12" x2="12" y2="21" />
                  <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3" />
                </svg></div>
              <div class="upload-zone-text" id="gstUploadLabel">Click to upload GST Registration</div>
              <div class="upload-zone-hint">PNG, JPG or PDF (max. 5MB)</div>
            </div>
            <input type="file" id="gstFileInput" style="display:none" accept="image/*,.pdf"
              onchange="handleUIUpload(this, 'gst')">
          </div>

          <div id="gstFileRow"></div>

          <div id="gstForm" style="display:none;">
            <div class="doc-form">
              <div class="doc-form-header">
                <div class="doc-form-title" id="gstFormTitle">GST Registration Details</div>
              </div>
              <div id="gstDynamicFields">
                <div class="form-row"><label class="form-label">GSTIN <span>*</span></label><input
                    class="form-input" type="text" id="gstNumber" placeholder="22AAAAA0000A1Z5"
                    oninput="checkManualReady()"></div>
                <div class="form-row" style="margin-bottom:0;"><label class="form-label">Legal Name
                    <span>*</span></label><input class="form-input" type="text" id="gstName"
                    placeholder="Enter legal name" oninput="checkManualReady()"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="doc-card" id="deedCard">
          <div class="doc-card-head">
            <div class="doc-head-left">
              <div class="doc-icon" id="deedIconEl"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                  stroke-width="2">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                  <polyline points="14,2 14,8 20,8" />
                  <line x1="16" y1="13" x2="8" y2="13" />
                  <line x1="16" y1="17" x2="8" y2="17" />
                  <polyline points="10 9 9 9 8 9" />
                </svg></div>
              <div>
                <div class="doc-title">Document 4: Partnership Deed <span class="badge-mandatory">Mandatory</span></div>
                <div class="doc-subtitle">Official Partnership Registration Document</div>
              </div>
            </div>
            <div id="deedCheck" style="display:none;" class="doc-check"><svg viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2.5">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                <polyline points="22,4 12,14.01 9,11.01" />
              </svg></div>
          </div>

          <div id="deedUploadZone">
            <div class="upload-zone" onclick="document.getElementById('deedFileInput').click()">
              <div class="upload-zone-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="16,16 12,12 8,16" />
                  <line x1="12" y1="12" x2="12" y2="21" />
                  <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3" />
                </svg></div>
              <div class="upload-zone-text" id="deedUploadLabel">Click to upload Partnership Deed</div>
              <div class="upload-zone-hint">PNG, JPG or PDF (max. 5MB)</div>
            </div>
            <input type="file" id="deedFileInput" style="display:none" accept="image/*,.pdf"
              onchange="handleUIUpload(this, 'deed')">
          </div>

          <div id="deedFileRow"></div>

          <div id="deedForm" style="display:none;">
            <div class="doc-form">
              <div class="doc-form-header">
                <div class="doc-form-title" id="deedFormTitle">Partnership Deed Details</div>
              </div>
              <div id="deedDynamicFields">
                <div class="form-row" style="margin-bottom:0;"><label class="form-label">Deed ID / Registration Number
                    <span>*</span></label><input class="form-input" type="text" id="deedNum"
                    placeholder="Enter Deed Registration Number" oninput="checkManualReady()"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="notes-box">
          <div class="notes-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <circle cx="12" cy="12" r="10" />
              <line x1="12" y1="8" x2="12" y2="12" />
              <line x1="12" y1="16" x2="12.01" y2="16" />
            </svg>
          </div>
          <div class="notes-content">
            <div class="notes-title">Important Notes:</div>
            <ul class="notes-list">
              <li>Ensure documents are clear and all corners are visible</li>
              <li>File size should not exceed 5MB per document</li>
              <li>Accepted formats: PNG, JPG, JPEG, PDF</li>
              <li>Fill all mandatory fields for each uploaded document</li>
              <li>Documents will be verified within 24-48 hours</li>
            </ul>
          </div>
        </div>

      </div>
      <div class="action-row" style="margin-top: 32px; text-align:center;">
        <button class="btn btn-outline" style="margin-right:12px;" onclick="goTo('screen-upload')">Back to Upload
          Options</button>
        <button class="btn btn-primary" id="submitManualBtn" onclick="buildReview(); goTo('screen-review')">Upload
          required document to continue</button>
      </div>
    </div>

    <!-- ================= SCREEN 4: REVIEW ================= -->
    <div class="screen" id="screen-review">
      <h1 class="page-title">Verify Your Information</h1>
      <p class="page-sub">Review your documents and confirm the details</p>

      <div id="reviewDocsGrid">
        <!-- JS injects here -->
      </div>

      <div class="review-banner">
        <div class="review-banner-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="20" height="20">
            <circle cx="12" cy="12" r="10" />
            <line x1="12" y1="8" x2="12" y2="12" />
            <line x1="12" y1="16" x2="12.01" y2="16" />
          </svg>
        </div>
        <div class="review-banner-text">
          <strong>Review Your Information</strong><br>
          Please ensure all the information is correct. You can go back to edit if needed. Once submitted, your
          documents will be verified within 24-48 hours.
        </div>
      </div>

      <div class="action-row center">
        <div class="action-row-inner">
          <button class="btn btn-outline" onclick="goTo('screen-manual')">
            Back to Documents
          </button>
          <button class="btn btn-primary" onclick="submitKYC()">Complete KYC</button>
        </div>
      </div>
    </div>

    <!-- ================= SCREEN 5: SUCCESS ================= -->
    <div class="screen" id="screen-success">
      <div class="success-card" style="max-width:700px; margin:0 auto;">
        <div class="success-icon-circle" style="width: 100px; height: 100px; margin: 0 auto 32px; background:var(--blue); color:#fff; display:flex; align-items:center; justify-content:center; border-radius:50%; box-shadow: 0 8px 20px rgba(26, 79, 214, 0.25);">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" width="52" height="52">
            <path d="M20 6L9 17l-5-5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <h1 class="page-title" style="text-align: center; margin-bottom: 16px; font-size: 2rem;">KYC Submitted Successfully!</h1>
        <p class="page-sub" style="text-align: center; font-size: 1.125rem; color: #64748b;">Your documents are being verified.</p>
        <div style="font-size:1.15rem; font-weight:600; text-align: center; color: var(--gray-800); margin:32px 0 16px;">Do you want to proceed with Bank Account Verification?</div>

        <div style="background: #f8fafc; padding: 20px; border-radius: 12px; margin-bottom: 40px; border: 1.5px solid #edf2f7; display:inline-block; width:100%;">
          <p style="text-align: center; margin:0; font-size:15px; color: #475569; line-height:1.6;">
            Verification typically takes <strong style="color:var(--gray-900);">24-48 hours</strong>. You'll receive an email notification once completed.
          </p>
        </div>

        <div style="display: flex; gap: 16px; flex-wrap: wrap; justify-content: center;">
          <button class="btn btn-outline" style="flex: 1; min-width: 200px;" onclick="window.location.href='<?php echo base_url('dashboard') ?>'">Skip for Now</button>
          <button class="btn btn-primary" style="flex: 1; min-width: 200px;" onclick="openBankModal()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
              <rect x="2" y="5" width="20" height="14" rx="2" /><line x1="2" y1="10" x2="22" y2="10" />
            </svg>
            Add Bank Details
          </button>
        </div>
      </div>
    </div>
  </div>
  <!-- ── DIGILOCKER MODAL ── -->
  <div class="modal-overlay" id="digiModal">
    <div class="modal">
      <button class="modal-close" onclick="closeModal()">✕</button>
      <div class="modal-header">
        <div class="digi-logo">
          <svg width="41" height="46" viewBox="0 0 41 46" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
<rect width="41" height="46" rx="8" fill="url(#pattern0_3311_8386)"/>
<defs>
<pattern id="pattern0_3311_8386" patternContentUnits="objectBoundingBox" width="1" height="1">
<use xlink:href="#image0_3311_8386" transform="matrix(0.00364583 0 0 0.00324074 -0.208333 -0.148148)"/>
</pattern>
<image id="image0_3311_8386" width="400" height="400" preserveAspectRatio="none" xlink:href="data:image/png;base64,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"/>
</defs>
</svg>

        </div>
        <div>
          <div class="modal-title">DigiLocker Authentication</div>
          <div class="modal-subtitle">Secure document verification</div>
        </div>
      </div>
      <div class="digi-info-box">
        <div class="digi-info-title">What is DigiLocker?</div>
        <div class="digi-info-desc">DigiLocker is a Government of India initiative that allows you to store and share
          your documents digitally. It's 100% secure and verified.</div>
        <div class="digi-check-list">
          <div class="digi-check-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M20 6L9 17l-5-5" />
            </svg>Instant verification – no manual upload needed</div>
          <div class="digi-check-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M20 6L9 17l-5-5" />
            </svg>Documents are fetched directly from government servers</div>
          <div class="digi-check-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M20 6L9 17l-5-5" />
            </svg>Secure and encrypted data transmission</div>
        </div>
      </div>
      <div class="digi-redirect-box">
        <div class="digi-redirect-title">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M11.9137 21.8418C17.397 21.8418 21.8421 17.3967 21.8421 11.9134C21.8421 6.43007 17.397 1.98499 11.9137 1.98499C6.43044 1.98499 1.98535 6.43007 1.98535 11.9134C1.98535 17.3967 6.43044 21.8418 11.9137 21.8418Z" stroke="#155DFC" stroke-width="1.98568" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M11.9141 7.94202V11.9134" stroke="#155DFC" stroke-width="1.98568" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M11.9141 15.8883H11.9237" stroke="#155DFC" stroke-width="1.98568" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          You will be redirected to DigiLocker
        </div>
        <div class="digi-redirect-text">Please log in with your Aadhaar number or mobile number linked to your Aadhaar.
        </div>
      </div>
      <div style="display:flex;gap:10px;">
        <button class="btn btn-outline" style="flex:1" onclick="closeModal()">Cancel</button>
        <button class="btn btn-primary" style="flex:1" onclick="digiDone()">Continue to DigiLocker</button>
      </div>
    </div>
  </div>

  <?php $this->load->view('billing/bank_details_modal'); ?>

  <script>
    let currentScreen = 'screen-entity';
    let selectedEntity = null;
    let uploadMethod = 'instant';
    let panUploaded = false;
    let cinUploaded = false;
    let gstUploaded = false;
    let deedUploaded = false;
    let panEditMode = true;
    let addrEditMode = true;
    let panScanned = false;
    let addrScanned = false;
    let capturedPhoto = null;
    const entityNames = { individual: 'Individual', sole_proprietor: 'Sole Proprietor', partnership: 'Partnership', company: 'Company' };

    /* ── TOAST ── */
    function showToast(type, title, msg) {
      const icons = {
        success: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>`,
        error: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>`,
        info: `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11.9137 21.8418C17.397 21.8418 21.8421 17.3967 21.8421 11.9134C21.8421 6.43007 17.397 1.98499 11.9137 1.98499C6.43044 1.98499 1.98535 6.43007 1.98535 11.9134C1.98535 17.3967 6.43044 21.8418 11.9137 21.8418Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M11.9141 7.94202V11.9134" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M11.9141 15.8883H11.9237" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>`,
        warning: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>`
      };
      const container = document.getElementById('toast-container');
      const t = document.createElement('div');
      t.className = `toast ${type}`;
      t.innerHTML = `
    <div class="toast-icon">${icons[type]}</div>
    <div class="toast-body">
      <div class="toast-title">${title}</div>
      <div class="toast-msg">${msg}</div>
    </div>
    <button class="toast-close" onclick="removeToast(this.parentElement)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="14" height="14"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>`;
      container.appendChild(t);

      // Animation: Spinner then Icon for success
      if (type === 'success') {
        const iconBox = t.querySelector('.toast-icon');
        const finalIcon = iconBox.innerHTML;
        iconBox.innerHTML = '<div class="toast-loader"></div>';
        setTimeout(() => {
          iconBox.innerHTML = finalIcon;
        }, 600);
      }

      setTimeout(() => removeToast(t), 5000);
    }

    function removeToast(el) {
      if (!el || el.classList.contains('hiding')) return;
      el.classList.add('hiding');
      setTimeout(() => el.remove(), 250);
    }

    /* ── SCAN ── */
    function startScan(type) {
      const overlay = document.getElementById('scanOverlay');
      overlay.classList.add('open');
      showToast('info', 'Scanning...', 'Please hold the document steady for best results.');
      setTimeout(() => {
        overlay.classList.remove('open');
        autoFillFromScan(type);
      }, 2200);
    }

    // Fake OCR data per doc type
    const fakeOCRData = {
      pan: { panNumber: 'ABCDE1234F', panName: 'Rahul Kumar Sharma', panDob: '15/03/1990' },
      aadhaar: { addrNum: '1234 5678 9012', addrName: 'Rahul Kumar Sharma', addrAddr: '42 MG Road, Koramangala, Bengaluru - 560034' },
      voter: { addrNum: 'ABC1234567', addrName: 'Rahul Kumar Sharma' },
      passport: { addrNum: 'J8234567', addrName: 'Rahul Kumar Sharma' },
      utility: { addrNum: 'BESCOM-9876543', addrAddr: '42 MG Road, Koramangala, Bengaluru - 560034' }
    };

    function autoFillFromScan(type) {
      if (type === 'pan') {
        panScanned = true;
        // simulate file upload
        if (!panUploaded) simulateUpload('pan', true);
        // fill fields
        const d = fakeOCRData.pan;
        const numEl = document.getElementById('panNumber');
        const nmEl = document.getElementById('panName');
        const dobEl = document.getElementById('panDob');
        if (numEl) { numEl.value = d.panNumber; numEl.classList.add('auto-filled'); numEl.disabled = true; }
        if (nmEl) { nmEl.value = d.panName; nmEl.classList.add('auto-filled'); nmEl.disabled = true; }
        if (dobEl) { dobEl.value = d.panDob; dobEl.classList.add('auto-filled'); dobEl.disabled = true; }
        document.getElementById('panAutoFillBanner').style.display = 'flex';
        document.getElementById('panEditBtns').style.display = 'block';
        panEditMode = false;
        showToast('success', 'PAN Card Scanned!', 'Details auto-filled. Review and proceed.');
        checkManualReady();
      } else {
        const docType = document.getElementById('addrDocType').value;
        if (!docType) { showToast('warning', 'Select Document Type', 'Please select a document type before scanning.'); return; }
        addrScanned = true;
        if (!addrUploaded) simulateUpload('addr', true);
        // fill fields after form rendered
        setTimeout(() => {
          const d = fakeOCRData[docType] || {};
          const numEl = document.getElementById('addrNum');
          const nmEl = document.getElementById('addrName');
          const adEl = document.getElementById('addrAddr');
          if (numEl && d.addrNum) { numEl.value = d.addrNum; numEl.classList.add('auto-filled'); numEl.disabled = true; }
          if (nmEl && d.addrName) { nmEl.value = d.addrName; nmEl.classList.add('auto-filled'); nmEl.disabled = true; }
          if (adEl && d.addrAddr) { adEl.value = d.addrAddr; adEl.classList.add('auto-filled'); adEl.disabled = true; }
          // show auto banner
          const banner = document.getElementById('addrAutoFillBanner');
          if (banner) banner.style.display = 'flex';
          const editBtns = document.getElementById('addrEditBtns');
          if (editBtns) editBtns.style.display = 'block';
          addrEditMode = false;
          showToast('success', 'Document Scanned!', 'Details auto-filled successfully. Review and proceed.');
          checkManualReady();
        }, 100);
      }
    }

    /* ── EDIT TOGGLE ── */
    function toggleEdit(type) {
      if (type === 'pan') {
        panEditMode = !panEditMode;
        const btn = document.getElementById('panEditBtn');
        const fields = ['panNumber', 'panName', 'panDob'];
        fields.forEach(id => {
          const el = document.getElementById(id);
          if (el) {
            el.disabled = !panEditMode;
            if (panEditMode) el.classList.remove('auto-filled');
            else if (panScanned) el.classList.add('auto-filled');
          }
        });
        if (panEditMode) {
          btn.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="12" height="12"><path d="M20 6L9 17l-5-5"/></svg> Save Changes`;
          btn.className = 'btn-save';
          btn.onclick = () => toggleEdit('pan');
          showToast('info', 'Edit Mode', 'You can now modify your PAN Card details.');
        } else {
          btn.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Edit Details`;
          btn.className = 'btn-edit';
          btn.onclick = () => toggleEdit('pan');
          showToast('success', 'Changes Saved', 'Your PAN Card details have been updated.');
        }
        checkManualReady();
      } else {
        addrEditMode = !addrEditMode;
        const btn = document.getElementById('addrEditBtnEl');
        const fields = ['addrNum', 'addrName', 'addrAddr'];
        fields.forEach(id => {
          const el = document.getElementById(id);
          if (el) {
            el.disabled = !addrEditMode;
            if (addrEditMode) el.classList.remove('auto-filled');
            else if (addrScanned) el.classList.add('auto-filled');
          }
        });
        if (addrEditMode) {
          btn.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="12" height="12"><path d="M20 6L9 17l-5-5"/></svg> Save Changes`;
          btn.className = 'btn-save';
          showToast('info', 'Edit Mode', 'You can now modify your address proof details.');
        } else {
          btn.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Edit Details`;
          btn.className = 'btn-edit';
          showToast('success', 'Changes Saved', 'Your address proof details have been updated.');
        }
        checkManualReady();
      }
    }

    /* ── NAV ── */
    function goTo(id) {
      document.querySelectorAll('.screen').forEach(s => s.classList.remove('active'));
      document.getElementById(id).classList.add('active');
      currentScreen = id;
      updateStepper();
      window.scrollTo({ top: 0, behavior: 'smooth' });
      if (id === 'screen-review') buildReview();
    }

    const checkSVG = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" width="13" height="13"><path d="M20 6L9 17l-5-5"/></svg>`;

    function updateStepper() {
      const s1 = document.getElementById('step1'), s2 = document.getElementById('step2'), s3 = document.getElementById('step3');
      const sc1 = document.getElementById('sc1'), sc2 = document.getElementById('sc2'), sc3 = document.getElementById('sc3');
      const l1 = document.getElementById('line1'), l2 = document.getElementById('line2');
      const lbl2 = document.getElementById('step2label');
      [s1, s2, s3].forEach(s => s.classList.remove('active', 'done'));
      [l1, l2].forEach(l => l.classList.remove('done'));
      if (currentScreen === 'screen-entity') {
        s1.classList.add('active'); sc1.innerHTML = '1'; sc2.textContent = '2'; sc3.textContent = '3'; lbl2.textContent = 'Take Photo';
      } else if (currentScreen === 'screen-selfie') {
        s1.classList.add('done'); l1.classList.add('done'); s2.classList.add('active');
        sc1.innerHTML = checkSVG; sc2.innerHTML = checkSVG; sc3.textContent = '3'; lbl2.textContent = 'Take Photo';
      } else if (['screen-upload', 'screen-manual', 'screen-review'].includes(currentScreen)) {
        s1.classList.add('done'); l1.classList.add('done'); s2.classList.add('done'); l2.classList.add('done'); s3.classList.add('active');
        sc1.innerHTML = checkSVG; sc2.innerHTML = checkSVG; sc3.innerHTML = checkSVG; lbl2.textContent = 'Selfie';
      } else if (currentScreen === 'screen-success') {
        s1.classList.add('done'); l1.classList.add('done'); s2.classList.add('done'); l2.classList.add('done'); s3.classList.add('done');
        sc1.innerHTML = checkSVG; sc2.innerHTML = checkSVG; sc3.innerHTML = checkSVG; lbl2.textContent = 'Selfie';
      }
    }

    document.addEventListener('DOMContentLoaded', () => {
      // Auto-selection based on URL
      const pathParts = window.location.pathname.split('/').filter(Boolean);
      const currentUrlType = pathParts.pop()?.split('?')[0];
      const validTypes = ['individual', 'sole_proprietor', 'partnership', 'company'];
      
      if (validTypes.includes(currentUrlType)) {
        const card = document.querySelector(`.entity-card[onclick*="'${currentUrlType}'"]`);
        if (card) selectEntity(card, currentUrlType, true);
      }
    });

    function selectEntity(el, type, isInitial = false) {
      document.querySelectorAll('.entity-card').forEach(c => c.classList.remove('selected'));
      el.classList.add('selected');
      selectedEntity = type;
      document.getElementById('entityLabel').textContent = entityNames[type] || 'Individual';

      // Redirect to the selected type's page if it's different from the current path
      const pathParts = window.location.pathname.split('/').filter(Boolean);
      const currentUrlType = pathParts.pop()?.split('?')[0];
      
      if (type && currentUrlType !== type) {
        window.location.href = "<?php echo base_url('kyc/'); ?>" + type;
        return;
      }

      document.getElementById('entityNextBtn').disabled = false;
      document.getElementById('entityNextRow').style.display = 'flex';
      if (!isInitial) {
        showToast('info', 'Entity Selected', `${entityNames[type]} selected. Click Next Step to continue.`);
      }
    }

    function takePhoto() {
      const circle = document.getElementById('camCircle');
      const label = document.getElementById('camLabel');
      const captureBtn = document.getElementById('captureBtn');
      const selectBtn = document.getElementById('selectBtn');

      circle.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" width="42" height="42" style="color:var(--blue)"><path d="M20 6L9 17l-5-5"/></svg>`;
      circle.style.borderColor = 'var(--blue)'; circle.style.background = '#fff';
      label.textContent = 'Selfie captured successfully!'; label.style.color = 'var(--blue)'; label.style.fontWeight = '700';
      captureBtn.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg> Retake`;

      const tag = document.getElementById('verifiedTag');
      tag.style.display = 'inline-flex'; tag.className = 'verified-tag ok';
      tag.textContent = `Verified : 99%`;

      showToast('success', 'Selfie Captured!', 'Your identity photo has been verified successfully.');
      // Store captured photo (Simulation: using a generic avatar data URI)
      capturedPhoto = 'https://images.unsplash.com/photo-1570295999919-56ceb8eecd61?q=80&w=1180&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D';
      setTimeout(() => goTo('screen-upload'), 1500);
    }

    function handleSelfieSelect(input) {
      if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();

        const circle = document.getElementById('camCircle');
        const label = document.getElementById('camLabel');
        const captureBtn = document.getElementById('captureBtn');

        circle.innerHTML = `<div style="width:100%; height:100%; border-radius:50%; background-size:cover; background-position:center; background-image:url('')" id="selfiePreview"></div>`;

        reader.onload = function (e) {
          document.getElementById('selfiePreview').style.backgroundImage = `url('${e.target.result}')`;
          label.textContent = 'Image selected successfully!';
          label.style.color = 'var(--blue)';

          const tag = document.getElementById('verifiedTag');
          tag.style.display = 'inline-flex'; tag.className = 'verified-tag ok';
          // Calculate arbitrary random percentage between 95 to 99 percent
          const percent = 95 + Math.floor(Math.random() * 5);
          tag.textContent = `Verified : ${percent}%`;

          showToast('success', 'Image Selected', 'Your photo is being processed for verification.');
          capturedPhoto = e.target.result;
          setTimeout(() => goTo('screen-upload'), 1500);
        }
        reader.readAsDataURL(file);
      }
    }

    function selectUpload(type) {
      uploadMethod = type;
      document.getElementById('opt-instant').classList.toggle('selected', type === 'instant');
      document.getElementById('opt-manual').classList.toggle('selected', type === 'manual');
    }

    function proceedUpload() {
      if (uploadMethod === 'instant') {
        document.getElementById('digiModal').classList.add('open');
      } else {
        // Since each file is now monolithic for its type, just go to the screen
        goTo('screen-manual');
      }
    }

    /* ── ADDR DOC TYPE ── */
    function onAddrTypeChange() {
      const type = document.getElementById('addrDocType').value;
      const uploadZone = document.getElementById('addrUploadZone');
      const label = document.getElementById('addrUploadLabel');
      const fileRow = document.getElementById('addrFileRow');
      const form = document.getElementById('addrForm');
      const scanRow = document.getElementById('addrScanRow');
      if (!type) {
        uploadZone.style.display = 'none';
        fileRow.innerHTML = ''; form.style.display = 'none';
        addrUploaded = false; addrScanned = false;
        document.getElementById('addrCheck').style.display = 'none';
        document.getElementById('addressCard').classList.remove('completed');
        document.getElementById('addrIconEl').classList.remove('green');
        checkManualReady(); return;
      }
      const labelMap = { aadhaar: 'Aadhaar Card', voter: 'Voter ID Card', passport: 'Passport', utility: 'Utility Bill' };
      label.textContent = `Click to upload ${labelMap[type]}`;
      uploadZone.style.display = 'block';
      fileRow.innerHTML = ''; form.style.display = 'none'; form.innerHTML = '';
      addrUploaded = false; addrScanned = false;
      document.getElementById('addrCheck').style.display = 'none';
      document.getElementById('addressCard').classList.remove('completed');
      document.getElementById('addrIconEl').classList.remove('green');
      checkManualReady();
    }

    /* ── UPLOAD ── */
    function handleDocUpload(input, type) {
      if (input.files && input.files[0]) {
        const file = input.files[0];

        if (type === 'pan') {
          panScanned = true;
        } else {
          addrScanned = true;
        }

        simulateUpload(type, true, file.name);
        autoFillFromScan(type);
      }
    }

    function autoFillFromScan(type) {
      if (type === 'pan') {
        const panNum = document.getElementById('panNumber');
        const panName = document.getElementById('panName');
        const panDob = document.getElementById('panDob');

        if (panNum) {
          panNum.value = '';
          panNum.classList.remove('auto-filled');
          panNum.disabled = false;
        }
        if (panName) {
          panName.value = '';
          panName.classList.remove('auto-filled');
          panName.disabled = false;
        }
        if (panDob) {
          panDob.value = '';
          panDob.classList.remove('auto-filled');
          panDob.disabled = false;
        }

        document.getElementById('panAutoFillBanner').style.display = 'none';
        document.getElementById('panEditBtns').style.display = 'none';
        panEditMode = true;

        showToast('success', 'Document Uploaded', 'Please enter high-accuracy details from your PAN card.');

      } else {
        const addrNum = document.getElementById('addrNum');
        const addrName = document.getElementById('addrName');
        const addrAddr = document.getElementById('addrAddr');

        if (addrNum) {
          addrNum.value = '';
          addrNum.classList.remove('auto-filled');
          addrNum.disabled = false;
        }
        if (addrName) {
          addrName.value = '';
          addrName.classList.remove('auto-filled');
          addrName.disabled = false;
        }
        if (addrAddr) {
          addrAddr.value = '';
          addrAddr.classList.remove('auto-filled');
          addrAddr.disabled = false;
        }

        document.getElementById('addrAutoFillBanner').style.display = 'none';
        document.getElementById('addrEditBtns').style.display = 'none';
        addrEditMode = true;

        showToast('success', 'Document Uploaded', 'Please enter the details exactly as shown on the document.');
      }
      checkManualReady();
    }

    function handleUIUpload(input, type) {
      if (input.files && input.files[0]) {
        simulateUpload(type, false, input.files[0].name);
      }
    }

    function simulateUpload(type, fromScan, fileName) {
      if (type === 'pan') {
        panUploaded = true;
        const fname = fileName || 'pan_card.jpg';
        const fileRow = `
      <div class="uploaded-file">
        <div class="uploaded-left">
          <div class="file-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/></svg></div>
          <div>
            <div class="file-name">${fname}</div>
            <div class="file-status">Uploaded successfully</div>
          </div>
        </div>
        <button class="file-remove" onclick="removeUpload('pan')">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>`;
        document.getElementById('panUploadZone').innerHTML = fileRow;
        document.getElementById('panForm').style.display = 'block';
        document.getElementById('panCheck').style.display = 'flex';
        document.getElementById('panCard').classList.add('completed');
        document.getElementById('panIconEl').classList.add('green');
        if (!fromScan) showToast('success', 'PAN Card Uploaded!', 'Please fill in the document details below.');
      } else if (type === 'cin') {
        cinUploaded = true;
        const fname = fileName || 'cin_registration.pdf';
        document.getElementById('cinFileRow').innerHTML = `
      <div class="uploaded-file">
        <div class="uploaded-left">
          <div class="file-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/></svg></div>
          <div><div class="file-name">${fname}</div><div class="file-status">Uploaded successfully</div></div>
        </div>
        <button class="file-remove" onclick="removeUpload('cin')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
      </div>`;
        document.getElementById('cinUploadZone').style.display = 'none';
        document.getElementById('cinForm').style.display = 'block';
        document.getElementById('cinCheck').style.display = 'flex';
        document.getElementById('cinCard').classList.add('completed');
        document.getElementById('cinIconEl').classList.add('green');
        showToast('success', 'CIN Uploaded!', 'Please fill in the registration details below.');
      } else if (type === 'gst') {
        gstUploaded = true;
        const fname = fileName || 'gst_registration.pdf';
        document.getElementById('gstFileRow').innerHTML = `
      <div class="uploaded-file">
        <div class="uploaded-left">
          <div class="file-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/></svg></div>
          <div><div class="file-name">${fname}</div><div class="file-status">Uploaded successfully</div></div>
        </div>
        <button class="file-remove" onclick="removeUpload('gst')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
      </div>`;
        document.getElementById('gstUploadZone').style.display = 'none';
        document.getElementById('gstForm').style.display = 'block';
        document.getElementById('gstCheck').style.display = 'flex';
        document.getElementById('gstCard').classList.add('completed');
        document.getElementById('gstIconEl').classList.add('green');
        showToast('success', 'GST Uploaded!', 'Please fill in the GSTIN details below.');
      } else if (type === 'deed') {
        deedUploaded = true;
        const fname = fileName || 'partnership_deed.pdf';
        document.getElementById('deedFileRow').innerHTML = `
      <div class="uploaded-file">
        <div class="uploaded-left">
          <div class="file-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/></svg></div>
          <div><div class="file-name">${fname}</div><div class="file-status">Uploaded successfully</div></div>
        </div>
        <button class="file-remove" onclick="removeUpload('deed')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
      </div>`;
        document.getElementById('deedUploadZone').style.display = 'none';
        document.getElementById('deedForm').style.display = 'block';
        document.getElementById('deedCheck').style.display = 'flex';
        document.getElementById('deedCard').classList.add('completed');
        document.getElementById('deedIconEl').classList.add('green');
        showToast('success', 'Deed Uploaded!', 'Please fill in the Partnership Deed details below.');
      }
      checkManualReady();
    }

    function removeUpload(type) {
      if (type === 'pan') {
        panUploaded = false; panScanned = false;
        document.getElementById('panCheck').style.display = 'none';
        document.getElementById('panCard').classList.remove('completed');
        document.getElementById('panIconEl').classList.remove('green');
        document.getElementById('panForm').style.display = 'none';
        document.getElementById('panUploadZone').innerHTML = `
      <div class="upload-zone" onclick="document.getElementById('panFileInput').click()">
        <div class="upload-zone-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16,16 12,12 8,16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/></svg></div>
        <div class="upload-zone-text">Click to upload PAN Card</div>
        <div class="upload-zone-hint">PNG, JPG or PDF (max. 5MB)</div>
      </div>
      <input type="file" id="panFileInput" style="display:none" accept="image/*,.pdf" onchange="handleUIUpload(this, 'pan')">`;
        showToast('warning', 'PAN Card Removed', 'Please upload your PAN Card again.');
      } else if (type === 'cin') {
        cinUploaded = false;
        document.getElementById('cinCheck').style.display = 'none';
        document.getElementById('cinCard').classList.remove('completed');
        document.getElementById('cinIconEl').classList.remove('green');
        document.getElementById('cinFileRow').innerHTML = '';
        document.getElementById('cinForm').style.display = 'none';
        document.getElementById('cinUploadZone').style.display = 'block';
        showToast('warning', 'CIN Removed', 'Please upload your CIN Registration document again.');
      } else if (type === 'gst') {
        gstUploaded = false;
        document.getElementById('gstCheck').style.display = 'none';
        document.getElementById('gstCard').classList.remove('completed');
        document.getElementById('gstIconEl').classList.remove('green');
        document.getElementById('gstFileRow').innerHTML = '';
        document.getElementById('gstForm').style.display = 'none';
        document.getElementById('gstUploadZone').style.display = 'block';
        showToast('warning', 'GST Removed', 'Please upload your GST Registration document again.');
      } else if (type === 'deed') {
        deedUploaded = false;
        document.getElementById('deedCheck').style.display = 'none';
        document.getElementById('deedCard').classList.remove('completed');
        document.getElementById('deedIconEl').classList.remove('green');
        document.getElementById('deedFileRow').innerHTML = '';
        document.getElementById('deedForm').style.display = 'none';
        document.getElementById('deedUploadZone').style.display = 'block';
        showToast('warning', 'Deed Removed', 'Please upload your Partnership Deed again.');
      }
      checkManualReady();
    }

    function checkManualReady() {
      const btn = document.getElementById('submitManualBtn');
      const panNum = document.getElementById('panNumber')?.value?.trim();
      const panNm = document.getElementById('panName')?.value?.trim();
      const panFieldsOk = panUploaded && panNum && panNm;

      const cinNumVal = document.getElementById('cinNum')?.value?.trim();
      const cinNmVal = document.getElementById('cinName')?.value?.trim();
      const cinDateVal = document.getElementById('cinDate')?.value?.trim();
      const cinFieldsOk = cinUploaded && cinNumVal && cinNmVal && cinDateVal;

      const gstNumVal = document.getElementById('gstNumber')?.value?.trim();
      const gstNmVal = document.getElementById('gstName')?.value?.trim();
      const gstFieldsOk = gstUploaded && gstNumVal && gstNmVal;

      const deedNumVal = document.getElementById('deedNum')?.value?.trim();
      const deedFieldsOk = deedUploaded && deedNumVal;

      if (!btn) return;

      if (!panUploaded) {
        btn.disabled = true;
        btn.textContent = 'Upload required document to continue';
      } else if (!panFieldsOk) {
        btn.disabled = true;
        btn.textContent = 'Complete PAN Card details to continue';
      } else if (!cinUploaded) {
        btn.disabled = true;
        btn.textContent = 'Upload CIN / Registration to continue';
      } else if (!cinFieldsOk) {
        btn.disabled = true;
        btn.textContent = 'Complete CIN / Registration details to continue';
      } else if (!gstUploaded) {
        btn.disabled = true;
        btn.textContent = 'Upload GST Registration to continue';
      } else if (!gstFieldsOk) {
        btn.disabled = true;
        btn.textContent = 'Complete GST Registration details to continue';
      } else if (!deedUploaded) {
        btn.disabled = true;
        btn.textContent = 'Upload Partnership Deed to continue';
      } else if (!deedFieldsOk) {
        btn.disabled = true;
        btn.textContent = 'Complete Partnership Deed details to continue';
      } else {
        btn.disabled = false;
        btn.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="15" height="15"><path d="M20 6L9 17l-5-5"/></svg> Continue to Verification`;
      }
    }

    function escapeHTML(str) {
      if (!str) return "";
      return String(str).replace(/[&<>"']/g, m => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
      })[m]);
    }

    function buildReview() {
      const panNumVal = document.getElementById('panNumber')?.value || '';
      const panNameVal = document.getElementById('panName')?.value || '';
      const cinNumVal = document.getElementById('cinNum')?.value || '';
      const cinNameVal = document.getElementById('cinName')?.value || '';
      const cinDateVal = document.getElementById('cinDate')?.value || '';
      const gstNumVal = document.getElementById('gstNumber')?.value || '';
      const gstNameVal = document.getElementById('gstName')?.value || '';
      const deedNumVal = document.getElementById('deedNum')?.value || '';

      const val = (v) => v ? `<div class="review-value">${escapeHTML(v)}</div>` : `<div class="review-value placeholder">As per document</div>`;

      const svgCamera = `<svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M23 19C23 19.5304 22.7893 20.0391 22.4142 20.4142C22.0391 20.7893 21.5304 21 21 21H3C2.46957 21 1.96086 20.7893 1.58579 20.4142C1.21071 20.0391 1 19.5304 1 19V8C1 7.46957 1.21071 6.96086 1.58579 6.58579C1.96086 6.21071 2.46957 6 3 6H7L9 3H15L17 6H21C21.5304 6 22.0391 6.21071 22.4142 6.58579C22.7893 6.96086 23 7.46957 23 8V19Z" stroke="#0446DB" stroke-opacity="0.4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M12 17C14.2091 17 16 15.2091 16 13C16 10.7909 14.2091 9 12 9C9.79086 9 8 10.7909 8 13C8 15.2091 9.79086 17 12 17Z" stroke="#0446DB" stroke-opacity="0.4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>`;

      const svgCheck = `<svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M20.4387 8.84767C20.8875 11.0502 20.5677 13.34 19.5325 15.3353C18.4973 17.3306 16.8095 18.9106 14.7503 19.812C12.6912 20.7134 10.3852 20.8817 8.21707 20.2887C6.0489 19.6957 4.14954 18.3774 2.83574 16.5535C1.52195 14.7296 0.873125 12.5105 0.997479 10.2661C1.12183 8.02174 2.01184 5.88785 3.51909 4.22027C5.02634 2.5527 7.05972 1.45224 9.28013 1.10242C11.5005 0.752598 13.7738 1.17456 15.7207 2.29793" stroke="#00C950" stroke-width="1.96541" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M6 11L9 14L16 7" stroke="#00C950" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>`;

      document.getElementById('reviewDocsGrid').innerHTML = `
        <!-- Photo Identification -->
        <div class="selfie-hero" style="position:relative; grid-column: 1 / -1; margin-bottom: 24px;">
          <div class="review-check" style="position:absolute; top:24px; right:24px;">
            ${svgCheck}
          </div>
          <div class="selfie-hero-left" style="text-align:center; width:150px; flex-shrink:0;">
             <div class="review-img-box" style="width:150px; height:150px; margin-bottom:12px; border-radius:18px; border: 1.5px solid #dbeafe; background:#f0f7ff; overflow:hidden; display:flex; align-items:center; justify-content:center;">
                ${(capturedPhoto && capturedPhoto.length > 50) ? `<img src="${capturedPhoto}" alt="Selfie" style="width:100%; height:100%; object-fit:cover;">` : svgCamera}
             </div>
             <div style="font-size:12px; color:var(--gray-500); font-weight:500; text-align:center;">Your Photo</div>
          </div>
          <div class="selfie-hero-content" style="padding-left:20px; align-self:flex-start; padding-top:8px;">
            <div class="selfie-hero-title" style="font-size:1.125rem; font-weight:700; color:var(--gray-900); margin-bottom:16px;">Photo Identification</div>
            <div class="selfie-status-box" style="width:100%;">
              <div class="selfie-status-title" style="font-size:1rem; font-weight:600; margin-bottom:6px;">Selfie captured successfully</div>
              <div class="selfie-status-text" style="font-size:0.875rem; color:var(--gray-500); font-weight:400;">Your identity photo has been verified and captured</div>
            </div>
          </div>
        </div>

    <div class="review-card doc-preview-card">
      <div class="dpc-head">
        <div class="dpc-head-left">
          <div class="dpc-icon-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
          </div>
          <div class="dpc-title">PAN Card</div>
        </div>
        <div class="dpc-check">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
        </div>
      </div>
      <div class="dpc-thumb">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
      </div>
      <div class="dpc-thumb-lbl">pancard.png</div>
      <div class="dpc-fields">
        <div class="dpc-dl"><div class="dpc-lbl">PAN Number</div>${val(panNumVal)}</div>
        <div class="dpc-dl"><div class="dpc-lbl">Full Name</div>${val(panNameVal)}</div>
      </div>
    </div>
    
    <div class="review-card doc-preview-card">
      <div class="dpc-head">
        <div class="dpc-head-left">
          <div class="dpc-icon-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
          </div>
          <div class="dpc-title">CIN / Registration</div>
        </div>
        <div class="dpc-check">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
        </div>
      </div>
      <div class="dpc-thumb">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
      </div>
      <div class="dpc-thumb-lbl">cin_registration.png</div>
      <div class="dpc-fields">
        <div class="dpc-dl"><div class="dpc-lbl">CIN Number</div>${val(cinNumVal)}</div>
        <div class="dpc-dl"><div class="dpc-lbl">Entity Name</div>${val(cinNameVal)}</div>
        <div class="dpc-dl"><div class="dpc-lbl">Date of Inc.</div>${val(cinDateVal)}</div>
      </div>
    </div>

    <div class="review-card doc-preview-card">
      <div class="dpc-head">
        <div class="dpc-head-left">
          <div class="dpc-icon-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
          </div>
          <div class="dpc-title">GST Registration</div>
        </div>
        <div class="dpc-check">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
        </div>
      </div>
      <div class="dpc-thumb">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
      </div>
      <div class="dpc-thumb-lbl">gst_registration.png</div>
      <div class="dpc-fields">
        <div class="dpc-dl"><div class="dpc-lbl">GST Number</div>${val(gstNum)}</div>
        <div class="dpc-dl"><div class="dpc-lbl">Legal Name</div>${val(gstName)}</div>
      </div>
    </div>

    <div class="review-card doc-preview-card">
      <div class="dpc-head">
        <div class="dpc-head-left">
          <div class="dpc-icon-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
          </div>
          <div class="dpc-title">Partnership Deed</div>
        </div>
        <div class="dpc-check">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
        </div>
      </div>
      <div class="dpc-thumb">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
      </div>
      <div class="dpc-thumb-lbl">partnership_deed.png</div>
      <div class="dpc-fields">
        <div class="dpc-dl"><div class="dpc-lbl">Agreement Number</div>${val(deedNum)}</div>
      </div>
    </div>`;
    }

    function submitKYC() {
      // Collect consolidated data
      const kycData = {
        meta: { type: 'partnership', timestamp: new Date().toISOString() },
        pan: {
          number: document.getElementById('panNumber')?.value,
          name: document.getElementById('panName')?.value,
        },
        registration: {
          number: document.getElementById('cinNum')?.value,
          name: document.getElementById('cinName')?.value,
          date_of_inc: document.getElementById('cinDate')?.value
        },
        gst: {
          number: document.getElementById('gstNumber')?.value,
          name: document.getElementById('gstName')?.value
        },
        deed: {
          number: document.getElementById('deedNum')?.value
        }
      };
      
      console.log('🏁 KYC Submission Data:', kycData);

      showToast('success', 'KYC Submitted!', 'Your documents are under review. You\'ll be notified soon.');
      goTo('screen-success');
    }

    function closeModal() { document.getElementById('digiModal').classList.remove('open'); }

    function digiDone() {
      closeModal();
      showToast('success', 'DigiLocker Connected!', 'Your documents have been fetched successfully.');
      setTimeout(() => goTo('screen-success'), 600);
    }

    // Dynamic Form Builder Handlers
    function onTypeChange(sel, prefix) {
      if (sel.value) {
        document.getElementById(prefix + 'UploadZone').style.display = 'block';
      } else {
        document.getElementById(prefix + 'UploadZone').style.display = 'none';
      }
      document.getElementById(prefix + 'Form').style.display = 'none';
      document.getElementById(prefix + 'FileRow').innerHTML = '';
      document.getElementById(prefix + 'Check').style.display = 'none';
      document.getElementById(prefix + 'Card').classList.remove('completed');
      document.getElementById(prefix + 'IconEl').classList.remove('green');
    }

    function handleUIUpload(input, prefix) {
      if (input.files && input.files[0]) {
        const fname = input.files[0].name;
        document.getElementById(prefix + 'UploadZone').style.display = 'none';
        document.getElementById(prefix + 'FileRow').innerHTML = `
          <div class="uploaded-file">
            <div class="uploaded-left">
              <div class="file-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/></svg></div>
              <div>
                <div class="file-name">${fname}</div>
                <div class="file-status">Uploaded successfully</div>
              </div>
            </div>
            <button class="file-remove" onclick="removeUIUpload('${prefix}')">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
          </div>
        `;
        document.getElementById(prefix + 'Form').style.display = 'block';
        document.getElementById(prefix + 'Check').style.display = 'flex';
        document.getElementById(prefix + 'Card').classList.add('completed');
        document.getElementById(prefix + 'IconEl').classList.add('green');
        showToast('success', 'Document Uploaded', 'Please fill in the document details below.');

        // Ensure manual submit relies on this UI
        const btn = document.getElementById('submitManualBtn');
        if (btn) { btn.disabled = false; btn.innerHTML = 'Continue to Verification'; }
      }
    }

    function removeUIUpload(prefix) {
      document.getElementById(prefix + 'UploadZone').style.display = 'block';
      document.getElementById(prefix + 'FileRow').innerHTML = '';
      document.getElementById(prefix + 'Form').style.display = 'none';
      document.getElementById(prefix + 'Check').style.display = 'none';
      document.getElementById(prefix + 'Card').classList.remove('completed');
      document.getElementById(prefix + 'IconEl').classList.remove('green');
      showToast('warning', 'Document Removed', 'Please upload your document again.');
    }

    updateStepper();
  </script>
</body>

</html>