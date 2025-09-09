<?php

use App\Http\Controllers\SchoolController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\StudentReceivableController;
use App\Http\Controllers\TeacherReceivableController;
use App\Http\Controllers\EmployeeReceivableController;
use App\Http\Controllers\StudentAlumniController;
use App\Http\Controllers\FixedAssetController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SchoolMajorController;
use App\Http\Controllers\FundManagementController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\VerifyController;
use App\Http\Controllers\FinancialPeriodController;
use App\Http\Controllers\InitialBalanceController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/receipts/verify/{code}', [VerifyController::class, 'verify'])->name('receipts.verify');

Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/reports/beginning-balance/{school?}', [ReportController::class, 'beginningBalance'])->name('beginning-balance');
    Route::get('/reports/general-journal/{school?}', [ReportController::class, 'generalJournal'])->name('general-journal');
    Route::get('/reports/ledger/{school?}', [ReportController::class, 'ledger'])->name('ledger');
    Route::get('/reports/trial-balance-before/{school?}', [ReportController::class, 'trialBalanceBefore'])->name('trial-balance-before');
    Route::get('/reports/adjusting-entries/{school?}', [ReportController::class, 'adjustingEntries'])->name('adjusting-entries');
    Route::get('/reports/trial-balance-after/{school?}', [ReportController::class, 'trialBalanceAfter'])->name('trial-balance-after');
    Route::get('/reports/financial-statements/{school?}', [ReportController::class, 'financialStatements'])->name('financial-statements');
    Route::get('/reports/cash-reports/{school?}', [ReportController::class, 'cashReports'])->name('cash-reports');
    Route::get('/export-transaction', [TransactionController::class, 'exportTransaction'])->name('export-transaction');

    // Super Admin Routes
    Route::middleware(['role:SuperAdmin'])->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
        Route::resource('transactions', TransactionController::class);
        Route::resource('student-receivables', StudentReceivableController::class)->only(['create', 'store']);
        Route::resource('teacher-receivables', TeacherReceivableController::class)->only(['create', 'store']);
        Route::resource('employee-receivables', EmployeeReceivableController::class)->only(['create', 'store']);
        Route::resource('fixed-assets', FixedAssetController::class)->only(['create', 'store']);

        Route::resource('students', StudentController::class)->only(['create', 'store']);
        Route::get('/students/import', [StudentController::class, 'importForm'])->name('students.import-form');
        Route::post('/students/import', [StudentController::class, 'import'])->name('students.import');

        Route::resource('accounts', AccountController::class)->only(['create', 'store']);
        Route::get('/accounts/import', [AccountController::class, 'importForm'])->name('accounts.import-form');
        Route::post('/accounts/import', [AccountController::class, 'import'])->name('accounts.import');

        Route::resource('teachers', TeacherController::class)->only(['create', 'store']);
        Route::get('/teachers/import', [TeacherController::class, 'importForm'])->name('teachers.import-form');
        Route::post('/teachers/import', [TeacherController::class, 'import'])->name('teachers.import');
        
        Route::resource('employees', EmployeeController::class)->only(['create', 'store']);
        Route::get('employees/import', [EmployeeController::class, 'importForm'])->name('employees.import-form');
        Route::post('employees/import', [EmployeeController::class, 'import'])->name('employees.import');

        Route::resource('school-majors', SchoolMajorController::class)->only(['create', 'store']);
        Route::resource('fund-managements', FundManagementController::class)->only(['create', 'store']);

        Route::get('payments/create', [SettingController::class, 'create'])->name('schedules.create');
        Route::post('payments/store', [SettingController::class, 'store'])->name('schedules.store');
    });

    // Super Admin, Admin Monitor Routes
    Route::middleware(['role:SuperAdmin,AdminMonitor'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('schools', SchoolController::class);
        Route::get('/reports/beginning-balance', [ReportController::class, 'beginningBalance'])->name('reports.beginning-balance');
        Route::get('/reports/general-journal', [ReportController::class, 'generalJournal'])->name('reports.general-journal');
        Route::get('/reports/ledger', [ReportController::class, 'ledger'])->name('reports.ledger');
        Route::get('/reports/trial-balance-before', [ReportController::class, 'trialBalanceBefore'])->name('reports.trial-balance-before');
        Route::get('/reports/adjusting-entries', [ReportController::class, 'adjustingEntries'])->name('reports.adjusting-entries');
        Route::get('/reports/trial-balance-after', [ReportController::class, 'trialBalanceAfter'])->name('reports.trial-balance-after');
        Route::get('/reports/financial-statements', [ReportController::class, 'financialStatements'])->name('reports.financial-statements');
        Route::get('/reports/cash-reports', [ReportController::class, 'cashReports'])->name('reports.cash-reports');
        Route::resource('transactions', TransactionController::class)->only(['index']);
        Route::resource('accounts', AccountController::class)->only(['index']);
        Route::resource('students', StudentController::class)->only(['index']);
        Route::resource('student-receivables', StudentReceivableController::class)->only(['index']);
        Route::resource('student-alumni', StudentAlumniController::class)->only(['index']);
        Route::resource('teachers', TeacherController::class)->only(['index']);
        Route::resource('teacher-receivables', TeacherReceivableController::class)->only(['index']);
        Route::resource('employees', EmployeeController::class)->only(['index']);
        Route::resource('employee-receivables', EmployeeReceivableController::class)->only(['index']);
        Route::resource('fixed-assets', FixedAssetController::class)->only(['index']);
        Route::resource('school-majors', SchoolMajorController::class)->only(['index']);
        Route::resource('fund-managements', FundManagementController::class)->only(['index']);
        Route::get('payments', [SettingController::class, 'index'])->name('schedules.index');
    });

    // School Admin Routes
    Route::middleware(['role:SchoolAdmin', 'school.access'])->group(function () {
        Route::get('/schools/{school}/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

        Route::resource('schools/{school}/students', StudentController::class)->except(['show'])->names([
            'index' => 'school-students.index',
            'create' => 'school-students.create',
            'store' => 'school-students.store'
        ]);
        Route::get('schools/{school}/students/import', [StudentController::class, 'importForm'])->name('school-students.import-form');
        Route::post('schools/{school}/students/import', [StudentController::class, 'import'])->name('school-students.import');

        Route::resource('schools/{school}/accounts', AccountController::class)->except(['show'])->names([
            'index' => 'school-accounts.index',
            'create' => 'school-accounts.create',
            'store' => 'school-accounts.store'
        ]);
        Route::get('schools/{school}/accounts/import', [AccountController::class, 'importForm'])->name('school-accounts.import-form');
        Route::post('schools/{school}/accounts/import', [AccountController::class, 'import'])->name('school-accounts.import');

        Route::resource('schools/{school}/teachers', TeacherController::class)->except(['show'])->names([
            'index' => 'school-teachers.index',
            'create' => 'school-teachers.create',
            'store' => 'school-teachers.store'
        ]);
        Route::get('schools/{school}/teachers/import', [TeacherController::class, 'importForm'])->name('school-teachers.import-form');
        Route::post('schools/{school}/teachers/import', [TeacherController::class, 'import'])->name('school-teachers.import');

        Route::resource('schools/{school}/employees', EmployeeController::class)->except(['show'])->names([
            'index' => 'school-employees.index',
            'create' => 'school-employees.create',
            'store' => 'school-employees.store'
        ]);
        Route::get('schools/{school}/employees/import', [EmployeeController::class, 'importForm'])->name('school-employees.import-form');
        Route::post('schools/{school}/employees/import', [EmployeeController::class, 'import'])->name('school-employees.import');

        Route::resource('schools/{school}/majors', SchoolMajorController::class)->except(['show', 'edit', 'update', 'destroy'])->names([
            'index' => 'school-school-majors.index',
            'create' => 'school-school-majors.create',
            'store' => 'school-school-majors.store'
        ]);

        Route::resource('schools/{school}/funds', FundManagementController::class)->except(['show', 'edit', 'update', 'destroy'])->names([
            'index' => 'school-fund-managements.index',
            'create' => 'school-fund-managements.create',
            'store' => 'school-fund-managements.store'
        ]);

        Route::resource('schools/{school}/transactions', TransactionController::class)->names([
            'index' => 'school-transactions.index',
            'create' => 'school-transactions.create',
            'store' => 'school-transactions.store',
        ]);

        Route::resource('schools/{school}/student-receivables', StudentReceivableController::class)->names([
            'index' => 'school-student-receivables.index',
            'create' => 'school-student-receivables.create',
            'store' => 'school-student-receivables.store',
        ]);

        Route::resource('schools/{school}/student-alumni', StudentAlumniController::class)->names([
            'index' => 'school-student-alumni.index',
        ]);

        Route::resource('schools/{school}/teacher-receivables', TeacherReceivableController::class)->names([
            'index' => 'school-teacher-receivables.index',
            'create' => 'school-teacher-receivables.create',
            'store' => 'school-teacher-receivables.store',
        ]);       
        
        Route::resource('schools/{school}/employee-receivables', EmployeeReceivableController::class)->names([
            'index' => 'school-employee-receivables.index',
            'create' => 'school-employee-receivables.create',
            'store' => 'school-employee-receivables.store',
        ]);

        Route::resource('schools/{school}/fixed-assets', FixedAssetController::class)->names([
            'index' => 'school-fixed-assets.index',
            'create' => 'school-fixed-assets.create',
            'store' => 'school-fixed-assets.store',
        ]);

        Route::resource('schools/{school}/payments', SettingController::class)->except(['show', 'edit', 'update', 'destroy'])->names([
            'index' => 'school-schedules.index',
            'create' => 'school-schedules.create',
            'store' => 'school-schedules.store',
        ]);

        Route::get('/schools/{school}/reports/beginning-balance', [ReportController::class, 'beginningBalance'])->name('school-reports.beginning-balance');
        Route::get('/schools/{school}/reports/general-journal', [ReportController::class, 'generalJournal'])->name('school-reports.general-journal');
        Route::get('/schools/{school}/reports/ledger', [ReportController::class, 'ledger'])->name('school-reports.ledger');
        Route::get('/schools/{school}/reports/trial-balance-before', [ReportController::class, 'trialBalanceBefore'])->name('school-reports.trial-balance-before');
        Route::get('/schools/{school}/reports/adjusting-entries', [ReportController::class, 'adjustingEntries'])->name('school-reports.adjusting-entries');
        Route::get('/schools/{school}/reports/trial-balance-after', [ReportController::class, 'trialBalanceAfter'])->name('school-reports.trial-balance-after');
        Route::get('/schools/{school}/reports/financial-statements', [ReportController::class, 'financialStatements'])->name('school-reports.financial-statements');
        Route::get('/schools/{school}/reports/cash-reports', [ReportController::class, 'cashReports'])->name('school-reports.cash-reports');
    });

    Route::middleware(['role:SuperAdmin,SchoolAdmin,AdminMonitor'])->group(function() {
        Route::post('student-receivables/student/filter', [StudentReceivableController::class, 'getStudent'])->name('student-receivables.filter');
        Route::post('student-alumni/student/filter', [StudentAlumniController::class, 'getStudent'])->name('student-alumni.filter');
        Route::post('student-alumni/year/filter', [StudentAlumniController::class, 'getYear']);
        Route::post('student-receivables/payment-history/filter', [StudentReceivableController::class, 'getPaymentHistory']);
        Route::post('teacher-receivables/payment-history/filter', [TeacherReceivableController::class, 'getPaymentHistory']);
        Route::post('employee-receivables/payment-history/filter', [EmployeeReceivableController::class, 'getPaymentHistory']);
        Route::post('teacher-receivables/teacher/filter', [TeacherReceivableController::class, 'getTeacher'])->name('teacher-receivables.filter');
        Route::post('employee-receivables/employee/filter', [EmployeeReceivableController::class, 'getEmployee'])->name('employee-receivables.filter');
        Route::post('transactions/account-parent', [TransactionController::class, 'getAccountParent'])->name('transactions.getAccountParent');
        Route::post('transactions/fund-source', [TransactionController::class, 'getFundSource'])->name('transactions.getFundSource');
        Route::get('/users/profile', [UserController::class, 'profile'])->name('users.profile');
        Route::put('/users/profile/{user}', [UserController::class, 'edit_profile'])->name('users.edit-profile');
        Route::post('/users/reset/passsword', [UserController::class, 'resetPassword'])->name('users.resetPassword');
        Route::get('/student-receivables/detail/{receivableId}', [StudentReceivableController::class, 'getReceivableDetail'])->name('student-receivables.detail');
        Route::get('/teacher-receivables/detail/{receivableId}', [TeacherReceivableController::class, 'getReceivableDetail'])->name('teacher-receivables.detail');
        Route::get('/employee-receivables/detail/{receivableId}', [EmployeeReceivableController::class, 'getReceivableDetail'])->name('employee-receivables.detail');
    });

    Route::middleware(['role:SuperAdmin,SchoolAdmin'])->group(function() {
        Route::get('/schools/{school}/transactions/{transaction}', [TransactionController::class, 'show'])->name('school-transactions.show');
        Route::get('/schools/{school}/transactions/{transaction}/edit', [TransactionController::class, 'edit'])->name('school-transactions.edit');
        Route::put('/schools/{school}/transactions/{transaction}', [TransactionController::class, 'update'])->name('school-transactions.update');
        Route::delete('/schools/{school}/transactions/{transaction}', [TransactionController::class, 'destroy'])->name('school-transactions.destroy');

        Route::get('/schools/{school}/fixed-assets/{fixed_asset}', [FixedAssetController::class, 'show'])->name('school-fixed-assets.show');
        Route::get('/schools/{school}/fixed-assets/{fixed_asset}/edit', [FixedAssetController::class, 'edit'])->name('school-fixed-assets.edit');
        Route::put('/schools/{school}/fixed-assets/{fixed_asset}', [FixedAssetController::class, 'update'])->name('school-fixed-assets.update');
        Route::delete('/schools/{school}/fixed-assets/{fixed_asset}', [FixedAssetController::class, 'destroy'])->name('school-fixed-assets.destroy');
        Route::get('schools/{school}/fixed-assets/{fixed_asset}/depreciate', [FixedAssetController::class, 'depreciateForm'])->name('school-fixed-assets.depreciate');
        Route::post('schools/{school}/fixed-assets/{fixed_asset}/depreciate', [FixedAssetController::class, 'depreciate']);

        Route::get('/schools/{school}/student-receivables/{student_receivable}', [StudentReceivableController::class, 'show'])->name('school-student-receivables.show');
        Route::get('/schools/{school}/student-receivables/{student_receivable}/edit', [StudentReceivableController::class, 'edit'])->name('school-student-receivables.edit');
        Route::put('/schools/{school}/student-receivables/{student_receivable}', [StudentReceivableController::class, 'update'])->name('school-student-receivables.update');
        Route::delete('/schools/{school}/student-receivables/{student_receivable}', [StudentReceivableController::class, 'destroy'])->name('school-student-receivables.destroy');
        Route::get('/schools/{school}/student-receivables/{student_receivable}/pay', [StudentReceivableController::class, 'payForm'])->name('school-student-receivables.pay');
        Route::post('/schools/{school}/student-receivables/{student_receivable}/pay', [StudentReceivableController::class, 'pay']);        
        Route::get('/schools/{school}/student-receivables/receivable/{student_receivable_detail}/pay', [StudentReceivableController::class, 'editPayForm'])->name('school-student-receivables.edit-pay');
        Route::get('/schools/{school}/student-receivables/receivable/{student_receivable_detail}/receipt', [StudentReceivableController::class, 'receipt'])->name('school-student-receivables.receipt');
        Route::get('/schools/{school}/student-receivables/{student_receivable}/receipt-all', [StudentReceivableController::class, 'receiptAll'])->name('school-student-receivables.receipt-all');
        Route::post('/schools/{school}/student-receivables/receivable/{student_receivable_detail}/pay', [StudentReceivableController::class, 'editPay']);

        Route::get('/schools/{school}/teacher-receivables/{teacher_receivable}', [TeacherReceivableController::class, 'show'])->name('school-teacher-receivables.show');
        Route::get('/schools/{school}/teacher-receivables/{teacher_receivable}/edit', [TeacherReceivableController::class, 'edit'])->name('school-teacher-receivables.edit');
        Route::put('/schools/{school}/teacher-receivables/{teacher_receivable}', [TeacherReceivableController::class, 'update'])->name('school-teacher-receivables.update');
        Route::delete('/schools/{school}/teacher-receivables/{teacher_receivable}', [TeacherReceivableController::class, 'destroy'])->name('school-teacher-receivables.destroy');
        Route::get('/schools/{school}/teacher-receivables/{teacher_receivable}/pay', [TeacherReceivableController::class, 'payForm'])->name('school-teacher-receivables.pay');
        Route::post('/schools/{school}/teacher-receivables/{teacher_receivable}/pay', [TeacherReceivableController::class, 'pay']);
        Route::get('/schools/{school}/teacher-receivables/receivable/{teacher_receivable_detail}/pay', [TeacherReceivableController::class, 'editPayForm'])->name('school-teacher-receivables.edit-pay');
        Route::get('/schools/{school}/teacher-receivables/receivable/{teacher_receivable_detail}/receipt', [TeacherReceivableController::class, 'receipt'])->name('school-teacher-receivables.receipt');
        Route::post('/schools/{school}/teacher-receivables/receivable/{teacher_receivable_detail}/pay', [TeacherReceivableController::class, 'editPay']);

        Route::get('/schools/{school}/employee-receivables/{employee_receivable}', [EmployeeReceivableController::class, 'show'])->name('school-employee-receivables.show');
        Route::get('/schools/{school}/employee-receivables/{employee_receivable}/edit', [EmployeeReceivableController::class, 'edit'])->name('school-employee-receivables.edit');
        Route::put('/schools/{school}/employee-receivables/{employee_receivable}', [EmployeeReceivableController::class, 'update'])->name('school-employee-receivables.update');
        Route::delete('/schools/{school}/employee-receivables/{employee_receivable}', [EmployeeReceivableController::class, 'destroy'])->name('school-employee-receivables.destroy');
        Route::get('/schools/{school}/employee-receivables/{employee_receivable}/pay', [EmployeeReceivableController::class, 'payForm'])->name('school-employee-receivables.pay');
        Route::post('/schools/{school}/employee-receivables/{employee_receivable}/pay', [EmployeeReceivableController::class, 'pay']);
        Route::get('/schools/{school}/employee-receivables/receivable/{employee_receivable_detail}/pay', [EmployeeReceivableController::class, 'editPayForm'])->name('school-employee-receivables.edit-pay');
        Route::get('/schools/{school}/employee-receivables/receivable/{employee_receivable_detail}/receipt', [EmployeeReceivableController::class, 'receipt'])->name('school-employee-receivables.receipt');
        Route::post('/schools/{school}/employee-receivables/receivable/{employee_receivable_detail}/pay', [EmployeeReceivableController::class, 'editPay']);

        Route::get('/schools/{school}/student-receipts/filter', [ReceiptController::class, 'filterForm'])->name('school-student-receipts.filter');
        Route::get('/schools/{school}/student-receipts/{student}', [ReceiptController::class, 'previewByStudent'])->name('school-student-receipts.previewByStudent');
        Route::get('/schools/{school}/student-receipts/{student}/print/{date}', [ReceiptController::class, 'printByStudentAndDate'])->name('school-student-receipts.printByStudentAndDate');
        Route::post('/schools/{school}/student-receipts/print', [ReceiptController::class, 'printByDate'])->name('school-student-receipts.print');

        Route::get('/schools/{school}/majors/{school_major}', [SchoolMajorController::class, 'show'])->name('school-school-majors.show');
        Route::get('/schools/{school}/majors/{school_major}/edit', [SchoolMajorController::class, 'edit'])->name('school-school-majors.edit');
        Route::put('/schools/{school}/majors/{school_major}', [SchoolMajorController::class, 'update'])->name('school-school-majors.update');
        Route::delete('/schools/{school}/majors/{school_major}', [SchoolMajorController::class, 'destroy'])->name('school-school-majors.destroy');

        Route::get('/schools/{school}/funds/{fund_management}', [FundManagementController::class, 'show'])->name('school-fund-managements.show');
        Route::get('/schools/{school}/funds/{fund_management}/edit', [FundManagementController::class, 'edit'])->name('school-fund-managements.edit');
        Route::put('/schools/{school}/funds/{fund_management}', [FundManagementController::class, 'update'])->name('school-fund-managements.update');
        Route::delete('/schools/{school}/funds/{fund_management}', [FundManagementController::class, 'destroy'])->name('school-fund-managements.destroy');
        
        Route::get('/schools/{school}/students/{student}/edit', [StudentController::class, 'edit'])->name('school-students.edit');
        Route::put('/schools/{school}/students/{student}', [StudentController::class, 'update'])->name('school-students.update');
        Route::delete('/schools/{school}/students/{student}', [StudentController::class, 'destroy'])->name('school-students.destroy');
        Route::get('students/download-template', [StudentController::class, 'downloadTemplate'])->name('students.download-template');

        Route::get('/schools/{school}/accounts/{account}/edit', [AccountController::class, 'edit'])->name('school-accounts.edit');
        Route::put('/schools/{school}/accounts/{account}', [AccountController::class, 'update'])->name('school-accounts.update');
        Route::delete('/schools/{school}/accounts/{account}', [AccountController::class, 'destroy'])->name('school-accounts.destroy');
        Route::get('accounts/download-template', [AccountController::class, 'downloadTemplate'])->name('accounts.download-template');

        Route::get('/schools/{school}/teachers/{teacher}/edit', [TeacherController::class, 'edit'])->name('school-teachers.edit');
        Route::put('/schools/{school}/teachers/{teacher}', [TeacherController::class, 'update'])->name('school-teachers.update');
        Route::delete('/schools/{school}/teachers/{teacher}', [TeacherController::class, 'destroy'])->name('school-teachers.destroy');
        Route::get('teachers/download-template', [TeacherController::class, 'downloadTemplate'])->name('teachers.download-template');

        Route::get('/schools/{school}/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('school-employees.edit');
        Route::put('/schools/{school}/employees/{employee}', [EmployeeController::class, 'update'])->name('school-employees.update');
        Route::delete('/schools/{school}/employees/{employee}', [EmployeeController::class, 'destroy'])->name('school-employees.destroy');
        Route::get('employees/download-template', [EmployeeController::class, 'downloadTemplate'])->name('employees.download-template');

        Route::get('/schools/{school}/payments/{schedule}', [SettingController::class, 'show'])->name('school-schedules.show');
        Route::get('/schools/{school}/payments/{schedule}/edit', [SettingController::class, 'edit'])->name('school-schedules.edit');
        Route::put('/schools/{school}/payments/{schedule}', [SettingController::class, 'update'])->name('school-schedules.update');
        Route::delete('/schools/{school}/payments/{schedule}', [SettingController::class, 'destroy'])->name('school-schedules.destroy');

        Route::prefix('schools/{school}')->name('school-')->group(function () {   
            Route::resource('financial-periods', FinancialPeriodController::class);
            Route::prefix('financial-periods/{financialPeriod}')->name('initial-balances.')->group(function () {
                Route::get('initial-balances', [InitialBalanceController::class, 'index'])->name('index');
                Route::get('initial-balances/edit', [InitialBalanceController::class, 'edit'])->name('edit');
                Route::put('initial-balances', [InitialBalanceController::class, 'update'])->name('update');
            });
            Route::post('financial-periods/{financialPeriod}/copy-balances', [FinancialPeriodController::class, 'copyBalances'])->name('financial-periods.copy-balances');
        });
    });
});
