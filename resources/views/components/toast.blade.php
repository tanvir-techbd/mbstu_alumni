@if (session('success') || session('error'))
    <div
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, 4000)"
        x-show="show"
        x-transition
        class="fixed bottom-4 right-4 z-50 max-w-sm rounded-lg shadow-lg ring-1 ring-black/5 px-4 py-3 text-sm font-medium
            {{ session('success') ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white' }}"
    >
        <div class="flex items-start gap-2">
            <span class="flex-1">{{ session('success') ?? session('error') }}</span>
            <button @click="show = false" class="opacity-75 hover:opacity-100" aria-label="Dismiss">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
@endif
