<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    {{-- Welcome banner --}}
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Welcome back, {{ auth()->user()->name }} 👋</h2>
            <p class="text-sm text-gray-500 mt-1">
                Logged in as
                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold capitalize
                    {{ auth()->user()->hasRole('admin') ? 'bg-red-100 text-red-700' : (auth()->user()->hasRole('manager') ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700') }}">
                    {{ auth()->user()->getRoleNames()->first() ?? '—' }}
                </span>
            </p>
        </div>
        <p class="text-xs text-gray-400">{{ now()->format('l, d M Y') }}</p>
    </div>

    {{-- Stats cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Users</p>
            <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['total_users'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border-t-4 border-red-400">
            <p class="text-xs font-medium text-red-500 uppercase tracking-wide">Admins</p>
            <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['admins'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border-t-4 border-yellow-400">
            <p class="text-xs font-medium text-yellow-500 uppercase tracking-wide">Managers</p>
            <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['managers'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border-t-4 border-blue-400">
            <p class="text-xs font-medium text-blue-500 uppercase tracking-wide">Viewers</p>
            <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['viewers'] }}</p>
        </div>
    </div>

    {{-- Quick actions --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Quick Actions</h3>
        <div class="flex flex-wrap gap-3">
            @can('viewAny', App\Models\User::class)
            <a href="{{ route('web.users.index') }}"
               class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                View Users
            </a>
            @endcan
            @can('create', App\Models\User::class)
            <a href="{{ route('web.users.create') }}"
               class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                + New User
            </a>
            @endcan
            <a href="{{ route('profile.edit') }}"
               class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition">
                My Profile
            </a>
        </div>
    </div>
</x-app-layout>
