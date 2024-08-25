<?php

namespace App\Http\Controllers;

use App\Models\CustomField;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class CustomFieldController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:custom_fields.index'])->only(['index', 'show']);
        $this->middleware(['permission:custom_fields.create'])->only(['create', 'store']);
        $this->middleware(['permission:custom_fields.update'])->only(['edit', 'update']);
        $this->middleware(['permission:custom_fields.destroy'])->only(['destroy']);

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
        $results = CustomField::filter(\request()->only('search', 'type'))
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->paginate($perPage)
            ->appends($request->input());
        return Inertia::render('CustomFields/Index', [
            'filters' => \request()->all('search', 'tariff_id', 'type'),
            'results' => $results
        ]);
    }


    /**
     * Show the form for creating a new resource.
     * @return \Inertia\Response
     */
    public function create()
    {

        return Inertia::render('CustomFields/Create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'category' => ['required'],
            'type' => ['required'],
            'active' => ['required'],
        ]);
        $field = new CustomField();
        $field->created_by_id = Auth::id();
        $field->category = $request->category;
        $field->type = $request->type;
        $field->name = $request->name;
        $field->label = $request->label;
        $field->field_position = $request->field_position;
        $field->options = $request->options;
        $field->class = $request->classes;
        $field->db_columns = $request->db_columns;
        $field->rules = $request->rules;
        $field->default_values = $request->default_values;
        $field->required = $request->required ? 1 : 0;
        $field->active = $request->active ? 1 : 0;
        $field->description = $request->description;
        $field->include_in_report = $request->include_in_report ? 1 : 0;
        $field->save();
        return redirect()->route('custom_fields.index')->with('success', 'Successfully created');
    }


    /**
     * Show the form for editing the specified resource.
     * @param CustomField $field
     * @return \Inertia\Response
     */
    public function edit(CustomField $field)
    {

        return Inertia::render('CustomFields/Edit', [
            'field' => $field
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param CustomField $field
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, CustomField $field)
    {
        $request->validate([
            'name' => ['required'],
            'category' => ['required'],
            'type' => ['required'],
            'active' => ['required'],
        ]);
        $field->category = $request->category;
        $field->type = $request->type;
        $field->name = $request->name;
        $field->label = $request->label;
        $field->field_position = $request->field_position;
        $field->options = $request->options;
        $field->class = $request->classes;
        $field->db_columns = $request->db_columns;
        $field->rules = $request->rules;
        $field->default_values = $request->default_values;
        $field->required = $request->required ? 1 : 0;
        $field->active = $request->active ? 1 : 0;
        $field->description = $request->description;
        $field->include_in_report = $request->include_in_report ? 1 : 0;
        $field->save();

        return redirect()->route('custom_fields.index')->with('success', 'Successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     * @param CustomField $field
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(CustomField $field)
    {
        $field->meta()->delete();
        $field->delete();
        return redirect()->back()->with('success', 'Successfully deleted');
    }
}
