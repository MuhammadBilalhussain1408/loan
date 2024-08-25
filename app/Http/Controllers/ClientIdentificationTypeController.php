<?php

namespace App\Http\Controllers;

use App\Models\ClientIdentificationType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class ClientIdentificationTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:members.identification_types.index'])->only(['index', 'show']);
        $this->middleware(['permission:members.identification_types.create'])->only(['create', 'store']);
        $this->middleware(['permission:members.identification_types.update'])->only(['edit', 'update']);
        $this->middleware(['permission:members.identification_types.destroy'])->only(['destroy']);

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
        $results = ClientIdentificationType::filter(request()->only('search'))
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->paginate($perPage)
            ->appends($request->input());
        return Inertia::render('ClientIdentificationTypes/Index', [
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
        return Inertia::render('ClientIdentificationTypes/Create');
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
        $type = new ClientIdentificationType();
        $type->name = $request->name;
        $type->save();
        activity()->on($type)
            ->withProperties(['id' => $type->id])
            ->log('Create Client Identification Type');
        return redirect()->route('members.identification_types.index')->with('success', 'Successfully created');
    }


    /**
     * Show the form for editing the specified resource.
     * @param ClientIdentificationType $type
     * @return \Inertia\Response
     */
    public function edit(ClientIdentificationType $type)
    {
        return Inertia::render('ClientIdentificationTypes/Edit', [
            'type' => $type
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param ClientIdentificationType $type
     * @return RedirectResponse
     */
    public function update(Request $request, ClientIdentificationType $type)
    {
        $request->validate([
            'name' => ['required'],
        ]);
        $type->name = $request->name;
        $type->save();
        activity()->on($type)
            ->withProperties(['id' => $type->id])
            ->log('Update Client Identification Type');
        return redirect()->route('members.identification_types.index')->with('success', 'Successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     * @param ClientIdentificationType $type
     * @return RedirectResponse
     */
    public function destroy(ClientIdentificationType $type)
    {
        $type->delete();
        activity()->on($type)
            ->withProperties(['id' => $type->id])
            ->log('Delete Client Identification Type');

        return redirect()->back()->with('success', 'Successfully deleted');
    }
}
