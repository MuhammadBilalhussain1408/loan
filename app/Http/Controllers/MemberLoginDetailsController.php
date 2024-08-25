<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\User;
use App\Notifications\SendLoginDetails;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class MemberLoginDetailsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:members.users.create'])->only(['index', 'show']);
        $this->middleware(['permission:members.users.create'])->only(['create', 'store']);
        $this->middleware(['permission:members.users.destroy'])->only(['destroy']);
    }

    public function index(Member $member)
    {
        $member->load(['user']);
        return Inertia::render('Members/LoginDetails/Index', [
            'member' => $member,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create(Member $member)
    {
        return Inertia::render('Members/LoginDetails/Create', [
            'member' => $member,

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

        $request->validate([
            'existing_user' => ['required'],
            'user_id' => ['required_if:existing_user,true'],
            'first_name' => ['required_if:existing_user,false', 'string', 'max:255'],
            'last_name' => ['required_if:existing_user,false', 'string', 'max:255'],
            'email' => ['required_if:existing_user,false', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required_if:existing_user,false', 'confirmed'],
        ]);
        if ($request->existing_user) {
            $member->user_id = $request->user_id;
            $member->save();
        } else {
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            $user->assignRole(Role::findByName('member'));
            $member->user_id = $user->id;
            $member->save();
            if ($request->send_notification) {
                $member->user->notify(new SendLoginDetails($request->password));
            }
        }
        return redirect()->route('members.login_details.index', $member->id)->with('success', 'Member user created successfully.');

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Member $member
     * @return RedirectResponse
     */
    public function destroy(Member $member)
    {

        $member->user_id=null;
        $member->save();
        return redirect()->route('members.login_details.index', $member->id)->with('success', 'Deleted successfully.');

    }
}
