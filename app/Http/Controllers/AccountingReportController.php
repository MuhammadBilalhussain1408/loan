<?php

namespace App\Http\Controllers;

use App\Actions\Reports\Reports;
use App\Exports\BaseExport;
use App\Models\Branch;
use App\Models\Currency;
use App\Models\FinancialPeriod;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class AccountingReportController extends Controller
{
    public Reports $reports;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:reports.accounting.index'])->only(['index']);
        $this->middleware(['permission:reports.accounting.balance_sheet'])->only(['balance_sheet']);
        $this->middleware(['permission:reports.accounting.trial_balance'])->only(['trial_balance']);
        $this->middleware(['permission:reports.accounting.income_statement'])->only(['income_statement']);
        $this->middleware(['permission:reports.accounting.ledger'])->only(['ledger']);
        $this->reports = new Reports();
    }

    /**
     * Display a listing of the resource.
     * @return \Inertia\Response|RedirectResponse
     */
    public function index()
    {
        if (!FinancialPeriod::where('closed', 0)->count()) {
            return redirect()->route('accounting.financial_periods.index')->with('error', 'Please setup Financial Period if you want to use accounting');
        }
        return Inertia::render('Reports/Accounting/Index');
    }

    public function trialBalance(Request $request)
    {
        $branches = Branch::all();
        $currencies = Currency::where('active', 1)->get();
        $financialPeriods = FinancialPeriod::orderBy('id', 'desc')->get();
        $results = [];
        $data = [
            'start_date' => $request->start_date ?: date('Y-m-d'),
            'end_date' => $request->end_date ?: date('Y-m-d'),
            'branch_id' => $request->branch_id,
            'currency_id' => $request->currency_id,
        ];
        if (!empty($data['end_date'])) {
            $results = $this->reports->getTrialBalance($data);
            //check if we should download
            if ($request->download) {
                $data['results'] = $results;
                $data['download_type'] = $request->download_type;
                if ($request->download_type == 'pdf') {
                    $pdf = Pdf::loadView('reports.trial_balance_pdf', $data);
                    return $pdf->download('trial_balance.pdf');
                }
                $view = view('reports.trial_balance_excel', $data);
                if ($request->download_type == 'excel') {
                    return Excel::download(new BaseExport($view), 'trial_balance.xls');
                }
                if ($request->download_type == 'csv') {
                    return Excel::download(new BaseExport($view), 'trial_balance.csv');
                }
            }
        }
        return Inertia::render('Reports/Accounting/TrialBalance',
            [
                'filters' => $data,
                'results' => $results,
                'branches' => $branches,
                'currencies' => $currencies,
                'financialPeriods' => $financialPeriods,
            ]
        );
    }

    //income statement
    public function incomeStatement(Request $request)
    {

        $branches = Branch::all();
        $currencies = Currency::where('active', 1)->get();
        $financialPeriods = FinancialPeriod::orderBy('id', 'desc')->get();
        $results = [];
        $data = [
            'start_date' => $request->start_date ?: date('Y-m-d'),
            'end_date' => $request->end_date ?: date('Y-m-d'),
            'branch_id' => $request->branch_id,
            'currency_id' => $request->currency_id,
            'financial_period_id' => $request->financial_period_id,
        ];

        if (!empty($data['end_date'])) {
            $results = $this->reports->getIncomeStatement($data);
            //check if we should download
            if ($request->download) {
                $data['results'] = $results;
                $data['download_type'] = $request->download_type;
                if ($request->download_type == 'pdf') {
                    $pdf = Pdf::loadView('reports.income_statement_pdf', $data)->setPaper('A4', 'landscape');
                    return $pdf->download('income_statement.pdf');
                }
                $view = view('reports.income_statement_excel', $data);
                if ($request->download_type == 'excel') {
                    return Excel::download(new BaseExport($view), 'income_statement.xls');
                }
                if ($request->download_type == 'csv') {
                    return Excel::download(new BaseExport($view), 'income_statement.csv');
                }
            }
        }
        return Inertia::render('Reports/Accounting/IncomeStatement',
            [
                'filters' => $data,
                'results' => $results,
                'branches' => $branches,
                'currencies' => $currencies,
                'financialPeriods' => $financialPeriods,
            ]
        );
    }

    //balance sheet
    public function balanceSheet(Request $request)
    {
        $branches = Branch::all();
        $currencies = Currency::where('active', 1)->get();
        $financialPeriods = FinancialPeriod::orderBy('id', 'desc')->get();
        $results = [];
        $data = [
            'start_date' => $request->start_date ?: date('Y-m-d'),
            'end_date' => $request->end_date ?: date('Y-m-d'),
            'branch_id' => $request->branch_id,
            'currency_id' => $request->currency_id,
            'financial_period_id' => $request->financial_period_id,
        ];
        if (!empty($data['end_date'])) {
            $results = $this->reports->getBalanceSheet($data);
            //check if we should download
            if ($request->download) {
                $data['results'] = $results;
                $data['download_type'] = $request->download_type;
                if ($request->download_type == 'pdf') {
                    $pdf = Pdf::loadView('reports.balance_sheet_pdf', $data)->setPaper('A4', 'landscape');
                    return $pdf->download('balance_sheet.pdf');
                }
                $view = view('reports.balance_sheet_excel', $data);
                if ($request->download_type == 'excel') {
                    return Excel::download(new BaseExport($view), 'balance_sheet.xls');
                }
                if ($request->download_type == 'csv') {
                    return Excel::download(new BaseExport($view), 'balance_sheet.csv');
                }
            }
        }
        return Inertia::render('Reports/Accounting/BalanceSheet',
            [
                'filters' => $data,
                'results' => $results,
                'branches' => $branches,
                'currencies' => $currencies,
                'financialPeriods' => $financialPeriods,
            ]
        );
    }

    public function ledger(Request $request)
    {
        $branches = Branch::all();
        $currencies = Currency::where('active', 1)->get();
        $financialPeriods = FinancialPeriod::orderBy('id', 'desc')->get();
        $results = [];
        $data = [
            'start_date' => $request->start_date ?: date('Y-m-d'),
            'end_date' => $request->end_date ?: date('Y-m-d'),
            'branch_id' => $request->branch_id,
            'savings_product_id' => $request->savings_product_id,
            'savings_officer_id' => $request->savings_officer_id,
            'age_from' => $request->age_from,
            'age_to' => $request->age_to,
            'gender' => $request->gender,
        ];

        $results = $this->reports->getLedgerReport($data);

        //check if we should download
        if ($request->download) {
            $data['results'] = $results;
            $data['download_type'] = $request->download_type;
            if ($request->download_type == 'pdf') {
                $pdf = Pdf::loadView('reports.ledger_pdf', $data)->setPaper('A4', 'landscape');
                return $pdf->download('ledger.pdf');
            }
            $view = view('reports.ledger_excel', $data);
            if ($request->download_type == 'excel') {
                return Excel::download(new BaseExport($view), 'ledger.xls');
            }
            if ($request->download_type == 'csv') {
                return Excel::download(new BaseExport($view), 'ledger.csv');
            }
        }
        return Inertia::render('Reports/Accounting/Ledger',
            [
                'filters' => $data,
                'results' => $results,
                'branches' => $branches,
                'currencies' => $currencies,
                'financialPeriods' => $financialPeriods,
            ]
        );
    }

}
