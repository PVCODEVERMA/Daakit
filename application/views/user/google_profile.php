<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$googleUser = !empty($google_user) ? $google_user : array();
$otpPhone = !empty($otp_context['phone']) ? $otp_context['phone'] : (!empty($googleUser['phone']) ? $googleUser['phone'] : '');
$otpVerified = !empty($otp_context['otp_verified']);
?>

<title>Complete Profile | DAAKiT - India's Leading Fulfillment Platform</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    brand: '#0446DB',
                    'brand-dark': '#0338AF',
                    'brand-pale': '#C5D3FB',
                },
                fontFamily: {
                    sans: ['DM Sans', 'sans-serif'],
                },
            }
        }
    };
</script>
<style>
    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes floatUp {
        from {
            transform: translateY(0px);
        }

        to {
            transform: translateY(-10px);
        }
    }

    @keyframes scrollLogos {
        from {
            transform: translateX(0);
        }

        to {
            transform: translateX(-50%);
        }
    }

    .animate-fade-up {
        animation: fadeUp 0.5s ease both;
    }

    .animate-float {
        animation: floatUp 4s ease-in-out infinite alternate;
    }

    .auth-bg {
        background: radial-gradient(circle at top right, #d9daab 0%, rgba(191, 191, 150, 0.35) 30%, rgba(191, 191, 150, 0.08) 50%, transparent 70%),
            linear-gradient(135deg, #0345DA 0%, #0345DA 40%, #0345DA 70%, #0345DA 100%);
    }

    .logo-marquee {
        overflow: hidden;
        width: 100%;
        position: relative;
        mask-image: linear-gradient(to right, transparent, black 15%, black 85%, transparent);
        -webkit-mask-image: linear-gradient(to right, transparent, black 15%, black 85%, transparent);
    }

    .logo-track {
        display: flex;
        align-items: center;
        gap: clamp(2rem, 4vw, 4rem);
        width: max-content;
        animation: scrollLogos 22s linear infinite;
    }

    .logo-marquee:hover .logo-track {
        animation-play-state: paused;
    }

    .logo-item {
        flex: 0 0 auto;
        opacity: 1;
        filter: grayscale(0);
        transform: scale(1);
        transition:
            transform 300ms cubic-bezier(0.34, 1.56, 0.64, 1),
            opacity 300ms ease,
            box-shadow 300ms ease;

        height: clamp(80px, 12vh, 100px);
        width: clamp(80px, 12vh, 100px);
        margin: 0 15px;
        object-fit: contain;
        background: #ffffff;
        padding: 15px;
        border-radius: 999px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border: 1px solid #f1f5f9;
        cursor: pointer;
    }

    @media (min-width: 1024px) {
        .logo-item {
            height: 140px;
            width: 140px;
            margin: 0 25px;
            padding: 20px;
        }
    }

    @media (min-width: 1280px) {
        .logo-item {
            height: 180px;
            width: 180px;
            margin: 0 35px;
            padding: 25px;
        }

        .logo-marquee {
            height: 240px !important;
        }
    }

    .logo-item:hover {
        opacity: 1;
        transform: scale(1.15);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        border-color: #0345DA;
    }

    @media (prefers-reduced-motion: reduce) {
        .logo-track {
            animation: none;
        }

        .logo-item {
            transition: none;
        }
    }

    input:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(26, 75, 240, 0.12);
    }

    html,
    body {
        height: 100%;
        width: 100%;
        margin: 0;
        padding: 0;
        overflow-x: hidden;
    }

    .step-content {
        display: none;
    }

    .step-content.active {
        display: flex;
        animation: fadeUp 0.4s ease both;
    }

    .hide-scrollbar::-webkit-scrollbar {
        display: none;
    }

    .hide-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .auth-panel {
        width: 100%;
    }

    .signup-card {
        width: min(100%, 36rem);
        min-height: clamp(33rem, 72vh, 44rem);
    }

    .signup-heading {
        font-size: clamp(1.75rem, 1.5rem + 1vw, 2.5rem);
        line-height: 1.1;
        font-weight: 800;
        letter-spacing: -0.02em;
    }

    .signup-subtext {
        font-size: clamp(0.95rem, 0.9rem + 0.2vw, 1.1rem);
        line-height: 1.5;
        color: #64748b;
    }

    .signup-label {
        font-size: clamp(0.7rem, 0.65rem + 0.05vw, 0.75rem);
        letter-spacing: 0.08em;
        font-weight: 700;
        color: #64748b;
    }

    .signup-input {
        min-height: clamp(3rem, 2.8rem + 0.4vw, 3.4rem);
        font-size: clamp(0.9rem, 0.85rem + 0.1vw, 1rem);
    }

    .signup-button {
        min-height: clamp(3.2rem, 3rem + 0.5vw, 3.8rem);
        font-size: clamp(1rem, 0.95rem + 0.15vw, 1.15rem);
        font-weight: 700;
    }

    @media (min-width: 1024px) {
        .auth-panel {
            width: 100%;
        }

        .signup-card {
            width: 100%;
            max-width: 100%;
            height: 90vh;
            overflow-y: auto;
        }

        .signup-heading {
            font-size: clamp(2.5rem, 2rem + 2vw, 4rem);
        }
    }

    @media (min-width: 1280px) {
        .signup-subtext {
            font-size: 1.15rem;
        }

        .signup-label {
            font-size: 0.8rem;
        }

        .signup-input {
            min-height: 3.8rem;
            font-size: 1.05rem;
        }

        .signup-button {
            min-height: 4.2rem;
            font-size: 1.25rem;
        }
    }
</style>

<section class="auth-bg">
    <main class="lg:h-screen flex justify-center overflow-hidden" style="font-family: 'DM Sans', sans-serif;">
        <div class="flex flex-col lg:flex-row w-full lg:h-screen animate-fade-up">

            <!-- ════ LEFT PANEL (Branding) ════ -->
            <div
                class="relative hidden lg:flex flex-col w-[45%] xl:w-[50%] h-screen overflow-hidden px-12 pt-10 pb-12 rounded-br-[60px] rounded-tr-[60px]">
                <div class="flex justify-between items-center w-full relative z-10">
                    <img src="<?= base_url('assets/images/logo-daakit.png') ?>" alt="DAAKit Logo" class="w-36 h-auto" />
                    <a href="<?= base_url() ?>"
                        class="flex items-center gap-2 text-white/90 hover:text-white text-sm font-medium transition-colors no-underline">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span class="font-bold text-base">Back</span>
                    </a>
                </div>

                <div class="relative z-10 mt-16">
                    <h1 class="text-white font-extrabold text-5xl xl:text-7xl leading-[1.1] tracking-tight">
                        Deliver in Hours<br>
                        <span class="text-white/80 font-extrabold">Not Days.</span>
                    </h1>
                    <p
                        class="mt-2 sm:mt-4 text-lg lg:text-xl xl:text-3xl font-extrabold text-white tracking-wide leading-tight">
                        Store better. Scale faster.<br class="hidden xl:block">
                        Ship smarter across India.
                    </p>
                </div>

                <div class="relative z-10 mt-32">
                    <img src="<?= base_url('assets/images/image.png') ?>" alt="DAAKit Auth Image"
                        class="w-full h-auto" />
                </div>
            </div>

            <!-- ════ RIGHT PANEL (Steps) ════ -->
            <div class="flex-1 flex flex-col relative px-4 bg-gray-50/30 lg:bg-transparent overflow-hidden">
                <div class="auth-panel flex-1 flex justify-center items-center w-full py-6 overflow-hidden">
                    <div
                        class="signup-card w-full bg-white rounded-lg border border-gray-100 shadow-[0_20px_60px_-28px_rgba(15,23,42,0.28)] px-5 pb-6 sm:px-7 sm:pb-7 lg:px-10 lg:pb-9 xl:px-14 xl:pb-14 pt-[24px] xl:pt-12 relative z-10 animate-fade-up flex flex-col justify-start">

                        <!-- Header (Back & Login) -->
                        <div class="px-1 sm:px-1.5 py-4 flex shrink-0 justify-between items-center bg-white z-20">
                            <div
                                class="text-sm sm:text-base font-medium flex items-center justify-between w-full border-b border-gray-100 pb-4">
                                <span class="text-gray-500">Not your account?</span>
                                <a href="<?= base_url('users/logout') ?>"
                                    class="text-[#0345DA] hover:underline font-bold">Switch Account</a>
                            </div>
                        </div>

                        <div
                            class="flex-1 overflow-y-auto hide-scrollbar px-1 sm:px-2 py-1.5 flex flex-col justify-start items-center xl:mt-16">

                            <!-- STEP 1: Details -->
                            <div id="step-1"
                                class="step-content <?= $otpVerified ? '' : 'active' ?> flex-col w-full max-w-full mx-auto md:mx-0">

                                <!-- Progress Stepper (Visual Only) -->
                                <div id="signup-stepper"
                                    class="flex items-start gap-2.5 sm:gap-4 mb-2.5 sm:mb-4 xl:mb-12">
                                    <div class="flex flex-col items-center min-w-[50px]">
                                        <div
                                            class="w-5 h-5 rounded-full bg-[#16A34A] flex items-center justify-center text-white shadow-sm">
                                            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="20 6 9 17 4 12" />
                                            </svg>
                                        </div>
                                        <span class="mt-1.5 text-[20px] font-medium text-gray-700">Account</span>
                                    </div>
                                    <div class="flex-1 h-px bg-gray-200 mt-[10px]"></div>
                                    <div class="flex flex-col items-center min-w-[50px]">
                                        <div
                                            class="w-5 h-5 rounded-full border border-brand bg-white flex items-center justify-center text-[11px] font-semibold text-brand">
                                            2</div>
                                        <span class="mt-1.5 text-[20px] font-semibold text-gray-900">Verify</span>
                                    </div>
                                    <div class="flex-1 h-px bg-gray-200 mt-[10px]"></div>
                                    <div class="flex flex-col items-center min-w-[50px]">
                                        <div
                                            class="w-5 h-5 rounded-full bg-gray-100 flex items-center justify-center text-[11px] font-semibold text-gray-400">
                                            3</div>
                                        <span class="mt-1.5 text-[20px] font-medium text-gray-400">Done</span>
                                    </div>
                                </div>

                                <h2 class="signup-heading mb-2">Welcome!</h2>
                                <p class="signup-subtext mb-6">Let's verify your phone number to complete setup.</p>

                                <form id="form-google-profile" class="xl:space-y-8">
                                    <div id="signup-step-1-message"
                                        class="hidden mb-4 rounded-xl px-4 py-3 text-sm font-medium"></div>

                                    <div class="mb-6">
                                        <label class="signup-label block font-bold text-gray-600 mb-2 uppercase">MOBILE
                                            NUMBER</label>
                                        <div
                                            class="flex gap-0 border border-gray-200 rounded-lg overflow-hidden focus-within:shadow-sm focus-within:border-brand transition-all">
                                            <span
                                                class="bg-[#F1F3F7] px-4 flex items-center text-gray-700 font-bold border-r border-gray-200">+91</span>
                                            <input id="google-signup-phone" type="tel" maxlength="10"
                                                placeholder="98765 43210"
                                                value="<?= htmlspecialchars($otpPhone, ENT_QUOTES, 'UTF-8') ?>"
                                                class="signup-input flex-1 px-4 bg-[#F8F9FB] focus:bg-white outline-none w-full" />
                                        </div>
                                    </div>

                                    <button id="send-google-otp" type="button"
                                        class="signup-button w-full bg-brand hover:bg-brand-dark text-white font-bold rounded-lg flex items-center justify-center gap-2 transition-all shadow-md active:scale-[0.98]">
                                        Send Verification Code
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                            <polyline points="12 5 19 12 12 19"></polyline>
                                        </svg>
                                    </button>
                                </form>
                            </div>

                            <!-- STEP 2: Verify -->
                            <div id="step-2"
                                class="step-content flex-col w-full max-w-full mx-auto md:mx-0 pt-4 sm:pt-6">
                                <div class="flex items-start gap-2.5 sm:gap-4 mb-5 sm:mb-6">
                                    <div class="flex flex-col items-center min-w-[50px]">
                                        <div
                                            class="w-5 h-5 rounded-full bg-[#16A34A] flex items-center justify-center text-white shadow-sm">
                                            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="20 6 9 17 4 12" />
                                            </svg>
                                        </div>
                                        <span class="mt-1.5 text-[10px] font-medium text-gray-700">Account</span>
                                    </div>
                                    <div class="flex-1 h-px bg-gray-200 mt-[10px]"></div>
                                    <div class="flex flex-col items-center min-w-[50px]">
                                        <div
                                            class="w-5 h-5 rounded-full border border-brand bg-white flex items-center justify-center text-[11px] font-semibold text-brand">
                                            2</div>
                                        <span class="mt-1.5 text-[10px] font-semibold text-gray-900">Verify</span>
                                    </div>
                                    <div class="flex-1 h-px bg-gray-200 mt-[10px]"></div>
                                    <div class="flex flex-col items-center min-w-[50px]">
                                        <div
                                            class="w-5 h-5 rounded-full bg-gray-100 flex items-center justify-center text-[11px] font-semibold text-gray-400">
                                            3</div>
                                        <span class="mt-1.5 text-[10px] font-medium text-gray-400">Done</span>
                                    </div>
                                </div>

                                <div class="flex justify-center mb-6">
                                    <div
                                        class="w-28 h-28 bg-[#F3F4F6] rounded-[28px] flex items-center justify-center shrink-0">
                                        <svg class="w-[72px] h-[84px]" viewBox="0 0 56 76" fill="none"
                                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <rect x="11" y="4" width="34" height="64" rx="8.5" fill="#155BD5" />
                                            <rect x="17" y="12" width="22" height="38" rx="2.5" fill="white" />
                                            <circle cx="28" cy="57.5" r="4.5" fill="white" />
                                        </svg>
                                    </div>
                                </div>

                                <h2 class="signup-heading text-center mb-2">Check your phone</h2>
                                <p class="signup-subtext text-center mb-8">Enter the code we sent to <span
                                        id="display-phone" class="text-gray-900 font-bold">+91 XXXXX XXXXX</span></p>

                                <div id="signup-step-2-message"
                                    class="hidden mb-4 rounded-xl px-4 py-3 text-sm font-medium"></div>

                                <!-- OTP inputs -->
                                <div class="flex justify-center gap-2 sm:gap-3 mb-6">
                                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*"
                                        class="signup-otp-input w-11 h-11 sm:w-13 sm:h-13 shrink-0 border-2 border-gray-200 bg-gray-50 rounded-lg text-center text-lg sm:text-xl font-bold text-gray-900 focus:border-brand focus:bg-white transition-all" />
                                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*"
                                        class="signup-otp-input w-11 h-11 sm:w-13 sm:h-13 shrink-0 border-2 border-gray-200 bg-gray-50 rounded-lg text-center text-lg sm:text-xl font-bold text-gray-900 focus:border-brand focus:bg-white transition-all" />
                                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*"
                                        class="signup-otp-input w-11 h-11 sm:w-13 sm:h-13 shrink-0 border-2 border-gray-200 bg-gray-50 rounded-lg text-center text-lg sm:text-xl font-bold text-gray-900 focus:border-brand focus:bg-white transition-all" />
                                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*"
                                        class="signup-otp-input w-11 h-11 sm:w-13 sm:h-13 shrink-0 border-2 border-gray-100 bg-gray-50 rounded-lg text-center text-lg sm:text-xl font-bold text-gray-900 outline-none" />
                                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*"
                                        class="signup-otp-input w-11 h-11 sm:w-13 sm:h-13 shrink-0 border-2 border-gray-100 bg-gray-50 rounded-lg text-center text-lg sm:text-xl font-bold text-gray-900 outline-none" />
                                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*"
                                        class="signup-otp-input w-11 h-11 sm:w-13 sm:h-13 shrink-0 border-2 border-gray-100 bg-gray-50 rounded-lg text-center text-lg sm:text-xl font-bold text-gray-900 outline-none" />
                                </div>

                                <button id="verify-google-otp" type="button"
                                    class="signup-button w-full bg-brand hover:bg-brand-dark text-white font-bold rounded-xl flex items-center justify-center gap-2 transition-all shadow-md mb-4">
                                    Verify & continue
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                        <polyline points="12 5 19 12 12 19"></polyline>
                                    </svg>
                                </button>

                                <button id="change-google-phone" type="button"
                                    class="signup-button w-full bg-white border-2 border-gray-100 hover:border-gray-200 text-gray-600 font-bold rounded-xl flex items-center justify-center gap-2 transition-all">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="19" y1="12" x2="5" y2="12"></line>
                                        <polyline points="12 19 5 12 12 5"></polyline>
                                    </svg>
                                    Change number
                                </button>

                                <div class="mt-6 text-center text-gray-500 font-medium text-sm">
                                    Didn't receive it? <button id="resend-google-otp" type="button"
                                        class="text-brand font-bold bg-transparent border-0 p-0">Resend code</button>
                                </div>
                            </div>

                            <!-- STEP 3: Done -->
                            <div id="step-3"
                                class="step-content <?= $otpVerified ? 'active' : '' ?> flex-col w-full max-w-full mx-auto md:mx-0 pt-4 sm:pt-6">
                                <div class="flex items-start gap-3 sm:gap-5 mb-6 sm:mb-8">
                                    <div class="flex flex-col items-center min-w-[56px]">
                                        <div
                                            class="w-5 h-5 rounded-full bg-[#16A34A] flex items-center justify-center text-white shadow-sm">
                                            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="20 6 9 17 4 12" />
                                            </svg>
                                        </div>
                                        <span class="mt-2 text-[11px] font-medium text-gray-700">Account</span>
                                    </div>
                                    <div class="flex-1 h-px bg-gray-200 mt-[10px]"></div>
                                    <div class="flex flex-col items-center min-w-[56px]">
                                        <div
                                            class="w-5 h-5 rounded-full bg-[#16A34A] flex items-center justify-center text-white shadow-sm">
                                            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="20 6 9 17 4 12" />
                                            </svg>
                                        </div>
                                        <span class="mt-2 text-[11px] font-medium text-gray-700">Verify</span>
                                    </div>
                                    <div class="flex-1 h-px bg-gray-200 mt-[10px]"></div>
                                    <div class="flex flex-col items-center min-w-[56px]">
                                        <div
                                            class="w-5 h-5 rounded-full border border-brand bg-white flex items-center justify-center text-[11px] font-semibold text-brand">
                                            3</div>
                                        <span class="mt-2 text-[11px] font-semibold text-gray-900">Done</span>
                                    </div>
                                </div>

                                <div class="flex justify-center mb-8">
                                    <div class="w-28 h-28 bg-[#A7F3C0] rounded-[18px] flex items-center justify-center">
                                        <div
                                            class="w-14 h-14 bg-[#15803D] rounded-full flex items-center justify-center text-white">
                                            <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="20 7 10 17 5 12" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <h2 class="signup-heading text-center mb-3">You're all set!</h2>
                                <p class="signup-subtext text-center mx-auto mb-10 max-w-[480px]">Your account has been
                                    created and mobile number verified successfully.</p>

                                <a id="signup-dashboard-link" href="<?= base_url('analytics') ?>"
                                    class="signup-button w-full max-w-[560px] mx-auto inline-flex items-center justify-center gap-2.5 px-6 rounded-2xl bg-brand hover:bg-brand-dark text-white font-bold tracking-[0.01em] transition-all shadow-xl shadow-blue-100 hover:shadow-2xl active:scale-[0.98] no-underline">
                                    Go to dashboard
                                    <svg class="w-5 h-5 sm:w-[22px] sm:h-[22px]" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                        <polyline points="12 5 19 12 19"></polyline>
                                    </svg>
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</section>

<!-- Trust Section -->
<div class="w-full bg-white pb-16 sm:pb-24 pt-16 border-t border-gray-50 overflow-hidden">
    <div class="max-w-[1000px] mx-auto mb-10 text-center px-4">
        <p
            class="text-gray-400 font-extrabold text-xs sm:text-sm tracking-[0.15em] mb-4 text-2xl lg:text-3xl xl:text-4xl uppercase">
            TRUSTED BY 500+ D2C BRAND PARTNERS.</p>
        <h2 class="text-slate-900 font-extrabold text-2xl sm:text-4xl lg:text-5xl mb-4 leading-[1.15]">
            Your all-in-one platform <br class="hidden sm:block">
            for quick commerce fulfillment
        </h2>
        <p class="text-gray-500 font-medium text-base sm:text-xl lg:text-2xl">
            Integrated with India's leading courier & channel partners
        </p>
    </div>

    <div class="flex justify-center mb-16 px-4">
        <div
            class="bg-[#F8FAFC] border border-gray-100 p-1.5 inline-flex rounded-2xl sm:rounded-[20px] shadow-sm overflow-hidden">
            <button onclick="switchTrack('brand')" id="btn-brand"
                class="px-4 sm:px-10 lg:px-12 py-2.5 sm:py-3 text-[11px] sm:text-sm lg:text-base font-bold rounded-xl sm:rounded-[14px] bg-[#0345DA] text-white shadow-lg shadow-blue-200/50 transition-all duration-300 whitespace-nowrap">
                Courier
            </button>
            <button onclick="switchTrack('courier')" id="btn-courier"
                class="px-4 sm:px-10 lg:px-12 py-2.5 sm:py-3 text-[11px] sm:text-sm lg:text-base font-bold rounded-xl sm:rounded-[14px] text-slate-500 hover:text-slate-700 transition-all duration-300 ml-1 whitespace-nowrap">
                Trusted By
            </button>
            <button onclick="switchTrack('channels')" id="btn-channels"
                class="px-4 sm:px-10 lg:px-12 py-2.5 sm:py-3 text-[11px] sm:text-sm lg:text-base font-bold rounded-xl sm:rounded-[14px] text-slate-500 hover:text-slate-700 transition-all duration-300 ml-1 whitespace-nowrap">
                Channels
            </button>
        </div>
    </div>

    <!-- Active Track Container -->
    <div class="relative w-full max-w-[1600px] mx-auto logo-marquee px-4 h-[120px] sm:h-[150px]">
        <?php
        $courierLogos = [
            'TrustcourierLogo_01.png',
            'TrustcourierLogo_02.png',
            'TrustcourierLogo_03.png',
            'TrustcourierLogo_04.png',
            'TrustcourierLogo_05.png',
            'TrustcourierLogo_06.jpg',
            'TrustcourierLogo_07.png',
            'TrustcourierLogo_08.jpg',
            'TrustcourierLogo_09.png',
            'TrustcourierLogo_10.png'
        ];
        $channelLogos = [
            'channels_1.webp',
            'channels_2.webp',
            'channels_3.webp',
            'channels_4.webp',
            'channels_5.webp',
            'channels_6.webp'
        ];
        $brandLogos = [
            'BrandLogo_01.svg',
            'BrandLogo_02.svg',
            'BrandLogo_03.svg',
            'BrandLogo_04.svg',
            'BrandLogo_05.svg',
            'BrandLogo_01.svg'
        ];
        ?>

        <!-- Courier Track -->
        <div id="track-courier"
            class="logo-track absolute inset-0 flex transition-all duration-500 opacity-0 pointer-events-none translate-y-4">
            <?php foreach (array_merge($courierLogos, $courierLogos) as $index => $logo): ?>
                <img src="<?= base_url('assets/TrustcourierLogo/' . $logo) ?>" class="logo-item"
                    alt="Courier <?= ($index % 10) + 1 ?>">
            <?php endforeach; ?>
        </div>

        <!-- Channels Track -->
        <div id="track-channels"
            class="logo-track absolute inset-0 flex transition-all duration-500 opacity-0 pointer-events-none translate-y-4">
            <?php foreach (array_merge($channelLogos, $channelLogos) as $index => $logo): ?>
                <img src="<?= base_url('assets/channels/' . $logo) ?>" class="logo-item"
                    alt="Channel <?= ($index % 6) + 1 ?>">
            <?php endforeach; ?>
        </div>

        <!-- Brand Track -->
        <div id="track-brand"
            class="logo-track absolute inset-0 flex transition-all duration-500 opacity-100 pointer-events-auto">
            <?php foreach (array_merge($brandLogos, $brandLogos) as $index => $logo): ?>
                <img src="<?= base_url('assets/BrandLogo/' . $logo) ?>" class="logo-item"
                    alt="Brand <?= ($index % 6) + 1 ?>">
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
    function switchTrack(type) {
        const brandBtn = document.getElementById('btn-brand');
        const courierBtn = document.getElementById('btn-courier');
        const channelsBtn = document.getElementById('btn-channels');
        const brandTrack = document.getElementById('track-brand');
        const courierTrack = document.getElementById('track-courier');
        const channelsTrack = document.getElementById('track-channels');

        const buttons = [brandBtn, courierBtn, channelsBtn];
        const tracks = [brandTrack, courierTrack, channelsTrack];

        buttons.forEach(btn => {
            if (btn) {
                btn.classList.remove('bg-[#0345DA]', 'text-white', 'shadow-lg');
                btn.classList.add('text-slate-500');
            }
        });

        tracks.forEach(track => {
            if (track) {
                track.classList.add('opacity-0', 'pointer-events-none', 'translate-y-4');
                track.classList.remove('opacity-100', 'pointer-events-auto');
            }
        });

        const activeBtn = document.getElementById('btn-' + type);
        const activeTrack = document.getElementById('track-' + type);

        if (activeBtn) {
            activeBtn.classList.add('bg-[#0345DA]', 'text-white', 'shadow-lg');
            activeBtn.classList.remove('text-slate-500');
        }

        if (activeTrack) {
            activeTrack.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-4');
            activeTrack.classList.add('opacity-100', 'pointer-events-auto');
        }
    }
</script>

<script>
    (function () {
        const phoneInput = document.getElementById('google-signup-phone');
        const sendOtpBtn = document.getElementById('send-google-otp');
        const verifyOtpBtn = document.getElementById('verify-google-otp');
        const resendOtpBtn = document.getElementById('resend-google-otp');
        const changeNumberBtn = document.getElementById('change-google-phone');
        const displayPhone = document.getElementById('display-phone');
        const msgStep1 = document.getElementById('signup-step-1-message');
        const msgStep2 = document.getElementById('signup-step-2-message');
        const otpInputs = Array.from(document.querySelectorAll('.signup-otp-input'));

        function showToast(message, type = 'error') {
            if (!message) return;
            let container = document.getElementById('toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toast-container';
                container.className = 'fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[9999] flex flex-col gap-3 w-[92vw] max-w-sm text-center';
                document.body.appendChild(container);
            }
            const toast = document.createElement('div');
            const baseClass = 'rounded-lg px-4 py-3 text-sm font-semibold shadow-lg border transition-all duration-300 opacity-0 translate-y-1';
            const variantClass = type === 'success' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200';
            toast.className = `${baseClass} ${variantClass}`;
            toast.textContent = message;
            container.appendChild(toast);
            requestAnimationFrame(() => { toast.classList.remove('opacity-0', 'translate-y-1'); });
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-1');
                setTimeout(() => toast.remove(), 250);
            }, 3200);
        }

        function goToStep(step) {
            document.querySelectorAll('.step-content').forEach(function (s) {
                s.classList.remove('active');
            });
            const activeStep = document.getElementById('step-' + step);
            if (activeStep) activeStep.classList.add('active');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function setMsg(el, message, type) {
            if (!el) return;
            if (!message) {
                el.className = 'hidden mb-4 rounded-xl px-4 py-3 text-sm font-medium';
                el.textContent = '';
                return;
            }
            const typeClasses = type === 'success'
                ? 'mb-4 rounded-xl px-4 py-3 text-sm font-medium bg-green-50 text-green-700 border border-green-200'
                : 'mb-4 rounded-xl px-4 py-3 text-sm font-medium bg-red-50 text-red-700 border border-red-200';
            el.className = typeClasses;
            el.textContent = message;
        }

        function normalizePhone(phone) {
            return String(phone || '').replace(/\D+/g, '').slice(-10);
        }

        function renderPhone(phone) {
            let normalized = normalizePhone(phone);
            if (normalized.length === 10) {
                displayPhone.textContent = '+91 ' + normalized.slice(0, 5) + ' ' + normalized.slice(5);
            }
        }

        async function postForm(url, payload) {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams(payload).toString()
            });
            return response.json();
        }

        // OTP inputs auto-advance
        otpInputs.forEach(function (input, index) {
            input.addEventListener('input', function (event) {
                const cleanValue = event.target.value.replace(/\D/g, '');
                event.target.value = cleanValue;
                if (cleanValue && otpInputs[index + 1]) {
                    otpInputs[index + 1].focus();
                }
            });
            input.addEventListener('keydown', function (event) {
                if (event.key === 'Backspace' && !event.target.value && otpInputs[index - 1]) {
                    otpInputs[index - 1].focus();
                }
            });
            input.addEventListener('paste', function (event) {
                const pasted = (event.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, otpInputs.length);
                if (!pasted) return;
                event.preventDefault();
                pasted.split('').forEach(function (digit, digitIndex) {
                    if (otpInputs[digitIndex]) {
                        otpInputs[digitIndex].value = digit;
                    }
                });
                otpInputs[Math.min(pasted.length, otpInputs.length) - 1].focus();
            });
        });

        function clearOtpInputs() {
            otpInputs.forEach(function (input) { input.value = ''; });
            if (otpInputs[0]) otpInputs[0].focus();
        }

        // Send OTP Handle
        async function handleSendOtp() {
            const phone = normalizePhone(phoneInput.value);
            if (phone.length !== 10) {
                setMsg(msgStep1, 'Please enter a valid 10-digit mobile number.', 'error');
                return;
            }

            setMsg(msgStep1, null);
            setMsg(msgStep2, null);
            sendOtpBtn.disabled = true;
            resendOtpBtn.disabled = true;
            const originalBtnText = sendOtpBtn.innerHTML;
            sendOtpBtn.innerHTML = 'Sending...';

            try {
                const result = await postForm('<?= base_url('users/send_google_signup_otp') ?>', { phone: phone });
                if (result.status) {
                    renderPhone(phone);
                    clearOtpInputs();
                    setMsg(msgStep2, result.message, 'success');
                    goToStep(2);
                } else {
                    setMsg(msgStep1, result.message, 'error');
                }
            } catch (error) {
                setMsg(msgStep1, 'Unable to send OTP right now. Please try again.', 'error');
            } finally {
                sendOtpBtn.innerHTML = originalBtnText;
                sendOtpBtn.disabled = false;
                resendOtpBtn.disabled = false;
            }
        }

        // Verify OTP Handle
        async function handleVerifyOtp() {
            const otp = otpInputs.map(function (input) { return input.value.trim(); }).join('');
            if (otp.length < 6) {
                setMsg(msgStep2, 'Please enter the complete 6-digit OTP.', 'error');
                return;
            }

            setMsg(msgStep2, null);
            verifyOtpBtn.disabled = true;
            const originalBtnText = verifyOtpBtn.innerHTML;
            verifyOtpBtn.innerHTML = 'Verifying...';

            try {
                const result = await postForm('<?= base_url('users/verify_google_signup_otp') ?>', { otp: otp });
                if (result.status) {
                    goToStep(3);
                    setTimeout(function () {
                        const redirectUrl = result.data && result.data.redirect_url ? result.data.redirect_url : '<?= base_url('analytics') ?>';
                        window.location.href = redirectUrl;
                    }, 1500); // Wait briefly so they see the success screen
                } else {
                    setMsg(msgStep2, result.message, 'error');
                }
            } catch (error) {
                setMsg(msgStep2, 'Unable to verify OTP right now. Please try again.', 'error');
            } finally {
                verifyOtpBtn.innerHTML = originalBtnText;
                verifyOtpBtn.disabled = false;
            }
        }

        // Attach listeners
        sendOtpBtn.addEventListener('click', handleSendOtp);
        resendOtpBtn.addEventListener('click', handleSendOtp);
        verifyOtpBtn.addEventListener('click', handleVerifyOtp);

        changeNumberBtn.addEventListener('click', function () {
            goToStep(1);
            phoneInput.focus();
        });

    })();
</script>