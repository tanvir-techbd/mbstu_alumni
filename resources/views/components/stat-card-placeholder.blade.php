@props(['label', 'milestone'])

<div class="rounded-xl border border-dashed border-gray-300 dark:border-gray-700 p-5 text-gray-400">
    <p class="text-sm">{{ $label }}</p>
    <p class="mt-2 text-2xl font-semibold">—</p>
    <p class="mt-1 text-xs">Available after {{ $milestone }}</p>
</div>
