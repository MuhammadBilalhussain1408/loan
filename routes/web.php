<?php


use App\Http\Controllers\ActivityLogController;

use App\Http\Controllers\ChartOfAccountController;
use App\Http\Controllers\CommunicationCampaignController;
use App\Http\Controllers\CommunicationController;
use App\Http\Controllers\CommunicationLogController;
use App\Http\Controllers\CommunicationTemplateController;
use App\Http\Controllers\CurrenciesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileTypeController;
use App\Http\Controllers\FundController;
use App\Http\Controllers\LoanApplicationApprovalStageController;
use App\Http\Controllers\LoanApplicationChecklistsController;
use App\Http\Controllers\LoanApplicationFileController;
use App\Http\Controllers\LoanApplicationNoteController;
use App\Http\Controllers\LoanChargeController;
use App\Http\Controllers\LoanCreditCheckController;
use App\Http\Controllers\LoanFileController;
use App\Http\Controllers\LoanGuarantorController;
use App\Http\Controllers\LoanLinkedChargesController;
use App\Http\Controllers\LoanNoteController;
use App\Http\Controllers\LoanProductController;
use App\Http\Controllers\LoanProvisioningController;
use App\Http\Controllers\LoanPurposeController;
use App\Http\Controllers\LoanRepaymentController;
use App\Http\Controllers\LoanTransactionController;
use App\Http\Controllers\MemberCategoriesController;
use App\Http\Controllers\MemberDesignationsController;
use App\Http\Controllers\MemberFileController;
use App\Http\Controllers\MemberLoginDetailsController;
use App\Http\Controllers\MemberPortal\MemberPortalController;
use App\Http\Controllers\MemberPortal\MemberPortalLoanApplicationsController;
use App\Http\Controllers\MemberPortal\MemberPortalLoanFileController;
use App\Http\Controllers\MemberPortal\MemberPortalLoanLinkedChargesController;
use App\Http\Controllers\MemberPortal\MemberPortalLoanScheduleController;
use App\Http\Controllers\MemberPortal\MemberPortalLoansController;
use App\Http\Controllers\MemberPortal\MemberPortalLoanTransactionController;
use App\Http\Controllers\MemberPortal\MemberPortalMemberBeneficiaryController;
use App\Http\Controllers\MemberPortal\MemberPortalMemberFileController;
use App\Http\Controllers\PaymentTypeController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SmsGatewaysController;

use App\Http\Controllers\TaxRateController;


use App\Http\Controllers\UsersController;
use App\Http\Controllers\WebhooksController;
use App\Http\Controllers\CustomFieldController;
use App\Http\Controllers\FilesController;

use App\Http\Controllers\LoanApplicationController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\LoanScheduleController;
use App\Http\Controllers\LoanStatementController;
use App\Http\Controllers\MemberBeneficiaryController;
use App\Http\Controllers\MemberContributionController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MemberRelationshipController;
use App\Http\Controllers\OtherLoanController;
use App\Http\Controllers\ProfessionController;
use App\Http\Controllers\StopLoanController;
use App\Http\Controllers\TitleController;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Console\Output\BufferedOutput;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//website routes

Route::get('/', [DashboardController::class, 'index'])->name('home');
Route::get('/reset_permissions', [SettingsController::class, 'resetPermissions'])->name('reset_permissions');
Route::group(['prefix' => 'dashboard'], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::any('/save_widgets', [DashboardController::class, 'saveWidgets'])->name('dashboard.save_widgets');
    Route::any('/update_widgets', [DashboardController::class, 'updateWidgets'])->name('dashboard.update_widgets');
    Route::any('/get_loan_statistics', [DashboardController::class, 'getLoanStatistics'])->name('dashboard.get_loan_statistics');
    Route::any('/get_loans_by_status_pie_chart', [DashboardController::class, 'getLoansByStatusPieChart'])->name('dashboard.get_loans_by_status_pie_chart');
    Route::any('/get_loan_collection_overview', [DashboardController::class, 'getLoanCollectionOverview'])->name('dashboard.get_loan_collection_overview');
    Route::any('/get_total_payments_amount', [DashboardController::class, 'getTotalPaymentsAmount'])->name('dashboard.get_total_payments_amount');
    Route::any('/get_waiting_list', [DashboardController::class, 'getWaitingList'])->name('dashboard.get_waiting_list');
    Route::any('/get_appointments', [DashboardController::class, 'getAppointments'])->name('dashboard.get_appointments');
    Route::any('/get_appointments_by_status_pie_chart', [DashboardController::class, 'getAppointmentsByStatusPieChart'])->name('dashboard.get_appointments_by_status_pie_chart');
    Route::any('/get_appointments_by_period_graph', [DashboardController::class, 'getAppointmentsByPeriodGraph'])->name('dashboard.get_appointments_by_period_graph');
    Route::any('/get_payments_by_payment_type_pie_chart', [DashboardController::class, 'getPaymentsByPaymentTypePieChart'])->name('dashboard.get_payments_by_payment_type_pie_chart');
    Route::any('/get_payments_by_period_graph', [DashboardController::class, 'getPaymentsByPeriodGraph'])->name('dashboard.get_payments_by_period_graph');
    Route::any('/get_income_expenses_pie_chart', [DashboardController::class, 'getIncomeExpensesPieChart'])->name('dashboard.get_income_expenses_pie_chart');
    Route::any('/get_period_income_expenses_graph', [DashboardController::class, 'getIncomeExpensesGraph'])->name('dashboard.get_period_income_expenses_graph');
    Route::any('/get_consultations_by_period_graph', [DashboardController::class, 'getConsultationsByPeriodGraph'])->name('dashboard.get_consultations_by_period_graph');
});
//users
Route::group(['prefix' => 'user'], function () {
    Route::get('/', [UsersController::class, 'index'])->name('users.index');
    Route::get('/search', [UsersController::class, 'search'])->name('users.search');
    Route::get('/create', [UsersController::class, 'create'])->name('users.create');
    Route::post('/store', [UsersController::class, 'store'])->name('users.store');
    Route::get('/{user}/show', [UsersController::class, 'show'])->name('users.show');
    Route::get('/{user}/edit', [UsersController::class, 'edit'])->name('users.edit');
    Route::put('/{user}/update', [UsersController::class, 'update'])->name('users.update');
    Route::delete('/{user}/destroy', [UsersController::class, 'destroy'])->name('users.destroy');
    //manage roles
    Route::get('/role', [RolesController::class, 'index'])->name('users.roles.index');
    Route::get('/role/create', [RolesController::class, 'create'])->name('users.roles.create');
    Route::post('/role/store', [RolesController::class, 'store'])->name('users.roles.store');
    Route::get('/role/{role}/show', [RolesController::class, 'show'])->name('users.roles.show');
    Route::get('/role/{role}/edit', [RolesController::class, 'edit'])->name('users.roles.edit');
    Route::put('/role/{role}/update', [RolesController::class, 'update'])->name('users.roles.update');
    Route::delete('/role/{role}/destroy', [RolesController::class, 'destroy'])->name('users.roles.destroy');
});
//activity log
Route::group(['prefix' => 'activity_log', 'as' => 'activity_logs.'], function () {
    Route::get('/', [ActivityLogController::class, 'index'])->name('index');
    Route::get('/{activity}/show', [ActivityLogController::class, 'show'])->name('show');
});


//members
Route::group(['prefix' => 'member', 'as' => 'members.'], function () {
    Route::get('/', [MemberController::class, 'index'])->name('index');
    Route::get('/search', [MemberController::class, 'search'])->name('search');
    Route::get('create', [MemberController::class, 'create'])->name('create');
    Route::post('store', [MemberController::class, 'store'])->name('store');
    Route::get('import', [MemberController::class, 'createImport'])->name('create_import');
    Route::post('import', [MemberController::class, 'importMembers'])->name('import');
    Route::get('{member}/show', [MemberController::class, 'show'])->name('show');
    Route::get('{member}/edit', [MemberController::class, 'edit'])->name('edit');
    Route::put('{member}/update', [MemberController::class, 'update'])->name('update');
    Route::delete('{member}/destroy', [MemberController::class, 'destroy'])->name('destroy');
    Route::put('{member}/change_status', [MemberController::class, 'change_status'])->name('change_status');
    Route::get('{member}/loan', [MemberController::class, 'loans'])->name('loans.index');
    Route::get('{member}/application', [MemberController::class, 'loanApplications'])->name('applications.index');
    Route::get('{member}/login_detail', [MemberLoginDetailsController::class, 'index'])->name('login_details.index');
    Route::get('{member}/login_detail/create', [MemberLoginDetailsController::class, 'create'])->name('login_details.create');
    Route::post('{member}/login_detail/store', [MemberLoginDetailsController::class, 'store'])->name('login_details.store');
    Route::delete('login_detail/{member}/destroy', [MemberLoginDetailsController::class, 'destroy'])->name('login_details.destroy');
    // other loan

    Route::get('{member}/other_loan', [OtherLoanController::class, 'index'])->name('other_loan.index');
    Route::get('{member}/other_loan/create', [OtherLoanController::class, 'create'])->name('other_loan.create');
    Route::post('{member}/other_loan/store', [OtherLoanController::class, 'store'])->name('other_loan.store');
    //member identification
    Route::get('{member}/beneficiary', [MemberBeneficiaryController::class, 'index'])->name('beneficiaries.index');
    Route::get('{member}/beneficiary/create', [MemberBeneficiaryController::class, 'create'])->name('beneficiaries.create');
    Route::post('{member}/beneficiary/store', [MemberBeneficiaryController::class, 'store'])->name('beneficiaries.store');
    Route::get('{member}/beneficiary/show', [MemberBeneficiaryController::class, 'show'])->name('beneficiaries.show');
    Route::get('beneficiary/{beneficiary}/edit', [MemberBeneficiaryController::class, 'edit'])->name('beneficiaries.edit');
    Route::put('beneficiary/{beneficiary}/update', [MemberBeneficiaryController::class, 'update'])->name('beneficiaries.update');
    Route::delete('beneficiary/{beneficiary}/destroy', [MemberBeneficiaryController::class, 'destroy'])->name('beneficiaries.destroy');
    //member files
    Route::get('{member}/file', [MemberFileController::class, 'index'])->name('files.index');
    Route::get('{member}/file/create', [MemberFileController::class, 'create'])->name('files.create');
    Route::post('{member}/file/store', [MemberFileController::class, 'store'])->name('files.store');
    Route::get('{member}/file/show', [MemberFileController::class, 'show'])->name('files.show');
    Route::get('file/{file}/edit', [MemberFileController::class, 'edit'])->name('files.edit');
    Route::put('file/{file}/update', [MemberFileController::class, 'update'])->name('files.update');
    Route::delete('file/{file}/destroy', [MemberFileController::class, 'destroy'])->name('files.destroy');
    //titles
    Route::group(['prefix' => 'title', 'as' => 'titles.'], function () {
        Route::get('/', [TitleController::class, 'index'])->name('index');
        Route::get('create', [TitleController::class, 'create'])->name('create');
        Route::post('store', [TitleController::class, 'store'])->name('store');
        Route::get('{title}/show', [TitleController::class, 'show'])->name('show');
        Route::get('{title}/edit', [TitleController::class, 'edit'])->name('edit');
        Route::put('{title}/update', [TitleController::class, 'update'])->name('update');
        Route::delete('{title}/destroy', [TitleController::class, 'destroy'])->name('destroy');
    });

    //member relationship
    Route::group(['prefix' => 'relationship', 'as' => 'relationships.'], function () {
        Route::get('/', [MemberRelationshipController::class, 'index'])->name('index');
        Route::get('create', [MemberRelationshipController::class, 'create'])->name('create');
        Route::post('store', [MemberRelationshipController::class, 'store'])->name('store');
        Route::get('{relationship}/show', [MemberRelationshipController::class, 'show'])->name('show');
        Route::get('{relationship}/edit', [MemberRelationshipController::class, 'edit'])->name('edit');
        Route::put('{relationship}/update', [MemberRelationshipController::class, 'update'])->name('update');
        Route::delete('{relationship}/destroy', [MemberRelationshipController::class, 'destroy'])->name('destroy');
    });
    Route::group(['prefix' => 'category', 'as' => 'categories.'], function () {
        Route::get('/', [MemberCategoriesController::class, 'index'])->name('index');
        Route::get('create', [MemberCategoriesController::class, 'create'])->name('create');
        Route::post('store', [MemberCategoriesController::class, 'store'])->name('store');
        Route::get('{category}/show', [MemberCategoriesController::class, 'show'])->name('show');
        Route::get('{category}/edit', [MemberCategoriesController::class, 'edit'])->name('edit');
        Route::put('{category}/update', [MemberCategoriesController::class, 'update'])->name('update');
        Route::delete('{category}/destroy', [MemberCategoriesController::class, 'destroy'])->name('destroy');
    });
    Route::group(['prefix' => 'designation', 'as' => 'designations.'], function () {
        Route::get('/', [MemberDesignationsController::class, 'index'])->name('index');
        Route::get('create', [MemberDesignationsController::class, 'create'])->name('create');
        Route::post('store', [MemberDesignationsController::class, 'store'])->name('store');
        Route::get('{designation}/show', [MemberDesignationsController::class, 'show'])->name('show');
        Route::get('{designation}/edit', [MemberDesignationsController::class, 'edit'])->name('edit');
        Route::put('{designation}/update', [MemberDesignationsController::class, 'update'])->name('update');
        Route::delete('{designation}/destroy', [MemberDesignationsController::class, 'destroy'])->name('destroy');
    });
    Route::group(['prefix' => 'profession', 'as' => 'professions.'], function () {
        Route::get('/', [ProfessionController::class, 'create'])->name('create');
        Route::get('create', [ProfessionController::class, 'index'])->name('index');
        Route::post('store', [ProfessionController::class, 'store'])->name('store');
        Route::get('{profession}/show', [ProfessionController::class, 'show'])->name('show');
        Route::get('{profession}/edit', [ProfessionController::class, 'edit'])->name('edit');
        Route::put('{profession}/update', [ProfessionController::class, 'update'])->name('update');
        Route::delete('{profession}/destroy', [ProfessionController::class, 'destroy'])->name('destroy');
    });
});
//custom fields
Route::group(['prefix' => 'custom_field', 'as' => 'custom_fields.'], function () {
    Route::get('/', [CustomFieldController::class, 'index'])->name('index');
    Route::get('/create', [CustomFieldController::class, 'create'])->name('create');
    Route::post('/store', [CustomFieldController::class, 'store'])->name('store');
    Route::get('/{field}/show', [CustomFieldController::class, 'show'])->name('show');
    Route::get('/{field}/edit', [CustomFieldController::class, 'edit'])->name('edit');
    Route::put('/{field}/update', [CustomFieldController::class, 'update'])->name('update');
    Route::delete('/{field}/destroy', [CustomFieldController::class, 'destroy'])->name('destroy');
});

//loans
Route::group(['prefix' => 'loan', 'as' => 'loans.'], function () {
    Route::get('/', [LoanController::class, 'index'])->name('index');
    Route::get('create', [LoanController::class, 'create'])->name('create');
    Route::get('create_member_loan', [LoanController::class, 'create_member_loan'])->name('create_member_loan');
    Route::post('store', [LoanController::class, 'store'])->name('store');
    Route::get('calculator', [LoanController::class, 'createLoanCalculator'])->name('calculator');
    Route::post('calculator', [LoanController::class, 'processLoanCalculator'])->name('process_loan_calculator');
    Route::get('{loan}/show', [LoanController::class, 'show'])->name('show');
    Route::get('{loan}/edit', [LoanController::class, 'edit'])->name('edit');
    Route::put('{loan}/update', [LoanController::class, 'update'])->name('update');
    Route::put('{loan}/change_status', [LoanController::class, 'changeStatus'])->name('change_status');
    Route::put('{loan}/undo_status', [LoanController::class, 'undoStatus'])->name('undo_status');
    Route::delete('{loan}/destroy', [LoanController::class, 'destroy'])->name('destroy');
    Route::post('{loan}/reschedule_loan', [LoanController::class, 'rescheduleLoan'])->name('reschedule_loan');
    Route::put('{loan}/change_loan_officer', [LoanController::class, 'changeLoanOfficer'])->name('change_loan_officer');
    Route::post('{loan}/waive_interest', [LoanController::class, 'waiveInterest'])->name('waive_interest');
    //schedules
    Route::get('{loan}/schedule', [LoanScheduleController::class, 'index'])->name('schedules.index');
    Route::get('{loan}/schedule/show', [LoanScheduleController::class, 'show'])->name('schedules.show');
    Route::get('{loan}/schedule/edit', [LoanScheduleController::class, 'edit'])->name('schedules.edit');
    Route::put('{loan}/schedule/update', [LoanScheduleController::class, 'update'])->name('schedules.update');
    Route::get('{loan}/schedule/email', [LoanScheduleController::class, 'email'])->name('schedules.email');
    Route::get('{loan}/schedule/pdf', [LoanScheduleController::class, 'pdf'])->name('schedules.pdf');
    Route::get('{loan}/schedule/print', [LoanScheduleController::class, 'printSchedule'])->name('schedules.print');
    //applications
    Route::group(['prefix' => 'application', 'as' => 'applications.'], function () {
        Route::get('/', [LoanApplicationController::class, 'index'])->name('index');
        Route::get('/create', [LoanApplicationController::class, 'create'])->name('create');
        Route::post('/store', [LoanApplicationController::class, 'store'])->name('store');
        Route::get('/{application}/show', [LoanApplicationController::class, 'show'])->name('show');
        Route::get('/{application}/edit', [LoanApplicationController::class, 'edit'])->name('edit');
        Route::put('/{application}/update', [LoanApplicationController::class, 'update'])->name('update');
        Route::put('/{application}/disburse', [LoanApplicationController::class, 'disburse'])->name('disburse');
        Route::put('/{application}/change_checklist_item_status', [LoanApplicationController::class, 'changeChecklistItemStatus'])->name('change_checklist_item_status');
        Route::put('/{application}/acknowledge_approval_stage', [LoanApplicationController::class, 'acknowledgeApprovalStage'])->name('acknowledge_approval_stage');
        Route::put('/{application}/assign_approval_stage', [LoanApplicationController::class, 'assignApprovalStage'])->name('assign_approval_stage');
        Route::put('/{application}/change_approval_stage_status', [LoanApplicationController::class, 'changeApprovalStageStatus'])->name('change_approval_stage_status');
        Route::get('/{application}/approve', [LoanApplicationController::class, 'approve'])->name('create_approve');
        Route::delete('/{application}/destroy', [LoanApplicationController::class, 'destroy'])->name('destroy');
        Route::post('/{application}/approve/store', [LoanApplicationController::class, 'storeApprove'])->name('store_approve');
        Route::get('{application}/schedule', [LoanApplicationController::class, 'schedule'])->name('schedules.index');
        Route::get('{application}/history', [LoanApplicationController::class, 'history'])->name('histories.index');
        Route::get('{application}/charges', [LoanApplicationController::class, 'charges'])->name('linked_charges.index');
        //files
        Route::get('{application}/file', [LoanApplicationFileController::class, 'index'])->name('files.index');
        Route::get('{application}/file/create', [LoanApplicationFileController::class, 'create'])->name('files.create');
        Route::post('{application}/file/store', [LoanApplicationFileController::class, 'store'])->name('files.store');
        Route::get('{application}/file/show', [LoanApplicationFileController::class, 'show'])->name('files.show');
        Route::get('file/{file}/edit', [LoanApplicationFileController::class, 'edit'])->name('files.edit');
        Route::put('file/{file}/update', [LoanApplicationFileController::class, 'update'])->name('files.update');
        Route::delete('file/{file}/destroy', [LoanApplicationFileController::class, 'destroy'])->name('files.destroy');
        //notes
        Route::get('{application}/note', [LoanApplicationNoteController::class, 'index'])->name('notes.index');
        Route::get('{application}/note/create', [LoanApplicationNoteController::class, 'create'])->name('notes.create');
        Route::post('{application}/note/store', [LoanApplicationNoteController::class, 'store'])->name('notes.store');
        Route::get('{application}/note/show', [LoanApplicationNoteController::class, 'show'])->name('notes.show');
        Route::get('note/{note}/edit', [LoanApplicationNoteController::class, 'edit'])->name('notes.edit');
        Route::put('note/{note}/update', [LoanApplicationNoteController::class, 'update'])->name('notes.update');
        Route::delete('note/{note}/destroy', [LoanApplicationNoteController::class, 'destroy'])->name('notes.destroy');
    });
    //loan files
    Route::get('{loan}/file', [LoanFileController::class, 'index'])->name('files.index');
    Route::get('{loan}/file/create', [LoanFileController::class, 'create'])->name('files.create');
    Route::post('{loan}/file/store', [LoanFileController::class, 'store'])->name('files.store');
    Route::get('{loan}/file/show', [LoanFileController::class, 'show'])->name('files.show');
    Route::get('file/{file}/edit', [LoanFileController::class, 'edit'])->name('files.edit');
    Route::put('file/{file}/update', [LoanFileController::class, 'update'])->name('files.update');
    Route::delete('file/{file}/destroy', [LoanFileController::class, 'destroy'])->name('files.destroy');
    //notes
    Route::get('{loan}/note', [LoanNoteController::class, 'index'])->name('notes.index');
    Route::get('{loan}/note/create', [LoanNoteController::class, 'create'])->name('notes.create');
    Route::post('{loan}/note/store', [LoanNoteController::class, 'store'])->name('notes.store');
    Route::get('{loan}/note/show', [LoanNoteController::class, 'show'])->name('notes.show');
    Route::get('note/{note}/edit', [LoanNoteController::class, 'edit'])->name('notes.edit');
    Route::put('note/{note}/update', [LoanNoteController::class, 'update'])->name('notes.update');
    Route::delete('note/{note}/destroy', [LoanNoteController::class, 'destroy'])->name('notes.destroy');
    //loan transactions

    Route::get('{loan}/transaction', [LoanTransactionController::class, 'index'])->name('transactions.index');
    Route::get('{loan}/transaction/create', [LoanTransactionController::class, 'create'])->name('transactions.create');
    Route::post('{loan}/transaction/store', [LoanTransactionController::class, 'store'])->name('transactions.store');
    Route::get('transaction/{transaction}/show', [LoanTransactionController::class, 'show'])->name('transactions.show');
    Route::get('transaction/{transaction}/pdf', [LoanTransactionController::class, 'pdf'])->name('transactions.pdf');
    Route::get('transaction/{transaction}/print', [LoanTransactionController::class, 'printTransaction'])->name('transactions.print');
    Route::post('transaction/{transaction}/reverse', [LoanTransactionController::class, 'reverse'])->name('transactions.reverse');
    Route::get('transaction/{transaction}/edit', [LoanTransactionController::class, 'edit'])->name('transactions.edit');
    Route::put('transaction/{transaction}/update', [LoanTransactionController::class, 'update'])->name('transactions.update');
    Route::delete('transaction/{transaction}/destroy', [LoanTransactionController::class, 'destroy'])->name('transactions.destroy');
    Route::delete('transaction/{transaction}/waive', [LoanTransactionController::class, 'waiveCharge'])->name('transactions.waive_charge');

    //charges
    Route::get('{loan}/charge', [LoanLinkedChargesController::class, 'index'])->name('linked_charges.index');
    Route::get('{loan}/charge/create', [LoanLinkedChargesController::class, 'create'])->name('linked_charges.create');
    Route::post('{loan}/charge/store', [LoanLinkedChargesController::class, 'store'])->name('linked_charges.store');
    Route::get('charge/{linkedCharge}/waive', [LoanLinkedChargesController::class, 'waive'])->name('linked_charges.waive');

    //funds
    Route::group(['prefix' => 'fund', 'as' => 'funds.'], function () {
        Route::get('/', [FundController::class, 'index'])->name('index');
        Route::get('create', [FundController::class, 'create'])->name('create');
        Route::post('store', [FundController::class, 'store'])->name('store');
        Route::get('{fund}/show', [FundController::class, 'show'])->name('show');
        Route::get('{fund}/edit', [FundController::class, 'edit'])->name('edit');
        Route::put('{fund}/update', [FundController::class, 'update'])->name('update');
        Route::delete('{fund}/destroy', [FundController::class, 'destroy'])->name('destroy');
    });
    //repayments
    Route::group(['prefix' => 'repayment', 'as' => 'repayments.'], function () {
        Route::get('/', [LoanRepaymentController::class, 'index'])->name('index');
        Route::get('create', [LoanRepaymentController::class, 'create'])->name('create');
        Route::post('store', [LoanRepaymentController::class, 'store'])->name('store');
        Route::get('bulk/create', [LoanRepaymentController::class, 'createBulkRepayment'])->name('create_bulk_repayment');
        Route::post('bulk/store', [LoanRepaymentController::class, 'storeBulkRepayment'])->name('store_bulk_repayments');
        Route::get('{repayment}/show', [LoanRepaymentController::class, 'show'])->name('show');
        Route::get('{repayment}/edit', [LoanRepaymentController::class, 'edit'])->name('edit');
        Route::put('{repayment}/update', [LoanRepaymentController::class, 'update'])->name('update');
        Route::delete('{repayment}/destroy', [LoanRepaymentController::class, 'destroy'])->name('destroy');
    });
    //purposes
    Route::group(['prefix' => 'purpose', 'as' => 'purposes.'], function () {
        Route::get('/', [LoanPurposeController::class, 'index'])->name('index');
        Route::get('create', [LoanPurposeController::class, 'create'])->name('create');
        Route::post('store', [LoanPurposeController::class, 'store'])->name('store');
        Route::get('{purpose}/show', [LoanPurposeController::class, 'show'])->name('show');
        Route::get('{purpose}/edit', [LoanPurposeController::class, 'edit'])->name('edit');
        Route::put('{purpose}/update', [LoanPurposeController::class, 'update'])->name('update');
        Route::delete('{purpose}/destroy', [LoanPurposeController::class, 'destroy'])->name('destroy');
    });
    //checklists
    Route::group(['prefix' => 'checklist', 'as' => 'checklists.'], function () {
        Route::get('/', [LoanApplicationChecklistsController::class, 'index'])->name('index');
        Route::get('create', [LoanApplicationChecklistsController::class, 'create'])->name('create');
        Route::post('store', [LoanApplicationChecklistsController::class, 'store'])->name('store');
        Route::get('{checklist}/show', [LoanApplicationChecklistsController::class, 'show'])->name('show');
        Route::get('{checklist}/edit', [LoanApplicationChecklistsController::class, 'edit'])->name('edit');
        Route::put('{checklist}/update', [LoanApplicationChecklistsController::class, 'update'])->name('update');;
        Route::delete('{checklist}/destroy', [LoanApplicationChecklistsController::class, 'destroy'])->name('destroy');
    });
    //credit checks
    Route::group(['prefix' => 'approval_stage', 'as' => 'approval_stages.'], function () {
        Route::get('/', [LoanApplicationApprovalStageController::class, 'index'])->name('index');
        Route::get('create', [LoanApplicationApprovalStageController::class, 'create'])->name('create');
        Route::post('store', [LoanApplicationApprovalStageController::class, 'store'])->name('store');
        Route::get('stage}/show', [LoanApplicationApprovalStageController::class, 'show'])->name('show');
        Route::get('{stage}/edit', [LoanApplicationApprovalStageController::class, 'edit'])->name('edit');
        Route::put('{stage}/update', [LoanApplicationApprovalStageController::class, 'update'])->name('update');
        Route::delete('{stage}/destroy', [LoanApplicationApprovalStageController::class, 'destroy'])->name('destroy');
    });
    //stop loan
    Route::group(['prefix' => 'stop_loan', 'as' => 'stop_loan.'], function () {
        Route::get('/', [StopLoanController::class, 'index'])->name('index');
        Route::get('create', [StopLoanController::class, 'create'])->name('create');
        Route::post('store', [StopLoanController::class, 'store'])->name('store');
        Route::get('{stage}/show', [StopLoanController::class, 'show'])->name('show');
        Route::get('{stage}/edit', [StopLoanController::class, 'edit'])->name('edit');
        Route::put('{stage}/update', [StopLoanController::class, 'update'])->name('update');
        Route::delete('{stage}/destroy', [StopLoanController::class, 'destroy'])->name('destroy');
    });
    //charges
    Route::group(['prefix' => 'charge', 'as' => 'charges.'], function () {
        Route::get('/', [LoanChargeController::class, 'index'])->name('index');
        Route::get('get_charges', [LoanChargeController::class, 'get_charges'])->name('get_charges');
        Route::get('get_charge_types', [LoanChargeController::class, 'get_charge_types'])->name('get_charge_types');
        Route::get('get_charge_options', [LoanChargeController::class, 'get_charge_options'])->name('get_charge_options');
        Route::get('create', [LoanChargeController::class, 'create'])->name('create');
        Route::post('store', [LoanChargeController::class, 'store'])->name('store');
        Route::get('{charge}/show', [LoanChargeController::class, 'show'])->name('show');
        Route::get('{charge}/edit', [LoanChargeController::class, 'edit'])->name('edit');
        Route::put('{charge}/update', [LoanChargeController::class, 'update'])->name('update');
        Route::delete('{charge}/destroy', [LoanChargeController::class, 'destroy'])->name('destroy');
    });
    //loan product
    Route::group(['prefix' => 'product', 'as' => 'products.'], function () {
        Route::get('/', [LoanProductController::class, 'index'])->name('index');
        Route::get('/search', [LoanProductController::class, 'search'])->name('search');
        Route::get('create', [LoanProductController::class, 'create'])->name('create');
        Route::post('store', [LoanProductController::class, 'store'])->name('store');
        Route::get('{product}/show', [LoanProductController::class, 'show'])->name('show');
        Route::get('{product}/edit', [LoanProductController::class, 'edit'])->name('edit');
        Route::put('{product}/update', [LoanProductController::class, 'update'])->name('update');
        Route::delete('{product}/destroy', [LoanProductController::class, 'destroy'])->name('destroy');
        Route::get('{product}/get_charges', [LoanProductController::class, 'get_charges'])->name('get_charges');
    });
    //loan provisioning
    Route::group(['prefix' => 'provisioning', 'as' => 'provisioning.'], function () {
        Route::get('/', [LoanProvisioningController::class, 'index'])->name('index');
        Route::get('create', [LoanProvisioningController::class, 'create'])->name('create');
        Route::post('store', [LoanProvisioningController::class, 'store'])->name('store');
        Route::get('{provisioning}/show', [LoanProvisioningController::class, 'show'])->name('show');
        Route::get('{provisioning}/edit', [LoanProvisioningController::class, 'edit'])->name('edit');
        Route::put('{provisioning}/update', [LoanProvisioningController::class, 'update'])->name('update');
        Route::delete('{provisioning}/destroy', [LoanProvisioningController::class, 'destroy'])->name('destroy');
    });
});
//files
Route::group(['prefix' => 'file', 'as' => 'files.'], function () {
    Route::get('/', [FilesController::class, 'index'])->name('index');
    Route::post('/upload', [FilesController::class, 'upload'])->name('upload');
    Route::get('/{file}/download', [FilesController::class, 'download'])->name('download');
    Route::get('/create', [FilesController::class, 'create'])->name('create');
    Route::post('/store', [FilesController::class, 'store'])->name('store');
    Route::get('/{file}/show', [FilesController::class, 'show'])->name('show');
    Route::get('/{file}/edit', [FilesController::class, 'edit'])->name('edit');
    Route::put('/{file}/update', [FilesController::class, 'update'])->name('update');
    Route::delete('/{file}/destroy', [FilesController::class, 'destroy'])->name('destroy');
    Route::group(['prefix' => 'type', 'as' => 'types.'], function () {
        Route::get('/', [FileTypeController::class, 'index'])->name('index');
        Route::get('/create', [FileTypeController::class, 'create'])->name('create');
        Route::post('/store', [FileTypeController::class, 'store'])->name('store');
        Route::get('/{type}/show', [FileTypeController::class, 'show'])->name('show');
        Route::get('/{type}/edit', [FileTypeController::class, 'edit'])->name('edit');
        Route::put('/{type}/update', [FileTypeController::class, 'update'])->name('update');
        Route::delete('/{type}/destroy', [FileTypeController::class, 'destroy'])->name('destroy');
    });
});

//branches
Route::group(['prefix' => 'branch'], function () {
    Route::get('/', [BranchesController::class, 'index'])->name('branches.index');
    Route::get('/create', [BranchesController::class, 'create'])->name('branches.create');
    Route::post('/store', [BranchesController::class, 'store'])->name('branches.store');
    Route::get('/{branch}/show', [BranchesController::class, 'show'])->name('branches.show');
    Route::get('/{branch}/edit', [BranchesController::class, 'edit'])->name('branches.edit');
    Route::put('/{branch}/update', [BranchesController::class, 'update'])->name('branches.update');
    Route::delete('/{branch}/destroy', [BranchesController::class, 'destroy'])->name('branches.destroy');
});

//currencies
Route::group(['prefix' => 'currency'], function () {
    Route::get('/', [CurrenciesController::class, 'index'])->name('currencies.index');
    Route::get('/create', [CurrenciesController::class, 'create'])->name('currencies.create');
    Route::post('/store', [CurrenciesController::class, 'store'])->name('currencies.store');
    Route::get('/{currency}/show', [CurrenciesController::class, 'show'])->name('currencies.show');
    Route::get('/{currency}/edit', [CurrenciesController::class, 'edit'])->name('currencies.edit');
    Route::put('/{currency}/update', [CurrenciesController::class, 'update'])->name('currencies.update');
    Route::delete('/{currency}/destroy', [CurrenciesController::class, 'destroy'])->name('currencies.destroy');
});
//currencies
Route::group(['prefix' => 'tax_rate'], function () {
    Route::get('/', [TaxRateController::class, 'index'])->name('tax_rates.index');
    Route::get('/create', [TaxRateController::class, 'create'])->name('tax_rates.create');
    Route::post('/store', [TaxRateController::class, 'store'])->name('tax_rates.store');
    Route::get('/{taxRate}/show', [TaxRateController::class, 'show'])->name('tax_rates.show');
    Route::get('/{taxRate}/edit', [TaxRateController::class, 'edit'])->name('tax_rates.edit');
    Route::put('/{taxRate}/update', [TaxRateController::class, 'update'])->name('tax_rates.update');
    Route::delete('/{taxRate}/destroy', [TaxRateController::class, 'destroy'])->name('tax_rates.destroy');
});
//tax rates
Route::group(['prefix' => 'payment_type'], function () {
    Route::get('/', [PaymentTypeController::class, 'index'])->name('payment_types.index');
    Route::get('/create', [PaymentTypeController::class, 'create'])->name('payment_types.create');
    Route::post('/store', [PaymentTypeController::class, 'store'])->name('payment_types.store');
    Route::get('/{paymentType}/show', [PaymentTypeController::class, 'show'])->name('payment_types.show');
    Route::get('/{paymentType}/edit', [PaymentTypeController::class, 'edit'])->name('payment_types.edit');
    Route::put('/{paymentType}/update', [PaymentTypeController::class, 'update'])->name('payment_types.update');
    Route::delete('/{paymentType}/destroy', [PaymentTypeController::class, 'destroy'])->name('payment_types.destroy');
});
//chart of accounts
Route::group(['prefix' => 'chart_of_account'], function () {
    Route::get('/', [ChartOfAccountController::class, 'index'])->name('accounting.chart_of_accounts.index');
    Route::get('/create', [ChartOfAccountController::class, 'create'])->name('accounting.chart_of_accounts.create');
    Route::post('/store', [ChartOfAccountController::class, 'store'])->name('accounting.chart_of_accounts.store');
    Route::get('/{chartOfAccount}/show', [ChartOfAccountController::class, 'show'])->name('accounting.chart_of_accounts.show');
    Route::get('/{chartOfAccount}/edit', [ChartOfAccountController::class, 'edit'])->name('accounting.chart_of_accounts.edit');
    Route::put('/{chartOfAccount}/update', [ChartOfAccountController::class, 'update'])->name('accounting.chart_of_accounts.update');
    Route::delete('/{chartOfAccount}/destroy', [ChartOfAccountController::class, 'destroy'])->name('accounting.chart_of_accounts.destroy');
});
Route::group(['prefix' => 'journal_entry'], function () {
    Route::get('/', [JournalEntryController::class, 'index'])->name('accounting.journal_entries.index');
    Route::get('/create', [JournalEntryController::class, 'create'])->name('accounting.journal_entries.create');
    Route::post('/store', [JournalEntryController::class, 'store'])->name('accounting.journal_entries.store');
    Route::get('/{journalEntry}/show', [JournalEntryController::class, 'show'])->name('accounting.journal_entries.show');
    Route::get('/{journalEntry}/edit', [JournalEntryController::class, 'edit'])->name('accounting.journal_entries.edit');
    Route::put('/{journalEntry}/update', [JournalEntryController::class, 'update'])->name('accounting.journal_entries.update');
    Route::delete('/{journalEntry}/destroy', [JournalEntryController::class, 'destroy'])->name('accounting.journal_entries.destroy');
});
Route::group(['prefix' => 'financial_period'], function () {
    Route::get('/', [FinancialPeriodController::class, 'index'])->name('accounting.financial_periods.index');
    Route::get('/create', [FinancialPeriodController::class, 'create'])->name('accounting.financial_periods.create');
    Route::post('/store', [FinancialPeriodController::class, 'store'])->name('accounting.financial_periods.store');
    Route::get('/{financialPeriod}/show', [FinancialPeriodController::class, 'show'])->name('accounting.financial_periods.show');
    Route::get('/{financialPeriod}/edit', [FinancialPeriodController::class, 'edit'])->name('accounting.financial_periods.edit');
    Route::put('/{financialPeriod}/update', [FinancialPeriodController::class, 'update'])->name('accounting.financial_periods.update');
    Route::put('/{financialPeriod}/close', [FinancialPeriodController::class, 'close'])->name('accounting.financial_periods.close');
    Route::delete('/{financialPeriod}/destroy', [FinancialPeriodController::class, 'destroy'])->name('accounting.financial_periods.destroy');
});
//communication
Route::prefix('communication')->group(function () {
    Route::get('/', [CommunicationController::class, 'index']);
    //sms gateway
    Route::group(['prefix' => 'sms_gateway'], function () {
        Route::get('/', [SmsGatewaysController::class, 'index'])->name('communication.sms_gateways.index');
        Route::get('/create', [SmsGatewaysController::class, 'create'])->name('communication.sms_gateways.create');
        Route::post('/store', [SmsGatewaysController::class, 'store'])->name('communication.sms_gateways.store');
        Route::get('/{smsGateway}/show', [SmsGatewaysController::class, 'show'])->name('communication.sms_gateways.show');
        Route::get('/{smsGateway}/edit', [SmsGatewaysController::class, 'edit'])->name('communication.sms_gateways.edit');
        Route::put('/{smsGateway}/update', [SmsGatewaysController::class, 'update'])->name('communication.sms_gateways.update');
        Route::delete('/{smsGateway}/destroy', [SmsGatewaysController::class, 'destroy'])->name('communication.sms_gateways.destroy');
    });
    Route::prefix('campaign')->group(function () {
        Route::get('/', [CommunicationCampaignController::class, 'index'])->name('communication.campaigns.index');
        Route::get('/create', [CommunicationCampaignController::class, 'create'])->name('communication.campaigns.create');
        Route::post('/store', [CommunicationCampaignController::class, 'store'])->name('communication.campaigns.store');
        Route::get('/{communicationCampaign}/show', [CommunicationCampaignController::class, 'show'])->name('communication.campaigns.show');
        Route::get('/{communicationCampaign}/edit', [CommunicationCampaignController::class, 'edit'])->name('communication.campaigns.edit');
        Route::put('/{communicationCampaign}/update', [CommunicationCampaignController::class, 'update'])->name('communication.campaigns.update');
        Route::delete('/{communicationCampaign}/destroy', [CommunicationCampaignController::class, 'destroy'])->name('communication.campaigns.destroy');
    });
    Route::prefix('template')->group(function () {
        Route::get('/', [CommunicationTemplateController::class, 'index'])->name('communication.templates.index');
        Route::get('/create', [CommunicationTemplateController::class, 'create'])->name('communication.templates.create');
        Route::post('/store', [CommunicationTemplateController::class, 'store'])->name('communication.templates.store');
        Route::get('/{template}/show', [CommunicationTemplateController::class, 'show'])->name('communication.templates.show');
        Route::get('/{template}/edit', [CommunicationTemplateController::class, 'edit'])->name('communication.templates.edit');
        Route::put('/{template}/update', [CommunicationTemplateController::class, 'update'])->name('communication.templates.update');
        Route::delete('/{template}/destroy', [CommunicationTemplateController::class, 'destroy'])->name('communication.templates.destroy');
    });
    Route::prefix('log')->group(function () {
        Route::get('/', [CommunicationLogController::class, 'index'])->name('communication.logs.index');
        Route::get('{communicationCampaignLog}/destroy', [CommunicationLogController::class, 'destroy'])->name('communication.logs.destroy');
    });
});

//member contribution
Route::group(['prefix' => 'contribution', 'as' => 'contribution.'], function () {
    Route::get('/', [MemberContributionController::class, 'index'])->name('index');
    Route::get('create', [MemberContributionController::class, 'create'])->name('create');
    Route::post('store', [MemberContributionController::class, 'store'])->name('store');
    Route::get('{stage}/show', [MemberContributionController::class, 'show'])->name('show');
    Route::get('{stage}/edit', [MemberContributionController::class, 'edit'])->name('edit');
    Route::put('{stage}/update', [MemberContributionController::class, 'update'])->name('update');
    Route::delete('{stage}/destroy', [MemberContributionController::class, 'destroy'])->name('destroy');
});
// Loan Statement
Route::group(['prefix' => 'statement', 'as' => 'statement.'], function () {
    Route::get('/', [LoanStatementController::class, 'index'])->name('index');
    Route::get('/get-loan-statement', [LoanStatementController::class, 'getLoanStatement'])->name('getLoanStatement');
});
//settings
Route::group(['prefix' => 'setting'], function () {
    Route::get('/', [SettingsController::class, 'index'])->name('settings.index');
    Route::get('/organisation', [SettingsController::class, 'organisation'])->name('settings.organisation');
    Route::get('/general', [SettingsController::class, 'general'])->name('settings.general');
    Route::post('/general/update', [SettingsController::class, 'generalUpdate'])->name('settings.general.update');
    Route::get('/system', [SettingsController::class, 'system'])->name('settings.system');
    Route::post('/system/update', [SettingsController::class, 'systemUpdate'])->name('settings.system.update');
    Route::get('/email', [SettingsController::class, 'email'])->name('settings.email');
    Route::post('/email/update', [SettingsController::class, 'emailUpdate'])->name('settings.email.update');
    Route::get('/sms', [SettingsController::class, 'sms'])->name('settings.sms');
    Route::post('/sms/update', [SettingsController::class, 'smsUpdate'])->name('settings.sms.update');
    Route::get('/other', [SettingsController::class, 'other'])->name('settings.other');
    Route::post('/other/update', [SettingsController::class, 'otherUpdate'])->name('settings.other.update');
    Route::get('/billing', [SettingsController::class, 'billing'])->name('settings.billing');
    Route::post('/update', [SettingsController::class, 'update'])->name('settings.update');
});

//reports
Route::group(['prefix' => 'report', 'as' => 'reports.'], function () {
    Route::get('/', [ReportsController::class, 'index'])->name('index');
    Route::group(['prefix' => 'accounting', 'as' => 'accounting.'], function () {
        Route::get('/', [AccountingReportController::class, 'index'])->name('index');
        Route::get('/balance_sheet', [AccountingReportController::class, 'balanceSheet'])->name('balance_sheet');
        Route::get('/trial_balance', [AccountingReportController::class, 'trialBalance'])->name('trial_balance');
        Route::get('/income_statement', [AccountingReportController::class, 'incomeStatement'])->name('income_statement');
        Route::get('/ledger', [AccountingReportController::class, 'ledger'])->name('ledger');
    });
    Route::group(['prefix' => 'savings', 'as' => 'savings.'], function () {
        Route::get('/', [SavingsReportController::class, 'index'])->name('index');
        Route::get('savings/transaction', [SavingsReportController::class, 'transaction'])->name('transaction');
        Route::get('savings/balance', [SavingsReportController::class, 'balance'])->name('balance');
        Route::get('savings/account', [SavingsReportController::class, 'account'])->name('account');
        Route::get('savings/account_statement', [SavingsReportController::class, 'accountStatement'])->name('account_statement');
    });
    Route::group(['prefix' => 'loan', 'as' => 'loans.'], function () {
        Route::get('/', [LoanReportController::class, 'index'])->name('index');
        Route::get('collection_sheet', [LoanReportController::class, 'collection_sheet'])->name('collection_sheet');
        Route::get('repayment', [LoanReportController::class, 'repayment'])->name('repayment');
        Route::get('expected_repayment', [LoanReportController::class, 'expectedRepayment'])->name('expected_repayment');
        Route::get('arrears', [LoanReportController::class, 'arrears'])->name('arrears');
        Route::get('disbursement', [LoanReportController::class, 'disbursement'])->name('disbursement');
        Route::get('portfolio_at_risk', [LoanReportController::class, 'portfolioAtRisk'])->name('portfolio_at_risk');
    });
    Route::group(['prefix' => 'user', 'as' => 'users.'], function () {
        Route::get('/', [UserReportController::class, 'index'])->name('index');
        Route::get('/performance', [UserReportController::class, 'performance'])->name('performance');
    });
});
//license
Route::group(['prefix' => 'license'], function () {
    Route::get('/', [LicenseController::class, 'index'])->name('license.index');
    Route::post('/verify', [LicenseController::class, 'verify'])->name('license.verify');
});

//member portal
Route::group(['prefix' => 'portal', 'as' => 'portal.'], function () {
    Route::get('/', [MemberPortalController::class, 'dashboard'])->name('home');
    Route::get('/dashboard', [MemberPortalController::class, 'dashboard'])->name('dashboard');
    //user
    Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
        Route::get('profile', [MemberPortalController::class, 'profile'])->name('profile');
        Route::get('linked_accounts', [MemberPortalController::class, 'linkedAccounts'])->name('linked_accounts');
        Route::post('linked_accounts/select', [MemberPortalController::class, 'selectLinkedAccount'])->name('linked_accounts.select');
    });
    Route::group(['prefix' => 'member', 'as' => 'member.'], function () {
        Route::get('/', [MemberPortalController::class, 'index'])->name('index');
        Route::get('show', [MemberPortalController::class, 'show'])->name('show');
        Route::get('edit', [MemberPortalController::class, 'edit'])->name('edit');
        Route::put('update', [MemberPortalController::class, 'update'])->name('update');


        //member identification
        Route::get('beneficiary', [MemberPortalMemberBeneficiaryController::class, 'index'])->name('beneficiaries.index');
        Route::get('beneficiary/create', [MemberPortalMemberBeneficiaryController::class, 'create'])->name('beneficiaries.create');
        Route::post('beneficiary/store', [MemberPortalMemberBeneficiaryController::class, 'store'])->name('beneficiaries.store');
        Route::get('beneficiary/show', [MemberPortalMemberBeneficiaryController::class, 'show'])->name('beneficiaries.show');
        Route::get('beneficiary/{beneficiary}/edit', [MemberPortalMemberBeneficiaryController::class, 'edit'])->name('beneficiaries.edit');
        Route::put('beneficiary/{beneficiary}/update', [MemberPortalMemberBeneficiaryController::class, 'update'])->name('beneficiaries.update');
        Route::delete('beneficiary/{beneficiary}/destroy', [MemberPortalMemberBeneficiaryController::class, 'destroy'])->name('beneficiaries.destroy');
        //member files
        Route::get('file', [MemberPortalMemberFileController::class, 'index'])->name('files.index');
        Route::get('file/create', [MemberPortalMemberFileController::class, 'create'])->name('files.create');
        Route::post('file/store', [MemberPortalMemberFileController::class, 'store'])->name('files.store');
        Route::get('file/show', [MemberPortalMemberFileController::class, 'show'])->name('files.show');
        Route::get('file/{file}/edit', [MemberPortalMemberFileController::class, 'edit'])->name('files.edit');
        Route::put('file/{file}/update', [MemberPortalMemberFileController::class, 'update'])->name('files.update');
    });
    //loans
    Route::group(['prefix' => 'loan', 'as' => 'loans.'], function () {


        Route::get('/', [MemberPortalLoansController::class, 'index'])->name('index');
        Route::get('/{loan}/show', [MemberPortalLoansController::class, 'show'])->name('show');
        Route::get('/application', [MemberPortalLoanApplicationsController::class, 'index'])->name('applications.index');
        Route::get('/application/create', [MemberPortalLoanApplicationsController::class, 'create'])->name('applications.create');
        Route::post('/application/store', [MemberPortalLoanApplicationsController::class, 'store'])->name('applications.store');
        Route::get('application/{application}/show', [MemberPortalLoanApplicationsController::class, 'show'])->name('applications.show');

        Route::get('{loan}/schedule', [MemberPortalLoanScheduleController::class, 'index'])->name('schedules.index');
        Route::get('{loan}/schedule/show', [MemberPortalLoanScheduleController::class, 'show'])->name('schedules.show');
        Route::get('{loan}/schedule/email', [MemberPortalLoanScheduleController::class, 'email'])->name('schedules.email');
        Route::get('{loan}/schedule/pdf', [MemberPortalLoanScheduleController::class, 'pdf'])->name('schedules.pdf');
        Route::get('{loan}/schedule/print', [MemberPortalLoanScheduleController::class, 'printSchedule'])->name('schedules.print');
        //applications

        //loan files
        Route::get('{loan}/file', [MemberPortalLoanFileController::class, 'index'])->name('files.index');
        Route::get('{loan}/file/create', [MemberPortalLoanFileController::class, 'create'])->name('files.create');
        Route::post('{loan}/file/store', [MemberPortalLoanFileController::class, 'store'])->name('files.store');
        Route::get('{loan}/file/show', [MemberPortalLoanFileController::class, 'show'])->name('files.show');
        Route::get('file/{file}/edit', [MemberPortalLoanFileController::class, 'edit'])->name('files.edit');
        Route::put('file/{file}/update', [MemberPortalLoanFileController::class, 'update'])->name('files.update');
        //loan transactions
        Route::get('{loan}/transaction', [MemberPortalLoanTransactionController::class, 'index'])->name('transactions.index');
        Route::get('{loan}/transaction/create', [MemberPortalLoanTransactionController::class, 'create'])->name('transactions.create');
        Route::post('{loan}/transaction/store', [MemberPortalLoanTransactionController::class, 'store'])->name('transactions.store');
        Route::get('transaction/{transaction}/show', [MemberPortalLoanTransactionController::class, 'show'])->name('transactions.show');
        Route::get('transaction/{transaction}/pdf', [MemberPortalLoanTransactionController::class, 'pdf'])->name('transactions.pdf');
        Route::get('transaction/{transaction}/print', [MemberPortalLoanTransactionController::class, 'printTransaction'])->name('transactions.print');
        //charges
        Route::get('{loan}/charge', [MemberPortalLoanLinkedChargesController::class, 'index'])->name('linked_charges.index');
    });
});
Route::prefix('webhooks')->name('webhooks.')->group(function () {
    Route::any('/{invoice}/initiate_invoice_payment', [WebhooksController::class, 'initiateInvoicePayment'])->name('initiate_invoice_payment');
    Route::any('/paypal', [WebhooksController::class, 'paypalWebhook'])->name('paypal');
    Route::any('/paynow', [WebhooksController::class, 'paynowWebhook'])->name('paynow');
    Route::any('/stripe', [WebhooksController::class, 'stripeWebhook'])->name('stripe');
});
Route::any('deploy', function () {
    $output = new BufferedOutput();
    Artisan::call('deploy', [], $output);
    dd($output->fetch());
});
Route::any('reset_permissions', function () {
    $output = new BufferedOutput();
    Artisan::call('permissions:reset', [], $output);
    dump($output->fetch());
});
