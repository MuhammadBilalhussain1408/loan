<?php

namespace App\Http\Controllers;

use App\Models\Fund;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class FundController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:loans.funds.index'])->only(['index', 'show']);
        $this->middleware(['permission:loans.funds.create'])->only(['create', 'store']);
        $this->middleware(['permission:loans.funds.update'])->only(['edit', 'update']);
        $this->middleware(['permission:loans.funds.destroy'])->only(['destroy']);

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
        $results = Fund::filter(\request()->only('search'))
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->paginate($perPage)
            ->appends($request->input());
        return Inertia::render('Funds/Index', [
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
        return Inertia::render('Funds/Create');
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
        $fund = new Fund();
        $fund->name = $request->name;
        $fund->save();
        activity()->on($fund)
            ->withProperties(['id' => $fund->id])
            ->log('Create Fund');
        return redirect()->route('loans.funds.index')->with('success','Successfully created');
    }



    /**
     * Show the form for editing the specified resource.
     * @param Fund $fund
     * @return \Inertia\Response
     */
    public function edit(Fund $fund)
    {

        return Inertia::render('Funds/Edit', [
            'fund'=>$fund
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param Fund $fund
     * @return RedirectResponse
     */
    public function update(Request $request, Fund $fund)
    {
        $request->validate([
            'name' => ['required'],
        ]);
        $fund->name = $request->name;
        $fund->save();
        activity()->on($fund)
            ->withProperties(['id' => $fund->id])
            ->log('Update Fund');
        return redirect()->route('loans.funds.index')->with('success','Successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     * @param Fund $fund
     * @return RedirectResponse
     */
    public function destroy(Fund $fund)
    {
        $fund->delete();
        activity()->on($fund)
            ->withProperties(['id' => $fund->id])
            ->log('Delete Fund');
        return redirect()->back()->with('success','Successfully deleted');
    }
}
