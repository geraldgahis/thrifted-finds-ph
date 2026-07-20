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

<aside class="w-64 bg-white border-r border-[#d2d2d7] flex flex-col shrink-0 z-20">
    <!-- Brand / Logo Area -->
    <div class="h-16 flex items-center px-6 border-b border-[#d2d2d7]">
        <a href="/admin/dashboard" wire:navigate
            class="flex items-center gap-2 text-[#1d1d1f] font-semibold tracking-tight text-[15px]">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
            </svg>
            <span>Thrifted Admin</span>
        </a>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 overflow-y-auto py-6 px-3 space-y-1">
        <a href="/admin/dashboard" wire:navigate
            class="flex items-center px-3 py-2 rounded-xl text-[13px] font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-[#f5f5f7] text-[#1d1d1f]' : 'text-[#6e6e73] hover:text-[#1d1d1f] hover:bg-[#f5f5f7]/50' }}">
            Overview
        </a>
        <a href="/admin/products" wire:navigate
            class="flex items-center px-3 py-2 rounded-xl text-[13px] font-medium transition-colors {{ request()->routeIs('admin.products.*') ? 'bg-[#f5f5f7] text-[#1d1d1f]' : 'text-[#6e6e73] hover:text-[#1d1d1f] hover:bg-[#f5f5f7]/50' }}">
            Products
        </a>
        <a href="/admin/categories" wire:navigate
            class="flex items-center px-3 py-2 rounded-xl text-[13px] font-medium transition-colors {{ request()->routeIs('admin.categories.*') ? 'bg-[#f5f5f7] text-[#1d1d1f]' : 'text-[#6e6e73] hover:text-[#1d1d1f] hover:bg-[#f5f5f7]/50' }}">
            Categories
        </a>
        <a href="/admin/orders" wire:navigate
            class="flex items-center px-3 py-2 rounded-xl text-[13px] font-medium transition-colors {{ request()->routeIs('admin.orders.*') ? 'bg-[#f5f5f7] text-[#1d1d1f]' : 'text-[#6e6e73] hover:text-[#1d1d1f] hover:bg-[#f5f5f7]/50' }}">
            Orders
        </a>
    </nav>

    <!-- Bottom Actions -->
    <div class="p-4 border-t border-[#d2d2d7]">
        <div class="flex items-center px-3 mb-4">
            <div
                class="h-8 w-8 rounded-full bg-[#0071e3] text-white flex items-center justify-center text-[12px] font-semibold">
                {{ substr(auth()->user()->first_name ?? 'A', 0, 1) }}
            </div>
            <div class="ml-3 truncate">
                <p class="text-[13px] font-medium text-[#1d1d1f]">{{ auth()->user()->first_name ?? 'Admin' }}</p>
                <p class="text-[11px] text-[#6e6e73]">Store Owner</p>
            </div>
        </div>

        <button wire:click="logout"
            class="w-full flex items-center px-3 py-2 rounded-xl text-[13px] font-medium text-[#6e6e73] hover:text-red-600 hover:bg-red-50 transition-colors">
            Sign Out
        </button>
    </div>
</aside>
