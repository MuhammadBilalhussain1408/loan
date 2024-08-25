<?php

namespace App\Http\Controllers;

use App\Models\MemberDesignation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MemberDesignationsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:members.designations.index'])->only(['index', 'show']);
        $this->middleware(['permission:members.designations.create'])->only(['create', 'store']);
        $this->middleware(['permission:members.designations.update'])->only(['edit', 'update']);
        $this->middleware(['permission:members.designations.destroy'])->only(['destroy']);

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
        $results = MemberDesignation::filter(request()->only('search'))
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->paginate($perPage)
            ->appends($request->input());
        return Inertia::render('MemberDesignations/Index', [
            'filters' => \request()->all('search'),
            'results' => $results,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('MemberDesignations/Create');
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
        $designation = new MemberDesignation();
        $designation->name = $request->name;
        $designation->description = $request->description;
        $designation->save();

        return redirect()->route('members.designations.index')->with('success', 'Successfully created');
    }

    /**
     * Show the form for editing the specified resource.
     * @param MemberDesignation $designation
     * @return \Inertia\Response
     */
    public function edit(MemberDesignation $designation)
    {
        return Inertia::render('MemberDesignations/Edit', [
            'designation' => $designation
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param MemberDesignation $designation
     * @return RedirectResponse
     */
    public function update(Request $request, MemberDesignation $designation)
    {
        $request->validate([
            'name' => ['required'],
        ]);
        $designation->name = $request->name;
        $designation->description = $request->description;
        $designation->save();
        return redirect()->route('members.designations.index')->with('success', 'Successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     * @param MemberDesignation $designation
     * @return RedirectResponse
     */
    public function destroy(MemberDesignation $designation)
    {

        $designation->delete();
        return redirect()->back()->with('success', 'Successfully deleted');
    }
}
