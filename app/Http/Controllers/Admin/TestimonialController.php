<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Testimonial;

class TestimonialController extends Controller
{
    public function index(Request $request)
    {
        $query = Testimonial::with('user')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('message', 'like', "%{$search}%")->orWhereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        if ($request->status === 'approved') {
            $query->where('is_approved', true);
        } elseif ($request->status === 'hidden') {
            $query->where('is_approved', false);
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        $testimonials = $query->paginate(10)->withQueryString();

        $stats = [
            'total' => Testimonial::count(),
            'approved' => Testimonial::where('is_approved', true)->count(),
            'hidden' => Testimonial::where('is_approved', false)->count(),
            'avg_rating' => round(Testimonial::avg('rating'), 1) ?: 0,
        ];

        return view('admin.testimonials.index', compact('testimonials', 'stats'));
    }

    /**
     * Tampilkan / sembunyikan testimoni dari halaman dashboard user.
     */
    public function toggleApprove(Testimonial $testimonial)
    {
        $testimonial->update(['is_approved' => !$testimonial->is_approved]);

        return back()->with('success', $testimonial->is_approved ? 'Testimoni sekarang ditampilkan di halaman utama.' : 'Testimoni disembunyikan dari halaman utama.');
    }

    /**
     * Admin mengoreksi isi testimoni (mis. typo atau konten tidak pantas).
     */
    public function update(Request $request, Testimonial $testimonial)
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'message' => ['required', 'string', 'min:10', 'max:500'],
        ]);

        $testimonial->update($validated);

        return back()->with('success', 'Testimoni berhasil diperbarui.');
    }

    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();

        return back()->with('success', 'Testimoni berhasil dihapus.');
    }
}
