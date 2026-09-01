<x-guest-layout :title="'Masuk'">

    <div class="space-y-6">
        <div class="space-y-1.5">
            <h1 class="text-xl font-semibold text-ink">Masuk</h1>
            <p class="text-sm text-ink-muted">Gunakan akun Anda untuk mengakses sistem.</p>
        </div>

        <div class="card card-body">
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1.5 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@contoh.com" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" class="block mt-1.5 w-full" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded border-line text-brand shadow-sm focus:ring-brand" name="remember">
                        <span class="ms-2 text-sm text-ink-muted">Ingat saya</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-sm text-ink-muted hover:text-brand rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand" href="{{ route('password.request') }}">
                            Lupa password?
                        </a>
                    @endif
                </div>

                <div>
                    <x-primary-button class="w-full">
                        Masuk
                    </x-primary-button>
                </div>
            </form>
        </div>

        @if (Route::has('register'))
            <p class="text-center text-sm text-ink-muted">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-medium text-brand hover:text-brand-dark">Daftar</a>
            </p>
        @endif
    </div>

</x-guest-layout>
