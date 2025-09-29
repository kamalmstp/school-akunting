$(document).ready(function() {
    let url = window.location.href;

    $('.treeview').removeClass('active current-page');
    $('.treeview > ul.treeview-menu').removeClass('menu-open');
    $('.treeview-menu li a').removeClass('active-sub');

    function activateNav(groupClass, keywords) {
        let shouldActivate = keywords.some(keyword => url.includes(keyword));
        if (shouldActivate) {
            $(groupClass).addClass('active current-page');
            $(groupClass + ' > ul.treeview-menu').addClass('menu-open');
        }
        return shouldActivate;
    }

    function activateSub(subMenuClass, keywords) {
        keywords.some(keyword => {
            if (url.includes(keyword)) {
                $(subMenuClass).addClass('active-sub');
                return true;
            }
            return false;
        });
    }

    let isMaster = activateNav('.master', ['teachers', 'employees', 'school-majors', 'students', 'fund-managements', 'financial-periods', 'cash-managements']);
    if (isMaster) {
        activateSub('.m-first', ['teachers']);
        activateSub('.m-second', ['employees']);
        activateSub('.m-third', ['school-majors']);
        activateSub('.m-fourth', ['students']);
        activateSub('.m-fifth', ['fund-managements', 'cash-managements']);
        activateSub('.m-sixth', ['financial-periods']);
    }
    
    let isReceivable = activateNav('.receivable', ['receivables', 'receipts']);
    if (isReceivable) {
        activateSub('.p-first', ['student-receivables']);
        activateSub('.p-second', ['teacher-receivables']);
        activateSub('.p-third', ['employee-receivables']);
        activateSub('.p-fourth', ['student-receipts']);
    }

    activateNav('.alumni', ['alumni']);
    activateSub('.a-first', ['alumni']);

    let isTransaction = activateNav('.transaction', ['transactions/index', 'fixed-assets']);
    if (isTransaction) {
        activateSub('.tr-first', ['transactions/index']);
        activateSub('.tr-second', ['fixed-assets']);
    }

    let isReport = activateNav('.report', ['/reports/']);
    if (isReport) {
        activateSub('.rp-first', ['beginning-balance']);
        activateSub('.rp-second', ['general-journal']);
        activateSub('.rp-third', ['/reports/ledger']);
        activateSub('.rp-fourth', ['trial-balance-before']);
        activateSub('.rp-fifth', ['adjusting-entries']);
        activateSub('.rp-sixth', ['trial-balance-after']);
        activateSub('.rp-seventh', ['financial-statements']);
        activateSub('.rp-eighth', ['cash-reports']);
        activateSub('.rp-nineth', ['rkas-global']);
    }

    if (url.includes('/accounts') && !url.includes('school-accounts')) {
         $('.sidebar-menu > li:has(a[href*="/accounts"])').addClass('active current-page');
    }
    if (url.includes('school-accounts')) {
         $('.sidebar-menu > li:has(a[href*="school-accounts"])').addClass('active current-page');
    }
    
    if (url.endsWith('/dashboard') || url.endsWith('/schools')) {
        $('.sidebar-menu > li:has(a[href$="/dashboard"])').addClass('active current-page');
        $('.sidebar-menu > li:has(a[href$="/schools"])').addClass('active current-page');
    }
});