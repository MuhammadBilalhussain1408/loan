<?php

namespace App\Http\Controllers;

use App\Models\LoanApplication;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LoanStatementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $LoanStatement = null;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $duration = $request->duration;
        switch ($duration) {
            case 'This Month':
                $start_date = Carbon::now()->startOfMonth();
                $end_date = Carbon::now()->endOfMonth();
                break;
            case 'Previous Month':
                $start_date = Carbon::now()->subMonth()->startOfMonth();
                $end_date = Carbon::now()->subMonth()->endOfMonth();
                break;
            case 'This Year':
                $start_date = Carbon::now()->startOfYear();
                $end_date = Carbon::now()->endOfYear();
                break;
            case 'Previous Year':
                $start_date = Carbon::now()->subYear()->startOfYear();
                $end_date = Carbon::now()->subYear()->endOfYear();
                break;
        }
        if ($request->member_id && $start_date && $end_date) {
            $LoanStatement = LoanApplication::where('member_id', $request->member_id)
                ->whereBetween('created_at', [$start_date, $end_date])
                ->get();
        }
        if ($request->member_id && is_null($LoanStatement)) {
            $LoanStatement = LoanApplication::where('member_id', $request->member_id)->get();
        }

        return Inertia::render('LoanStatement/index', [
            'LoanStatement' => $LoanStatement
        ]);
    }
    function getLoanStatement(Request $request)
    {
        dd($request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
