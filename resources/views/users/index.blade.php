<x-app-layout>
    <x-slot name="title">Users</x-slot>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">

        {{-- Header --}}
        <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b">
            <h2 class="text-base font-semibold text-gray-800">All Users</h2>
            @can('create', App\Models\User::class)
            <a href="{{ route('web.users.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition w-fit">
                + New User
            </a>
            @endcan
        </div>

        {{-- Table (desktop) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500 tracking-wider">
                    <tr>
                        <th class="px-6 py-3">#</th>
                        <th class="px-6 py-3">Name</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Role</th>
                        <th class="px-6 py-3">Joined</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-gray-400">{{ $user->id }}</td>
                        <td class="px-6 py-4 font-medium text-gray-800">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold uppercase flex-shrink-0">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                {{ $user->name }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-500">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            @foreach($user->getRoleNames() as $role)
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold capitalize
                                {{ $role === 'admin' ? 'bg-red-100 text-red-700' : ($role === 'manager' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700') }}">
                                {{ $role }}
                            </span>
                            @endforeach
                        </td>
                        <td class="px-6 py-4 text-gray-400 text-xs">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @can('update', $user)
                                <a href="{{ route('web.users.edit', $user) }}"
                                   class="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition">
                                    Edit
                                </a>
                                @endcan
                                @can('delete', $user)
                                <form method="POST" action="{{ route('web.users.destroy', $user) }}"
                                      onsubmit="return confirm('Delete {{ addslashes($user->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200 transition">
                                        Delete
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-400">No users found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Cards (mobile) --}}
        <div class="md:hidden divide-y divide-gray-100">
            @forelse($users as $user)
            <div class="p-4 space-y-2">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm font-bold uppercase">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-medium text-gray-800 text-sm">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                        </div>
                    </div>
                    @foreach($user->getRoleNames() as $role)
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold capitalize
                        {{ $role === 'admin' ? 'bg-red-100 text-red-700' : ($role === 'manager' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700') }}">
                        {{ $role }}
                    </span>
                    @endforeach
                </div>
                <div class="flex gap-2 justify-end">
                    @can('update', $user)
                    <a href="{{ route('web.users.edit', $user) }}"
                       class="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition">Edit</a>
                    @endcan
                    @can('delete', $user)
                    <form method="POST" action="{{ route('web.users.destroy', $user) }}"
                          onsubmit="return confirm('Delete {{ addslashes($user->name) }}?')">
                        @csrf @method('DELETE')
                        <button class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200 transition">Delete</button>
                    </form>
                    @endcan
                </div>
            </div>
            @empty
            <p class="p-6 text-center text-gray-400 text-sm">No users found.</p>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
        <div class="px-6 py-4 border-t">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
