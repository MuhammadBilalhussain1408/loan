<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\ClientIdentification;
use App\Models\ClientIdentificationType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;


class ClientIdentificationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:members.identification.index'])->only(['index', 'show']);
        $this->middleware(['permission:members.identification.create'])->only(['create', 'store']);
        $this->middleware(['permission:members.identification.update'])->only(['edit', 'update']);
        $this->middleware(['permission:members.identification.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return \Inertia\Response
     */
    public function index(Member $member)
    {
        $member->load(['identifications', 'identifications.type', 'identifications.file']);
        return Inertia::render('Clients/ClientIdentifications/Index', [
            'member' => $member,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return \Inertia\Response
     */
    public function create(Member $member)
    {
        $types = ClientIdentificationType::all();
        return Inertia::render('Clients/ClientIdentifications/Create', [
            'member' => $member,
            'types' => $types
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request, Member $member)
    {
        $request->validate([
            'member_identification_type_id' => ['required'],
            'identification_value' => ['required'],
            'file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf'],
        ]);
        $identification = new ClientIdentification();
        $identification->created_by_id = Auth::id();
        $identification->member_id = $member->id;
        $identification->status = $request->status;
        $identification->identification_value = $request->identification_value;
        $identification->member_identification_type_id = $request->member_identification_type_id;
        $identification->description = $request->description;

        $identification->save();
        if ($request->hasFile('file')) {
            $fileController = new FilesController();
            $file = $fileController->store([
                'file' => $request->file('file'),
                'name' => $request->name,
                'description' => $request->description,
                'category' => 'member_identification',
                'record_id' => $identification->id,
            ]);
            $identification->file_id = $file->id;
            $identification->save();
        }
        return redirect()->route('members.identifications.index', $member->id)->with('success', 'Successfully created');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */


    /**
     * Show the form for editing the specified resource.
     * @param ClientIdentification $identification
     * @return \Inertia\Response
     */
    public function edit(ClientIdentification $identification)
    {
        $types = ClientIdentificationType::all();
        return Inertia::render('Clients/ClientIdentifications/Edit', [
            'types' => $types,
            'identification' => $identification,
            'member' => $identification->member,
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param ClientIdentification $identification
     * @return RedirectResponse
     */
    public function update(Request $request, ClientIdentification $identification)
    {
        $request->validate([
            'member_identification_type_id' => ['required'],
            'identification_value' => ['required'],
            'file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf'],
        ]);
        $identification->status = $request->status;
        $identification->identification_value = $request->identification_value;
        $identification->member_identification_type_id = $request->member_identification_type_id;
        $identification->description = $request->description;

        $identification->save();
        if ($request->hasFile('file')) {
            $fileController = new FilesController();
            $file = $fileController->store([
                'file' => $request->file('file'),
                'name' => $request->name,
                'description' => $request->description,
                'category' => 'member_identification',
                'record_id' => $identification->id,
            ]);
            $identification->file_id = $file->id;
            $identification->save();
        }
        return redirect()->route('members.identifications.index', $identification->member_id)->with('success', 'Successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     * @param ClientIdentification $identification
     * @return RedirectResponse
     */
    public function destroy(ClientIdentification $identification)
    {

        $identification->delete();
        return redirect()->route('members.identifications.index', $identification->member_id)->with('success', 'Successfully deleted.');
    }
}
