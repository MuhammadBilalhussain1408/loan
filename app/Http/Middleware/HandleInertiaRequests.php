<?php

namespace App\Http\Middleware;


use App\Models\Currency;
use App\Models\Setting;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function share(Request $request)
    {
        $logo = null;
        $companyName = null;
        $smallLogo = null;
        $companyAddress = null;
        $companyMobile = null;
        $companyTel = null;
        $companyEmail = null;
        $currency = null;
        try {
            if (Schema::hasTable('settings')) {
                if (Setting::where('setting_key', 'setup_complete')->first()) {
                    $logo = Setting::where('setting_key', 'company_logo')->first()->setting_value ?? null;
                    $companyName = Setting::where('setting_key', 'company_name')->first()->setting_value ?? null;
                    $smallLogo = Setting::where('setting_key', 'company_small_logo')->first()->setting_value ?? null;
                    $companyAddress = Setting::where('setting_key', 'company_address')->first()->setting_value ?? null;
                    $companyMobile = Setting::where('setting_key', 'company_mobile')->first()->setting_value ?? null;
                    $companyTel = Setting::where('setting_key', 'company_tel')->first()->setting_value ?? null;
                    $companyEmail = Setting::where('setting_key', 'company_email')->first()->setting_value ?? null;
                    $currency = Currency::find(Setting::where('setting_key', 'currency')->first()->setting_value) ?? null;
                }
            }
        } catch (Exception $exception) {

        }
        $menu = (Auth::check() && Auth::user()->hasRole('member')) ? config('menu.member') : config('menu.admin');
        return array_merge(parent::share($request), [
            'flash' => function () use ($request) {
                return [
                    'message' => $request->get('message') ?? $request->session()->get('message'),
                    'success' => $request->get('success') ?? $request->session()->get('success'),
                    'error' => $request->get('error') ?? $request->session()->get('error'),
                ];
            },
            'logoUrl' => $logo ? asset('storage/' . $logo) : asset('images/logo.png'),
            'companyName' => $companyName,
            'user' => Auth::user(),
            'menu' => $menu,
            'smallLogoUrl' => $smallLogo ? asset('storage/' . $smallLogo) : null,
            'companyAddress' => $companyAddress,
            'companyMobile' => $companyMobile,
            'companyTel' => $companyTel,
            'companyEmail' => $companyEmail,
            'currency' => $currency,
        ]);
    }
}
