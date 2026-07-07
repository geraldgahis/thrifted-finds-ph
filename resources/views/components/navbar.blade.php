<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return $this->redirect('/', navigate: true);
    }
};
?>

<!-- Apple-style slim, frosted glass fixed navigation -->
<nav class="fixed top-0 inset-x-0 z-50 bg-[#161617]/80 backdrop-blur-md border-b border-white/10 font-sans">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-12">

            <!-- 1. Logo (Left) -->
            <a href="/" wire:navigate
                class="flex-shrink-0 flex items-center gap-2 text-white font-semibold tracking-tight text-sm hover:opacity-80 transition-opacity">
                <!-- Minimalist Sparkle/Star Icon -->
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
                <span>Thrifted Finds</span>
            </a>

            <!-- 2. Navigation Links (Middle) -->
            <!-- Apple uses very small, slightly spaced, light gray text that turns white on hover -->
            <div class="hidden md:flex items-center justify-center space-x-8">
                <a href="/shop" wire:navigate
                    class="text-[12px] tracking-wide font-medium text-gray-300 hover:text-white transition-colors">Store</a>
                <a href="/men" wire:navigate
                    class="text-[12px] tracking-wide font-medium text-gray-300 hover:text-white transition-colors">Men</a>
                <a href="/women" wire:navigate
                    class="text-[12px] tracking-wide font-medium text-gray-300 hover:text-white transition-colors">Women</a>
                <a href="/vintage" wire:navigate
                    class="text-[12px] tracking-wide font-medium text-gray-300 hover:text-white transition-colors">Vintage</a>
            </div>

            <!-- 3. Utility & Auth (Right) -->
            <div class="flex items-center space-x-5">

                <!-- Search & Cart Icons (Apple uses very thin strokes for icons) -->
                <div class="flex items-center space-x-4">
                    <button class="text-gray-300 hover:text-white transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                    <a href="/cart" wire:navigate class="text-gray-300 hover:text-white transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </a>
                </div>

                <!-- Auth Section Separator -->
                <div class="pl-5 border-l border-white/20 flex items-center space-x-4">
                    @auth
                        <a href="/profile" wire:navigate
                            class="text-[12px] tracking-wide font-medium text-gray-300 hover:text-white transition-colors">
                            Account
                        </a>
                        <!-- Subtle logout link -->
                        <button wire:click="logout"
                            class="text-[12px] tracking-wide font-medium text-gray-500 hover:text-gray-300 transition-colors">
                            Sign Out
                        </button>
                    @else
                        <!-- Secondary Action -->
                        <a href="/login" wire:navigate
                            class="hidden sm:block text-[12px] tracking-wide font-medium text-gray-300 hover:text-white transition-colors">
                            Sign In
                        </a>
                        <!-- Primary CTA (Apple Pill Style) -->
                        <a href="/register" wire:navigate
                            class="bg-white text-black hover:bg-gray-200 transition-colors text-[12px] font-medium px-4 py-1 rounded-full tracking-wide">
                            Sign Up
                        </a>
                    @endauth
                </div>
            </div>

        </div>
    </div>
</nav>