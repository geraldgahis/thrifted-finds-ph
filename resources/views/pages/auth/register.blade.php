<?php

use Livewire\Component;
use App\Models\User;
use App\Models\CustomerProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

new class extends Component {
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register()
    {
        $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => 'customer',
        ]);

        CustomerProfile::create([
            'user_id' => $user->id,
        ]);

        Auth::login($user);
        session()->regenerate();

        return $this->redirect('/shop', navigate: true);
    }
};
?>

<div
    class="min-h-screen bg-[#f5f5f7] flex flex-col justify-center items-center px-4 sm:px-6 lg:px-8 font-sans antialiased">
    <div class="w-full max-w-2xl bg-white border border-[#d2d2d7] rounded-3xl shadow-sm p-8 sm:p-10">

        <div class="text-center mb-8">
            <h2 class="text-[28px] font-semibold tracking-tight text-[#1d1d1f]">
                Create your account
            </h2>
            <p class="text-[14px] text-[#6e6e73] mt-2">
                Join us to explore premium thrifted collections.
            </p>
        </div>

        <form wire:submit="register" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="first_name"
                        class="block text-[12px] font-medium text-[#6e6e73] tracking-wide mb-1">First Name</label>
                    <input wire:model="first_name" type="text" id="first_name" required
                        class="w-full px-3 py-2.5 border border-[#d2d2d7] rounded-xl text-[14px] bg-[#f5f5f7] text-[#1d1d1f] focus:outline-none focus:border-[#0071e3] focus:bg-white transition-colors">
                    @error('first_name') <span class="text-red-600 text-[12px] mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="last_name" class="block text-[12px] font-medium text-[#6e6e73] tracking-wide mb-1">Last
                        Name</label>
                    <input wire:model="last_name" type="text" id="last_name" required
                        class="w-full px-3 py-2.5 border border-[#d2d2d7] rounded-xl text-[14px] bg-[#f5f5f7] text-[#1d1d1f] focus:outline-none focus:border-[#0071e3] focus:bg-white transition-colors">
                    @error('last_name') <span class="text-red-600 text-[12px] mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

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

            <div>
                <label for="password_confirmation"
                    class="block text-[12px] font-medium text-[#6e6e73] tracking-wide mb-1">Confirm Password</label>
                <input wire:model="password_confirmation" type="password" id="password_confirmation" required
                    class="w-full px-3 py-2.5 border border-[#d2d2d7] rounded-xl text-[14px] bg-[#f5f5f7] text-[#1d1d1f] focus:outline-none focus:border-[#0071e3] focus:bg-white transition-colors">
            </div>

            <div class="pt-4">
                <button type="submit"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-full text-[14px] font-medium text-white bg-[#0071e3] hover:bg-[#0077ed] focus:outline-none transition-colors shadow-sm">
                    <span wire:loading.remove>Agree & Continue</span>
                    <span wire:loading>Processing...</span>
                </button>
            </div>
        </form>

        <div class="mt-6 border-t border-[#d2d2d7] pt-6 text-center">
            <p class="text-[13px] text-[#6e6e73]">
                Already have an account?
                <a href="/login" wire:navigate class="text-[#0071e3] font-medium hover:underline ml-1">
                    Sign in here.
                </a>
            </p>
        </div>

    </div>
</div>