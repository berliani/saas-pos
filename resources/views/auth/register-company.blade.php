<x-guest-layout>
    <x-jet-authentication-card>
        <x-slot name="logo">
            <a href="/">
                <svg class="w-16 h-16" viewbox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M11.395 44.428C4.557 40.198 0 32.632 0 24 0 10.745 10.745 0 24 0s24 10.745 24 24c0 8.632-4.557 16.198-11.395 20.428L24 28.172l-12.605 16.256z" fill="#FF2D20"></path>
                    <path d="M24 28.172L35.395 44.428C42.233 40.198 48 32.632 48 24c0-13.255-10.745-24-24-24S0 10.745 0 24c0 8.632 4.557 16.198 11.395 20.428L24 28.172z" fill="#F05340"></path>
                </svg>
            </a>
        </x-slot>

        <x-jet-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('company.register.store') }}">
            @csrf

            <div class="text-center text-gray-600 mb-6">
                <h2 class="text-2xl font-bold">Buat Akun Perusahaan Anda</h2>
                <p class="text-sm">Daftarkan perusahaan dan buat akun akses utama (owner).</p>
            </div>

            <!-- Bagian Informasi Perusahaan -->
            <fieldset class="border rounded-md p-4 mb-6">
                <legend class="text-sm font-medium text-gray-700 px-2">Informasi Perusahaan</legend>

                <div class="mt-2">
                    <x-jet-label for="company_name" value="{{ __('Nama Perusahaan') }}" />
                    <x-jet-input id="company_name" class="block mt-1 w-full" type="text" name="company_name" :value="old('company_name')" required autofocus autocomplete="organization" />
                </div>

                <div class="mt-4">
                    <x-jet-label for="address" value="{{ __('Alamat (Opsional)') }}" />
                    <x-jet-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address')" autocomplete="street-address" />
                </div>

                <div class="mt-4">
                    <x-jet-label for="phone" value="{{ __('Nomor Telepon (Opsional)') }}" />
                    <x-jet-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" autocomplete="tel" />
                </div>
            </fieldset>

            <!-- Bagian Akun Owner -->
            <fieldset class="border rounded-md p-4">
                <legend class="text-sm font-medium text-gray-700 px-2">Akun Akses Owner</legend>

                <div class="mt-2">
                    <x-jet-label for="name" value="{{ __('Nama Lengkap Anda (Owner)') }}" />
                    <x-jet-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autocomplete="name" />
                </div>

                <div class="mt-4">
                    <x-jet-label for="email" value="{{ __('Email untuk Login') }}" />
                    <x-jet-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                </div>

                <div class="mt-4">
                    <x-jet-label for="password" value="{{ __('Password') }}" />
                    <x-jet-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                </div>

                <div class="mt-4">
                    <x-jet-label for="password_confirmation" value="{{ __('Konfirmasi Password') }}" />
                    <x-jet-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                </div>
            </fieldset>


            <div class="flex items-center justify-end mt-6">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                    {{ __('Sudah punya akun?') }}
                </a>

                <x-jet-button class="ml-4">
                    {{ __('Daftarkan Perusahaan') }}
                </x-jet-button>
            </div>
        </form>
    </x-jet-authentication-card>
</x-guest-layout>
