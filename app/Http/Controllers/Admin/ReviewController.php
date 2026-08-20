<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $query = Review::with('product', 'user')->latest();

        if ($request->query('filter') === 'pending') {
            $query->where('is_approved', false);
        }

        $reviews = $query->paginate(15)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function moderate(Request $request, Review $review): RedirectResponse
    {
        $review->update(['is_approved' => $request->boolean('approve')]);

        return back()->with('status', $review->is_approved ? 'Avis approuvé.' : 'Avis rejeté.');
    }
}
