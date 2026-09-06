@props(['icon' => 'inbox', 'title' => 'Tidak ada data', 'description' => '', 'action' => null])

<div {{ $attributes->merge(['class' => 'card px-6 py-14 text-center']) }}>
    <div class="mx-auto inline-flex items-center justify-center h-14 w-14 rounded-full bg-app text-ink-light mb-4">
        <x-icon name="{{ $icon }}" size="28" />
    </div>
    <h3 class="text-sm font-semibold text-ink">{{ $title }}</h3>
    @if ($description)
        <p class="mt-1 text-sm text-ink-muted max-w-sm mx-auto">{{ $description }}</p>
    @endif
    @if ($action)
        <div class="mt-5">
            <a href="{{ $action }}" class="btn btn-secondary">
                <x-icon name="plus" size="16" />
                {{ $slot }}
            </a>
        </div>
    @endif
</div>