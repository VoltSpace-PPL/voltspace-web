<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VoltSpace Login</title>
    <meta name="color-scheme" content="dark">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: {
                        navy: {
                            950: '#0b1120',
                            900: '#0f172a', // Left side background (slightly brighter)
                            850: '#1e293b', // Box background
                            800: '#1e293b', // Login card background
                        },
                        accent: {
                            teal: '#00d4aa',
                            blue: '#00aaff',
                            purple: '#9b59b6',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { 
            background-color: #0f172a; 
            color: #ffffff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            overflow-x: hidden;
        }
        /* Exact teal glow from Photo 1 - Brightened */
        .glow-bg {
            background: radial-gradient(ellipse at top left, #113a3a 0%, #0f172a 60%);
        }
        /* Blueprint grid pattern - slightly more visible */
        .grid-pattern {
            background-image: 
                linear-gradient(rgba(255, 255, 255, 0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.04) 1px, transparent 1px);
            background-size: 60px 60px;
        }
        .feature-icon-container {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .stat-box {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 16px;
            padding: 24px 32px;
            min-width: 160px;
        }
        .login-input-field {
            background-color: #0f172a;
            border: 1px solid #334155;
            color: white;
            height: 52px;
            border-radius: 10px;
            padding-left: 48px;
            width: 100%;
            box-sizing: border-box;
            font-size: 15px;
            transition: all 0.2s;
        }
        .login-input-field--password {
            padding-right: 48px;
        }
        .password-toggle-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            padding: 6px;
            cursor: pointer;
            color: #94a3b8;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            line-height: 0;
        }
        .password-toggle-btn:hover {
            color: #00d4aa;
        }
        .password-toggle-btn:focus-visible {
            outline: 2px solid #00d4aa;
            outline-offset: 2px;
        }
        .login-input-field:focus {
            border-color: #00d4aa;
            background-color: #1e293b;
            outline: none;
        }
        .login-input-field::placeholder { color: #94a3b8; }
        
        .btn-signin {
            background: linear-gradient(135deg, #00d4aa 0%, #00c896 100%);
            color: #ffffff;
            font-weight: 700;
            height: 52px;
            border-radius: 10px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-signin:hover { 
            filter: brightness(1.1);
        }
        
        .layout-container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            width: 100%;
            max-width: 100%;
        }
        @media (min-width: 1024px) {
            .layout-container {
                flex-direction: row;
            }
        }
        .left-section {
            flex: 1.4;
            padding: 64px 72px;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            z-index: 10;
            min-width: 0;
        }
        .right-section {
            flex: 1;
            background-color: #0b1120;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 28px;
            min-width: 0;
            box-sizing: border-box;
        }
        @media (max-width: 1023px) {
            .left-section {
                flex: none;
                padding: 28px 20px 20px;
            }
            .right-section {
                flex: 1;
                padding: 16px 16px 32px;
            }
        }
        
        .login-card {
            width: 100%;
            max-width: 430px;
            margin-left: auto;
            margin-right: auto;
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 20px;
            padding: 42px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
            box-sizing: border-box;
        }
        @media (max-width: 1023px) {
            .login-card {
                max-width: min(430px, 100%);
                padding: 32px 24px;
            }
        }
        @media (max-width: 380px) {
            .login-card {
                padding: 28px 18px;
            }
        }

        .demo-credential-box {
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 10px;
            padding: 16px 20px;
        }
    </style>
</head>
<body class="glow-bg">
    <div class="layout-container grid-pattern">
        <!-- LEFT SIDE: Branding -->
        <div class="left-section">
            <div>
                <!-- Exact Logo implementation from Photo 1 -->
                <div class="flex items-center gap-5 mb-10 lg:mb-16">
                    <div class="w-14 h-14 relative overflow-hidden rounded-full bg-white p-1">
                        <img src="/images/voltspace-logo.png" alt="VoltSpace Logo" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <h1 class="text-[26px] font-bold text-white leading-none">VoltSpace</h1>
                        <p class="text-[12px] font-bold uppercase tracking-[0.8px] mt-1" style="color: #00d4aa;">Smart Energy Management</p>
                    </div>
                </div>

                <!-- Titles -->
                <div class="mb-10 lg:mb-14">
                    <h2 class="text-[32px] sm:text-[44px] lg:text-[56px] font-extrabold text-white leading-[1.1] tracking-tight">Monitor & Control</h2>
                    <h2 class="text-[32px] sm:text-[44px] lg:text-[56px] font-extrabold leading-[1.1] tracking-tight" style="color: #00d4aa;">Your Energy</h2>
                </div>
                
                <p class="text-[#94a3b8] text-[16px] sm:text-[18px] max-w-[500px] leading-relaxed mb-10 lg:mb-14">
                    The complete platform for campus energy monitoring, room management, and intelligent automation.
                </p>

                <!-- Features -->
                <div class="space-y-8">
                    <div class="flex items-center gap-5">
                        <div class="feature-icon-container" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.2);">
                            <svg class="w-6 h-6" style="color: #10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-white text-[17px]">Real-time Monitoring</h4>
                            <p class="text-[#94a3b8] text-[14px]">Track energy consumption across all buildings</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-5">
                        <div class="feature-icon-container" style="background: rgba(0, 170, 255, 0.15); border: 1px solid rgba(0, 170, 255, 0.2);">
                            <svg class="w-6 h-6" style="color: #00aaff;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-white text-[17px]">Advanced Analytics</h4>
                            <p class="text-[#94a3b8] text-[14px]">Detailed reports and energy insights</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-5">
                        <div class="feature-icon-container" style="background: rgba(155, 89, 182, 0.15); border: 1px solid rgba(155, 89, 182, 0.2);">
                            <svg class="w-6 h-6" style="color: #9b59b6;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-white text-[17px]">Automated Control</h4>
                            <p class="text-[#94a3b8] text-[14px]">Schedule and automate room electricity</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="flex flex-wrap gap-6">
                <div class="stat-box">
                    <p class="text-[32px] font-bold leading-none mb-2" style="color: #00d4aa;">99.9%</p>
                    <p class="text-[12px] font-semibold text-[#94a3b8] uppercase tracking-widest">Uptime</p>
                </div>
                <div class="stat-box">
                    <p class="text-[32px] font-bold leading-none mb-2" style="color: #00aaff;">50+</p>
                    <p class="text-[12px] font-semibold text-[#94a3b8] uppercase tracking-widest">Campuses</p>
                </div>
                <div class="stat-box">
                    <p class="text-[32px] font-bold leading-none mb-2" style="color: #9b59b6;">24/7</p>
                    <p class="text-[12px] font-semibold text-[#94a3b8] uppercase tracking-widest">Support</p>
                </div>
            </div>
        </div>

        <!-- RIGHT SIDE: Login Card -->
        <div class="right-section">
            <div class="login-card">
                <div class="mb-10 text-left">
                    <h3 class="text-[32px] font-bold text-white leading-tight mb-3">Welcome Back</h3>
                    <p class="text-[#94a3b8] text-[16px]">Sign in to access your dashboard</p>
                </div>

                <form id="login-form" class="space-y-7">
                    <div>
                        <label class="block text-[15px] font-medium text-white mb-3">Email Address</label>
                        <div class="relative">
                            <input type="email" name="email" placeholder="admin@voltspace.id" value="admin@voltspace.id" required
                                   class="login-input-field">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#94a3b8] text-xl">✉</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[15px] font-medium text-white mb-3">Password</label>
                        <div class="relative">
                            <input type="password" id="login-password" name="password" placeholder="Enter your password" value="admin123" required
                                   class="login-input-field login-input-field--password" autocomplete="current-password">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#8892a4] text-lg pointer-events-none">🔒</span>
                            <button type="button" id="toggle-password" class="password-toggle-btn" aria-label="Show password" aria-pressed="false">
                                <svg id="icon-eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg id="icon-eye-off" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-y-2 gap-x-3 py-1">
                        <label class="flex items-center gap-2.5 cursor-pointer">
                            <input type="checkbox" class="w-4 h-4 rounded border-[#2a3a4a] bg-navy-950 text-accent-teal focus:ring-accent-teal/50">
                            <span class="text-[13px] text-[#8892a4]">Remember me</span>
                        </label>
                        <a href="#" class="text-[13px] font-bold" style="color: #00d4aa;">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn-signin">Sign In</button>
                </form>

                <!-- Demo Credentials -->
                <div class="mt-8">
                    <p class="text-center text-[11px] text-[#8892a4] mb-4 uppercase tracking-widest font-bold">Demo Credentials:</p>
                    <div class="space-y-2">
                        <div class="demo-credential-box">
                            <p class="text-[13px] font-medium text-white mb-1">Admin Account</p>
                            <p class="text-[13px] font-mono" style="color: #00d4aa;">admin@voltspace.id / admin123</p>
                        </div>
                        <div class="demo-credential-box">
                            <p class="text-[13px] font-medium text-white mb-1">Student Account</p>
                            <p class="text-[13px] font-mono" style="color: #00d4aa;">student@voltspace.id / student123</p>
                        </div>
                    </div>
                </div>
                <p class="text-center text-[11px] text-[#8892a4] mt-8">© 2024 VoltSpace. All rights reserved.</p>
            </div>
        </div>
    </div>

    @include('partials.voltspace-api')
    <script>
        (function () {
            var input = document.getElementById('login-password');
            var btn = document.getElementById('toggle-password');
            var iconEye = document.getElementById('icon-eye');
            var iconEyeOff = document.getElementById('icon-eye-off');
            if (!input || !btn || !iconEye || !iconEyeOff) return;
            btn.addEventListener('click', function () {
                var show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                btn.setAttribute('aria-pressed', show ? 'true' : 'false');
                btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
                iconEye.classList.toggle('hidden', show);
                iconEyeOff.classList.toggle('hidden', !show);
            });
        })();

        document.getElementById('login-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const f = e.target;
            try {
                const res = await apiFetch('/auth/login', {
                    method: 'POST',
                    body: JSON.stringify({ email: f.email.value, password: f.password.value }),
                });
                const data = await res.json().catch(() => ({}));
                const token = data.token || data.access_token || data.data?.token || data.data?.access_token;
                if (res.ok && token) {
                    localStorage.setItem('token', token);
                    location.href = '/dashboard';
                    location.href = '/rooms';
                } else {
                    const fromErrors = data.errors ? Object.values(data.errors).flat().filter(Boolean).join('\n') : '';
                    alert(fromErrors || data.message || 'Email atau password salah. Periksa kredensial demo di bawah form.');
                }
            } catch (err) {
                alert('API Connection Error');
            }
        });
    </script>
</body>
</html>