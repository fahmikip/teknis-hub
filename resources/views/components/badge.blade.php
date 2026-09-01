@props(['color' => 'neutral', 'dot' => true])

@php
    $styles = [
        'neutral' => 'bg-gray-100 text-ink',
        'success' => 'bg-green-50 text-success',
        'warning' => 'bg-amber-50 text-warning',
        'danger' => 'bg-red-50 text-danger',
        'brand' => 'bg-red-50 text-brand',
        'gold' => 'bg-amber-100/60 text-amber-800',
        'info' => 'bg-blue-50 text-blue-700',
    ][$color];
    $dot = [
        'neutral' => 'bg-gray-400',
        'success' => 'bg-green-500',
        'warning' => 'bg-amber-500',
        'danger' => 'bg-red-500',
        'brand' => 'bg-brand',
        'gold' => 'bg-gold',
        'info' => 'bg-blue-500',
    ][$color] ?? null;
@endphp

<span {{ $attributes->merge(['class' => 'badge ' . $styles]) }}>
    @if ($dot)
        <span class="w-1.5 h-1.5 rounded-full {{ $dot }}" aria-hidden="true"></span>
    @endif
    {{ $slot }}
</span>
