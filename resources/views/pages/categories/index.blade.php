<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithPagination, WithFileUploads;

    // Modal states
    public bool $showModal = false;
    public bool $showEditModal = false;
    public bool $showDeleteModal = false;

    // Form fields
    public $categoryId = null;
    public string $name = '';
    public string $slug = '';
    public $image;
    public $existingImage;

    public function updatedName($value)
    {
        $this->slug = Str::slug($value);
    }

    // --- CREATE LOGIC ---
    public function openModal()
    {
        $this->resetValidation();
        $this->reset(['categoryId', 'name', 'slug', 'image', 'existingImage']);
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
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($this->image) {
            $imagePath = $this->image->store('categories', 'public');
        }

        Category::create([
            'name' => $this->name,
            'slug' => $this->slug,
            'image_path' => $imagePath,
        ]);

        session()->flash('success', 'Category "' . $this->name . '" created successfully.');
        $this->closeModal();
    }

    // --- EDIT LOGIC ---
    public function openEditModal($id)
    {
        $this->resetValidation();
        $this->reset(['image']);

        $category = Category::findOrFail($id);
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->existingImage = $category->image_path;

        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $this->categoryId,
            'slug' => 'required|string|max:255|unique:categories,slug,' . $this->categoryId,
            'image' => 'nullable|image|max:2048',
        ]);

        $category = Category::findOrFail($this->categoryId);
        $imagePath = $category->image_path;

        if ($this->image) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $this->image->store('categories', 'public');
        }

        $category->update([
            'name' => $this->name,
            'slug' => $this->slug,
            'image_path' => $imagePath,
        ]);

        session()->flash('success', 'Category "' . $this->name . '" updated successfully.');
        $this->closeEditModal();
    }

    // --- DELETE LOGIC ---
    public function openDeleteModal($id)
    {
        $this->categoryId = $id;
        $category = Category::findOrFail($id);
        $this->name = $category->name;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
    }

    public function destroy()
    {
        $category = Category::findOrFail($this->categoryId);

        if ($category->image_path) {
            Storage::disk('public')->delete($category->image_path);
        }

        $category->delete();

        session()->flash('success', 'Category "' . $this->name . '" deleted successfully.');
        $this->closeDeleteModal();
    }

    public function render()
    {
        $categories = Category::withCount('products')->latest()->paginate(10);
        return view('pages.categories.index', compact('categories'))->title('Manage Categories - Admin');
    }
};
?>

{{-- Restored x-data to the parent wrapper so buttons can instantly trigger the modals --}}
<div x-data="{ modalOpen: @entangle('showModal'), editModalOpen: @entangle('showEditModal'), deleteModalOpen: @entangle('showDeleteModal') }" class="min-h-screen bg-[#f5f5f7] py-12 px-4 sm:px-6 lg:px-8 font-sans antialiased relative">
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
            <button @click="modalOpen = true" wire:click="openModal"
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
                            <th class="py-3 px-6 text-[12px] font-medium text-[#6e6e73] uppercase tracking-wider">Image
                            </th>
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
                                <td class="py-4 px-6">
                                    @if ($category->image_path)
                                        <img src="{{ Storage::url($category->image_path) }}" alt="{{ $category->name }}"
                                            class="w-10 h-10 rounded-lg object-cover border border-[#d2d2d7]">
                                    @else
                                        <div
                                            class="w-10 h-10 rounded-lg bg-[#e8e8ed] flex items-center justify-center text-[#6e6e73] text-[10px] uppercase font-semibold">
                                            None</div>
                                    @endif
                                </td>
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
                                <td class="py-4 px-6 text-right space-x-3">
                                    <button @click="editModalOpen = true"
                                        wire:click="openEditModal({{ $category->id }})"
                                        class="text-[13px] font-medium text-[#0071e3] hover:underline focus:outline-none">Edit</button>
                                    <button @click="deleteModalOpen = true"
                                        wire:click="openDeleteModal({{ $category->id }})"
                                        class="text-[13px] font-medium text-red-600 hover:underline focus:outline-none">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-[14px] text-[#6e6e73]">
                                    No categories found. Click "Add Category" to get started.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($categories->hasPages())
                <div class="bg-white border-t border-[#d2d2d7] px-6 py-4">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- CREATE MODAL -->
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0">
        <div x-show="modalOpen" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="modalOpen = false" wire:click="closeModal"
            class="fixed inset-0 bg-[#161617]/40 backdrop-blur-sm"></div>

        <div x-show="modalOpen" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative bg-white rounded-3xl shadow-xl w-full max-w-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-[#d2d2d7] flex items-center justify-between">
                <h3 class="text-[20px] font-semibold text-[#1d1d1f]">New Category</h3>
                <button @click="modalOpen = false" wire:click="closeModal"
                    class="text-[#6e6e73] hover:text-[#1d1d1f] transition-colors focus:outline-none">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="px-6 py-6">
                <form wire:submit="save" class="space-y-5">
                    <div>
                        <label class="block text-[12px] font-medium text-[#6e6e73] tracking-wide mb-2">Category Image
                            (Optional)</label>
                        <input type="file" wire:model="image"
                            class="block w-full text-sm text-[#6e6e73] file:mr-4 file:py-2.5 file:px-4 file:rounded-full file:border-0 file:text-[13px] file:font-medium file:bg-[#f5f5f7] file:text-[#1d1d1f] hover:file:bg-[#e8e8ed] transition-colors cursor-pointer"
                            accept="image/*">
                        @error('image')
                            <span class="text-red-600 text-[12px] mt-1 block">{{ $message }}</span>
                        @enderror

                        @if ($image)
                            <div class="mt-4">
                                <span class="block text-[12px] text-[#6e6e73] mb-1">Preview:</span>
                                <img src="{{ $image->temporaryUrl() }}"
                                    class="w-20 h-20 object-cover rounded-xl border border-[#d2d2d7]">
                            </div>
                        @endif
                    </div>

                    <div>
                        <label for="name"
                            class="block text-[12px] font-medium text-[#6e6e73] tracking-wide mb-1">Category
                            Name</label>
                        <input wire:model.live="name" type="text" id="name"
                            placeholder="e.g., Vintage Jackets" required
                            class="w-full px-3 py-2.5 border border-[#d2d2d7] rounded-xl text-[14px] bg-[#f5f5f7] text-[#1d1d1f] focus:outline-none focus:border-[#0071e3] focus:bg-white transition-colors">
                        @error('name')
                            <span class="text-red-600 text-[12px] mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="slug"
                            class="block text-[12px] font-medium text-[#6e6e73] tracking-wide mb-1">URL Slug</label>
                        <input wire:model="slug" type="text" id="slug" required
                            class="w-full px-3 py-2.5 border border-[#d2d2d7] rounded-xl text-[14px] bg-[#f5f5f7] text-[#6e6e73] focus:outline-none focus:border-[#0071e3] focus:bg-white transition-colors">
                        @error('slug')
                            <span class="text-red-600 text-[12px] mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="pt-4 flex items-center justify-end space-x-3">
                        <button type="button" @click="modalOpen = false" wire:click="closeModal"
                            class="px-4 py-2.5 rounded-full text-[13px] font-medium text-[#1d1d1f] hover:bg-[#f5f5f7] transition-colors">Cancel</button>
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

    <!-- EDIT MODAL -->
    <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0">
        <div x-show="editModalOpen" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="editModalOpen = false" wire:click="closeEditModal"
            class="fixed inset-0 bg-[#161617]/40 backdrop-blur-sm"></div>

        <div x-show="editModalOpen" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative bg-white rounded-3xl shadow-xl w-full max-w-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-[#d2d2d7] flex items-center justify-between">
                <h3 class="text-[20px] font-semibold text-[#1d1d1f]">Edit Category</h3>
                <button @click="editModalOpen = false" wire:click="closeEditModal"
                    class="text-[#6e6e73] hover:text-[#1d1d1f] transition-colors focus:outline-none">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="px-6 py-6">
                <form wire:submit="update" class="space-y-5">
                    <div>
                        <label class="block text-[12px] font-medium text-[#6e6e73] tracking-wide mb-2">Update Category
                            Image</label>
                        <input type="file" wire:model="image"
                            class="block w-full text-sm text-[#6e6e73] file:mr-4 file:py-2.5 file:px-4 file:rounded-full file:border-0 file:text-[13px] file:font-medium file:bg-[#f5f5f7] file:text-[#1d1d1f] hover:file:bg-[#e8e8ed] transition-colors cursor-pointer"
                            accept="image/*">
                        @error('image')
                            <span class="text-red-600 text-[12px] mt-1 block">{{ $message }}</span>
                        @enderror

                        <div class="mt-4 flex gap-4">
                            @if ($existingImage && !$image)
                                <div>
                                    <span class="block text-[12px] text-[#6e6e73] mb-1">Current Image:</span>
                                    <img src="{{ Storage::url($existingImage) }}"
                                        class="w-20 h-20 object-cover rounded-xl border border-[#d2d2d7]">
                                </div>
                            @endif
                            @if ($image)
                                <div>
                                    <span class="block text-[12px] text-[#6e6e73] mb-1">New Preview:</span>
                                    <img src="{{ $image->temporaryUrl() }}"
                                        class="w-20 h-20 object-cover rounded-xl border border-[#d2d2d7] border-2 border-dashed border-[#0071e3]">
                                </div>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label for="edit_name"
                            class="block text-[12px] font-medium text-[#6e6e73] tracking-wide mb-1">Category
                            Name</label>
                        <input wire:model.live="name" type="text" id="edit_name" required
                            class="w-full px-3 py-2.5 border border-[#d2d2d7] rounded-xl text-[14px] bg-[#f5f5f7] text-[#1d1d1f] focus:outline-none focus:border-[#0071e3] focus:bg-white transition-colors">
                        @error('name')
                            <span class="text-red-600 text-[12px] mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="edit_slug"
                            class="block text-[12px] font-medium text-[#6e6e73] tracking-wide mb-1">URL Slug</label>
                        <input wire:model="slug" type="text" id="edit_slug" required
                            class="w-full px-3 py-2.5 border border-[#d2d2d7] rounded-xl text-[14px] bg-[#f5f5f7] text-[#6e6e73] focus:outline-none focus:border-[#0071e3] focus:bg-white transition-colors">
                        @error('slug')
                            <span class="text-red-600 text-[12px] mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="pt-4 flex items-center justify-end space-x-3">
                        <button type="button" @click="editModalOpen = false" wire:click="closeEditModal"
                            class="px-4 py-2.5 rounded-full text-[13px] font-medium text-[#1d1d1f] hover:bg-[#f5f5f7] transition-colors">Cancel</button>
                        <button type="submit"
                            class="px-5 py-2.5 border border-transparent rounded-full text-[13px] font-medium text-white bg-[#0071e3] hover:bg-[#0077ed] transition-colors shadow-sm">
                            <span wire:loading.remove wire:target="update">Update Category</span>
                            <span wire:loading wire:target="update">Updating...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- DELETE CONFIRMATION MODAL -->
    <div x-show="deleteModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0">
        <div x-show="deleteModalOpen" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="deleteModalOpen = false" wire:click="closeDeleteModal"
            class="fixed inset-0 bg-[#161617]/40 backdrop-blur-sm"></div>

        <div x-show="deleteModalOpen" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative bg-white rounded-3xl shadow-xl w-full max-w-md overflow-hidden">
            <div class="px-6 py-6 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-[20px] font-semibold text-[#1d1d1f] mb-2">Delete Category</h3>
                <p class="text-[14px] text-[#6e6e73]">
                    Are you sure you want to delete <strong class="text-[#1d1d1f]">{{ $name }}</strong>? This
                    action cannot be undone and may affect products currently assigned to this category.
                </p>
            </div>
            <div class="px-6 py-4 bg-[#f5f5f7] border-t border-[#d2d2d7] flex items-center justify-center space-x-3">
                <button type="button" @click="deleteModalOpen = false" wire:click="closeDeleteModal"
                    class="px-5 py-2.5 rounded-full text-[13px] font-medium text-[#1d1d1f] bg-white border border-[#d2d2d7] hover:bg-[#f5f5f7] transition-colors">
                    Cancel
                </button>
                <button type="button" wire:click="destroy"
                    class="px-5 py-2.5 rounded-full text-[13px] font-medium text-white bg-red-600 hover:bg-red-700 transition-colors shadow-sm">
                    <span wire:loading.remove wire:target="destroy">Delete Category</span>
                    <span wire:loading wire:target="destroy">Deleting...</span>
                </button>
            </div>
        </div>
    </div>
</div>
