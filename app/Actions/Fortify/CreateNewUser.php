<?php

namespace App\Actions\Fortify;

use App\Models\Patient;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param array $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        if (Setting::where('setting_key', 'allow_self_registration')->first()->setting_value === 'yes') {


            Validator::make($input, [
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => $this->passwordRules(),
                'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
            ])->validate();
            $user = User::create([
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'],
                'gender' => $input['gender'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]);
            $user->assignRole('patient');
            //create patient record
            $patient = new Patient();
            $patient->first_name = $user->first_name;
            $patient->last_name = $user->last_name;
            $patient->gender = $user->gender;
            $patient->email = $user->email;
            $patient->source = 'online';
            $patient->save();
            return $user;
        }
        abort(422, 'Registration disabled');
    }
}
