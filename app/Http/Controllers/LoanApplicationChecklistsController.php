<?php

namespace App\Http\Controllers;

use App\Models\LoanApplicationChecklist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class LoanApplicationChecklistsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:loans.checklists.index'])->only(['index', 'show']);
        $this->middleware(['permission:loans.checklists.create'])->only(['create', 'store']);
        $this->middleware(['permission:loans.checklists.update'])->only(['edit', 'update']);
        $this->middleware(['permission:loans.checklists.destroy'])->only(['destroy']);

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
        $results = LoanApplicationChecklist::filter(\request()->only('search'))
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->paginate($perPage)
            ->appends($request->input());
        return Inertia::render('LoanApplicationChecklists/Index', [
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
        return Inertia::render('LoanApplicationChecklists/Create');
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
        $checklist = new LoanApplicationChecklist();
        $checklist->name = $request->name;
        $checklist->description = $request->description;
        $checklist->save();
        foreach ($request->items as $key) {
            $checklist->items()->create([
                'name' => $key['name'],
                'description' => $key['description'],
            ]);
        }
        return redirect()->route('loans.checklists.index')->with('success', 'Successfully created');
    }


    /**
     * Show the form for editing the specified resource.
     * @param LoanApplicationChecklist $checklist
     * @return \Inertia\Response
     */
    public function edit(LoanApplicationChecklist $checklist)
    {
        $checklist->load(['items']);
        return Inertia::render('LoanApplicationChecklists/Edit', [
            'checklist' => $checklist
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param LoanApplicationChecklist $checklist
     * @return RedirectResponse
     */
    public function update(Request $request, LoanApplicationChecklist $checklist)
    {
        $request->validate([
            'name' => ['required'],
        ]);
        $checklist->name = $request->name;
        $checklist->description = $request->description;
        $checklist->save();
        $existingIDS = $checklist->items->pluck('id')->toArray();
        $currentIDS = [];
        foreach ($request->items as $key) {
            if (isset($key['id'])) {
                $currentIDS[] = $key['id'];
                $item = $checklist->items()->find($key['id']);
                $item->name = $key['name'];
                $item->description = $key['description'];
                $item->save();
            } else {
                $checklist->items()->create([
                    'name' => $key['name'],
                    'description' => $key['description'],
                ]);
            }

        }
        foreach ($existingIDS as $key) {
            if (!in_array($key, $currentIDS)) {
                $checklist->items()->find($key)->delete();
            }
        }
        return redirect()->route('loans.checklists.index')->with('success', 'Successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     * @param LoanApplicationChecklist $checklist
     * @return RedirectResponse
     */
    public function destroy(LoanApplicationChecklist $checklist)
    {
        $checklist->items()->delete();
        $checklist->delete();
        return redirect()->back()->with('success', 'Successfully deleted');
    }
}
