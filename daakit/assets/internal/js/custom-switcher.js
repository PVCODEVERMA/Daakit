import * as data from "./indexcharts"

// modified code start
let lightPrimary  = document.querySelector('#colorID')
lightPrimary?.addEventListener('input', changePrimaryColor)

let transparentBgColorID  = document.querySelector('#transparentBgColorID')
transparentBgColorID?.addEventListener('input', transparentBgColor)

"use strict";
let mainContent;
(function () {
    let html = document.querySelector('html');
    mainContent = document.querySelector('.app-content');

    // theme color picker
    const dynamicPrimaryLight = document.querySelectorAll('input.color-primary-light');
    const dynamicBackground = document.querySelectorAll('input.color-bg-transparent');
    dynamicPrimaryColor(dynamicPrimaryLight);
    dynamicBackgroundColor(dynamicBackground);

    localStorageBackup();

    
    //LTR to RTL
    if (!localStorage.getItem("solicrtl")) {
        // html.setAttribute("dir" , "rtl") // for rtl version 
    }

    //Light Theme Style
    if (!localStorage.getItem("soliclighttheme")) {
        // html.setAttribute("data-theme-color" , "light") // for light theme 
    }

    //Dark Theme Style
    if (!localStorage.getItem("solicdarktheme")) {
        // html.setAttribute("data-theme-color" , "dark") // for dark theme 
    }

    //Menu Layout
    if (!localStorage.getItem("soliclayout")) {
        // html.setAttribute("data-layout" , "vertical") // for Vertical layout 
        // html.setAttribute("data-layout" , "horizontal") // for horizontal layout 
    }
    
    //Menu Styles
    if (!localStorage.getItem("solicMenu")) {
        // html.setAttribute("data-menu-style" , "light") // for light menu style 
        // html.setAttribute("data-menu-style" , "dark") // for dark menu style 
        // html.setAttribute("data-menu-style" , "color") // for color menu style
        // html.setAttribute("data-menu-style" , "gradient") // for gradient menu style 
    }
    
    //Header Styles
    if (!localStorage.getItem("solicHeader")) {
        // html.setAttribute("data-header-style" , "light") // for light header style 
        // html.setAttribute("data-header-style" , "dark") // for dark header style 
        html.setAttribute("data-header-style" , "color") // for color header style 
        // html.setAttribute("data-header-style" , "gradient") // for gradient header style 
    }
    
    //Default Layout Styles
    if (!localStorage.getItem("solicverticalstyles")) {
        // html.setAttribute("data-vertical-style" , "default") // for Vertical default style 
    }

    //Closed Layout Styles
    if (!localStorage.getItem("solicverticalstyles")) {
        // html.setAttribute("data-vertical-style" , "closed") // for Vertical closed style 
        // $('body').addClass('sidenav-toggled');
    }

    //IconText Layout Styles
    if (!localStorage.getItem("solicverticalstyles")) {
        // html.setAttribute("data-vertical-style" , "icontext") // for Vertical icontext style 
        // textLayoutFn();
    }

    //Overlay Layout Styles
    if (!localStorage.getItem("solicverticalstyles")) {
        // html.setAttribute("data-vertical-style" , "overlay") // for Vertical overlay style 
        // $('body').addClass('sidenav-toggled');
    }

    //Hover Submenu Layout Styles
    if (!localStorage.getItem("solicverticalstyles")) {
        // html.setAttribute("data-vertical-style" , "hover") // for Vertical hover style 
        // hoverLayoutFn();
    }

    //Hover Submenu1 Layout Styles
    if (!localStorage.getItem("solicverticalstyles")) {
        // html.setAttribute("data-vertical-style" , "hover1") // for Vertical hover1 style 
        // hoverLayoutFn();
    }

    //Double Menu Layout Styles
    if (!localStorage.getItem("solicverticalstyles")) {
        // html.setAttribute("data-vertical-style" , "doublemenu") // for Vertical doublemenu style 
        // doubleLayoutFn();
    }

    //Double Menu Tabs Layout Styles
    if (!localStorage.getItem("solicverticalstyles")) {
        // html.setAttribute("data-vertical-style" , "doublemenu-tabs") // for Vertical doublemenu-tabs style 
        // doubleLayoutFn();
    }

    //horizontalmenu Layout Styles
    if (!localStorage.getItem("soliclayout") === "horizontal" || localStorage.getItem("soliclayout") == null) {
        // html.setAttribute("data-hor-style" , "hor-click") // for horizontal click style 
        // html.setAttribute("data-hor-style" , "hor-hover") // for horizontal hover style 
    } 

    //Boxed styles
    if (!localStorage.getItem("solicboxed")) {
        // html.setAttribute("data-width" , "boxed") // for boxed style
    }

    //Scrollabel styles
    if (!localStorage.getItem("solicscrollable")) {
        // html.setAttribute("data-position" , "scrollable") // for scrollable style
    }

    //No-Shadow styles
    if (!localStorage.getItem("solicnoshadow")) {
        // html.setAttribute("data-skins" , "no-shadow") // for No-shadow styles
    }

    //Centerlogo For Horizontal
    if (!localStorage.getItem("soliccenterlogo")) {
        // html.setAttribute("data-logo" , "centerlogo") // for Horizontal Centerlogo
    }

    /*RTL Start*/
    if (html.getAttribute('dir') === "rtl") {
        rtlFn();
    }
    /*RTL End*/

    /*Horizontal Start*/
    if (html.getAttribute('data-hor-style') === "hor-click") {
        horizontalClickFn();
    }
    /*Horizontal End*/

    /*Horizontal-Hover Start*/
    if (html.getAttribute('data-hor-style') === "hor-hover") {
        horizontalHoverFn();
    }
    /*Horizontal-Hover End*/

    if (document.querySelector(".sidebar-right1")) {
        switcherClick();
    }

    checkOptions();

})();

function switcherClick() {
    let ltrBtn, rtlBtn, verticalBtn, horiBtn, horiHoverBtn, lightBtn, darkBtn, boxedBtn, fullwidthBtn, scrollableBtn, fixedBtn, lightHeaderBtn, darkHeaderBtn, colorHeaderBtn, gradientHeaderBtn, lightMenuBtn, darkMenuBtn, colorMenuBtn, gradientMenuBtn, shadowBtn, NoshadowBtn, defaultBtn, closedBtn, iconTextBtn, hoversubBtn, hoversub1Btn, overlayBtn, doubleBtn, doubleTabsBtn, defaultlogoBtn, centerlogoBtn, resetBtn;
    let html = document.querySelector('html');
    lightBtn = document.querySelector('#switchbtn-light');
    darkBtn = document.querySelector('#switchbtn-dark');
    ltrBtn = document.querySelector('#switchbtn-ltr');
    rtlBtn = document.querySelector('#switchbtn-rtl');
    verticalBtn = document.querySelector('#switchbtn-vertical');
    horiBtn = document.querySelector('#switchbtn-horizontal');
    horiHoverBtn = document.querySelector('#switchbtn-horizontalHover');
    boxedBtn = document.querySelector('#switchbtn-boxed');
    fullwidthBtn = document.querySelector('#switchbtn-fullwidth');
    scrollableBtn = document.querySelector('#switchbtn-scrollable');
    fixedBtn = document.querySelector('#switchbtn-fixed');
    lightHeaderBtn = document.querySelector('#switchbtn-lightheader');
    darkHeaderBtn = document.querySelector('#switchbtn-darkheader');
    colorHeaderBtn = document.querySelector('#switchbtn-colorheader');
    gradientHeaderBtn = document.querySelector('#switchbtn-gradientheader');
    lightMenuBtn = document.querySelector('#switchbtn-lightmenu');
    darkMenuBtn = document.querySelector('#switchbtn-darkmenu');
    colorMenuBtn = document.querySelector('#switchbtn-colormenu');
    gradientMenuBtn = document.querySelector('#switchbtn-gradientmenu');
    defaultBtn = document.querySelector('#switchbtn-defaultmenu');
    closedBtn = document.querySelector('#switchbtn-closed');
    iconTextBtn = document.querySelector('#switchbtn-text');
    hoversubBtn = document.querySelector('#switchbtn-hoversub');
    hoversub1Btn = document.querySelector('#switchbtn-hoversub1');
    overlayBtn = document.querySelector('#switchbtn-overlay');
    doubleBtn = document.querySelector('#switchbtn-doublemenu');
    doubleTabsBtn = document.querySelector('#switchbtn-doublemenu-tabs');
    defaultlogoBtn = document.querySelector('#switchbtn-defaultlogo');
    centerlogoBtn = document.querySelector('#switchbtn-centerlogo');
    resetBtn = document.querySelector('#resetbtn');

    /*Light Layout Start*/
    let lightThemeVar = lightBtn.addEventListener('click', () => {
        html.setAttribute('data-theme-color', 'light');
        html.setAttribute('data-header-style', 'color');
        html.setAttribute('data-menu-style', 'light');
        $('#switchbtn-lightmenu').prop('checked', true);
        $('#switchbtn-colorheader').prop('checked', true);

        localStorage.setItem("soliclighttheme", true);
        localStorage.removeItem("solicdarktheme");
        localStorage.removeItem("solicbgColor");
        localStorage.removeItem("solicheaderbg");
        localStorage.removeItem("solicbgwhite");
        localStorage.removeItem("solicmenubg");

        localStorage.setItem("solicHeader", 'color');
        localStorage.setItem("solicMenu", 'light');

        checkOptions();
        const root = document.querySelector(':root');
        root.style = "";
        names();

        if (!document.body.classList.contains('auth-page')) {
            let mainHeader = document.querySelector('.app-header');
            mainHeader.style = "";
            let appSidebar = document.querySelector('.app-sidebar');
            appSidebar.style = "";
        }

    })
    /*Light Layout End*/

    /*Dark Layout Start*/
    let darkThemeVar = darkBtn.addEventListener('click', () => {
        html.setAttribute('data-theme-color', 'dark');
        html.setAttribute('data-header-style', 'dark');
        html.setAttribute('data-menu-style', 'dark');
        $('#switchbtn-darkmenu').prop('checked', true);
        $('#switchbtn-darkheader').prop('checked', true);
       
        localStorage.setItem("solicdarktheme", true);
        localStorage.removeItem("soliclighttheme");
        localStorage.removeItem("solicbgColor");
        localStorage.removeItem("solicheaderbg");
        localStorage.removeItem("solicbgwhite");
        localStorage.removeItem("solicmenubg");
        //
        

        localStorage.setItem("solicHeader", 'dark');
        localStorage.setItem("solicMenu", 'dark');

        checkOptions();

        const root = document.querySelector(':root');
        root.style = "";
        names();

        if (!document.body.classList.contains('auth-page')) {
            let mainHeader = document.querySelector('.app-header');
            mainHeader.style = "";
            let appSidebar = document.querySelector('.app-sidebar');
            appSidebar.style = "";
        }

    });
    /*Dark Layout End*/

    /*Light Menu Start*/
    let lightMenuVar = lightMenuBtn?.addEventListener('click', () => {
        html.setAttribute('data-menu-style', 'light');
        let appSidebar = document.querySelector('.app-sidebar');
        appSidebar.style = "";
        localStorage.setItem("solicMenu", 'light');
    });
    /*Light Menu End*/

    /*Color Menu Start*/
    let colorMenuVar = colorMenuBtn?.addEventListener('click', () => {
        html.setAttribute('data-menu-style', 'color');
        let appSidebar = document.querySelector('.app-sidebar');
        appSidebar.style = "";
        localStorage.setItem("solicMenu", 'color');
    });
    /*Color Menu End*/

    /*Dark Menu Start*/
    let darkMenuVar = darkMenuBtn?.addEventListener('click', () => {
        html.setAttribute('data-menu-style', 'dark');
        let appSidebar = document.querySelector('.app-sidebar');
        appSidebar.style = "";
        localStorage.setItem("solicMenu", 'dark');
    });
    /*Dark Menu End*/

    /*Gradient Menu Start*/
    let gradientMenuVar = gradientMenuBtn?.addEventListener('click', () => {
        html.setAttribute('data-menu-style', 'gradient');
        let appSidebar = document.querySelector('.app-sidebar');
        appSidebar.style = "";
        localStorage.setItem("solicMenu", 'gradient');
    });
    /*Gradient Menu End*/

    /*Light Header Start*/
    let lightHeaderVar = lightHeaderBtn?.addEventListener('click', () => {
        html.setAttribute('data-header-style', 'light');
        let mainHeader = document.querySelector('.app-header');
        mainHeader.style = "";
        localStorage.setItem("solicHeader", 'light');
    });
    /*Light Header End*/

    /*Color Header Start*/
    let colorHeaderVar = colorHeaderBtn?.addEventListener('click', () => {
        html.setAttribute('data-header-style', 'color');
        let mainHeader = document.querySelector('.app-header');
        mainHeader.style = "";
        localStorage.setItem("solicHeader", 'color');
    });
    /*Color Header End*/

    /*Dark Header Start*/
    let darkHeaderVar = darkHeaderBtn?.addEventListener('click', () => {
        html.setAttribute('data-header-style', 'dark');
        let mainHeader = document.querySelector('.app-header');
        mainHeader.style = "";
        localStorage.setItem("solicHeader", 'dark');
    });
    /*Dark Header End*/

    /*Gradient Header Start*/
    let gradientHeaderVar = gradientHeaderBtn?.addEventListener('click', () => {
        html.setAttribute('data-header-style', 'gradient');
        let mainHeader = document.querySelector('.app-header');
        mainHeader.style = "";
        localStorage.setItem("solicHeader", 'gradient');
    });
    /*Gradient Header End*/

    /*Full Width Layout Start*/
    let fullwidthVar = fullwidthBtn?.addEventListener('click', () => {
        html.setAttribute('data-width', 'fullwidth');
        if (html.getAttribute('data-layout') === "horizontal") {
            checkHoriMenu();
        }
        localStorage.setItem("solicfullwidth", true);
        localStorage.removeItem("solicboxed");
    });
    /*Full Width Layout End*/

    /*Boxed Layout Start*/
    let boxedVar = boxedBtn?.addEventListener('click', () => {
        html.setAttribute('data-width', 'boxed');
        if (html.getAttribute('data-layout') === "horizontal") {
            checkHoriMenu();
        }
        localStorage.setItem("solicboxed", true);
        localStorage.removeItem("solicfullwidth");
    });
    /*Boxed Layout End*/

    /*Shadow Layout Start*/
    let shadowVar = shadowBtn?.addEventListener('click', () => {
        html.setAttribute('data-skins', 'shadow');
        localStorage.setItem("solicshadow", true);
        localStorage.removeItem("solicnoshadow");
    });
    /*Shadow Layout End*/

    /*No Shadow Layout Start*/
    let noShadowVar = NoshadowBtn?.addEventListener('click', () => {
        html.setAttribute('data-skins', 'no-shadow');
        localStorage.setItem("solicnoshadow", true);
        localStorage.removeItem("solicshadow");
    });
    /*No Shadow Layout End*/

    /*Header-Position Styles Start*/
    let fixedVar = fixedBtn?.addEventListener('click', () => {
        html.setAttribute('data-position', 'fixed');
        localStorage.setItem("solicfixed", true);
        localStorage.removeItem("solicscrollable");
    });

    let scrollableVar = scrollableBtn?.addEventListener('click', () => {
        html.setAttribute('data-position', 'scrollable');
        localStorage.setItem("solicscrollable", true);
        localStorage.removeItem("solicfixed");
    });
    /*Header-Position Styles End*/

    /*Default Sidemenu Start*/
    let defaultVar = defaultBtn?.addEventListener('click', () => {
        html.setAttribute('data-vertical-style', 'default');
        document.body.classList.remove('sidenav-toggled');
        localStorage.removeItem("solicverticalstyles");

        hovermenu();
    });
    /*Default Sidemenu End*/

    /*Closed Sidemenu Start*/
    let closedVar = closedBtn?.addEventListener('click', () => {
        html.setAttribute('data-vertical-style', 'closed');
        localStorage.setItem("solicverticalstyles", 'closed');

        hoverLayoutFn();
    });
    /*Closed Sidemenu End*/

    /*Hover Submenu Start*/
    let hoverSubVar = hoversubBtn?.addEventListener('click', () => {
        html.setAttribute('data-vertical-style', 'hover');
        localStorage.setItem("solicverticalstyles", 'hover');

        hoverLayoutFn();
    });
    /*Hover Submenu End*/

    /*Hover Submenu 1 Start*/
    let hoverSub1Var = hoversub1Btn?.addEventListener('click', () => {
        html.setAttribute('data-vertical-style', 'hover1');
        localStorage.setItem("solicverticalstyles", 'hover1');

        hoverLayoutFn();
    });
    /*Hover Submenu 1 End*/

    /*Icon Text Sidemenu Start*/
    let iconTextVar = iconTextBtn?.addEventListener('click', () => {
        html.setAttribute('data-vertical-style', 'icontext');
        localStorage.setItem("solicverticalstyles", 'icontext');

        textLayoutFn();
    });
    /*Icon Text Sidemenu End*/

    /*Icon Overlay Sidemenu Start*/
    let overlayVar = overlayBtn?.addEventListener('click', () => {
        html.setAttribute('data-vertical-style', 'overlay');
        localStorage.setItem("solicverticalstyles", 'overlay');

        hoverLayoutFn();
    });
    /*Icon Overlay Sidemenu End*/

    /*Double Menu Sidemenu Start*/
    let doubleVar = doubleBtn?.addEventListener('click', () => {
        html.setAttribute('data-vertical-style', 'doublemenu');
        localStorage.setItem("solicverticalstyles", 'doublemenu');

        doubleLayoutFn();
    });
    /*Double Menu Sidemenu End*/

    /*Double Menu Sidemenu Start*/
    let doubleTabsVar = doubleTabsBtn?.addEventListener('click', () => {
        html.setAttribute('data-vertical-style', 'doublemenu-tabs');
        localStorage.setItem("solicverticalstyles", 'doublemenu-tabs');

        doubleLayoutFn();
    });
    /*Double Menu Sidemenu End*/

    /* Sidemenu start*/
    let verticalVar = verticalBtn?.addEventListener('click', () => {
        // local storage
        localStorage.removeItem("soliclayout");
        localStorage.setItem("solicverticalstyles", 'default');

        verticalFn();
    });
    /* Sidemenu end*/

    /* horizontal click start*/
    let horiVar = horiBtn?.addEventListener('click', () => {

        //    local storage 
        localStorage.setItem("soliclayout", 'horizontal');
        localStorage.removeItem("solicverticalstyles");

        horizontalClickFn();
    });
    /* horizontal click end*/

    /* horizontal hover start*/
    let horiHoverVar = horiHoverBtn?.addEventListener('click', () => {

        //    local storage 
        localStorage.setItem("soliclayout", 'horizontalhover');
        localStorage.removeItem("solicverticalstyles");

        horizontalHoverFn();
    });
    /* horizontal hover end*/
    /* rtl start*/
    let rtlVar = rtlBtn?.addEventListener('click', () => {
        localStorage.setItem("solicrtl", true);
        localStorage.removeItem("solicltr");
        rtlFn();
    });
    /* rtl end*/
    /* ltr start*/
    let ltrVar = ltrBtn?.addEventListener('click', () => {
        //    local storage 
        localStorage.setItem("solicltr", true);
        localStorage.removeItem("solicrtl");

        ltrFn();
    });
    /* ltr end*/


    /*Horizontal Logo Position Start*/
    let defaultlogoVar = defaultlogoBtn?.addEventListener('click', () => {
        html.setAttribute('data-logo', 'defaultlogo');
        localStorage.setItem("solicdefaultlogo", true);
        localStorage.removeItem("soliccenterlogo");
    });

    let centerlogoVar = centerlogoBtn?.addEventListener('click', () => {
        html.setAttribute('data-logo', 'centerlogo');
        localStorage.setItem("soliccenterlogo", true);
        localStorage.removeItem("solicdefaultlogo");
    });
    /*Horizontal Logo Position End*/
}

function ltrFn() {
    let html = document.querySelector('html');
    html.setAttribute("dir", "ltr");
    let select2Cont = document.querySelectorAll(".select2-container")
    select2Cont.forEach(e => e.setAttribute("dir", "ltr"))
    document.querySelector("#style")?.setAttribute("href", "http://127.0.0.1:8000/build/assets/plugins/bootstrap/css/bootstrap.min.css");
    var carousel = $('.owl-carousel');
    $.each(carousel, function (index, element) {
        // element == this
        var carouselData = $(element).data('owl.carousel');
        carouselData.settings.rtl = false; //don't know if both are necessary
        carouselData.options.rtl = false;
        $(element).trigger('refresh.owl.carousel');
    });
    if (html.getAttribute('data-layout') === "horizontal") {
        checkHoriMenu();
    }
    //
    checkOptions();
}

function checkHoriMenu(){}

function rtlFn() {
    let html = document.querySelector('html');
    html.setAttribute("dir", "rtl");
    let select2Cont = document.querySelectorAll(".select2-container")
    select2Cont.forEach(e => e.setAttribute("dir", "rtl"))
    document.querySelector("#style")?.setAttribute("href", "http://127.0.0.1:8000/build/assets/plugins/bootstrap/css/bootstrap.rtl.min.css");
    var carousel = $('.owl-carousel');
    $.each(carousel, function (index, element) {
        // element == this
        var carouselData = $(element).data('owl.carousel');
        carouselData.settings.rtl = true; //don't know if both are necessary
        carouselData.options.rtl = true;
        $(element).trigger('refresh.owl.carousel');
    });
    if (html.getAttribute('data-layout') === "horizontal") {
            checkHoriMenu();
    }
    //
    checkOptions();
}

function verticalFn() {
    $('#switchbtn-vertical').prop('checked', true);
    let html = document.querySelector('html');
    html.setAttribute('data-layout', 'vertical');
    html.setAttribute('data-vertical-style', 'default');
    html.removeAttribute('data-hor-style');
    if (!document.body.classList.contains('auth-page')) {
        document.body.classList.add('sidebar-mini');
        document.querySelector(".main-content").classList.add("app-content");
        let mainContainer = document.querySelectorAll(".main-container")
        mainContainer.forEach(e => e.classList.add("container-fluid"))
        mainContainer.forEach(e => e.classList.remove("container"))
        document.querySelector(".main-content").classList.remove("horizontal-content");
        document.querySelector(".app-header").classList.remove("hor-header");
        document.querySelector(".app-sidebar").classList.remove("horizontal-main");
        document.querySelector(".main-sidemenu").classList.remove("container");
        document.querySelector('#slide-left').classList.remove('d-none');
        document.querySelector('#slide-right').classList.remove('d-none');
        if (html.getAttribute('data-layout') === "horizontal") {
            checkHoriMenu();
        }
        responsive();
        menuClick();
        mainContent.removeEventListener('click', slideClick);
        //
        checkOptions();
    }
    // document.querySelectorAll(".slide").forEach((ele)=>{
    //     if(ele.querySelector("a")?.classList.contains("active")){
    //         ele.querySelector("ul").style.display = "block"
    //     }
    // })
}

function horizontalClickFn() {
    $('#switchbtn-horizontal').prop('checked', true);
    let html = document.querySelector('html');
    html.setAttribute('data-layout', 'horizontal');
    html.setAttribute('data-hor-style', 'hor-click');
    html.removeAttribute('data-vertical-style');
    if (!document.body.classList.contains('auth-page')) {
        
        ActiveSubmenu();
        document.querySelector(".main-content").classList.add("horizontal-content");
        let mainContainer = document.querySelectorAll(".main-container")
        mainContainer.forEach(e => e.classList.add("container"))
        mainContainer.forEach(e => e.classList.remove("container-fluid"))
        document.querySelector(".app-header").classList.add("hor-header");
        document.querySelector(".app-sidebar").classList.add("horizontal-main");
        document.querySelector(".main-sidemenu").classList.add("container");

        document.querySelector(".main-content").classList.remove("app-content");
        document.body.classList.remove('sidebar-mini');
        document.body.classList.remove('sidenav-toggled');
        responsive();
        menuClick();
        mainContent.addEventListener('click', slideClick);
        setTimeout(() => {
            if (window.innerWidth >= 992) {
                slideClick()
            }
            checkHoriMenu();
        }, 800)
        //
        checkOptions();
    }
}

function horizontalHoverFn() {
    $('#switchbtn-horizontalHover').prop('checked', true);
    let html = document.querySelector('html');
    html.setAttribute('data-layout', 'horizontal');
    html.setAttribute('data-hor-style', 'hor-hover');
    html.removeAttribute('data-vertical-style');
    let li = document.querySelectorAll('.side-menu li')

    if (!document.body.classList.contains('auth-page')) {

        document.querySelector(".main-content").classList.add("horizontal-content");
        document.querySelector(".main-content").classList.remove("app-content");
        let mainContainer = document.querySelectorAll(".main-container")
        mainContainer.forEach(e => e.classList.add("container"))
        mainContainer.forEach(e => e.classList.remove("container-fluid"))
        document.querySelector(".app-header").classList.add("hor-header");
        document.querySelector(".app-sidebar").classList.add("horizontal-main")
        document.querySelector(".main-sidemenu").classList.add("container")
        document.body.classList.remove('sidebar-mini');
        document.body.classList.remove('sidenav-toggled');
        responsive();
        menuClick();
        mainContent.removeEventListener('click', slideClick);
        //
        setTimeout(() => {
            if (window.innerWidth >= 992) {
                slideClick()
            }
            checkHoriMenu();
        }, 500)
        checkOptions();
    }
}
// RESET SWITCHER TO DEFAULT
let reset = document.querySelector('#resetbtn');
if (reset) {
    reset.addEventListener('click', () => {
        resetData();
    })
}

function resetData() {
    
	localStorage.clear();
    let html = document.querySelector('html');
    document.querySelector('html').style = "";
    names();
    
    $('#switchbtn-ltr').prop('checked', true);
    $('#switchbtn-rtl').prop('checked', false);
    $('#switchbtn-light').prop('checked', true);
    $('#switchbtn-lightmenu').prop('checked', true);
    $('#switchbtn-colorheader').prop('checked', true);
    $('#switchbtn-fullwidth').prop('checked', true);
    $('#switchbtn-fixed').prop('checked', true);
    $('#switchbtn-defaultmenu').prop('checked', true);
    $('#switchbtn-defaultlogo').prop('checked', true);
    html.setAttribute('data-theme-color', 'light');
    html.setAttribute('data-header-style', 'color');
    html.setAttribute('data-menu-style', 'light');
    html.setAttribute('data-width', 'fullwidth');
    html.setAttribute('data-position', 'fixed');
    html.setAttribute('data-logo', 'defaultlogo');
    html.setAttribute('data-skins', 'shadow');
    html.setAttribute('data-layout', 'vertical');
    html.setAttribute('data-vertical-style', 'default');
    document.body.classList.remove('sidenav-toggled');
    verticalFn();
    ltrFn();
    localStorage.clear();
    if (!document.body.classList.contains('auth-page')) {
        let mainHeader = document.querySelector('.app-header');
        mainHeader.style = "";
        let appSidebar = document.querySelector('.app-sidebar');
        appSidebar.style = "";
    
        //
        checkOptions();
        menuClick();
    }
}

export function checkOptions() {

    let html= document.querySelector('html')

    // dark
    if (html.getAttribute('data-theme-color') === "dark") {
        $('#switchbtn-dark').prop('checked', true);
        $('#switchbtn-darkmenu').prop('checked', true);
        $('#switchbtn-darkheader').prop('checked', true);
    }

    // horizontal
    if (html.getAttribute('data-hor-style') === "hor-click") {
        $('#switchbtn-horizontal').prop('checked', true);
    }

    // horizontal-hover
    if (html.getAttribute('data-hor-style') === "hor-hover")  {
        $('#switchbtn-horizontalHover').prop('checked', true);
    }

    //RTL 
    if (html.getAttribute('dir') === "rtl") {
        $('#switchbtn-rtl').prop('checked', true);
    }

    //LTR
    if (html.getAttribute('dir') === "ltr") {
        $('#switchbtn-ltr').prop('checked', true);
    }

    // light header 
    if (html.getAttribute('data-header-style') === "light") {
        $('#switchbtn-lightheader').prop('checked', true);
    }

    // color header 
    if (html.getAttribute('data-header-style') === "color") {
        $('#switchbtn-colorheader').prop('checked', true);
    }

    // gradient header 
    if (html.getAttribute('data-header-style') === "gradient") {
        $('#switchbtn-gradientheader').prop('checked', true);
    }

    // dark header 
    if (html.getAttribute('data-header-style') === "dark") {
        $('#switchbtn-darkheader').prop('checked', true);
    }

    // light menu
    if (html.getAttribute('data-menu-style') === 'light') {
        $('#switchbtn-lightmenu').prop('checked', true);
    }

    // color menu
    if (html.getAttribute('data-menu-style') === 'color') {
        $('#switchbtn-colormenu').prop('checked', true);
    }

    // gradient menu
    if (html.getAttribute('data-menu-style') === 'gradient') {
        $('#switchbtn-gradientmenu').prop('checked', true);
    }

    // dark menu
    if (html.getAttribute('data-menu-style') === 'dark') {
        $('#switchbtn-darkmenu').prop('checked', true);
    }

    //boxed 
    if (html.getAttribute('data-width') === 'boxed') {
        $('#switchbtn-boxed').prop('checked', true);
    }

    //scrollable 
    if (html.getAttribute('data-position') === 'scrollable') {
        $('#switchbtn-scrollable').prop('checked', true);
    }

    //centerlogo 
    if (html.getAttribute('data-logo') === 'centerlogo') {
        $('#switchbtn-centerlogo').prop('checked', true);
    }

    //vertical menus
   
    let verticalStyles = html.getAttribute('data-vertical-style');
    switch (verticalStyles) {
        case 'default':
            $('#switchbtn-defaultmenu').prop('checked', true);
            break;
        case 'closed':
            $('#switchbtn-closed').prop('checked', true);
            break;
        case 'icontext':
            $('#switchbtn-text').prop('checked', true);
            break;
        case 'overlay':
            $('#switchbtn-overlay').prop('checked', true);
            break;
        case 'hover':
            $('#switchbtn-hoversub').prop('checked', true);
            break;
        case 'hover1':
            $('#switchbtn-hoversub1').prop('checked', true);
            break;
        case 'doublemenu':
            $('#switchbtn-doublemenu').prop('checked', true);
            break;
        case 'doublemenu-tabs':
            $('#switchbtn-doublemenu-tabs').prop('checked', true);
            break;
        default:
            $('#switchbtn-defaultmenu').prop('checked', true);
            break;

    }
}

const handleThemeUpdate = (cssVars) => {
    const root = document.querySelector(':root');
    const keys = Object.keys(cssVars);
    keys.forEach(key => {
        root.style.setProperty(key, cssVars[key]);
    });
}

// to check the value is hexa or not
const isValidHex = (hexValue) => /^#([A-Fa-f0-9]{3,4}){1,2}$/.test(hexValue)

const getChunksFromString = (st, chunkSize) => st.match(new RegExp(`.{${chunkSize}}`, "g"))
// convert hex value to 256
const convertHexUnitTo256 = (hexStr) => parseInt(hexStr.repeat(2 / hexStr.length), 16)
// get alpha value is equla to 1 if there was no value is asigned to alpha in function
const getAlphafloat = (a, alpha) => {
    if (typeof a !== "undefined") { return a / 255 }
    if ((typeof alpha != "number") || alpha < 0 || alpha > 1) {
        return 1
    }
    return alpha
}
// convertion of hex code to rgba code
function hexToRgba(hexValue, alpha) {
    if (!isValidHex(hexValue)) { return null }
    const chunkSize = Math.floor((hexValue.length - 1) / 3)
    const hexArr = getChunksFromString(hexValue.slice(1), chunkSize)
    const [r, g, b, a] = hexArr.map(convertHexUnitTo256)
    return `rgba(${r}, ${g}, ${b}, ${getAlphafloat(a, alpha)})`
}

// convertion of hex code to rgb code
function hexToRgb(hexValue) {
    if (!isValidHex(hexValue)) { return null }
    const chunkSize = Math.floor((hexValue.length - 1) / 3)
    const hexArr = getChunksFromString(hexValue.slice(1), chunkSize)
    const [r, g, b] = hexArr.map(convertHexUnitTo256)
    return `${r}, ${g}, ${b}`
}


function dynamicPrimaryColor(primaryColor) {
    'use strict'

    primaryColor.forEach((item) => {
        item.addEventListener('input', (e) => {
            document.querySelector('html').style.setProperty('--primary-bg-color-rgb', hexToRgb(e.target.value));
            document.querySelector('html').style.setProperty('--primary-bg-color-rgb1', `${e.target.value}`);
        });
    });
}
function dynamicBackgroundColor(bgColor) {
    bgColor.forEach((item) => {
        item.addEventListener('input', (e) => {
            document.querySelector('html').style.setProperty('--background', hexToRgba(e.target.value, 0.8));
            document.querySelector('html').style.setProperty('--white', `${e.target.value}`);
            document.querySelector('html').style.setProperty('--menu-bg', `${e.target.value}`);
            document.querySelector('html').style.setProperty('--header-bg', `${e.target.value}`);
            if (!document.body.classList.contains('auth-page') && !document.querySelector(".error-page1")) {
                let mainHeader = document.querySelector('.app-header');
                mainHeader.setAttribute('style', `--header-bg: ${e.target.value}`)
                let appSidebar = document.querySelector('.app-sidebar');
                appSidebar.setAttribute('style', `--menu-bg: ${e.target.value}`)
                // appSidebar.setAttribute('style', `--white: ${e.target.value}`)
            }
        });
    });

}



function changePrimaryColor() {
    let userColor = document.getElementById('colorID').value;
    localStorage.setItem('solicprimaryColor', hexToRgb(userColor));
    localStorage.setItem('solicprimaryColor1', userColor);
    names()
}

function transparentBgColor() {
    let userColor1 = document.getElementById('transparentBgColorID').value;
    console.log(userColor1);
    localStorage.setItem('solicbgColor', hexToRgba(userColor1, 0.8));
    localStorage.setItem('solicbgwhite', userColor1);
    localStorage.setItem('solicmenubg', userColor1);
    localStorage.setItem('solicheaderbg', userColor1);
    names()
    let html = document.querySelector('html');
    html.setAttribute('data-theme-color', 'dark');
    html.setAttribute('data-menu-style', 'dark');
    html.setAttribute('data-header-style', 'dark');
    $('#switchbtn-dark').prop('checked', true);
}

// chart colors
let myVarVal, myVarVal2, myVarVal1,primaryColorVal,primaryColorVal1
export function names() {

    primaryColorVal = getComputedStyle(document.documentElement).getPropertyValue('--primary-bg-color-rgb').trim();
    primaryColorVal1 = getComputedStyle(document.documentElement).getPropertyValue('--primary-bg-color-rgb1').trim();
    // let primaryColorVal = getComputedStyle(document.documentElement).getPropertyValue('--primary-bg-color').trim();

    //get variable
    myVarVal = localStorage.getItem("solicprimaryColor") || primaryColorVal;
    myVarVal2 = localStorage.getItem("solicprimaryColor1") || primaryColorVal1;
    myVarVal1 = localStorage.getItem("solicprimaryColor") ? hexToRgba(localStorage.getItem("solicprimaryColor"), 0.8) : null;

    if (document.querySelector('#conversion') !== null) {
        data.conversionChart(myVarVal);
    }
    if (document.querySelector('#retunchart') !== null) {
        data.returnItens(myVarVal);
    }
    if (document.querySelector('#analytic') !== null) {
        data.analyticsChart(myVarVal);
    }
    if (document.querySelector('#totalvisitors') !== null) {
        data.totalVisitors(myVarVal);
    }
    if (document.querySelector('#total-orders') !== null) {
        data.totalOders(myVarVal)
    }
    if (document.querySelector('#perfectorder') !== null) {
        data.orderRate(myVarVal);
    }

}
names()


function localStorageBackup() {
    // if there is a value stored, update color picker and background color
    // Used to retrive the data from local storage
    if (localStorage.getItem("solicprimaryColor")) {
        if (document.getElementById('colorID')) {
            document.getElementById('colorID').value = localStorage.getItem("solicprimaryColor");
        }
        document.querySelector('html').style.setProperty('--primary-bg-color-rgb', localStorage.getItem("solicprimaryColor"));
        document.querySelector('html').style.setProperty('--primary-bg-color-rgb1', localStorage.getItem("solicprimaryColor1"));
    }
    if (localStorage.getItem("solicbgColor")) {
        if (document.getElementById('transparentBgColorID')) {
            document.getElementById('transparentBgColorID').value = localStorage.getItem("solicbgColor");
        }
        document.querySelector('html').style.setProperty('--background', localStorage.getItem("solicbgColor"));
        document.querySelector('html').style.setProperty('--white', localStorage.getItem("solicbgwhite"));
        document.querySelector('html').style.setProperty('--menu-bg', localStorage.getItem("solicmenubg"));
        document.querySelector('html').style.setProperty('--header-bg', localStorage.getItem("solicheaderbg"));
        let html = document.querySelector('html');
        html.setAttribute('data-theme-color', 'dark');
        html.setAttribute('data-menu-style', 'dark');
        html.setAttribute('data-header-style', 'dark');
        if (!document.body.classList.contains('auth-page') && !document.querySelector(".error-page1")) {
            let mainHeader = document.querySelector('.app-header');
            mainHeader.style.setProperty('--header-bg', localStorage.getItem("solicheaderbg"))
            let appSidebar = document.querySelector('.app-sidebar');
            appSidebar.style.setProperty('--menu-bg', localStorage.getItem("solicmenubg"));
            // appSidebar.style.setProperty('--white', localStorage.getItem("solicbgwhite"));
        }
        $('#switchbtn-dark').prop('checked', true);
        $('#switchbtn-darkmenu').prop('checked', true);
        $('#switchbtn-darkheader').prop('checked', true);

        localStorage.removeItem("solicHeader", 'dark');
        localStorage.removeItem("solicMenu", 'dark');
        localStorage.removeItem("solicHeader", 'light');
        localStorage.removeItem("solicMenu", 'light');
    }
    if (localStorage.solicdarktheme) {
        let html = document.querySelector('html');
        html.setAttribute('data-theme-color', 'dark');
    }
    if (localStorage.solicrtl) {
        let html = document.querySelector('html');
        html.setAttribute('dir', 'rtl');
    }
    if (localStorage.soliclayout) {
        let html = document.querySelector('html');
        let layoutValue = localStorage.getItem('soliclayout');
        html.setAttribute('data-layout', 'horizontal');
        switch (layoutValue) {
            case 'horizontal':
                html.setAttribute('data-hor-style', 'hor-click');
                break;
            case 'horizontalhover':
                html.setAttribute('data-hor-style', 'hor-hover');
                break;
        }
    }
    if (localStorage.solicverticalstyles) {
        let html = document.querySelector('html');
        let verticalStyles = localStorage.getItem('solicverticalstyles');
        if (!(document.body.classList.contains('auth-page'))) {
            switch (verticalStyles) {
                case 'closed':
                    hoverLayoutFn();
                    html.setAttribute('data-vertical-style', 'closed');
                    break;
                case 'icontext':
                    textLayoutFn();
                    html.setAttribute('data-vertical-style', 'icontext');
                    break;
                case 'overlay':
                    hoverLayoutFn();
                    html.setAttribute('data-vertical-style', 'overlay');
                    break;
                case 'hover':
                    hoverLayoutFn();
                    html.setAttribute('data-vertical-style', 'hover');
                    break;
                case 'hover1':
                    html.setAttribute('data-vertical-style', 'hover1');
                    hoverLayoutFn();
                    break;
                case 'doublemenu':
                    html.setAttribute('data-vertical-style', 'doublemenu');
                    doubleLayoutFn();
                    break;
                case 'doublemenu-tabs':
                    html.setAttribute('data-vertical-style', 'doublemenu-tabs');
                    doubleLayoutFn();
                    break;

            }
        }
    }
    if (localStorage.solicnoshadow) {
        let html = document.querySelector('html');
        html.setAttribute('data-skins', 'no-shadow');
    }
    if (localStorage.solicboxed) {
        let html = document.querySelector('html');
        html.setAttribute('data-width', 'boxed');
    }
    if (localStorage.solicscrollable) {
        let html = document.querySelector('html');
        html.setAttribute('data-position', 'scrollable');
    }
    if (localStorage.soliccenterlogo) {
        let html = document.querySelector('html');
        html.setAttribute('data-logo', 'centerlogo');
    }
    if (localStorage.solicMenu) {
        let html = document.querySelector('html');
        let menuValue = localStorage.getItem('solicMenu');
        switch (menuValue) {
            case 'light':
                html.setAttribute('data-menu-style', 'light');
                break;
            case 'dark':
                html.setAttribute('data-menu-style', 'dark');
                break;
            case 'color':
                html.setAttribute('data-menu-style', 'color');
                break;
            case 'gradient':
                html.setAttribute('data-menu-style', 'gradient');
                break;

            default:
                break;
        }
    }
    if (localStorage.solicHeader) {
        let html = document.querySelector('html');
        let headerValue = localStorage.getItem('solicHeader');
        switch (headerValue) {
            case 'light':
                html.setAttribute('data-header-style', 'light');
                break;
            case 'dark':
                html.setAttribute('data-header-style', 'dark');
                break;
            case 'color':
                html.setAttribute('data-header-style', 'color');
                break;
            case 'gradient':
                html.setAttribute('data-header-style', 'gradient');
                break;

            default:
                break;
        }
    }
}