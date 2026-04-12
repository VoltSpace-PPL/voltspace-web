<?php $__env->startSection('content'); ?>
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
                <p class="text-[12px] font-bold text-slate-500 uppercase tracking-widest mb-1">Active Users</p>
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
                        <th class="px-8 py-5 text-[11px] font-bold text-slate-500 uppercase tracking-widest">Last Login</th>
                        <th class="px-8 py-5 text-[11px] font-bold text-slate-500 uppercase tracking-widest text-center">Status</th>
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

                <div class="space-y-3">
                    <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">Status</label>
                    <div class="flex items-center gap-8">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="radio" name="status" value="Active" checked class="w-4 h-4 accent-[#00d4aa]">
                            <span class="text-[15px] text-slate-400 group-hover:text-white transition-colors">Active</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="radio" name="status" value="Inactive" class="w-4 h-4 accent-[#00d4aa]">
                            <span class="text-[15px] text-slate-400 group-hover:text-white transition-colors">Inactive</span>
                        </label>
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

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    function openModal() { 
        document.getElementById('user-modal').classList.remove('hidden'); 
        document.body.style.overflow = 'hidden';
    }
    function closeModal() { 
        document.getElementById('user-modal').classList.add('hidden'); 
        document.body.style.overflow = 'auto';
    }

    // Default users data to show immediately as requested
    const DEFAULT_USERS = [
        { id: 1, name: 'Admin User', email: 'admin@voltspace.id', role: 'Admin', status: 'Active', last_login: '2024-03-27 10:30' },
        { id: 2, name: 'John Doe', email: 'john@voltspace.id', role: 'User', status: 'Active', last_login: '2024-03-27 09:15' },
        { id: 3, name: 'Energy Analyst', email: 'analyst@voltspace.id', role: 'User', status: 'Active', last_login: '2024-03-26 16:45' },
        { id: 4, name: 'Campus Staff', email: 'staff@voltspace.id', role: 'User', status: 'Inactive', last_login: '2024-03-20 14:20' }
    ];

    async function loadUsers() {
        const tbody = document.getElementById('users-table-body');
        let users = [...DEFAULT_USERS];

        try {
            const res = await apiFetch('/users');
            const data = await res.json();
            const apiUsers = Array.isArray(data) ? data : (data.data || data.users || []);
            if (apiUsers.length > 0) {
                users = apiUsers;
            }
        } catch (err) {
            console.warn('Backend logic skipped or unreachable, using default display data.');
        }
            
        document.getElementById('stat-total').textContent = users.length;
        document.getElementById('stat-admins').textContent = users.filter(u => String(u.role || '').toLowerCase() === 'admin').length;
        document.getElementById('stat-active').textContent = users.filter(u => ['active', '1', 'true'].includes(String(u.status || 'active').toLowerCase())).length;

        tbody.innerHTML = users.map(user => {
            const normalizedRole = String(user.role || '').toLowerCase();
            const isAdmin = normalizedRole === 'admin';
            const isActive = ['active', '1', 'true'].includes(String(user.status || 'active').toLowerCase());
            const lastLogin = user.last_login || '2024-03-27 10:30';

            return `
            <tr class="hover:bg-white/[0.02] transition-all group">
                <td class="px-8 py-6">
                    <span class="text-[13px] font-bold text-[#00d4aa] tracking-wider uppercase">USR-${String(user.id).padStart(3, '0')}</span>
                </td>
                <td class="px-8 py-6">
                    <span class="text-[15px] font-bold text-white">${user.name}</span>
                </td>
                <td class="px-8 py-6 text-[14px] text-slate-400 font-medium">${user.email}</span>
                </td>
                <td class="px-8 py-6">
                    <span class="px-3.5 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-widest ${isAdmin ? 'bg-purple-500/10 text-purple-500 border border-purple-500/20' : 'bg-slate-500/10 text-slate-400 border border-slate-500/20'}">
                        ${user.role || 'mahasiswa'}
                    </span>
                </td>
                <td class="px-8 py-6 text-[14px] text-slate-400 font-medium">${lastLogin}</span>
                </td>
                <td class="px-8 py-6">
                    <div class="flex justify-center">
                        <span class="px-4 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-widest ${isActive ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' : 'bg-slate-500/10 text-slate-500 border border-slate-500/20'}">
                            ${isActive ? 'Active' : 'Inactive'}
                        </span>
                    </div>
                </td>
                <td class="px-8 py-6 text-right">
                    <div class="flex items-center justify-end gap-3">
                        <button class="w-8 h-8 rounded-lg flex items-center justify-center bg-[#00aaff]/10 border border-[#00aaff]/20 text-[#00aaff] hover:bg-[#00aaff]/20 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.3"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </button>
                        <button class="w-8 h-8 rounded-lg flex items-center justify-center bg-red-500/10 border border-red-500/20 text-red-500 hover:bg-red-500/20 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.3"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </td>
            </tr>
            `;
        }).join('');
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
            if(res.ok){ 
                closeModal(); 
                f.reset(); 
                await loadUsers(); 
            } else { 
                const err = await res.json();
                alert('Error: ' + (err.message || 'Failed to save user')); 
            }
        } catch(err) {
            console.warn('Backend unavailable, simulating success.');
            closeModal();
            f.reset();
            alert('User added (Simulated)!');
            await loadUsers();
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });

    document.addEventListener('DOMContentLoaded', loadUsers);
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Telkom University\Materi Kuliah Semester 6\Proyek Perangkat Lunak\project_ppl-main\resources\views/users/index.blade.php ENDPATH**/ ?>