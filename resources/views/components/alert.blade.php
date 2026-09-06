@props(['type' => 'info', 'message' => '', 'duration' => 5000])

@php
    $styles = [
        'success' => 'bg-white border-green-200 text-success shadow-card',
        'danger' => 'bg-white border-red-200 text-danger shadow-card',
        'warning' => 'bg-white border-amber-200 text-warning shadow-card',
        'info' => 'bg-white border-blue-200 text-blue-800 shadow-card',
    ][$type];
    $icon = ['success' => 'check', 'danger' => 'alert', 'warning' => 'alert', 'info' => 'info'][$type];
@endphp

<div {{ $attributes->merge(['class' => 'relative rounded-lg border px-4 py-3 text-sm flex items-start gap-3 ' . $styles]) }}
     role="alert"
     x-data="{ show: true, leaving: false }"
     x-init="setTimeout(() => { leaving = true; setTimeout(() => show = false, 200) }, {{ $duration }})"
     x-show="show"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 -translate-y-2">
    <span class="mt-0.5 shrink-0">
        <x-icon name="{{ $icon }}" size="18" />
    </span>
    <div class="flex-1 pr-2">{{ $message ?: $slot }}</div>
    <button
        type="button"
        class="shrink-0 opacity-60 hover:opacity-100 transition-opacity"
        @click="leaving = true; setTimeout(() => show = false, 200)"
        aria-label="Tutup"
    >
        <x-icon name="x" size="16" />
    </button>
</div>