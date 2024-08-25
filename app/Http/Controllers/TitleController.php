<?php

namespace App\Http\Controllers;

use App\Models\Title;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class TitleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:members.titles.index'])->only(['index', 'show']);
        $this->middleware(['permission:members.titles.create'])->only(['create', 'store']);
        $this->middleware(['permission:members.titles.update'])->only(['edit', 'update']);
        $this->middleware(['permission:members.titles.destroy'])->only(['destroy']);

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
        $results = Title::filter(request()->only('search'))
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->paginate($perPage)
            ->appends($request->input());
        return Inertia::render('MemberTitles/Index', [
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
        return Inertia::render('MemberTitles/Create');
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
        $title = new Title();
        $title->name = $request->name;
        $title->save();
        activity()->on($title)
            ->withProperties(['id' => $title->id])
            ->log('Create Member Title');
        return redirect()->route('members.titles.index')->with('success', 'Successfully created');
    }


    /**
     * Show the form for editing the specified resource.
     * @param Title $title
     * @return \Inertia\Response
     */
    public function edit(Title $title)
    {

        return Inertia::render('MemberTitles/Edit', [
            'title' => $title
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param Title $title
     * @return RedirectResponse
     */
    public function update(Request $request, Title $title)
    {
        $request->validate([
            'name' => ['required'],
        ]);
        $title->name = $request->name;
        $title->save();
        activity()->on($title)
            ->withProperties(['id' => $title->id])
            ->log('Update Member Title');
        return redirect()->route('members.titles.index')->with('success', 'Successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     * @param Title $title
     * @return RedirectResponse
     */
    public function destroy(Title $title)
    {

        $title->delete();
        activity()->on($title)
            ->withProperties(['id' => $title->id])
            ->log('Delete Member Title');
        return redirect()->back()->with('success', 'Successfully deleted');
    }
}
