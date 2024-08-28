<?php

namespace App\Http\Controllers;

use App\Models\LoanApplication;
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
        if ($request->member_id && $request->start_date && $request->end_date) {
            $LoanStatement = LoanApplication::where('member_id', $request->member_id)
            ->whereBetween('created_at',[$request->start_date, $request->end_date])
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
