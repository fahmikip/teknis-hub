<x-guest-layout :title="'Daftar'">

    <div class="space-y-6">
        <div class="space-y-1.5">
            <h1 class="text-xl font-semibold text-ink">Buat Akun</h1>
            <p class="text-sm text-ink-muted">Daftar untuk mendapatkan akses sistem.</p>
        </div>

        <div class="card card-body">
            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <div>
                    <x-input-label for="name" :value="__('Nama')" />
                    <x-text-input id="name" class="block mt-1.5 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Nama lengkap" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1.5 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="nama@contoh.com" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" class="block mt-1.5 w-full" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
                    <x-text-input id="password_confirmation" class="block mt-1.5 w-full" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div>
                    <x-primary-button class="w-full">
                        Daftar
                    </x-primary-button>
                </div>
            </form>
        </div>

        <p class="text-center text-sm text-ink-muted">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-medium text-brand hover:text-brand-dark">Masuk</a>
        </p>
    </div>

</x-guest-layout>
