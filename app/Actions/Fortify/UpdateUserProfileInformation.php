<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, mixed>  $input
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
            // Add validation rules for new fields
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'contact' => ['nullable', 'string', 'max:255'],
            'residence' => ['nullable', 'string', 'max:255'],
            'doctor_name' => ['nullable', 'string', 'max:255'],
            'doctor_email' => ['nullable', 'email', 'max:255'],
            'doctor_contact' => ['nullable', 'string', 'max:255'],
            // 'role' => ['nullable', 'string', 'max:255'],
            // Add more validation rules as needed
        ])->validateWithBag('updateProfileInformation');

        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        if ($input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'name' => $input['name'],
                'email' => $input['email'],
                // Update with new fields
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'],
                'contact' => $input['contact'],
                'residence' => $input['residence'],
                'doctor_name' => $input['doctor_name'],
                'doctor_email' => $input['doctor_email'],
                'doctor_contact' => $input['doctor_contact'],
                // 'role' => $input['role'],
            ])->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            // Update with new fields
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'contact' => $input['contact'],
            'residence' => $input['residence'],
            'doctor_name' => $input['doctor_name'],
            'doctor_email' => $input['doctor_email'],
            'doctor_contact' => $input['doctor_contact'],
            // 'role' => $input['role'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
