@props(['textColor' => 'text-white', 'markRed' => true])

<a href="{{ route('dashboard') }}" class="flex items-center gap-3">
    <span class="inline-flex items-center justify-center h-9 w-9 rounded-md bg-brand text-white shrink-0" aria-hidden="true">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 3l9 5-9 5-9-5 9-5z"></path>
            <path d="M3 13l9 5 9-5"></path>
            <line x1="12" y1="18" x2="12" y2="21"></line>
        </svg>
    </span>
    <span class="leading-tight">
        <span class="{{ $textColor }} block text-[15px] font-semibold tracking-tight">TeknisHub</span>
        <span class="block text-2xs uppercase tracking-wider text-white/60">Divisi Teknis</span>
    </span>
</a>
