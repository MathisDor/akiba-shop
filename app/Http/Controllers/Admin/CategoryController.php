<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ListingCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = ListingCategory::roots()
            ->withCount('listings')
            ->with(['children' => fn ($q) => $q->withCount('listings')])
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return $this->form(new ListingCategory(['is_active' => true]));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);
        $validated['slug'] = $this->uniqueSlug($validated['name']);

        ListingCategory::create($validated);

        return redirect()->route('admin.categories.index')->with('status', 'Catégorie créée.');
    }

    public function edit(ListingCategory $category): View
    {
        return $this->form($category);
    }

    public function update(Request $request, ListingCategory $category): RedirectResponse
    {
        $category->update($this->validated($request, $category));

        return redirect()->route('admin.categories.index')->with('status', 'Catégorie mise à jour.');
    }

    public function destroy(ListingCategory $category): RedirectResponse
    {
        if ($category->listings()->exists() || $category->children()->exists()) {
            return back()->with('error', 'Impossible de supprimer une catégorie qui contient des annonces ou des sous-catégories.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('status', 'Catégorie supprimée.');
    }

    private function form(ListingCategory $category): View
    {
        return view('admin.categories.form', [
            'category' => $category,
            'parents' => ListingCategory::roots()->whereKeyNot($category->id ?? 0)->get(),
        ]);
    }

    private function validated(Request $request, ?ListingCategory $category = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // Only two levels: a parent must itself be a root category.
            'parent_id' => ['nullable', 'integer', Rule::exists('listing_categories', 'id')->whereNull('parent_id'), Rule::notIn([$category?->id])],
            'icon' => ['nullable', 'string', 'max:40'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
        ]);

        $validated['parent_id'] ??= null;

        if ($category && $validated['parent_id'] && $category->children()->exists()) {
            throw ValidationException::withMessages([
                'parent_id' => 'Cette catégorie a des sous-catégories : elle doit rester une catégorie principale.',
            ]);
        }

        return [...$validated, 'sort_order' => $validated['sort_order'] ?? 0, 'is_active' => $request->boolean('is_active')];
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 2;
        while (ListingCategory::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
