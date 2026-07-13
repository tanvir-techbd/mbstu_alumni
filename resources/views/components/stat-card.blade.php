@props(['label', 'value', 'hint' => null, 'hintTone' => 'neutral'])

@php
    $hintClasses = match ($hintTone) {
        'positive' => 'text-emerald-600 dark:text-emerald-400',
        'warning' => 'text-amber-600 dark:text-amber-400',
        default => 'text-gray-500 dark:text-gray-400',
    };
@endphp

<div class="rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-900/5 dark:ring-white/10 p-5">
    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $label }}</p>
    <p class="mt-2 text-2xl font-semibold">{{ $value }}</p>
    @if ($hint)
        <p class="mt-1 text-xs {{ $hintClasses }}">{{ $hint }}</p>
    @endif
</div>
