$(document).ready(function() {
    let url = window.location.href;

    if (url.includes('dashboard')) {
    }

    if (url.includes('schools')) {
    }

    // --- Data Master ---
    if (url.includes('teachers') || url.includes('employees') || url.includes('school-majors') || url.includes('students') || url.includes('fund-managements') || url.includes('financial-periods')) {
        $('.master').addClass('active current-page');
        $('.master').find('> a').click(); 
    }

    if (url.includes('teachers') && !url.includes('receivables')) {
        $('.m-first').addClass('active-sub');
    }
    if (url.includes('employees') && !url.includes('receivables')) {
        $('.m-second').addClass('active-sub');
    }
    if (url.includes('school-majors')) {
        $('.m-third').addClass('active-sub');
    }
    if (url.includes('students') && !url.includes('receivables') && !url.includes('alumni') && !url.includes('receipts')) {
        $('.m-fourth').addClass('active-sub');
    }
    if (url.includes('fund-managements') || url.includes('cash-managements')) {
        $('.m-fifth').addClass('active-sub');
    }
    if (url.includes('financial-periods')) {
        $('.m-sixth').addClass('active-sub');
    }

    // --- Penerimaan (Receivable) ---
    if (url.includes('receivables') || url.includes('receipts')) {
        $('.receivable').addClass('active current-page');
        $('.receivable').find('> a').click();
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
    if (url.includes('student-receipts')) {
        $('.p-fourth').addClass('active-sub');
    }

    // --- Alumni ---
    if (url.includes('alumni')) {
        $('.alumni').addClass('active current-page');
        $('.alumni').find('> a').click();
        $('.a-first').addClass('active-sub');
    }

    // --- Transaksi (Transaction) ---
    if (url.includes('transactions') || url.includes('fixed-assets')) {
        $('.transaction').addClass('active current-page');
        $('.transaction').find('> a').click();
    }
    if (url.includes('transactions') && !url.includes('reports')) { // Pastikan bukan route laporan
        $('.tr-first').addClass('active-sub');
    }
    if (url.includes('fixed-assets')) {
        $('.tr-second').addClass('active-sub');
    }

    // --- Laporan (Report) ---
    if (url.includes('reports')) {
        $('.report').addClass('active current-page');
        $('.report').find('> a').click();
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
});