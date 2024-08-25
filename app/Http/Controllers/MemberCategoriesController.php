<?php

namespace App\Http\Controllers;

use App\Models\MemberCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MemberCategoriesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:members.categories.index'])->only(['index', 'show']);
        $this->middleware(['permission:members.categories.create'])->only(['create', 'store']);
        $this->middleware(['permission:members.categories.update'])->only(['edit', 'update']);
        $this->middleware(['permission:members.categories.destroy'])->only(['destroy']);

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
        $results = MemberCategory::filter(request()->only('search'))
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->paginate($perPage)
            ->appends($request->input());
        return Inertia::render('MemberCategories/Index', [
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
        return Inertia::render('MemberCategories/Create');
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
        $category = new MemberCategory();
        $category->name = $request->name;
        $category->description = $request->description;
        $category->save();

        return redirect()->route('members.categories.index')->with('success', 'Successfully created');
    }

    /**
     * Show the form for editing the specified resource.
     * @param MemberCategory $category
     * @return \Inertia\Response
     */
    public function edit(MemberCategory $category)
    {
        return Inertia::render('MemberCategories/Edit', [
            'category' => $category
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param MemberCategory $category
     * @return RedirectResponse
     */
    public function update(Request $request, MemberCategory $category)
    {
        $request->validate([
            'name' => ['required'],
        ]);
        $category->name = $request->name;
        $category->description = $request->description;
        $category->save();
        return redirect()->route('members.categories.index')->with('success', 'Successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     * @param MemberCategory $category
     * @return RedirectResponse
     */
    public function destroy(MemberCategory $category)
    {

        $category->delete();
        return redirect()->back()->with('success', 'Successfully deleted');
    }
}
