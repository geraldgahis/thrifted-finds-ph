<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;

new class extends Component {
    use WithPagination;

    public string $search = '';

    // Reset pagination when searching
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Fetch products with their category and primary image (prevents N+1 database queries)
        $products = Product::with(['category', 'primaryImage'])
            ->when($this->search, function ($query) {
                $query->where('title', 'ilike', '%' . $this->search . '%')->orWhere('brand', 'ilike', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('pages.products.index', compact('products'))->title('Manage Products - Admin');
    }
};
?>

<div class="min-h-screen bg-[#f5f5f7] py-12 px-4 sm:px-6 lg:px-8 font-sans antialiased">
    <div class="max-w-6xl mx-auto">

        <!-- Header Section -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-[28px] font-semibold tracking-tight text-[#1d1d1f]">
                    Products
                </h1>
                <p class="text-[14px] text-[#6e6e73] mt-1">
                    Manage your ukay-ukay inventory, track statuses, and add new drops.
                </p>
            </div>

            <a href="/admin/products/create" wire:navigate
                class="flex items-center justify-center py-2 px-4 border border-transparent rounded-full text-[13px] font-medium text-white bg-[#0071e3] hover:bg-[#0077ed] transition-colors shadow-sm w-max">
                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Product
            </a>
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

        <!-- Controls (Search) -->
        <div class="mb-6">
            <div class="relative max-w-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-[#6e6e73]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by title or brand..."
                    class="block w-full pl-10 pr-3 py-2 border border-[#d2d2d7] rounded-full text-[13px] bg-white text-[#1d1d1f] focus:outline-none focus:border-[#0071e3] transition-colors shadow-sm">
            </div>
        </div>

        <!-- Data Table Card -->
        <div class="bg-white border border-[#d2d2d7] rounded-3xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-[#d2d2d7] bg-[#f5f5f7]/50">
                            <th class="py-3 px-6 text-[12px] font-medium text-[#6e6e73] uppercase tracking-wider">
                                Product</th>
                            <th class="py-3 px-6 text-[12px] font-medium text-[#6e6e73] uppercase tracking-wider">
                                Category</th>
                            <th class="py-3 px-6 text-[12px] font-medium text-[#6e6e73] uppercase tracking-wider">Price
                            </th>
                            <th class="py-3 px-6 text-[12px] font-medium text-[#6e6e73] uppercase tracking-wider">Status
                            </th>
                            <th
                                class="py-3 px-6 text-[12px] font-medium text-[#6e6e73] uppercase tracking-wider text-right">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#d2d2d7]">
                        @forelse ($products as $product)
                            <tr class="hover:bg-[#f5f5f7]/50 transition-colors">

                                <!-- Product & Image Info -->
                                <td class="py-4 px-6 flex items-center">
                                    <div
                                        class="h-10 w-10 shrink-0 rounded-md overflow-hidden bg-[#e8e8ed] border border-[#d2d2d7]">
                                        @if ($product->primaryImage)
                                            <img src="{{ Storage::url($product->primaryImage->image_path) }}"
                                                alt="" class="h-full w-full object-cover">
                                        @else
                                            <svg class="h-full w-full text-[#6e6e73] p-2" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div
                                            class="text-[14px] font-medium text-[#1d1d1f] truncate max-w-50 sm:max-w-75">
                                            {{ $product->title }}
                                        </div>
                                        <div class="text-[12px] text-[#6e6e73]">
                                            {{ $product->brand ?? 'Unbranded' }} • Size {{ $product->size_tag }}
                                        </div>
                                    </div>
                                </td>

                                <!-- Category -->
                                <td class="py-4 px-6 text-[14px] text-[#6e6e73]">
                                    {{ $product->category->name ?? 'None' }}
                                </td>

                                <!-- Price -->
                                <td class="py-4 px-6 text-[14px] font-medium text-[#1d1d1f]">
                                    ₱{{ number_format($product->price, 2) }}
                                </td>

                                <!-- Status Badge -->
                                <td class="py-4 px-6">
                                    @if ($product->status === 'available')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#e8f5e9] text-[#1b5e20]">
                                            Available
                                        </span>
                                    @elseif($product->status === 'reserved')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#fff8e1] text-[#f57f17]">
                                            In Cart
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#f5f5f7] text-[#6e6e73]">
                                            Sold
                                        </span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="py-4 px-6 text-right font-medium">
                                    <a href="#" class="text-[13px] text-[#0071e3] hover:underline mr-3">Edit</a>
                                    <!-- We will wire up delete functionality later -->
                                    <button class="text-[13px] text-red-500 hover:underline">Drop</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-[14px] text-[#6e6e73]">
                                    @if ($search)
                                        No products found matching "{{ $search }}".
                                    @else
                                        Your inventory is empty. Click "Add Product" to list your first item.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($products->hasPages())
                <div class="bg-white border-t border-[#d2d2d7] px-6 py-4">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
