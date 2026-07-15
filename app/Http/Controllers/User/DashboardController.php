<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $featured = Product::active()->featured()->with('sizes')->take(4)->get();
        $newArrivals = Product::active()->with('sizes')->latest()->take(8)->get();
        $cartCount = Cart::where('user_id', auth()->id())->sum('quantity');

        $testimonials = Testimonial::approved()->with('user')->latest()->take(6)->get();

        $myTestimonial = auth()->check() ? Testimonial::where('user_id', auth()->id())->first() : null;

        return view('user.dashboard', compact('featured', 'newArrivals', 'cartCount', 'testimonials', 'myTestimonial'));
    }
}
