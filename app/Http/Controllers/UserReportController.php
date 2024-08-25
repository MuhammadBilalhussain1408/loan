<?php

namespace App\Http\Controllers;

use App\Actions\Reports\Reports;
use App\Exports\BaseExport;
use App\Models\Branch;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


class UserReportController extends Controller
{
    public Reports $reports;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:reports.users.index'])->only(['index']);
        $this->middleware(['permission:reports.users.performance'])->only(['performance']);
        $this->middleware(['permission:user.reports.accounts'])->only(['account']);
        $this->reports = new Reports();
    }

    /**
     * Display a listing of the resource.
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Reports/User/Index');
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return \Inertia\Response|BinaryFileResponse
     */
    public function performance(Request $request)
    {
        $branches = Branch::all();
        $data = [
            'start_date' => $request->start_date ?: date('Y-m-d'),
            'end_date' => $request->end_date ?: date('Y-m-d'),
            'branch_id' => $request->branch_id,
            'user_id' => $request->user_id,
            'age_from' => $request->age_from,
            'age_to' => $request->age_to,
            'gender' => $request->gender,
        ];
        $results = [];
        if ($request->download) {
            $data['paginate'] = false;
        } else {
            $data['paginate'] = true;
        }
        if (!empty($data['start_date'])) {
            $results = $this->reports->getStaffReport($data);
            //check if we should download
            if ($request->download) {
                $data['results'] = $results;
                $data['download_type'] = $request->download_type;
                if ($request->download_type == 'pdf') {
                    $pdf = Pdf::loadView('reports.staff_performance_pdf', $data)->setPaper('A4', 'landscape');
                    return $pdf->download('staff_performance.pdf');
                }
                $view = view('reports.staff_performance_excel', $data);
                if ($request->download_type == 'excel') {
                    return Excel::download(new BaseExport($view), 'staff_performance.xls');
                }
                if ($request->download_type == 'csv') {
                    return Excel::download(new BaseExport($view), 'staff_performance.csv');
                }
            }
        }
        return Inertia::render('Reports/User/Performance',
            [
                'filters' => $data,
                'results' => $results,
                'branches' => $branches,
            ]
        );
    }

}
