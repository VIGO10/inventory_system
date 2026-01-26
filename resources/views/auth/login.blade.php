<x-guest-layout>
    <div style="position: relative; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 3rem 1rem; overflow: hidden; background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);">

        <!-- Animated glowing background orbs -->
        <div style="position: absolute; inset: 0; pointer-events: none; z-index: 0; overflow: hidden;">
            <div style="position: absolute; width: 500px; height: 500px; background: radial-gradient(circle at 30% 20%, rgba(139,92,246,0.18) 0%, transparent 60%); border-radius: 50%; filter: blur(80px); animation: float 18s infinite ease-in-out; top: -10%; left: -15%;"></div>
            <div style="position: absolute; width: 600px; height: 600px; background: radial-gradient(circle at 70% 80%, rgba(79,70,229,0.16) 0%, transparent 65%); border-radius: 50%; filter: blur(100px); animation: float 22s infinite ease-in-out reverse; bottom: -15%; right: -20%;"></div>
            <div style="position: absolute; width: 400px; height: 400px; background: radial-gradient(circle, rgba(236,72,153,0.12) 0%, transparent 70%); border-radius: 50%; filter: blur(90px); animation: float 26s infinite ease-in-out; top: 40%; left: 45%;"></div>
        </div>

        <div style="position: relative; z-index: 10; width: 100%; max-width: 420px;">

            <!-- Logo & Title -->
            <div style="text-align: center; margin-bottom: 2.5rem;">
                <div style="margin: 0 auto 1.5rem; width: 80px; height: 80px; background: linear-gradient(135deg, #6366f1, #a855f7); border-radius: 1.25rem; display: flex; align-items: center; justify-content: center; box-shadow: 0 25px 50px -12px rgba(99,102,241,0.5), inset 0 1px 0 rgba(255,255,255,0.15);">
                    <svg style="width: 44px; height: 44px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                    </svg>
                </div>
                <h1 style="font-size: 2.5rem; font-weight: 800; color: white; letter-spacing: -0.025em; margin-bottom: 0.5rem;">
                    Inventory System
                </h1>
                <p style="color: rgba(255,255,255,0.75); font-size: 1.1rem;">
                    Secure internal access only
                </p>
            </div>

            <!-- Main Card - Glassmorphism -->
            <div style="backdrop-filter: blur(16px); background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.12); border-radius: 1.25rem; box-shadow: 0 30px 60px -15px rgba(0,0,0,0.7); padding: 2.5rem 2rem; position: relative; z-index: 2;">

                <x-auth-session-status style="margin-bottom: 1.5rem; text-align: center; color: #c7d2fe; font-size: 0.95rem;" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" style="display: flex; flex-direction: column; gap: 1.5rem;">
                    @csrf
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

                    <!-- Username / Email -->
                    <div>
                        <label for="identifier" style="display: block; font-size: 0.875rem; font-weight: 500; color: white; margin-bottom: 0.375rem;">
                            Username or Email
                        </label>
                        <div style="position: relative;">
                            <div style="position: absolute; inset-y: 0; left: 0; padding-left: 0.875rem; display: flex; align-items: center; pointer-events: none; z-index: 10;">
                                <i class="bi bi-envelope" style="font-size: 1.75rem; color: #94a3b8;"></i>
                            </div>

                            <input
                                id="identifier"
                                name="identifier"
                                type="text"
                                required
                                autofocus
                                autocomplete="username email"
                                value="{{ old('identifier') }}"
                                placeholder="username or admin@company.com"
                                style="width: 100%; height: 44px; padding-left: 3.5rem; padding-right: 1rem; font-size: 1rem; background: rgba(255,255,255,0.1); border: none; border-radius: 0.75rem; color: white; outline: none; transition: all 0.2s; line-height: 1.5; box-sizing: border-box;"
                            />
                            @error('identifier')
                                <p style="color:#fca5a5; font-size:0.85rem; margin-top:0.25rem;">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password-field" style="display: block; color: rgba(255,255,255,0.9); font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">
                            Password
                        </label>
                        <div style="position: relative;">
                            <!-- Icons container (both on left) -->
                            <div style="position: absolute; inset-y: 0; left: 0; display: flex; padding-left: 0.875rem; align-items: center; pointer-events: none; z-index: 10;">
                                <!-- Lock icon -->
                                <i class="bi bi-lock" style="font-size: 1.75rem; color: #94a3b8; margin-top: 0.2rem;"></i>
                                <button
                                    type="button"
                                    class="toggle-password"
                                    style="pointer-events: auto; background: none; border: none; padding: 0; cursor: pointer; margin-top : 0.5rem;"
                                >
                                    <!-- Eye icon (will toggle between eye and eye-slash) -->
                                    <i class="bi bi-eye-fill" style="font-size: 1.25rem; color: #94a3b8; transition: color 0.2s; margin-left: 0.5rem;"></i>
                                </button>
                            </div>
                            <input
                                id="password-field"
                                name="password"
                                type="password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••••••"
                                style="width: 100%; padding: 0.9rem 3.5rem 0.9rem 5rem; background: rgba(255,255,255,0.08); border: none; border-radius: 0.75rem; color: white; font-size: 1rem; outline: none; transition: all 0.2s; box-shadow: inset 0 1px 3px rgba(0,0,0,0.2);"
                            />
                            @error('password')
                                <p style="color:#fca5a5; font-size:0.85rem; margin-top:0.25rem;">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <!-- Remember me & Forgot password -->
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; font-size: 0.9rem;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; color: rgba(255,255,255,0.85); cursor: pointer; user-select: none;">
                            <input name="remember" type="checkbox" style="width: 1.1rem; height: 1.1rem; accent-color: #6366f1;" />
                            Remember me
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" style="color: #c7d2fe; text-decoration: none; transition: color 0.2s;">
                                Forgot password?
                            </a>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        style="width: 100%; padding: 1rem; font-size: 1.05rem; font-weight: 600; color: white; background: linear-gradient(to right, #6366f1, #a855f7); border: none; border-radius: 0.75rem; box-shadow: 0 10px 25px -5px rgba(99,102,241,0.5); transition: all 0.3s; cursor: pointer;"
                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 20px 35px -10px rgba(99,102,241,0.6)'"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 25px -5px rgba(99,102,241,0.5)'"
                    >
                        Sign In
                    </button>

                    <!-- Don't have account -->
                    <div style="text-align: center; color: rgba(255,255,255,0.75); font-size: 0.95rem; margin-top: 1.25rem;">
                        Don't have an account?
                        <a href="{{ route('register') }}" style="color: #c7d2fe; text-decoration: none; font-weight: 500; transition: color 0.2s;">
                            Sign up here
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Password visibility toggle script -->
    <script>
        document.querySelector('.toggle-password').addEventListener('click', function() {
            const field = document.getElementById('password-field');
            const icon = this.querySelector('i');
            const type = field.type === 'password' ? 'text' : 'password';
            field.type = type;
            icon.classList.toggle('bi-eye-fill');
            icon.classList.toggle('bi-eye-slash-fill');
        });
    </script>

</x-guest-layout>