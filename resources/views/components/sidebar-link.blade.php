@props(['href', 'active' => false, 'soon' => false])

<a
    href="{{ $href }}"
    {{ $attributes->class([
        'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition',
        'bg-primary-50 text-primary-700 dark:bg-primary-500/10 dark:text-primary-400' => $active,
        'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' => ! $active,
    ]) }}
>
    @isset($icon)
        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            {{ $icon }}
        </svg>
    @endisset

    <span>{{ $slot }}</span>

    @if ($soon)
        <span class="ml-auto text-[10px] uppercase tracking-wide bg-gray-100 text-gray-400 dark:bg-gray-800 dark:text-gray-500 rounded-full px-2 py-0.5">Soon</span>
    @endif
</a>
