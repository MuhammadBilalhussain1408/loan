<?php

namespace App\Http\Controllers;

use App\Models\FileType;
use App\Models\Member;
use App\Models\File;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class MemberFileController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:members.files.index'])->only(['index', 'show']);
        $this->middleware(['permission:members.files.create'])->only(['create', 'store']);
        $this->middleware(['permission:members.files.update'])->only(['edit', 'update']);
        $this->middleware(['permission:members.files.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return \Inertia\Response
     */
    public function index(Member $member)
    {

        $files = File::with(['type'])
            ->where('record_id', $member->id)
            ->where('category', 'members')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return Inertia::render('Members/Files/Index', [
            'member' => $member,
            'files' => $files,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create(Member $member)
    {
        return Inertia::render('Members/Files/Create', [
            'member' => $member,
            'types' => FileType::get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request, Member $member)
    {

        $fileController = new FilesController();
        $file = $fileController->store([
            'file' => $request->file('file'),
            'name' => $request->name,
            'file_type_id' => $request->file_type_id,
            'description' => $request->description,
            'category' => 'members',
            'record_id' => $member->id,
        ]);

        return redirect()->route('members.files.index', $member->id)->with('success', 'Member File created successfully.');

    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param File $file
     * @return \Inertia\Response
     */
    public function edit(File $file)
    {
        $member = Member::find($file->record_id);

        return Inertia::render('Members/Files/Edit', [
            'member' => $member,
            'file' => $file,
            'types' => FileType::get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param File $file
     * @return RedirectResponse
     */
    public function update(Request $request, File $file)
    {

        $member = Member::find($file->record_id);
        $fileController = new FilesController();
        $file = $fileController->update($file->id, [
            'file' => $request->file('file'),
            'file_type_id' => $request->file_type_id,
            'name' => $request->name,
            'description' => $request->description,
        ]);
        return redirect()->route('members.files.index', $member->id)->with('success', 'Updated File successfully.');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param File $file
     * @return RedirectResponse
     */
    public function destroy(File $file)
    {

        $fileController = new FilesController();
        $fileController->destroy($file->id);
        return redirect()->route('members.files.index', $file->record_id)->with('success', 'File deleted successfully.');

    }
}
