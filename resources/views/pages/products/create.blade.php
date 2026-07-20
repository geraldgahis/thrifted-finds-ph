<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.admin')] class extends Component {
    use WithFileUploads;

    // Form fields
    public $category_id = '';
    public $brand_id = '';
    public string $title = '';
    public string $slug = '';
    public string $description = '';
    public string $size_tag = '';
    public string $measurements = '';
    public string $condition = '';
    public string $price = '';
    public string $status = 'available';

    // Image handling
    public $photos = [];

    // Auto-generate slug
    public function updatedTitle($value)
    {
        $this->slug = Str::slug($value);
    }

    public function save()
    {
        $this->validate([
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'description' => 'nullable|string',
            'size_tag' => 'nullable|string|max:50',
            'measurements' => 'nullable|string|max:255',
            'condition' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:available,reserved,sold',
            'photos.*' => 'image|max:5120', // Max 5MB per image
        ]);

        // 1. Create the Product
        $product = Product::create([
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'size_tag' => $this->size_tag,
            'measurements' => $this->measurements,
            'condition' => $this->condition,
            'price' => $this->price,
            'status' => $this->status,
        ]);

        // 2. Handle Image Uploads
        if (!empty($this->photos)) {
            foreach ($this->photos as $index => $photo) {
                // Store in storage/app/public/products
                $path = $photo->store('products', 'public');

                $product->images()->create([
                    'image_path' => $path,
                    // The first uploaded image is automatically the primary storefront image
                    'is_primary' => $index === 0 ? true : false,
                ]);
            }
        }

        session()->flash('success', 'Product "' . $this->title . '" added successfully.');
        return $this->redirect('/admin/products', navigate: true);
    }

    public function render()
    {
        return view('pages.products.create', [
            'categories' => Category::orderBy('name')->get(),
            'brands' => Brand::orderBy('name')->get(),
        ])->title('Add Product - Admin');
    }
};
?>

<div class="min-h-screen bg-[#f5f5f7] py-12 px-4 sm:px-6 lg:px-8 font-sans antialiased">
    <div class="max-w-4xl mx-auto">

        <!-- Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-[28px] font-semibold tracking-tight text-[#1d1d1f]">
                    Add New Product
                </h1>
                <p class="text-[14px] text-[#6e6e73] mt-1">
                    Upload a unique 1-of-1 item to your catalog.
                </p>
            </div>
            <a href="/admin/products" wire:navigate
                class="text-[13px] font-medium text-[#1d1d1f] bg-white border border-[#d2d2d7] px-4 py-2 rounded-full hover:bg-[#f5f5f7] transition-colors shadow-sm">
                Cancel
            </a>
        </div>

        <form wire:submit="save" class="space-y-6">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Details (Left Column) -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white border border-[#d2d2d7] rounded-3xl p-6 sm:p-8 shadow-sm">
                        <h2 class="text-[16px] font-semibold text-[#1d1d1f] mb-5">Basic Information</h2>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-[12px] font-medium text-[#6e6e73] mb-1">Title</label>
                                <input wire:model.live="title" type="text"
                                    placeholder="e.g., 90s Nike Center Swoosh Hoodie" required
                                    class="w-full px-3 py-2.5 border border-[#d2d2d7] rounded-xl text-[14px] bg-[#f5f5f7] focus:outline-none focus:border-[#0071e3] focus:bg-white transition-colors">
                                @error('title')
                                    <span class="text-red-600 text-[12px] mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[12px] font-medium text-[#6e6e73] mb-1">Brand</label>
                                    <select wire:model="brand_id" required
                                        class="w-full px-3 py-2.5 border border-[#d2d2d7] rounded-xl text-[14px] bg-[#f5f5f7] focus:outline-none focus:border-[#0071e3] focus:bg-white transition-colors">
                                        <option value="">No Brand / Unbranded</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('brand_id')
                                        <span class="text-red-600 text-[12px] mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-[12px] font-medium text-[#6e6e73] mb-1">Category</label>
                                    <select wire:model="category_id" required
                                        class="w-full px-3 py-2.5 border border-[#d2d2d7] rounded-xl text-[14px] bg-[#f5f5f7] focus:outline-none focus:border-[#0071e3] focus:bg-white transition-colors">
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <span class="text-red-600 text-[12px] mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-[12px] font-medium text-[#6e6e73] mb-1">Description</label>
                                <textarea wire:model="description" rows="4"
                                    class="w-full px-3 py-2.5 border border-[#d2d2d7] rounded-xl text-[14px] bg-[#f5f5f7] focus:outline-none focus:border-[#0071e3] focus:bg-white transition-colors"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Images Section -->
                    <div class="bg-white border border-[#d2d2d7] rounded-3xl p-6 sm:p-8 shadow-sm">
                        <h2 class="text-[16px] font-semibold text-[#1d1d1f] mb-5">Product Images</h2>

                        <div
                            class="w-full flex justify-center px-6 pt-5 pb-6 border-2 border-[#d2d2d7] border-dashed rounded-xl bg-[#f5f5f7]">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-[#6e6e73]" stroke="currentColor" fill="none"
                                    viewBox="0 0 48 48" aria-hidden="true">
                                    <path
                                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-[14px] text-[#6e6e73] justify-center mt-2">
                                    <label for="file-upload"
                                        class="relative cursor-pointer bg-transparent rounded-md font-medium text-[#0071e3] hover:underline focus-within:outline-none">
                                        <span>Upload files</span>
                                        <input id="file-upload" wire:model="photos" type="file" multiple
                                            class="sr-only" accept="image/*">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-[12px] text-[#6e6e73]">PNG, JPG up to 5MB (Select multiple)</p>
                            </div>
                        </div>

                        <!-- Image Previews -->
                        @if ($photos)
                            <div class="mt-4 grid grid-cols-4 gap-4">
                                @foreach ($photos as $photo)
                                    <div
                                        class="relative aspect-square rounded-lg overflow-hidden border border-[#d2d2d7]">
                                        <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Thrift Specifics (Right Column) -->
                <div class="space-y-6">
                    <div class="bg-white border border-[#d2d2d7] rounded-3xl p-6 shadow-sm">
                        <h2 class="text-[16px] font-semibold text-[#1d1d1f] mb-5">Thrift Details</h2>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-[12px] font-medium text-[#6e6e73] mb-1">Price (₱)</label>
                                <input wire:model="price" type="number" step="0.01" placeholder="0.00" required
                                    class="w-full px-3 py-2.5 border border-[#d2d2d7] rounded-xl text-[14px] bg-[#f5f5f7] focus:outline-none focus:border-[#0071e3] focus:bg-white transition-colors">
                                @error('price')
                                    <span class="text-red-600 text-[12px] mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-[12px] font-medium text-[#6e6e73] mb-1">Tag Size</label>
                                <input wire:model="size_tag" type="text" placeholder="e.g., L, Medium"
                                    class="w-full px-3 py-2.5 border border-[#d2d2d7] rounded-xl text-[14px] bg-[#f5f5f7] focus:outline-none focus:border-[#0071e3] focus:bg-white transition-colors">
                            </div>

                            <div>
                                <label class="block text-[12px] font-medium text-[#6e6e73] mb-1">Exact
                                    Measurements</label>
                                <input wire:model="measurements" type="text" placeholder="e.g., W: 32 x L: 30"
                                    class="w-full px-3 py-2.5 border border-[#d2d2d7] rounded-xl text-[14px] bg-[#f5f5f7] focus:outline-none focus:border-[#0071e3] focus:bg-white transition-colors">
                            </div>

                            <div>
                                <label class="block text-[12px] font-medium text-[#6e6e73] mb-1">Condition</label>
                                <input wire:model="condition" type="text" placeholder="e.g., 9/10, Minor pinhole"
                                    class="w-full px-3 py-2.5 border border-[#d2d2d7] rounded-xl text-[14px] bg-[#f5f5f7] focus:outline-none focus:border-[#0071e3] focus:bg-white transition-colors">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Action -->
                    <button type="submit"
                        class="w-full flex justify-center items-center py-3.5 px-6 border border-transparent rounded-full text-[14px] font-medium text-white bg-[#0071e3] hover:bg-[#0077ed] transition-colors shadow-sm">
                        <span wire:loading.remove wire:target="save">Publish Product</span>
                        <span wire:loading wire:target="save">Uploading...</span>
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>
