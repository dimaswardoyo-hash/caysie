<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    /**
     * Simpan atau perbarui testimoni milik user yang sedang login.
     * Satu user hanya boleh punya satu testimoni (unique per user_id).
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'rating' => ['required', 'integer', 'min:1', 'max:5'],
                'message' => ['required', 'string', 'min:10', 'max:500'],
            ],
            [
                'rating.required' => 'Silakan beri rating bintang.',
                'message.required' => 'Testimoni tidak boleh kosong.',
                'message.min' => 'Testimoni minimal 10 karakter.',
            ],
        );

        Testimonial::updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'rating' => $validated['rating'],
                'message' => $validated['message'],
                'is_approved' => true,
            ],
        );

        return back()->with('success', 'Terima kasih! Testimonimu berhasil disimpan.');
    }

    /**
     * Hapus testimoni milik user yang sedang login.
     */
    public function destroy(Request $request)
    {
        Testimonial::where('user_id', $request->user()->id)->delete();

        return back()->with('success', 'Testimonimu berhasil dihapus.');
    }
}
