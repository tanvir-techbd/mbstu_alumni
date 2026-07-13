@props(['items' => []])

<nav {{ $attributes->merge(['class' => 'hidden sm:flex items-center text-sm text-gray-500 dark:text-gray-400']) }}>
    @foreach ($items as $index => $item)
        @if ($index > 0)
            <svg class="h-4 w-4 mx-1 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
        @endif

        @if (! $loop->last && isset($item['url']))
            <a href="{{ $item['url'] }}" class="hover:text-gray-700 dark:hover:text-gray-200">{{ $item['label'] }}</a>
        @else
            <span class="{{ $loop->last ? 'text-gray-900 dark:text-gray-100 font-medium' : '' }}">{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>
