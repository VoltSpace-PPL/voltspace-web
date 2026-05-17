@extends('layouts.main')

@section('content')
{{-- Breadcrumbs --}}
<div class="flex items-center gap-2 text-[13px] text-slate-500 font-medium mb-6">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2"/></svg>
    <span>></span>
    <a href="/dashboard" class="hover:text-white transition-colors">Dashboard</a>
    <span>></span>
    <span class="text-white">Settings</span>
</div>

{{-- Page Header --}}
<div class="mb-8">
    <h1 class="text-[28px] font-extrabold text-white tracking-tight leading-none">Settings</h1>
    <p class="text-slate-400 text-[14px] mt-2">System configuration and preferences</p>
</div>

{{-- Main Grid --}}
<div class="grid grid-cols-1 gap-6 pb-20 relative">
    {{-- Form element to submit the settings --}}
    <form id="settings-form" class="space-y-6">

        {{-- Profile Settings Card --}}
        <div class="rounded-2xl p-6" style="background:#131c2f; border:1px solid #1e293b;">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background:rgba(0,170,255,0.15); border:1px solid rgba(0,170,255,0.25);">
                    <svg class="w-6 h-6 text-[#00aaff]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-[16px] font-bold text-white">Profile Settings</h2>
                    <p class="text-[13px] text-slate-400">Manage your personal information</p>
                </div>
            </div>

            <div class="space-y-5">
                <div>
                    <label class="block text-[13px] font-semibold text-slate-400 mb-2">
                        Full Name
                    </label>
                    <input type="text" id="profile_name" name="profile_name" readonly
                        class="w-full rounded-xl px-4 py-3.5 text-[14px] text-white focus:outline-none transition-all cursor-not-allowed"
                        style="background:#1e293b; border:1.5px solid #334155;">
                </div>
                <div>
                    <label class="block text-[13px] font-semibold text-slate-400 mb-2">
                        Email
                    </label>
                    <input type="email" id="profile_email" name="profile_email" readonly
                        class="w-full rounded-xl px-4 py-3.5 text-[14px] text-white focus:outline-none transition-all cursor-not-allowed"
                        style="background:#1e293b; border:1.5px solid #334155;">
                </div>
            </div>
        </div>

        {{-- Energy Threshold Configuration Card --}}
        <div class="rounded-2xl p-6" style="background:#131c2f; border:1px solid #1e293b;">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background:rgba(239,68,68,0.15); border:1px solid rgba(239,68,68,0.25);">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" stroke-width="2"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-[16px] font-bold text-white">Energy Threshold Configuration</h2>
                    <p class="text-[13px] text-slate-400">Set alert thresholds for energy consumption</p>
                </div>
            </div>

            <div class="space-y-5">
                <div>
                    <label class="block text-[13px] font-semibold text-slate-400 mb-2">
                        High Usage Threshold (kWh)
                    </label>
                    <input type="number" step="0.01" id="high_usage_threshold_kwh" name="high_usage_threshold_kwh" required
                        class="w-full rounded-xl px-4 py-3.5 text-[14px] text-white focus:outline-none focus:ring-2 focus:ring-[#00d4aa]/50 transition-all"
                        style="background:#1e293b; border:1.5px solid #334155;">
                </div>
                <div>
                    <label class="block text-[13px] font-semibold text-slate-400 mb-2">
                        Peak Demand Limit (kW)
                    </label>
                    <input type="number" step="0.01" id="peak_demand_limit_kw" name="peak_demand_limit_kw" required
                        class="w-full rounded-xl px-4 py-3.5 text-[14px] text-white focus:outline-none focus:ring-2 focus:ring-[#00d4aa]/50 transition-all"
                        style="background:#1e293b; border:1.5px solid #334155;">
                </div>
            </div>
        </div>


        {{-- Floating Save Button --}}
        <div class="absolute bottom-0 right-0">
            <button type="submit" id="save-settings-btn"
                class="flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-bold text-[14px] transition-all shadow-lg text-[#0b1120]"
                style="background:#00d4aa; box-shadow:0 4px 20px rgba(0,212,170,0.3);">
                Save Changes
            </button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', async () => {
    // 1. Load User Profile
    try {
        const resUser = await apiFetch('/auth/me');
        if (resUser.ok) {
            const user = await resUser.json();
            document.getElementById('profile_name').value = user.name || '';
            document.getElementById('profile_email').value = user.email || '';
        }
    } catch (e) {
        console.warn('Failed to load user profile', e);
    }

    // 2. Load Settings
    try {
        const resSet = await apiFetch('/energy-alerts/settings');
        if (resSet.ok) {
            const data = await resSet.json();
            document.getElementById('high_usage_threshold_kwh').value = data.high_usage_threshold_kwh ?? '';
            document.getElementById('peak_demand_limit_kw').value = data.peak_demand_limit_kw ?? '';
        }
    } catch (e) {
        console.warn('Failed to load settings', e);
    }

    // 3. Handle Submit
    document.getElementById('settings-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const btn = document.getElementById('save-settings-btn');
        const origText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = 'Saving...';

        const payload = {
            high_usage_threshold_kwh: parseFloat(document.getElementById('high_usage_threshold_kwh').value),
            peak_demand_limit_kw: parseFloat(document.getElementById('peak_demand_limit_kw').value)
        };

        try {
            const res = await apiFetch('/energy-alerts/settings', {
                method: 'PUT',
                body: JSON.stringify(payload)
            });
            if (res.ok) {
                vsAlert.success('Settings Saved', 'Energy threshold configurations have been successfully updated.');
            } else {
                const err = await res.json();
                vsAlert.error('Save Failed', err.message || 'Failed to save settings.');
            }
        } catch (e) {
            vsAlert.error('Connection Error', 'Failed to connect to the server.');
        }
    });
});
</script>
@endpush
