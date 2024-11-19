<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanApplicationDeclaration;
use App\Models\LoanNote;
use App\Models\Note;
use App\Models\PaymentType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class LoanApplicationNoteController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:loans.notes.index'])->only(['index', 'show']);
        $this->middleware(['permission:loans.notes.create'])->only(['create', 'store']);
        $this->middleware(['permission:loans.notes.update'])->only(['edit', 'update']);
        $this->middleware(['permission:loans.notes.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return \Inertia\Response
     */
    public function index(LoanApplication $application)
    {
        $application->load(['member', 'loan','product', 'product.charges', 'product.charges.charge', 'product.charges.charge.option', 'product.charges.charge.type']);
        $results = Note::with(['createdBy'])
            ->where('record_id', $application->id)
            ->where('category', 'loan_application')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return Inertia::render('LoanApplications/Notes/Index', [
            'application' => $application,
            'results' => $results,
            'paymentTypes' => PaymentType::where('active', 1)->get(),
        ]);
    }
    public function declarations(LoanApplication $application)
    {
        $application->load(['member','declarations','loan','product', 'product.charges', 'product.charges.charge', 'product.charges.charge.option', 'product.charges.charge.type']);
        $results = LoanApplicationDeclaration::where('loan_id', $application->id)
            // ->where('category', 'loan_application')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return Inertia::render('LoanApplications/Declarations/Index', [
            'application' => $application,
            'results' => $results,
            'paymentTypes' => PaymentType::where('active', 1)->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return \Inertia\Response
     */
    public function create(LoanApplication $application)
    {
        $application->load(['member','loan', 'product', 'product.charges', 'product.charges.charge', 'product.charges.charge.option', 'product.charges.charge.type']);

        return Inertia::render('LoanApplications/Notes/Create', [
            'application' => $application,
            'paymentTypes' => PaymentType::where('active', 1)->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request, LoanApplication $application)
    {
        $request->validate([
            'description' => ['required'],
        ]);
        $note = new Note();
        $note->created_by_id = Auth::id();
        $note->record_id = $application->id;
        $note->category = 'loan_application';
        $note->description = $request->description;
        $note->save();
        return redirect()->route('loans.applications.notes.index', $application->id)->with('success', 'Successfully created.');
    }


    /**
     * Show the form for editing the specified resource.
     * @param Note $note
     * @return \Inertia\Response
     */
    public function edit(Note $note)
    {
        $application = LoanApplication::find($note->record_id);
        $application->load(['member', 'loan','product', 'product.charges', 'product.charges.charge', 'product.charges.charge.option', 'product.charges.charge.type']);

        return Inertia::render('LoanApplications/Notes/Edit', [
            'note' => $note,
            'application' => $application,
            'paymentTypes' => PaymentType::where('active', 1)->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param Note $note
     * @return RedirectResponse
     */
    public function update(Request $request, Note $note)
    {
        $request->validate([
            'description' => ['required'],
        ]);
        $note->description = $request->description;
        $note->save();
        return redirect()->route('loans.applications.notes.index', $note->record_id)->with('success', 'Successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     * @param Note $note
     * @return RedirectResponse
     */
    public function destroy(Note $note)
    {
        $note->delete();
        return redirect()->route('loans.applications.notes.index', $note->record_id)->with('success', 'Successfully deleted');
    }
}
