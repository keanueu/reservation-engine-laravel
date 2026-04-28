<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => [
                'required',
                'string',
                'max:25',
                'regex:/^[A-Za-zÀ-ÿÑñ\s\'\-]+$/', // letters, spaces, accents, apostrophes, hyphens
            ],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'regex:/^(09)\d{9}$/', 'unique:users,phone'], // PH 11-digit format starting with 09
            'password' => [
                'required',
                'string',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
                'confirmed',
            ],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature()
                ? ['accepted', 'required']
                : [],
        ])->validate();

        // Create and hash password
        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'phone' => $input['phone'],
            'password' => Hash::make($input['password']),
            'is_verified' => false,
        ]);

        // Optional OTP handling
        if (class_exists(\App\Services\RegistrationOtpService::class)) {
            \App\Services\RegistrationOtpService::generate($user);
            session(['registration_otp_user_id' => $user->id]);
        }

        return $user;
    }
}
