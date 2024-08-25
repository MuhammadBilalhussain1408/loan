<?php

namespace App\Http\Controllers;

use App\Actions\Reports\Reports;
use App\Exports\BaseExport;
use App\Models\Branch;
use App\Models\CustomField;
use App\Models\CustomFieldMeta;
use App\Models\LoanProduct;
use App\Models\LoanTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LoanReportController extends Controller
{
    public Reports $reports;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:reports.loans.index'])->only(['index']);
        $this->middleware(['permission:reports.loans.repayments'])->only(['repayment']);
        $this->middleware(['permission:reports.loans.collection_sheet'])->only(['collection_sheet']);
        $this->middleware(['permission:reports.loans.expected_repayments'])->only(['expected_repayment']);
        $this->middleware(['permission:reports.loans.arrears'])->only(['arrears']);
        $this->middleware(['permission:reports.loans.disbursement'])->only(['disbursement']);
        $this->reports = new Reports();
    }

    /**
     * Display a listing of the resource.
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Reports/Loan/Index');
    }

    /**
     * Show the form for creating a new resource.
     * @return \Inertia\Response
     */
    public function collection_sheet(Request $request)
    {
        $branches = Branch::all();
        $products = LoanProduct::all();
        $results = [];
        $data = [
            'start_date' => $request->start_date ?: date('Y-m-d'),
            'end_date' => $request->end_date ?: date('Y-m-d'),
            'branch_id' => $request->branch_id,
            'loan_product_id' => $request->loan_product_id,
            'loan_officer_id' => $request->loan_officer_id,
            'age_from' => $request->age_from,
            'age_to' => $request->age_to,
            'gender' => $request->gender,
        ];
        if ($request->download) {
            $data['paginate'] = false;
        } else {
            $data['paginate'] = true;
        }
        if (!empty($data['start_date'])) {
            $results = $this->reports->getCollectionSheetReport($data);

            //check if we should download
            if ($request->download) {
                $data['results'] = $results;
                $data['download_type'] = $request->download_type;
                if ($request->download_type == 'pdf') {
                    $pdf = Pdf::loadView('reports.collection_sheet_pdf', $data);
                    return $pdf->download('collection_sheet.pdf');
                }
                $view = view('reports.collection_sheet_excel', $data);
                if ($request->download_type == 'excel') {
                    return Excel::download(new BaseExport($view), 'collection_sheet.xls');
                }
                if ($request->download_type == 'csv') {
                    return Excel::download(new BaseExport($view), 'collection_sheet.csv');
                }
            }
        }
        return Inertia::render('Reports/Loan/CollectionSheet',
            [
                'filters' => $data,
                'results' => $results,
                'branches' => $branches,
                'products' => $products,
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return \Inertia\Response|BinaryFileResponse|Response
     */
    public function repayment(Request $request)
    {
        $branches = Branch::all();
        $products = LoanProduct::all();
        $results = [];
        $data = [
            'start_date' => $request->start_date ?: date('Y-m-d'),
            'end_date' => $request->end_date ?: date('Y-m-d'),
            'branch_id' => $request->branch_id,
            'loan_product_id' => $request->loan_product_id,
            'loan_officer_id' => $request->loan_officer_id,
            'age_from' => $request->age_from,
            'age_to' => $request->age_to,
            'gender' => $request->gender,
        ];
        if ($request->download) {
            $data['paginate'] = false;
        } else {
            $data['paginate'] = true;
        }
        if (!empty($data['start_date'])) {
            $results = $this->reports->getLoanRepaymentReport($data);
            //check if we should download
            if ($request->download) {
                $data['results'] = $results;
                $data['download_type'] = $request->download_type;
                if ($request->download_type == 'pdf') {
                    $pdf = Pdf::loadView('reports.repayment_pdf', $data);
                    return $pdf->download('loan_repayments.pdf');
                }
                $view = view('reports.repayment_excel', $data);
                if ($request->download_type == 'excel') {
                    return Excel::download(new BaseExport($view), 'loan_repayments.xls');
                }
                if ($request->download_type == 'csv') {
                    return Excel::download(new BaseExport($view), 'loan_repayments.csv');
                }
            }
        }
        return Inertia::render('Reports/Loan/Repayment',
            [
                'filters' => $data,
                'results' => $results,
                'branches' => $branches,
                'products' => $products,
            ]
        );
    }

    public function expectedRepayment(Request $request)
    {
        $branches = Branch::all();
        $products = LoanProduct::all();
        $results = [];
        $data = [
            'start_date' => $request->start_date ?: date('Y-m-d'),
            'end_date' => $request->end_date ?: date('Y-m-d'),
            'branch_id' => $request->branch_id,
            'loan_product_id' => $request->loan_product_id,
            'loan_officer_id' => $request->loan_officer_id,
            'age_from' => $request->age_from,
            'age_to' => $request->age_to,
            'gender' => $request->gender,
        ];
        if ($request->download) {
            $data['paginate'] = false;
        } else {
            $data['paginate'] = true;
        }
        $branches = Branch::all();
        if (!empty($data['start_date'])) {
            $results = $this->reports->getLoanExpectedRepaymentReport($data);
            //check if we should download
            if ($request->download) {
                $data['results'] = $results;
                $data['download_type'] = $request->download_type;
                if ($request->download_type == 'pdf') {
                    $pdf = Pdf::loadView('reports.expected_repayment_pdf', $data);
                    return $pdf->download('expected_repayments.pdf');
                }
                $view = view('reports.expected_repayment_excel', $data);
                if ($request->download_type == 'excel') {
                    return Excel::download(new BaseExport($view), 'expected_repayments.xls');
                }
                if ($request->download_type == 'csv') {
                    return Excel::download(new BaseExport($view), 'expected_repayments.csv');
                }
            }
        }
        return Inertia::render('Reports/Loan/ExpectedRepayment',
            [
                'filters' => $data,
                'results' => $results,
                'branches' => $branches,
                'products' => $products,
            ]
        );
    }

    public function arrears(Request $request)
    {
        $branches = Branch::all();
        $products = LoanProduct::all();
        $results = [];
        $data = [
            'start_date' => $request->start_date ?: date('Y-m-d'),
            'end_date' => $request->end_date ?: date('Y-m-d'),
            'branch_id' => $request->branch_id,
            'loan_product_id' => $request->loan_product_id,
            'loan_officer_id' => $request->loan_officer_id,
            'age_from' => $request->age_from,
            'age_to' => $request->age_to,
            'gender' => $request->gender,
        ];
        if ($request->download) {
            $data['paginate'] = false;
        } else {
            $data['paginate'] = true;
        }
        if (!empty($data['end_date'])) {
            $results = $this->reports->getArrearsReport($data);

            //check if we should download
            if ($request->download) {
                $data['results'] = $results;
                $data['download_type'] = $request->download_type;
                if ($request->download_type == 'pdf') {
                    $pdf = Pdf::loadView('reports.arrears_pdf', $data)->setPaper('A4', 'landscape');
                    return $pdf->download('arrears.pdf');
                }
                $view = view('reports.arrears_excel', $data);
                if ($request->download_type == 'excel') {
                    return Excel::download(new BaseExport($view), 'arrears.xls');
                }
                if ($request->download_type == 'csv') {
                    return Excel::download(new BaseExport($view), 'arrears.csv');
                }
            }
        }
        return Inertia::render('Reports/Loan/Arrears',
            [
                'filters' => $data,
                'results' => $results,
                'branches' => $branches,
                'products' => $products,
            ]
        );
    }

    public function disbursement(Request $request)
    {
        $branches = Branch::all();
        $products = LoanProduct::all();
        $results = [];
        $data = [
            'start_date' => $request->start_date ?: date('Y-m-d'),
            'end_date' => $request->end_date ?: date('Y-m-d'),
            'branch_id' => $request->branch_id,
            'loan_product_id' => $request->loan_product_id,
            'loan_officer_id' => $request->loan_officer_id,
            'age_from' => $request->age_from,
            'age_to' => $request->age_to,
            'gender' => $request->gender,
        ];
        if ($request->download) {
            $data['paginate'] = false;
        } else {
            $data['paginate'] = true;
        }
        if (!empty($data['start_date'])) {
            $results = $this->reports->getDisbursementReport($data);
            //check if we should download
            if ($request->download) {
                $data['results'] = $results;
                $data['download_type'] = $request->download_type;
                if ($request->download_type == 'pdf') {
                    $pdf = Pdf::loadView('reports.disbursement_pdf', $data)->setPaper('A4', 'landscape');
                    return $pdf->download('disbursement.pdf');
                }
                $view = view('reports.disbursement_excel', $data);
                if ($request->download_type == 'excel') {
                    return Excel::download(new BaseExport($view), 'disbursement.xls');
                }
                if ($request->download_type == 'csv') {
                    return Excel::download(new BaseExport($view), 'disbursement.csv');
                }
            }
        }
        return Inertia::render('Reports/Loan/Disbursement',
            [
                'filters' => $data,
                'results' => $results,
                'branches' => $branches,
                'products' => $products,
            ]
        );
    }
}
