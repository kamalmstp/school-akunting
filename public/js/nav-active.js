$(document).ready(function () {
    const url = window.location.href;
    if (url.includes('transactions')) {
        $('.transaction').addClass('active current-page');
        $('.transaction > ul').addClass('menu-open');
        $('.tr-first').addClass('active-sub');
    }
    if (url.includes('fixed-assets')) {
        $('.transaction').addClass('active current-page');
        $('.transaction > ul').addClass('menu-open');
        $('.tr-second').addClass('active-sub');
    }
    if (url.includes('beginning-balance')) {
        $('.report').addClass('active current-page');
        $('.report > ul').addClass('menu-open');
        $('.rp-first').addClass('active-sub');
    }
    if (url.includes('general-journal')) {
        $('.report').addClass('active current-page');
        $('.report > ul').addClass('menu-open');
        $('.rp-second').addClass('active-sub');
    }
    if (url.includes('ledger')) {
        $('.report').addClass('active current-page');
        $('.report > ul').addClass('menu-open');
        $('.rp-third').addClass('active-sub');
    }
    if (url.includes('trial-balance-before')) {
        $('.report').addClass('active current-page');
        $('.report > ul').addClass('menu-open');
        $('.rp-fourth').addClass('active-sub');
    }
    if (url.includes('adjusting-entries')) {
        $('.report').addClass('active current-page');
        $('.report > ul').addClass('menu-open');
        $('.rp-fifth').addClass('active-sub');
    }
    if (url.includes('trial-balance-after')) {
        $('.report').addClass('active current-page');
        $('.report > ul').addClass('menu-open');
        $('.rp-sixth').addClass('active-sub');
    }
    if (url.includes('financial-statements')) {
        $('.report').addClass('active current-page');
        $('.report > ul').addClass('menu-open');
        $('.rp-seventh').addClass('active-sub');
    }
    if (url.includes('cash-reports')) {
        $('.report').addClass('active current-page');
        $('.report > ul').addClass('menu-open');
        $('.rp-eighth').addClass('active-sub');
    }

    if (url.includes('rkas')) {
        $('.report').addClass('active current-page');
        $('.report > ul').addClass('menu-open');
        $('.rp-nineth').addClass('active-sub');
    }
    if (url.includes('teachers')) {
        $('.master').addClass('active current-page');
        $('.master > ul').addClass('menu-open');
        $('.m-first').addClass('active-sub');
    }
    if (url.includes('employees')) {
        $('.master').addClass('active current-page');
        $('.master > ul').addClass('menu-open');
        $('.m-second').addClass('active-sub');
    }
    if (url.includes('majors')) {
        $('.master').addClass('active current-page');
        $('.master > ul').addClass('menu-open');
        $('.m-third').addClass('active-sub');
    }
    if (url.includes('students')) {
        $('.master').addClass('active current-page');
        $('.master > ul').addClass('menu-open');
        $('.m-fourth').addClass('active-sub');
    }
    if (url.includes('cash-managements')) {
        $('.master').addClass('active current-page');
        $('.master > ul').addClass('menu-open');
        $('.m-fifth').addClass('active-sub');
    }
    if (url.includes('financial-periods')) {
        $('.master').addClass('active current-page');
        $('.master > ul').addClass('menu-open');
        $('.m-sixth').addClass('active-sub');
    }
    
    if (url.includes('student-receivables')) {
        $('.receivable').addClass('active current-page');
        $('.receivable > ul').addClass('menu-open');
        $('.p-first').addClass('active-sub');
    }
    if (url.includes('teacher-receivables')) {
        $('.receivable').addClass('active current-page');
        $('.receivable > ul').addClass('menu-open');
        $('.p-second').addClass('active-sub');
    }
    if (url.includes('employee-receivables')) {
        $('.receivable').addClass('active current-page');
        $('.receivable > ul').addClass('menu-open');
        $('.p-third').addClass('active-sub');
    }
    if (url.includes('student-receipts')) {
        $('.receivable').addClass('active current-page');
        $('.receivable > ul').addClass('menu-open');
        $('.p-fourth').addClass('active-sub');
    }
    if (url.includes('student-alumni')) {
        $('.alumni').addClass('active current-page');
        $('.alumni > ul').addClass('menu-open');
        $('.a-first').addClass('active-sub');
    }
})