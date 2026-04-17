<title>Sign Up | DAAKiT - India's Leading Fulfillment Platform</title>
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
        width: min(100%, 42rem);
        min-height: clamp(36rem, 78vh, 50rem);
        border-radius: 0.5rem !important;
        /* Force rounded-lg */
    }

    .signup-heading {
        font-size: clamp(1.2rem, 1.1rem + 0.5vw, 1.6rem);
        line-height: 1.1;
        font-weight: 800;
        letter-spacing: -0.02em;
    }

    .signup-subtext {
        font-size: clamp(0.78rem, 0.75rem + 0.05vw, 0.82rem);
        line-height: 1.3;
        color: #64748b;
    }

    .signup-label {
        font-size: 0.65rem;
        letter-spacing: 0.05em;
        font-weight: 700;
        color: #64748B;
        margin-bottom: 2px !important;
    }

    .signup-input {
        min-height: clamp(2.2rem, 2rem + 0.2vw, 2.5rem);
        font-size: 0.8rem;
    }

    .signup-button {
        min-height: clamp(2.4rem, 2.2rem + 0.3vw, 2.7rem);
        font-size: 0.88rem;
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
            font-size: clamp(1.4rem, 1.2rem + 0.6vw, 1.9rem);
        }
    }

    /* ── MacBook Air (1280×800) ── */
    @media (min-width: 1280px) and (max-width: 1439px) {
        .signup-card {
            height: 90vh;
            overflow-y: auto;
            padding-top: 1.25rem;
            padding-bottom: 1.5rem;
        }

        .signup-subtext {
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
        }

        .signup-label {
            font-size: 0.7rem;
            margin-bottom: 4px !important;
        }

        .signup-input {
            min-height: 2.7rem;
            font-size: 0.88rem;
        }

        .signup-button {
            min-height: 2.9rem;
            font-size: 0.95rem;
        }
    }

    /* ── XL desktops (1440px+) ── */
    @media (min-width: 1440px) {
        .signup-subtext {
            font-size: 1rem;
        }

        .signup-label {
            font-size: 0.75rem;
        }

        .signup-input {
            min-height: 3.2rem;
            font-size: 1rem;
        }

        .signup-button {
            min-height: 3.6rem;
            font-size: 1.15rem;
        }
    }
</style>


<section class="auth-bg">
    <main class=" lg:h-screen flex justify-center overflow-hidden" style="font-family: 'DM Sans', sans-serif;">
        <div class="flex flex-col lg:flex-row w-full lg:h-screen animate-fade-up">

            <!-- ════ LEFT PANEL (Branding) ════ -->
            <div
                class="relative hidden lg:flex flex-col w-[45%] xl:w-[50%] h-screen overflow-hidden px-12 pt-10 pb-12 rounded-br-[60px] rounded-tr-[60px]">
                <div class="flex justify-between items-center w-full relative z-10">
                    <img onclick="window.location.href='<?= base_url() ?>'"
                        src="<?= base_url('assets/images/logo-daakit.png') ?>" alt="DAAKit Logo"
                        class="w-36 h-auto cursor-pointer" />
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
                <div
                    class="auth-panel flex-1 flex justify-center items-center w-full py-2 mt-6 lg:mt-8 overflow-hidden">
                    <div
                        class="signup-card w-full bg-white rounded-lg border border-gray-100 shadow-[0_20px_60px_-28px_rgba(15,23,42,0.28)] px-5 pb-3 sm:pb-6 lg:px-8 lg:pb-5 xl:px-10 xl:pb-6 2xl:px-12 2xl:pb-8 pt-[16px] xl:pt-4 2xl:pt-6 relative z-10 animate-fade-up flex flex-col justify-start">


                        <!-- Header (Back & Login) -->
                        <div class="px-1 sm:px-1.5 flex shrink-0 justify-between items-center bg-white z-10">
                            <div
                                class="text-xs sm:text-sm xl:text-sm 2xl:text-lg font-medium flex items-center justify-between w-full border-b border-gray-100 pb-0.5 xl:pb-2 2xl:pb-4">
                                <span class="text-gray-500">Already have an account?</span>
                                <a href="<?= base_url('users/login') ?>"
                                    class="text-[#0345DA] hover:underline font-bold flex items-center gap-1.5 no-underline">
                                    Login
                                </a>
                            </div>
                        </div>

                        <div
                            class="flex-1 overflow-y-auto hide-scrollbar px-1 sm:px-2 py-1 flex flex-col justify-start items-center xl:mt-2 xl:gap-2 2xl:mt-3 2xl:gap-4">

                            <!-- STEP 1: Details -->
                            <div id="step-1" class="step-content active flex-col w-full max-w-full mx-auto md:mx-0">

                                <!-- Progress Stepper -->
                                <div id="signup-stepper"
                                    class="flex items-start gap-1 sm:gap-2 mb-1.5 sm:mb-2 xl:mb-2.5 2xl:mb-4 mt-1">
                                    <div class="flex flex-col items-center min-w-[50px]">
                                        <div
                                            class="w-7 h-7 sm:w-8 sm:h-8 rounded-[10px] sm:rounded-[14px] border-2 border-[#198038] bg-white flex items-center justify-center text-[#198038] text-[10px] sm:text-xs font-bold shadow-sm">
                                            1</div>
                                        <span
                                            class="mt-1 text-[10px] sm:text-[11px] font-bold text-gray-900">Details</span>
                                    </div>
                                    <div class="flex-1 h-[1.5px] bg-gray-100 mt-3.5 sm:mt-4"></div>
                                    <div class="flex flex-col items-center min-w-[50px]">
                                        <div
                                            class="w-7 h-7 sm:w-8 sm:h-8 rounded-[10px] sm:rounded-[14px] bg-gray-100 flex items-center justify-center text-gray-400 text-[10px] sm:text-xs font-bold">
                                            2</div>
                                        <span
                                            class="mt-1 text-[10px] sm:text-[11px] font-medium text-gray-400">Verify</span>
                                    </div>
                                    <div class="flex-1 h-[1.5px] bg-gray-100 mt-3.5 sm:mt-4"></div>
                                    <div class="flex flex-col items-center min-w-[50px]">
                                        <div
                                            class="w-7 h-7 sm:w-8 sm:h-8 rounded-[10px] sm:rounded-[14px] bg-gray-100 flex items-center justify-center text-gray-400 text-[10px] sm:text-xs font-bold">
                                            3</div>
                                        <span
                                            class="mt-1 text-[10px] sm:text-[11px] font-medium text-gray-400">Done</span>
                                    </div>
                                </div>

                                <h2 class="signup-heading mb-0">Let's Get Started!</h2>
                                <p class="signup-subtext mb-1">Set up in minutes. Scale for years.</p>

                                <!-- Role Toggle -->
                                <!-- Optimized Role Toggle -->
                                <div
                                    class="flex bg-gray-100/50 p-1 2xl:p-2 rounded-lg border border-gray-200 mb-2 xl:mb-3 2xl:mb-4 w-full text-xs">
                                    <button type="button" id="role-seller"
                                        class="flex-1 py-1.5 sm:py-2 2xl:py-4 text-[10px] sm:text-xs 2xl:text-base font-bold rounded-lg transition-all shadow-sm bg-brand text-white"
                                        onclick="toggleRole('seller')">Seller</button>
                                    <button type="button" id="role-buyer"
                                        class="flex-1 py-1.5 sm:py-2 2xl:py-4 text-[10px] sm:text-xs 2xl:text-base font-bold rounded-lg transition-all text-gray-500 hover:text-gray-700"
                                        onclick="toggleRole('buyer')">Buyer</button>
                                </div>


                                <!-- SELLER FORM -->
                                <form id="form-seller" class="2xl:space-y-4">
                                    <div id="signup-step-1-message"
                                        class="hidden mb-1 rounded-xl px-3 py-1.5 text-xs font-medium">
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-1">
                                        <div>
                                            <label
                                                class="signup-label block font-bold text-gray-600 mb-1.5 uppercase">FULL
                                                NAME</label>
                                            <input id="signup_full_name" type="text" placeholder="Dev"
                                                class="signup-input w-full px-3.5 sm:px-4 py-2 sm:py-2.5 border border-gray-200 rounded-xl text-xs sm:text-sm bg-[#F8F9FB] focus:bg-white transition-all outline-none" />
                                        </div>
                                        <div>
                                            <label
                                                class="signup-label block font-bold text-gray-600 mb-1.5 uppercase">WORK
                                                EMAIL</label>
                                            <input id="signup_email" type="email" placeholder="Dev@company.com"
                                                class="signup-input w-full px-3.5 sm:px-4 py-2 sm:py-2.5 border border-gray-200 rounded-xl text-xs sm:text-sm bg-[#F8F9FB] focus:bg-white transition-all outline-none" />
                                        </div>
                                    </div>

                                    <div class="mb-2.5">
                                        <label
                                            class="signup-label block font-bold text-gray-600 mb-1.5 uppercase">MOBILE
                                            NUMBER</label>
                                        <div
                                            class="flex gap-0 border border-gray-200 rounded-xl overflow-hidden focus-within:shadow-sm focus-within:border-brand transition-all">
                                            <span
                                                class="bg-[#F1F3F7] px-3 sm:px-3.5 py-2 sm:py-2.5 flex items-center text-gray-700 font-bold border-r border-gray-200 text-xs sm:text-sm">+91</span>
                                            <input id="signup_phone" type="tel" maxlength="10"
                                                placeholder="91 XXXXX XXXXX"
                                                class="signup-input flex-1 px-3.5 sm:px-4 py-2 sm:py-2.5 text-xs sm:text-sm bg-[#F8F9FB] focus:bg-white outline-none w-full" />
                                        </div>
                                    </div>

                                    <div class="mb-1.5">
                                        <label
                                            class="signup-label block font-bold text-gray-600 mb-0.5 uppercase">CREATE
                                            PASSWORD</label>
                                        <div class="relative">
                                            <input id="signup_password" type="password" placeholder="Enter password"
                                                value="" autocomplete="new-password" spellcheck="false"
                                                class="signup-input w-full px-3.5 sm:px-4 py-2 sm:py-2.5 border border-gray-200 rounded-xl text-xs sm:text-sm bg-[#F8F9FB] focus:bg-white transition-all outline-none" />
                                            <button type="button" onclick="toggleSignupPassword()"
                                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none">
                                                <svg id="signup-password-eye" class="w-4 h-4 sm:w-5 sm:h-5"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                                    <circle cx="12" cy="12" r="3" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="mt-2.5 flex items-center justify-between">

                                            <div class="flex-1 flex gap-1.5">
                                                <div id="signup-password-bar-1"
                                                    class="h-1.5 flex-1 rounded-full bg-gray-100 transition-all"></div>
                                                <div id="signup-password-bar-2"
                                                    class="h-1.5 flex-1 rounded-full bg-gray-100 transition-all"></div>
                                                <div id="signup-password-bar-3"
                                                    class="h-1.5 flex-1 rounded-full bg-gray-100 transition-all"></div>
                                                <div id="signup-password-bar-4"
                                                    class="h-1.5 flex-1 rounded-full bg-gray-100 transition-all"></div>
                                            </div>
                                            <span id="signup-password-strength"
                                                class="ml-3 text-[9px] font-bold text-gray-400 uppercase tracking-tighter">Enter
                                                password</span>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-2 mb-1.5">
                                        <div class="pt-1">
                                            <input type="checkbox" id="terms"
                                                class="w-3.5 h-3.5 rounded border-gray-300 text-brand focus:ring-brand"
                                                checked />
                                        </div>
                                        <label for="terms"
                                            class="text-[10px] sm:text-[11px] text-gray-600 leading-tight font-medium">
                                            I agree to the <a href="https://daakit.com/terms-and-conditions/"
                                                class="text-brand font-bold underline">Terms</a>
                                            and
                                            <a href="https://daakit.com/privacy-policy/"
                                                class="text-brand font-bold underline">Privacy Policy</a>
                                        </label>
                                    </div>

                                    <button id="signup-send-otp-btn" type="button"
                                        class="signup-button w-full bg-brand hover:bg-brand-dark text-white font-bold rounded-xl flex items-center justify-center gap-2 transition-all shadow-md py-3 sm:py-3.5 active:scale-[0.98]">
                                        Signup
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                            <polyline points="12 5 19 12 12 19"></polyline>
                                        </svg>
                                    </button>

                                    <div
                                        class="divider text-[#b5b7b9] font-medium text-[10px] my-1 px-1 text-center italic">
                                        Or</div>

                                    <a href="<?= base_url('oauth/all/google?flow=signup') ?>"
                                        class="signup-button w-full bg-white border border-gray-200 hover:border-gray-300 hover:bg-gray-50 text-gray-700 font-bold rounded-xl flex items-center justify-center gap-2 transition-all shadow-sm py-2.5 sm:py-3 no-underline">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" viewBox="0 0 24 24">
                                            <path
                                                d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                                                fill="#4285F4" />
                                            <path
                                                d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                                                fill="#34A853" />
                                            <path
                                                d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                                                fill="#FBBC05" />
                                            <path
                                                d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                                                fill="#EA4335" />
                                        </svg>
                                        Sign up with Google
                                    </a>
                                </form>

                                <!-- BUYER FORM -->
                                <form id="form-buyer" class="hidden mt-[5px] w-full">
                                    <h3 class="signup-heading text-lg sm:text-xl font-bold mb-2">Enter Tracking Details
                                    </h3>
                                    <p class="signup-subtext mb-6">Select your identification method to begin.</p>

                                    <div class="flex gap-3 sm:gap-4 mb-2">
                                        <label class="flex items-center gap-2 sm:gap-3 cursor-pointer group">
                                            <input type="radio" name="track_type" checked
                                                class="w-4 h-4 sm:w-5 sm:h-5 text-brand focus:ring-brand border-gray-300" />
                                            <span
                                                class="text-[10px] sm:text-sm font-bold text-gray-900 group-hover:text-brand transition-colors whitespace-nowrap">Order
                                                ID</span>
                                        </label>
                                        <label class="flex items-center gap-2 sm:gap-3 cursor-pointer group">
                                            <input type="radio" name="track_type"
                                                class="w-4 h-4 sm:w-5 sm:h-5 text-brand focus:ring-brand border-gray-300" />
                                            <span
                                                class="text-[10px] sm:text-sm font-bold text-gray-500 group-hover:text-brand transition-colors whitespace-nowrap">AWB
                                                Number</span>
                                        </label>
                                    </div>

                                    <div class="mb-2">
                                        <label class="signup-label block font-bold text-gray-600 mb-1 uppercase">ORDER
                                            ID
                                            / AWB NUMBER</label>
                                        <div class="relative">
                                            <div class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400">
                                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">

                                                    <!-- Corner Top Left -->
                                                    <path d="M4 8V5h3" stroke="#7F95A6" stroke-width="1.8"
                                                        stroke-linecap="round" stroke-linejoin="round" />

                                                    <!-- Corner Top Right -->
                                                    <path d="M20 8V5h-3" stroke="#7F95A6" stroke-width="1.8"
                                                        stroke-linecap="round" stroke-linejoin="round" />

                                                    <!-- Corner Bottom Left -->
                                                    <path d="M4 16v3h3" stroke="#7F95A6" stroke-width="1.8"
                                                        stroke-linecap="round" stroke-linejoin="round" />

                                                    <!-- Corner Bottom Right -->
                                                    <path d="M20 16v3h-3" stroke="#7F95A6" stroke-width="1.8"
                                                        stroke-linecap="round" stroke-linejoin="round" />

                                                    <!-- Barcode Bars -->
                                                    <rect x="7" y="7" width="1.2" height="10" rx="0.6" fill="#7F95A6" />
                                                    <rect x="9" y="7" width="1.8" height="10" rx="0.9" fill="#7F95A6" />
                                                    <rect x="11.8" y="7" width="1.2" height="10" rx="0.6"
                                                        fill="#7F95A6" />
                                                    <rect x="14" y="7" width="1.8" height="10" rx="0.9"
                                                        fill="#7F95A6" />
                                                    <rect x="16.8" y="7" width="1.2" height="10" rx="0.6"
                                                        fill="#7F95A6" />

                                                </svg>
                                            </div>
                                            <input type="text" placeholder="e.g. LOG-98234123"
                                                class="signup-input w-full pl-11 sm:pl-12 pr-3.5 sm:pr-4 border border-gray-200 rounded-xl text-xs sm:text-sm bg-[#F8F9FB] focus:bg-white transition-all outline-none" />
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="signup-label block font-bold text-gray-600 mb-1 uppercase">MOBILE
                                            NUMBER</label>
                                        <div class="relative">
                                            <div class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400">
                                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <!-- Phone body -->
                                                    <rect x="5" y="3" width="14" height="18" rx="2" stroke="#777"
                                                        stroke-width="2" />

                                                    <!-- Side button -->
                                                    <rect x="19" y="8" width="1" height="4" rx="0.5" fill="#777" />

                                                    <!-- Bottom speaker -->
                                                    <rect x="9" y="17" width="6" height="1.5" rx="0.75" fill="#777" />
                                                </svg>
                                            </div>
                                            <input type="text" maxlength="10" placeholder="+1 (555) 000-0000"
                                                class="signup-input w-full pl-11 sm:pl-12 pr-3.5 sm:pr-4 border border-gray-200 rounded-xl text-xs sm:text-sm bg-[#F8F9FB] focus:bg-white transition-all outline-none" />
                                        </div>
                                    </div>

                                    <button type="button" onclick=""
                                        class="signup-button w-full bg-brand hover:bg-brand-dark text-white rounded-xl flex items-center justify-center gap-2 transition-all shadow-md">
                                        Track Order
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                        </svg>
                                    </button>

                                    <div
                                        class="mt-2.5 sm:mt-4 flex items-center justify-center gap-2 text-gray-400 text-[10px] font-medium">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2">
                                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                        </svg>
                                        Your information is encrypted and secure
                                    </div>
                                </form>
                            </div>

                            <!-- STEP 2: Verify -->
                            <div id="step-2"
                                class="step-content flex-col w-full max-w-full mx-auto md:mx-0 pt-4 sm:pt-6">
                                <div class="flex items-start gap-1 sm:gap-2 mb-4 sm:mb-6 xl:mb-8">
                                    <div class="flex flex-col items-center min-w-[50px]">
                                        <div
                                            class="w-8 h-8 sm:w-9 sm:h-9 rounded-[12px] sm:rounded-[14px] bg-[#198038] flex items-center justify-center text-white shadow-sm">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="20 6 9 17 4 12" />
                                            </svg>
                                        </div>
                                        <span
                                            class="mt-2 text-[10px] sm:text-[11px] font-medium text-gray-500">Details</span>
                                    </div>
                                    <div class="flex-1 h-[1.5px] bg-[#198038] mt-6 sm:mt-[18px]"></div>
                                    <div class="flex flex-col items-center min-w-[50px]">
                                        <div
                                            class="w-8 h-8 sm:w-9 sm:h-9 rounded-[12px] sm:rounded-[14px] border-2 border-[#198038] bg-white flex items-center justify-center text-[#198038] text-xs sm:text-sm font-bold shadow-sm">
                                            2</div>
                                        <span
                                            class="mt-2 text-[10px] sm:text-[11px] font-bold text-gray-900">Verify</span>
                                    </div>
                                    <div class="flex-1 h-[1.5px] bg-gray-100 mt-6 sm:mt-[18px]"></div>
                                    <div class="flex flex-col items-center min-w-[50px]">
                                        <div
                                            class="w-8 h-8 sm:w-9 sm:h-9 rounded-[12px] sm:rounded-[14px] bg-gray-100 flex items-center justify-center text-gray-400 text-xs sm:text-sm font-bold">
                                            3</div>
                                        <span
                                            class="mt-2 text-[10px] sm:text-[11px] font-medium text-gray-400">Done</span>
                                    </div>
                                </div>

                                <div class="flex justify-center mb-1 sm:mb-1.5 mt-0">
                                    <div
                                        class="w-16 h-20 bg-[#F3F4F6] rounded-lg flex items-center justify-center shrink-0">
                                        <svg class="w-14 h-[58px] sm:w-[50px] sm:h-[64px]" viewBox="0 0 56 76"
                                            fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <rect x="11" y="4" width="34" height="64" rx="8.5" fill="#155BD5" />
                                            <rect x="17" y="12" width="22" height="38" rx="2.5" fill="white" />
                                            <circle cx="28" cy="57.5" r="4.5" fill="white" />
                                        </svg>
                                    </div>
                                </div>

                                <h2 class="signup-heading text-center mb-0">Check your phone</h2>
                                <p class="signup-subtext text-center mb-1.5 sm:mb-2 text-[10px] sm:text-xs">Enter code
                                    sent to <span id="signup-display-phone" class="text-gray-900 font-bold"></span>
                                </p>

                                <div id="signup-step-2-message"
                                    class="hidden mb-1.5 rounded-lg px-4 py-1.5 text-xs font-medium"></div>

                                <!-- OTP inputs -->
                                <div class="flex justify-center gap-1 sm:gap-1.5 mb-1.5 sm:mb-2">
                                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*"
                                        class="signup-otp-input w-8 h-8 sm:w-10 sm:h-10 shrink-0 border-2 border-gray-100 bg-[#F8F9FB] rounded-lg text-center text-base sm:text-lg font-bold text-gray-900 focus:border-brand focus:bg-white transition-all outline-none" />
                                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*"
                                        class="signup-otp-input w-8 h-8 sm:w-10 sm:h-10 shrink-0 border-2 border-gray-100 bg-[#F8F9FB] rounded-lg text-center text-base sm:text-lg font-bold text-gray-900 focus:border-brand focus:bg-white transition-all outline-none" />
                                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*"
                                        class="signup-otp-input w-8 h-8 sm:w-10 sm:h-10 shrink-0 border-2 border-gray-100 bg-[#F8F9FB] rounded-lg text-center text-base sm:text-lg font-bold text-gray-900 focus:border-brand focus:bg-white transition-all outline-none" />
                                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*"
                                        class="signup-otp-input w-8 h-8 sm:w-10 sm:h-10 shrink-0 border-2 border-gray-100 bg-[#F8F9FB] rounded-lg text-center text-base sm:text-lg font-bold text-gray-900 focus:border-brand focus:bg-white transition-all outline-none" />
                                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*"
                                        class="signup-otp-input w-8 h-8 sm:w-10 sm:h-10 shrink-0 border-2 border-gray-100 bg-[#F8F9FB] rounded-lg text-center text-base sm:text-lg font-bold text-gray-900 focus:border-brand focus:bg-white transition-all outline-none" />
                                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*"
                                        class="signup-otp-input w-8 h-8 sm:w-10 sm:h-10 shrink-0 border-2 border-gray-100 bg-[#F8F9FB] rounded-lg text-center text-base sm:text-lg font-bold text-gray-900 focus:border-brand focus:bg-white transition-all outline-none" />
                                </div>

                                <button id="signup-verify-otp-btn" type="button"
                                    class="signup-button w-full bg-brand hover:bg-brand-dark text-white font-bold rounded-lg flex items-center justify-center gap-2 transition-all shadow-md py-3 sm:py-3.5 mb-1 xl:mb-4">
                                    Verify & continue
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                        <polyline points="12 5 19 12 12 19"></polyline>
                                    </svg>
                                </button>

                                <button id="signup-change-number-btn" type="button"
                                    class="signup-button w-full bg-white border border-gray-200 hover:border-gray-300 text-gray-600 font-bold rounded-lg flex items-center justify-center gap-2 transition-all py-3 sm:py-3.5">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="19" y1="12" x2="5" y2="12"></line>
                                        <polyline points="12 19 5 12 12 5"></polyline>
                                    </svg>
                                    Change number
                                </button>

                                <div class="mt-6 text-center text-gray-400 font-medium text-xs sm:text-sm">
                                    Didn't receive it? <span id="signup-otp-timer-container">Resend code in <span
                                            id="signup-otp-timer">30</span>s</span>
                                    <button id="signup-resend-otp-btn" type="button"
                                        class="text-brand font-bold bg-transparent border-0 p-0 hidden">Resend
                                        code</button>
                                </div>
                            </div>

                            <!-- STEP 3: Done -->
                            <div id="step-3"
                                class="step-content flex-col w-full max-w-full mx-auto md:mx-0 pt-4 sm:pt-6">
                                <div class="flex items-start gap-1 sm:gap-2 mb-4 sm:mb-6 xl:mb-8">
                                    <div class="flex flex-col items-center min-w-[50px]">
                                        <div
                                            class="w-8 h-8 sm:w-9 sm:h-9 rounded-[12px] sm:rounded-[14px] bg-[#198038] flex items-center justify-center text-white shadow-sm">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="20 6 9 17 4 12" />
                                            </svg>
                                        </div>
                                        <span
                                            class="mt-2 text-[10px] sm:text-[11px] font-medium text-gray-500">Details</span>
                                    </div>
                                    <div class="flex-1 h-[1.5px] bg-[#198038] mt-6 sm:mt-[18px]"></div>
                                    <div class="flex flex-col items-center min-w-[50px]">
                                        <div
                                            class="w-8 h-8 sm:w-9 sm:h-9 rounded-[12px] sm:rounded-[14px] bg-[#198038] flex items-center justify-center text-white shadow-sm">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="20 6 9 17 4 12" />
                                            </svg>
                                        </div>
                                        <span
                                            class="mt-2 text-[10px] sm:text-[11px] font-medium text-gray-500">Verify</span>
                                    </div>
                                    <div class="flex-1 h-[1.5px] bg-[#198038] mt-6 sm:mt-[18px]"></div>
                                    <div class="flex flex-col items-center min-w-[50px]">
                                        <div
                                            class="w-8 h-8 sm:w-9 sm:h-9 rounded-[12px] sm:rounded-[14px] border-2 border-[#198038] bg-white flex items-center justify-center text-[#198038] text-xs sm:text-sm font-bold shadow-sm">
                                            3</div>
                                        <span
                                            class="mt-2 text-[10px] sm:text-[11px] font-bold text-gray-900">Done</span>
                                    </div>
                                </div>

                                <div class="flex justify-center mb-3 sm:mb-4 mt-1">
                                    <div
                                        class="w-14 h-14 sm:w-16 sm:h-16 bg-[#A7F3C0] rounded-[18px] flex items-center justify-center">
                                        <div
                                            class="w-8 h-8 sm:w-10 sm:h-10 bg-[#15803D] rounded-full flex items-center justify-center text-white">
                                            <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2.8" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <polyline points="20 7 10 17 5 12" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <h2 class="signup-heading text-center mb-0.5">You're all set!</h2>
                                <p class="signup-subtext text-center mx-auto mb-4 max-w-[480px]">Your account has been
                                    created and mobile number verified successfully.</p>

                                <a id="signup-dashboard-link" href="<?= base_url('analytics') ?>"
                                    class="signup-button w-full max-w-[560px] mx-auto inline-flex items-center justify-center gap-2.5 px-6 rounded-2xl bg-brand hover:bg-brand-dark text-white font-bold tracking-[0.01em] transition-all shadow-xl shadow-blue-100 hover:shadow-2xl active:scale-[0.98] no-underline">
                                    Go to dashboard
                                    <svg class="w-5 h-5 sm:w-[22px] sm:h-[22px]" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                        <polyline points="12 5 19 12 12 19"></polyline>
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
            TRUSTED BY 500+ D2C
            BRAND PARTNERS.</p>
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

        // Reset all buttons
        buttons.forEach(btn => {
            if (btn) {
                btn.classList.remove('bg-[#0345DA]', 'text-white', 'shadow-lg');
                btn.classList.add('text-slate-500');
            }
        });

        // Reset all tracks
        tracks.forEach(track => {
            if (track) {
                track.classList.add('opacity-0', 'pointer-events-none', 'translate-y-4');
                track.classList.remove('opacity-100', 'pointer-events-auto');
            }
        });

        // Set active state
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


    function setActive(el) {
        document.querySelectorAll('#toggleBtn button').forEach(btn => {
            btn.classList.remove('bg-blue-600', 'text-white');
            btn.classList.add('text-gray-700');
        });
        el.classList.add('bg-blue-600', 'text-white');
    }
    // JS for Role Toggle
    function toggleRole(role) {
        const sellerBtn = document.getElementById('role-seller');
        const buyerBtn = document.getElementById('role-buyer');
        const sellerForm = document.getElementById('form-seller');
        const buyerForm = document.getElementById('form-buyer');
        const signupStepper = document.getElementById('signup-stepper');

        if (role === 'seller') {
            sellerBtn.classList.add('bg-brand', 'text-white', 'shadow-md');
            sellerBtn.classList.remove('text-gray-500', 'hover:text-gray-700', 'bg-white', 'text-brand');
            buyerBtn.classList.remove('bg-brand', 'text-white', 'shadow-md');
            buyerBtn.classList.add('text-gray-500', 'hover:text-gray-700');

            buyerForm.classList.add('hidden');
            buyerForm.style.display = 'none';
            sellerForm.classList.remove('hidden');
            sellerForm.style.display = 'block';
            if (signupStepper) {
                signupStepper.classList.remove('hidden');
                signupStepper.style.display = 'flex';
            }

            // Re-trigger fade animation
            sellerForm.classList.remove('animate-fade-up');
            void sellerForm.offsetWidth; // Force reflow
            sellerForm.classList.add('animate-fade-up');
        } else {
            buyerBtn.classList.add('bg-brand', 'text-white', 'shadow-md');
            buyerBtn.classList.remove('text-gray-500', 'hover:text-gray-700', 'bg-white', 'text-brand');
            sellerBtn.classList.remove('bg-brand', 'text-white', 'shadow-md');
            sellerBtn.classList.add('text-gray-500', 'hover:text-gray-700');

            sellerForm.classList.add('hidden');
            sellerForm.style.display = 'none';
            buyerForm.classList.remove('hidden');
            buyerForm.style.display = 'block';
            if (signupStepper) {
                signupStepper.classList.add('hidden');
                signupStepper.style.display = 'none';
            }

            // Re-trigger fade animation
            buyerForm.classList.remove('animate-fade-up');
            void buyerForm.offsetWidth; // Force reflow
            buyerForm.classList.add('animate-fade-up');
        }
    }

    // JS for Step Switching (Demo Flow)
    function goToStep(step) {
        document.querySelectorAll('.step-content').forEach(s => s.classList.remove('active'));
        document.getElementById(`step-${step}`).classList.add('active');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
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
    function toggleSignupPassword() {
        const input = document.getElementById('signup_password');
        const icon = document.getElementById('signup-password-eye');
        if (!input || !icon) return;

        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" /><line x1="1" y1="1" x2="23" y2="23" />`;
        } else {
            input.type = 'password';
            icon.innerHTML = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" /><circle cx="12" cy="12" r="3" />`;
        }
    }

    function setSignupMessage(elementId, message, type) {
        const el = document.getElementById(elementId);
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

    function normalizeSignupPhone(phone) {
        return (phone || '').replace(/\D/g, '').slice(-10);
    }

    function formatSignupPhone(phone) {
        const digits = normalizeSignupPhone(phone);
        if (digits.length !== 10) {
            return '+91 ' + digits;
        }

        return `+91 ${digits.slice(0, 5)} ${digits.slice(5)}`;
    }

    async function signupPost(url, payload) {
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

    // Rotates the placeholder of the mobile input automatically and wires signup OTP flow
    document.addEventListener("DOMContentLoaded", () => {
        const phoneInput = document.getElementById('signup_phone');
        if (!phoneInput) return;
        const fullNameInput = document.getElementById('signup_full_name');
        const emailInput = document.getElementById('signup_email');
        const passwordInput = document.getElementById('signup_password');
        const passwordStrengthLabel = document.getElementById('signup-password-strength');
        const passwordBars = [
            document.getElementById('signup-password-bar-1'),
            document.getElementById('signup-password-bar-2'),
            document.getElementById('signup-password-bar-3'),
            document.getElementById('signup-password-bar-4')
        ];
        const termsInput = document.getElementById('terms');
        const sendOtpBtn = document.getElementById('signup-send-otp-btn');
        const verifyOtpBtn = document.getElementById('signup-verify-otp-btn');
        const resendOtpBtn = document.getElementById('signup-resend-otp-btn');
        const changeNumberBtn = document.getElementById('signup-change-number-btn');
        const dashboardLink = document.getElementById('signup-dashboard-link');
        const displayPhone = document.getElementById('signup-display-phone');
        const otpInputs = Array.from(document.querySelectorAll('.signup-otp-input'));
        let signupState = null;

        const placeholders = [
            "XXXXX XXXXX",
            "Enter your company phone number"
        ];

        let currentIdx = 0;
        let charIdx = 0;
        let isDeleting = false;
        let delay = 100;

        function typeWriter() {
            const currentString = placeholders[currentIdx];
            if (!isDeleting && charIdx <= currentString.length) {
                phoneInput.setAttribute('placeholder', currentString.substring(0, charIdx));
                charIdx++;
                delay = 50;
            } else if (isDeleting && charIdx >= 0) {
                phoneInput.setAttribute('placeholder', currentString.substring(0, charIdx));
                charIdx--;
                delay = 30;
            } else {
                isDeleting = !isDeleting;
                if (!isDeleting) {
                    currentIdx = (currentIdx + 1) % placeholders.length;
                    delay = 400;
                } else {
                    delay = 2500;
                }
            }
            setTimeout(typeWriter, delay);
        }
        setTimeout(typeWriter, 1000);

        let signupTimerInterval = null;
        function startSignupTimer(seconds = 30) {
            const timerEl = document.getElementById('signup-otp-timer');
            const timerContainer = document.getElementById('signup-otp-timer-container');
            const resendBtn = document.getElementById('signup-resend-otp-btn');
            if (!timerEl || !timerContainer || !resendBtn) return;

            if (signupTimerInterval) clearInterval(signupTimerInterval);

            resendBtn.classList.add('hidden');
            timerContainer.classList.remove('hidden');
            timerEl.textContent = seconds;

            let timeLeft = seconds;
            signupTimerInterval = setInterval(() => {
                timeLeft--;
                if (timerEl) timerEl.textContent = timeLeft;
                if (timeLeft <= 0) {
                    clearInterval(signupTimerInterval);
                    timerContainer.classList.add('hidden');
                    resendBtn.classList.remove('hidden');
                }
            }, 1000);
        }

        const handleFormEnter = (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                sendOtpBtn.click();
            }
        };

        if (fullNameInput) fullNameInput.addEventListener('keydown', handleFormEnter);
        if (emailInput) emailInput.addEventListener('keydown', handleFormEnter);
        if (phoneInput) phoneInput.addEventListener('keydown', handleFormEnter);
        if (passwordInput) passwordInput.addEventListener('keydown', handleFormEnter);

        const handleOtpEnter = (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                verifyOtpBtn.click();
            }
        };

        otpInputs.forEach((input, index) => {
            input.addEventListener('input', (event) => {
                const cleanValue = event.target.value.replace(/\D/g, '');
                event.target.value = cleanValue;
                if (cleanValue && otpInputs[index + 1]) {
                    otpInputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', (event) => {
                if (event.key === 'Backspace' && !event.target.value && otpInputs[index - 1]) {
                    otpInputs[index - 1].focus();
                }
                handleOtpEnter(event);
            });

            input.addEventListener('paste', (event) => {
                const pasted = (event.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, otpInputs.length);
                if (!pasted) {
                    return;
                }

                event.preventDefault();
                pasted.split('').forEach((digit, digitIndex) => {
                    if (otpInputs[digitIndex]) {
                        otpInputs[digitIndex].value = digit;
                    }
                });

                otpInputs[Math.min(pasted.length, otpInputs.length) - 1].focus();
            });
        });

        function clearOtpInputs() {
            otpInputs.forEach((input) => {
                input.value = '';
            });

            if (otpInputs[0]) {
                otpInputs[0].focus();
            }
        }

        function getSignupPayload() {
            return {
                full_name: fullNameInput.value.trim(),
                email: emailInput.value.trim(),
                phone: normalizeSignupPhone(phoneInput.value),
                password: passwordInput.value,
                is_agree: termsInput.checked ? '1' : '0'
            };
        }

        function getPasswordStrength(password) {
            let score = 0;

            if (password.length >= 8) score++;
            if (/[A-Z]/.test(password) && /[a-z]/.test(password)) score++;
            if (/[0-9]/.test(password)) score++;
            if (/[!@#$%^&*()\-_=+{};:,<.>~]/.test(password)) score++;

            if (password.length === 0) {
                return { score: 0, label: 'Enter password', color: 'gray' };
            }

            if (score <= 1) {
                return { score, label: 'Weak', color: 'red' };
            }

            if (score <= 3) {
                return { score, label: 'Medium', color: 'yellow' };
            }

            return { score: 4, label: 'Strong', color: 'brand' };
        }

        function updatePasswordStrength() {
            const strength = getPasswordStrength(passwordInput.value || '');
            const activeClassesByColor = {
                red: 'bg-red-500',
                yellow: 'bg-yellow-400',
                brand: 'bg-[#155BD5]'
            };
            const textClassesByColor = {
                gray: 'text-gray-400',
                red: 'text-red-500',
                yellow: 'text-yellow-500',
                brand: 'text-brand'
            };

            passwordBars.forEach((bar, index) => {
                if (!bar) return;
                bar.className = 'h-1.5 flex-1 rounded-full transition-all ' + (index < strength.score
                    ? activeClassesByColor[strength.color]
                    : 'bg-gray-100');
            });

            if (passwordStrengthLabel) {
                passwordStrengthLabel.className = 'ml-4 text-[10px] font-bold uppercase tracking-tighter ' + (textClassesByColor[strength.color] || 'text-gray-400');
                passwordStrengthLabel.textContent = strength.label;
            }
        }

        async function sendSignupOtp(payloadOverride = null) {
            setSignupMessage('signup-step-1-message', '', '');
            setSignupMessage('signup-step-2-message', '', '');

            const payload = payloadOverride || getSignupPayload();
            sendOtpBtn.disabled = true;
            sendOtpBtn.classList.add('opacity-70', 'cursor-not-allowed');

            try {
                const result = await signupPost("<?= base_url('users/send_signup_otp') ?>", payload);
                if (!result.status) {
                    setSignupMessage('signup-step-1-message', result.message || 'Unable to send OTP.', 'error');
                    return false;
                }

                signupState = payload;
                // Mask the phone for security: +91 XXXXXXXXXX
                const phoneDigits = normalizeSignupPhone(result.data && result.data.phone ? result.data.phone : payload.phone);
                displayPhone.textContent = `+91 XXXXXXXXXX`;

                startSignupTimer(30);
                clearOtpInputs();
                setSignupMessage('signup-step-2-message', result.message || 'OTP sent successfully.', 'success');
                goToStep(2);
                return true;
            } catch (error) {
                setSignupMessage('signup-step-1-message', 'Unable to send OTP right now. Please try again.', 'error');
                return false;
            } finally {
                sendOtpBtn.disabled = false;
                sendOtpBtn.classList.remove('opacity-70', 'cursor-not-allowed');
            }
        }

        async function verifySignupOtp() {
            setSignupMessage('signup-step-2-message', '', '');
            const otp = otpInputs.map((input) => input.value).join('');

            if (otp.length !== otpInputs.length) {
                setSignupMessage('signup-step-2-message', 'Please enter the complete 6-digit OTP.', 'error');
                return;
            }

            verifyOtpBtn.disabled = true;
            verifyOtpBtn.classList.add('opacity-70', 'cursor-not-allowed');

            try {
                const result = await signupPost("<?= base_url('users/verify_signup_otp') ?>", { otp });
                if (!result.status) {
                    setSignupMessage('signup-step-2-message', result.message || 'OTP verification failed.', 'error');
                    return;
                }

                const redirectUrl = result.data && result.data.redirect_url ? result.data.redirect_url : "<?= base_url('analytics') ?>";
                if (dashboardLink) {
                    dashboardLink.href = redirectUrl;
                }
                goToStep(3);
            } catch (error) {
                setSignupMessage('signup-step-2-message', 'Unable to verify OTP right now. Please try again.', 'error');
            } finally {
                verifyOtpBtn.disabled = false;
                verifyOtpBtn.classList.remove('opacity-70', 'cursor-not-allowed');
            }
        }

        sendOtpBtn.addEventListener('click', () => {
            sendSignupOtp();
        });

        verifyOtpBtn.addEventListener('click', () => {
            verifySignupOtp();
        });

        resendOtpBtn.addEventListener('click', async () => {
            if (!signupState) {
                setSignupMessage('signup-step-2-message', 'Please enter your details again to resend OTP.', 'error');
                goToStep(1);
                return;
            }
            sendSignupOtp(signupState);
        });

        changeNumberBtn.addEventListener('click', () => {
            setSignupMessage('signup-step-2-message', '', '');
            goToStep(1);
            phoneInput.focus();
        });

        passwordInput.addEventListener('input', updatePasswordStrength);
        passwordInput.value = '';
        updatePasswordStrength();
    });
</script>