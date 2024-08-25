<?php

namespace App\Http\Controllers;

use App\Models\FileType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FileTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:files.types.index'])->only(['index', 'show']);
        $this->middleware(['permission:files.types.create'])->only(['create', 'store']);
        $this->middleware(['permission:files.types.update'])->only(['edit', 'update']);
        $this->middleware(['permission:files.types.destroy'])->only(['destroy']);

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
        $results = FileType::filter(request()->only('search'))
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->paginate($perPage)
            ->appends($request->input());
        return Inertia::render('FileTypes/Index', [
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
        return Inertia::render('FileTypes/Create');
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
        $type = new FileType();
        $type->name = $request->name;
        $type->save();
        return redirect()->route('files.types.index')->with('success', 'Created successfully.');
    }


    /**
     * Show the form for editing the specified resource.
     * @param FileType $type
     * @return \Inertia\Response
     */
    public function edit(FileType $type)
    {
        return Inertia::render('FileTypes/Edit', [
            'type' => $type
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param FileType $type
     * @return RedirectResponse
     */
    public function update(Request $request, FileType $type)
    {
        $request->validate([
            'name' => ['required'],
        ]);
        $type->name = $request->name;
        $type->save();
        return redirect()->route('files.types.index')->with('success', 'Successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     * @param FileType $type
     * @return RedirectResponse
     */
    public function destroy(FileType $type)
    {
        $type->delete();
        return redirect()->back()->with('success', 'Successfully deleted');
    }
}
