$(document).ready(function() {
    setTimeout(function() {
        let url = window.location.href;

        $('.treeview').removeClass('active current-page');
        $('.treeview > ul.treeview-menu').removeClass('menu-open');
        $('.treeview-menu li a').removeClass('active-sub');

        if (url.includes('/teachers') || url.includes('/employees') || url.includes('school-majors') || url.includes('/students') || url.includes('fund-managements') || url.includes('financial-periods') || url.includes('cash-managements')) {
            $('.master').addClass('active current-page');
            $('.master > ul.treeview-menu').addClass('menu-open');
        }

        if (url.includes('/teachers') && !url.includes('-receivables')) {
            $('.m-first').addClass('active-sub');
        }
        if (url.includes('/employees') && !url.includes('-receivables')) {
            $('.m-second').addClass('active-sub');
        }
        if (url.includes('school-majors')) {
            $('.m-third').addClass('active-sub');
        }
        if (url.includes('/students') && !url.includes('-receivables') && !url.includes('-alumni') && !url.includes('-receipts')) {
            $('.m-fourth').addClass('active-sub');
        }
        if (url.includes('fund-managements') || url.includes('cash-managements')) {
            $('.m-fifth').addClass('active-sub');
        }
        if (url.includes('financial-periods')) {
            $('.m-sixth').addClass('active-sub');
        }

        if (url.includes('-receivables') || url.includes('-receipts')) {
            $('.receivable').addClass('active current-page');
            $('.receivable > ul.treeview-menu').addClass('menu-open');
        }

        if (url.includes('student-receivables')) {
            $('.p-first').addClass('active-sub');
        }
        if (url.includes('teacher-receivables')) {
            $('.p-second').addClass('active-sub');
        }
        if (url.includes('employee-receivables')) {
            $('.p-third').addClass('active-sub');
        }
        if (url.includes('student-receipts') || url.includes('receipts/filter')) {
            $('.p-fourth').addClass('active-sub');
        }

        if (url.includes('-alumni')) {
            $('.alumni').addClass('active current-page');
            $('.alumni > ul.treeview-menu').addClass('menu-open');
            $('.a-first').addClass('active-sub');
        }

        if (url.includes('transactions/index') || url.includes('fixed-assets')) {
            $('.transaction').addClass('active current-page');
            $('.transaction > ul.treeview-menu').addClass('menu-open');
        }
        
        if (url.includes('transactions/index') && !url.includes('reports')) {
            $('.tr-first').addClass('active-sub');
        }
        if (url.includes('fixed-assets')) {
            $('.tr-second').addClass('active-sub');
        }

        if (url.includes('/reports/')) {
            $('.report').addClass('active current-page');
            $('.report > ul.treeview-menu').addClass('menu-open');
        }
        
        if (url.includes('reports/beginning-balance')) {
            $('.rp-first').addClass('active-sub');
        }
        if (url.includes('reports/general-journal')) {
            $('.rp-second').addClass('active-sub');
        }
        if (url.includes('reports/ledger')) {
            $('.rp-third').addClass('active-sub');
        }
        if (url.includes('reports/trial-balance-before')) {
            $('.rp-fourth').addClass('active-sub');
        }
        if (url.includes('reports/adjusting-entries')) {
            $('.rp-fifth').addClass('active-sub');
        }
        if (url.includes('reports/trial-balance-after')) {
            $('.rp-sixth').addClass('active-sub');
        }
        if (url.includes('reports/financial-statements')) {
            $('.rp-seventh').addClass('active-sub');
        }
        if (url.includes('reports/cash-reports')) {
            $('.rp-eighth').addClass('active-sub');
        }
        if (url.includes('reports/rkas-global')) {
            $('.rp-nineth').addClass('active-sub');
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
    }, 50);
});