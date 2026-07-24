<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Sign In — Customer Service</title>
<script src="{{ asset('vendor/tailwind.js') }}"></script>
<script src="{{ asset('vendor/lucide.min.js') }}"></script>
<style>
  body {
    font-family: 'Segoe UI', 'Poppins', sans-serif;
    margin: 0;
    min-height: 100vh;
    /*
      TODO: Replace this background with your actual image once you place it in /public/assets/.
      Recommended: a .jpg (photo backgrounds compress much smaller as jpg than png).
      Example once you have the file:
      background: url('{{ asset('assets/login-bg.jpg') }}') center center / cover no-repeat;
    */
    background: url('{{ asset('img/background.avif') }}') center center / cover no-repeat;
    display: flex;
    align-items: center;
    justify-content: center;
  }
</style>
</head>
<body>

<div class="w-full max-w-md mx-4 my-10">
  <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl shadow-2xl px-8 sm:px-10 py-10">

    <!-- Icon -->
    <div class="flex justify-center mb-5">
      <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
        <i data-lucide="headphones" class="w-7 h-7 text-white"></i>
      </div>
    </div>

    <!-- Title -->
    <h1 class="text-2xl sm:text-3xl font-bold text-white text-center">Customer Service</h1>
    <p class="text-slate-300 text-sm text-center mt-2 mb-8">Securely access your customer communication management system.</p>

    @if ($errors->any())
      <div class="mb-6 rounded-xl bg-red-500/10 border border-red-500/30 px-4 py-3">
        <p class="text-red-300 text-sm font-medium">{{ $errors->first() }}</p>
      </div>
    @endif

    @if (session('status'))
      <div class="mb-6 rounded-xl bg-emerald-500/10 border border-emerald-500/30 px-4 py-3">
        <p class="text-emerald-300 text-sm font-medium">{{ session('status') }}</p>
      </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
      @csrf

      <!-- Email -->
      <div>
        <label for="email" class="text-sm text-slate-200 mb-1.5 block">Email Address</label>
        <div class="relative">
          <i data-lucide="mail" class="w-4 h-4 text-blue-400 absolute left-4 top-1/2 -translate-y-1/2"></i>
          <input
            id="email"
            name="email"
            type="email"
            value="{{ old('email') }}"
            required
            autofocus
            placeholder="agent@company.com"
            class="w-full bg-white/5 border border-white/15 text-white placeholder-slate-400 rounded-xl pl-11 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400/40 focus:border-blue-400/40"
          >
        </div>
      </div>

      <!-- Password -->
      <div>
        <label for="password" class="text-sm text-slate-200 mb-1.5 block">Password</label>
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

      <!-- Remember me / Forgot password -->
      <div class="flex items-center justify-between text-sm">
        <label class="flex items-center gap-2 text-slate-300 cursor-pointer">
          <input type="checkbox" name="remember" class="w-4 h-4 rounded accent-blue-500">
          Remember me
        </label>
        @if (Route::has('password.request'))
          <a href="{{ route('password.request') }}" class="text-blue-300 hover:text-blue-200">Forgot Password?</a>
        @endif
      </div>

      <!-- Submit -->
      <button type="submit" class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-500 hover:to-purple-500 text-white font-semibold rounded-xl py-3 text-sm transition-colors">
        Sign In
        <i data-lucide="arrow-right" class="w-4 h-4"></i>
      </button>
    </form>

    <!-- Divider -->
    <div class="flex items-center gap-3 my-7">
      <div class="flex-1 h-px bg-white/15"></div>
      <span class="text-xs text-slate-400">OR</span>
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

    @if (Route::has('register'))
      <p class="text-center text-sm text-slate-300 mt-8">
        Don't have an account?
        <a href="{{ route('register') }}" class="text-emerald-400 font-semibold hover:text-emerald-300">Create Account</a>
      </p>
    @endif

  </div>
</div>

<script>
  lucide.createIcons();

  const pwInput = document.getElementById('password');
  const toggleBtn = document.getElementById('togglePasswordBtn');
  const eyeIcon = document.getElementById('pwEyeIcon');

  toggleBtn.addEventListener('click', () => {
    const isHidden = pwInput.type === 'password';
    pwInput.type = isHidden ? 'text' : 'password';
    eyeIcon.setAttribute('data-lucide', isHidden ? 'eye-off' : 'eye');
    lucide.createIcons();
  });
</script>

</body>
</html>