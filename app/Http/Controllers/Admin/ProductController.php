<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    private array $sizes = ['S', 'M', 'L', 'XL', 'XXL'];
    private array $categories = ['kaos', 'celana', 'jaket', 'aksesoris'];

    // ── INDEX ────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Product::with('sizes')->latest();

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }
        if ($request->category) {
            $query->where('category', $request->category);
        }
        if ($request->status === 'active') {
            $query->where('is_active', true);
        } elseif ($request->status === 'inactive') {
            $query->where('is_active', false);
        }

        $products = $query->paginate(10)->withQueryString();
        $categories = $this->categories;

        return view('admin.products.index', compact('products', 'categories'));
    }

    // ── CREATE ───────────────────────────────────────────
    public function create()
    {
        $sizes = $this->sizes;
        $categories = $this->categories;
        return view('admin.products.create', compact('sizes', 'categories'));
    }

    // ── STORE ────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'price_sale' => 'nullable|numeric|min:0|lt:price',
                'category' => 'required|in:' . implode(',', $this->categories),
                'weight' => 'required|integer|min:150|max:250',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'is_featured' => 'boolean',
                'is_active' => 'boolean',
                'sizes' => 'required|array|min:1',
                'sizes.*.size' => 'required|in:' . implode(',', $this->sizes),
                'sizes.*.stock' => 'required|integer|min:0',
            ],
            [
                'price_sale.lt' => 'Harga diskon harus lebih kecil dari harga normal.',
                'sizes.required' => 'Pilih minimal 1 ukuran produk.',
                'weight.min' => 'Berat minimal 150 gram.',
                'weight.max' => 'Berat maksimal 250 gram.',
            ],
        );

        // Upload foto utama
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // Upload foto tambahan
        $extraImages = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $extraImages[] = $img->store('products', 'public');
            }
        }

        $product = Product::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'price_sale' => $validated['price_sale'] ?? null,
            'category' => $validated['category'],
            'weight' => $validated['weight'],
            'image' => $imagePath,
            'images' => $extraImages ?: null,
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        // Simpan ukuran & stok
        foreach ($validated['sizes'] as $sizeData) {
            $product->sizes()->create([
                'size' => $sizeData['size'],
                'stock' => $sizeData['stock'],
            ]);
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk "' . $product->name . '" berhasil ditambahkan!');
    }

    // ── SHOW ─────────────────────────────────────────────
    public function show(Product $product)
    {
        $product->load('sizes');
        return view('admin.products.show', compact('product'));
    }

    // ── EDIT ─────────────────────────────────────────────
    public function edit(Product $product)
    {
        $product->load('sizes');
        $sizes = $this->sizes;
        $categories = $this->categories;
        return view('admin.products.edit', compact('product', 'sizes', 'categories'));
    }

    // ── UPDATE ───────────────────────────────────────────
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate(
            [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'price_sale' => 'nullable|numeric|min:0|lt:price',
                'category' => 'required|in:' . implode(',', $this->categories),
                'weight' => 'required|integer|min:150|max:250',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'is_featured' => 'boolean',
                'is_active' => 'boolean',
                'sizes' => 'required|array|min:1',
                'sizes.*.size' => 'required|in:' . implode(',', $this->sizes),
                'sizes.*.stock' => 'required|integer|min:0',
            ],
            [
                'price_sale.lt' => 'Harga diskon harus lebih kecil dari harga normal.',
                'sizes.required' => 'Pilih minimal 1 ukuran produk.',
            ],
        );

        // Ganti foto utama jika ada upload baru
        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // Foto tambahan baru
        if ($request->hasFile('images')) {
            $extraImages = [];
            foreach ($request->file('images') as $img) {
                $extraImages[] = $img->store('products', 'public');
            }
            $validated['images'] = array_merge($product->images ?? [], $extraImages);
        }

        $product->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'price_sale' => $validated['price_sale'] ?? null,
            'category' => $validated['category'],
            'weight' => $validated['weight'],
            'image' => $validated['image'] ?? $product->image,
            'images' => $validated['images'] ?? $product->images,
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        // Sync ukuran & stok (hapus lama, isi baru)
        $product->sizes()->delete();
        foreach ($validated['sizes'] as $sizeData) {
            $product->sizes()->create([
                'size' => $sizeData['size'],
                'stock' => $sizeData['stock'],
            ]);
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk "' . $product->name . '" berhasil diperbarui!');
    }

    // ── DELETE IMAGE EXTRA ───────────────────────────────
    public function deleteImage(Request $request, Product $product)
    {
        $index = $request->index;
        $images = $product->images ?? [];
        if (isset($images[$index])) {
            Storage::disk('public')->delete($images[$index]);
            array_splice($images, $index, 1);
            $product->update(['images' => $images ?: null]);
        }
        return back()->with('success', 'Foto berhasil dihapus.');
    }

    // ── DESTROY ──────────────────────────────────────────
    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        if ($product->images) {
            foreach ($product->images as $img) {
                Storage::disk('public')->delete($img);
            }
        }
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus.');
    }

    // ── TOGGLE STATUS ────────────────────────────────────
    public function toggleStatus(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);
        $label = $product->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Produk berhasil {$label}.");
    }
}
