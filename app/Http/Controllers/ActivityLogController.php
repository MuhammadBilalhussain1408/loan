<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;


class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['permission:activity_logs.index'])->only(['index', 'show', 'get_activity_logs']);
        $this->middleware(['permission:activity_logs.destroy'])->only(['destroy']);

    }

    /**
     * Display a listing of the resource.
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->per_page ?: 20;
        $orderBy = $request->order_by ?: 'created_at';
        $orderByDir = $request->order_by_dir ?: 'desc';
        $causer_id = $request->causer_id;
        $search = $request->s;
        $results = Activity::with(['causer'])
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('activity_log.id', 'like', "%$search%");
                $query->orWhere('activity_log.description', 'like', "%$search%");
                $query->orWhere('activity_log.causer_id', 'like', "%$search%");
                $query->orWhereHas('causer', function ($query) use ($search) {
                    $query->orWhere('users.first_name', 'like', "%$search%");
                    $query->orWhere('users.last_name', 'like', "%$search%");
                });

            })
            ->when($causer_id, function ($query) use ($causer_id) {
                $query->where('activity_log.causer_id', $causer_id);
            })
            ->paginate($perPage)
            ->appends($request->input());
        return Inertia::render('ActivityLogs/Index', [
            'results' => $results,
            'filters' => \request()->all('search', 'status', 'user_id'),
        ]);
    }


    public function show(Activity $activity)
    {
        $activity->load(['causer']);
        return Inertia::render('ActivityLogs/Show', [
            'activity' => $activity
        ]);
    }

}
