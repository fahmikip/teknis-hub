@props(['crumbs' => []])

<nav aria-label="Breadcrumb" class="min-w-0">
    <ol class="flex items-center gap-1.5 text-sm text-ink-muted">
        @php $last = count($crumbs); $i = 0; @endphp
        @foreach ($crumbs as $label => $url)
            @php $i++; @endphp
            <li class="flex items-center gap-1.5 min-w-0">
                @if ($url)
                    <a href="{{ $url }}" class="hover:text-ink transition-colors truncate">{{ $label }}</a>
                @else
                    <span class="text-ink font-medium truncate" aria-current="page">{{ $label }}</span>
                @endif
                @if ($i < $last)
                    <x-icon name="chevron-right" size="14" class="text-ink-light" />
                @endif
            </li>
        @endforeach
    </ol>
</nav>
