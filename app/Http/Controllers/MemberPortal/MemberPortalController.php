<?php

namespace App\Http\Controllers\MemberPortal;

use App\Http\Controllers\Controller;
use App\Models\LoanApplication;
use App\Models\MemberUser;
use App\Models\Loan;
use App\Models\Savings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Jenssegers\Agent\Agent;
use Spatie\Permission\Models\Role;

class MemberPortalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
        $totalLoans = Loan::where('member_id', Auth::user()->member->id)->count();
        $totalLoanApplications = LoanApplication::where('member_id', Auth::user()->member->id)->count();
        $totalLoanApplicationsApproved = LoanApplication::where('member_id', Auth::user()->member->id)->where('status', 'approved')->count();
        $totalLoanApplicationsRejected = LoanApplication::where('member_id', Auth::user()->member->id)->where('status', 'rejected')->count();
        $totalLoanApplicationsPending = LoanApplication::where('member_id', Auth::user()->member->id)->where('status', 'pending')->count();
        $totalLoanAmount = Loan::where('member_id', Auth::user()->member->id)->sum('total_disbursed_derived');
        $totalLoanRepaid = Loan::where('member_id', Auth::user()->member->id)->sum('total_repaid_derived');
        $totalLoanBalance = Loan::where('member_id', Auth::user()->member->id)->sum('total_outstanding_derived');


        return Inertia::render('MemberPortal/Dashboard', [
            'totalLoans' => number_format($totalLoans),
            'totalLoanApplications' => number_format($totalLoanApplications),
            'totalLoanApplicationsApproved' => number_format($totalLoanApplicationsApproved),
            'totalLoanApplicationsRejected' => number_format($totalLoanApplicationsRejected),
            'totalLoanApplicationsPending' => number_format($totalLoanApplicationsPending),
            'totalLoanAmount' => number_format($totalLoanAmount),
            'totalLoanRepaid' => number_format($totalLoanRepaid),
            'totalLoanBalance' => number_format($totalLoanBalance),
        ]);
    }

    public function index()
    {
        return Inertia::render('MemberPortal/Member/Show', [
            'member' =>Auth::user()->member,
        ]);
    }

    public function profile(Request $request)
    {
        return Inertia::render('MemberPortal/Profile/Show', [
            'sessions' => $this->sessions($request)->all(),
        ]);
    }

    public function linkedAccounts()
    {
        $accounts = MemberUser::with('member')
            ->where('user_id', Auth::id())
            ->get();
        return Inertia::render('MemberPortal/User/LinkedAccounts', [
            'accounts' => $accounts,
            'selectMemberID' => Auth::user()->member->id,
        ]);
    }

    public function selectLinkedAccount(Request $request)
    {
        session(['member_id' => $request->member_id]);
        return redirect()->back()->with('success', 'Member selected successfully');

    }

    /**
     * Get the current sessions.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Support\Collection
     */
    public function sessions(Request $request)
    {
        if (config('session.driver') !== 'database') {
            return collect();
        }

        return collect(
            DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
                ->where('user_id', $request->user()->getAuthIdentifier())
                ->orderBy('last_activity', 'desc')
                ->get()
        )->map(function ($session) use ($request) {
            $agent = $this->createAgent($session);

            return (object)[
                'agent' => [
                    'is_desktop' => $agent->isDesktop(),
                    'platform' => $agent->platform(),
                    'browser' => $agent->browser(),
                ],
                'ip_address' => $session->ip_address,
                'is_current_device' => $session->id === $request->session()->getId(),
                'last_active' => \Illuminate\Support\Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
            ];
        });
    }

    /**
     * Create a new agent instance from the given session.
     *
     * @param mixed $session
     * @return \Jenssegers\Agent\Agent
     */
    protected function createAgent($session)
    {
        return tap(new Agent, function ($agent) use ($session) {
            $agent->setUserAgent($session->user_agent);
        });
    }

    public function create()
    {

        return Inertia::render('Users/Create', [
            'roles' => Role::all()->transform(function ($role) {
                return [
                    'value' => $role->id,
                    'label' => $role->display_name,
                ];
            }),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'gender' => ['required'],
            'active' => ['required'],
            'roles' => ['required'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed'],
            'photo' => ['nullable', 'image', 'max:1024'],
        ]);
        $user = User::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'country_id' => $request->country_id,
            'title_id' => $request->title_id,
            'gender' => $request->gender,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'tel' => $request->tel,
            'zip' => $request->zip,
            'external_id' => $request->external_id,
            'practice_number' => $request->practice_number,
            'address' => $request->address,
            'active' => $request->active,
            'password' => Hash::make($request->password),
        ]);
        foreach ($request->roles as $key) {
            $user->assignRole(Role::findById($key));
        }
        if ($request->file('photo')) {
            $user->updateProfilePhoto($request->file('photo'));
        }
        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $user->selected_roles = $user->roles->map(function ($role) {
            return $role->id;
        });
        return Inertia::render('Users/Edit', [
            'user' => $user,
            'roles' => Role::all()->transform(function ($role) {
                return [
                    'value' => $role->id,
                    'label' => $role->display_name,
                ];
            }),
        ]);
    }

    public function update(Request $request, User $user)
    {
        if (App::environment('demo') && $user->isDemoUser()) {
            return redirect()->back()->with('error', 'Updating the demo user is not allowed.');
        }
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'gender' => ['required'],
            'roles' => ['required'],
            'active' => ['required'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'confirmed'],
            'photo' => ['nullable', 'image', 'max:1024'],
        ]);
        $user->update([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'country_id' => $request->country_id,
            'title_id' => $request->title_id,
            'gender' => $request->gender,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'tel' => $request->tel,
            'zip' => $request->zip,
            'external_id' => $request->external_id,
            'practice_number' => $request->practice_number,
            'address' => $request->address,
            'active' => $request->active,
        ]);
        if ($request->password) {
            $user->update(['password' => Hash::make($request->password)]);
        }
        if ($request->file('photo')) {
            $user->updateProfilePhoto($request->file('photo'));
        }
        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if (App::environment('demo') && $user->isDemoUser()) {
            return redirect()->back()->with('error', 'Deleting the demo user is not allowed.');
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    public function search(Request $request)
    {
        $search = $request->s;
        $type = $request->type ?: 'doctor';
        $id = $request->id;
        $data = User::where(function ($query) use ($search) {
            $query->where('first_name', 'like', "%$search%");
            $query->orWhere('last_name', 'like', "%$search%");
            $query->orWhere('middle_name', 'like', "%$search%");
            $query->orWhere('id', 'like', "%$search%");
            $query->orWhere('email', 'like', "%$search%");
            $query->orWhere('external_id', 'like', "%$search%");
            $query->orWhere('practice_number', 'like', "%$search%");
        })->when($type, function ($query) use ($type) {
            return $query->whereHas('roles', function ($query) use ($type) {
                $query->where('name', $type);
            });
        })->when($id, function ($query) use ($id) {
            return $query->where('id', $id);
        })->get();
        return response()->json($data);
    }
}
