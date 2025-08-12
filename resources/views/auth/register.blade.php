<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="text-center text-gray-600 mb-6">
                <h2 class="text-2xl font-bold">Buat Akun Perusahaan Anda</h2>
                <p class="text-sm">Daftarkan perusahaan dan buat akun akses utama (owner).</p>
            </div>

            <!-- Bagian Informasi Perusahaan -->
            <fieldset class="border rounded-md p-4 mb-6">
                <legend class="text-sm font-medium text-gray-700 px-2">Informasi Perusahaan</legend>

                <div class="mt-2">
                    <x-label for="company_name" value="{{ __('Nama Perusahaan') }}" />
                    <x-input id="company_name" class="block mt-1 w-full" type="text" name="company_name" :value="old('company_name')" required autofocus autocomplete="organization" />
                </div>

                <div class="mt-4">
                    <x-label for="address" value="{{ __('Alamat (Opsional)') }}" />
                    <x-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address')" autocomplete="street-address" />
                </div>

                <div class="mt-4">
                    <x-label for="phone" value="{{ __('Nomor Telepon (Opsional)') }}" />
                    <x-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" autocomplete="tel" />
                </div>
            </fieldset>

            <!-- Bagian Akun Owner -->
            <fieldset class="border rounded-md p-4">
                <legend class="text-sm font-medium text-gray-700 px-2">Akun Akses Owner</legend>

                <div class="mt-2">
                    <x-label for="name" value="{{ __('Nama Lengkap Anda (Owner)') }}" />
                    <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autocomplete="name" />
                </div>

                <div class="mt-4">
                    <x-label for="email" value="{{ __('Email untuk Login') }}" />
                    <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                </div>

                <div class="mt-4">
                    <x-label for="password" value="{{ __('Password') }}" />
                    <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                </div>

                <div class="mt-4">
                    <x-label for="password_confirmation" value="{{ __('Konfirmasi Password') }}" />
                    <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                </div>
            </fieldset>

            <div class="flex items-center justify-end mt-6">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                    {{ __('Sudah punya akun?') }}
                </a>

                <x-button class="ms-4">
                    {{ __('Daftarkan Perusahaan') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
