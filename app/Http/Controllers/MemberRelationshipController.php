<?php

namespace App\Http\Controllers;

use App\Models\MemberRelationship;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MemberRelationshipController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:members.member_relationships.index'])->only(['index', 'show']);
        $this->middleware(['permission:members.member_relationships.create'])->only(['create', 'store']);
        $this->middleware(['permission:members.member_relationships.update'])->only(['edit', 'update']);
        $this->middleware(['permission:members.member_relationships.destroy'])->only(['destroy']);

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
        $results = MemberRelationship::filter(request()->only('search'))
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->paginate($perPage)
            ->appends($request->input());
        return Inertia::render('MemberRelationships/Index', [
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
        return Inertia::render('MemberRelationships/Create');
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
        $relationship = new MemberRelationship();
        $relationship->name = $request->name;
        $relationship->save();
        activity()->on($relationship)
            ->withProperties(['id' => $relationship->id])
            ->log('Create Member Relationship');
        return redirect()->route('members.relationships.index')->with('success', 'Successfully created');
    }

    /**
     * Show the form for editing the specified resource.
     * @param MemberRelationship $relationship
     * @return \Inertia\Response
     */
    public function edit(MemberRelationship $relationship)
    {
        return Inertia::render('MemberRelationships/Edit', [
            'relationship' => $relationship
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param MemberRelationship $relationship
     * @return RedirectResponse
     */
    public function update(Request $request, MemberRelationship $relationship)
    {
        $request->validate([
            'name' => ['required'],
        ]);
        $relationship->name = $request->name;
        $relationship->save();
        activity()->on($relationship)
            ->withProperties(['id' => $relationship->id])
            ->log('Update Member Relationship');
        return redirect()->route('members.relationships.index')->with('success', 'Successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     * @param MemberRelationship $relationship
     * @return RedirectResponse
     */
    public function destroy(MemberRelationship $relationship)
    {

        $relationship->delete();
        activity()->on($relationship)
            ->withProperties(['id' => $relationship->id])
            ->log('Delete Member Relationship');
        return redirect()->back()->with('success', 'Successfully deleted');
    }
}
