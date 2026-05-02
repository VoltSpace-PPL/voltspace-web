@extends('layouts.main')

@section('content')
<div class="mt-2">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-[13px] text-slate-400 mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2"/></svg>
        <span>&rsaquo;</span>
        <span>Dashboard</span>
        <span>&rsaquo;</span>
        <span class="text-white font-medium">Users</span>
    </nav>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-[32px] font-bold text-white leading-tight">Users</h1>
            <p class="text-[14px] text-slate-500 mt-1">User management and role assignment</p>
        </div>
        <button onclick="openModal()" class="flex items-center gap-2 px-5 py-2.5 bg-[#00d4aa] hover:bg-[#00bfa0] text-white rounded-lg font-bold text-[14px] transition-all self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M12 4v16m8-8H4"/></svg>
            Add User
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-[#1e293b] border border-[#334155] rounded-[24px] p-6 flex items-center gap-5 group transition-all hover:border-slate-500 shadow-lg">
            <div class="w-14 h-14 rounded-xl bg-[#00aaff]/10 flex items-center justify-center text-[#00aaff] border border-[#00aaff]/20">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div>
                <p class="text-[12px] font-bold text-slate-500 uppercase tracking-widest mb-1">Total Users</p>
                <h3 id="stat-total" class="text-[28px] font-bold text-white leading-none">0</h3>
            </div>
        </div>
        <div class="bg-[#1e293b] border border-[#334155] rounded-[24px] p-6 flex items-center gap-5 group transition-all hover:border-slate-500 shadow-lg">
            <div class="w-14 h-14 rounded-xl bg-[#10b981]/10 flex items-center justify-center text-[#10b981] border border-[#10b981]/20">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div>
                <p class="text-[12px] font-bold text-slate-500 uppercase tracking-widest mb-1">Admins</p>
                <h3 id="stat-admins" class="text-[28px] font-bold text-white leading-none">0</h3>
            </div>
        </div>
        <div class="bg-[#1e293b] border border-[#334155] rounded-[24px] p-6 flex items-center gap-5 group transition-all hover:border-slate-500 shadow-lg">
            <div class="w-14 h-14 rounded-xl bg-[#9b59b6]/10 flex items-center justify-center text-[#9b59b6] border border-[#9b59b6]/20">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
                <p class="text-[12px] font-bold text-slate-500 uppercase tracking-widest mb-1">Mahasiswa</p>
                <h3 id="stat-active" class="text-[28px] font-bold text-white leading-none">0</h3>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-[#1e293b] border border-[#334155] rounded-[24px] overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#0f172a] border-b border-[#334155]">
                        <th class="px-8 py-5 text-[11px] font-bold text-slate-500 uppercase tracking-widest">User ID</th>
                        <th class="px-8 py-5 text-[11px] font-bold text-slate-500 uppercase tracking-widest">Name</th>
                        <th class="px-8 py-5 text-[11px] font-bold text-slate-500 uppercase tracking-widest">Email</th>
                        <th class="px-8 py-5 text-[11px] font-bold text-slate-500 uppercase tracking-widest">Role</th>
                        <th class="px-8 py-5 text-[11px] font-bold text-slate-500 uppercase tracking-widest">Joined</th>
                        <th class="px-8 py-5 text-[11px] font-bold text-slate-500 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="users-table-body" class="divide-y divide-[#334155]">
                    <!-- Data rows populated by JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal with Glassmorphism -->
<div id="user-modal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-md" onclick="closeModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-[480px] p-4 max-h-[100dvh] overflow-y-auto overscroll-contain">
        <div class="glass-effect rounded-[24px] shadow-2xl overflow-hidden">
            <div class="p-6 flex justify-between items-center border-b border-white/10">
                <h3 class="text-[22px] font-bold text-white">Add New User</h3>
                <button onclick="closeModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-white/5 text-slate-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5"/></svg>
                </button>
            </div>
            
            <form id="user-form" class="p-8 space-y-6">
                <div class="space-y-2">
                    <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">Full Name</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">👤</span>
                        <input type="text" name="name" placeholder="Enter full name" required
                               class="w-full bg-white/5 border border-white/10 rounded-xl pl-11 pr-4 py-3.5 text-[15px] text-white placeholder:text-slate-600 focus:outline-none focus:border-[#00d4aa] transition-colors">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">Email Address</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">✉</span>
                        <input type="email" name="email" placeholder="user@voltspace.id" required
                               class="w-full bg-white/5 border border-white/10 rounded-xl pl-11 pr-4 py-3.5 text-[15px] text-white placeholder:text-slate-600 focus:outline-none focus:border-[#00d4aa] transition-colors">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">Password</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">🔒</span>
                        <input type="password" name="password" placeholder="Enter password" required
                               class="w-full bg-white/5 border border-white/10 rounded-xl pl-11 pr-4 py-3.5 text-[15px] text-white placeholder:text-slate-600 focus:outline-none focus:border-[#00d4aa] transition-colors">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">Role</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">🛡️</span>
                        <select name="role" class="w-full bg-white/5 border border-white/10 rounded-xl pl-11 pr-4 py-3.5 text-[15px] text-white focus:outline-none focus:border-[#00d4aa] transition-colors appearance-none cursor-pointer">
                            <option value="admin">Admin</option>
                            <option value="mahasiswa" selected>User</option>
                        </select>
                        <svg class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2"/></svg>
                    </div>
                </div>


                <div class="flex gap-4 pt-6">
                    <button type="button" onclick="closeModal()" class="flex-1 py-3.5 bg-white/5 border border-white/10 text-white font-bold rounded-xl hover:bg-white/10 transition-colors">Cancel</button>
                    <button type="submit" class="flex-1 py-3.5 bg-[#00d4aa] text-white font-bold rounded-xl hover:bg-[#00bfa0] transition-colors shadow-lg shadow-[#00d4aa]/20">Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>



<!-- Edit User Modal -->
<div id="edit-user-modal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-md" onclick="closeEditUserModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-[480px] p-4 max-h-[100dvh] overflow-y-auto overscroll-contain">
        <div class="glass-effect rounded-[24px] shadow-2xl overflow-hidden">
            <div class="p-6 flex justify-between items-center border-b border-white/10">
                <h3 class="text-[22px] font-bold text-white">Edit User</h3>
                <button onclick="closeEditUserModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-white/5 text-slate-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5"/></svg>
                </button>
            </div>
            <form id="edit-user-form" class="p-8 space-y-6">
                <input type="hidden" name="edit_user_id">
                <div class="space-y-2">
                    <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">Full Name</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">👤</span>
                        <input type="text" name="edit_name" placeholder="Enter full name" required class="w-full bg-white/5 border border-white/10 rounded-xl pl-11 pr-4 py-3.5 text-[15px] text-white placeholder:text-slate-600 focus:outline-none focus:border-[#00d4aa] transition-colors">
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">Email Address</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">✉</span>
                        <input type="email" name="edit_email" placeholder="user@voltspace.id" required class="w-full bg-white/5 border border-white/10 rounded-xl pl-11 pr-4 py-3.5 text-[15px] text-white placeholder:text-slate-600 focus:outline-none focus:border-[#00d4aa] transition-colors">
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">Password <span class="normal-case text-slate-500 font-normal">(Leave empty to keep current)</span></label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">🔒</span>
                        <input type="password" name="edit_password" placeholder="Enter password" class="w-full bg-white/5 border border-white/10 rounded-xl pl-11 pr-4 py-3.5 text-[15px] text-white placeholder:text-slate-600 focus:outline-none focus:border-[#00d4aa] transition-colors">
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">Role</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">🛡️</span>
                        <select name="edit_role" class="w-full bg-white/5 border border-white/10 rounded-xl pl-11 pr-4 py-3.5 text-[15px] text-white focus:outline-none focus:border-[#00d4aa] transition-colors appearance-none cursor-pointer">
                            <option value="admin">Admin</option>
                            <option value="mahasiswa">User</option>
                        </select>
                        <svg class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2"/></svg>
                    </div>
                </div>

                <div class="flex gap-4 pt-6">
                    <button type="button" onclick="closeEditUserModal()" class="flex-1 py-3.5 bg-white/5 border border-white/10 text-white font-bold rounded-xl hover:bg-white/10 transition-colors">Cancel</button>
                    <button type="submit" class="flex-1 py-3.5 bg-[#00d4aa] text-white font-bold rounded-xl hover:bg-[#00bfa0] transition-colors shadow-lg shadow-[#00d4aa]/20">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete User Modal -->
<div id="delete-user-modal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-md" onclick="closeDeleteUserModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-[420px] p-4">
        <div class="glass-effect rounded-[24px] shadow-2xl p-8">
            <div class="flex justify-between items-start mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-red-500/10 flex items-center justify-center text-red-500 border border-red-500/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="2"/></svg>
                    </div>
                    <h3 class="text-[20px] font-bold text-white">Delete User?</h3>
                </div>
                <button onclick="closeDeleteUserModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-white/5 text-slate-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5"/></svg>
                </button>
            </div>
            <p id="delete-user-message" class="text-[14px] text-slate-400 mb-5">Are you sure you want to delete this user?</p>
            <div class="flex items-start gap-3 p-4 bg-yellow-500/5 border border-yellow-500/20 rounded-xl mb-8">
                <svg class="w-5 h-5 text-yellow-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="2"/></svg>
                <p class="text-[13px] text-yellow-500/80">This action cannot be undone. All user data and associated records will be permanently deleted.</p>
            </div>
            <div class="flex gap-4">
                <button onclick="closeDeleteUserModal()" class="flex-1 py-3.5 bg-white/5 border border-white/10 text-white font-bold rounded-xl hover:bg-white/10 transition-colors">Cancel</button>
                <button id="confirm-delete-user-btn" class="flex-1 py-3.5 bg-red-500 text-white font-bold rounded-xl hover:bg-red-600 transition-colors">Delete User</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openModal() { 
        document.getElementById('user-modal').classList.remove('hidden'); 
        document.body.style.overflow = 'hidden';
    }
    function closeModal() { 
        document.getElementById('user-modal').classList.add('hidden'); 
        document.body.style.overflow = 'auto';
    }

    function openEditUserModal(user) {
        const f = document.getElementById('edit-user-form');
        f.edit_user_id.value = user.id;
        f.edit_name.value    = user.name || '';
        f.edit_email.value   = user.email || '';
        f.edit_password.value = '';
        f.edit_role.value    = (user.role || 'mahasiswa').toLowerCase();
        document.getElementById('edit-user-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeEditUserModal() {
        document.getElementById('edit-user-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    let deleteUserId = null;
    function openDeleteUserModal(id, name) {
        deleteUserId = id;
        document.getElementById('delete-user-message').textContent = `Are you sure you want to delete user "${name}"?`;
        document.getElementById('delete-user-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeDeleteUserModal() {
        document.getElementById('delete-user-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        deleteUserId = null;
    }

    document.getElementById('confirm-delete-user-btn').addEventListener('click', async () => {
        if (!deleteUserId) return;
        const btn = document.getElementById('confirm-delete-user-btn');
        btn.disabled = true; btn.textContent = 'Deleting...';
        try {
            const res = await apiFetch('/users/' + deleteUserId, { method: 'DELETE' });
            if (res.ok) { closeDeleteUserModal(); await loadUsers(); }
            else { const err = await res.json(); alert('Error: ' + (err.message || 'Failed')); }
        } catch(e) { alert('Network error.'); }
        finally { btn.disabled = false; btn.textContent = 'Delete User'; }
    });

    document.getElementById('edit-user-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const f = e.target;
        const btn = f.querySelector('button[type="submit"]');
        const orig = btn.innerHTML;
        btn.disabled = true; btn.textContent = 'Saving...';
        const id = f.edit_user_id.value;
        const payload = { name: f.edit_name.value, email: f.edit_email.value, role: f.edit_role.value };
        if (f.edit_password.value) payload.password = f.edit_password.value;
        try {
            const res = await apiFetch('/users/' + id, { method: 'PUT', body: JSON.stringify(payload) });
            if (res.ok) { closeEditUserModal(); await loadUsers(); }
            else { const err = await res.json(); alert('Error: ' + (err.message || 'Failed')); }
        } catch(e) { alert('Network error.'); }
        finally { btn.disabled = false; btn.innerHTML = orig; }
    });

    // Global users store - keyed by id
    let usersMap = {};

    async function loadUsers() {
        const tbody = document.getElementById('users-table-body');
        tbody.innerHTML = `<tr><td colspan="5" class="px-8 py-16 text-center text-slate-500">Loading users...</td></tr>`;

        try {
            const res = await apiFetch('/users');
            if (!res.ok) throw new Error('API error');
            const data = await res.json();
            const users = Array.isArray(data) ? data : (data.data || data.users || []);

            usersMap = {};
            users.forEach(u => { usersMap[u.id] = u; });

            document.getElementById('stat-total').textContent   = users.length;
            document.getElementById('stat-admins').textContent   = users.filter(u => String(u.role||'').toLowerCase() === 'admin').length;
            document.getElementById('stat-active').textContent   = users.filter(u => String(u.role||'').toLowerCase() === 'mahasiswa').length;

            if (users.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="px-8 py-16 text-center text-slate-500">No users found.</td></tr>`;
                return;
            }

            tbody.innerHTML = users.map(user => {
                const normalizedRole = String(user.role || '').toLowerCase();
                const isAdmin = normalizedRole === 'admin';
                const userId = user.id;

                return `
                <tr class="hover:bg-white/[0.02] transition-all group">
                    <td class="px-8 py-6">
                        <span class="text-[13px] font-bold text-[#00d4aa] tracking-wider uppercase">USR-${String(userId).padStart(3, '0')}</span>
                    </td>
                    <td class="px-8 py-6">
                        <span class="text-[15px] font-bold text-white">${user.name}</span>
                    </td>
                    <td class="px-8 py-6 text-[14px] text-slate-400 font-medium">${user.email}</td>
                    <td class="px-8 py-6">
                        <span class="px-3.5 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-widest ${isAdmin ? 'bg-purple-500/10 text-purple-500 border border-purple-500/20' : 'bg-slate-500/10 text-slate-400 border border-slate-500/20'}">
                            ${user.role || 'mahasiswa'}
                        </span>
                    </td>
                    <td class="px-8 py-6 text-[14px] text-slate-400 font-medium">${(user.created_at || '').slice(0,10) || '-'}</td>
                    <td class="px-8 py-6 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <button data-edit-uid="${userId}" class="btn-edit-user w-8 h-8 rounded-lg flex items-center justify-center bg-[#00aaff]/10 border border-[#00aaff]/20 text-[#00aaff] hover:bg-[#00aaff]/20 transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.3"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </button>
                            <button data-delete-uid="${userId}" class="btn-delete-user w-8 h-8 rounded-lg flex items-center justify-center bg-red-500/10 border border-red-500/20 text-red-500 hover:bg-red-500/20 transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.3"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                `;
            }).join('');

            // Attach event listeners after rendering
            document.querySelectorAll('.btn-edit-user').forEach(btn => {
                btn.addEventListener('click', function() {
                    const user = usersMap[this.dataset.editUid];
                    if (user) openEditUserModal(user);
                });
            });
            document.querySelectorAll('.btn-delete-user').forEach(btn => {
                btn.addEventListener('click', function() {
                    const user = usersMap[this.dataset.deleteUid];
                    if (user) openDeleteUserModal(user.id, user.name);
                });
            });

        } catch (err) {
            tbody.innerHTML = `<tr><td colspan="5" class="px-8 py-16 text-center text-slate-500">Failed to load users. Make sure you are logged in.</td></tr>`;
        }
    }

    document.getElementById('user-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const f = e.target;
        const btn = f.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = 'Processing...';

        const payload = { 
            name: f.name.value, 
            email: f.email.value, 
            password: f.password.value, 
            role: f.role.value 
        };

        try {
            const res = await apiFetch('/users', { method:'POST', body: JSON.stringify(payload) });
            if (res.ok) { 
                closeModal(); 
                f.reset(); 
                await loadUsers(); 
            } else { 
                const err = await res.json();
                const msg = err?.errors ? Object.values(err.errors).flat().join('\n') : (err.message || 'Failed to save user');
                alert('Error: ' + msg); 
            }
        } catch(err) {
            alert('Network error. Please try again.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });

    document.addEventListener('DOMContentLoaded', loadUsers);
</script>
@endpush


