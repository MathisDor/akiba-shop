<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\View\View;

class CategoriesController extends Controller
{
    public function __invoke(): View
    {
        $femme = Category::where('gender', 'femme')
            ->where('is_active', true)
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get();

        $homme = Category::where('gender', 'homme')
            ->where('is_active', true)
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('femme', 'homme'));
    }
}
