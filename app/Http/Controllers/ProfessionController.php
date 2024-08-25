<?php

namespace App\Http\Controllers;

use App\Models\Profession;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class ProfessionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:members.professions.index'])->only(['index', 'show']);
        $this->middleware(['permission:members.professions.create'])->only(['create', 'store']);
        $this->middleware(['permission:members.professions.update'])->only(['edit', 'update']);
        $this->middleware(['permission:members.professions.destroy'])->only(['destroy']);

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
        $results = Profession::filter(request()->only('search'))
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->paginate($perPage);
        return Inertia::render('MemberProfessions/Index', [
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
        return Inertia::render('MemberProfessions/Create');
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
        $profession = new Profession();
        $profession->name = $request->name;
        $profession->save();
        activity()->on($profession)
            ->withProperties(['id' => $profession->id])
            ->log('Create Profession');
        return redirect()->route('members.professions.index')->with('success','Successfully created');
    }


    /**
     * Show the form for editing the specified resource.
     * @param Profession $profession
     * @return \Inertia\Response
     */
    public function edit(Profession $profession)
    {
        return Inertia::render('MemberProfessions/Edit', [
            'profession'=>$profession
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param Profession $profession
     * @return RedirectResponse
     */
    public function update(Request $request, Profession $profession)
    {
        $request->validate([
            'name' => ['required'],
        ]);
        $profession->name = $request->name;
        $profession->save();
        activity()->on($profession)
            ->withProperties(['id' => $profession->id])
            ->log('Update Profession');
        return redirect()->route('members.professions.index')->with('success','Successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     * @param Profession $profession
     * @return RedirectResponse
     */
    public function destroy(Profession $profession)
    {
        $profession->delete();
        activity()->on($profession)
            ->withProperties(['id' => $profession->id])
            ->log('Delete Profession');
        return redirect()->back()->with('success','Successfully deleted');
    }
}
