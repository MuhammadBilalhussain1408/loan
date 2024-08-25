<?php

namespace App\Http\Controllers;

use App\Models\LoanApplicationApprovalStage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class LoanApplicationApprovalStageController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:loans.approval_stages.index'])->only(['index', 'show']);
        $this->middleware(['permission:loans.approval_stages.create'])->only(['create', 'store']);
        $this->middleware(['permission:loans.approval_stages.update'])->only(['edit', 'update']);
        $this->middleware(['permission:loans.approval_stages.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->per_page ?: 20;
        $orderBy = $request->order_by;
        $orderByDir = $request->order_by_dir;
        $search = $request->s;
        $results = LoanApplicationApprovalStage::filter(\request()->only('search'))
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->orderBy('field_position')
            ->paginate($perPage)
            ->appends($request->input());
        return Inertia::render('LoanApplicationApprovalStages/Index', [
            'filters' => \request()->all('search'),
            'results' => $results
        ]);
    }


    /**
     * Show the form for creating a new resource.
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('LoanApplicationApprovalStages/Create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required'],
        ]);
        $stage = new LoanApplicationApprovalStage();
        $stage->name = $request->name;
        $stage->assigned_to_id = $request->assigned_to_id;
        $stage->is_last = $request->is_last;
        $stage->field_position = $request->field_position;
        $stage->description = $request->description;
        $stage->save();
        return redirect()->route('loans.approval_stages.index')->with('success', 'Successfully created');
    }


    /**
     * Show the form for editing the specified resource.
     * @param LoanApplicationApprovalStage $stage
     * @return \Inertia\Response
     */
    public function edit(LoanApplicationApprovalStage $stage)
    {

        return Inertia::render('LoanApplicationApprovalStages/Edit', [
            'stage' => $stage
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param LoanApplicationApprovalStage $stage
     * @return RedirectResponse
     */
    public function update(Request $request, LoanApplicationApprovalStage $stage)
    {
        $request->validate([
            'name' => ['required'],
        ]);
        $stage->name = $request->name;
        $stage->assigned_to_id = $request->assigned_to_id;
        $stage->is_last = $request->is_last;
        $stage->field_position = $request->field_position;
        $stage->description = $request->description;
        $stage->save();
        return redirect()->route('loans.approval_stages.index')->with('success', 'Successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     * @param LoanApplicationApprovalStage $stage
     * @return RedirectResponse
     */
    public function destroy(LoanApplicationApprovalStage $stage)
    {
        $stage->delete();
        return redirect()->back()->with('success', 'Successfully deleted');
    }
}
