<title>Forgot Password | DAAKiT - India's Leading Fulfillment Platform</title>
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

    .divider {
        display: flex;
        align-items: center;
        gap: 12px;
        color: #b5b7b9;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        font-size: 0.75rem;
    }

    .divider::before,
    .divider::after {
        content: "";
        flex: 1;
        height: 1px;
        background: #e5e5e5;
    }

    input:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(4, 70, 219, 0.12);
    }

    html,
    body {
        height: 100%;
        width: 100%;
        margin: 0;
        padding: 0;
        overflow-x: hidden;
    }

    .auth-panel {
        width: 100%;
    }

    .signup-card {
        width: min(100%, 42rem);
        min-height: clamp(33rem, 78vh, 50rem);
    }

    .signup-heading {
        font-size: clamp(1.4rem, 1.25rem + 1.2vw, 3.2rem);
        line-height: 1.1;
        font-weight: 800;
        letter-spacing: -0.02em;
    }

    .signup-subtext {
        font-size: clamp(0.85rem, 0.82rem + 0.1vw, 1rem);
        line-height: 1.4;
        color: #64748b;
    }

    .signup-label {
        font-size: clamp(0.7rem, 0.65rem + 0.05vw, 0.75rem);
        letter-spacing: 0.08em;
        font-weight: 700;
        color: #64748b;
    }

    .signup-input {
        min-height: clamp(2.8rem, 2.6rem + 0.4vw, 3.6rem);
        font-size: clamp(0.9rem, 0.82rem + 0.1vw, 1rem);
    }

    .signup-button {
        min-height: clamp(3.2rem, 3rem + 0.5vw, 4rem);
        font-size: clamp(0.95rem, 0.92rem + 0.15vw, 1.1rem);
        font-weight: 700;
    }

    @media (min-width: 1024px) {
        .signup-card {
            width: 100%;
            max-width: 100%;
            height: 90vh;
            overflow-y: auto;
        }
    }

    /* Custom spacing for recovery card */
    .recovery-form-container {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
</style>

<body>
    <section class="auth-bg">
        <main class="lg:h-screen flex justify-center overflow-hidden" style="font-family: 'DM Sans', sans-serif;">
            <div class="flex flex-col lg:flex-row w-full lg:h-screen animate-fade-up">

                <!-- LEFT PANEL -->
                <div
                    class="relative hidden lg:flex flex-col w-[45%] xl:w-[50%] h-screen overflow-hidden px-12 pt-10 pb-12 rounded-br-[60px] rounded-tr-[60px]">
                    <div class="flex justify-between items-center w-full relative z-10">
                        <img src="<?= base_url('assets/images/logo-daakit.png') ?>" alt="DAAKit Logo"
                            class="w-36 h-auto" />
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

                <!-- RIGHT PANEL -->
                <div class="flex-1 flex flex-col relative px-4 bg-gray-50/30 lg:bg-transparent overflow-hidden">
                    <div class="auth-panel flex-1 flex justify-center items-center w-full py-4 overflow-hidden">
                        <div
                            class="signup-card w-full bg-white rounded-lg border border-gray-100 shadow-[0_20px_60px_-28px_rgba(15,23,42,0.28)] px-5 pb-8 sm:px-7 sm:pb-10 lg:px-12 lg:pb-12 xl:px-16 xl:pb-16 pt-8 sm:pt-10 xl:pt-12 relative z-10 animate-fade-up flex flex-col justify-center">

                            <div id="forgot-message" class="hidden mb-6 rounded-xl px-4 py-3 text-sm font-medium"></div>

                            <form action="<?= base_url('') ?>" method="POST" id="main-recovery-form"
                                class="recovery-form-container">

                                <!-- STEP 1: INITIAL IDENTIFIER -->
                                <div id="step-identifier" class="relative flex flex-col">
                                    <div class="mb-6 md:text-left lg:mb-8 xl:mb-14">
                                        <h2 class="signup-heading text-gray-900 tracking-tight">Forgot Password?</h2>
                                        <p id="associated-account-msg"
                                            class="hidden text-brand font-bold text-base mt-2 mb-1"></p>
                                        <p class="signup-subtext mt-3">Enter your phone number registered with DAAKiT to
                                            receive a verification code.</p>
                                    </div>

                                    <div class="space-y-6 md:space-y-8 xl:space-y-12">
                                        <div>
                                            <label class="signup-label block mb-2.5 uppercase tracking-[0.1em]"
                                                for="identity">
                                                PHONE NUMBER
                                            </label>
                                            <div class="relative group">
                                                <input id="identifier-input" name="identity" type="text"
                                                    placeholder="+91 XXXXX XXXXX"
                                                    class="signup-input w-full px-5 bg-[#F8FAFC] border-2 border-gray-100 rounded-lg text-gray-900 font-semibold placeholder-gray-400 focus:bg-white focus:border-brand/20 transition-all outline-none"
                                                    required />
                                            </div>
                                        </div>

                                        <button type="button" id="send-otp-btn"
                                            class="signup-button w-full bg-brand hover:bg-brand-dark text-white rounded-lg flex items-center justify-center gap-2 transition-all shadow-[0_12px_24px_-8px_rgba(4,70,219,0.4)] hover:-translate-y-1">
                                            Send Verification Code
                                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                            </svg>
                                        </button>

                                        <div class="divider my-4">Or</div>

                                        <a href="<?= base_url('oauth/all/google?flow=login') ?>"
                                            class="signup-button w-full bg-white border-2 border-gray-100 hover:border-gray-200 text-gray-700 rounded-lg flex items-center justify-center gap-3 transition-all no-underline">
                                            <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24">
                                                <path
                                                    d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                                                    fill="#4285F4" />
                                                <path
                                                    d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                                                    fill="#34A853" />
                                                <path
                                                    d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"
                                                    fill="#FBBC05" />
                                                <path
                                                    d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                                                    fill="#EA4335" />
                                            </svg>
                                            Sign in with Google
                                        </a>

                                        <div
                                            class="mt-6 xl:mt-10 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-gray-100 pt-8 xl:pt-12">
                                            <p class="text-slate-500 font-medium text-sm sm:text-base">Remember your
                                                password?</p>
                                            <a href="<?= base_url('users/login') ?>"
                                                class="text-brand font-bold text-sm sm:text-base hover:underline transition-all">
                                                Back to Login
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- STEP 2: OTP VERIFICATION -->
                                <div id="step-otp" class="relative hidden flex-col">
                                    <div class="mb-8 md:text-left">
                                        <h2 class="signup-heading text-gray-900 tracking-tight">Verification Required
                                        </h2>
                                        <p class="signup-subtext mt-3">
                                            We've sent a 6-digit code to <span id="display-phone"
                                                class="font-bold text-brand"></span>. Please enter it below.
                                        </p>
                                    </div>

                                    <div class="space-y-8">
                                        <div class="flex flex-nowrap justify-center gap-2 sm:gap-4 md:gap-5">
                                            <input type="text" maxlength="1"
                                                class="otp-input aspect-square w-full max-w-[48px] sm:max-w-[60px] md:max-w-[72px] rounded-lg border-2 border-gray-100 bg-[#F8FAFC] text-center text-2xl font-bold text-gray-900 transition-all outline-none focus:border-brand focus:bg-white focus:ring-4 focus:ring-brand/5"
                                                inputmode="numeric" />
                                            <input type="text" maxlength="1"
                                                class="otp-input aspect-square w-full max-w-[48px] sm:max-w-[60px] md:max-w-[72px] rounded-lg border-2 border-gray-100 bg-[#F8FAFC] text-center text-2xl font-bold text-gray-900 transition-all outline-none focus:border-brand focus:bg-white focus:ring-4 focus:ring-brand/5"
                                                inputmode="numeric" />
                                            <input type="text" maxlength="1"
                                                class="otp-input aspect-square w-full max-w-[48px] sm:max-w-[60px] md:max-w-[72px] rounded-lg border-2 border-gray-100 bg-[#F8FAFC] text-center text-2xl font-bold text-gray-900 transition-all outline-none focus:border-brand focus:bg-white focus:ring-4 focus:ring-brand/5"
                                                inputmode="numeric" />
                                            <input type="text" maxlength="1"
                                                class="otp-input aspect-square w-full max-w-[48px] sm:max-w-[60px] md:max-w-[72px] rounded-lg border-2 border-gray-100 bg-[#F8FAFC] text-center text-2xl font-bold text-gray-900 transition-all outline-none focus:border-brand focus:bg-white focus:ring-4 focus:ring-brand/5"
                                                inputmode="numeric" />
                                            <input type="text" maxlength="1"
                                                class="otp-input aspect-square w-full max-w-[48px] sm:max-w-[60px] md:max-w-[72px] rounded-lg border-2 border-gray-100 bg-[#F8FAFC] text-center text-2xl font-bold text-gray-900 transition-all outline-none focus:border-brand focus:bg-white focus:ring-4 focus:ring-brand/5"
                                                inputmode="numeric" />
                                            <input type="text" maxlength="1"
                                                class="otp-input aspect-square w-full max-w-[48px] sm:max-w-[60px] md:max-w-[72px] rounded-lg border-2 border-gray-100 bg-[#F8FAFC] text-center text-2xl font-bold text-gray-900 transition-all outline-none focus:border-brand focus:bg-white focus:ring-4 focus:ring-brand/5"
                                                inputmode="numeric" />
                                        </div>

                                        <div class="space-y-4 pt-4">
                                            <button type="button" id="verify-otp-btn"
                                                class="signup-button flex w-full items-center justify-center gap-3 rounded-lg bg-brand text-white shadow-[0_12px_24px_-8px_rgba(4,70,219,0.4)] transition-all hover:-translate-y-1 hover:bg-brand-dark">
                                                Verify & Continue
                                                <svg class="h-6 w-6" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                                </svg>
                                            </button>

                                            <button type="button" id="resend-otp-btn"
                                                class="signup-button flex w-full items-center justify-center gap-2 rounded-lg border-2 border-gray-100 bg-white text-gray-700 transition-all hover:bg-gray-50 hover:border-gray-200">
                                                Resend Code
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2.2"
                                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                            </button>
                                        </div>

                                        <div class="flex flex-col gap-6 pt-4">
                                            <div
                                                class="flex flex-row items-center justify-between gap-2 border-t border-gray-100 pt-8 text-[12px] sm:text-sm font-bold text-gray-400 flex-nowrap">
                                                <div class="flex items-center gap-3 sm:gap-6">
                                                    <a href="https://daakit.com/privacy-policy/"
                                                        class="transition-colors hover:text-gray-900 no-underline whitespace-nowrap uppercase tracking-wider">Privacy
                                                        Policy</a>
                                                    <a href="https://daakit.com/terms-and-conditions/"
                                                        class="transition-colors hover:text-gray-900 no-underline whitespace-nowrap uppercase tracking-wider">Terms
                                                        of Service</a>
                                                </div>
                                                <a href="https://daakit.com/contact/"
                                                    class="inline-flex items-center gap-1.5 transition-colors hover:text-gray-900 no-underline whitespace-nowrap">
                                                    <svg class="h-4 w-4 sm:h-5 sm:w-5 text-slate-300"
                                                        viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <circle cx="12" cy="12" r="9" stroke="currentColor"
                                                            stroke-width="2" fill="none" />
                                                        <path
                                                            d="M9.5 9a2.5 2.5 0 1 1 4.2 1.8c-.6.6-1.2 1-1.7 1.5-.3.3-.5.6-.5 1.2"
                                                            stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" fill="none" />
                                                        <circle cx="12" cy="17" r="1.5" fill="currentColor" />
                                                    </svg>
                                                    Need Support?
                                                </a>
                                            </div>

                                            <div
                                                class="flex gap-4 rounded-2xl bg-[#F8FAFC] p-5 sm:p-6 border border-gray-100">
                                                <div class="shrink-0 text-brand mt-1">
                                                    <svg class="h-6 w-6" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2.5"
                                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h4
                                                        class="mb-1 text-sm font-bold text-gray-900 uppercase tracking-wide">
                                                        Security Tip</h4>
                                                    <p
                                                        class="text-xs sm:text-sm font-medium leading-relaxed text-slate-500">
                                                        DAAKiT agents will <strong>never</strong> ask for this code.
                                                        Keep it private.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- STEP 3: NEW PASSWORD -->
                                <div id="step-new-password" class="relative hidden flex-col">
                                    <div class="mb-8 md:text-left">
                                        <h2 class="signup-heading text-gray-900 tracking-tight">Set New Password</h2>
                                        <p class="signup-subtext mt-3">Choose a strong, unique password to secure your
                                            DAAKiT account.</p>
                                    </div>

                                    <div class="space-y-6">
                                        <div>
                                            <label for="new-password"
                                                class="signup-label block mb-2.5 uppercase tracking-[0.1em]">
                                                New Password
                                            </label>
                                            <div class="relative group">
                                                <input id="new-password" name="password" type="password"
                                                    autocomplete="new-password" placeholder="••••••••"
                                                    class="signup-input w-full px-5 bg-[#F8FAFC] border-2 border-gray-100 rounded-lg text-gray-900 font-semibold placeholder-gray-400 focus:bg-white focus:border-brand/20 transition-all outline-none" />
                                                <button type="button" onclick="togglePwd('new-password', 'pwd-eye-1')"
                                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 transition-colors hover:text-brand">
                                                    <svg id="pwd-eye-1" class="h-5 w-5" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                                        <circle cx="12" cy="12" r="3" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <div>
                                            <label for="confirm-password"
                                                class="signup-label block mb-2.5 uppercase tracking-[0.1em]">
                                                Confirm New Password
                                            </label>
                                            <div class="relative group">
                                                <input id="confirm-password" name="passconf" type="password"
                                                    autocomplete="new-password" placeholder="••••••••"
                                                    class="signup-input w-full px-5 bg-[#F8FAFC] border-2 border-gray-100 rounded-lg text-gray-900 font-semibold placeholder-gray-400 focus:bg-white focus:border-brand/20 transition-all outline-none" />
                                                <button type="button"
                                                    onclick="togglePwd('confirm-password', 'pwd-eye-2')"
                                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 transition-colors hover:text-brand">
                                                    <svg id="pwd-eye-2" class="h-5 w-5" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                                        <circle cx="12" cy="12" r="3" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="bg-[#F8FAFC] p-5 rounded-2xl border border-gray-100">
                                            <p
                                                class="mb-3 text-sm font-bold text-slate-700 uppercase tracking-wide text-xs">
                                                Password Rules:</p>
                                            <div class="space-y-2.5">
                                                <div id="pwd-rule-length"
                                                    class="flex items-center gap-3 text-slate-500 font-medium text-sm">
                                                    <div
                                                        class="rule-icon w-5 h-5 flex items-center justify-center rounded-full bg-white border border-gray-200">
                                                        <div class="w-1.5 h-1.5 rounded-full bg-slate-300"></div>
                                                    </div>
                                                    <span>Min. 8 characters</span>
                                                </div>
                                                <div id="pwd-rule-uppercase"
                                                    class="flex items-center gap-3 text-slate-500 font-medium text-sm">
                                                    <div
                                                        class="rule-icon w-5 h-5 flex items-center justify-center rounded-full bg-white border border-gray-200">
                                                        <div class="w-1.5 h-1.5 rounded-full bg-slate-300"></div>
                                                    </div>
                                                    <span>One uppercase letter</span>
                                                </div>
                                                <div id="pwd-rule-number"
                                                    class="flex items-center gap-3 text-slate-500 font-medium text-sm">
                                                    <div
                                                        class="rule-icon w-5 h-5 flex items-center justify-center rounded-full bg-white border border-gray-200">
                                                        <div class="w-1.5 h-1.5 rounded-full bg-slate-300"></div>
                                                    </div>
                                                    <span>One numeric digit</span>
                                                </div>
                                            </div>
                                        </div>

                                        <button type="button" id="reset-password-btn"
                                            class="signup-button w-full bg-brand hover:bg-brand-dark text-white rounded-lg flex items-center justify-center gap-3 transition-all shadow-[0_12px_24px_-8px_rgba(4,70,219,0.4)] hover:-translate-y-1">
                                            Update Password & Login
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </form>
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
            <!-- Courier Track -->
            <div id="track-courier"
                class="logo-track absolute inset-0 flex transition-all duration-500 opacity-0 pointer-events-none translate-y-4">
                <!-- Set 1 -->
                <img src="<?= base_url('assets/TrustcourierLogo/TrustcourierLogo_01.png') ?>" class="logo-item"
                    alt="Courier 1">
                <img src="<?= base_url('assets/TrustcourierLogo/TrustcourierLogo_02.png') ?>" class="logo-item"
                    alt="Courier 2">
                <img src="<?= base_url('assets/TrustcourierLogo/TrustcourierLogo_03.png') ?>" class="logo-item"
                    alt="Courier 3">
                <img src="<?= base_url('assets/TrustcourierLogo/TrustcourierLogo_04.png') ?>" class="logo-item"
                    alt="Courier 4">
                <img src="<?= base_url('assets/TrustcourierLogo/TrustcourierLogo_05.png') ?>" class="logo-item"
                    alt="Courier 5">
                <img src="<?= base_url('assets/TrustcourierLogo/TrustcourierLogo_06.jpg') ?>" class="logo-item"
                    alt="Courier 6">
                <img src="<?= base_url('assets/TrustcourierLogo/TrustcourierLogo_07.png') ?>" class="logo-item"
                    alt="Courier 7">
                <img src="<?= base_url('assets/TrustcourierLogo/TrustcourierLogo_08.jpg') ?>" class="logo-item"
                    alt="Courier 8">
                <img src="<?= base_url('assets/TrustcourierLogo/TrustcourierLogo_09.png') ?>" class="logo-item"
                    alt="Courier 9">
                <img src="<?= base_url('assets/TrustcourierLogo/TrustcourierLogo_10.png') ?>" class="logo-item"
                    alt="Courier 10">


            </div>

            <!-- Channels Track -->
            <div id="track-channels"
                class="logo-track absolute inset-0 flex transition-all duration-500 opacity-0 pointer-events-none translate-y-4">
                <!-- Set 1 -->
                <img src="<?= base_url('assets/channels/channels_1.webp') ?>" class="logo-item" alt="Channel 1">
                <img src="<?= base_url('assets/channels/channels_2.webp') ?>" class="logo-item" alt="Channel 2">
                <img src="<?= base_url('assets/channels/channels_3.webp') ?>" class="logo-item" alt="Channel 3">
                <img src="<?= base_url('assets/channels/channels_4.webp') ?>" class="logo-item" alt="Channel 4">
                <img src="<?= base_url('assets/channels/channels_5.webp') ?>" class="logo-item" alt="Channel 5">
                <img src="<?= base_url('assets/channels/channels_6.webp') ?>" class="logo-item" alt="Channel 6">

            </div>

            <!-- Brand Track -->
            <div id="track-brand"
                class="logo-track absolute inset-0 flex transition-all duration-500 opacity-100 pointer-events-auto">
                <!-- Set 1 -->
                <img src="<?= base_url('assets/BrandLogo/BrandLogo_01.svg') ?>" class="logo-item" alt="Brand 1">
                <img src="<?= base_url('assets/BrandLogo/BrandLogo_02.svg') ?>" class="logo-item" alt="Brand 2">
                <img src="<?= base_url('assets/BrandLogo/BrandLogo_03.svg') ?>" class="logo-item" alt="Brand 3">
                <img src="<?= base_url('assets/BrandLogo/BrandLogo_04.svg') ?>" class="logo-item" alt="Brand 4">
                <img src="<?= base_url('assets/BrandLogo/BrandLogo_05.svg') ?>" class="logo-item" alt="Brand 5">
                <img src="<?= base_url('assets/BrandLogo/BrandLogo_01.svg') ?>" class="logo-item" alt="Brand 6">

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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const forgotState = {
            identity: '',
            otpVerified: false,
            associatedAccount: ''
        };

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

        function showMessage(message, type = 'error') {
            const box = document.getElementById('forgot-message');
            box.classList.remove('hidden', 'bg-red-100', 'text-red-700', 'bg-green-100', 'text-green-700');
            if (type === 'success') {
                box.classList.add('bg-green-100', 'text-green-700');
            } else {
                box.classList.add('bg-red-100', 'text-red-700');
            }
            box.textContent = message;
        }

        function clearMessage() {
            document.getElementById('forgot-message').classList.add('hidden');
        }

        function togglePwd(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" /><line x1="1" y1="1" x2="23" y2="23" />`;
            } else {
                input.type = 'password';
                icon.innerHTML = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" /><circle cx="12" cy="12" r="3" />`;
            }
        }

        function getPasswordRuleIcon(isValid) {
            if (isValid) {
                return `
                    <svg class="h-4 w-4" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="12" cy="12" r="8" fill="#2F9E44"></circle>
                        <path d="M8.5 12.2l2.2 2.2 4.8-5.1" fill="none" stroke="#FFFFFF" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                    </svg>
                `;
            }

            return `
                <svg class="h-4 w-4" viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="12" cy="12" r="7" fill="none" stroke="#C6C6C6" stroke-width="2"></circle>
                </svg>
            `;
        }

        function updatePasswordRules() {
            const passwordInput = document.getElementById('new-password');
            if (!passwordInput) return;

            const rules = [
                { id: 'pwd-rule-length', valid: passwordInput.value.length >= 8 },
                { id: 'pwd-rule-uppercase', valid: /[A-Z]/.test(passwordInput.value) },
                { id: 'pwd-rule-number', valid: /\d/.test(passwordInput.value) }
            ];

            rules.forEach((rule) => {
                const row = document.getElementById(rule.id);
                if (!row) return;

                row.style.color = rule.valid ? '#262626' : '#7A7A7A';

                const iconWrap = row.querySelector('.rule-icon');
                if (iconWrap) {
                    iconWrap.innerHTML = getPasswordRuleIcon(rule.valid);
                }
            });
        }

        function resetPasswordFields() {
            const passwordInput = document.getElementById('new-password');
            const confirmInput = document.getElementById('confirm-password');

            if (passwordInput) {
                passwordInput.value = '';
            }

            if (confirmInput) {
                confirmInput.value = '';
            }

            updatePasswordRules();
        }

        function showStep(stepId) {
            const steps = ['step-identifier', 'step-otp', 'step-new-password'];
            steps.forEach((id) => {
                const el = document.getElementById(id);
                if (!el) return;
                if (id === stepId) {
                    el.classList.remove('hidden');
                } else {
                    el.classList.add('hidden');
                }
            });

            // Reset scroll position of the card when switching steps
            const card = document.querySelector('.forgot-card');
            if (card) {
                card.scrollTop = 0;
            }
        }



        function goToOtp() {
            const identifier = document.getElementById('identifier-input').value.trim();
            if (!identifier) return alert('Please enter your email or phone number first');

            let displayId = identifier;
            if (/^\d+$/.test(identifier) && identifier.length === 10) {
                displayId = '+91 ' + identifier;
            }

            document.getElementById('display-phone').innerText = displayId;
            showStep('step-otp');
        }

        function goToNewPassword() {
            resetPasswordFields();
            showStep('step-new-password');
        }

        function goBack() {
            if (!document.getElementById('step-otp').classList.contains('hidden')) {
                showStep('step-identifier');
            } else if (!document.getElementById('step-new-password').classList.contains('hidden')) {
                showStep('step-otp');
            } else {
                window.location.href = "<?= base_url('users/login') ?>";
            }
        }

        // Auto-focus OTP inputs
        $(document).on('input', '.otp-input', function () {
            if (this.value.length === 1) {
                $(this).next('.otp-input').focus();
            }
        });

        $(document).on('keydown', '.otp-input', function (e) {
            if (e.key === 'Backspace' && !this.value) {
                $(this).prev('.otp-input').focus();
            }
        });

        function getOtpValue() {
            let otp = '';
            document.querySelectorAll('.otp-input').forEach((input) => {
                otp += input.value.trim();
            });
            return otp;
        }

        function clearOtpInputs() {
            document.querySelectorAll('.otp-input').forEach((input) => {
                input.value = '';
            });
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

        async function sendResetOtp() {
            clearMessage();

            const identity = document.getElementById('identifier-input').value.trim();
            if (!identity) {
                showMessage('Please enter your phone number.');
                return;
            }

            const sendOtpBtn = document.getElementById('send-otp-btn');
            const resendOtpBtn = document.getElementById('resend-otp-btn');

            sendOtpBtn.disabled = true;
            resendOtpBtn.disabled = true;

            try {
                const payload = /^\d+$/.test(identity) ? { phone: identity } : { email: identity };
                if (forgotState.associatedAccount) {
                    payload.associated_account = forgotState.associatedAccount;
                }
                const result = await postForm('<?= base_url('users/send_reset_otp') ?>', payload);

                if (!result.status) {
                    showMessage(result.message || 'Unable to send OTP.');
                    return;
                }

                forgotState.identity = identity;
                forgotState.otpVerified = false;

                let displayId = identity;
                if (/^\d+$/.test(identity)) {
                    const digits = identity.replace(/\D+/g, '');
                    if (digits.length === 10) {
                        displayId = '+91 ' + digits;
                    }
                }

                document.getElementById('display-phone').innerText = displayId;
                clearOtpInputs();
                showStep('step-otp');
                showMessage(result.message || 'OTP sent successfully.', 'success');
            } catch (error) {
                showMessage('Unable to send OTP right now. Please try again.');
            } finally {
                sendOtpBtn.disabled = false;
                resendOtpBtn.disabled = false;
            }
        }

        async function verifyResetOtp() {
            clearMessage();

            const otp = getOtpValue();
            if (otp.length !== 6) {
                showMessage('Please enter the 6-digit OTP.');
                return;
            }

            const verifyOtpBtn = document.getElementById('verify-otp-btn');
            verifyOtpBtn.disabled = true;

            try {
                const result = await postForm('<?= base_url('users/verify_reset_otp') ?>', { otp: otp });
                if (!result.status) {
                    showMessage(result.message || 'Invalid OTP.');
                    return;
                }

                forgotState.otpVerified = true;
                resetPasswordFields();
                showStep('step-new-password');
                showMessage(result.message || 'OTP verified successfully.', 'success');
            } catch (error) {
                showMessage('Unable to verify OTP right now. Please try again.');
            } finally {
                verifyOtpBtn.disabled = false;
            }
        }

        async function resetPassword() {
            clearMessage();

            if (!forgotState.otpVerified) {
                showMessage('Please verify OTP first.');
                return;
            }

            const password = document.getElementById('new-password').value;
            const passconf = document.getElementById('confirm-password').value;

            if (!password || !passconf) {
                showMessage('Please enter and confirm your new password.');
                return;
            }

            const resetPasswordBtn = document.getElementById('reset-password-btn');
            resetPasswordBtn.disabled = true;

            try {
                const result = await postForm('<?= base_url('users/reset_password') ?>', {
                    password: password,
                    passconf: passconf
                });

                if (!result.status) {
                    showMessage(result.message || 'Unable to reset password.');
                    return;
                }

                const redirectUrl = result.data && result.data.redirect_url ? result.data.redirect_url : '<?= base_url('analytics') ?>';
                showMessage(result.message || 'Password reset successful. Redirecting to dashboard...', 'success');
                setTimeout(() => {
                    window.location.href = redirectUrl;
                }, 1200);
            } catch (error) {
                showMessage('Unable to reset password right now. Please try again.');
            } finally {
                resetPasswordBtn.disabled = false;
            }
        }

        // Rotates the placeholder
        document.addEventListener("DOMContentLoaded", () => {
            document.getElementById('send-otp-btn').addEventListener('click', sendResetOtp);
            document.getElementById('resend-otp-btn').addEventListener('click', sendResetOtp);
            document.getElementById('verify-otp-btn').addEventListener('click', verifyResetOtp);
            document.getElementById('reset-password-btn').addEventListener('click', resetPassword);

            // Parse identity from URL
            const urlParams = new URLSearchParams(window.location.search);
            const identityFromUrl = urlParams.get('identity');
            if (identityFromUrl) {
                forgotState.associatedAccount = identityFromUrl;
                const msgEl = document.getElementById('associated-account-msg');
                if (msgEl) {
                    msgEl.innerText = `Recovering account for: ${identityFromUrl}`;
                    msgEl.classList.remove('hidden');
                }
            }

            const newPasswordInput = document.getElementById('new-password');
            if (newPasswordInput) {
                newPasswordInput.addEventListener('input', updatePasswordRules);
                updatePasswordRules();
            }

            const phoneInput = document.getElementById('identifier-input');
            if (!phoneInput) return;
            const placeholders = ["+91 XXXXX XXXXX", "Enter your phone number"];
            let currentIdx = 0, charIdx = 0, isDeleting = false, delay = 100;
            function typeWriter() {
                const currentString = placeholders[currentIdx];
                if (!isDeleting && charIdx <= currentString.length) {
                    phoneInput.setAttribute('placeholder', currentString.substring(0, charIdx++));
                    delay = 50;
                } else if (isDeleting && charIdx >= 0) {
                    phoneInput.setAttribute('placeholder', currentString.substring(0, charIdx--));
                    delay = 30;
                } else {
                    isDeleting = !isDeleting;
                    if (!isDeleting) currentIdx = (currentIdx + 1) % placeholders.length;
                    delay = isDeleting ? 2500 : 400;
                }
                setTimeout(typeWriter, delay);
            }
            setTimeout(typeWriter, 1000);
        });
    </script>

</body>

</html>