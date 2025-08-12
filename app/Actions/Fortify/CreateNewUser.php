<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Company; // Tambahkan ini
use Illuminate\Support\Facades\DB; // Tambahkan ini
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str; // Tambahkan ini
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        // Tambahkan validasi untuk field perusahaan
        Validator::make($input, [
            'company_name' => ['required', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        // Gunakan transaksi untuk memastikan kedua data (company & user) berhasil dibuat
        return DB::transaction(function () use ($input) {
            // 1. Buat perusahaan terlebih dahulu
            $company = Company::create([
                'name' => $input['company_name'],
                'slug' => Str::slug($input['company_name']) . '-' . substr(uniqid(), -6),
                'address' => $input['address'],
                'phone' => $input['phone'],
            ]);

            // 2. Buat user dan hubungkan dengan company_id
            return User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
                'company_id' => $company->id, // Hubungkan user dengan perusahaan
                'role' => 'owner', // Jadikan sebagai owner
            ]);
        });
    }
}
