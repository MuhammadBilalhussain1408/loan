<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use function Modules\Client\Http\Controllers\theme_view;

class ClientReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:member.members.files.index'])->only(['index', 'show']);
        $this->middleware(['permission:member.members.files.create'])->only(['create', 'store']);
        $this->middleware(['permission:member.members.files.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:member.members.files.destroy'])->only(['destroy']);

    }


    /**
     * Display a listing of the resource.
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Reports/Client/Index');
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

}
