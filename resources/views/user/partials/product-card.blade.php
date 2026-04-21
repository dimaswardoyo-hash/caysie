<div class="card-hover bg-white rounded-2xl overflow-hidden border border-gray-100 group">
    <a href="{{ route('user.product.show', $product->slug) }}">
        <div
            class="h-44 bg-gradient-to-br from-purple-100 to-indigo-100 relative overflow-hidden flex items-center justify-center">
            @if ($product->image)
                <img src="{{ asset('storage/' . $product->image) }}"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                    alt="{{ $product->name }}">
            @else
                <span class="text-6xl">👕</span>
            @endif
            @if ($product->price_sale)
                <span class="absolute top-2 left-2 bg-red-500 text-white text-xs font-black px-2 py-0.5 rounded-lg">
                    -{{ $product->discount_percent }}%
                </span>
            @endif
            @if ($product->is_featured)
                <span
                    class="absolute top-2 right-2 bg-yellow-400 text-yellow-900 text-xs font-black px-2 py-0.5 rounded-lg">
                    ⭐
                </span>
            @endif
        </div>
    </a>
    <div class="p-4">
        <a href="{{ route('user.product.show', $product->slug) }}">
            <p class="font-bold text-gray-800 text-sm mb-1 line-clamp-1 hover:text-primary transition">
                {{ $product->name }}
            </p>
        </a>
        <div class="flex gap-1 mb-3">
            @foreach ($product->sizes->where('stock', '>', 0)->take(4) as $sz)
                <span
                    class="text-xs bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded font-semibold">{{ $sz->size }}</span>
            @endforeach
            @if ($product->sizes->where('stock', '>', 0)->count() === 0)
                <span class="text-xs bg-red-100 text-red-500 px-2 py-0.5 rounded font-semibold">Habis</span>
            @endif
        </div>
        <div class="flex items-center justify-between">
            <div>
                @if ($product->price_sale)
                    <p class="text-base font-black text-primary">{{ $product->display_sale_price }}</p>
                    <p class="text-xs text-gray-300 line-through">{{ $product->display_price }}</p>
                @else
                    <p class="text-base font-black text-primary">{{ $product->display_price }}</p>
                @endif
            </div>
            <a href="{{ route('user.product.show', $product->slug) }}"
                class="w-9 h-9 bg-primary text-white rounded-xl flex items-center justify-center hover:bg-primary-dark transition shadow-md shadow-purple-200">
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>
    </div>
</div>
