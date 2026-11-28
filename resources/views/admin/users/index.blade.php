<x-admin-layout>
    <div class="py-8 px-3   sm:px-6 lg:px-8 min-w-0  overflow-y-auto ">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-800">Manage Admins & Users</h2>
                <span class ="text-sm text-gray-500">    
                    Active Admins: {{ $users->where('utype', 'ADM')->count() }} / 3
                </span>
            </div>
        <div class="flex flex-col bg-white shadow sm:rounded-lg p-6  w-full">

            @if (session('success'))
                <div class="mb-4 p-3 rounded bg-green-100 text-green-800 text-sm">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-3 rounded bg-red-100 text-red-800 text-sm">{{ session('error') }}</div>
            @endif
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left whitespace-nowrap  ">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-600">Name</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-600">Email</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-600">Role</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-600">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($users as $user)
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $user->name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $user->email }}</td>
                                <td class="px-4 py-3">
                                    @if ($user->utype === 'ADM')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">Admin</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">User</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if ($user->id !== Auth::id())
                                        <form action="{{ route('admin.admin.users.update.role', $user) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="utype" value="{{ $user->utype === 'ADM' ? 'USER' : 'ADM' }}">
                                            <button type="submit" class="px-3 py-1.5 rounded text-xs font-medium {{ $user->utype === 'ADM' ? 'bg-red-600 text-white hover:bg-red-700' : 'bg-indigo-600 text-white hover:bg-indigo-700' }}">
                                                {{ $user->utype === 'ADM' ? 'Remove Admin' : 'Make Admin' }}
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-400">Current Session</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </bg-white>
    </div>
</x-admin-layout>