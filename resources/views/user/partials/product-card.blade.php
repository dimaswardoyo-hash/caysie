<div class="card-hover bg-white rounded-2xl overflow-hidden border border-gray-100 hover:border-primary/20 group">
    <a href="{{ route('user.product.show', $product->slug) }}" class="block">
        <div
            class="h-44 bg-gradient-to-br from-purple-100 to-indigo-100 relative overflow-hidden flex items-center justify-center">
            @if ($product->image)
                <img src="{{ asset('storage/' . $product->image) }}"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                    alt="{{ $product->name }}">
                <div
                    class="absolute inset-0 bg-gradient-to-t from-black/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                </div>
            @else
                <span class="text-6xl opacity-90">👕</span>
            @endif

            <div class="absolute top-2 left-2 flex flex-col gap-1.5">
                @if ($product->price_sale)
                    <span class="bg-red-500 text-white text-xs font-black px-2 py-0.5 rounded-lg shadow-sm">
                        -{{ $product->discount_percent }}%
                    </span>
                @endif
            </div>

            @if ($product->is_featured)
                <span
                    class="absolute top-2 right-2 bg-yellow-400 text-yellow-900 text-xs font-black w-6 h-6 rounded-lg shadow-sm flex items-center justify-center">
                    <i class="fa-solid fa-star text-[10px]"></i>
                </span>
            @endif
        </div>
    </a>

    <div class="p-4">
        <a href="{{ route('user.product.show', $product->slug) }}">
            <p class="font-bold text-gray-800 text-sm mb-2 line-clamp-1 group-hover:text-primary transition">
                {{ $product->name }}
            </p>
        </a>

        <div class="flex flex-wrap gap-1 mb-3 min-h-[22px]">
            @forelse ($product->sizes->where('stock', '>', 0)->take(4) as $sz)
                <span
                    class="text-[11px] bg-gray-50 border border-gray-100 text-gray-500 px-1.5 py-0.5 rounded-md font-bold">
                    {{ $sz->size }}
                </span>
            @empty
                <span class="text-[11px] bg-red-50 text-red-500 px-2 py-0.5 rounded-md font-bold">
                    Stok Habis
                </span>
            @endforelse
        </div>

        <div class="flex items-center justify-between">
            <div>
                @if ($product->price_sale)
                    <p class="text-base font-black text-primary leading-tight">{{ $product->display_sale_price }}</p>
                    <p class="text-xs text-gray-300 line-through leading-tight">{{ $product->display_price }}</p>
                @else
                    <p class="text-base font-black text-primary leading-tight">{{ $product->display_price }}</p>
                @endif
            </div>
            <a href="{{ route('user.product.show', $product->slug) }}"
                class="w-9 h-9 bg-primary text-white rounded-xl flex items-center justify-center shadow-md shadow-primary/25 group-hover:bg-primary-dark group-hover:scale-105 transition-all duration-200">
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>
    </div>
</div>
