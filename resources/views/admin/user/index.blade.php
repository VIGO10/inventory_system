<x-guest-layout>
    @if (Session::get('fail'))
        <div class="alert alert-danger">
            {{ Session::get('fail') }}
        </div>
    @endif
    @if (Session::get('success'))
        <div class="alert alert-success">
            {{ Session::get('success') }}
        </div>
    @endif
    <div style="padding: 2rem 1rem; max-width: 1400px; margin: 0 auto;">
        <!-- Header -->
        <div style="
            display: flex; 
            flex-direction: column; 
            align-items: flex-start; 
            margin-bottom: 2rem;
        ">
            <div style="margin-bottom: 1.5rem;">
                <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item" style="color: black;" ><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page" style="color: #6366f1">User Management</li>
                </ol>
                </nav>

                <h1 style="
                    font-size: 2.25rem; 
                    font-weight: 700; 
                    color: #111827; 
                    margin: 0;
                ">
                    User Management
                </h1>
                <p style="
                    margin-top: 0.5rem; 
                    color: #6b7280; 
                    font-size: 1rem;
                ">
                    Manage system users and their roles
                </p>
            </div>
        </div>

        <!-- Table Container -->
        <div style="
            background: white; 
            border-radius: 1rem; 
            overflow: hidden; 
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
            border: 1px solid #e5e7eb;
            max-width: 100%;
        ">

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; min-width: 800px;">
                    <thead>
                        <tr style="background: #f9fafb;">
                            <th style="
                                padding: 1rem 1.5rem; 
                                text-align: left; 
                                font-size: 0.75rem; 
                                font-weight: 600; 
                                color: #6b7280; 
                                text-transform: uppercase; 
                                letter-spacing: 0.05em;
                            ">Full Name</th>
                            <th style="padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Username</th>
                            <th style="padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Email</th>
                            <th style="padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Role</th>
                            <th style="padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Status</th>
                            <th style="padding: 1rem 1.5rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr style="
                                transition: background-color 0.15s;
                            "
                                onmouseover="this.style.backgroundColor='#f8fafc'"
                                onmouseout="this.style.backgroundColor='white'">
                                <td style="padding: 1rem 1.5rem; white-space: nowrap;">
                                    <div style="display: flex; align-items: center;">
                                        <div style="font-weight: 500; color: #111827;">
                                            {{ $user->fullname }}
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 1rem 1.5rem; color: #4b5563; white-space: nowrap;">{{ $user->username }}</td>
                                <td style="padding: 1rem 1.5rem; color: #4b5563; white-space: nowrap;">{{ $user->email }}</td>
                                <td style="padding: 1rem 1.5rem;">
                                    <span style="
                                        padding: 0.35rem 0.85rem; 
                                        border-radius: 9999px; 
                                        font-size: 0.75rem; 
                                        font-weight: 600;
                                        background-color: {{ $user->role === 'admin' ? '#ede9fe' : '#dbeafe' }};
                                        color: {{ $user->role === 'admin' ? '#6d28d9' : '#1d4ed8' }};
                                    ">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td style="padding: 1rem 1.5rem; white-space: nowrap;">
                                    @if($user->is_verified)
                                        <span style="
                                            background: rgba(34, 197, 94, 0.15);
                                            color: #22c55e;
                                            padding: 0.35rem 0.75rem;
                                            border-radius: 0.75rem;
                                            font-size: 0.85rem;
                                            font-weight: 600;
                                        ">
                                            Verified
                                        </span>
                                    @else
                                        <span style="
                                            background: rgba(239, 68, 68, 0.15);
                                            color: #ef4444;
                                            padding: 0.35rem 0.75rem;
                                            border-radius: 0.75rem;
                                            font-size: 0.85rem;
                                            font-weight: 600;
                                        ">
                                            Unverified
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 1rem 1.5rem; text-align: right; white-space: nowrap;">
                                    @if ($user->username === Auth::user()->username)
                                        <span style="
                                            color: #9ca3af; 
                                            font-weight: 500;
                                        ">
                                            Current User
                                        </span>
                                    @else
                                        @if (!$user->is_verified)
                                            <form action="{{ route('admin.user.verify', $user->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" 
                                                        style="
                                                            color: #10b981; 
                                                            background: none; 
                                                            border: none; 
                                                            font-weight: 500; 
                                                            cursor: pointer; 
                                                            transition: color 0.2s;
                                                        "
                                                        onmouseover="this.style.color='#059669'"
                                                        onmouseout="this.style.color='#10b981'">
                                                    Verify
                                                </button>
                                            </form>
                                            ||
                                        @endif
                                        <form action="{{ route('admin.user.delete', $user->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    onclick="return confirm('Are you sure you want to delete {{ addslashes($user->fullname) }}?')"
                                                    style="
                                                        color: #ef4444; 
                                                        background: none; 
                                                        border: none; 
                                                        font-weight: 500; 
                                                        cursor: pointer; 
                                                        transition: color 0.2s;
                                                    "
                                                    onmouseover="this.style.color='#dc2626'"
                                                    onmouseout="this.style.color='#ef4444'">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="padding: 4rem 1.5rem; text-align: center; color: #6b7280;">
                                    <div style="font-size: 4rem; margin-bottom: 1rem; opacity: 0.7;">👥</div>
                                    <p style="font-size: 1.25rem; font-weight: 500; margin-bottom: 0.5rem;">No users found</p>
                                    <p style="font-size: 0.95rem;">Start by adding your first user</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
                <div style="padding: 1.25rem 1.5rem; border-top: 1px solid #e5e7eb; text-align: center;">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-guest-layout>