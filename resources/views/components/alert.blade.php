@props(['type' => 'info', 'message' => ''])

@php
    $styles = [
        'success' => 'bg-green-50 border-green-200 text-success',
        'danger' => 'bg-red-50 border-red-200 text-danger',
        'warning' => 'bg-amber-50 border-amber-200 text-warning',
        'info' => 'bg-blue-50 border-blue-200 text-blue-800',
    ][$type];
    $icon = ['success' => 'check', 'danger' => 'alert', 'warning' => 'alert', 'info' => 'info'][$type];
@endphp

<div {{ $attributes->merge(['class' => 'rounded-md border px-4 py-3 text-sm flex items-start gap-3 ' . $styles]) }} role="alert">
    <span class="mt-0.5 shrink-0">
        <x-icon name="{{ $icon }}" size="18" />
    </span>
    <div class="flex-1">{{ $message ?: $slot }}</div>
    @if ($attributes->has('dismissable'))
        <button type="button" onclick="this.parentElement.remove()" class="shrink-0 opacity-60 hover:opacity-100" aria-label="Tutup">
            <x-icon name="x" size="16" />
        </button>
    @endif
</div>
