<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' — ' . config('app.name') : config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="h-full">
    <div
        x-data="{ sidebarOpen: false, sidebarCollapsed: localStorage.getItem('teknishub.sidebar') === '1' }"
        class="h-full flex"
        @toggle-sidebar.window="sidebarOpen = !sidebarOpen"
    >

        {{-- Overlay mobile --}}
        <div
            x-show="sidebarOpen"
            x-cloak
            x-transition.opacity
            class="fixed inset-0 z-40 bg-black/40 lg:hidden"
            @click="sidebarOpen = false"
        ></div>

        {{-- Sidebar --}}
        <aside
            class="fixed inset-y-0 left-0 z-50 w-64 bg-ink text-white flex flex-col
                   transform transition-all duration-200 ease-out
                   lg:translate-x-0 lg:static lg:z-30 lg:shrink-0"
            :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', sidebarCollapsed ? 'lg:w-20' : 'lg:w-64']"
            x-cloak
            aria-label="Navigasi samping"
        >
            <div class="h-16 flex items-center justify-between px-4 border-b border-white/10 shrink-0 transition-all duration-200"
                 :class="sidebarCollapsed ? 'lg:justify-center lg:px-2' : ''">
                <span class="flex items-center" :class="sidebarCollapsed ? 'lg:hidden' : ''">
                    <x-application-logo markRed="true" />
                </span>

                <div class="flex items-center gap-1">
                    <button
                        type="button"
                        class="hidden lg:inline-flex items-center justify-center h-9 w-9 rounded-md text-white/70 hover:bg-white/10 transition-colors"
                        @click="sidebarCollapsed = !sidebarCollapsed; localStorage.setItem('teknishub.sidebar', sidebarCollapsed ? '1' : '0')"
                        :title="sidebarCollapsed ? 'Perluas navigasi' : 'Ciutkan navigasi'"
                        aria-label="Ciutkan atau perluas navigasi"
                    >
                        <x-icon name="chevrons-left" size="18" x-show="!sidebarCollapsed" x-cloak />
                        <x-icon name="chevrons-right" size="18" x-show="sidebarCollapsed" x-cloak />
                    </button>
                    <button
                        type="button"
                        class="lg:hidden inline-flex items-center justify-center h-9 w-9 rounded-md text-white/70 hover:bg-white/10 transition-colors"
                        @click="sidebarOpen = false"
                        aria-label="Tutup navigasi"
                    >
                        <x-icon name="x" size="20" />
                    </button>
                </div>
            </div>

            <x-sidebar />
        </aside>

        {{-- Panel utama --}}
        <div class="flex-1 min-w-0 flex flex-col h-full">
            <x-topbar>
                @isset($breadcrumb)
                    {{ $breadcrumb }}
                @endisset
            </x-topbar>

            {{-- Flash --}}
            @if (session('success') || session('error') || session('warning') || session('info'))
                <div class="fixed top-4 right-4 z-[60] w-full max-w-sm space-y-3 px-4 sm:px-0">
                    @if (session('success'))
                        <x-alert type="success" message="{{ session('success') }}" dismissable />
                    @endif
                    @if (session('error'))
                        <x-alert type="danger" message="{{ session('error') }}" dismissable />
                    @endif
                    @if (session('warning'))
                        <x-alert type="warning" message="{{ session('warning') }}" dismissable />
                    @endif
                    @if (session('info'))
                        <x-alert type="info" message="{{ session('info') }}" dismissable />
                    @endif
                </div>
            @endif

            <main id="content" class="flex-1 px-4 sm:px-6 py-6 overflow-y-auto">
                {{ $slot }}
            </main>

            <footer class="border-t border-line px-4 sm:px-6 py-3 text-2xs text-ink-muted flex items-center justify-between gap-2">
                <span>&copy; {{ date('Y') }} TeknisHub — Sistem Manajemen Dokumen &amp; Informasi Divisi Teknis</span>
                <a href="https://github.com/fahmikip" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 hover:text-ink hover:underline">
                    <x-icon name="github" size="14" />
                    github.com/fahmikip
                </a>
            </footer>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
