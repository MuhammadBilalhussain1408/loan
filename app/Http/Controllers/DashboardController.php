<?php

namespace App\Http\Controllers;

use App\Actions\Reports\Reports;
use App\Models\Appointment;
use App\Models\LoanApplication;
use App\Models\Member;
use App\Models\Consultation;
use App\Models\CoPayer;
use App\Models\Currency;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Loan;
use App\Models\LoanRepaymentSchedule;
use App\Models\LoanTransaction;
use App\Models\LoanTransactionType;
use App\Models\MemberCategory;
use App\Models\MemberDesignation;
use App\Models\Patient;
use App\Models\PaymentType;
use App\Models\UserWidgets;
use App\Models\Vital;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }

    public function index()
    {
        $totalMembers = Member::count();
        $totalLoanApplications = LoanApplication::count();
        $totalLoansRejected = LoanApplication::where('status', 'rejected')->count();
        $totalLoansPending = LoanApplication::where('status', 'pending')->count();
        $totalLoansApproved = LoanApplication::where('status', 'approved')->count();
        $totalLoans = Loan::count();
        $totalLoanPrincipal = Loan::sum('principal');
        $totalLoanPrincipalRepaid = Loan::sum('principal_repaid_derived');
        $totalLoanPrincipalOutstanding = Loan::sum('principal_outstanding_derived');
        $totalLoanInterest = Loan::sum('interest_disbursed_derived');
        $totalLoanInterestRepaid = Loan::sum('interest_repaid_derived');
        $totalLoanInterestOutstanding = Loan::sum('interest_outstanding_derived');
        $totalLoanFees = Loan::sum('fees_disbursed_derived');
        $totalLoanFeesRepaid = Loan::sum('fees_repaid_derived');
        $totalLoanFeesOutstanding = Loan::sum('fees_outstanding_derived');
        $totalLoanPenalties = Loan::sum('penalties_disbursed_derived');
        $totalLoanPenaltiesRepaid = Loan::sum('penalties_repaid_derived');
        $totalLoanPenaltiesOutstanding = Loan::sum('penalties_outstanding_derived');
        $totalLoanOutstandingAmount = Loan::sum('total_outstanding_derived');
        $totalLoanRepaidAmount = Loan::sum('total_repaid_derived');

        return Inertia::render('Dashboard', [
            'categories' => MemberCategory::get(),
            'designations' => MemberDesignation::get(),
            'totalMembers' => number_format($totalMembers),
            'totalLoanApplications' => number_format($totalLoanApplications),
            'totalLoans' => number_format($totalLoans),
            'totalLoansRejected' => number_format($totalLoansRejected),
            'totalLoansPending' => number_format($totalLoansPending),
            'totalLoansApproved' => number_format($totalLoansApproved),
            'totalLoanPrincipal' => number_format($totalLoanPrincipal),
            'totalLoanPrincipalRepaid' => number_format($totalLoanPrincipalRepaid),
            'totalLoanPrincipalOutstanding' => number_format($totalLoanPrincipalOutstanding),
            'totalLoanInterest' => number_format($totalLoanInterest),
            'totalLoanInterestRepaid' => number_format($totalLoanInterestRepaid),
            'totalLoanInterestOutstanding' => number_format($totalLoanInterestOutstanding),
            'totalLoanFees' => number_format($totalLoanFees),
            'totalLoanFeesRepaid' => number_format($totalLoanFeesRepaid),
            'totalLoanFeesOutstanding' => number_format($totalLoanFeesOutstanding),
            'totalLoanPenalties' => number_format($totalLoanPenalties),
            'totalLoanPenaltiesRepaid' => number_format($totalLoanPenaltiesRepaid),
            'totalLoanPenaltiesOutstanding' => number_format($totalLoanPenaltiesOutstanding),
            'totalLoanOutstandingAmount' => number_format($totalLoanOutstandingAmount),
            'totalLoanRepaidAmount' => number_format($totalLoanRepaidAmount),
        ]);
    }

    public function test()
    {
        return Inertia::render('Test', [

        ]);
    }

    public function saveWidgets(Request $request)
    {
        $widgets = config('widgets');
        return;
        $userWidgets = UserWidgets::where('user_id', Auth::id())->first();
        if (empty($userWidgets)) {
            $userWidgets = new UserWidgets();
            $userWidgets->user_id = Auth::id();
            $userWidgets->widgets = [];
            $userWidgets->save();
        }
        $selectedWidgets = [];
        foreach ($request->widgets as $key) {
            if (empty($userWidgets->widgets[$key])) {
                foreach ($widgets as $widget) {
                    if ($widget['id'] === $key) {
                        $selectedWidgets[$key] = $widget;
                    }
                }
            }
        }
        foreach ($userWidgets->widgets as $widget) {
            if (in_array($widget['id'], $request->widgets)) {
                $selectedWidgets[$widget['id']] = $widget;
            }
        }
        $userWidgets->widgets = $selectedWidgets;
        $userWidgets->save();
        return redirect()->back()->with('success', 'Updated successfully.');
    }

    public function updateWidgets(Request $request)
    {
        $widgets = config('widgets');
        return;
        $userWidgets = UserWidgets::where('user_id', Auth::id())->first();
        if (empty($userWidgets)) {
            $userWidgets = new UserWidgets();
            $userWidgets->user_id = Auth::id();
            $userWidgets->widgets = [];
            $userWidgets->save();
        }
        $selectedWidgets = [];
        foreach ($request->widgets as $key) {
            foreach ($widgets as $widget) {
                if ($widget['id'] === $key['id']) {
                    $selectedWidgets[$key['id']] = $key;
                }
            }
        }
        $userWidgets->widgets = $selectedWidgets;
        $userWidgets->save();
        return response()->json([
            'success' => true
        ]);
    }

    public function getLoanStatistics()
    {
        $loansDisbursedAmount = Loan::whereIn('status', ['active', 'closed', 'written_off'])
            ->selectRaw('coalesce(sum(if(loans.xrate>1,coalesce(loans.principal/loans.xrate,0),coalesce(loans.principal*loans.xrate,0))),0) as principal')
            ->first()
            ->principal;
        $loansDisbursedAmountThisMonth = Loan::where('status', 'active')
            ->whereBetween('disbursed_on_date', [Carbon::today()->startOfMonth()->format('Y-m-d H:i:s'), Carbon::today()->endOfMonth()->format('Y-m-d H:i:s')])
            ->selectRaw('coalesce(sum(if(loans.xrate>1,coalesce(loans.principal/loans.xrate,0),coalesce(loans.principal*loans.xrate,0))),0) as principal')
            ->first()
            ->principal;
        $loansDisbursedLastMonthAmount = Loan::where('status', 'active')
            ->whereBetween('disbursed_on_date', [Carbon::today()->subMonth()->startOfMonth()->format('Y-m-d H:i:s'), Carbon::today()->subMonth()->endOfMonth()->format('Y-m-d H:i:s')])
            ->selectRaw('coalesce(sum(if(loans.xrate>1,coalesce(loans.principal/loans.xrate,0),coalesce(loans.principal*loans.xrate,0))),0) as principal')
            ->first()
            ->principal;
        $loansDisbursedChange = 0;
        $loansDisbursedChangeClass = 'text-green-500';
        if ($loansDisbursedLastMonthAmount > 0) {
            $loansDisbursedChange = abs(($loansDisbursedLastMonthAmount - $loansDisbursedAmountThisMonth) * 100 / $loansDisbursedLastMonthAmount);
            if ($loansDisbursedAmountThisMonth < $loansDisbursedLastMonthAmount) {
                $loansDisbursedChangeClass = 'text-red-500';
            }
        }
        if ($loansDisbursedLastMonthAmount == 0 && $loansDisbursedAmountThisMonth > 0) {
            $loansDisbursedChange = 100;
        }
        $loansRepaymentsAmount = LoanTransaction::whereIn('loan_transaction_type_id', [LoanTransactionType::where('name', 'Repayment')->first()->id, LoanTransactionType::where('name', 'Repayment At Disbursement')->first()->id, LoanTransactionType::where('name', 'Recovery Repayment')->first()->id])
            ->where('reversed', 0)
            ->selectRaw('coalesce(sum(if(loan_transactions.xrate>1,coalesce(loan_transactions.amount/loan_transactions.xrate,0),coalesce(loan_transactions.amount*loan_transactions.xrate,0))),0) as amount')
            ->first()
            ->amount;
        $loansRepaymentsAmountThisMonth = LoanTransaction::whereIn('loan_transaction_type_id', [LoanTransactionType::where('name', 'Repayment')->first()->id, LoanTransactionType::where('name', 'Repayment At Disbursement')->first()->id, LoanTransactionType::where('name', 'Recovery Repayment')->first()->id])
            ->whereBetween('submitted_on', [Carbon::today()->startOfMonth()->format('Y-m-d H:i:s'), Carbon::today()->endOfMonth()->format('Y-m-d H:i:s')])
            ->where('reversed', 0)
            ->selectRaw('coalesce(sum(if(loan_transactions.xrate>1,coalesce(loan_transactions.amount/loan_transactions.xrate,0),coalesce(loan_transactions.amount*loan_transactions.xrate,0))),0) as amount')
            ->first()
            ->amount;
        $loansRepaymentsLastMonthAmount = LoanTransaction::whereIn('loan_transaction_type_id', [LoanTransactionType::where('name', 'Repayment')->first()->id, LoanTransactionType::where('name', 'Repayment At Disbursement')->first()->id, LoanTransactionType::where('name', 'Recovery Repayment')->first()->id])
            ->whereBetween('submitted_on', [Carbon::today()->subMonth()->startOfMonth()->format('Y-m-d H:i:s'), Carbon::today()->subMonth()->endOfMonth()->format('Y-m-d H:i:s')])
            ->where('reversed', 0)
            ->selectRaw('coalesce(sum(if(loan_transactions.xrate>1,coalesce(loan_transactions.amount/loan_transactions.xrate,0),coalesce(loan_transactions.amount*loan_transactions.xrate,0))),0) as amount')
            ->first()
            ->amount;
        $loansRepaymentsChange = 0;
        $loansRepaymentsChangeClass = 'text-green-500';
        if ($loansRepaymentsLastMonthAmount > 0) {
            $loansRepaymentsChange = abs(($loansRepaymentsLastMonthAmount - $loansRepaymentsAmountThisMonth) * 100 / $loansRepaymentsLastMonthAmount);
            if ($loansRepaymentsAmountThisMonth < $loansRepaymentsLastMonthAmount) {
                $loansRepaymentsChangeClass = 'text-red-500';
            }
        }
        if ($loansRepaymentsLastMonthAmount == 0 && $loansRepaymentsAmountThisMonth > 0) {
            $loansRepaymentsChange = 100;
        }
        $loansOutstandingAmount = Loan::whereIn('status', ['active', 'closed', 'written_off'])
            ->selectRaw('coalesce(sum(if(loans.xrate>1,coalesce(loans.total_outstanding_derived/loans.xrate,0),coalesce(loans.total_outstanding_derived*loans.xrate,0))),0) as total_outstanding_derived')
            ->first()
            ->total_outstanding_derived;
        $loansArrearsAmount = LoanRepaymentSchedule::where('due_date', '<', Carbon::today())
            ->where('total_due', '>', 0)
            ->whereHas('loan', function ($query) {
                $query->where('status', 'active');
            })
            ->selectRaw('coalesce(sum(if(loan_repayment_schedules.xrate>1,coalesce(loan_repayment_schedules.total_due/loan_repayment_schedules.xrate,0),coalesce(loan_repayment_schedules.total_due*loan_repayment_schedules.xrate,0))),0) as total_due')
            ->first()
            ->total_due;
        return response()->json([
            'loansDisbursedAmount' => number_format($loansDisbursedAmount),
            'loansDisbursedAmountChange' => number_format($loansDisbursedChange, 2),
            'loansDisbursedAmountChangeClass' => $loansDisbursedChangeClass,
            'loansRepaymentsAmount' => number_format($loansRepaymentsAmount),
            'loansRepaymentsAmountChange' => number_format($loansRepaymentsChange, 2),
            'loansRepaymentsAmountChangeClass' => $loansRepaymentsChangeClass,
            'loansOutstandingAmount' => number_format($loansOutstandingAmount),
            'loansArrearsAmount' => number_format($loansArrearsAmount),
        ]);
    }

    public function getClientStatistics()
    {
        $loansDisbursedAmountThisMonth = Loan::where('status', 'active')
            ->whereBetween('disbursed_on_date', [Carbon::today()->startOfMonth()->format('Y-m-d H:i:s'), Carbon::today()->endOfMonth()->format('Y-m-d H:i:s')])
            ->selectRaw('coalesce(sum(if(loans.xrate>1,coalesce(loans.principal/loans.xrate,0),coalesce(loans.principal*loans.xrate,0))),0) as principal')
            ->first()
            ->principal;
        $loansDisbursedLastMonthAmount = Loan::where('status', 'active')
            ->whereBetween('disbursed_on_date', [Carbon::today()->subMonth()->startOfMonth()->format('Y-m-d H:i:s'), Carbon::today()->subMonth()->endOfMonth()->format('Y-m-d H:i:s')])
            ->selectRaw('coalesce(sum(if(loans.xrate>1,coalesce(loans.principal/loans.xrate,0),coalesce(loans.principal*loans.xrate,0))),0) as principal')
            ->first()
            ->principal;
        $membersLastMonth = Member::whereBetween('created_date', [Carbon::today()->subMonth()->startOfMonth()->format('Y-m-d H:i:s'), Carbon::today()->subMonth()->endOfMonth()->format('Y-m-d H:i:s')])->count();
        $membersThisMonth = Member::whereBetween('created_date', [Carbon::today()->startOfMonth()->format('Y-m-d H:i:s'), Carbon::today()->endOfMonth()->format('Y-m-d H:i:s')])->count();
        $membersChange = 0;
        $membersChangeClass = 'text-green-500';
        if ($membersLastMonth > 0) {
            $membersChange = abs(($membersLastMonth - $membersThisMonth) * 100 / $membersLastMonth);
            if ($membersThisMonth < $membersLastMonth) {
                $membersChangeClass = 'text-red-500';
            }
        }
        if ($membersLastMonth === 0 && $membersThisMonth > 0) {
            $membersChange = 100;
        }
        return response()->json([
            'consultations' => number_format($consultations),
            'consultationsChange' => number_format($membersChange, 2),
            'consultationsChangeClass' => $membersChangeClass,
        ]);
    }

    public function getTotalPatientsCount()
    {
        $patients = Patient::count();
        $patientsLastMonth = Patient::whereBetween('created_at', [Carbon::today()->subMonth()->startOfMonth()->format('Y-m-d H:i:s'), Carbon::today()->subMonth()->endOfMonth()->format('Y-m-d H:i:s')])->count();
        $patientsThisMonth = Patient::whereBetween('created_at', [Carbon::today()->startOfMonth()->format('Y-m-d H:i:s'), Carbon::today()->endOfMonth()->format('Y-m-d H:i:s')])->count();
        $patientsChange = 0;
        $patientsChangeClass = 'text-green-500';
        if ($patientsLastMonth > 0) {
            $patientsChange = abs(($patientsThisMonth - $patientsLastMonth) * 100 / $patientsLastMonth);
            if ($patientsThisMonth < $patientsLastMonth) {
                $patientsChangeClass = 'text-red-500';
            }
        }
        if ($patientsLastMonth === 0 && $patientsThisMonth > 0) {
            $patientsChange = 100;
        }
        return response()->json([
            'patients' => number_format($patients),
            'patientsChange' => number_format($patientsChange, 2),
            'patientsChangeClass' => $patientsChangeClass,
        ]);
    }

    public function getLoanCollectionOverview()
    {
        $paymentsToday = LoanTransaction::whereIn('loan_transaction_type_id', [LoanTransactionType::where('name', 'Repayment')->first()->id, LoanTransactionType::where('name', 'Repayment At Disbursement')->first()->id, LoanTransactionType::where('name', 'Recovery Repayment')->first()->id])
            ->where('submitted_on', Carbon::today()->format('Y-m-d H:i:s'))
            ->where('reversed', 0)
            ->selectRaw('coalesce(sum(if(loan_transactions.xrate>1,coalesce(loan_transactions.amount/loan_transactions.xrate,0),coalesce(loan_transactions.amount*loan_transactions.xrate,0))),0) as amount')
            ->first()
            ->amount;
        $paymentsThisWeek = LoanTransaction::whereIn('loan_transaction_type_id', [LoanTransactionType::where('name', 'Repayment')->first()->id, LoanTransactionType::where('name', 'Repayment At Disbursement')->first()->id, LoanTransactionType::where('name', 'Recovery Repayment')->first()->id])
            ->whereBetween('submitted_on', [Carbon::today()->startOfWeek()->format('Y-m-d H:i:s'), Carbon::today()->endOfWeek()->format('Y-m-d H:i:s')])
            ->where('reversed', 0)
            ->selectRaw('coalesce(sum(if(loan_transactions.xrate>1,coalesce(loan_transactions.amount/loan_transactions.xrate,0),coalesce(loan_transactions.amount*loan_transactions.xrate,0))),0) as amount')
            ->first()
            ->amount;
        $paymentsThisMonth = LoanTransaction::whereIn('loan_transaction_type_id', [LoanTransactionType::where('name', 'Repayment')->first()->id, LoanTransactionType::where('name', 'Repayment At Disbursement')->first()->id, LoanTransactionType::where('name', 'Recovery Repayment')->first()->id])
            ->whereBetween('submitted_on', [Carbon::today()->startOfMonth()->format('Y-m-d H:i:s'), Carbon::today()->endOfMonth()->format('Y-m-d H:i:s')])
            ->where('reversed', 0)
            ->selectRaw('coalesce(sum(if(loan_transactions.xrate>1,coalesce(loan_transactions.amount/loan_transactions.xrate,0),coalesce(loan_transactions.amount*loan_transactions.xrate,0))),0) as amount')
            ->first()
            ->amount;
        $paymentsExpectedThisMonth = LoanRepaymentSchedule::whereHas('loan', function ($query) {
            $query->where('status', 'active');
        })
            ->whereBetween('due_date', [Carbon::today()->startOfMonth()->format('Y-m-d H:i:s'), Carbon::today()->endOfMonth()->format('Y-m-d H:i:s')])
            ->selectRaw('coalesce(sum(if(loan_repayment_schedules.xrate>1,coalesce(loan_repayment_schedules.total/loan_repayment_schedules.xrate,0),coalesce(loan_repayment_schedules.total*loan_repayment_schedules.xrate,0))),0) as total')
            ->first()
            ->total;
        $paymentsProgress = 0;
        if ($paymentsThisMonth > 0) {
            $paymentsProgress = round(abs(($paymentsExpectedThisMonth - $paymentsThisMonth) * 100 / $paymentsExpectedThisMonth));
        }
        $chartData = [];
        $startDate = Carbon::today()->startOfMonth()->subYear();
        for ($i = 0; $i < 13; $i++) {
            $actual = round(LoanTransaction::whereIn('loan_transaction_type_id', [LoanTransactionType::where('name', 'Repayment')->first()->id, LoanTransactionType::where('name', 'Repayment At Disbursement')->first()->id, LoanTransactionType::where('name', 'Recovery Repayment')->first()->id])
                ->whereBetween('submitted_on', [(clone $startDate)->startOfMonth()->format('Y-m-d'), (clone $startDate)->endOfMonth()->format('Y-m-d')])
                ->where('reversed', 0)
                ->selectRaw('coalesce(sum(if(loan_transactions.xrate>1,coalesce(loan_transactions.amount/loan_transactions.xrate,0),coalesce(loan_transactions.amount*loan_transactions.xrate,0))),0) as amount')
                ->first()
                ->amount);
            $expected = round(LoanRepaymentSchedule::whereHas('loan', function ($query) {
                $query->where('status', 'active');
            })
                ->whereBetween('due_date', [(clone $startDate)->startOfMonth()->format('Y-m-d'), (clone $startDate)->endOfMonth()->format('Y-m-d')])
                ->selectRaw('coalesce(sum(if(loan_repayment_schedules.xrate>1,coalesce(loan_repayment_schedules.total/loan_repayment_schedules.xrate,0),coalesce(loan_repayment_schedules.total*loan_repayment_schedules.xrate,0))),0) as total')
                ->first()
                ->total);
            $chartData[] = [
                'expected' => $expected,
                'actual' => $actual,
                'label' => (clone $startDate)->format('M Y')
            ];
            $startDate = $startDate->addMonth();
        }

        return response()->json([
            'paymentsToday' => number_format($paymentsToday),
            'paymentsThisWeek' => number_format($paymentsThisWeek),
            'paymentsThisMonth' => number_format($paymentsThisMonth),
            'paymentsExpectedThisMonth' => number_format($paymentsExpectedThisMonth),
            'paymentsProgress' => $paymentsProgress,
            'chartData' => $chartData,
        ]);
    }

    public function getTotalPaymentsAmount()
    {
        $payments = InvoicePayment::selectRaw('coalesce(sum(if(xrate>1,amount*xrate,amount/xrate)),0) as total_amount')
            ->first()->total_amount ?? 0;
        $paymentsLastMonth = InvoicePayment::whereBetween('created_at', [Carbon::today()->subMonth()->startOfMonth()->format('Y-m-d H:i:s'), Carbon::today()->subMonth()->endOfMonth()->format('Y-m-d H:i:s')])
            ->selectRaw('coalesce(sum(if(xrate>1,amount*xrate,amount/xrate)),0) as total_amount')
            ->first()->total_amount ?? 0;
        $paymentsThisMonth = InvoicePayment::whereBetween('created_at', [Carbon::today()->startOfMonth()->format('Y-m-d H:i:s'), Carbon::today()->endOfMonth()->format('Y-m-d H:i:s')])
            ->selectRaw('coalesce(sum(if(xrate>1,amount*xrate,amount/xrate)),0) as total_amount')
            ->first()->total_amount ?? 0;
        $paymentsChange = 0;
        $paymentsChangeClass = 'text-green-500';
        if ($paymentsLastMonth > 0) {
            $paymentsChange = abs(($paymentsThisMonth - $paymentsLastMonth) * 100 / $paymentsLastMonth);
            if ($paymentsThisMonth < $paymentsLastMonth) {
                $paymentsChangeClass = 'text-red-500';
            }
        }
        if ($paymentsLastMonth === 0 && $paymentsThisMonth > 0) {
            $paymentsChange = 100;
        }
        return response()->json([
            'payments' => number_format($payments),
            'paymentsChange' => number_format($paymentsChange, 2),
            'paymentsChangeClass' => $paymentsChangeClass,
        ]);
    }

    public function getTotalInvoicesAmount()
    {
        $invoices = Invoice::selectRaw('coalesce(sum(if(xrate>1,amount*xrate,amount/xrate)),0) as total_amount')
            ->first()->total_amount ?? 0;
        $invoicesLastMonth = Invoice::whereBetween('created_at', [Carbon::today()->subMonth()->startOfMonth()->format('Y-m-d H:i:s'), Carbon::today()->subMonth()->endOfMonth()->format('Y-m-d H:i:s')])
            ->selectRaw('coalesce(sum(if(xrate>1,amount*xrate,amount/xrate)),0) as total_amount')
            ->first()->total_amount ?? 0;
        $invoicesThisMonth = Invoice::whereBetween('created_at', [Carbon::today()->startOfMonth()->format('Y-m-d H:i:s'), Carbon::today()->endOfMonth()->format('Y-m-d H:i:s')])
            ->selectRaw('coalesce(sum(if(xrate>1,amount*xrate,amount/xrate)),0) as total_amount')
            ->first()->total_amount ?? 0;
        $invoicesChange = 0;
        $invoicesChangeClass = 'text-green-500';
        if ($invoicesLastMonth > 0) {
            $invoicesChange = abs(($invoicesThisMonth - $invoicesLastMonth) * 100 / $invoicesLastMonth);
            if ($invoicesThisMonth < $invoicesLastMonth) {
                $invoicesChangeClass = 'text-red-500';
            }
        }
        if ($invoicesLastMonth === 0 && $invoicesThisMonth > 0) {
            $invoicesChange = 100;
        }
        return response()->json([
            'invoices' => number_format($invoices),
            'invoicesChange' => number_format($invoicesChange, 2),
            'invoicesChangeClass' => $invoicesChangeClass,
        ]);
    }

    public function getWaitingList()
    {
        $query = Consultation::with(['doctor', 'patient', 'nurse']);
        if (Auth::user()->hasRole('doctor') && Auth::user()->hasPermissionTo('consultations.view_assigned_consultations_only')) {
            $query->where('doctor_id', Auth::id());
            $query->where('stage', 'waiting_for_doctor');
        }
        if (Auth::user()->hasRole('nurse') && Auth::user()->hasPermissionTo('consultations.view_assigned_consultations_only')) {
            $query->where('nurse_id', Auth::id());
            $query->where('stage', 'waiting_for_nurse');
        }
        if (Auth::user()->hasRole('receptionist') && Auth::user()->hasPermissionTo('consultations.view_assigned_consultations_only')) {
            $query->where('receptionist_id', Auth::id());
            $query->where('stage', 'with_receptionist');
        }
        $patients = $query->orderBy('created_at')->get()->map(function ($item) {
            if (Auth::user()->hasRole('doctor')) {
                $item->waiting_time = Carbon::now()->diffForHumans($item->nurse_completed_at, true, false);
            } elseif (Auth::user()->hasRole('nurse')) {
                $item->waiting_time = Carbon::now()->diffForHumans($item->receptionist_completed_at, true, false);
            } else {
                $item->waiting_time = Carbon::now()->diffForHumans($item->created_at, true, false);
            }
            return $item;
        });
        return response()->json($patients);
    }

    public function getAppointments(Request $request)
    {
        $doctorID = null;
        $nurseID = null;
        $receptionistID = null;
        if (Auth::user()->hasRole('doctor') && Auth::user()->hasPermissionTo('appointments.view_assigned_appointments_only')) {
            $doctorID = Auth::id();
        }
        $appointments = Appointment::with(['doctor', 'patient'])
            ->filter(\request()->only('search', 'branch_id', 'status', 'created_by_type', 'patient_id', 'doctor_id', 'appointment_type', 'date_range'))
            ->doctor($doctorID)
            ->where('start_date', '>=', Carbon::today()->format('Y-m-d'))
            ->get();
        return response()->json($appointments);
    }

    public function getAppointmentsByStatusPieChart(Request $request)
    {
        $reports = new Reports();
        $doctorID = null;
        $nurseID = null;
        $receptionistID = null;
        if (Auth::user()->hasRole('doctor') && Auth::user()->hasPermissionTo('appointments.view_assigned_appointments_only')) {
            $doctorID = Auth::id();
        }
        $appointments = $reports->getAppointmentsByStatus([
            'doctor_id' => $doctorID,
        ]);
        return response()->json($appointments);
    }

    public function getAppointmentsByPeriodGraph(Request $request)
    {
        $reports = new Reports();
        $doctorID = null;
        $nurseID = null;
        $receptionistID = null;
        if (Auth::user()->hasRole('doctor') && Auth::user()->hasPermissionTo('appointments.view_assigned_appointments_only')) {
            $doctorID = Auth::id();
        }
        $appointments = $reports->getAppointmentsByPeriod([
            'doctor_id' => $doctorID,
            'period' => $request->period,
        ]);
        return response()->json($appointments);
    }

    public function getLoansByStatusPieChart(Request $request)
    {
        $reports = new Reports();
        $data = $reports->getLoansByStatus([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'loan_officer_id' => $request->loan_officer_id,
            'branch_id' => $request->branch_id,
            'currency_id' => $request->currency_id,
        ]);
        return response()->json($data);
    }

    public function getPaymentsByPeriodGraph(Request $request)
    {
        $reports = new Reports();
        $data = $reports->getPaymentsByPeriod([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'doctor_id' => $request->doctor_id,
            'branch_id' => $request->branch_id,
            'currency_id' => $request->currency_id,
            'co_payer_id' => $request->co_payer_id,
            'payment_type_id' => $request->payment_type_id,
            'paid_by' => $request->paid_by,
            'period' => $request->period,
        ]);
        return response()->json($data);
    }

    public function getIncomeExpensesPieChart(Request $request)
    {
        $reports = new Reports();
        $data = $reports->getIncomeExpenses([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'doctor_id' => $request->doctor_id,
            'branch_id' => $request->branch_id,
            'currency_id' => $request->currency_id,
            'co_payer_id' => $request->co_payer_id,
            'payment_type_id' => $request->payment_type_id,
            'paid_by' => $request->paid_by,
            'period' => $request->period,
        ]);
        return response()->json($data);
    }

    public function getIncomeExpensesGraph(Request $request)
    {
        $reports = new Reports();
        $data = $reports->getPeriodIncomeExpenses([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'doctor_id' => $request->doctor_id,
            'branch_id' => $request->branch_id,
            'currency_id' => $request->currency_id,
            'co_payer_id' => $request->co_payer_id,
            'payment_type_id' => $request->payment_type_id,
            'paid_by' => $request->paid_by,
            'period' => $request->period,
        ]);
        return response()->json($data);
    }

    public function getConsultationsByPeriodGraph(Request $request)
    {
        $reports = new Reports();
        $data = $reports->getConsultationsByPeriod([
            'doctor_id' => $request->doctor_id,
            'branch_id' => $request->branch_id,
            'co_payer_id' => $request->co_payer_id,
            'period' => $request->period,
        ]);
        return response()->json($data);
    }
}
