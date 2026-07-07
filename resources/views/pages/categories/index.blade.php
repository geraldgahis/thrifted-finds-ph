<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Category;
use Illuminate\Support\Str;

new class extends Component {
    use WithPagination;

    // Modal state
    public bool $showModal = false;

    // Form fields
    public string $name = '';
    public string $slug = '';

    public function updatedName($value)
    {
        $this->slug = Str::slug($value);
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->reset(['name', 'slug']);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'slug' => 'required|string|max:255|unique:categories,slug',
        ]);

        Category::create([
            'name' => $this->name,
            'slug' => $this->slug,
        ]);

        session()->flash('success', 'Category "' . $this->name . '" created successfully.');

        $this->closeModal();
    }

    public function render()
    {
        // Fetch categories with a count of their associated products
        $categories = Category::withCount('products')->latest()->paginate(10);

        return view('pages.categories.index', compact('categories'))->title('Manage Categories - Admin');
    }
};
?>

<div class="min-h-screen bg-[#f5f5f7] py-12 px-4 sm:px-6 lg:px-8 font-sans antialiased relative">
    <div class="max-w-5xl mx-auto">

        <!-- Header Section -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-[28px] font-semibold tracking-tight text-[#1d1d1f]">
                    Categories
                </h1>
                <p class="text-[14px] text-[#6e6e73] mt-1">
                    Manage the categories used to organize your thrifted inventory.
                </p>
            </div>

            <!-- Primary Action -->
            <button wire:click="openModal"
                class="flex items-center justify-center py-2 px-4 border border-transparent rounded-full text-[13px] font-medium text-white bg-[#0071e3] hover:bg-[#0077ed] transition-colors shadow-sm">
                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Category
            </button>
        </div>

        <!-- Success Notification -->
        @if (session()->has('success'))
            <div class="mb-6 p-4 rounded-2xl bg-[#e8f5e9] border border-[#c8e6c9] flex items-start">
                <svg class="w-5 h-5 text-[#2e7d32] mt-0.5 mr-3 shrink-0" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-[14px] font-medium text-[#1b5e20]">
                    {{ session('success') }}
                </span>
            </div>
        @endif

        <!-- Data Table Card -->
        <div class="bg-white border border-[#d2d2d7] rounded-3xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-[#d2d2d7] bg-[#f5f5f7]/50">
                            <th class="py-3 px-6 text-[12px] font-medium text-[#6e6e73] uppercase tracking-wider">Name
                            </th>
                            <th class="py-3 px-6 text-[12px] font-medium text-[#6e6e73] uppercase tracking-wider">URL
                                Slug</th>
                            <th
                                class="py-3 px-6 text-[12px] font-medium text-[#6e6e73] uppercase tracking-wider text-center">
                                Items</th>
                            <th
                                class="py-3 px-6 text-[12px] font-medium text-[#6e6e73] uppercase tracking-wider text-right">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#d2d2d7]">
                        @forelse ($categories as $category)
                            <tr class="hover:bg-[#f5f5f7]/50 transition-colors">
                                <td class="py-4 px-6 text-[14px] font-medium text-[#1d1d1f]">
                                    {{ $category->name }}
                                </td>
                                <td class="py-4 px-6 text-[14px] text-[#6e6e73] font-mono text-sm">
                                    /{{ $category->slug }}
                                </td>
                                <td class="py-4 px-6 text-[14px] text-[#6e6e73] text-center">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#f5f5f7] text-[#1d1d1f]">
                                        {{ $category->products_count }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <button class="text-[13px] font-medium text-[#0071e3] hover:underline">Edit</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-[14px] text-[#6e6e73]">
                                    No categories found. Click "Add Category" to get started.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            @if ($categories->hasPages())
                <div class="bg-white border-t border-[#d2d2d7] px-6 py-4">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Create Category Modal Overlay -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0">
            <!-- Frosted Glass Backdrop -->
            <div wire:click="closeModal" class="fixed inset-0 bg-[#161617]/40 backdrop-blur-sm transition-opacity">
            </div>

            <!-- Modal Panel -->
            <div
                class="relative bg-white rounded-3xl shadow-xl w-full max-w-lg overflow-hidden transform transition-all">
                <div class="px-6 py-5 border-b border-[#d2d2d7] flex items-center justify-between">
                    <h3 class="text-[20px] font-semibold text-[#1d1d1f]">New Category</h3>
                    <button wire:click="closeModal"
                        class="text-[#6e6e73] hover:text-[#1d1d1f] transition-colors focus:outline-none">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="px-6 py-6">
                    <form wire:submit="save" class="space-y-5">

                        <!-- Category Name -->
                        <div>
                            <label for="name"
                                class="block text-[12px] font-medium text-[#6e6e73] tracking-wide mb-1">
                                Category Name
                            </label>
                            <input wire:model.live="name" type="text" id="name"
                                placeholder="e.g., Vintage Jackets" required
                                class="w-full px-3 py-2.5 border border-[#d2d2d7] rounded-xl text-[14px] bg-[#f5f5f7] text-[#1d1d1f] focus:outline-none focus:border-[#0071e3] focus:bg-white transition-colors">
                            @error('name')
                                <span class="text-red-600 text-[12px] mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Category Slug -->
                        <div>
                            <label for="slug"
                                class="block text-[12px] font-medium text-[#6e6e73] tracking-wide mb-1">
                                URL Slug
                            </label>
                            <input wire:model="slug" type="text" id="slug" required
                                class="w-full px-3 py-2.5 border border-[#d2d2d7] rounded-xl text-[14px] bg-[#f5f5f7] text-[#6e6e73] focus:outline-none focus:border-[#0071e3] focus:bg-white transition-colors">
                            @error('slug')
                                <span class="text-red-600 text-[12px] mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Actions -->
                        <div class="pt-4 flex items-center justify-end space-x-3">
                            <button type="button" wire:click="closeModal"
                                class="px-4 py-2.5 rounded-full text-[13px] font-medium text-[#1d1d1f] hover:bg-[#f5f5f7] transition-colors">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-5 py-2.5 border border-transparent rounded-full text-[13px] font-medium text-white bg-[#0071e3] hover:bg-[#0077ed] transition-colors shadow-sm">
                                <span wire:loading.remove wire:target="save">Save Category</span>
                                <span wire:loading wire:target="save">Saving...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
