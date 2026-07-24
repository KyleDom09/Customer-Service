<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Create Account — Customer Service </title>
<script src="{{ asset('vendor/tailwind.js') }}"></script>
<script src="{{ asset('vendor/lucide.min.js') }}"></script>
<style>
  body {
    font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
    margin: 0;
    min-height: 100vh;
    background: url('{{ asset('img/background.avif') }}') center center / cover no-repeat;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #f1f5f9; /* slate-100 default, so nothing inherits invisible dark text */
    -webkit-font-smoothing: antialiased;
  }
  input {
    color: #ffffff !important;
    font-size: 15px !important;
  }
  input::placeholder {
    color: #a3adc2 !important; /* lighter than slate-400, visible on dark card */
    opacity: 1;
  }
  label {
    color: #e2e8f0 !important; /* slate-200, brighter than before */
    font-size: 14px !important;
    font-weight: 500;
  }
</style>
</head>
<body>

<div class="w-full max-w-5xl mx-4 my-10">
  <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl shadow-2xl overflow-hidden flex flex-col md:flex-row">

    <!-- Left panel: branding -->
    <div class="md:w-[38%] px-8 sm:px-10 py-10 flex flex-col justify-center items-center text-center border-b md:border-b-0 md:border-r border-white/10 bg-white/5">
      <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center mb-5">
        <i data-lucide="headphones" class="w-7 h-7 text-white"></i>
      </div>
      <h1 class="text-3xl font-bold text-white">Customer Service</h1>
      <p class="text-slate-200 text-base mt-3">Create your account to get started.</p>

      @if (Route::has('login'))
        <p class="text-sm text-slate-200 mt-8">
          Already have an account?<br>
          <a href="{{ route('login') }}" class="text-emerald-400 font-semibold hover:text-emerald-300">Sign in</a>
        </p>
      @endif
    </div>

    <!-- Right panel: form -->
    <div class="md:w-[62%] px-8 sm:px-10 py-10">

    @if ($errors->any())
      <div class="mb-6 rounded-xl bg-red-500/10 border border-red-500/30 px-4 py-3">
        <p class="text-red-300 text-sm font-medium">{{ $errors->first() }}</p>
      </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="space-y-5" id="signupForm">
      @csrf

      <!-- First / Last Name / Email in one row on landscape -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label for="first_name" class="text-sm mb-2 block">First Name</label>
          <div class="relative">
            <i data-lucide="user" class="w-4 h-4 text-blue-400 absolute left-4 top-1/2 -translate-y-1/2"></i>
            <input
              id="first_name"
              name="first_name"
              type="text"
              value="{{ old('first_name') }}"
              required
              autofocus
              placeholder="John"
              class="w-full bg-white/5 border border-white/15 text-white placeholder-slate-400 rounded-xl pl-11 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400/40 focus:border-blue-400/40"
            >
          </div>
        </div>
        <div>
          <label for="last_name" class="text-sm mb-2 block">Last Name</label>
          <div class="relative">
            <i data-lucide="user" class="w-4 h-4 text-blue-400 absolute left-4 top-1/2 -translate-y-1/2"></i>
            <input
              id="last_name"
              name="last_name"
              type="text"
              value="{{ old('last_name') }}"
              required
              placeholder="Doe"
              class="w-full bg-white/5 border border-white/15 text-white placeholder-slate-400 rounded-xl pl-11 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400/40 focus:border-blue-400/40"
            >
          </div>
        </div>
      </div>

      <!-- Email -->
      <div>
        <label for="email" class="text-sm mb-2 block">Email Address</label>
        <div class="relative">
          <i data-lucide="mail" class="w-4 h-4 text-blue-400 absolute left-4 top-1/2 -translate-y-1/2"></i>
          <input
            id="email"
            name="email"
            type="email"
            value="{{ old('email') }}"
            required
            placeholder="agent@company.com"
            class="w-full bg-white/5 border border-white/15 text-white placeholder-slate-400 rounded-xl pl-11 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400/40 focus:border-blue-400/40"
          >
        </div>
      </div>

      <!-- Password / Confirm Password side by side in landscape -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label for="password" class="text-sm mb-2 block">Password</label>
          <div class="relative">
            <i data-lucide="lock" class="w-4 h-4 text-slate-300 absolute left-4 top-1/2 -translate-y-1/2"></i>
            <input
              id="password"
              name="password"
              type="password"
              required
              placeholder="••••••••"
              class="w-full bg-white/5 border border-white/15 text-white placeholder-slate-400 rounded-xl pl-11 pr-11 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400/40 focus:border-blue-400/40"
            >
            <button type="button" id="togglePasswordBtn" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 hover:text-white">
              <i data-lucide="eye" id="pwEyeIcon" class="w-4 h-4"></i>
            </button>
          </div>
        </div>

        <div>
          <label for="password_confirmation" class="text-sm mb-2 block">Confirm Password</label>
          <div class="relative">
            <i data-lucide="shield-check" id="confirmIcon" class="w-4 h-4 text-slate-300 absolute left-4 top-1/2 -translate-y-1/2"></i>
            <input
              id="password_confirmation"
              name="password_confirmation"
              type="password"
              required
              placeholder="••••••••"
              class="w-full bg-white/5 border border-white/15 text-white placeholder-slate-400 rounded-xl pl-11 pr-11 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400/40 focus:border-blue-400/40"
            >
            <button type="button" id="toggleConfirmBtn" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 hover:text-white">
              <i data-lucide="eye" id="confirmEyeIcon" class="w-4 h-4"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Password Requirements -->
      <div class="rounded-xl bg-white/5 border border-white/10 px-4 py-3.5">
        <p class="text-sm font-medium text-slate-100 mb-3">Password Requirements</p>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-3 gap-y-1.5">
          <div class="flex items-center gap-1.5 text-sm text-slate-200" data-rule="length">
            <span class="req-dot w-3.5 h-3.5 rounded-full bg-white/10 flex items-center justify-center shrink-0">
              <i data-lucide="check" class="w-2.5 h-2.5"></i>
            </span>
            Minimum 8 characters
          </div>
          <div class="flex items-center gap-1.5 text-sm text-slate-200" data-rule="upper">
            <span class="req-dot w-3.5 h-3.5 rounded-full bg-white/10 flex items-center justify-center shrink-0">
              <i data-lucide="check" class="w-2.5 h-2.5"></i>
            </span>
            One uppercase letter
          </div>
          <div class="flex items-center gap-1.5 text-sm text-slate-200" data-rule="lower">
            <span class="req-dot w-3.5 h-3.5 rounded-full bg-white/10 flex items-center justify-center shrink-0">
              <i data-lucide="check" class="w-2.5 h-2.5"></i>
            </span>
            One lowercase letter
          </div>
          <div class="flex items-center gap-1.5 text-sm text-slate-200" data-rule="number">
            <span class="req-dot w-3.5 h-3.5 rounded-full bg-white/10 flex items-center justify-center shrink-0">
              <i data-lucide="check" class="w-2.5 h-2.5"></i>
            </span>
            One number
          </div>
          <div class="flex items-center gap-1.5 text-sm text-slate-200 col-span-2 sm:col-span-1" data-rule="special">
            <span class="req-dot w-3.5 h-3.5 rounded-full bg-white/10 flex items-center justify-center shrink-0">
              <i data-lucide="check" class="w-2.5 h-2.5"></i>
            </span>
            One special character (!@#$%^&*)
          </div>
        </div>
      </div>

      <!-- Terms -->
      <div>
        <label class="flex items-start gap-2 text-sm text-slate-200 cursor-pointer">
          <input type="checkbox" name="terms" required class="w-4 h-4 mt-0.5 rounded accent-emerald-500">
          <span>
            I agree to the
            <a href="#" class="text-blue-300 hover:text-blue-200 underline">Terms of Service</a>
            and
            <a href="#" class="text-blue-300 hover:text-blue-200 underline">Privacy Policy</a>
          </span>
        </label>
        @error('terms') <p class="text-red-300 text-sm mt-1.5">{{ $message }}</p> @enderror
      </div>

      <!-- Submit -->
      <button type="submit" class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-500 hover:to-purple-500 text-white font-semibold rounded-xl py-3 text-sm transition-colors">
        Create Account
        <i data-lucide="arrow-right" class="w-4 h-4"></i>
      </button>
    </form>

    <!-- Divider -->
    <div class="flex items-center gap-3 my-7">
      <div class="flex-1 h-px bg-white/15"></div>
      <span class="text-sm text-slate-300 font-medium">OR</span>
      <div class="flex-1 h-px bg-white/15"></div>
    </div>

    <!-- Social buttons (visual only — not wired to a provider yet) -->
    <div class="grid grid-cols-3 gap-3">
      <button type="button" title="Continue with Google" class="flex items-center justify-center py-3 rounded-xl bg-white/5 border border-white/15 hover:bg-white/10 transition-colors">
        <svg class="w-4 h-4" viewBox="0 0 24 24"><path fill="#EA4335" d="M12 10.2v3.9h5.5c-.24 1.3-1.7 3.8-5.5 3.8-3.3 0-6-2.7-6-6s2.7-6 6-6c1.9 0 3.15.8 3.87 1.5l2.64-2.55C16.85 3.15 14.65 2.2 12 2.2 6.9 2.2 2.7 6.4 2.7 11.5S6.9 20.8 12 20.8c6.9 0 9.3-4.85 9.3-7.35 0-.5-.05-.85-.12-1.25H12z"/></svg>
      </button>
      <button type="button" title="Continue with Microsoft" class="flex items-center justify-center py-3 rounded-xl bg-white/5 border border-white/15 hover:bg-white/10 transition-colors">
        <i data-lucide="layout-grid" class="w-4 h-4 text-white"></i>
      </button>
      <button type="button" title="Continue with Apple" class="flex items-center justify-center py-3 rounded-xl bg-white/5 border border-white/15 hover:bg-white/10 transition-colors">
        <i data-lucide="apple" class="w-4 h-4 text-white"></i>
      </button>
    </div>

    </div>
    <!-- end right panel -->

  </div>
</div>

<script>
  lucide.createIcons();

  function setupToggle(inputId, btnId, iconId) {
    const input = document.getElementById(inputId);
    const btn = document.getElementById(btnId);
    const icon = document.getElementById(iconId);
    btn.addEventListener('click', () => {
      const isHidden = input.type === 'password';
      input.type = isHidden ? 'text' : 'password';
      icon.setAttribute('data-lucide', isHidden ? 'eye-off' : 'eye');
      lucide.createIcons();
    });
  }
  setupToggle('password', 'togglePasswordBtn', 'pwEyeIcon');
  setupToggle('password_confirmation', 'toggleConfirmBtn', 'confirmEyeIcon');

  // Live password requirement checks
  const pwInput = document.getElementById('password');
  const rules = {
    length:  v => v.length >= 8,
    upper:   v => /[A-Z]/.test(v),
    lower:   v => /[a-z]/.test(v),
    number:  v => /[0-9]/.test(v),
    special: v => /[!@#$%^&*]/.test(v),
  };
  pwInput.addEventListener('input', () => {
    const val = pwInput.value;
    Object.keys(rules).forEach(key => {
      const row = document.querySelector(`[data-rule="${key}"]`);
      const dot = row.querySelector('.req-dot');
      const met = rules[key](val);
      row.classList.toggle('text-slate-200', !met);
      row.classList.toggle('text-emerald-300', met);
      dot.classList.toggle('bg-white/10', !met);
      dot.classList.toggle('bg-emerald-500', met);
    });
  });
</script>

</body>
</html>