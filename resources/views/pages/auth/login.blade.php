<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

new class extends Component {
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    protected array $rules = [
        'email' => 'required|email',
        'password' => 'required|string',
    ];

    public function login()
    {
        $this->validate();

        // 1. Enforce Rate Limiting (Prevent brute-force attacks)
        $throttleKey = strtolower($this->email) . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        // 2. Attempt Authentication
        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            // Hit the rate limiter on failure
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

        // 3. Prevent Session Fixation attacks on successful login
        session()->regenerate();
        RateLimiter::clear($throttleKey);

        // 4. Role-based redirection
        if (Auth::user()->role === 'admin') {
            return $this->redirect('/admin/dashboard', navigate: true);
        }

        return $this->redirect('/shop', navigate: true);
    }
};
?>

<div
    class="min-h-screen bg-[#f5f5f7] flex flex-col justify-center items-center px-4 sm:px-6 lg:px-8 font-sans antialiased">
    <div class="w-full max-w-2xl bg-white border border-[#d2d2d7] rounded-3xl shadow-sm p-8 sm:p-10">

        <div class="text-center mb-8">
            <h2 class="text-[28px] font-semibold tracking-tight text-[#1d1d1f]">
                Sign in to Thrifted Finds
            </h2>
            <p class="text-[14px] text-[#6e6e73] mt-2">
                Enter your details to access your storefront.
            </p>
        </div>

        <form wire:submit="login" class="space-y-4">
            <div>
                <label for="email" class="block text-[12px] font-medium text-[#6e6e73] tracking-wide mb-1">Email
                    Address</label>
                <input wire:model="email" type="email" id="email" required
                    class="w-full px-3 py-2.5 border border-[#d2d2d7] rounded-xl text-[14px] bg-[#f5f5f7] text-[#1d1d1f] focus:outline-none focus:border-[#0071e3] focus:bg-white transition-colors">
                @error('email') <span class="text-red-600 text-[12px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="password"
                    class="block text-[12px] font-medium text-[#6e6e73] tracking-wide mb-1">Password</label>
                <input wire:model="password" type="password" id="password" required
                    class="w-full px-3 py-2.5 border border-[#d2d2d7] rounded-xl text-[14px] bg-[#f5f5f7] text-[#1d1d1f] focus:outline-none focus:border-[#0071e3] focus:bg-white transition-colors">
                @error('password') <span class="text-red-600 text-[12px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center pt-1">
                <input wire:model="remember" id="remember" type="checkbox"
                    class="h-4 w-4 text-[#0071e3] border-[#d2d2d7] rounded focus:ring-0">
                <label for="remember" class="ml-2 block text-[12px] text-[#6e6e73]">
                    Stay signed in
                </label>
            </div>

            <div class="pt-4">
                <button type="submit"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-full text-[14px] font-medium text-white bg-[#0071e3] hover:bg-[#0077ed] focus:outline-none transition-colors shadow-sm">
                    <span wire:loading.remove>Sign In</span>
                    <span wire:loading>Processing...</span>
                </button>
            </div>
        </form>

        <div class="mt-6 border-t border-[#d2d2d7] pt-6 text-center">
            <p class="text-[13px] text-[#6e6e73]">
                New to Thrifted Finds?
                <a href="/register" wire:navigate class="text-[#0071e3] font-medium hover:underline ml-1">
                    Create yours now.
                </a>
            </p>
        </div>

    </div>
</div>