@php
    $user = auth()->user();
@endphp

<nav class="p-3 space-y-6 overflow-y-auto h-[calc(100vh-4rem)]">
    <div>
        <p class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Overview</p>
        <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            <x-slot name="icon">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
            </x-slot>
            Dashboard
        </x-sidebar-link>
    </div>

    @role('super-admin')
        <div>
            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Management</p>
            <x-sidebar-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">Users</x-sidebar-link>
            <x-sidebar-link href="#" soon>Alumni Verification</x-sidebar-link>
            <x-sidebar-link href="#" soon>Alumni Directory</x-sidebar-link>
        </div>

        <div>
            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Content</p>
            <x-sidebar-link href="#" soon>Events</x-sidebar-link>
            <x-sidebar-link href="#" soon>Jobs</x-sidebar-link>
            <x-sidebar-link href="#" soon>Notice Board</x-sidebar-link>
            <x-sidebar-link href="#" soon>Success Stories</x-sidebar-link>
            <x-sidebar-link href="#" soon>Gallery</x-sidebar-link>
            <x-sidebar-link href="#" soon>Documents</x-sidebar-link>
        </div>

        <div>
            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Finance</p>
            <x-sidebar-link href="#" soon>Donations</x-sidebar-link>
            <x-sidebar-link href="#" soon>Reports</x-sidebar-link>
        </div>
    @endrole

    @role('alumni')
        <div>
            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Alumni</p>
            <x-sidebar-link href="#" soon>Alumni Directory</x-sidebar-link>
            <x-sidebar-link href="#" soon>Post a Job</x-sidebar-link>
            <x-sidebar-link href="#" soon>Mentorship Requests</x-sidebar-link>
            <x-sidebar-link href="#" soon>Submit Success Story</x-sidebar-link>
        </div>

        <div>
            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Community</p>
            <x-sidebar-link href="#" soon>Events</x-sidebar-link>
            <x-sidebar-link href="#" soon>Donations</x-sidebar-link>
            <x-sidebar-link href="#" soon>Notice Board</x-sidebar-link>
            <x-sidebar-link href="#" soon>Gallery</x-sidebar-link>
            <x-sidebar-link href="#" soon>Documents</x-sidebar-link>
        </div>
    @endrole

    @role('student')
        <div>
            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Student</p>
            <x-sidebar-link href="#" soon>Job Board</x-sidebar-link>
            <x-sidebar-link href="#" soon>Find a Mentor</x-sidebar-link>
            <x-sidebar-link href="#" soon>Events</x-sidebar-link>
        </div>

        <div>
            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Resources</p>
            <x-sidebar-link href="#" soon>Alumni Directory</x-sidebar-link>
            <x-sidebar-link href="#" soon>Notice Board</x-sidebar-link>
            <x-sidebar-link href="#" soon>Gallery</x-sidebar-link>
            <x-sidebar-link href="#" soon>Documents</x-sidebar-link>
        </div>
    @endrole

    @role('faculty')
        <div>
            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Faculty</p>
            <x-sidebar-link href="#" soon>Events</x-sidebar-link>
            <x-sidebar-link href="#" soon>Notice Board</x-sidebar-link>
        </div>

        <div>
            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Resources</p>
            <x-sidebar-link href="#" soon>Alumni Directory</x-sidebar-link>
            <x-sidebar-link href="#" soon>Gallery</x-sidebar-link>
            <x-sidebar-link href="#" soon>Documents</x-sidebar-link>
        </div>
    @endrole

    <div>
        <p class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Account</p>
        <x-sidebar-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">Settings</x-sidebar-link>
        <x-sidebar-link href="#" soon>Feedback</x-sidebar-link>
    </div>
</nav>
