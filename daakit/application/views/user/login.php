<title>Login | DAAKiT - India's Leading Fulfillment Platform</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    brand: '#0446DB',
                    'brand-dark': '#0338AF',
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
        gap: 48px;
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

    .auth-panel {
        width: 100%;
    }

    .login-card {
        width: min(100%, 36rem);
        min-height: clamp(33rem, 72vh, 44rem);
    }

    .auth-heading {
        line-height: 1.1;
    }

    .auth-subtext {
        font-size: clamp(0.875rem, 0.85rem + 0.1vw, 1rem);
        /* text-sm to text-base */
        line-height: 1.55;
    }

    .auth-label {
        font-size: clamp(0.72rem, 0.7rem + 0.1vw, 0.82rem);
        letter-spacing: 0.1em;
    }

    .auth-input {
        min-height: clamp(3.2rem, 3rem + 0.5vw, 3.8rem);
        font-size: clamp(0.95rem, 0.92rem + 0.15vw, 1.05rem);
    }

    .auth-button {
        min-height: clamp(3.4rem, 3.2rem + 0.5vw, 4rem);
        font-size: clamp(0.95rem, 0.92rem + 0.15vw, 1.05rem);
    }

    @media (min-width: 1024px) {
        .auth-panel {
            width: 100%;
        }

        .login-card {
            width: 100%;
            max-width: 100%;
            height: 90vh;
            overflow-y: auto;
        }
    }

    @media (min-width: 1280px) {
        .auth-subtext {
            font-size: 1.25rem;
        }

        .auth-label {
            font-size: clamp(0.8rem, 0.75rem + 0.15vw, 0.95rem);
        }

        .auth-input {
            min-height: clamp(3.6rem, 3.4rem + 0.6vw, 4.2rem);
            font-size: clamp(1.05rem, 1rem + 0.2vw, 1.2rem);
        }

        .auth-button {
            min-height: clamp(4.4rem, 4.2rem + 0.8vw, 5.2rem);
            font-size: clamp(1.2rem, 1.1rem + 0.4vw, 1.5rem);
            font-weight: 800;
            letter-spacing: 0.01em;
        }
    }

    .signup-button {
        min-height: 3.6rem;
        font-size: 1.15rem;
    }
</style>

<?php
$csrfName = $this->security->get_csrf_token_name();
$csrfHash = $this->security->get_csrf_hash();
$returnUrl = $this->input->get('r') ?: $this->input->post('r');
$isPostRequest = strtoupper($this->input->method(TRUE)) === 'POST';
?>

<body>
    <section class="auth-bg">
        <main class="lg:h-screen flex justify-center overflow-hidden" style="font-family: 'DM Sans', sans-serif;">
            <div class="flex flex-col lg:flex-row w-full min-h-screen lg:h-screen animate-fade-up">

                <div
                    class="relative hidden lg:flex flex-col w-[45%] xl:w-[50%] h-screen  overflow-hidden px-12 pt-10 pb-12 rounded-br-[60px] rounded-tr-[60px]">
                    <div class="flex justify-between items-center w-full relative z-10">
                        <img src="<?= base_url('assets/images/logo-daakit.png') ?>" alt="DAAKit Logo"
                            class="w-36 h-auto cursor-pointer" onclick="window.location.href='<?= base_url() ?>'" />
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

                <div class="flex-1 flex flex-col relative px-4 bg-gray-50/30 lg:bg-transparent overflow-hidden">
                    <div class="auth-panel flex-1 flex justify-center items-center w-full py-6 overflow-hidden">
                        <div
                            class="login-card w-full bg-white rounded-lg border border-gray-100 shadow-[0_20px_60px_-28px_rgba(15,23,42,0.28)] px-5 pb-6 sm:px-7 sm:pb-7 lg:px-10 lg:pb-9 xl:px-12 xl:pb-10 pt-8 sm:pt-10 lg:pt-12 relative z-10 animate-fade-up flex flex-col justify-center">

                            <!-- Mobile Back Header -->
                            <div
                                class="lg:hidden flex items-center justify-between mb-4 sm:mb-6 pb-4 border-b border-gray-100">
                                <a href="<?= base_url() ?>"
                                    class="flex items-center gap-2 text-gray-600 hover:text-brand transition-colors no-underline">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                    </svg>
                                    <span class="font-bold text-sm sm:text-base">Back</span>
                                </a>
                                <img src="<?= base_url('assets/images/logo-daakit.png') ?>" alt="DAAKit Logo"
                                    class="h-6 sm:h-8 w-auto" />
                            </div>

                            <form action="<?= base_url('users/login_process') ?>" method="POST" id="main-login-form">
                                <input type="hidden" name="<?= $csrfName; ?>" id="csrf-token-input"
                                    value="<?= $csrfHash; ?>" />
                                <?php if (!empty($returnUrl)) { ?>
                                    <input type="hidden" name="r" value="<?= html_escape($returnUrl); ?>" />
                                <?php } ?>

                                <div id="step-identifier" class="flex flex-col relative">
                                    <div class="mb-6 lg:mb-7 xl:mb-8 md:text-left">
                                        <h2
                                            class="auth-heading text-2xl sm:text-3xl xl:text-6xl whitespace-nowrap font-bold text-gray-900 mb-2 tracking-tight">
                                            Welcome Back!</h2>
                                        <p class="text-gray-500 font-medium max-w-[28rem]">Enter your details to manage
                                            your shipments.</p>
                                    </div>

                                    <div class="space-y-4 sm:space-y-5 lg:space-y-6 xl:space-y-8">
                                        <div>
                                            <label class="auth-label block font-bold text-black mb-2.5 uppercase"
                                                for="identifier-input">EMAIL OR PHONE NUMBER</label>
                                            <div class="relative group">
                                                <input id="identifier-input" name="identity" type="text"
                                                    placeholder="name@company.com"
                                                    class="auth-input w-full px-4 sm:px-5 bg-[#F3F4F6] border-2 border-transparent rounded-lg text-gray-900 font-semibold placeholder-gray-400 focus:bg-white focus:border-brand/20 transition-all outline-none"
                                                    required />
                                            </div>
                                            <p id="identifier-error" class="mt-2 hidden text-lg text-red-600"></p>
                                        </div>

                                        <button type="button" onclick="goToPassword()"
                                            class="signup-button w-full bg-brand hover:bg-brand-dark text-white font-bold rounded-lg flex items-center justify-center gap-2 transition-all shadow-md active:scale-[0.98]">
                                            Continue
                                            <svg class="w-5 h-5 sm:w-5 sm:h-5 lg:w-[22px] lg:h-[22px]" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                            </svg>
                                        </button>

                                        <div class="divider font-medium text-lg sm:text-lg my-3 sm:my-4">Or</div>

                                        <a href="<?= base_url('oauth/all/google?flow=login') ?>"
                                            class="signup-button w-full bg-white border border-gray-200 hover:border-gray-300 hover:bg-gray-50 text-gray-700 font-bold rounded-lg flex items-center justify-center gap-3 transition-all shadow-sm no-underline">
                                            <svg class="w-5 h-5 sm:w-5 sm:h-5 lg:w-[22px] lg:h-[22px]"
                                                viewBox="0 0 24 24">
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

                                        <div class="pt-5 mt-5 xl:pt-8 xl:mt-8 border-t border-gray-100">
                                        </div>
                                    </div>
                                </div>

                                <div id="step-password" class="hidden flex flex-col relative">
                                    <div class="mb-6 md:text-left lg:mb-7">
                                        <h2
                                            class="auth-heading text-2xl sm:text-3xl xl:text-6xl whitespace-nowrap font-bold text-gray-900 tracking-tight">
                                            Enter Password</h2>
                                        <p class="auth-subtext text-gray-500 font-medium  mt-2">Please enter the
                                            password
                                            for your account associated with <span id="display-type"></span> <span
                                                id="display-identifier"
                                                class="text-gray-900 font-bold">user@example.com</span></p>
                                    </div>

                                    <div class="space-y-4 sm:space-y-5 lg:space-y-6">
                                        <div>
                                            <label
                                                class="auth-label block font-extrabold text-gray-500 mb-2.5 uppercase">PASSWORD</label>
                                            <div class="relative group">
                                                <input id="password-input" name="password" type="password" value=""
                                                    placeholder="Enter your password" autocomplete="new-password"
                                                    autocapitalize="off" autocorrect="off" spellcheck="false"
                                                    data-lpignore="true"
                                                    class="auth-input w-full px-4 sm:px-5 bg-white border border-gray-300 rounded-lg text-gray-900 font-semibold placeholder-gray-400 focus:border-brand/30 transition-all outline-none"
                                                    required />
                                                <button type="button" onclick="togglePwd('password-input', 'pwd-eye')"
                                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-brand focus:outline-none">
                                                    <svg id="pwd-eye" class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24" stroke-width="2">
                                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                                        <circle cx="12" cy="12" r="3" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <p id="password-error" class="mt-2 hidden text-lg text-red-600"></p>
                                        </div>

                                        <div>
                                            <a id="forgot-password-link" href="<?= base_url('users/forgot') ?>"
                                                class="inline-flex items-center text-lg font-medium text-[#155BD5] no-underline transition-colors hover:underline hover:text-[#1752D0]">
                                                Forgot password?
                                            </a>
                                        </div>

                                        <button type="submit"
                                            class="signup-button w-full bg-brand hover:bg-brand-dark text-white font-bold rounded-lg flex items-center justify-center gap-2 transition-all shadow-md active:scale-[0.98]">
                                            Login
                                            <svg class="w-5 h-5 sm:w-5 sm:h-5 lg:w-[22px] lg:h-[22px]" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                            </svg>
                                        </button>

                                        <button type="button" onclick="goBack()"
                                            class="flex items-center gap-2 text-lg font-semibold text-[#155BD5] hover:underline hover:text-[#1752D0]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M11 17l-5-5m0 0l5-5m-5 5h12" stroke-width="2.5"
                                                    stroke-linecap="round" />
                                            </svg>
                                            Back to Login
                                        </button>


                                    </div>
                                </div>
                            </form>

                            <span class="border-gray-200 border-t w-full mt-5 sm:mt-6"></span>
                            <div class="flex justify-between items-center 
    text-gray-500 text-xs sm:text-sm font-medium 
    w-full pt-5 sm:pt-6 
    whitespace-nowrap overflow-x-auto">

                                <div class="flex gap-4 sm:gap-6 flex-nowrap">
                                    <a href="https://daakit.com/privacy-policy/"
                                        class="hover:text-gray-900 transition-colors">
                                        Privacy Policy
                                    </a>

                                    <a href="https://daakit.com/terms-and-conditions/"
                                        class="hover:text-gray-900 transition-colors">
                                        Terms of Service
                                    </a>
                                </div>

                                <a href="https://daakit.com/contact/" class="flex items-center gap-1.5 hover:text-gray-900 
       transition-colors no-underline ml-4">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                            stroke-width="2" />
                                    </svg>
                                    Need Support?
                                </a>

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

</body>
<script>
    localStorage.setItem('token', '');
    localStorage.setItem('token', 'logout');

    const serverErrorMessage = <?= json_encode(($isPostRequest && !empty($error)) ? $error : '') ?>;
    const serverSuccessMessage = <?= json_encode(!empty($success) ? $success : '') ?>;

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
        const variantClass = type === 'success'
            ? 'bg-green-50 text-green-700 border-green-200'
            : 'bg-red-50 text-red-700 border-red-200';

        toast.className = `${baseClass} ${variantClass}`;
        toast.textContent = message;
        container.appendChild(toast);

        requestAnimationFrame(() => {
            toast.classList.remove('opacity-0', 'translate-y-1');
        });

        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-y-1');
            setTimeout(() => toast.remove(), 250);
        }, 3200);
    }

    async function goToPassword() {
        const identifierInput = document.getElementById('identifier-input');
        const errorEl = document.getElementById('identifier-error');
        const identifier = identifierInput.value.trim();

        errorEl.classList.add('hidden');
        errorEl.textContent = '';

        if (!identifier) {
            showToast('Please enter your email or phone number');
            return;
        }

        try {
            const url = '<?= base_url('users/check_user') ?>?identity=' + encodeURIComponent(identifier);
            const res = await fetch(url, {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            const data = await res.json();
            if (!data.status) {
                showToast(data.message || 'No account found for this email/phone.');
                return;
            }

            let displayId = identifier;
            let displayType = 'email';

            if (/^\d+$/.test(identifier.replace(/\s+/g, ''))) {
                displayType = 'phone number';
                const onlyDigits = identifier.replace(/\D+/g, '');
                if (onlyDigits.length === 10) {
                    displayId = '+91 ' + onlyDigits;
                }
            }

            document.getElementById('display-type').innerText = displayType;
            document.getElementById('display-identifier').innerText = displayId;
            document.getElementById('step-identifier').classList.add('hidden');
            document.getElementById('step-password').classList.remove('hidden');
            document.getElementById('password-input').focus();
        } catch (e) {
            showToast('Unable to verify account right now. Please try again.');
        }
    }

    function goBack() {
        document.getElementById('step-password').classList.add('hidden');
        document.getElementById('step-identifier').classList.remove('hidden');
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

    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('main-login-form');
        const passwordInput = document.getElementById('password-input');
        const passwordErrorEl = document.getElementById('password-error');

        form.addEventListener('submit', function (e) {
            const passwordStepVisible = !document.getElementById('step-password').classList.contains('hidden');
            if (!passwordStepVisible) return;

            if (!passwordInput.value.trim()) {
                e.preventDefault();
                showToast('Please enter your password');
                passwordInput.focus();
            }
        });

        const forgotLink = document.getElementById('forgot-password-link');
        const input = document.getElementById('identifier-input');
        if (!input) return;

        input.addEventListener('input', function () {
            // Update forgot password link
            const val = input.value.trim();
            const baseUrl = '<?= base_url('users/forgot') ?>';
            if (forgotLink) {
                forgotLink.href = val ? `${baseUrl}?identity=${encodeURIComponent(val)}` : baseUrl;
            }
        });

        passwordInput.addEventListener('input', function () {
            if (passwordInput.value.trim()) {
                passwordErrorEl.classList.add('hidden');
                passwordErrorEl.textContent = '';
            }
        });

        if (serverErrorMessage) {
            showToast(serverErrorMessage, 'error');
        }
        if (serverSuccessMessage) {
            showToast(serverSuccessMessage, 'success');
        }

        const placeholders = ['name@company.com', '+91 XXXXX XXXXX'];
        let currentIdx = 0;
        let charIdx = 0;
        let isDeleting = false;

        function typeWriter() {
            const currentString = placeholders[currentIdx];
            let delay = 80;

            if (!isDeleting && charIdx <= currentString.length) {
                input.setAttribute('placeholder', currentString.substring(0, charIdx++));
            } else if (isDeleting && charIdx >= 0) {
                input.setAttribute('placeholder', currentString.substring(0, charIdx--));
                delay = 40;
            } else {
                isDeleting = !isDeleting;
                if (!isDeleting) currentIdx = (currentIdx + 1) % placeholders.length;
                delay = isDeleting ? 2200 : 300;
            }

            setTimeout(typeWriter, delay);
        }

        setTimeout(typeWriter, 900);
    });
</script>