<?php

namespace App\Actions\Fortify;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     */
    public function update($user, array $input): void
    {
        Validator::make($input, [
            'name' => [
                'required',
                'string',
                'max:25',
                'regex:/^[A-Za-zÀ-ÿÑñ\s\'\-]+$/', // letters, spaces, hyphens, apostrophes
            ],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['required', 'regex:/^(09)\d{9}$/', 'unique:users,phone,' . $user->id],
        ])->validateWithBag('updateProfileInformation'); 

        if ($input['email'] !== $user->email && $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'name' => $input['name'],
                'email' => $input['email'],
                'phone' => $input['phone'],
            ])->save();
        }
    }

    /**
     * Update verified user's profile info.
     */
    protected function updateVerifiedUser($user, array $input): void
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'phone' => $input['phone'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
