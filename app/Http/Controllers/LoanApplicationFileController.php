<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\FileType;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\PaymentType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class LoanApplicationFileController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:loans.files.index'])->only(['index', 'show']);
        $this->middleware(['permission:loans.files.create'])->only(['create', 'store']);
        $this->middleware(['permission:loans.files.update'])->only(['edit', 'update']);
        $this->middleware(['permission:loans.files.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return \Inertia\Response
     */
    public function index(LoanApplication $application)
    {
        $application->load(['member','loan', 'product', 'product.charges', 'product.charges.charge', 'product.charges.charge.option', 'product.charges.charge.type']);
        $files = File::with(['type'])
            ->where('record_id', $application->id)
            ->where('category', 'loan_applications')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return Inertia::render('LoanApplications/Files/Index', [
            'application' => $application,
            'files' => $files,
            'paymentTypes' => PaymentType::where('active', 1)->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create(LoanApplication $application)
    {
        $application->load(['member', 'loan','product', 'product.charges', 'product.charges.charge', 'product.charges.charge.option', 'product.charges.charge.type']);

        return Inertia::render('LoanApplications/Files/Create', [
            'application' => $application,
            'types' => FileType::get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request, LoanApplication $application)
    {
        $application->load(['member', 'product', 'product.charges', 'product.charges.charge', 'product.charges.charge.option', 'product.charges.charge.type']);

        $fileController = new FilesController();
        $fileController->store([
            'file' => $request->file('file'),
            'file_type_id' => $request->file_type_id,
            'name' => $request->name,
            'description' => $request->description,
            'category' => 'loan_applications',
            'record_id' => $application->id,
        ]);

        return redirect()->route('loans.applications.files.index', $application->id)->with('success', 'Loan File created successfully.');

    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param File $file
     * @return \Inertia\Response
     */
    public function edit(File $file)
    {
        $application = LoanApplication::find($file->record_id);
        $application->load(['member','loan', 'product', 'product.charges', 'product.charges.charge', 'product.charges.charge.option', 'product.charges.charge.type']);

        return Inertia::render('LoanApplications/Files/Edit', [
            'application' => $application,
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


        $fileController = new FilesController();
        $fileController->update($file->id, [
            'file' => $request->file('file'),
            'file_type_id' => $request->file_type_id,
            'name' => $request->name,
            'description' => $request->description,
        ]);
        return redirect()->route('loans.applications.files.index', $file->record_id)->with('success', 'Updated File successfully.');

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
        return redirect()->route('loans.applications.files.index', $file->record_id)->with('success', 'File deleted successfully.');

    }
}
